<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('cajas/cajas_mov_model');
    }

    function get_all()
    {
        $result = $this->db->join('local', 'local.int_local_id = caja.local_id')
            ->join('usuario', 'usuario.nUsuCodigo = caja.responsable_id')
            ->get('caja')->result();

        foreach ($result as $desglose) {
            $desglose->desgloses = $this->db->where('caja_id', $desglose->id)
                ->join('usuario', 'usuario.nUsuCodigo = caja_desglose.responsable_id')
                ->get('caja_desglose')->result();

            foreach ($desglose->desgloses as $detalle) {
                $detalle->pendientes = $this->db->get_where('caja_pendiente', array(
                    'estado'=>0,
                    'caja_desglose_id'=>$detalle->id
                ))->result();
            }


        }

        return $result;
    }

    function getCajasSelect()
    {
        return $this->db->select('
            caja_desglose.id as cuenta_id,
            caja.moneda_id as moneda_id,
            caja_desglose.principal as principal,
            caja_desglose.descripcion as descripcion
            ')
            ->from('caja_desglose')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->where('caja.estado', 1)
            ->where('caja_desglose.estado', 1)
            ->where('caja_desglose.retencion', 0)
            ->get()->result();
    }


    function get($id)
    {
        return $this->db->get_where('caja', array('id' => $id))->row();
    }

    function get_cuenta($id)
    {
        return $this->db->get_where('caja_desglose', array('id' => $id))->row();
    }

    function get_cierre($id)
    {
        return $this->db->get_where('caja_cuadre', array('id' => $id))->row();
    }


    function save($caja, $id = FALSE)
    {

        if ($id != FALSE) {
            $this->db->where('id', $id);
            $this->db->update('caja', $caja);
            return $id;
        } else {
            $this->db->insert('caja', $caja);
            return $this->db->insert_id();
        }
    }

    function update_saldo($id, $saldo, $ingreso = TRUE)
    {
        $cuenta = $this->get_cuenta($id);

        if ($ingreso == TRUE) {
            $new_saldo = $cuenta->saldo + $saldo;
        } elseif ($ingreso == FALSE) {
            $new_saldo = $cuenta->saldo - $saldo;
        }

        if ($new_saldo >= 0) {
            $this->db->where('id', $id);
            $this->db->update('caja_desglose', array('saldo' => $new_saldo));
        }
    }

    function save_cuenta($caja, $id = FALSE)
    {
        $this->db->where('caja_id', $caja['caja_id']);
        $this->db->from('caja_desglose');
        if ($this->db->count_all_results() == 0) {
            $caja['principal'] == 1;
        }

        if ($caja['principal'] == 1) {
            $caja['estado'] == 1;
            $this->db->where('principal', 1);
            $this->db->where('caja_id', $caja['caja_id']);
            $this->db->update('caja_desglose', array('principal' => 0));
        }

        if ($id != FALSE) {
            $this->db->where('id', $id);
            $this->db->update('caja_desglose', $caja);
            return $id;
        } else {
            $data['saldo'] = 0;
            $this->db->insert('caja_desglose', $caja);
            return $this->db->insert_id();
        }
    }

    function ajustar_cuenta($data, $id)
    {
        $fecha = date('Y-m-d H:i:s', strtotime($data['fecha'] . ' ' . date('H:i:s')));
        $cuenta = $this->get_cuenta($id);

        if($data['tipo_ajuste'] == 'TRASPASO')
            $cuenta_destino = $this->get_cuenta($data['cuenta_id']);

        if ($data['tipo_ajuste'] == 'INGRESO' || $data['tipo_ajuste'] == 'EGRESO') {
            $saldo = $data['tipo_ajuste'] == 'EGRESO' ? $cuenta->saldo - $data['importe'] : $cuenta->saldo + $data['importe'];
            $saldo_old = $cuenta->saldo;

            $this->db->where('id', $id);
            $this->db->update('caja_desglose', array(
                'saldo' => $saldo
            ));

            $this->cajas_mov_model->save_mov(array(
                'caja_desglose_id' => $id,
                'usuario_id' => $this->session->userdata('nUsuCodigo'),
                'fecha_mov' => $fecha,
                'movimiento' => $data['tipo_ajuste'],
                'operacion' => 'AJUSTE',
                'medio_pago' => 'INTERNO',
                'saldo' => $data['importe'],
                'saldo_old' => $saldo_old,
                'ref_id' => '',
                'ref_val' => $data['motivo'],
            ));
        } else if ($data['tipo_ajuste'] == 'TRASPASO' && $cuenta->responsable_id == $cuenta_destino->responsable_id) {
            //HAGO EL EGRESO
            $saldo = $cuenta->saldo - $data['importe'];
            $saldo_old = $cuenta->saldo;

            $this->db->where('id', $id);
            $this->db->update('caja_desglose', array(
                'saldo' => $saldo
            ));

            $this->cajas_mov_model->save_mov(array(
                'caja_desglose_id' => $id,
                'usuario_id' => $this->session->userdata('nUsuCodigo'),
                'fecha_mov' => $fecha,
                'movimiento' => 'EGRESO',
                'operacion' => 'TRASPASO',
                'medio_pago' => 'INTERNO',
                'saldo' => $data['importe'],
                'saldo_old' => $saldo_old,
                'ref_id' => $data['cuenta_id'],
                'ref_val' => $data['motivo'],
            ));

            //HAGO EL INGRESO
            $saldo = $cuenta_destino->saldo + $data['subimporte'];
            $saldo_old = $cuenta_destino->saldo;

            $tasa = "";
            if ($cuenta->caja_id != $cuenta_destino->caja_id)
                $tasa = $data['tasa'];

            $this->db->where('id', $data['cuenta_id']);
            $this->db->update('caja_desglose', array(
                'saldo' => $saldo
            ));

            $this->cajas_mov_model->save_mov(array(
                'caja_desglose_id' => $cuenta_destino->id,
                'usuario_id' => $this->session->userdata('nUsuCodigo'),
                'fecha_mov' => $fecha,
                'movimiento' => 'INGRESO',
                'operacion' => 'TRASPASO',
                'medio_pago' => 'INTERNO',
                'saldo' => $data['subimporte'],
                'saldo_old' => $saldo_old,
                'ref_id' => $id,
                'ref_val' => $tasa,
            ));
        }
        else if ($data['tipo_ajuste'] == 'TRASPASO' && $cuenta->responsable_id != $cuenta_destino->responsable_id){

            $this->db->insert('caja_pendiente', array(
                'caja_desglose_id'=>$cuenta_destino->id,
                'usuario_id'=>$this->session->userdata('nUsuCodigo'),
                'tipo'=>'TRASPASO',
                'IO'=>1,
                'monto'=>$data['importe'],
                'estado'=>0,
                'ref_id'=>$id
            ));
        }
    }

    function valid_caja($data, $id = FALSE)
    {
        $this->db->where('local_id', $data['local_id']);
        $this->db->where('moneda_id', $data['moneda_id']);
        if ($id != FALSE)
            $this->db->where('id !=', $id);
        $this->db->from('caja');

        if ($this->db->count_all_results() == 0)
            return TRUE;
        else
            return FALSE;
    }

    function valid_caja_cuenta($data, $id = FALSE)
    {

        $this->db->where('descripcion', $data['descripcion']);
        $this->db->where('responsable_id', $data['responsable_id']);
        if ($id != FALSE)
            $this->db->where('id !=', $id);
        $this->db->from('caja_desglose');

        if ($this->db->count_all_results() == 0)
            return TRUE;
        else
            return TRUE;
    }

    function save_pendiente($data){
        $cuenta = $this->db->get_where('caja_desglose', array(
            'caja_id'=>1,
            'principal'=>1
        ))->row();

        $this->db->insert('caja_pendiente', array(
            'caja_desglose_id'=>$cuenta->id,
            'usuario_id'=>$this->session->userdata('nUsuCodigo'),
            'tipo'=>$data['tipo'],
            'monto'=> $data['monto'],
            'estado'=>0,
            'IO'=>$data['IO'],
            'ref_id'=>$data['ref_id']
        ));
    }

    function update_pendiente($data){

        $cuenta = $this->db->get_where('caja_desglose', array(
            'caja_id'=>1,
            'principal'=>1
        ))->row();

        $caja_pendiente = $this->db->get_where('caja_pendiente', array(
            'tipo'=>$data['tipo'],
            'ref_id'=>$data['ref_id']
            ))->row();

        if($caja_pendiente != NULL){
            if($caja_pendiente->estado == 1){
                $new_saldo = $cuenta->saldo + $caja_pendiente->monto;
                $this->db->where('id', $cuenta->id);
                $this->db->update('caja_desglose', array('saldo'=>$new_saldo));

                //hay que agregar el movimiento
            }

            $this->db->where('id', $caja_pendiente->id);
            $this->db->update('caja_pendiente', array(
                'caja_desglose_id'=>$cuenta->id,
                'usuario_id'=>$this->session->userdata('nUsuCodigo'),
                'monto'=>$data['monto'],
                'estado'=>0,
            ));
        }
        else{
            if(!isset($data['IO']))
                $data['IO'] = 2;
                
            $this->db->insert('caja_pendiente', array(
                'caja_desglose_id'=>$cuenta->id,
                'usuario_id'=>$this->session->userdata('nUsuCodigo'),
                'tipo'=>$data['tipo'],
                'monto'=> $data['monto'],
                'estado'=>0,
                'IO'=>$data['IO'],
                'ref_id'=>$data['ref_id']
            ));
        }
    }

    function delete_pendiente($data){

        $cuenta = $this->db->get_where('caja_desglose', array(
            'caja_id'=>1,
            'principal'=>1
        ))->row();

        $caja_pendiente = $this->db->get_where('caja_pendiente', array(
            'tipo'=>$data['tipo'],
            'ref_id'=>$data['ref_id']
            ))->row();

        if($caja_pendiente->estado == 1){
            $new_saldo = $cuenta->saldo + $caja_pendiente->monto;
            $this->db->where('id', $cuenta->id);
            $this->db->update('caja_desglose', array('saldo'=>$new_saldo));

            //hay que agregar el movimiento
        }

        $this->db->where('id', $caja_pendiente->id);
        $this->db->delete('caja_pendiente');
    }


}

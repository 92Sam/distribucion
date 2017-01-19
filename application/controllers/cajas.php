<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('cajas/cajas_model');
        $this->load->model('cajas/cajas_mov_model');
        $this->load->model('local/local_model');
        $this->load->model('usuario/usuario_model');
    }

    function index()
    {
        $data['cajas'] = $this->cajas_model->get_all();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/cajas/cajas', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function caja_form($id = FALSE)
    {

        $data['header_text'] = 'Nueva Caja';
        if ($id != FALSE) {
            $data['caja'] = $this->cajas_model->get($id);
            $data['header_text'] = 'Editar Caja';
        }

        $data['locales'] = $this->local_model->get_all();
        $data['usuarios'] = $this->db->get_where('usuario', array('activo' => 1))->result();

        $this->load->view('menu/cajas/form', $data);
    }

    function caja_guardar($id = FALSE)
    {
        $data = array(
            'local_id' => $this->input->post('local_id'),
            'moneda_id' => $this->input->post('moneda_id'),
            'responsable_id' => $this->input->post('responsable_id'),
            'estado' => $this->input->post('estado')
        );

        header('Content-Type: application/json');
        if ($this->cajas_model->valid_caja($data, $id)) {
            $result = $this->cajas_model->save($data, $id);
            echo json_encode(array('success' => $result));
        } else {
            echo json_encode(array('error' => '1'));
        }
    }

    function caja_cuenta_form($caja_id, $id = FALSE)
    {
        $data['header_text'] = 'Nueva Cuenta de Caja';
        if ($id != FALSE) {
            $data['cuenta'] = $this->cajas_model->get_cuenta($id);
            $data['header_text'] = 'Editar Cuenta de Caja';
        }

        $data['caja_id'] = $caja_id;

        $data['usuarios'] = $this->db->get_where('usuario', array('activo' => 1))->result();

        $this->load->view('menu/cajas/form_cuenta', $data);
    }

    function caja_cuenta_guardar($id = FALSE)
    {
        $data = array(
            'caja_id' => $this->input->post('caja_id'),
            'descripcion' => $this->input->post('descripcion'),
            'responsable_id' => $this->input->post('responsable_id'),
            'principal' => $this->input->post('principal'),
            'estado' => $this->input->post('estado')
        );

        header('Content-Type: application/json');
        if ($this->cajas_model->valid_caja_cuenta($data, $id)) {
            $result = $this->cajas_model->save_cuenta($data, $id);
            echo json_encode(array('success' => $result));
        } else {
            echo json_encode(array('error' => '1'));
        }
    }

    function caja_ajustar_form($caja_id, $id)
    {
        $data['header_text'] = 'Ajustar Cuenta de Caja';
        $data['cuenta'] = $this->cajas_model->get_cuenta($id);

        $data['caja_id'] = $caja_id;
        $data['caja_actual'] = $this->cajas_model->get($caja_id);

        $data['cajas'] = $this->db->get_where('caja', array('estado' => 1))->result();
        $data['caja_cuentas'] = $this->db->get_where('caja_desglose', array('estado' => 1))->result();

        $this->load->view('menu/cajas/form_ajustar_cuenta', $data);
    }

    function caja_ajustar_guardar($id)
    {
        $data = array(
            'fecha' => $this->input->post('fecha'),
            'motivo' => $this->input->post('motivo'),
            'tipo_ajuste' => $this->input->post('tipo_ajuste'),
            'cuenta_id' => $this->input->post('cuenta_id'),
            'tasa' => $this->input->post('tasa'),
            'importe' => $this->input->post('importe'),
            'subimporte' => $this->input->post('subimporte')
        );
        $this->cajas_model->ajustar_cuenta($data, $id);

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function caja_ajustar_retencion_form($caja_id, $id)
    {
        $data['header_text'] = 'Realizar pago por retencion';
        $data['cuenta'] = $this->cajas_model->get_cuenta($id);

        $data['caja_id'] = $caja_id;
        $data['caja_actual'] = $this->cajas_model->get($caja_id);

        $data['cajas'] = $this->db->get_where('caja', array('estado' => 1))->result();
        $data['caja_cuentas'] = $this->db->get_where('caja_desglose', array('estado' => 1))->result();

        $data['retenciones'] = $this->db->get_where('caja_movimiento', array(
            'caja_desglose_id'=>$id,
            'medio_pago'=>7,
            'operacion'=>'COBRANZA',
            'movimiento'=>'INGRESO'
        ))->result();

        $data['year'] = $this->db->query("
            SELECT date_format(fecha_mov, '%Y') as year
            FROM caja_movimiento
            GROUP BY date_format(fecha_mov, '%Y')
            ")->result();

        $this->load->view('menu/cajas/form_ajustar_cuenta_retencion', $data);
    }

    function caja_ajustar_retencion_guardar($id)
    {
        $data = array(
            'fecha' => $this->input->post('fecha'),
            'importe' => $this->input->post('importe'),
            'retenciones' => json_decode($this->input->post('retenciones'))
        );
        $this->cajas_model->ajustar_retencion($data, $id);

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function caja_detalle_form($id)
    {
        $data['cuenta'] = $this->cajas_model->get_cuenta($id);

        $data['cuenta_movimientos'] = $this->cajas_mov_model->get_movimientos_today($id);

        $this->load->view('menu/cajas/form_detalle', $data);
    }

    function caja_pendiente_form($id)
    {
        $data['cuenta'] = $this->cajas_model->get_cuenta($id);

        $data['saldos_pendientes'] = $this->db->get_where('caja_pendiente', array(
            'estado'=>0,
            'caja_desglose_id'=>$id
        ))->result();

        $this->load->view('menu/cajas/form_pendiente', $data);
    }

    function confirmar_saldo($id)
    {
        header('Content-Type: application/json');

        $caja_pendiente = $this->db->get_where('caja_pendiente', array('id'=>$id))->row();

        $caja_desglose = $this->db->get_where('caja_desglose', array('id'=>$caja_pendiente->caja_desglose_id))->row();

        $traspaso_flag = false;
        if($caja_pendiente->tipo == 'TRASPASO'){
            $caja_desglose_d = $this->db->get_where('caja_desglose', array('id'=>$caja_pendiente->ref_id))->row();
            if($caja_pendiente->monto > $caja_desglose_d->saldo)
                $traspaso_flag = true;
        }

        if(($caja_pendiente->IO == 2 && $caja_pendiente->monto > $caja_desglose->saldo) || $traspaso_flag == true){
            echo json_encode(array('error' => 1));
        }
        else{
            $data_mov = array(
                'caja_desglose_id' => $caja_desglose->id,
                'usuario_id' => $this->session->userdata('nUsuCodigo'),
                'fecha_mov' => date('Y-m-d H:i:s'),
                'operacion' => $caja_pendiente->tipo,
                'medio_pago' => 'INTERNO',
                'saldo' => $caja_pendiente->monto,
                'saldo_old' => $caja_desglose->saldo,
                'ref_id' => $caja_pendiente->id
            );

            $new_saldo = 0;
            if($caja_pendiente->IO == 2){
                $data_mov['movimiento'] = 'EGRESO';
                $new_saldo = $caja_desglose->saldo - $caja_pendiente->monto;
            }
            else{
                $data_mov['movimiento'] = 'INGRESO';
                $new_saldo = $caja_desglose->saldo + $caja_pendiente->monto;
            }

            $this->cajas_mov_model->save_mov($data_mov);

            $this->db->where('id', $caja_desglose->id);
            $this->db->update('caja_desglose', array('saldo'=>$new_saldo));

            if($caja_pendiente->tipo == 'TRASPASO'){

                $caja_desglose_d = $this->db->get_where('caja_desglose', array('id'=>$caja_pendiente->ref_id))->row();

                $this->cajas_mov_model->save_mov(array(
                    'caja_desglose_id' => $caja_desglose_d->id,
                    'usuario_id' => $this->session->userdata('nUsuCodigo'),
                    'fecha_mov' => date('Y-m-d H:i:s'),
                    'operacion' => $caja_pendiente->tipo,
                    'medio_pago' => 'INTERNO',
                    'saldo' => $caja_pendiente->monto,
                    'saldo_old' => $caja_desglose_d->saldo,
                    'ref_id' => $caja_desglose->id,
                    'movimiento'=>'EGRESO'
                ));

                $this->db->where('id', $caja_desglose_d->id);
                $this->db->update('caja_desglose', array('saldo'=>$caja_desglose_d->saldo - $caja_pendiente->monto));
            }

            $this->db->where('id', $caja_pendiente->id);
            $this->db->update('caja_pendiente', array('estado'=>1));

            echo json_encode(array('success' => 1));
        }




    }

}

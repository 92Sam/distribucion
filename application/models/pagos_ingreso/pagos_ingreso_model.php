<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class pagos_ingreso_model extends CI_Model
{

    private $table = 'pagos_ingreso';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_ajuste_detalle($local = false)
    {

        if ($local != false) {
            $query = $this->db->where('local_id', $local);
            $query = $this->db->get('ajustedetalle');
            return $query->result();
        }
    }

    function get_by($campos, $filas)
    {
//si filas es igual a falso se ejecuta row. sino ejecuta row_array
        $this->db->where($campos);
        $query = $this->db->get('ajustedetalle');

        if ($filas != false) {
            return $query->result();
        } else {
            return $query->row_array();

        }
    }

    function guardar($data)
    {
        $this->db->trans_start(true);

        $this->db->trans_begin();

        $this->load->model('cajas/cajas_model');

        if ($data['medio_pago_id'] != '4') {
            unset($data['banco_id']);
        }

        $this->db->insert('pagos_ingreso', $data);
        $id = $this->db->insert_id();

        if ($data['medio_pago_id'] != '6') {
            $this->cajas_model->save_pendiente(array(
                'monto' => $data['pagoingreso_monto'],
                'tipo' => 'PAGOS',
                'IO' => 2,
                'ref_id' => $id
            ));
        } else {
            $this->load->model('kardex/kardex_model');
            $ingreso = $this->db->get_where('ingreso', array('id_ingreso' => $data['pagoingreso_ingreso_id']))->row();

            $this->kardex_model->insert_kardex(array(
                'fecha' => date('Y-m-d H:i:s'),
                'local_id' => $ingreso->local_id,
                'producto_id' => 0,
                'unidad_id' => 0,
                'serie' => '000',
                'numero' => '00000',
                'tipo_doc' => 7,
                'tipo_operacion' => 2,
                'cantidad' => 0,
                'costo_unitario' => $data['pagoingreso_monto'],
                'IO' => 1,
                'ref_id' => $id,
                'referencia' => $id,
                'total' => $data['pagoingreso_monto'],
            ));
        }


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {

            return $id;
        }

        $this->db->trans_off();

    }


    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $where = false, $group = false,
                             $order = false, $retorno = false)
    {
//si filas es igual a false entonces es un resutl que trae varios resultados
        //sino es una sola fila

        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);


        }

        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                $this->db->join($join[$i], $campos_join[$i]);
            }
        }
        if ($where != false) {
            $this->db->where($where);

        }
        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order);
        }

        $query = $this->db->get();

        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }


}

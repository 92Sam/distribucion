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

    function guardar($listadetalle)
    {
        $this->db->trans_start(true);

        $this->db->trans_begin();

        $this->load->model('cajas/cajas_model');

        foreach ($listadetalle as $row) {

            $list_cp = array(
                'pagoingreso_ingreso_id' => $row->id_ingreso,
                'pagoingreso_fecha' => date("Y-m-d H:i:s"),
                'pagoingreso_monto' => $row->cantidad_ingresada,
                'pagoingreso_restante' => $row->total_ingreso - ($row->suma + $row->cantidad_ingresada)
            );

            $this->db->insert('pagos_ingreso', $list_cp);
            $id = $this->db->insert_id();

            $this->cajas_model->save_pendiente(array(
                'monto'=>$row->cantidad_ingresada,
                'tipo'=>'PAGOS',
                'IO'=>2,
                'ref_id'=>$id
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

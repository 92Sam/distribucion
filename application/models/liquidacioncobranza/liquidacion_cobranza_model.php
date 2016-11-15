<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class liquidacion_cobranza_model extends CI_Model
{

    private $tabla = "liquidacion_cobranza";

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
                             $nombre_or, $where_or,
                             $group = false,
                             $order = false, $retorno = false)
    {
        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);
        }
        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    for ($t = 0; $t < count($tipo_join); $t++) {

                        if ($tipo_join[$t] != "") {

                            $this->db->join($join[$i], $campos_join[$i], $tipo_join[$t]);
                        }

                    }

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);
        }

        if ($nombre_in != false) {
            for ($i = 0; $i < count($nombre_in); $i++) {
                $this->db->where_in($nombre_in[$i], $where_in[$i]);
            }
        }

        if ($nombre_or != false) {
            for ($i = 0; $i < count($nombre_or); $i++) {
                $this->db->or_where($where_or);
            }
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

    function guardar_liquidacion($lista)
    {
        $this->db->trans_start(true);

        $this->db->trans_begin();

            $this->db->insert($this->tabla, $lista);
        $id=$this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {

            return $id;
        }

        $this->db->trans_off();

    }

    function guardar_liquidacion_detalle($lista)
    {
        $this->db->trans_start(true);

        $this->db->trans_begin();

        $this->db->insert('liquidacion_cobranza_detalle', $lista);
        $id=$this->db->insert_id();
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {

            return $id;
        }

        $this->db->trans_off();

    }



}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class tipos_gasto_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $query = $this->db->where('status_tipos_gasto', 1);
        $query = $this->db->get('tipos_gasto');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('tipos_gasto');
        return $query->row_array();
    }

    function insertar($tiposgasto)
    {

        $nombre = $this->input->post('nombre_tipos_gasto');
        $validar_nombre = sizeof($this->get_by('nombre_tipos_gasto', $nombre));

        if ($validar_nombre < 1) {

            $this->db->trans_start();
            $this->db->insert('tipos_gasto', $tiposgasto);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update($tiposgasto)
    {

        $produc_exite=$this->get_by('nombre_tipos_gasto', $tiposgasto['nombre_tipos_gasto']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_tipos_gasto']==$tiposgasto ['id_tipos_gasto']))) {

            $this->db->trans_start();
            $this->db->where('id_tipos_gasto', $tiposgasto['id_tipos_gasto']);
            $this->db->update('tipos_gasto', $tiposgasto);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        }else{
            return NOMBRE_EXISTE;
        }
    }


}

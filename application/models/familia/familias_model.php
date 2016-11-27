<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class familias_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_familias(){
        $query=$this->db->where('estatus_familia',1);
        $this->db->order_by('nombre_familia', 'asc');
        $query=$this->db->get('familia');
        return $query->result_array();
    }

    function get_by($campo, $valor){
        $this->db->where($campo,$valor);
        $query=$this->db->get('familia');
        return $query->row_array();
    }
    function set_familias(){

        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_familia', $nombre));

        if ($validar_nombre < 1) {

            $query_familia = array(

                'nombre_familia' => $this->input->post('nombre')

            );

            $this->db->trans_start();
            $this->db->insert('familia', $query_familia);

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

    function update_familias($grupo, $quehago = NULL)
    {

        $produc_exite=$this->get_by('nombre_familia', $grupo['nombre_familia']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_familia']==$grupo ['id_familia']))) {
            $this->db->trans_start();
            $this->db->where('id_familia', $grupo['id_familia']);
            $this->db->update('familia', $grupo);
            if ($quehago != NULL) {
                $modificar = array("producto_familia" => NULL);
                $this->db->where('producto_familia', $grupo['id_familia']);
                $this->db->update('producto', $modificar);
            }
            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

}

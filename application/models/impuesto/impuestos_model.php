<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class impuestos_model extends CI_Model {

    private $table = 'impuestos';

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_impuestos(){
        $this->db->where('estatus_impuesto',1);
        $query=$this->db->get($this->table);
        return $query->result_array();
    }
    function get_by($campo, $valor){
        $this->db->where($campo,$valor);
        $query=$this->db->get($this->table);
        return $query->row_array();
    }

    function set_impuestos($grupo){

        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_impuesto', $nombre));

        if ($validar_nombre < 1) {
        $this->db->trans_start ();
        $this->db->insert($this->table,$grupo);

        $this->db->trans_complete ();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status () === FALSE)
            return FALSE;
        else
            return TRUE;
        }else{
            return NOMBRE_EXISTE;
        }
    }

    function update_impuestos($grupo){


        $produc_exite=$this->get_by('nombre_impuesto', $grupo['nombre_impuesto']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_impuesto']==$grupo ['id_impuesto']))) {
        $this->db->trans_start();
        $this->db->where('id_impuesto', $grupo['id_impuesto']);
        $this->db->update($this->table, $grupo);

        $this->db->trans_complete ();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status () === FALSE)
            return FALSE;
        else
            return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }


}

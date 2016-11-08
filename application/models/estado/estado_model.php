<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class estado_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

    function get_all()
    {
        $query = $this->db->select('*');
        $query = $this->db->from('estados');
        $query = $this->db->join('pais', 'pais.id_pais=estados.pais_id');
        $query = $this->db->get();
        return $query->result_array();
    }



    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('estados');
        return $query->row_array();
    }

    function get_all_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('estados');
        return $query->result_array();
    }

    function insertar($estado)
    {

        $this->db->trans_start();
        $this->db->insert('estados', $estado);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update($estado)
    {

        $this->db->trans_start();
        $this->db->where('estados_id', $estado['estados_id']);
        $this->db->update('estados', $estado);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }


}

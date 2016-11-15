<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class banco_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

    function get_all()
    {
        $query = $this->db->where('banco_status', 1);
        $query = $this->db->get('banco');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('banco');
        return $query->row_array();
    }

    function insertar($banco)
    {

        $this->db->trans_start();
        $this->db->insert('banco', $banco);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update($banco)
    {

        $this->db->trans_start();
        $this->db->where('banco_id', $banco['banco_id']);
        $this->db->update('banco', $banco);

        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }
}

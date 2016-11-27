<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_estatus_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }
	
	function get_all()
    {
        $this->db->order_by('venta_id', 'asc');
        $query = $this->db->get('venta_estatus');
        
		return $query->result_array();
    }
	
	function get_by($campo, $valor, $result = false)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('venta_estatus');
        
		if ($result == true) {
			return $query->result_array();
		} else {
			return $query->row_array();
		}
    }

    function insert_estatus($data = array())
    {
		$data['fecha'] = date('Y-m-d h:m:s');
		
        $this->db->trans_start();
        $this->db->insert('venta_estatus', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
		}
    }
}

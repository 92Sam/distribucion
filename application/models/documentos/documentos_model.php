<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class documentos_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
		$this->db->select('documento_fiscal.*, documento_detalle.* ');
        $this->db->join('documento_detalle', 'documento_detalle.documento_fiscal_id = documento_detalle.documento_fiscal_id', 'left');
        
		$query = $this->db->get('documento_fiscal');
		
        return $query->result_array();
    }




    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
		$this->db->select('documento_fiscal.*, documento_detalle.* ');
        $this->db->join('documento_detalle', 'documento_detalle.documento_fiscal_id = documento_detalle.documento_fiscal_id', 'left');
		
        $query = $this->db->get('documento_fiscal');
		
        return $query->row_array();
    }
}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente_datos_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

   

    function get_all_by($valor)
    {
        $this->db->where('cliente_id', $valor);
        $query = $this->db->get('cliente_datos');
        return $query->result_array();
    }

}
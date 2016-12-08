<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas_mov_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function save_mov($data = array()){

        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert('caja_movimiento', $data);
    }


}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class gcm_users_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function getAll()
    {
             $query = $this->db->get('android_gcm_users');

        return $query->result_array();
    }

    function getUser($username)
    {
        $this->db->where('usuario', $username);

        $query = $this->db->get('android_gcm_users');

        return $query->row_array();
    }


    function saveUser($params)
    {


        $sql = "INSERT INTO android_gcm_users (usuario, codigo) VALUES ('" . stripslashes($params['usuario']) . "', '" . stripslashes($params['codigo']) . "')
    ON DUPLICATE KEY UPDATE codigo = '" . stripslashes($params['codigo']) . "' ; ";

        $this->db->query($sql);
        try {
            $this->db->trans_complete();
        } catch (Exception $e) {
            return $this->db->_error_message();
        }

        if ($this->db->trans_status() === FALSE) {
            return $this->db->_error_message();
        } else {
            return TRUE;
        }

    }


}
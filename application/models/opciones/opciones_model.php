<?php

/**
 * Created by IntelliJ IDEA.
 * User: Jhainey
 * Date: 18/03/2015
 * Time: 11:56 AM
 */
class opciones_model extends CI_Model
{

    public function __construct()
    {
        $this->load->database();
    }

    public function guardar_configuracion($configuraciones, $updateproductos)
    {


        $this->db->trans_start();


        foreach ($configuraciones as $conf) {
            if (!empty($conf)) {

                $this->db->where('config_key', $conf['config_key']);
                $this->db->update('configuraciones', $conf);

            }
        }

        $this->db->update('producto', $updateproductos);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;

    }


    public function get_opciones()
    {
        $this->db->select('*');
        $this->db->from('configuraciones');
        $query = $this->db->get();
        return $query->result_array();
    }


}

?>
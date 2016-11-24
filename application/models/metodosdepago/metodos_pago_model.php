<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class metodos_pago_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $query = $this->db->where('status_metodo', 1);
        $query = $this->db->get('metodos_pago');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('metodos_pago');
        return $query->row_array();
    }

    function insertar($metodospago)
    {
        $nombre = $this->input->post('nombre_metodo');
        $validar_nombre = sizeof($this->get_by('nombre_metodo', $nombre));

        if ($validar_nombre < 1) {


            $this->db->trans_start();
            $this->db->insert('metodos_pago', $metodospago);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update($metodospago)
    {
        $produc_exite = $this->get_by('nombre_metodo', $metodospago['nombre_metodo']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_metodo'] == $metodospago ['id_metodo']))) {

            $this->db->trans_start();
            $this->db->where('id_metodo', $metodospago['id_metodo']);
            $this->db->update('metodos_pago', $metodospago);

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

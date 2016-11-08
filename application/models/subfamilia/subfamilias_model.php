<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class subfamilias_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_subfamilias()
    {
        $query = $this->db->where('estatus_subfamilia', 1);
        $this->db->order_by('nombre_subfamilia', 'asc');
        $query = $this->db->get('subfamilia');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('subfamilia');
        return $query->row_array();
    }

    function set_subfamilias()
    {

        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_subfamilia', $nombre));

        if ($validar_nombre < 1) {

            $query_subfamilia = array(

                'nombre_subfamilia' => $this->input->post('nombre')

            );

            $this->db->trans_start();
            $this->db->insert('subfamilia', $query_subfamilia);

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

    function update_subfamilias($grupo, $quehago = NULL)
    {

        $produc_exite = $this->get_by('nombre_subfamilia', $grupo['nombre_subfamilia']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_subfamilia'] == $grupo ['id_subfamilia']))) {
            $this->db->trans_start();
            $this->db->where('id_subfamilia', $grupo['id_subfamilia']);
            $this->db->update('subfamilia', $grupo);
            if ($quehago != NULL) {
                $modificar = array("producto_subfamilia" => NULL);
                $this->db->where('producto_subfamilia', $grupo['id_subfamilia']);
                $this->db->update('producto', $modificar);
            }
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

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class subgrupos_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_subgrupos()
    {
        $this->db->where('estatus_subgrupo', 1);
        $this->db->order_by('nombre_subgrupo', 'asc');
        $query = $this->db->get('subgrupo');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('subgrupo');
        return $query->row_array();
    }

    function set_subgrupos()
    {

        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_subgrupo', $nombre));

        if ($validar_nombre < 1) {
            $query_grupo = array(

                'nombre_subgrupo' => $nombre

            );

            $this->db->trans_start();
            $this->db->insert('subgrupo', $query_grupo);

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


    function update_subgrupos($grupo, $quehago = NULL ){


        $produc_exite = $this->get_by('nombre_subgrupo', $grupo['nombre_subgrupo']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_subgrupo'] == $grupo ['id_subgrupo']))) {

            $this->db->trans_start();
            $this->db->where('id_subgrupo', $grupo['id_subgrupo']);
            $this->db->update('subgrupo', $grupo);
            if ($quehago != NULL)
            {
                $modificar = array("producto_subgrupo" => NULL);
                $this->db->where('producto_subgrupo', $grupo['id_subgrupo']);
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

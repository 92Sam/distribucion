<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class pais_model extends CI_Model 
{
	function __construct() 
	{
		parent::__construct();
	}

    function get_all()
    {
        $query = $this->db->get('pais');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('pais');
        return $query->row_array();
    }

    function insertar($pais)
    {
        $nombre = $this->input->post('nombre_pais');
        $validar_nombre = sizeof($this->get_by('nombre_pais', $nombre));

        if ($validar_nombre < 1) {

            $this->db->trans_start();
            $this->db->insert('pais', $pais);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update($pais)
    {
        $produc_exite=$this->get_by('nombre_pais', $pais['nombre_pais']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_pais']==$pais ['id_pais']))) {
            $this->db->trans_start();
            $this->db->where('id_pais', $pais['id_pais']);
            $this->db->update('pais', $pais);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }
}

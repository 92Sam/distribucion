<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ciudad_model extends CI_Model {

	function __construct() {
		parent::__construct();
	}

    function get_all()
    {
       /* $query = $this->db->where('ciudad_status', 1); */
        $query = $this->db->select('*');
        $query = $this->db->from('ciudades');
        $query = $this->db->join('estados', 'estados.estados_id=ciudades.estado_id');
        $query = $this->db->join('pais', 'estados.pais_id=pais.id_pais');
        $query = $this->db->get();
        return $query->result_array();
    }



    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->select('*');
        $query = $this->db->from('ciudades');
        $query = $this->db->join('estados', 'estados.estados_id=ciudades.estado_id');
        $query = $this->db->join('pais', 'estados.pais_id=pais.id_pais');
        $query = $this->db->get();
        return $query->row_array();
    }

    function get_all_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('ciudades');
        return $query->result_array();
    }

    function insertar($ciudad)
    {

        $this->db->trans_start();
        $this->db->insert('ciudades', $ciudad);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update($ciudad)
    {

        $this->db->trans_start();
        $this->db->where('ciudad_id', $ciudad['ciudad_id']);
        $this->db->update('ciudades', $ciudad);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }


}

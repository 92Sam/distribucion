<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class escalas_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $this->db->select('escalas.*, escala_producto.*');
        $this->db->join('escalas', 'escalas.escala_id = escala_producto.producto', 'left');
        $query = $this->db->get('escala_producto');
		
        return $query->result_array();
    }

    function get_by($campo, $valor, $result = false)
    {
		$this->db->where($campo, $valor);
        $this->db->select('escalas.*, escala_producto.*, producto.producto_nombre, unidades.nombre_unidad');
        $this->db->join('escalas', 'escalas.escala_id = escala_producto.escala', 'left');
        $this->db->join('producto', 'escala_producto.producto = producto.producto_id', 'left');
        $this->db->join('unidades', 'escala_producto.unidad = unidades.id_unidad', 'left');
        $query = $this->db->get('escala_producto');
		
		if ($result == true) {
			return $query->result_array();
		} else {
			return $query->row_array();
		}
    }

    function insertar($array = array())
    {
        $this->db->trans_start();
        $this->db->insert('escalas', $array);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
		}
    }

    function update($array = array(), $find = 'escala_id', $field = 'escala_id')
    {
        $this->db->trans_start();
        $this->db->where($find, $array[$field]);
        $this->db->update('escalas', $array);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
		}
    }
}
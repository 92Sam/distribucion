<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class unidades_model extends CI_Model
{

    private $table = 'unidades';

    function __construct()
    {
        parent::__construct();
        $this->load->database();


    }

    function get_unidades()
    {
        $this->db->where('estatus_unidad', 1);
        $this->db->order_by('nombre_unidad', 'asc');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    function set_unidades($grupo)
    {
        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_unidad', $nombre));

        if ($validar_nombre < 1) {
            $this->db->trans_start();
            $this->db->insert($this->table, $grupo);

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

    function update_unidades($grupo)
    {


        $produc_exite = $this->get_by('nombre_unidad', $grupo['nombre_unidad']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_unidad'] == $grupo ['id_unidad']))) {

            $this->db->trans_start();
            $this->db->where('id_unidad', $grupo['id_unidad']);
            $this->db->update($this->table, $grupo);

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

    function get_by_producto($producto)
    {
        $this->db->select('*');
        $this->db->from('unidades_has_producto');
        $this->db->where('unidades_has_producto.producto_id', $producto);
        $this->db->join('unidades', 'unidades_has_producto.id_unidad=unidades.id_unidad');
        $this->db->join('producto', 'producto.producto_id=unidades_has_producto.producto_id');
        $this->db->order_by('orden', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }


}

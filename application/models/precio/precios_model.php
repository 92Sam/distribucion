<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class precios_model extends CI_Model {

    private $table = 'precios';


    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_precios(){
        $this->db->where('estatus_precio',1);
        $query=$this->db->get($this->table);
        return $query->result_array();
    }
    function get_by($campo, $valor){
        $this->db->where('estatus_precio',1);
        $this->db->where($campo,$valor);
        $query=$this->db->get($this->table);
        return $query->row_array();
    }

    function get_all_by($campo, $valor){
        $this->db->where('estatus_precio',1);
        $this->db->where($campo,$valor);
        $query=$this->db->get($this->table);
        return $query->result_array();
    }

    function set_precios($grupo){
        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_precio', $nombre));

        if ($validar_nombre < 1) {

        $this->db->trans_start ();
        $this->db->insert($this->table,$grupo);

        $this->db->trans_complete ();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status () === FALSE)
            return FALSE;
        else
            return TRUE;
        }else{
            return NOMBRE_EXISTE;
        }
    }

    function update_precios($grupo){



        $produc_exite=$this->get_by('nombre_precio', $grupo['nombre_precio']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_precio']==$grupo ['id_precio']))) {
        $this->db->trans_start();
        $this->db->where('id_precio', $grupo['id_precio']);
        $this->db->update($this->table, $grupo);

        $this->db->trans_complete ();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status () === FALSE)
            return FALSE;
        else
            return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }


    function get_by_unidad_and_producto($producto, $unidad){
        $this->db->select('*');
        $this->db->from('unidades_has_precio');
        $this->db->join('precios', 'unidades_has_precio.id_precio=precios.id_precio');
        $this->db->join('unidades', 'unidades_has_precio.id_unidad=unidades.id_unidad');
        $this->db->where('unidades_has_precio.id_producto', $producto);
        $this->db->where('unidades_has_precio.id_unidad', $unidad);
        $this->db->where('precios.mostrar_precio', 1);
        $this->db->where('precios.estatus_precio', 1);
        $query =$this->db->get();
        return $query->result_array();
    }

    function get_all_by_producto($producto){
        $this->db->select('*');
        $this->db->from('unidades_has_precio');
        $this->db->join('precios', 'unidades_has_precio.id_precio=precios.id_precio');
        $this->db->join('unidades', 'unidades_has_precio.id_unidad=unidades.id_unidad');
        $this->db->join('unidades_has_producto', 'unidades_has_precio.id_unidad=unidades_has_producto.id_unidad and unidades_has_precio.id_producto=unidades_has_producto.producto_id', 'left');
        $this->db->where('unidades_has_precio.id_producto', $producto);
        $this->db->where('unidades_has_producto.id_unidad IS NOT NULL', null );
        $this->db->order_by('unidades_has_producto.orden', 'asc');
        $this->db->order_by('unidades_has_precio.id_precio', 'asc');
        $query =$this->db->get();
        return $query->result_array();
    }

    function get_by_precio_and_producto($producto, $precio){
        $this->db->select('*');
        $this->db->from('unidades_has_precio');
        $this->db->join('producto', 'unidades_has_precio.id_producto=producto.producto_id');
        $this->db->join('impuestos', 'producto.producto_impuesto=impuestos.id_impuesto', 'left');
        $this->db->join('precios', 'unidades_has_precio.id_precio=precios.id_precio');
        $this->db->join('unidades', 'unidades_has_precio.id_unidad=unidades.id_unidad');
        $this->db->join('unidades_has_producto', 'unidades_has_precio.id_unidad=unidades_has_producto.id_unidad and unidades_has_precio.id_producto=unidades_has_producto.producto_id ');
        $this->db->where('unidades_has_precio.id_precio', $precio);
        $this->db->where('unidades_has_precio.id_producto', $producto);
        $this->db->order_by('unidades_has_producto.orden', 'asc');
        $query =$this->db->get();
        return $query->result_array();
    }


}

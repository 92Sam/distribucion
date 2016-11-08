<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class kardex_model extends CI_Model
{

    private $table = 'kardex';

    function __construct()
    {
        parent::__construct();
        $this->load->database();

    }

    function getKardex($where, $order)
    {
        $this->db->select('unidades.*, kardex.*, producto.*, cliente.*, proveedor.*');
        $this->db->from('kardex');
        $this->db->join('producto', 'producto.producto_id=kardex.cKardexProducto');
        $this->db->join('unidades', 'unidades.id_unidad=kardex.cKardexUnidadMedida','left');
        $this->db->join('local', 'local.int_local_id=kardex.cKardexAlmacen');
        $this->db->join('cliente', 'cliente.id_cliente=kardex.cKardexCliente', 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=kardex.cKardexProveedor', 'left');
        $this->db->where($where);
        $this->db->order_by($order);
        $query = $this->db->get();
      //  echo $this->db->last_query();
        return $query->result_array();
    }

    function getKardexFiscal($where, $order)
    {
        $this->db->select('unidades.*, kardex_fiscal.*, producto.*, cliente.*');
        $this->db->from('kardex_fiscal');
        $this->db->join('producto', 'producto.producto_id=kardex_fiscal.cKardexProducto');
        $this->db->join('unidades', 'unidades.id_unidad=kardex_fiscal.cKardexUnidadMedida','left');
        $this->db->join('local', 'local.int_local_id=kardex_fiscal.cKardexAlmacen');
        $this->db->join('cliente', 'cliente.id_cliente=kardex_fiscal.cKardexCliente', 'left');

        $this->db->where($where);
        $this->db->order_by($order);
        $query = $this->db->get();

        return $query->result_array();
    }

    function set_batch($kardex)
    {
        $result = $this->db->insert_batch('kardex', $kardex);
        if ($result)
            return TRUE;
        else
            return FALSE;
    }

}

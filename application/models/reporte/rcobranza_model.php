<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class rcobranza_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_cobranzas($params = array())
    {
        $this->db->select("
            documento_venta.nombre_tipo_documento as documento_nombre, 
            documento_venta.documento_Serie as documento_serie, 
            documento_venta.documento_Numero as documento_numero, 
            cliente.razon_social as cliente_nombre, 
            venta.fecha as fecha_documento, 
            venta.fecha as fecha_venta, 
            venta.total as total_deuda, 
            zonas.zona_nombre as cliente_zona_nombre, 
            usuario.nombre as vendedor_nombre 
        ")
            ->from('venta')
            ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
            ->join('cliente', 'venta.id_cliente = cliente.id_cliente')
            ->join('usuario', 'venta.id_vendedor = usuario.nUsuCodigo')
            ->join('zonas', 'cliente.id_zona = zonas.zona_id');

        return $this->db->get()->result();

    }

}

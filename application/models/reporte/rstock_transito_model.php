<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class rstock_transito_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_stock_transito($params = array())
    {
        $this->db->select("
            historial_pedido_proceso.proceso_id as proceso_id,
            historial_pedido_proceso.pedido_id as pedido_id,
            historial_pedido_detalle.producto_id as producto_id,
            producto.producto_nombre as producto_nombre,
            producto.presentacion as presentacion,
            unidades.nombre_unidad as unidad_nombre,
            SUM(historial_pedido_detalle.stock) as stock
        ");

        $stocks = $this->aplicar_filtros($params);

        foreach ($stocks as $stock) {
            $this->db->select('SUM(historial_pedido_detalle.stock) as liquidado');
            $params['proceso_transito'] = PROCESO_LIQUIDAR;
            $params['producto_id'] = $stock->producto_id;
            $stock->liquidado = $this->aplicar_filtros($params);

        }

        return $stocks;

    }

    private function aplicar_filtros($params)
    {
        $this->db->from('historial_pedido_proceso')
            ->join('historial_pedido_detalle', 'historial_pedido_detalle.historial_pedido_proceso_id = historial_pedido_proceso.id')
            ->join('producto', 'producto.producto_id = historial_pedido_detalle.producto_id')
            ->join('unidades', 'unidades.id_unidad = historial_pedido_detalle.unidad_id')
            ->join('venta', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->join('cliente', 'venta.id_cliente = cliente.id_cliente')
            ->join('usuario', 'venta.id_vendedor = usuario.nUsuCodigo')
            ->join('zonas', 'cliente.id_zona = zonas.zona_id')
            ->group_by('historial_pedido_detalle.producto_id');

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('historial_pedido_proceso.created_at >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('historial_pedido_proceso.created_at <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['producto_id']))
            $this->db->where('historial_pedido_detalle.producto_id', $params['producto_id']);

        if (isset($params['proceso_transito']))
            $this->db->where('historial_pedido_proceso.proceso_id', $params['proceso_transito']);

        if (isset($params['vendedor_id']) && $params['vendedor_id'] != 0)
            $this->db->where('usuario.nUsuCodigo', $params['vendedor_id']);

        if (isset($params['cliente_id']) && $params['cliente_id'] != 0)
            $this->db->where('cliente.id_cliente', $params['cliente_id']);

        if (isset($params['zonas_id']) && count($params['zonas_id']))
            $this->db->where_in('cliente.id_zona', $params['zonas_id']);

        if (isset($params['producto_id'])) {
            $result = $this->db->get()->row();
            return $result != NULL ? $result->liquidado : 0;
        } else
            return $this->db->get()->result();
    }

}

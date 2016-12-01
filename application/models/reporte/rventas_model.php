<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class rventas_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_ventas($params = array())
    {
        $ventas = new stdClass();

        $ventas->total_completado = $this->count_ventas('COMPLETADO', $params);
        $ventas->total_cancelada = $this->count_ventas('CANCELADA', $params);
        $ventas->total_rechazado = $this->count_ventas('RECHAZADO', $params);
        $ventas->total_proceso = $this->count_ventas('PROCESO', $params);
        $ventas->total_cobranzas = $this->count_ventas('COBRANZAS', $params);


        $ventas->importe_completado = $this->get_importe('COMPLETADO', $params);
        $ventas->importe_cobranza = $this->get_importe('COBRANZAS', $params);

        $ventas->desgloses = $this->get_desglose($params);

        return $ventas;

    }

    function get_desglose($params)
    {

        if ($params['desglose'] == '1') {
            $this->db->select('zonas.zona_nombre as desglose, zonas.zona_id as desglose_id');
            $this->db->group_by('zonas.zona_id');
        }
        if ($params['desglose'] == '2') {
            $this->db->select('usuario.nombre as desglose, usuario.nUsuCodigo as desglose_id');
            $this->db->group_by('usuario.nUsuCodigo');
        }
        if ($params['desglose'] == '3') {
            $this->db->select('cliente.razon_social as desglose, cliente.id_cliente as desglose_id');
            $this->db->group_by('cliente.id_cliente');
        }

        $this->aplicar_from();

        $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR);
        $this->db->where('venta.venta_status !=', 'RECHAZADO');
        $this->db->where('venta.venta_status !=', 'ANULADO');

        $this->db->or_where('historial_pedido_proceso.proceso_id !=', PROCESO_LIQUIDAR);
        $this->db->where('historial_pedido_proceso.actual', 1);

        $this->aplicar_filtros($params);

        $result = $this->db->get()->result();


        foreach ($result as $desglose) {
            $desglose->total_completado = $this->count_ventas('COMPLETADO', $params, 1, $desglose->desglose_id);
            $desglose->total_cancelada = $this->count_ventas('CANCELADA', $params, 1, $desglose->desglose_id);
            $desglose->total_rechazado = $this->count_ventas('RECHAZADO', $params, 1, $desglose->desglose_id);
            $desglose->total_proceso = $this->count_ventas('PROCESO', $params, 1, $desglose->desglose_id);
            $desglose->total_cobranzas = $this->count_ventas('COBRANZAS', $params, 1, $desglose->desglose_id);


            $desglose->importe_completado = $this->get_importe('COMPLETADO', $params, 1, $desglose->desglose_id);
            $desglose->importe_cobranza = $this->get_importe('COBRANZAS', $params, 1, $desglose->desglose_id);
        }

        return $result;
    }

    function count_ventas($estado, $params, $desglose = 0, $desglose_id = 0)
    {

        $this->db->select('COUNT(venta.venta_id) as total');
        $this->aplicar_from();

        if ($estado == 'COMPLETADO') {
            $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR);
            $this->db->where('venta.venta_status !=', 'RECHAZADO');
            $this->db->where('venta.venta_status !=', 'ANULADO');
        }

        if ($estado == 'CANCELADA') {
            $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR);
            $this->db->where('venta.venta_status !=', 'RECHAZADO');
            $this->db->where('venta.venta_status !=', 'ANULADO')
                ->where('venta.total <= credito.dec_credito_montodebito');
        }

        if ($estado == 'RECHAZADO') {
            $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR);
            $this->db->where_in('venta.venta_status', array('RECHAZADO', 'ANULADO'))
                ->where('venta.total > credito.dec_credito_montodebito');
        }

        if ($estado == 'PROCESO') {
            $this->db->where('historial_pedido_proceso.proceso_id !=', PROCESO_LIQUIDAR);
            $this->db->where('historial_pedido_proceso.actual', 1);
        }

        if ($estado == 'COBRANZAS') {
            $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
                ->where('venta.venta_status !=', 'RECHAZADO')
                ->where('venta.total > credito.dec_credito_montodebito')
                ->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));
        }

        $this->aplicar_filtros($params);

        if ($desglose == 1)
            $this->aplicar_desglose($params, $desglose_id);

        $result = $this->db->get()->row();
        return $result->total;
    }

    function get_importe($estado, $params, $desglose = 0, $desglose_id = 0)
    {
        $this->aplicar_from();
        if ($estado == 'COMPLETADO') {
            $this->db->select('SUM(venta.total) as total');
            $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR);
            $this->db->where('venta.venta_status !=', 'RECHAZADO');
            $this->db->where('venta.venta_status !=', 'ANULADO');
        }

        if ($estado == 'COBRANZAS') {
            $this->db->select('SUM(credito.dec_credito_montodebito) as total');
            $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
                ->where('venta.venta_status !=', 'RECHAZADO')
                ->where('venta.total > credito.dec_credito_montodebito')
                ->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));
        }

        $this->aplicar_filtros($params);


        if ($desglose == 1)
            $this->aplicar_desglose($params, $desglose_id);


        $result = $this->db->get()->row();

        return $result->total;
    }

    function aplicar_desglose($params, $desglose_id)
    {
        if ($params['desglose'] == '1') {
            $this->db->where('zonas.zona_id', $desglose_id);
        }
        if ($params['desglose'] == '2') {
            $this->db->where('usuario.nUsuCodigo', $desglose_id);
        }
        if ($params['desglose'] == '3') {
            $this->db->where('cliente.id_cliente', $desglose_id);
        }
    }

    function aplicar_filtros($params)
    {
        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('historial_pedido_proceso.created_at >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('historial_pedido_proceso.created_at <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['vendedor_id']) && $params['vendedor_id'] != 0)
            $this->db->where('usuario.nUsuCodigo', $params['vendedor_id']);

        if (isset($params['cliente_id']) && $params['cliente_id'] != 0)
            $this->db->where('cliente.id_cliente', $params['cliente_id']);

        if (isset($params['zonas_id']) && count($params['zonas_id']))
            $this->db->where_in('cliente.id_zona', $params['zonas_id']);
    }

    function aplicar_from()
    {
        $this->db->from('venta')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
            ->join('cliente', 'venta.id_cliente = cliente.id_cliente')
            ->join('usuario', 'venta.id_vendedor = usuario.nUsuCodigo')
            ->join('zonas', 'cliente.id_zona = zonas.zona_id');
    }

}

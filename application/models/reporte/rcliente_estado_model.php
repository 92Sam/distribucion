<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class rcliente_estado_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_estado_cuenta($params = array())
    {
        $this->db->select("
            cliente.id_cliente as cliente_id,
            cliente.razon_social as cliente_nombre,
            zonas.zona_nombre as cliente_zona_nombre,
            usuario.nombre as vendedor_nombre, 
            SUM(venta.total) as subtotal_venta,
            SUM(credito.dec_credito_montodebito) as subtotal_pago
        ")
            ->from('cliente')
            ->join('venta', 'venta.id_cliente = cliente.id_cliente')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->join('zonas', 'cliente.id_zona = zonas.zona_id')
            ->join('usuario', 'venta.id_vendedor = usuario.nUsuCodigo')
            ->where('cliente.cliente_status', 1)
            ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
            ->where('venta.venta_status !=', 'RECHAZADO')
            ->where('venta.venta_status !=', 'ANULADO')
            ->group_by('cliente.id_cliente');

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('historial_pedido_proceso.created_at >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('historial_pedido_proceso.created_at <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['estado']) && $params['estado'] != 0) {
            switch ($params['estado']) {
                case 1: {
                    $this->db->where('credito.var_credito_estado', CREDITO_CANCELADO);
                    break;
                }
                case 2: {
                    $this->db->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));
                    break;
                }
            }
        }

        if (isset($params['cliente_id']) && $params['cliente_id'] != 0)
            $this->db->where('cliente.id_cliente', $params['cliente_id']);

        if (isset($params['vendedor_id']) && $params['vendedor_id'] != 0)
            $this->db->where('usuario.nUsuCodigo', $params['vendedor_id']);

        if (isset($params['zonas_id']) && count($params['zonas_id']))
            $this->db->where_in('cliente.id_zona', $params['zonas_id']);

        $clientes = $this->db->get()->result();

        foreach ($clientes as $cliente) {
            $cliente->cobranzas = $this->get_cobranzas_by_cliente($cliente->cliente_id, $params);

            $this->db->select("
                SUM(historial_pagos_clientes.historial_monto) as monto,
            ")
                ->from('historial_pagos_clientes')
                ->join('venta', 'venta.venta_id = historial_pagos_clientes.credito_id')
                ->join('credito', 'credito.id_venta = historial_pagos_clientes.credito_id')
                ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
                ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
                ->where('venta.venta_status !=', 'RECHAZADO')
                ->where('venta.venta_status !=', 'ANULADO')
                ->where('venta.id_cliente', $cliente->cliente_id)
                ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE');

            if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
                $this->db->where('historial_pedido_proceso.created_at >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
                $this->db->where('historial_pedido_proceso.created_at <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
            }

            if (isset($params['estado']) && $params['estado'] != 0) {
                switch ($params['estado']) {
                    case 1: {
                        $this->db->where('credito.var_credito_estado', CREDITO_CANCELADO);
                        break;
                    }
                    case 2: {
                        $this->db->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));
                        break;
                    }
                }
            }
            $pagado_pendientes = $this->db->get()->row();

            $cliente->pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;
            $cliente->subtotal_pago -= $cliente->pagado_pendientes;
        }

        return $clientes;

    }

    function get_cobranzas_by_cliente($cliente_id, $params)
    {
        $this->db->select("
            venta.venta_id as venta_id,
            documento_venta.nombre_tipo_documento as documento_nombre, 
            documento_venta.documento_Serie as documento_serie, 
            documento_venta.documento_Numero as documento_numero, 
            historial_pedido_proceso.created_at as fecha_venta, 
            venta.total as total_deuda, 
            credito.dec_credito_montodebito as actual,
            (venta.total - credito.dec_credito_montodebito)  as credito,
            venta.venta_status as venta_estado,
            DATEDIFF(CURDATE(), (historial_pedido_proceso.created_at)) as atraso
        ")
            ->from('venta')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
            ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
            ->where('venta.id_cliente', $cliente_id);

        $this->db->where('venta.venta_status !=', 'ANULADO');
        $this->db->where('venta.venta_status !=', 'RECHAZADO');

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('historial_pedido_proceso.created_at >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('historial_pedido_proceso.created_at <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['estado']) && $params['estado'] != 0) {
            switch ($params['estado']) {
                case 1: {
                    $this->db->where('credito.var_credito_estado', CREDITO_CANCELADO);
                    break;
                }
                case 2: {
                    $this->db->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));
                    break;
                }
            }
        }


        $cobranzas = $this->db->get()->result();

        foreach ($cobranzas as $cobranza) {
            $pagado_pendientes = $this->db->select("
                SUM(historial_pagos_clientes.historial_monto) as monto,
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE')
                ->group_by('credito_id')
                ->get()->row();

            $cobranza->pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;
            $cobranza->actual = $cobranza->actual - $cobranza->pagado_pendientes;
            $cobranza->credito = $cobranza->credito + $cobranza->pagado_pendientes;

            $cobranza->detalles = $this->db->select("
                historial_pagos_clientes.historial_fecha as fecha,
                historial_pagos_clientes.historial_monto as monto,
                metodos_pago.nombre_metodo as tipo_pago_nombre,
                historial_pagos_clientes.historial_estatus as estado,
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'CONFIRMADO')
                ->order_by('historial_pagos_clientes.historial_fecha', 'ASC ')
                ->get()->result();

            $pagado_pendientes = $this->db->select("
                SUM(historial_pagos_clientes.historial_monto) as monto,
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE')
                ->group_by('credito_id')
                ->get()->row();

            $cobranza->pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;

            $pagado_confirmado = $this->db->select("
                SUM(historial_pagos_clientes.historial_monto) as monto,
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'CONFIRMADO')
                ->group_by('credito_id')
                ->get()->row();

            $cobranza->pagado_confirmados = isset($pagado_confirmado->monto) ? $pagado_confirmado->monto : 0;
        }

        return $cobranzas;
    }

}

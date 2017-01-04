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
            venta.venta_id as venta_id,
            documento_venta.nombre_tipo_documento as documento_nombre, 
            documento_venta.documento_Serie as documento_serie, 
            documento_venta.documento_Numero as documento_numero, 
            cliente.razon_social as cliente_nombre, 
            venta.fecha as fecha_documento, 
            historial_pedido_proceso.created_at as fecha_venta, 
            venta.total as total_deuda, 
            credito.dec_credito_montodebito as actual, 
            (venta.total - credito.dec_credito_montodebito)  as saldo, 
            zonas.zona_nombre as cliente_zona_nombre, 
            usuario.nombre as vendedor_nombre, 
            DATEDIFF(CURDATE(), (historial_pedido_proceso.created_at)) as atraso
        ")
            ->from('venta')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
            ->join('cliente', 'venta.id_cliente = cliente.id_cliente')
            ->join('usuario', 'venta.id_vendedor = usuario.nUsuCodigo')
            ->join('zonas', 'cliente.id_zona = zonas.zona_id')
            ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
            ->where('venta.venta_status !=', 'RECHAZADO')
            ->where('venta.venta_status !=', 'ANULADO')
            ->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));

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

        if (isset($params['dif_deuda']) && isset($params['dif_deuda_value']) && $params['dif_deuda_value'] > 0) {
            if ($params['dif_deuda'] == 1)
                $this->db->where('venta.total >=', $params['dif_deuda_value']);
            elseif ($params['dif_deuda'] == 2)
                $this->db->where('venta.total <=', $params['dif_deuda_value']);
        }

        if (isset($params['atraso']) && $params['atraso'] != 0) {
            switch ($params['atraso']) {
                case 1: {
                    $this->db->where('DATEDIFF(CURDATE(), (historial_pedido_proceso.created_at)) <= 7');
                    break;
                }
                case 2: {
                    $this->db->where('DATEDIFF(CURDATE(), (historial_pedido_proceso.created_at)) > 7');
                    $this->db->where('DATEDIFF(CURDATE(), (historial_pedido_proceso.created_at)) <= 15');
                    break;
                }
                case 3: {
                    $this->db->where('DATEDIFF(CURDATE(), (historial_pedido_proceso.created_at)) > 15');
                    $this->db->where('DATEDIFF(CURDATE(), (historial_pedido_proceso.created_at)) <= 30');
                    break;
                }
                case 4: {
                    $this->db->where('DATEDIFF(CURDATE(), (historial_pedido_proceso.created_at)) > 30');
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
            $cobranza->saldo = $cobranza->saldo + $cobranza->pagado_pendientes;
            
            $cobranza->detalles = $this->db->select("
                historial_pagos_clientes.historial_fecha as fecha,
                historial_pagos_clientes.historial_monto as monto,
                metodos_pago.nombre_metodo as tipo_pago_nombre
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->where('historial_pagos_clientes.historial_estatus', 'CONFIRMADO')
                ->order_by('historial_pagos_clientes.historial_fecha', 'ASC ')
                ->get()->result();
        }

        return $cobranzas;

    }

}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_cobro_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function get_pagos_pendientes($params = array())
    {
        $this->db->select("
            cliente.id_cliente as cliente_id,
            cliente.razon_social as cliente_nombre,
            zonas.zona_nombre as cliente_zona_nombre,
            usuario.nombre as vendedor_nombre, 
            usuario.nUsuCodigo as vendedor_id, 
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

        $this->db->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));


        if (isset($params['cliente_id']) && $params['cliente_id'] != 0)
            $this->db->where('cliente.id_cliente', $params['cliente_id']);

        if (isset($params['vendedor_id']) && $params['vendedor_id'] != 0)
            $this->db->where('usuario.nUsuCodigo', $params['vendedor_id']);

        if (isset($params['zonas_id']) && count($params['zonas_id']))
            $this->db->where_in('cliente.id_zona', $params['zonas_id']);

        $clientes = $this->db->get()->result();

        foreach ($clientes as $cliente) {
            $cliente->cobranzas = $this->get_cobranzas_detalles($cliente->cliente_id, $params);
        }

        return $this->get_vendedor_pendiente($clientes);
    }

    function get_cobranzas_detalles($cliente_id, $params)
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

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('historial_pedido_proceso.created_at >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('historial_pedido_proceso.created_at <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }


        $this->db->where('venta.venta_status !=', 'RECHAZADO');
        $this->db->where('venta.venta_status !=', 'ANULADO');
        $this->db->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));


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

    function get_vendedor_pendiente($clientes)
    {
        $vendedor = array();
        foreach ($clientes as $cliente) {
            foreach ($cliente->cobranzas as $cobranza) {
                if (!isset($vendedor[$cliente->vendedor_id]))
                    $vendedor[$cliente->vendedor_id] = 0;
                $vendedor[$cliente->vendedor_id] += $cobranza->pagado_pendientes;
            }
        }

        foreach ($clientes as $cliente) {
            $cliente->vendedor_pendiente = $vendedor[$cliente->vendedor_id];
        }

        return $clientes;
    }

    function get_pagos($pedido_id, $estado = "")
    {

        $cobranza = new stdClass();

        $historial_pedido = $this->db->get_where('historial_pedido_proceso', array(
            'pedido_id' => $pedido_id,
            'proceso_id' => PROCESO_LIQUIDAR
        ))->row();

        $liquidacion = $this->db->select('liquidacion_monto_cobrado')->from('consolidado_detalle')
            ->where('consolidado_detalle.pedido_id', $pedido_id)->get()->row();
        $cobranza->liquidacion = new stdClass();
        $cobranza->liquidacion->fecha = $historial_pedido->created_at;
        $cobranza->liquidacion->monto = $liquidacion->liquidacion_monto_cobrado != null ? $liquidacion->liquidacion_monto_cobrado : 0;
        $cobranza->liquidacion->pago_id = 0;
        $cobranza->liquidacion->pago_nombre = 'Liquidado';
        $cobranza->liquidacion->banco_nombre = '';
        $cobranza->liquidacion->num_oper = '';
        $cobranza->liquidacion->estado = 'CONFIRMADO';

        $this->db->select("
                historial_pagos_clientes.credito_id as venta_id,
                historial_pagos_clientes.historial_fecha as fecha,
                historial_pagos_clientes.historial_monto as monto,
                historial_pagos_clientes.historial_tipopago as pago_id,
                metodos_pago.nombre_metodo as pago_nombre,
                banco.banco_nombre as banco_nombre,
                banco.banco_id as banco_id,
                historial_pagos_clientes.pago_data as num_oper,
                historial_pagos_clientes.historial_estatus as estado
            ")
            ->from('historial_pagos_clientes')
            ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
            ->join('banco', 'banco.banco_id = historial_pagos_clientes.historial_banco_id', 'left')
            ->where('credito_id', $pedido_id);
        if ($estado == 'PENDIENTE')
            $this->db->where('historial_pagos_clientes.historial_estatus', $estado);
        elseif ($estado == 'CONFIRMADO')
            $this->db->where('historial_pagos_clientes.historial_estatus', $estado);

        $cobranza->detalles = $this->db->get()->result();

        return $cobranza;
    }

    function get_pagos_by_vendedor($vendedor_id)
    {
        $this->db->select("
            venta.venta_id as venta_id,
            documento_venta.nombre_tipo_documento as documento_nombre, 
            documento_venta.documento_Serie as documento_serie, 
            documento_venta.documento_Numero as documento_numero, 
        ")
            ->from('venta')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
            ->join('historial_pagos_clientes', 'historial_pagos_clientes.credito_id = venta.venta_id')
            ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
            ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE')
            ->where('venta.id_vendedor', $vendedor_id)
            ->group_by('venta.venta_id');

        $cobranzas = $this->db->get()->result();
        $result = new stdClass();
        $result->pendientes = array();
        $result->espera = array();

        foreach ($cobranzas as $cobranza) {
            $cobranza->pagos_pendientes = $this->get_pagos($cobranza->venta_id, 'PENDIENTE');

            foreach ($cobranza->pagos_pendientes->detalles as $pago) {
                $temp = new stdClass();
                $temp->documento = $cobranza->documento_serie . ' - ' . $cobranza->documento_numero;
                $temp->pago_nombre = $pago->pago_nombre;
                $temp->pago_id = $pago->pago_id;
                $temp->monto = $pago->monto;
                $temp->banco_nombre = $pago->banco_nombre;
                $temp->num_oper = $pago->num_oper;

                if ($pago->pago_id == '3')
                    $result->pendientes[] = $temp;
                else
                    $result->espera[] = $temp;
            }
        }

        return $result;
    }


    function get_cobranza_by_venta($pedido_id)
    {
        return $this->db->select("
            venta.venta_id as venta_id,
            documento_venta.nombre_tipo_documento as documento_nombre, 
            documento_venta.documento_Serie as documento_serie, 
            documento_venta.documento_Numero as documento_numero,
            usuario.nombre as vendedor_nombre,
            venta.total as total_deuda, 
            credito.dec_credito_montodebito as actual, 
            (venta.total - credito.dec_credito_montodebito)  as saldo, 
        ")
            ->from('venta')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
            ->join('usuario', 'venta.id_vendedor = usuario.nUsuCodigo')
            ->where('venta.venta_id', $pedido_id)->get()->row();
    }

    function get_cobranza_by_cliente($cliente_id)
    {
        return $this->db->select("
            cliente.id_cliente as cliente_id,
            cliente.razon_social as cliente_nombre,
            SUM(venta.total) as total_deuda,
            SUM(credito.dec_credito_montodebito) as subtotal_pago,
            (SUM(venta.total) - SUM(credito.dec_credito_montodebito)) as saldo
        ")
            ->from('cliente')
            ->join('venta', 'venta.id_cliente = cliente.id_cliente')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
            ->where('venta.venta_status !=', 'RECHAZADO')
            ->where('venta.venta_status !=', 'ANULADO')
            ->where('cliente.id_cliente', $cliente_id)
            ->group_by('cliente.id_cliente')->get()->row();
    }

    function pagar_nota_pedido($venta_id, $data)
    {
        $credito = $this->db->get_where('credito', array('id_venta' => $venta_id))->row();
        $historial_pago = array(
            'credito_id' => $venta_id,
            'historial_fecha' => date('Y-m-d H:i:s'),
            'historial_monto' => $data['importe'],
            'historial_tipopago' => $data['pago_id'],
            'monto_restante' => $credito->dec_credito_montodebito + $data['importe'],
            'historial_usuario' => $this->session->userdata('nUsuCodigo'),
            'historial_estatus' => 'PENDIENTE',
            'pago_data' => $data['num_oper']
        );
        if ($historial_pago['historial_tipopago'] == 4)
            $historial_pago['historial_banco_id'] = $data['banco_id'];

        $this->db->insert('historial_pagos_clientes', $historial_pago);
        $result = $this->db->insert_id();

        $this->db->where('id_venta', $venta_id);
        $this->db->update('credito', array('dec_credito_montodebito' => $historial_pago['monto_restante']));
    }

    function pagar_cliente($cliente_id, $data)
    {
        $ventas = $this->db->select("
            venta.venta_id as venta_id,
            venta.total as total_deuda,
            (venta.total - credito.dec_credito_montodebito) as saldo
        ")
            ->from('cliente')
            ->join('venta', 'venta.id_cliente = cliente.id_cliente')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
            ->where('venta.venta_status !=', 'RECHAZADO')
            ->where('venta.venta_status !=', 'ANULADO')
            ->where('cliente.id_cliente', $cliente_id)
            ->group_by('venta.venta_id')->get()->result();

        $total_pagado = $data['importe'];

        foreach ($ventas as $venta) {
            if ($total_pagado == 0)
                break;
            elseif ($total_pagado > $venta->saldo) {
                $data['importe'] = $venta->saldo;
                $total_pagado -= $venta->saldo;
                $this->pagar_nota_pedido($venta->venta_id, $data);
            } elseif ($total_pagado < $venta->saldo) {
                $data['importe'] = $total_pagado;
                $total_pagado = 0;
                $this->pagar_nota_pedido($venta->venta_id, $data);
            }
        }

        if ($total_pagado > 0) {
            //AQUI HAGO LA ACTUALIZACION DE QU ELE KEDO SALDO A FAVOR AL CLIENTE.
        }
    }


    function get_retencion($venta_id)
    {
        $retencion = new stdClass();

        $venta = $this->db->select("
            venta.venta_id as venta_id,
            venta.total as total_deuda,
            cliente.agente_retencion as retencion,
            cliente.linea_credito_valor as retencion_valor,
        ")
            ->from('venta')
            ->join('cliente', 'venta.id_cliente = cliente.id_cliente')
            ->where('venta_id', $venta_id)->get()->row();

        $cobro = $this->db->get_where('historial_pagos_clientes', array(
            'credito_id' => $venta_id,
            'historial_tipopago' => 7,
        ))->row();

        $retencion->pedido_id = $venta->venta_id;
        $retencion->retencion = $venta->retencion == 1 ? TRUE : FALSE;
        $retencion->retencion_valor = $venta->retencion_valor != NULL ? $venta->retencion_valor : 0;
        $retencion->cobro_estado = $cobro != NULL ? $cobro->historial_estatus : 'NO_COBRADO';

        return $retencion;
    }
}

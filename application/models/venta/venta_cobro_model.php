<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_cobro_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('cajas/cajas_model');
        $this->load->model('cajas/cajas_mov_model');
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

            $cliente->vendedor_pendiente = $this->get_vendedor_pendiente($cliente->vendedor_id);
        }

        return $clientes;
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

    function get_vendedor_pendiente($vendedor_id)
    {
        $pagos = $this->get_pagos_by_vendedor($vendedor_id);
        $result = 0;
        foreach ($pagos->pendientes as $pago)
            $result += $pago->monto;
        return $result;
    }

    function get_pagos($pedido_id, $estado = "")
    {

        $cobranza = new stdClass();

        $this->db->select("
                historial_pagos_clientes.credito_id as venta_id,
                historial_pagos_clientes.historial_id as historial_id,
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
        if ($estado == 'PENDIENTE') {
            $this->db->where('historial_pagos_clientes.vendedor_id', NULL);
            $this->db->where('historial_pagos_clientes.historial_estatus', $estado);
        } elseif ($estado == 'CONFIRMADO')
            $this->db->where('historial_pagos_clientes.historial_estatus', $estado);
        elseif ($estado == 'CONSOLIDADO')
            $this->db->where('historial_pagos_clientes.historial_estatus', $estado);

        $cobranza->detalles = $this->db->get()->result();

        return $cobranza;
    }

    function get_pagos_by_vendedor($vendedor_id = FALSE)
    {
        $this->db->select("
            venta.venta_id as venta_id,
            documento_venta.nombre_tipo_documento as documento_nombre,
            documento_venta.documento_Serie as documento_serie,
            documento_venta.documento_Numero as documento_numero,
             usuario.nombre as vendedor_nombre
        ")
            ->from('venta')
            ->join('usuario', 'usuario.nUsuCodigo = venta.id_vendedor')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
            ->join('historial_pagos_clientes', 'historial_pagos_clientes.credito_id = venta.venta_id')
            ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
            ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE')
            ->group_by('venta.venta_id');
        if ($vendedor_id != FALSE)
            $this->db->where('venta.id_vendedor', $vendedor_id);

        $cobranzas = $this->db->get()->result();
        $result = new stdClass();
        $result->pendientes = array();
        $result->consolidado = array();
        $result->espera = array();

        foreach ($cobranzas as $cobranza) {
            $cobranza->pagos_pendientes = $this->get_pagos($cobranza->venta_id, 'PENDIENTE');

            foreach ($cobranza->pagos_pendientes->detalles as $pago) {
                $temp = new stdClass();
                $temp->id = $pago->historial_id;
                $temp->documento = 'Venta: ' . $cobranza->documento_serie . ' - ' . $cobranza->documento_numero;
                $temp->vendedor_nombre = $cobranza->vendedor_nombre;
                $temp->pago_nombre = $pago->pago_nombre;
                $temp->pago_id = $pago->pago_id;
                $temp->monto = $pago->monto;
                $temp->banco_nombre = $pago->banco_nombre;
                $temp->banco_id = $pago->banco_id;
                $temp->num_oper = $pago->num_oper;

                if ($pago->pago_id == '3')
                    $result->pendientes[] = $temp;
                else
                    $result->espera[] = $temp;
            }
        }


        $this->db->select("
            venta.venta_id as venta_id,
            documento_venta.nombre_tipo_documento as documento_nombre,
            documento_venta.documento_Serie as documento_serie,
            documento_venta.documento_Numero as documento_numero,
            usuario.nombre as vendedor_nombre,
            consolidado_detalle.consolidado_id as consolidado_id
        ")
            ->from('venta')
            ->join('consolidado_detalle', 'consolidado_detalle.pedido_id = venta.venta_id')
            ->join('consolidado_carga', 'consolidado_carga.consolidado_id = consolidado_detalle.consolidado_id')
            ->join('usuario', 'usuario.nUsuCodigo = venta.id_vendedor')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
            ->join('credito', 'credito.id_venta = venta.venta_id')
            ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
            ->join('historial_pagos_clientes', 'historial_pagos_clientes.credito_id = venta.venta_id')
            ->where('historial_pedido_proceso.proceso_id', PROCESO_IMPRIMIR)
            ->where('historial_pagos_clientes.historial_estatus', 'CONSOLIDADO')
            ->where('consolidado_carga.status', 'CERRADO')
            ->group_by('venta.venta_id');
        if ($vendedor_id != FALSE)
            $this->db->where('venta.id_vendedor', $vendedor_id);

        $cobranzas = $this->db->get()->result();

        foreach ($cobranzas as $cobranza) {
            $cobranza->consolidado = $this->get_pagos($cobranza->venta_id, 'CONSOLIDADO');

            foreach ($cobranza->consolidado->detalles as $pago) {
                $temp = new stdClass();
                $temp->id = $pago->historial_id;
                $temp->documento = 'Venta: ' . $cobranza->documento_serie . ' - ' . $cobranza->documento_numero;
                $temp->vendedor_nombre = $cobranza->vendedor_nombre;
                $temp->consolidado = $cobranza->consolidado_id;
                $temp->pago_nombre = $pago->pago_nombre;
                $temp->pago_id = $pago->pago_id;
                $temp->monto = $pago->monto;
                $temp->banco_nombre = $pago->banco_nombre;
                $temp->banco_id = $pago->banco_id;
                $temp->num_oper = $pago->num_oper;

                $result->consolidado[] = $temp;
            }
        }


        $this->db->select("
                historial_pagos_clientes.credito_id as venta_id,
                historial_pagos_clientes.historial_id as historial_id,
                historial_pagos_clientes.historial_fecha as fecha,
                historial_pagos_clientes.historial_monto as monto,
                historial_pagos_clientes.historial_tipopago as pago_id,
                metodos_pago.nombre_metodo as pago_nombre,
                banco.banco_nombre as banco_nombre,
                banco.banco_id as banco_id,
                historial_pagos_clientes.pago_data as num_oper,
                historial_pagos_clientes.historial_estatus as estado,
                usuario.nombre as vendedor_nombre
            ")
            ->from('historial_pagos_clientes')
            ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
            ->join('banco', 'banco.banco_id = historial_pagos_clientes.historial_banco_id', 'left')
            ->join('usuario', 'usuario.nUsuCodigo = historial_pagos_clientes.vendedor_id')
            ->where('historial_pagos_clientes.historial_estatus', 'ESPERA');
        if ($vendedor_id != FALSE)
            $this->db->where('vendedor_id', $vendedor_id);

        $pagos = $this->db->get()->result();

        foreach ($pagos as $pago) {
            $temp = new stdClass();
            $temp->id = $pago->historial_id;
            $temp->documento = 'Conjunto de Efectivos';
            $temp->vendedor_nombre = $pago->vendedor_nombre;
            $temp->pago_nombre = $pago->pago_nombre;
            $temp->pago_id = $pago->pago_id;
            $temp->monto = $pago->monto;
            $temp->banco_nombre = $pago->banco_nombre;
            $temp->banco_id = $pago->banco_id;
            $temp->num_oper = $pago->num_oper;

            $result->espera[] = $temp;
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
            usuario.nUsuCodigo as vendedor_id,
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
            'historial_usuario' => isset($data['vendedor']) ? $data['vendedor'] : $this->session->userdata('nUsuCodigo'),
            'historial_estatus' => 'PENDIENTE',
            'fecha_documento' => $data['fecha_documento'],
            'pago_data' => $data['num_oper']
        );

        if (isset($data['historial_estatus']))
            $historial_pago['historial_estatus'] = $data['historial_estatus'];

        if ($historial_pago['historial_tipopago'] == 4)
            $historial_pago['historial_banco_id'] = $data['banco_id'];

        $this->db->insert('historial_pagos_clientes', $historial_pago);
        $result = $this->db->insert_id();

        $this->db->where('id_venta', $venta_id);
        $this->db->update('credito', array('dec_credito_montodebito' => $historial_pago['monto_restante']));

        return $result;
    }

    function pagar_by_vendedor($id, $data)
    {
        $historial_pago = array(
            'historial_fecha' => date('Y-m-d H:i:s'),
            'historial_monto' => $data['importe'],
            'historial_tipopago' => $data['pago_id'],
            'monto_restante' => 0,
            'historial_usuario' => isset($data['vendedor']) ? $data['vendedor'] : $this->session->userdata('nUsuCodigo'),
            'historial_estatus' => 'ESPERA',
            'pago_data' => $data['num_oper'],
            'fecha_documento' => date('Y-m-d H:i:s', strtotime($data['fecha_documento'])),
            'vendedor_id' => $id
        );
        if ($historial_pago['historial_tipopago'] == 4)
            $historial_pago['historial_banco_id'] = $data['banco_id'];

        $this->db->insert('historial_pagos_clientes', $historial_pago);
        $result = $this->db->insert_id();

        foreach ($data['historial_id'] as $historial) {
            $this->db->where('historial_id', $historial->id);
            $this->db->update('historial_pagos_clientes', array('vendedor_id' => $result));
        }
    }

    function confirmar_pago($id, $cuenta_id)
    {
        $pago = $this->db->get_where('historial_pagos_clientes', array('historial_id' => $id))->row();

        $data_mov = array(
            'usuario_id' => $this->session->userdata('nUsuCodigo'),
            'fecha_mov' => $pago->historial_fecha,
            'movimiento' => 'INGRESO',
            'operacion' => 'COBRANZA',
            'medio_pago' => $pago->historial_tipopago,
            'saldo' => $pago->historial_monto,
            'ref_id' => $id,
            'ref_val' => $pago->pago_data,
        );
        $cuenta = NULL;

        if ($pago->historial_tipopago == 3 || $pago->historial_tipopago == 5) {
            $cuenta = $this->cajas_model->get_cuenta($cuenta_id);

        } elseif ($pago->historial_tipopago == 4) {
            $banco = $this->db->get_where('banco', array('banco_id' => $pago->historial_banco_id))->row();
            $cuenta = $this->cajas_model->get_cuenta($banco->cuenta_id);

        } elseif ($pago->historial_tipopago == 7) {
            $cuenta = $this->db->select('caja_desglose.*')->from('caja_desglose')
                ->join('caja', 'caja.id = caja_desglose.caja_id')
                ->where('retencion', 1)
                ->where('moneda_id', 1)->get()->row();
        }

        if ($cuenta != NULL) {
            $data_mov['caja_desglose_id'] = $cuenta->id;
            $data_mov['saldo_old'] = $cuenta->saldo;
            $this->cajas_mov_model->save_mov($data_mov);

            $this->cajas_model->update_saldo($cuenta->id, $pago->historial_monto);

            if ($pago->historial_estatus == 'PENDIENTE' || $pago->historial_estatus == 'CONSOLIDADO') {
                $this->db->where('historial_id', $id);
                $this->db->update('historial_pagos_clientes', array('historial_estatus' => 'CONFIRMADO'));

            } elseif ($pago->historial_estatus == 'ESPERA') {
                $confirmar = $this->db->get_where('historial_pagos_clientes', array(
                    'historial_estatus !=' => 'ESPERA',
                    'historial_estatus !=' => 'CERRADO',
                    'historial_estatus' => 'PENDIENTE',
                    'vendedor_id' => $id
                ))->result();

                foreach ($confirmar as $pago) {
                    $this->db->where('historial_id', $pago->historial_id);
                    $this->db->update('historial_pagos_clientes', array('historial_estatus' => 'CONFIRMADO'));
                }
                $this->db->where('historial_id', $id);
                $this->db->update('historial_pagos_clientes', array('historial_estatus' => 'CERRADO'));
            }
        }

        $creditos = $this->db->select('credito.id_venta as credito_id')->from('credito')
            ->where('credito.dec_credito_montodebito >= credito.dec_credito_montodeuda')
            ->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA))->get()->result();
        foreach ($creditos as $credito) {
            $this->db->where('historial_pagos_clientes.credito_id', $credito->credito_id);
            $this->db->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE');
            $this->db->from('historial_pagos_clientes');
            if ($this->db->count_all_results() == 0) {
                $this->db->where('id_venta', $credito->credito_id);
                $this->db->update('credito', array('var_credito_estado' => CREDITO_CANCELADO));
            }
        }

    }

    function eliminar_pago($id)
    {
        $pago = $this->db->get_where('historial_pagos_clientes', array('historial_id' => $id))->row();

        if ($pago->historial_estatus == 'PENDIENTE') {
            $credito = $this->db->get_where('credito', array('id_venta' => $pago->credito_id))->row();
            $this->db->where('id_venta', $pago->credito_id);
            $this->db->update('credito', array('dec_credito_montodebito' => $credito->dec_credito_montodebito - $pago->historial_monto));

            $this->db->where('historial_id', $id);
            $this->db->delete('historial_pagos_clientes');
        }
        if ($pago->historial_estatus == 'ESPERA') {
            $this->db->where('vendedor_id', $id);
            $this->db->update('historial_pagos_clientes', array('vendedor_id' => NULL));

            $this->db->where('historial_id', $id);
            $this->db->delete('historial_pagos_clientes');
        }
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
    }


    function get_retencion($venta_id)
    {
        $retencion = new stdClass();

        $venta = $this->db->select("
            venta.venta_id as venta_id,
            venta.total as total_deuda,
            venta.retencion as retencion,
        ")
            ->from('venta')
            ->where('venta_id', $venta_id)->get()->row();

        $cobro = $this->db->get_where('historial_pagos_clientes', array(
            'credito_id' => $venta_id,
            'historial_tipopago' => 7,
        ))->row();

        $retencion->pedido_id = $venta->venta_id;
        $retencion->retencion = $venta->retencion != NULL ? $venta->retencion : 0;
        $retencion->cobro_estado = $cobro != NULL ? $cobro->historial_estatus : 'NO_COBRADO';

        return $retencion;
    }
}

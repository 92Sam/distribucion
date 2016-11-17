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
            ->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA));

        $cobranzas = $this->db->get()->result();

        foreach ($cobranzas as $cobranza) {
            $generado = $this->db->select('pagado')->from('venta')
                ->where('venta.venta_id', $cobranza->venta_id)->get()->row();

            $historial_pedido = $this->db->get_where('historial_pedido_proceso', array(
                'pedido_id' => $cobranza->venta_id,
                'proceso_id' => PROCESO_GENERAR
            ))->row();

            $cobranza->generado = new stdClass();
            $cobranza->generado->fecha = $historial_pedido->created_at;
            $cobranza->generado->monto = $generado->pagado != null ? $generado->pagado : 0;
            $cobranza->generado->tipo_pago_nombre = 'Generaci&oacute;n del pedido';


            $liquidacion = $this->db->select('liquidacion_monto_cobrado')->from('consolidado_detalle')
                ->where('consolidado_detalle.pedido_id', $cobranza->venta_id)->get()->row();
            $cobranza->liquidacion = new stdClass();
            $cobranza->liquidacion->fecha = $cobranza->fecha_venta;
            $cobranza->liquidacion->monto = $liquidacion->liquidacion_monto_cobrado != null ? $liquidacion->liquidacion_monto_cobrado : 0;
            $cobranza->liquidacion->tipo_pago_nombre = 'Liquidacion del pedido';

            $cobranza->detalles = $this->db->select("
                historial_pagos_clientes.historial_fecha as fecha,
                historial_pagos_clientes.historial_monto as monto,
                metodos_pago.nombre_metodo as tipo_pago_nombre
            ")
                ->from('historial_pagos_clientes')
                ->join('metodos_pago', 'metodos_pago.id_metodo = historial_pagos_clientes.historial_tipopago')
                ->where('credito_id', $cobranza->venta_id)
                ->get()->result();
        }

        return $cobranzas;

    }

}

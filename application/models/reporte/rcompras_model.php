<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class rcompras_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_compras($params = array())
    {
        $compras = new stdClass();

        $compras->total_completado = $this->count_compras('COMPLETADO', $params);
        $compras->total_pendiente = $this->count_compras('PENDIENTE', $params);
        $compras->total_anulado = $this->count_compras('ANULADO', $params);


        $compras->importe_completado = $this->get_importe('COMPLETADO', $params);
        $compras->importe_pagado = $this->get_importe('CONTADO', $params) + $this->get_importe('CREDTIO', $params);
        $compras->importe_pendiente = $compras->importe_completado - $compras->importe_pagado;

        $compras->desgloses = $this->get_desglose($params);

        return $compras;

    }

    function get_desglose($params)
    {
        $this->aplicar_from();

        if ($params['desglose'] == '1') {
            $this->db->select('proveedor.proveedor_nombre as desglose, proveedor.id_proveedor as desglose_id');
            $this->db->group_by('proveedor.id_proveedor');
        }
        if ($params['desglose'] == '2') {
            $this->db->select('DISTINCT(ingreso.tipo_documento) as desglose, ingreso.tipo_documento as desglose_id');
            $this->db->group_by('ingreso.tipo_documento');
        }
        if ($params['desglose'] == '3') {
            $this->db->select('DISTINCT(ingreso.pago) as desglose, ingreso.pago as desglose_id');
            $this->db->group_by('ingreso.pago');
        }

        $this->aplicar_filtros($params);

        $result = $this->db->get()->result();

        if ($params['desglose'] != '0')
            foreach ($result as $desglose) {

                $desglose->total_completado = $this->count_compras('COMPLETADO', $params, 1, $desglose->desglose_id);
                $desglose->total_pendiente = $this->count_compras('PENDIENTE', $params, 1, $desglose->desglose_id);
                $desglose->total_anulado = $this->count_compras('ANULADO', $params, 1, $desglose->desglose_id);


                $desglose->importe_completado = $this->get_importe('COMPLETADO', $params, 1, $desglose->desglose_id);
                $desglose->importe_pagado = $this->get_importe('CONTADO', $params, 1, $desglose->desglose_id) + $this->get_importe('CREDTIO', $params, 1, $desglose->desglose_id);
                $desglose->importe_pendiente = $desglose->importe_completado - $desglose->importe_pagado;
            }

        return $result;
    }

    function count_compras($estado, $params, $desglose = 0, $desglose_id = 0)
    {

        $this->db->select('COUNT(ingreso.id_ingreso) as total');

        $this->aplicar_from();

        if ($estado == 'COMPLETADO') {
            $this->db->where('ingreso.ingreso_status', 'COMPLETADO');
        }

        if ($estado == 'PENDIENTE') {
            $this->db->where('ingreso.ingreso_status', 'PENDIENTE');
        }

        if ($estado == 'ANULADO') {
            $this->db->where('ingreso.ingreso_status', 'DEVUELTO');
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
            $this->db->select('SUM(ingreso.total_ingreso) as total');
        }

        if ($estado == 'CONTADO') {
            $this->db->select('SUM(ingreso.total_ingreso) as total');
            $this->db->where('ingreso.pago', 'CONTADO');
        }

        if ($estado == 'CREDTIO') {
            $this->db->join('pagos_ingreso', 'ingreso.id_ingreso = pagos_ingreso.pagoingreso_ingreso_id', 'left');
            $this->db->select('SUM(pagos_ingreso.pagoingreso_monto) as total');
            $this->db->where('ingreso.pago', 'CREDITO');
        }

        $this->db->where('ingreso.ingreso_status', 'COMPLETADO');

        $this->aplicar_filtros($params);

        if ($desglose == 1)
            $this->aplicar_desglose($params, $desglose_id);

        $result = $this->db->get()->row();

        return $result->total;
    }

    function aplicar_desglose($params, $desglose_id)
    {

        if ($params['desglose'] == '1') {
            $this->db->where('proveedor.id_proveedor', $desglose_id);
        }
        if ($params['desglose'] == '2') {
            $this->db->where('ingreso.tipo_documento', $desglose_id);
        }
        if ($params['desglose'] == '3') {
            $this->db->where('ingreso.pago', $desglose_id);
        }
    }

    function aplicar_filtros($params)
    {
        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('ingreso.fecha_emision >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('ingreso.fecha_emision <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['proveedor_id']) && $params['proveedor_id'] != 0)
            $this->db->where('ingreso.int_Proveedor_id', $params['proveedor_id']);

        if (isset($params['tipo_documento']) && $params['tipo_documento'] != '0')
            $this->db->where('ingreso.tipo_documento', $params['tipo_documento']);

    }

    function aplicar_from()
    {
        $this->db->from('ingreso');
        $this->db->join('proveedor', 'ingreso.int_Proveedor_id = proveedor.id_proveedor');
    }

}

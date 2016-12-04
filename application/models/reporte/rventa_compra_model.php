<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class rventa_compra_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('reporte/rventas_model');
        $this->load->model('reporte/rcompras_model');
    }

    function get_ventas_compras($params = array())
    {
        $ventas_compras = array();

        for ($i = 0; $i < 12; $i++) {
            $temp = new stdClass();
            $ventas = $this->rventas_model->get_ventas(array(
                'fecha_ini' => $params['year'] . '-' . sumCod($i + 1, 2) . '-01',
                'fecha_fin' => $params['year'] . '-' . sumCod($i + 1, 2) . '-' . last_day($params['year'], sumCod($i + 1, 2)),
                'fecha_flag' => 1,
                'desglose' => 0
            ));

            $compras = $this->rcompras_model->get_compras(array(
                'fecha_ini' => $params['year'] . '-' . sumCod($i + 1, 2) . '-01',
                'fecha_fin' => $params['year'] . '-' . sumCod($i + 1, 2) . '-' . last_day($params['year'], sumCod($i + 1, 2)),
                'fecha_flag' => 1,
                'desglose' => 0
            ));

            $temp->mes = $i + 1;

            $temp->cantidad_venta = $ventas->total_completado;
            $temp->importe_venta = $ventas->importe_completado;
            $temp->pagado_venta = $ventas->importe_cobranza;
            $temp->saldo_venta = $ventas->importe_completado - $ventas->importe_cobranza;

            $temp->cantidad_compra = $compras->total_completado - $compras->total_pendiente;
            $temp->importe_compra = $compras->importe_completado;
            $temp->pagado_compra = $compras->importe_pagado;
            $temp->saldo_compra = $compras->importe_pendiente;

            $ventas_compras[] = $temp;
        }


        return $ventas_compras;

    }

}

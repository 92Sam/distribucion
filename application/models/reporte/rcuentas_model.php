<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class rcuentas_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_cuentas($params = array())
    {
        $this->db->select("
            ingreso.id_ingreso as ingreso_id,
            ingreso.tipo_documento as documento_nombre, 
            ingreso.documento_serie as documento_serie, 
            ingreso.documento_numero as documento_numero, 
            proveedor.proveedor_nombre as proveedor_nombre, 
            ingreso.fecha_emision as fecha_emision, 
            ingreso.total_ingreso as total, 
            DATEDIFF(CURDATE(), (ingreso.fecha_emision)) as atraso
        ")
            ->from('ingreso')
            ->join('proveedor', 'ingreso.int_Proveedor_id = proveedor.id_proveedor')
            ->where('ingreso.ingreso_status', 'COMPLETADO')
            ->where('ingreso.pago', 'CREDITO')
            ->where("(SELECT 
                        COUNT(*)
                    FROM
                        pagos_ingreso
                    WHERE
                        pagoingreso_ingreso_id = ingreso.id_ingreso 
                        AND pagoingreso_restante = 0) = 0");

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $this->db->where('ingreso.fecha_emision >=', date('Y-m-d H:i:s', strtotime($params['fecha_ini'] . ' 00:00:00')));
            $this->db->where('ingreso.fecha_emision <=', date('Y-m-d H:i:s', strtotime($params['fecha_fin'] . ' 23:59:59')));
        }

        if (isset($params['proveedor_id']) && $params['proveedor_id'] != 0)
            $this->db->where('ingreso.int_Proveedor_id', $params['proveedor_id']);

        if (isset($params['tipo_documento']) && $params['tipo_documento'] != '0')
            $this->db->where('ingreso.tipo_documento', $params['tipo_documento']);

        if (isset($params['dif_deuda']) && isset($params['dif_deuda_value']) && $params['dif_deuda_value'] > 0) {
            if ($params['dif_deuda'] == 1)
                $this->db->where('ingreso.total_ingreso >=', $params['dif_deuda_value']);
            elseif ($params['dif_deuda'] == 2)
                $this->db->where('ingreso.total_ingreso <=', $params['dif_deuda_value']);
        }

        if (isset($params['atraso']) && $params['atraso'] != 0) {
            switch ($params['atraso']) {
                case 1: {
                    $this->db->where('DATEDIFF(CURDATE(), (ingreso.fecha_emision)) <= 7');
                    break;
                }
                case 2: {
                    $this->db->where('DATEDIFF(CURDATE(), (ingreso.fecha_emision)) > 7');
                    $this->db->where('DATEDIFF(CURDATE(), (ingreso.fecha_emision)) <= 15');
                    break;
                }
                case 3: {
                    $this->db->where('DATEDIFF(CURDATE(), (ingreso.fecha_emision)) > 15');
                    $this->db->where('DATEDIFF(CURDATE(), (ingreso.fecha_emision)) <= 30');
                    break;
                }
                case 4: {
                    $this->db->where('DATEDIFF(CURDATE(), (ingreso.fecha_emision)) > 30');
                    break;
                }
            }
        }


        $cuentas = $this->db->get()->result();

        foreach ($cuentas as $cuenta) {
            $pagado = $this->db->select('SUM(pagos_ingreso.pagoingreso_monto) as monto_pagado')
                ->from('pagos_ingreso')
                ->where('pagos_ingreso.pagoingreso_ingreso_id', $cuenta->ingreso_id)
                ->get()->row();
            $cuenta->pagado = $pagado->monto_pagado != null ? $pagado->monto_pagado : 0;
            $cuenta->cuenta_pagar = $cuenta->total - $cuenta->pagado;

            $cuenta->detalles = $this->db->select("
                pagos_ingreso.pagoingreso_fecha as fecha,
                pagos_ingreso.pagoingreso_monto as monto,,
                pagos_ingreso.medio_pago_id as pago_id,
                metodos_pago.nombre_metodo as pago_nombre,
                banco.banco_nombre as banco_nombre,
                pagos_ingreso.operacion as operacion
            ")
                ->from('pagos_ingreso')
                ->where('pagoingreso_ingreso_id', $cuenta->ingreso_id)
                ->join('metodos_pago', 'metodos_pago.id_metodo = pagos_ingreso.medio_pago_id', 'left')
                ->join('banco', 'banco.banco_id = pagos_ingreso.banco_id', 'left')
                ->get()->result();
        }

        return $cuentas;

    }

}

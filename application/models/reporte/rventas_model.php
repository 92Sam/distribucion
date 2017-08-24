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

        $this->aplicar_filtros($params);

        $result = $this->db->get()->result();

        if ($params['desglose'] != '0')
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
                ->where('credito.var_credito_estado', CREDITO_CANCELADO);
        }

        if ($estado == 'RECHAZADO') {
            $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR);
            $this->db->where_in('venta.venta_status', array('RECHAZADO', 'ANULADO'));
        }

        if ($estado == 'PROCESO') {
            $this->db->where('historial_pedido_proceso.proceso_id !=', PROCESO_LIQUIDAR);
            $this->db->where('historial_pedido_proceso.actual', 1);
        }

        if ($estado == 'COBRANZAS') {
            $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
                ->where('venta.venta_status !=', 'ANULADO')
                ->where('venta.venta_status !=', 'RECHAZADO')
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

        }

        if ($estado == 'COBRANZAS') {
            $this->db->select('SUM(credito.dec_credito_montodebito) as total')
                ->where('venta.venta_status !=', 'RECHAZADO')
                ->where_in('credito.var_credito_estado', array(CREDITO_DEBE, CREDITO_ACUENTA, CREDITO_CANCELADO));
        }

        $this->db->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR);
        $this->db->where('venta.venta_status !=', 'RECHAZADO');
        $this->db->where('venta.venta_status !=', 'ANULADO');

        $this->aplicar_filtros($params);


        if ($desglose == 1)
            $this->aplicar_desglose($params, $desglose_id);


        $result = $this->db->get()->row();

        if ($estado == 'COMPLETADO')
            return $result->total;
        elseif ($estado == 'COBRANZAS') {
            $this->db->select("
                SUM(historial_pagos_clientes.historial_monto) as monto,
            ")
                ->from('historial_pagos_clientes')
                ->join('venta', 'venta.venta_id = historial_pagos_clientes.credito_id')
                ->join('cliente', 'venta.id_cliente = cliente.id_cliente')
                ->join('usuario', 'venta.id_vendedor = usuario.nUsuCodigo')
                ->join('zonas', 'cliente.id_zona = zonas.zona_id')
                ->join('credito', 'credito.id_venta = historial_pagos_clientes.credito_id')
                ->join('historial_pedido_proceso', 'historial_pedido_proceso.pedido_id = venta.venta_id')
                ->where('historial_pedido_proceso.proceso_id', PROCESO_LIQUIDAR)
                ->where('venta.venta_status !=', 'RECHAZADO')
                ->where('venta.venta_status !=', 'ANULADO')
                ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE');

            $this->aplicar_filtros($params);
            $pagado_pendientes = $this->db->get()->row();
            $pagado_pendientes = isset($pagado_pendientes->monto) ? $pagado_pendientes->monto : 0;

            return $result->total == 0 ? 0 : $result->total - $pagado_pendientes;
        }
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

    function get_for_devolver($data)
    {
        $query = "
            SELECT
                hp.created_at AS fecha,
                CONCAT('NE ',
                        dv.documento_Serie,
                        '-',
                        dv.documento_Numero) AS documento,
                v.venta_id AS venta_id,
                c.ruc_cliente AS ruc_dni,
                c.razon_social AS razon_social,
                u.nombre AS vendedor,
                v.venta_status AS estado_ne,
                z.zona_nombre AS zona,
                cp.nombre_condiciones AS condicion,
                v.total AS total,
                IF((SELECT
                            var_credito_estado
                        FROM
                            credito
                        WHERE
                            credito.id_venta = v.venta_id
                        LIMIT 1) = 'CANCELADA',
                    'CANCELADO',
                    'PENDIENTE') AS estado
            FROM
                venta AS v
                    JOIN
                historial_pedido_proceso AS hp ON hp.pedido_id = v.venta_id
                    JOIN 
                credito ON credito.id_venta = v.venta_id 
                    JOIN
                documento_venta AS dv ON dv.id_tipo_documento = v.numero_documento
                    JOIN
                cliente AS c ON c.id_cliente = v.id_cliente
                    JOIN
                usuario AS u ON u.nUsuCodigo = v.id_vendedor
                    JOIN
                zonas AS z ON z.zona_id = c.id_zona
                    JOIN
                condiciones_pago AS cp ON cp.id_condiciones = v.condicion_pago
            WHERE
                hp.proceso_id = 4 AND v.venta_status = 'ENTREGADO' AND hp.proceso_id = 4 
            AND (SELECT COUNT(*) FROM historial_pagos_clientes WHERE credito_id = v.venta_id) = 0


        ";


        if (isset($data['cliente_id']) && $data['cliente_id'] != 0)
            $query .= " AND v.id_cliente = " . $data['cliente_id'];


        if (isset($data['pedido']) && $data['pedido'] != "-")
            $query .= " AND v.venta_id LIKE '%" . $data['pedido'] . "%'";

        return $this->db->query($query)->result();
    }


    function get_nota_entrega($data)
    {
        $query = "
            SELECT
                hp.created_at AS fecha,
                CONCAT('NE ',
                        dv.documento_Serie,
                        '-',
                        dv.documento_Numero) AS documento,
                v.venta_id AS venta_id,
                c.ruc_cliente AS ruc_dni,
                c.razon_social AS razon_social,
                u.nombre AS vendedor,
                v.venta_status AS estado_ne,
                z.zona_nombre AS zona,
                cp.nombre_condiciones AS condicion,
                v.total AS total,
                IF((SELECT
                            var_credito_estado
                        FROM
                            credito
                        WHERE
                            credito.id_venta = v.venta_id
                        LIMIT 1) = 'CANCELADA',
                    'CANCELADO',
                    'PENDIENTE') AS estado
            FROM
                venta AS v
                    JOIN
                historial_pedido_proceso AS hp ON hp.pedido_id = v.venta_id
                    JOIN
                documento_venta AS dv ON dv.id_tipo_documento = v.numero_documento
                    JOIN
                cliente AS c ON c.id_cliente = v.id_cliente
                    JOIN
                usuario AS u ON u.nUsuCodigo = v.id_vendedor
                    JOIN
                zonas AS z ON z.zona_id = c.id_zona
                    JOIN
                condiciones_pago AS cp ON cp.id_condiciones = v.condicion_pago
            WHERE
                hp.proceso_id = 4 


        ";

        if (isset($data['estado_ne']) && $data['estado_ne'] != '0') {
            $query .= ' AND v.venta_status = "' . $data['estado_ne'] . '" ';
        }

        if (isset($data['cliente_id']) && $data['cliente_id'] != 0)
            $query .= " AND v.id_cliente = " . $data['cliente_id'];

        if (isset($data['estado']) && $data['estado'] != 0) {
            if ($data['estado'] == 1) {
                $query .= " AND (SELECT
                            var_credito_estado
                        FROM
                            credito
                        WHERE
                            credito.id_venta = v.venta_id
                        LIMIT 1) = 'CANCELADA'";
            }

            if ($data['estado'] == 2) {
                $query .= " AND (SELECT
                            var_credito_estado
                        FROM
                            credito
                        WHERE
                            credito.id_venta = v.venta_id
                        LIMIT 1) != 'CANCELADA'";
            }

        }

        if (isset($data['mes']) && isset($data['year']) && isset($data['dia_min']) && isset($data['dia_max'])) {
            $last_day = last_day($data['year'], sumCod($data['mes'], 2));
            if ($last_day > $data['dia_max'])
                $last_day = $data['dia_max'];

            $query .= " AND hp.created_at >= '" . $data['year'] . '-' . sumCod($data['mes'], 2) . '-' . $data['dia_min'] . " 00:00:00'";
            $query .= " AND hp.created_at <= '" . $data['year'] . '-' . sumCod($data['mes'], 2) . '-' . $last_day . " 23:59:59'";
        }


        return $this->db->query($query)->result();
    }

    function get_nota_entrega_detalle($venta_id)
    {
        $query = "
            SELECT
                df.documento_tipo AS documento,
                CONCAT(df.documento_serie,
                        '-',
                        df.documento_numero) AS documento_numero,
                SUM(dd.detalle_importe) AS importe, 
                (SELECT CONCAT(kardex.serie, ' - ', kardex.numero) 
                    FROM 
                      kardex 
                    WHERE 
                      kardex.tipo_doc = 7 AND tipo_operacion = 5 AND kardex.ref_id = " . $venta_id . " 
                    LIMIT 1) AS nota_credito
            FROM
                documento_fiscal AS df
                    JOIN
                documento_detalle AS dd ON dd.documento_fiscal_id = df.documento_fiscal_id
            WHERE
                df.venta_id = " . $venta_id . "
            GROUP BY df.documento_fiscal_id
        ";

        return $this->db->query($query)->result();
    }

    function get_documentos($data)
    {
        $query = "
            SELECT
                v.venta_id AS venta_id,
                hp.created_at AS fecha,
                df.documento_tipo AS documento,
                CONCAT(df.documento_serie,
                        '-',
                        df.documento_numero) AS documento_numero,
                c.ruc_cliente AS ruc_dni,
                c.razon_social AS razon_social,
                u.nombre AS vendedor,
                z.zona_nombre AS zona,
                cp.nombre_condiciones AS condicion,
                IF(df.documento_tipo = 'FACTURA',
                    SUM(dd.detalle_importe) * 18 / 100,
                    0) AS igv,
                IF(df.documento_tipo = 'FACTURA',
                    SUM(dd.detalle_importe) - (SUM(dd.detalle_importe) * 18 / 100),
                    SUM(dd.detalle_importe)) AS subtotal,
                SUM(dd.detalle_importe) AS total,
                IF((SELECT
                            SUM(detalle_importe)
                        FROM
                            documento_detalle
                        WHERE
                            id_venta = v.venta_id) = (v.total + IFNULL((SELECT 
                            SUM(hpd.stock * dd.precio)
                        FROM
                            historial_pedido_detalle AS hpd
                                JOIN
                            historial_pedido_proceso AS hist_p ON hist_p.id = hpd.historial_pedido_proceso_id
                        WHERE
                            hist_p.pedido_id = v.venta_id AND hist_p.proceso_id = 6), 0)),
                    'S',
                    'D') AS criterio,
                    ROUND((v.total + IFNULL((SELECT 
                            SUM(hpd.stock * hpd.precio_unitario * 1.18)
                        FROM
                            historial_pedido_detalle AS hpd
                                JOIN
                            historial_pedido_proceso AS hist_p ON hist_p.id = hpd.historial_pedido_proceso_id
                        WHERE
                            hist_p.pedido_id = v.venta_id AND hist_p.proceso_id = 6),
                    0)), 2) AS venta_total_descuento,
                v.total AS venta_total,
                IF((SELECT
                            var_credito_estado
                        FROM
                            credito
                        WHERE
                            credito.id_venta = v.venta_id
                        LIMIT 1) = 'CANCELADA',
                    'CANCELADO',
                    'PENDIENTE') AS estado
            FROM
                venta AS v
                    JOIN
                historial_pedido_proceso AS hp ON hp.pedido_id = v.venta_id
                    JOIN
                documento_fiscal AS df ON df.venta_id = v.venta_id
                    JOIN
                documento_detalle AS dd ON dd.documento_fiscal_id = df.documento_fiscal_id
                    JOIN
                documento_venta AS dv ON dv.id_tipo_documento = v.numero_documento
                    JOIN
                cliente AS c ON c.id_cliente = v.id_cliente
                    JOIN
                usuario AS u ON u.nUsuCodigo = v.id_vendedor
                    JOIN
                zonas AS z ON z.zona_id = c.id_zona
                    JOIN
                condiciones_pago AS cp ON cp.id_condiciones = v.condicion_pago
            WHERE
                hp.proceso_id = 4 AND v.venta_status != 'RECHAZADO' AND v.venta_status != 'ANULADO'

        ";

        if (isset($data['cliente_id']) && $data['cliente_id'] != 0)
            $query .= " AND v.id_cliente = " . $data['cliente_id'];

        if (isset($data['estado']) && $data['estado'] != 0) {
            if ($data['estado'] == 1) {
                $query .= " AND (SELECT
                            var_credito_estado
                        FROM
                            credito
                        WHERE
                            credito.id_venta = v.venta_id
                        LIMIT 1) = 'CANCELADA'";
            }

            if ($data['estado'] == 2) {
                $query .= " AND (SELECT
                            var_credito_estado
                        FROM
                            credito
                        WHERE
                            credito.id_venta = v.venta_id
                        LIMIT 1) != 'CANCELADA'";
            }

        }

        if (isset($data['mes']) && isset($data['year']) && isset($data['dia_min']) && isset($data['dia_max'])) {
            $last_day = last_day($data['year'], sumCod($data['mes'], 2));
            if ($last_day > $data['dia_max'])
                $last_day = $data['dia_max'];

            $query .= " AND hp.created_at >= '" . $data['year'] . '-' . sumCod($data['mes'], 2) . '-' . $data['dia_min'] . " 00:00:00'";
            $query .= " AND hp.created_at <= '" . $data['year'] . '-' . sumCod($data['mes'], 2) . '-' . $last_day . " 23:59:59'";
        }

        $query .= "  GROUP BY df.documento_fiscal_id";


        return $this->db->query($query)->result();
    }

    function get_historial_cobranzas($params)
    {
        $query = "SELECT
                    v.venta_id AS venta_id,
                    hpc.historial_id AS historial_id,
                    hpc.historial_fecha AS fecha,
                    hpc.historial_monto AS monto,
                    mp.nombre_metodo AS tipo_pago,
                    CONCAT('NE ',
                            dv.documento_Serie,
                            '-',
                            dv.documento_Numero) AS venta,
                    v.fecha AS fecha_venta,
                    u.nombre AS vendedor,
                    c.razon_social AS cliente,
                    z.zona_nombre AS zona,
                    b.banco_nombre AS banco,
                    hpc.pago_data AS operacion
                FROM
                    historial_pagos_clientes AS hpc
                        JOIN
                    metodos_pago AS mp ON mp.id_metodo = hpc.historial_tipopago
                        JOIN
                    venta AS v ON v.venta_id = hpc.credito_id
                        JOIN
                    documento_venta AS dv ON dv.id_tipo_documento = v.venta_id
                        JOIN
                    usuario AS u ON u.nUsuCodigo = v.id_vendedor
                        JOIN
                    cliente AS c ON c.id_cliente = v.id_cliente
                        JOIN
                    zonas AS z ON z.zona_id = c.id_zona
                        LEFT JOIN
                    banco AS b ON b.banco_id = hpc.historial_banco_id
                WHERE
                    hpc.historial_estatus = 'CONFIRMADO'";

        if (isset($params['fecha_ini']) && isset($params['fecha_fin']) && $params['fecha_flag'] == 1) {
            $query .= " AND hpc.historial_fecha >= '" . $params['fecha_ini'] . " 00:00:00'";
            $query .= " AND hpc.historial_fecha <= '" . $params['fecha_fin'] . " 23:59:59'";
        }

        if (isset($params['vendedor_id']) && $params['vendedor_id'] != 0)
            $query .= " AND u.nUsuCodigo = " . $params['vendedor_id'];

        if (isset($params['cliente_id']) && $params['cliente_id'] != 0)
            $query .= " AND c.id_cliente = " . $params['cliente_id'];

        if (isset($params['zonas_id']) && count($params['zonas_id'])) {
            $zonas = '';
            for ($i = 0; $i < count($params['zonas_id']); $i++) {
                $zonas .= $params['zonas_id'][$i];

                if ($i < count($params['zonas_id']) - 1)
                    $zonas .= ',';
            }
            $query .= " AND c.id_zona IN (" . $zonas . ")";
        }

        $query .= " ORDER BY hpc.credito_id , hpc.historial_fecha ";


        return $this->db->query($query)->result();
    }


    function getVentasProducto($data)
    {
        $query = "
            SELECT 
                v.venta_id,
                hpp.fecha_plan AS fecha,
                doc_v.documento_Serie AS serie, 
                doc_v.documento_Numero AS numero, 
                v.tipo_doc_fiscal AS doc_fiscal,
                hpd.producto_id AS producto_id, 
                hpd.stock AS cantidad,
                hpd.costo_unitario, 
                hpd.precio_unitario, 
                hpd.bonificacion AS bono, 
                v.venta_status AS estado
            FROM
                venta AS v 
            JOIN documento_venta AS doc_v ON doc_v.id_tipo_documento = v.numero_documento 
            JOIN historial_pedido_proceso AS hpp ON hpp.pedido_id = v.venta_id 
            JOIN historial_pedido_detalle AS hpd ON hpd.historial_pedido_proceso_id = hpp.id  
            JOIN producto AS p ON p.producto_id = hpd.producto_id  
            WHERE ";

        if (isset($data['fecha_ini']) && isset($data['fecha_fin']))
            $query .= " hpp.fecha_plan >= '" . $data['fecha_ini'] . " 00:00:00'  AND hpp.fecha_plan <= '" . $data['fecha_fin'] . " 23:59:59'";

        if (isset($data['producto_id']) && $data['producto_id'] != 0)
            $query .= " AND hpd.producto_id = " . $data['producto_id'];

        if (isset($data['grupo_id']) && $data['grupo_id'] != 0)
            $query .= " AND p.produto_grupo = " . $data['grupo_id'];

        if (isset($data['marca_id']) && $data['marca_id'] != 0)
            $query .= " AND p.producto_marca = " . $data['marca_id'];

        if (isset($data['linea_id']) && $data['linea_id'] != 0)
            $query .= " AND p.producto_subgrupo = " . $data['linea_id'];

        if (isset($data['sublinea_id']) && $data['sublinea_id'] != 0)
            $query .= " AND p.producto_familia = " . $data['sublinea_id'];

        if (isset($data['tipo_documento']) && $data['tipo_documento'] != '0')
            $query .= " AND v.tipo_doc_fiscal = '" . $data['tipo_documento'] . "'";

        if (isset($data['estado']) && $data['estado'] != '0') {
            if ($data['estado'] == 'ENTREGADO') {
                $query .= " AND hpp.proceso_id = 4 AND (v.venta_status = 'ENTREGADO' OR v.venta_status = 'DEVUELTO PARCIALMENTE')";
            } elseif ($data['estado'] == 'DEVUELTO') {
                $query .= " AND hpp.proceso_id = 6 AND (v.venta_status = 'RECHAZADO' OR v.venta_status = 'DEVUELTO PARCIALMENTE')";
            } elseif ($data['estado'] == 'ANULADO') {
                $query .= " AND hpp.proceso_id = 1 AND v.venta_status = 'ANULADO'";
            }

        }


        $query .= "
            GROUP BY hpd.id 
            ORDER BY hpp.fecha_plan, v.venta_id ASC 
        ";

        return $this->db->query($query)->result();

    }

}

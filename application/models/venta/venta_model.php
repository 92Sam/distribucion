<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }

    function insertar_venta($venta_cabecera, $detalle, $montoboletas)
    {
        $this->db->trans_start(true);
        $this->db->trans_begin();

        $query_ins = $this->db->query("select (case when (`documento_venta`.`documento_Numero` = '9999999999') then
		   convert( right(concat('0000',(ifnull(`documento_venta`.`documento_Serie`,0) + 1)), 4) using latin1)
		   when (ifnull(`documento_venta`.`documento_Serie`,0) = 0) then convert( right(concat('0000', 1), 4) using latin1)
		   else `documento_venta`.`documento_Serie` end) AS `SERIE`,
		  (case when (`documento_venta`.`documento_Numero` = '9999999999') then right(concat((`documento_venta`.`documento_Numero` + 2)),10)
				else right(concat('0000000000',(`documento_venta`.`documento_Numero` + 1)),10) end) AS `NUMERO`, `documento_venta`.`nombre_tipo_documento` AS `Documento`
		   from `documento_venta` where `documento_venta`.`nombre_tipo_documento` = '" . NOTA_ENTREGA . "' order by `documento_venta`.`documento_Serie`, `documento_venta`.`documento_Numero` desc limit 0,1");
        $rs_ins = $query_ins->row_array();

        if (empty($rs_ins['SERIE'])) {
            $serie = '0001';
        } else {
            $serie = $rs_ins['SERIE'];
        }

        if (empty($rs_ins['NUMERO'])) {
            $numero = '0000000001';
        } else {
            $numero = $rs_ins['NUMERO'];
        }

        $tip_doc = array(
            'nombre_tipo_documento' => NOTA_ENTREGA,
            'documento_Serie' => $serie,
            'documento_Numero' => $numero
        );

        $this->db->insert('documento_venta', $tip_doc);
        $id_documento = $this->db->insert_id();

        // Venta Tipo
        if ($venta_cabecera['venta_status'] == 'GENERADO') {
            $venta_tipo = 'ENTREGA';
        } else {
            $venta_tipo = 'CAJA';
        }


        /****Validacion de ventas a contdo con pago total**/

        if ($venta_cabecera['diascondicionpagoinput'] > 0 && $venta_cabecera['importe'] >= $venta_cabecera['total']) {

            $id_contado = $this->db->query("select id_condiciones from condiciones_pago where nombre_condiciones='contado'")->row_array();
            $venta_cabecera['condicion_pago'] = $id_contado['id_condiciones'];
        }
        /*****************/
        $venta = array(
            'fecha' => $venta_cabecera['fecha'],
            'id_cliente' => $venta_cabecera['id_cliente'],
            'id_vendedor' => $venta_cabecera['id_vendedor'],
            'condicion_pago' => $venta_cabecera['condicion_pago'],
            'venta_status' => $venta_cabecera['venta_status'],
            'local_id' => $venta_cabecera['local_id'],
            'subtotal' => $venta_cabecera['subtotal'],
            'total_impuesto' => $venta_cabecera['total_impuesto'],
            'total' => $venta_cabecera['total'],
            'tipo_doc_fiscal' => $venta_cabecera['tipo_documento'],
            'numero_documento' => $id_documento,
            'pagado' => $venta_cabecera['importe'],
            'venta_tipo' => $venta_tipo,
            'retencion' => $venta_cabecera['retencion']
        );

        $this->db->insert('venta', $venta);
        $venta_id = $this->db->insert_id();

        ///// backup de venta
        $venta['venta_id'] = $venta_id;
        $this->db->insert('venta_backup', $venta);


        // Documento Fiscal
        if ($venta_cabecera['tipo_documento'] == 'FACTURA' || $venta_cabecera['tipo_documento'] == 'BOLETA DE VENTA') {
            $this->setDocumentoDetalle($venta_id, $venta_cabecera, $detalle, $montoboletas);
        }

        $kardex = array();
        // Detalle de la Venta
        foreach ($detalle as $row) {
            $cantidad_venta = floatval($row->cantidad);
            $unidad_medida_venta = $row->unidad_medida;
            $id_producto = $row->id_producto;
            $precio = floatval($row->precio);
            $precio_sugerido = $row->precio_sugerido;
            $importe = floatval($row->precio) * floatval($row->cantidad);
            $bono = isset($row->bono) ? $row->bono : false;


            $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
				FROM inventario where id_producto=' . $id_producto . ' and id_local=' . $venta_cabecera['local_id']);
            $inventario_existente = $query->row_array();
            $cantidad_vieja = 0;
            $fraccion_vieja = 0;

            if (isset($inventario_existente['cantidad'])) {
                $cantidad_vieja = $inventario_existente['cantidad'];
            }
            if (isset($inventario_existente['fraccion'])) {
                $fraccion_vieja = $inventario_existente['fraccion'];
            }

            // CALCULOS DE UNDIAD DE MEDIDA
            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

            $unidades_producto = $query->result_array();


            $unidad_maxima = $unidades_producto[0];
            $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
            $unidad_form = 0;
            foreach ($unidades_producto as $up) {
                if ($up['id_unidad'] == $unidad_medida_venta) {
                    $unidad_form = $up;
                }
            }

            $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
            $total_unidades_minimas_viejas = ($unidad_maxima['unidades'] * $cantidad_vieja) + $fraccion_vieja;

            $suma_cantidades = $total_unidades_minimas_viejas - $total_unidades_minimas;

            if ($suma_cantidades >= $unidad_maxima['unidades']) {
                $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                $cantidad_nueva = intval($resultado_division);
                $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                $fraccion_nueva = $resto_division;
            } else {
                if ($suma_cantidades < $unidad_maxima['unidades']) {
                    $cantidad_nueva = 0;
                    $fraccion_nueva = +$suma_cantidades;
                } else {
                    $cantidad_nueva = $cantidad_vieja;
                    $fraccion_nueva = +$suma_cantidades;
                }
            }

            if ($unidad_medida_venta == $unidad_maxima['id_unidad']) {
                $cantidad_nueva = $cantidad_vieja - $cantidad_venta;
                $fraccion_nueva = $fraccion_vieja;
            }

            if ($unidad_medida_venta == $unidad_minima['id_unidad']) {
                if ($suma_cantidades >= $unidad_maxima['unidades']) {
                    $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                    $cantidad_nueva = intval($resultado_division);
                    $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                    $fraccion_nueva = $resto_division;
                } else {
                    if ($suma_cantidades < $unidad_maxima['unidades']) {
                        $cantidad_nueva = 0;
                        $fraccion_nueva = +$suma_cantidades;
                    } else {
                        if ($cantidad_vieja > 0) {
                            $cantidad_nueva = $cantidad_vieja;
                            $fraccion_nueva = $suma_cantidades;
                        } else {
                            if (count($unidades_producto) > 1) {
                                $cantidad_nueva = 0;
                                $fraccion_nueva = $cantidad_venta;
                            } else {
                                $cantidad_nueva = $cantidad_venta;
                                $fraccion_nueva = 0;
                            }
                        }
                    }
                }
            }

            /********************CALCULO DE UTILIDAD****/
            ///busco el costo unitario del producto

            $query_costo_u = $this->db->query("select producto_id,  costo_unitario from producto
  WHERE producto_id=" . $id_producto . " ");
            $costo_unitario = $query_costo_u->row_array();

            if ($costo_unitario['costo_unitario'] == null or $costo_unitario['costo_unitario'] == '0') {

                $query_compra = $this->db->query("SELECT detalleingreso.*, ingreso.fecha_registro, unidades_has_producto.* FROM detalleingreso
JOIN ingreso ON ingreso.id_ingreso=detalleingreso.id_ingreso
JOIN unidades ON unidades.id_unidad=detalleingreso.unidad_medida
JOIN unidades_has_producto ON unidades_has_producto.id_unidad=detalleingreso.unidad_medida
AND unidades_has_producto.producto_id=detalleingreso.id_producto
WHERE detalleingreso.id_producto=" . $id_producto . " AND  fecha_registro=(SELECT MAX(fecha_registro) FROM ingreso
JOIN detalleingreso ON detalleingreso.id_ingreso=ingreso.id_ingreso WHERE detalleingreso.id_producto=" . $id_producto . ")  ");

                $result_ingreso = $query_compra->result_array();
                if (count($result_ingreso) > 0) {

                    $calcular_costo_u = ($result_ingreso[0]['precio'] / $result_ingreso[0]['unidades']) * $unidad_maxima['unidades'];
                    $promedio_compra = ($calcular_costo_u / $unidad_maxima['unidades']) * $unidad_form['unidades'];
                } else {
                    $promedio_compra = 0;
                }

            } else {
                $promedio_compra = ($costo_unitario['costo_unitario'] / $unidad_maxima['unidades']) * $unidad_form['unidades'];

            }

            $utilidad = ($precio - $promedio_compra) * $cantidad_venta;

            $detalle_item = array(
                'id_venta' => $venta_id,
                'id_producto' => $id_producto,
                'precio' => $precio,
                'precio_sugerido' => $precio_sugerido,
                'cantidad' => $cantidad_venta,
                'unidad_medida' => $unidad_medida_venta,
                'detalle_costo_promedio' => $promedio_compra,
                'detalle_utilidad' => $utilidad,
                'detalle_importe' => $importe,
                'bono' => $bono == 'true' ? 1 : 0,


            );

            $this->db->insert('detalle_venta', $detalle_item);

            /***********************esto s para la tabla detalle_venta_backup******************************/
            $this->db->insert('detalle_venta_backup', $detalle_item);
            /************************************/

            if (count($inventario_existente) > 0) {
                $inventario_nuevo = array(
                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva
                );
                $this->update_inventario($inventario_nuevo, array('id_inventario' => $inventario_existente['id_inventario']));
            } else {
                $inventario_nuevo = array(
                    'id_producto' => $id_producto,
                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva,
                    'id_local' => $venta_cabecera['local_id']
                );
                $this->db->insert('inventario', $inventario_nuevo);
            }


            /*******Preparo la data para insertar en el kardex********/
            $documento_fiscal = $this->db->get_where('documento_fiscal', array('venta_id' => $venta_id));
            $documento_fiscal = $documento_fiscal->row_array();

            $item_kardex = array(
                'dkardexFecha' => date('Y-m-d H:i:s:u'),
                'ckardexReferencia' => ($venta_cabecera['condicion_pago'] > 1) ? VENTA_CREDITO : VENTA_CONTADO,
                'cKardexProducto' => $row->id_producto,
                'nKardexCantidad' => $row->cantidad,
                'nKardexPrecioUnitario' => $row->precio,
                'nKardexPrecioTotal' => $row->detalle_importe,
                'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                'cKardexOperacion' => VENTA,
                'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                'cKardexAlmacen' => $venta_cabecera['local_id'],
                'cKardexTipo' => SALIDA,
                'cKardexIdOperacion' => $venta_id,
                'cKardexTipoDocumento' => NOTA_ENTREGA,
                'cKardexNumeroDocumento' => $numero,
                'cKardexNumeroSerie' => $serie,
                'cKardexEstado' => PEDIDO_GENERADO,
                'stockUManterior' => $total_unidades_minimas_viejas,
                'stockUMactual' => $suma_cantidades,
                'cKardexTipoDocumentoFiscal' => $venta_cabecera['tipo_documento'],
                'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                'cKardexCliente' => $venta_cabecera['id_cliente'],
                'cKardexProveedor' => null,
            );

            array_push($kardex, $item_kardex);

        }
        $this->db->insert_batch('kardex', $kardex);

        // Venta a Credito
        if ($venta_cabecera['diascondicionpagoinput'] > 0 or ($venta_cabecera['diascondicionpagoinput'] == 0 && $venta_cabecera['importe'] < $venta_cabecera['total'])) {
            $credito = array(
                'id_venta' => $venta_id,
                'dec_credito_montodeuda' => $venta_cabecera['total'],
                //'var_credito_estado' => ($venta_cabecera['importe'] > 0) ? CREDITO_ACUENTA : CREDITO_DEBE,
                'var_credito_estado' => CREDITO_DEBE,
                // 'dec_credito_montodebito' => $venta_cabecera['importe']
                'dec_credito_montodebito' => 0
            );

            //echo $venta_cabecera['venta_tipo'];

            if ($venta_cabecera['venta_tipo'] == 'CAJA') {

                $credito['dec_credito_montodebito'] = $venta_cabecera['importe'];

                if (floatval($venta_cabecera['importe']) != 0.00) {
                    $credito['var_credito_estado'] = CREDITO_ACUENTA;
                }
            }

            $this->db->insert('credito', $credito);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $venta_id;
        }

        $this->db->trans_off();
    }

    function setDocumento($venta_id, $documento_tipo = null)
    {
        $this->db->trans_start();

        $query = "select (
			case when (`documento_fiscal`.`documento_numero` = '9999999999') then
					convert( right(concat('0000',(ifnull(`documento_fiscal`.`documento_serie`,0) + 1)), 4) using latin1)
				when (ifnull(`documento_fiscal`.`documento_serie`,0) = 0) then 
					convert( right(concat('0000', 1), 4) using latin1)
				else `documento_fiscal`.`documento_serie` 
			end) AS `SERIE`,
			(
				case when (`documento_fiscal`.`documento_numero` = '9999999999') then 
					right(concat((`documento_fiscal`.`documento_numero` + 2)),10)
				else 
					right(concat('0000000000',(`documento_fiscal`.`documento_numero` + 1)),10) 
			end) AS `NUMERO`, `documento_fiscal`.`documento_tipo` AS `DOCUMENTO`
			   from `documento_fiscal` where `documento_fiscal`.`documento_tipo` = '" . $documento_tipo . "' order by `documento_fiscal`.`documento_serie`, `documento_fiscal`.`documento_numero` desc limit 0,1";

        $numFiscal = $this->db->query($query);

        $numData = $numFiscal->row_array();

        if (empty($numData['SERIE'])) {
            $serie = '0001';
        } else {
            $serie = $numData['SERIE'];
        }

        if (empty($numData['NUMERO'])) {
            $numero = '0000000001';
        } else {
            $numero = $numData['NUMERO'];
        }

        $newFiscal = array(
            'venta_id' => $venta_id,
            'documento_tipo' => $documento_tipo,
            'documento_serie' => $serie,
            'documento_numero' => $numero
        );

        $this->db->insert('documento_fiscal', $newFiscal);
        $id = $this->db->insert_id();

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return $id;
        }
    }

    function setDocumentoDetalle($venta_id, $venta_cabecera, $detalle, $montoboletas, $devoler = false)
    {

        // Monto Limite
        $MONTO_BOLETAS_VENTA = $montoboletas;


        $montototalenlaboleta = 0;
        $arregloitems = array();
        $arreglobonos = array();
        $j = 0;;
        $documento_fiscal_id = null;

        if ($venta_cabecera['tipo_documento'] == FACTURA) { // SI ES UNA FACTURA GENERO EL DOCUMENTO DE FORMA NORMAL
            $documento_fiscal_id = $this->setDocumento($venta_id, $venta_cabecera['tipo_documento']);


            $kardex = array();
            $i = 0;
            foreach ($detalle as $data) {
                $importe = $data->detalle_importe;
                $id_producto = $data->id_producto;
                $precio = $data->precio;
                $id_unidad = $data->unidad_medida;
                $cantidad = $data->cantidad;

                if ($documento_fiscal_id == null) {
                    $documento_fiscal_id = $this->setDocumento($venta_id, $venta_cabecera['tipo_documento']);
                }
                $newDetalle = array(
                    'documento_fiscal_id' => $documento_fiscal_id,
                    'id_venta' => $venta_id,
                    'id_producto' => $id_producto,
                    'precio' => $precio,
                    'cantidad' => $cantidad,
                    'id_unidad' => $id_unidad,
                    'detalle_importe' => $importe
                );
                $this->db->insert('documento_detalle', $newDetalle);


                $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
				FROM inventario where id_producto=' . $id_producto . ' and id_local=' . $venta_cabecera['local_id']);
                $inventario_existente = $query->row_array();
                $cantidad_vieja = 0;
                $fraccion_vieja = 0;

                if (isset($inventario_existente['cantidad'])) {
                    $cantidad_vieja = $inventario_existente['cantidad'];
                }
                if (isset($inventario_existente['fraccion'])) {
                    $fraccion_vieja = $inventario_existente['fraccion'];
                }
                $unidad_medida_venta = $data->unidad_medida;
                $cantidad_venta = $data->cantidad;

                // CALCULOS DE UNDIAD DE MEDIDA
                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

                $unidades_producto = $query->result_array();


                $unidad_maxima = $unidades_producto[0];
                $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
                $unidad_form = 0;
                foreach ($unidades_producto as $up) {
                    if ($up['id_unidad'] == $unidad_medida_venta) {
                        $unidad_form = $up;
                    }
                }

                $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
                $total_unidades_minimas_viejas = ($unidad_maxima['unidades'] * $cantidad_vieja) + $fraccion_vieja;

                $suma_cantidades = $total_unidades_minimas_viejas - $total_unidades_minimas;

                /*******Preparo la data para insertar en el kardex********/
                $documento_fiscal = $this->db->get_where('documento_fiscal', array('documento_fiscal_id' => $documento_fiscal_id));
                $documento_fiscal = $documento_fiscal->row_array();

                if ($devoler == true) {


                    $sql_detalle_bk = $this->db->query("SELECT * FROM detalle_venta_backup
            WHERE detalle_venta_backup.id_producto='$id_producto' and detalle_venta_backup.id_venta=" . $venta_id);

                    $query_detalle_venta_bk = $sql_detalle_bk->row_array();
                    $sql_detalle = $this->db->query("SELECT * FROM detalle_venta
            JOIN producto ON producto.producto_id=detalle_venta.id_producto
            LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
            LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
            JOIN venta ON venta.`venta_id`=detalle_venta.`id_venta`
             LEFT JOIN documento_venta ON documento_venta.id_tipo_documento=venta.numero_documento
            LEFT JOIN documento_fiscal ON documento_fiscal.venta_id=venta.venta_id
            WHERE detalle_venta.id_venta='$venta_id' group by detalle_venta.id_detalle");

                    $query_detalle_venta = $sql_detalle->result_array();


                    $local = $query_detalle_venta[$i]['local_id'];
                    $unidad_maxima = $query_detalle_venta[$i]['unidades'];
                    $unidad_minima = $query_detalle_venta[sizeof($query_detalle_venta) - 1];

                    $producto_id = $query_detalle_venta[$i]['producto_id'];
                    $unidad = $query_detalle_venta[$i]['unidad_medida'];
                    $cantidad_compra = $query_detalle_venta[$i]['cantidad'];

                    $sql_inventario = $this->db->query("SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto='$producto_id' and id_local='$local'");
                    $inventario_existente = $sql_inventario->row_array();
                    if (count($inventario_existente) > 0) {
                        $id_inventario = $inventario_existente['id_inventario'];
                        $cantidad_vieja = $inventario_existente['cantidad'];
                        $fraccion_vieja = $inventario_existente['fraccion'];
                    } else {
                        $cantidad_vieja = 0;
                        $fraccion_vieja = 0;
                    }


                    $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$producto_id' ORDER BY orden");

                    $unidades_producto = $query->result_array();

                    foreach ($unidades_producto as $row) {
                        if ($row['id_unidad'] == $unidad) {
                            $unidad_form = $row;
                        }
                    }

                    if ($cantidad_vieja >= 1) {
                        $unidades_minimas_inventario = ($cantidad_vieja * $query_detalle_venta[$i]['unidades']) + $fraccion_vieja;
                    } else {
                        $unidades_minimas_inventario = $fraccion_vieja;
                    }

                    $unidades_minimas_detalle = $unidad_form['unidades'] * $cantidad_compra;

                    $suma = $unidades_minimas_inventario + $unidades_minimas_detalle;

                    $cont = 0;
                    while ($suma >= $unidad_maxima) {
                        $cont++;
                        $suma = $suma - $unidad_maxima;
                    }

                    if ($cont < 1) {
                        $cantidad_nueva = 0;
                        $fraccion_nueva = $suma;
                    } else {
                        $cantidad_nueva = $cont;
                        $fraccion_nueva = $suma;
                    }

                    $inventario_nuevo = array(
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva
                    );

                    $estadonuevo = 'ENTRADA POR ';
                    if ($venta_cabecera['devolver'] == 'true') {
                        $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                    } else {
                        $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                    }


                    if (isset($query_detalle_venta_bk['cantidad'])) {
                        if ($query_detalle_venta_bk['cantidad'] - $data->cantidad > 0) {
                            $item_kardex = array(
                                'dkardexFecha' => date('Y-m-d H:i:s:u'),
                                'ckardexReferencia' => $estadonuevo,
                                'cKardexProducto' => $data->id_producto,
                                'nKardexCantidad' => $query_detalle_venta_bk['cantidad'] - $data->cantidad,
                                'nKardexPrecioUnitario' => $data->precio,
                                'nKardexPrecioTotal' => $data->precio * ($query_detalle_venta_bk['cantidad'] - $data->cantidad),
                                'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                                'cKardexOperacion' => VENTA,
                                'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                                'cKardexAlmacen' => $venta_cabecera['local_id'],
                                'cKardexTipo' => ENTRADA,
                                'cKardexIdOperacion' => $venta_id,
                                'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                                'stockUManterior' => $unidades_minimas_inventario,
                                'stockUMactual' => $suma,
                                'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                                'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                                'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                                'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                                'cKardexProveedor' => null,
                            );

                            array_push($kardex, $item_kardex);
                        } else {

                            $estadonuevo = 'SALIDA POR ';
                            if ($venta_cabecera['devolver'] == 'true') {
                                $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                            } else {
                                $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                            }

                            $item_kardex = array(
                                'dkardexFecha' => date('Y-m-d H:i:s:u'),
                                'ckardexReferencia' => $estadonuevo,
                                'cKardexProducto' => $data->id_producto,
                                'nKardexCantidad' => $data->cantidad - $query_detalle_venta_bk['cantidad'],
                                'nKardexPrecioUnitario' => $data->precio,
                                'nKardexPrecioTotal' => $data->precio * ($data->cantidad - $query_detalle_venta_bk['cantidad']),
                                'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                                'cKardexOperacion' => VENTA,
                                'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                                'cKardexAlmacen' => $venta_cabecera['local_id'],
                                'cKardexTipo' => SALIDA,
                                'cKardexIdOperacion' => $venta_id,

                                'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                                'stockUManterior' => $unidades_minimas_inventario,
                                'stockUMactual' => $suma,
                                'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                                'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                                'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                                'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                                'cKardexProveedor' => null,
                            );

                            array_push($kardex, $item_kardex);
                        }
                    } else {
                        $item_kardex = array(
                            'dkardexFecha' => date('Y-m-d H:i:s:u'),
                            'ckardexReferencia' => $estadonuevo,
                            'cKardexProducto' => $data->id_producto,
                            'nKardexCantidad' => $data->cantidad,
                            'nKardexPrecioUnitario' => $data->precio,
                            'nKardexPrecioTotal' => $data->detalle_importe,
                            'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                            'cKardexOperacion' => VENTA,
                            'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                            'cKardexAlmacen' => $venta_cabecera['local_id'],
                            'cKardexTipo' => SALIDA,
                            'cKardexIdOperacion' => $venta_id,
                            'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                            'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                            'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                            'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                            'stockUManterior' => $total_unidades_minimas_viejas,
                            'stockUMactual' => $suma_cantidades,
                            'cKardexCliente' => $venta_cabecera['id_cliente'],
                            'cKardexProveedor' => null,
                        );

                        array_push($kardex, $item_kardex);
                    }

                } else {
                    $item_kardex = array(
                        'dkardexFecha' => date('Y-m-d H:i:s:u'),
                        'ckardexReferencia' => ($venta_cabecera['condicion_pago'] > 1) ? VENTA_CREDITO : VENTA_CONTADO,
                        'cKardexProducto' => $data->id_producto,
                        'nKardexCantidad' => $data->cantidad,
                        'nKardexPrecioUnitario' => $data->precio,
                        'nKardexPrecioTotal' => $data->detalle_importe,
                        'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                        'cKardexOperacion' => VENTA,
                        'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                        'cKardexAlmacen' => $venta_cabecera['local_id'],
                        'cKardexTipo' => SALIDA,
                        'cKardexIdOperacion' => $venta_id,
                        'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                        'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                        'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                        'cKardexEstado' => PEDIDO_GENERADO,
                        'stockUManterior' => $total_unidades_minimas_viejas,
                        'stockUMactual' => $suma_cantidades,
                        'cKardexCliente' => $venta_cabecera['id_cliente'],
                        'cKardexProveedor' => null,
                    );

                    array_push($kardex, $item_kardex);

                }

                $i++;
            }

            $this->db->insert_batch('kardex_fiscal', $kardex);
        } else { // SI ES UNA BOLETA ENTRO EN ESTE PROCESO ENGORROSO
            // var_dump($detalle);

            //RECORRO TODOS LOS ITEMS PARA SEPARAR LOS PRODUCTOS NORMALES DE LOS BONOS, Y LOS SEPARO EN ARREGLOS APARTE

            foreach ($detalle as $data) {
                if (isset($data->bono) && $data->bono == 1) {
                    $arreglobonos[] = $data;

                } else {
                    $arregloitems[] = $data;
                }
            }

            $doc = 0;
            $cantidadenboleta = 0;
            $producto_anterior = null;
            $bonoanterior = null;
            $romper = false;

            $arregloboletas = array();
            $sumamontototalenlaboleta = 0;
//RECORRO EL AREGLO DE PRODUCTOS NORMALES PARA SEPARARLOS EN BOLETAS
            $itemsboletas = array(); // CREO UN ARAY DE ITEMS QUE TENDRA LA BOLETA
            foreach ($arregloitems as $data) {


                $id_producto = $data->id_producto;


                $precio = $data->precio;
                $id_unidad = $data->unidad_medida;
                $cantidad = $data->cantidad;


                // ESTE OBJETO DETALLE SE USA PARA ASIGNARLO A LA BOLETA
                $newDetalle = array(
                    'id_venta' => $venta_id,
                    'id_producto' => $id_producto,
                    'precio' => $precio,
                    'cantidad' => $cantidadenboleta,
                    'id_unidad' => $id_unidad,
                    'detalle_importe' => $montototalenlaboleta
                );


                /* if ($producto_anterior != $id_producto) {
                     $producto_anterior = $data->id_producto;
                 }*/

                // RECORRO UNITARIAMENTE CADA CANTIDAD DEL ITEM ACTUAL

                for ($i = 1; $i <= $cantidad; $i++) {

                    $montototalenlaboleta += $precio;
                    $cantidadenboleta++;

                    if (($montototalenlaboleta + $precio + $sumamontototalenlaboleta) >= $MONTO_BOLETAS_VENTA) {
                        //

                        if (($romper == true && $doc > 0) || ($montototalenlaboleta >= $MONTO_BOLETAS_VENTA) ||
                            ($montototalenlaboleta + $sumamontototalenlaboleta) >= $MONTO_BOLETAS_VENTA
                        ) {
                            $doc++;
                        }

                        if ($doc == 0 and count($arregloboletas) > 0 and $romper != false) {
                            $doc++;
                        }

                        if ($montototalenlaboleta >= $MONTO_BOLETAS_VENTA) {


                            $newDetalle['cantidad'] = $cantidadenboleta;
                            $newDetalle['detalle_importe'] = $precio * $cantidadenboleta;
                            $newDetalle['precio'] = $precio;
                            $newDetalle['documento_fiscal_id'] = $doc;
                            $itemsboletas = $newDetalle;

                            // var_dump($doc);
                            $arregloboletas[$doc][] = $itemsboletas;
                            $montototalenlaboleta = 0;
                            $cantidadenboleta = 0;
                            $romper = true;

                        } else {

                            if (($montototalenlaboleta + $sumamontototalenlaboleta) >= $MONTO_BOLETAS_VENTA) {

                                $newDetalle['cantidad'] = $cantidadenboleta;
                                $newDetalle['detalle_importe'] = $precio * $cantidadenboleta;
                                $newDetalle['precio'] = $precio;
                                $newDetalle['documento_fiscal_id'] = $doc;
                                $itemsboletas = $newDetalle;


                                $arregloboletas[$doc][] = $itemsboletas;
                                $romper = false;

                                $sumamontototalenlaboleta = $montototalenlaboleta;
                                $cantidadenboleta = 0;
                                $montototalenlaboleta = 0;

                            } else {

                                if ($cantidad == $i) {


                                    $newDetalle['cantidad'] = $cantidadenboleta;
                                    $newDetalle['detalle_importe'] = $precio * $cantidadenboleta;
                                    $newDetalle['precio'] = $precio;
                                    $newDetalle['documento_fiscal_id'] = $doc;
                                    $itemsboletas = $newDetalle;


                                    $arregloboletas[$doc][] = $itemsboletas;

                                    $cantidadenboleta = 0;
                                    $romper = false;
                                } else {
                                    $newDetalle['cantidad'] = $cantidadenboleta;
                                    $newDetalle['detalle_importe'] = $precio * $cantidadenboleta;
                                    $newDetalle['precio'] = $precio;
                                    $newDetalle['documento_fiscal_id'] = $doc;
                                    $itemsboletas = $newDetalle;
                                    $sumamontototalenlaboleta = 0;

                                    $arregloboletas[$doc][] = $itemsboletas;
                                    $montototalenlaboleta = 0;
                                    $cantidadenboleta = 0;
                                    $romper = true;
                                }
                            }

                        }

                        $itemsboletas = array();

                    } else {

                        if ($cantidad == $i) {
                            $itemsboletas = array();
                            if ($romper == true) {
                                $romper = false;
                                $doc++;

                            }

                            $newDetalle['cantidad'] = $cantidadenboleta;
                            $newDetalle['detalle_importe'] = $cantidadenboleta * $precio;
                            $newDetalle['precio'] = $precio;
                            $newDetalle['documento_fiscal_id'] = $doc;


                            $itemsboletas = $newDetalle;


                            $arregloboletas[$doc][] = $itemsboletas;

                            $sumamontototalenlaboleta += $montototalenlaboleta;
                            $montototalenlaboleta = 0;
                            $cantidadenboleta = 0;

                            $itemsboletas = array();
                        }


                    }

                }


                $j++;
            }


            $i = 0; // aqui quiero agregar lso bonos a cada boleta

            foreach ($arreglobonos as $bono) {

                if ($i >= sizeof($arregloboletas)) {
                    $i = 0;
                }
                // var_dump($bono);
                $newDetalle = array(
                    'id_venta' => $venta_id,
                    'id_producto' => $bono->id_producto,
                    'precio' => $bono->precio,
                    'cantidad' => $bono->cantidad,
                    'id_unidad' => $bono->unidad_medida,
                    'detalle_importe' => $bono->detalle_importe,
                    'documento_fiscal_id' => $doc
                );
                $arregloboletas[$i][] = $newDetalle;
                $i++;

            }

//var_dump($arregloboletas);
            $kardex = array();
// aqui entonces ya con mi arreglo boleta lo recorro para insertar en la  bd
            foreach ($arregloboletas as $data) {

                $documento_fiscal_id = $this->setDocumento($venta_id, $venta_cabecera['tipo_documento']);

                $arreglo_documento_detalle = array(
                    'documento_fiscal_id' => $documento_fiscal_id,
                    'id_venta' => $venta_id,
                    'id_producto' => $data[0]['id_producto'],
                    'precio' => $data[0]['precio'],
                    'cantidad' => $data[0]['cantidad'],
                    'id_unidad' => $data[0]['id_unidad'],
                    'detalle_importe' => $data[0]['detalle_importe'],
                );
                $this->db->insert('documento_detalle', $arreglo_documento_detalle);


                $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
				FROM inventario where id_producto=' . $id_producto . ' and id_local=' . $venta_cabecera['local_id']);
                $inventario_existente = $query->row_array();
                $cantidad_vieja = 0;
                $fraccion_vieja = 0;

                if (isset($inventario_existente['cantidad'])) {
                    $cantidad_vieja = $inventario_existente['cantidad'];
                }
                if (isset($inventario_existente['fraccion'])) {
                    $fraccion_vieja = $inventario_existente['fraccion'];
                }
                $unidad_medida_venta = $data[0]['id_unidad'];
                $cantidad_venta = $data[0]['cantidad'];

                // CALCULOS DE UNDIAD DE MEDIDA
                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

                $unidades_producto = $query->result_array();


                $unidad_maxima = $unidades_producto[0];
                $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
                $unidad_form = 0;
                foreach ($unidades_producto as $up) {
                    if ($up['id_unidad'] == $unidad_medida_venta) {
                        $unidad_form = $up;
                    }
                }

                $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
                $total_unidades_minimas_viejas = ($unidad_maxima['unidades'] * $cantidad_vieja) + $fraccion_vieja;

                $suma_cantidades = $total_unidades_minimas_viejas - $total_unidades_minimas;

                /*******Preparo la data para insertar en el kardex********/
                $documento_fiscal = $this->db->get_where('documento_fiscal', array('documento_fiscal_id' => $documento_fiscal_id));
                $documento_fiscal = $documento_fiscal->row_array();
                if ($devoler == true) {


                    $sql_detalle_bk = $this->db->query("SELECT * FROM detalle_venta_backup
            WHERE detalle_venta_backup.id_producto='$id_producto' and detalle_venta_backup.id_venta=" . $venta_id);

                    $query_detalle_venta_bk = $sql_detalle_bk->row_array();
                    $sql_detalle = $this->db->query("SELECT * FROM detalle_venta
            JOIN producto ON producto.producto_id=detalle_venta.id_producto
            LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
            LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
            JOIN venta ON venta.`venta_id`=detalle_venta.`id_venta`
             LEFT JOIN documento_venta ON documento_venta.id_tipo_documento=venta.numero_documento
            LEFT JOIN documento_fiscal ON documento_fiscal.venta_id=venta.venta_id
            WHERE detalle_venta.id_venta='$venta_id' group by detalle_venta.id_detalle");

                    $query_detalle_venta = $sql_detalle->result_array();


                    $local = $query_detalle_venta[$i]['local_id'];
                    $unidad_maxima = $query_detalle_venta[$i]['unidades'];
                    $unidad_minima = $query_detalle_venta[sizeof($query_detalle_venta) - 1];

                    $producto_id = $query_detalle_venta[$i]['producto_id'];
                    $unidad = $query_detalle_venta[$i]['unidad_medida'];
                    $cantidad_compra = $query_detalle_venta[$i]['cantidad'];

                    $sql_inventario = $this->db->query("SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto='$producto_id' and id_local='$local'");
                    $inventario_existente = $sql_inventario->row_array();
                    if (count($inventario_existente) > 0) {
                        $id_inventario = $inventario_existente['id_inventario'];
                        $cantidad_vieja = $inventario_existente['cantidad'];
                        $fraccion_vieja = $inventario_existente['fraccion'];
                    } else {
                        $cantidad_vieja = 0;
                        $fraccion_vieja = 0;
                    }


                    $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$producto_id' ORDER BY orden");

                    $unidades_producto = $query->result_array();

                    foreach ($unidades_producto as $row) {
                        if ($row['id_unidad'] == $unidad) {
                            $unidad_form = $row;
                        }
                    }

                    if ($cantidad_vieja >= 1) {
                        $unidades_minimas_inventario = ($cantidad_vieja * $query_detalle_venta[$i]['unidades']) + $fraccion_vieja;
                    } else {
                        $unidades_minimas_inventario = $fraccion_vieja;
                    }

                    $unidades_minimas_detalle = $unidad_form['unidades'] * $cantidad_compra;

                    $suma = $unidades_minimas_inventario + $unidades_minimas_detalle;

                    $cont = 0;
                    while ($suma >= $unidad_maxima) {
                        $cont++;
                        $suma = $suma - $unidad_maxima;
                    }

                    if ($cont < 1) {
                        $cantidad_nueva = 0;
                        $fraccion_nueva = $suma;
                    } else {
                        $cantidad_nueva = $cont;
                        $fraccion_nueva = $suma;
                    }

                    $inventario_nuevo = array(
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva
                    );

                    $estadonuevo = 'ENTRADA POR ';
                    if ($venta_cabecera['devolver'] == 'true') {
                        $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                    } else {
                        $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                    }
                    //var_dump($query_detalle_venta_bk);
                    // echo $query_detalle_venta_bk['cantidad'];
                    //   echo $query_detalle_venta_bk['cantidad'];


                    if (isset($query_detalle_venta_bk['cantidad'])) {
                        if ($query_detalle_venta_bk['cantidad'] - $data[0]['cantidad'] > 0) {
                            $item_kardex = array(
                                'dkardexFecha' => date('Y-m-d H:i:s:u'),
                                'ckardexReferencia' => $estadonuevo,
                                'cKardexProducto' => $data[0]['id_producto'],
                                'nKardexCantidad' => $query_detalle_venta_bk['cantidad'] - $data[0]['cantidad'],
                                'nKardexPrecioUnitario' => $data[0]['precio'],
                                'nKardexPrecioTotal' => $data[0]['precio'] * ($query_detalle_venta_bk['cantidad'] - $data[0]['cantidad']),
                                'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                                'cKardexOperacion' => VENTA,
                                'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                                'cKardexAlmacen' => $venta_cabecera['local_id'],
                                'cKardexTipo' => ENTRADA,
                                'cKardexIdOperacion' => $venta_id,

                                'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                                'stockUManterior' => $unidades_minimas_inventario,
                                'stockUMactual' => $suma,
                                'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                                'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                                'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                                'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                                'cKardexProveedor' => null,
                            );

                            array_push($kardex, $item_kardex);
                        } else {

                            $estadonuevo = 'SALIDA POR ';
                            if ($venta_cabecera['devolver'] == 'true') {
                                $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                            } else {
                                $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                            }

                            $item_kardex = array(
                                'dkardexFecha' => date('Y-m-d H:i:s:u'),
                                'ckardexReferencia' => $estadonuevo,
                                'cKardexProducto' => $data[0]['id_producto'],
                                'nKardexCantidad' => $data[0]['cantidad'] - $query_detalle_venta_bk['cantidad'],
                                'nKardexPrecioUnitario' => $data[0]['precio'],
                                'nKardexPrecioTotal' => $data[0]['precio'] * ($data[0]['cantidad'] - $query_detalle_venta_bk['cantidad']),
                                'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                                'cKardexOperacion' => VENTA,
                                'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                                'cKardexAlmacen' => $venta_cabecera['local_id'],
                                'cKardexTipo' => SALIDA,
                                'cKardexIdOperacion' => $venta_id,

                                'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                                'stockUManterior' => $unidades_minimas_inventario,
                                'stockUMactual' => $suma,
                                'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                                'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                                'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                                'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                                'cKardexProveedor' => null,
                            );

                            array_push($kardex, $item_kardex);
                        }
                    } else {

                        $item_kardex = array(
                            'dkardexFecha' => date('Y-m-d H:i:s:u'),
                            'ckardexReferencia' => $estadonuevo,
                            'cKardexProducto' => $data[0]['id_producto'],
                            'nKardexCantidad' => $data[0]['cantidad'],
                            'nKardexPrecioUnitario' => $data[0]['precio'],
                            'nKardexPrecioTotal' => $data[0]['detalle_importe'],
                            'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                            'cKardexOperacion' => VENTA,
                            'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                            'cKardexAlmacen' => $venta_cabecera['local_id'],
                            'cKardexTipo' => SALIDA,
                            'cKardexIdOperacion' => $venta_id,
                            'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                            'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                            'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                            'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                            'stockUManterior' => $total_unidades_minimas_viejas,
                            'stockUMactual' => $suma_cantidades,
                            'cKardexCliente' => $venta_cabecera['id_cliente'],
                            'cKardexProveedor' => null,
                        );

                        array_push($kardex, $item_kardex);
                    }

                } else {

                    $item_kardex = array(
                        'dkardexFecha' => date('Y-m-d H:i:s:u'),
                        'ckardexReferencia' => ($venta_cabecera['condicion_pago'] > 1) ? VENTA_CREDITO : VENTA_CONTADO,
                        'cKardexProducto' => $data[0]['id_producto'],
                        'nKardexCantidad' => $data[0]['cantidad'],
                        'nKardexPrecioUnitario' => $data[0]['precio'],
                        'nKardexPrecioTotal' => $data[0]['detalle_importe'],
                        'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                        'cKardexOperacion' => VENTA,
                        'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                        'cKardexAlmacen' => $venta_cabecera['local_id'],
                        'cKardexTipo' => SALIDA,
                        'cKardexIdOperacion' => $venta_id,
                        'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                        'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                        'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                        'cKardexEstado' => PEDIDO_GENERADO,
                        'stockUManterior' => $total_unidades_minimas_viejas,
                        'stockUMactual' => $suma_cantidades,
                        'cKardexCliente' => $venta_cabecera['id_cliente'],
                        'cKardexProveedor' => null,
                    );

                    array_push($kardex, $item_kardex);
                }

                if (count($data) > 1) {

                    for ($j = 1; $j < count($data); $j++) {

                        // if (is_array($data[$j])) {

                        $arreglo_documento_detalle = array(
                            'documento_fiscal_id' => $documento_fiscal_id,
                            'id_venta' => $venta_id,
                            'id_producto' => $data[$j]['id_producto'],
                            'precio' => $data[$j]['precio'],
                            'cantidad' => $data[$j]['cantidad'],
                            'id_unidad' => $data[$j]['id_unidad'],
                            'detalle_importe' => $data[$j]['detalle_importe'],
                        );
                        /* } else {
                              $arreglo_documento_detalle = array(
                                  'documento_fiscal_id' => $documento_fiscal_id,
                                  'id_venta' => $venta_id,
                                  'id_producto' => $data[$j]->id_producto,
                                  'precio' => 0,
                                  'cantidad' => $data[$j]->cantidad,
                                  'id_unidad' => $data[$j]->unidad_medida,
                                  'detalle_importe' => 0
                              );

                          }*/

                        $this->db->insert('documento_detalle', $arreglo_documento_detalle);


                        $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
				FROM inventario where id_producto=' . $id_producto . ' and id_local=' . $venta_cabecera['local_id']);
                        $inventario_existente = $query->row_array();
                        $cantidad_vieja = 0;
                        $fraccion_vieja = 0;

                        if (isset($inventario_existente['cantidad'])) {
                            $cantidad_vieja = $inventario_existente['cantidad'];
                        }
                        if (isset($inventario_existente['fraccion'])) {
                            $fraccion_vieja = $inventario_existente['fraccion'];
                        }
                        $unidad_medida_venta = $data[$j]['id_unidad'];
                        $cantidad_venta = $data[$j]['cantidad'];

                        // CALCULOS DE UNDIAD DE MEDIDA
                        $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

                        $unidades_producto = $query->result_array();


                        $unidad_maxima = $unidades_producto[0];
                        $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
                        $unidad_form = 0;
                        foreach ($unidades_producto as $up) {
                            if ($up['id_unidad'] == $unidad_medida_venta) {
                                $unidad_form = $up;
                            }
                        }

                        $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
                        $total_unidades_minimas_viejas = ($unidad_maxima['unidades'] * $cantidad_vieja) + $fraccion_vieja;

                        $suma_cantidades = $total_unidades_minimas_viejas - $total_unidades_minimas;

                        /*******Preparo la data para insertar en el kardex********/
                        $documento_fiscal = $this->db->get_where('documento_fiscal', array('documento_fiscal_id' => $documento_fiscal_id));
                        $documento_fiscal = $documento_fiscal->row_array();
                        if ($devoler == true) {


                            $sql_detalle_bk = $this->db->query("SELECT * FROM detalle_venta_backup
            WHERE detalle_venta_backup.id_producto='$id_producto' and detalle_venta_backup.id_venta=" . $venta_id);

                            $query_detalle_venta_bk = $sql_detalle_bk->row_array();
                            $sql_detalle = $this->db->query("SELECT * FROM detalle_venta
            JOIN producto ON producto.producto_id=detalle_venta.id_producto
            LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
            LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
            JOIN venta ON venta.`venta_id`=detalle_venta.`id_venta`
             LEFT JOIN documento_venta ON documento_venta.id_tipo_documento=venta.numero_documento
            LEFT JOIN documento_fiscal ON documento_fiscal.venta_id=venta.venta_id
            WHERE detalle_venta.id_venta='$venta_id' group by detalle_venta.id_detalle");

                            $query_detalle_venta = $sql_detalle->result_array();


                            $local = $query_detalle_venta[$i]['local_id'];
                            $unidad_maxima = $query_detalle_venta[$i]['unidades'];
                            $unidad_minima = $query_detalle_venta[sizeof($query_detalle_venta) - 1];

                            $producto_id = $query_detalle_venta[$i]['producto_id'];
                            $unidad = $query_detalle_venta[$i]['unidad_medida'];
                            $cantidad_compra = $query_detalle_venta[$i]['cantidad'];

                            $sql_inventario = $this->db->query("SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto='$producto_id' and id_local='$local'");
                            $inventario_existente = $sql_inventario->row_array();
                            if (count($inventario_existente) > 0) {
                                $id_inventario = $inventario_existente['id_inventario'];
                                $cantidad_vieja = $inventario_existente['cantidad'];
                                $fraccion_vieja = $inventario_existente['fraccion'];
                            } else {
                                $cantidad_vieja = 0;
                                $fraccion_vieja = 0;
                            }


                            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$producto_id' ORDER BY orden");

                            $unidades_producto = $query->result_array();

                            foreach ($unidades_producto as $row) {
                                if ($row['id_unidad'] == $unidad) {
                                    $unidad_form = $row;
                                }
                            }

                            if ($cantidad_vieja >= 1) {
                                $unidades_minimas_inventario = ($cantidad_vieja * $query_detalle_venta[$i]['unidades']) + $fraccion_vieja;
                            } else {
                                $unidades_minimas_inventario = $fraccion_vieja;
                            }

                            $unidades_minimas_detalle = $unidad_form['unidades'] * $cantidad_compra;

                            $suma = $unidades_minimas_inventario + $unidades_minimas_detalle;

                            $cont = 0;
                            while ($suma >= $unidad_maxima) {
                                $cont++;
                                $suma = $suma - $unidad_maxima;
                            }

                            if ($cont < 1) {
                                $cantidad_nueva = 0;
                                $fraccion_nueva = $suma;
                            } else {
                                $cantidad_nueva = $cont;
                                $fraccion_nueva = $suma;
                            }

                            $inventario_nuevo = array(
                                'cantidad' => $cantidad_nueva,
                                'fraccion' => $fraccion_nueva
                            );

                            $estadonuevo = 'ENTRADA POR ';
                            if ($venta_cabecera['devolver'] == 'true') {
                                $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                            } else {
                                $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                            }
                            //var_dump($query_detalle_venta_bk);
                            // echo $query_detalle_venta_bk['cantidad'];
                            // echo $row->cantidad;

                            //  echo $query_detalle_venta_bk['cantidad'] - $row->cantidad;
                            // echo $query_detalle_venta_bk['cantidad'];
                            if (isset($query_detalle_venta_bk['cantidad'])) {
                                if ($query_detalle_venta_bk['cantidad'] - $data[$j]['cantidad'] > 0) {
                                    $item_kardex = array(
                                        'dkardexFecha' => date('Y-m-d H:i:s:u'),
                                        'ckardexReferencia' => $estadonuevo,
                                        'cKardexProducto' => $data[$j]['id_producto'],
                                        'nKardexCantidad' => $query_detalle_venta_bk['cantidad'] - $data[$j]['cantidad'],
                                        'nKardexPrecioUnitario' => $data[$j]['precio'],
                                        'nKardexPrecioTotal' => $data[$j]['precio'] * ($query_detalle_venta_bk['cantidad'] - $data[$j]['cantidad']),
                                        'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                                        'cKardexOperacion' => VENTA,
                                        'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                                        'cKardexAlmacen' => $venta_cabecera['local_id'],
                                        'cKardexTipo' => ENTRADA,
                                        'cKardexIdOperacion' => $venta_id,

                                        'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                                        'stockUManterior' => $unidades_minimas_inventario,
                                        'stockUMactual' => $suma,
                                        'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                                        'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                                        'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                                        'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                                        'cKardexProveedor' => null,
                                    );

                                    array_push($kardex, $item_kardex);
                                } else {

                                    $estadonuevo = 'SALIDA POR ';
                                    if ($venta_cabecera['devolver'] == 'true') {
                                        $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                                    } else {
                                        $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                                    }

                                    $item_kardex = array(
                                        'dkardexFecha' => date('Y-m-d H:i:s:u'),
                                        'ckardexReferencia' => $estadonuevo,
                                        'cKardexProducto' => $data[$j]['id_producto'],
                                        'nKardexCantidad' => $data[$j]['cantidad'] - $query_detalle_venta_bk['cantidad'],
                                        'nKardexPrecioUnitario' => $data[$j]['precio'],
                                        'nKardexPrecioTotal' => $data[$j]['precio'] * ($data[$j]['cantidad'] - $query_detalle_venta_bk['cantidad']),
                                        'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                                        'cKardexOperacion' => VENTA,
                                        'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                                        'cKardexAlmacen' => $venta_cabecera['local_id'],
                                        'cKardexTipo' => SALIDA,
                                        'cKardexIdOperacion' => $venta_id,

                                        'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                                        'stockUManterior' => $unidades_minimas_inventario,
                                        'stockUMactual' => $suma,

                                        'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                                        'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                                        'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                                        'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                                        'cKardexProveedor' => null,
                                    );

                                    array_push($kardex, $item_kardex);
                                }
                            } else {

                                $item_kardex = array(
                                    'dkardexFecha' => date('Y-m-d H:i:s:u'),
                                    'ckardexReferencia' => $estadonuevo,
                                    'cKardexProducto' => $data[$j]['id_producto'],
                                    'nKardexCantidad' => $data[$j]['cantidad'],
                                    'nKardexPrecioUnitario' => $data[$j]['precio'],
                                    'nKardexPrecioTotal' => $data[$j]['detalle_importe'],
                                    'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                                    'cKardexOperacion' => VENTA,
                                    'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                                    'cKardexAlmacen' => $venta_cabecera['local_id'],
                                    'cKardexTipo' => SALIDA,
                                    'cKardexIdOperacion' => $venta_id,
                                    'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                                    'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                                    'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                                    'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                                    'stockUManterior' => $total_unidades_minimas_viejas,
                                    'stockUMactual' => $suma_cantidades,
                                    'cKardexCliente' => $venta_cabecera['id_cliente'],
                                    'cKardexProveedor' => null,
                                );

                                // var_dump($item_kardex);
                                array_push($kardex, $item_kardex);
                            }

                        } else {

                            $item_kardex = array(
                                'dkardexFecha' => date('Y-m-d H:i:s:u'),
                                'ckardexReferencia' => ($venta_cabecera['condicion_pago'] > 1) ? VENTA_CREDITO : VENTA_CONTADO,
                                'cKardexProducto' => $data[$j]['id_producto'],
                                'nKardexCantidad' => $data[$j]['cantidad'],
                                'nKardexPrecioUnitario' => $data[$j]['precio'],
                                'nKardexPrecioTotal' => $data[$j]['detalle_importe'],
                                'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                                'cKardexOperacion' => VENTA,
                                'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                                'cKardexAlmacen' => $venta_cabecera['local_id'],
                                'cKardexTipo' => SALIDA,
                                'cKardexIdOperacion' => $venta_id,
                                'cKardexTipoDocumentoFiscal' => $documento_fiscal['documento_tipo'],
                                'cKardexNumeroDocumentoFiscal' => $documento_fiscal['documento_numero'],
                                'cKardexNumeroSerieFiscal' => $documento_fiscal['documento_serie'],
                                'cKardexEstado' => PEDIDO_GENERADO,
                                'stockUManterior' => $total_unidades_minimas_viejas,
                                'stockUMactual' => $suma_cantidades,
                                'cKardexCliente' => $venta_cabecera['id_cliente'],
                                'cKardexProveedor' => null,
                            );

                            array_push($kardex, $item_kardex);
                        }

                    }
                }
            }

            //   var_dump($kardex);
            if (sizeof($kardex) > 0) {
                $this->db->insert_batch('kardex_fiscal', $kardex);
            }


        }
    }

    function record_sort($records, $field, $reverse = false)
    {
        $hash = array();
        foreach ($records as $record) {
            $keys = $record[$field];
            if (isset($hash[$keys])) {
                $hash[$keys] = $record;
            } else {
                $hash[$keys] = $record;
            }
        }

        ($reverse) ? krsort($hash) : ksort($hash);

        $records = array();
        foreach ($hash as $record) {
            $records[] = $record;
        }

        return $records;
    }


//ESTE METODO SOLAENTE SE USA EN DEVOLUCION DEVENTAS O EDICION/DEVOLUCION DE PEDIDOS . OJO
    function actualizar_venta($venta_cabecera, $detalle = false, $montoboletas)
    {
        $this->db->trans_start(true);
        $this->db->trans_begin();

        $venta_id = $venta_cabecera['venta_id'];

        $venta = array(
            // 'fecha' => $venta_cabecera['fecha'],
            // 'id_vendedor' => $venta_cabecera['id_vendedor'],
            'local_id' => $venta_cabecera['local_id'],
            'subtotal' => $venta_cabecera['subtotal'],
            'total_impuesto' => $venta_cabecera['total_impuesto'],
            'total' => $venta_cabecera['total'],
            //  'pagado' => $venta_cabecera['importe'],

        );

        if ($venta_cabecera['devolver'] != 'true') {

            if ($venta_cabecera['venta_tipo'] == 'ENTREGA') {
                //actualizo el detalle del consolidado monto cobrdo en liqudiacion
                $this->db->where('pedido_id', $venta_id);
                $this->db->update('consolidado_detalle', array('liquidacion_monto_cobrado' => $venta_cabecera['importe']));

                if ($venta_cabecera['devolver'] == 'false') {
                    $venta['pagado'] = $venta_cabecera['importe'];
                }
            } else {
                $venta['pagado'] = $venta_cabecera['importe'];
            }
        }

        if (!empty($venta_cabecera['id_cliente'])) {
            $venta['id_cliente'] = $venta_cabecera['id_cliente'];
        }
        if (!empty($venta_cabecera['venta_status'])) {
            $venta['venta_status'] = $venta_cabecera['venta_status'];
        }
        if (!empty($venta_cabecera['condicion_pago'])) {
            $venta['condicion_pago'] = $venta_cabecera['condicion_pago'];
        }

        $this->db->where('venta_id', $venta_cabecera['venta_id']);
        $this->db->update('venta', $venta);
        if ($venta_cabecera['venta_status'] == PEDIDO_GENERADO && $venta_cabecera['devolver'] != 'true') {
            $this->db->where('venta_id', $venta_cabecera['venta_id']);
            $this->db->update('venta_backup', $venta);
        }


        $sql_venta = $this->db->query("SELECT * FROM venta_backup where venta_id='$venta_id' ");

        $query_venta = $sql_venta->result_array();


        ///// se actualiza el backup de la tabla venta
        if (isset($venta_cabecera['estatus_actual']) and
            $venta_cabecera['estatus_actual'] == PEDIDO_DEVUELTO and $venta_cabecera['venta_status'] != PEDIDO_DEVUELTO
        ) {


            foreach ($query_venta as $row) {

                $venta_backup = array(
                    'id_cliente' => $row['id_cliente'],
                    'fecha' => $row['fecha'],
                    // 'id_vendedor' => $row['id_vendedor'],
                    'local_id' => $row['local_id'],
                    'subtotal' => $row['subtotal'],
                    'numero_documento' => $row['numero_documento'],
                    'venta_status' => $venta_cabecera['venta_status'],
                    'condicion_pago' => $row['condicion_pago'],
                    'total_impuesto' => $row['total_impuesto'],
                    'total' => $row['total'],
                    'pagado' => $row['pagado'],
                    'venta_tipo' => $row['venta_tipo']

                );
                $this->db->where('venta_id', $venta_id);
                $this->db->update('venta', $venta_backup);
            }


            $this->db->where('id_venta', $venta_id);
            $this->db->delete('detalle_venta');


            $sql_detalle = $this->db->query("SELECT * FROM detalle_venta_backup where id_venta='$venta_id' ");

            $query_detalle_venta_backup = $sql_detalle->result_array();

            foreach ($query_detalle_venta_backup as $row) {

                $detalle_item = array(
                    'id_venta' => $venta_id,
                    'id_producto' => $row['id_producto'],
                    'precio' => $row['precio'],
                    'precio_sugerido' => $row['precio_sugerido'],
                    'cantidad' => $row['cantidad'],
                    'unidad_medida' => $row['unidad_medida'],
                    'detalle_costo_promedio' => $row['detalle_costo_promedio'],
                    'detalle_utilidad' => $row['detalle_utilidad'],
                    'detalle_importe' => $row['detalle_importe'],
                    'bono' => $row['bono']
                );
                $this->db->insert('detalle_venta', $detalle_item);
            }
        }

        if ($detalle != false) {


            // Documento Fiscal
            //  if ($venta_cabecera['tipo_documento'] == 'FACTURA' || $venta_cabecera['tipo_documento'] == 'BOLETA DE VENTA') {


            // BORO DCUEMNTO DETALL
            $this->db->where('id_venta', $venta_id);
            $this->db->delete('documento_detalle');
            //BORRO DOCUMENTO FISCAL
            $this->db->where('venta_id', $venta_id);
            $this->db->delete('documento_fiscal');
            //BORORO KARDEX
            $this->db->where('cKardexIdOperacion', $venta_id);
            $this->db->where('cKardexOperacion', VENTA);
            //   $this->db->delete('kardex_fiscal');

            $venta_cabecera['tipo_documento'] = $query_venta[0]['tipo_doc_fiscal'];
            $this->setDocumentoDetalle($venta_id, $venta_cabecera, $detalle, $montoboletas, true);
            //}

            /**********QUITO DEL INVETARIO TODOS LOS ITEMS DE LA VENTA***********/

            $sql_detalle = $this->db->query("SELECT * FROM detalle_venta
            JOIN producto ON producto.producto_id=detalle_venta.id_producto
            LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
            LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
            JOIN venta ON venta.`venta_id`=detalle_venta.`id_venta`
             LEFT JOIN documento_venta ON documento_venta.id_tipo_documento=venta.numero_documento
            LEFT JOIN documento_fiscal ON documento_fiscal.venta_id=venta.venta_id
            WHERE detalle_venta.id_venta='$venta_id' group by detalle_venta.id_detalle");

            $query_detalle_venta = $sql_detalle->result_array();

            $countQuery = count($query_detalle_venta);

            for ($i = 0; $i < $countQuery; $i++) {
                $local = $query_detalle_venta[$i]['local_id'];
                $unidad_maxima = $query_detalle_venta[$i]['unidades'];

                $producto_id = $query_detalle_venta[$i]['producto_id'];
                $unidad = $query_detalle_venta[$i]['unidad_medida'];
                $cantidad_compra = $query_detalle_venta[$i]['cantidad'];

                $sql_inventario = $this->db->query("SELECT id_inventario, cantidad, fraccion
					FROM inventario where id_producto='$producto_id' and id_local='$local'");
                $inventario_existente = $sql_inventario->row_array();

                $id_inventario = $inventario_existente['id_inventario'];
                $cantidad_vieja = $inventario_existente['cantidad'];
                $fraccion_vieja = $inventario_existente['fraccion'];

                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$producto_id' ORDER BY orden");

                $unidades_producto = $query->result_array();

                foreach ($unidades_producto as $row) {
                    if ($row['id_unidad'] == $unidad) {
                        $unidad_form = $row;
                    }
                }

                $unidad_minima = $unidades_producto[count($unidades_producto) - 1];

                if ($cantidad_vieja >= 1) {
                    $unidades_minimas_inventario = ($cantidad_vieja * $query_detalle_venta[$i]['unidades']) + $fraccion_vieja;
                } else {
                    $unidades_minimas_inventario = $fraccion_vieja;
                }

                $unidades_minimas_detalle = $unidad_form['unidades'] * $cantidad_compra;


                // comparar contra la cantidad vieja paa saber si debo sumar o restar


                $suma = $unidades_minimas_inventario + $unidades_minimas_detalle;

                $cont = 0;

                while ($suma >= $unidad_maxima) {
                    $cont++;
                    $suma = $suma - $unidad_maxima;
                }

                if ($cont < 1) {
                    $cantidad_nueva = 0;
                    $fraccion_nueva = $suma;
                } else {
                    $cantidad_nueva = $cont;
                    $fraccion_nueva = $suma;
                }

                $inventario_nuevo = array(
                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva
                );

                $where = array('id_inventario' => $id_inventario);
                $this->update_inventario($inventario_nuevo, $where);


                //  if ($venta_cabecera['devolver']=='true') {
                /*******Preparo la data para insertar en el kardex********/


                $estadonuevo = 'SALIDA POR ';
                if ($venta_cabecera['devolver'] == 'true') {
                    $estadonuevo .= ($query_detalle_venta[$i]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                } else {
                    $estadonuevo .= ($query_detalle_venta[$i]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                }


                /*  $item_kardex = array(
                      'dkardexFecha' => date('Y-m-d H:i:s:u'),
                      'ckardexReferencia' => $estadonuevo,
                      'cKardexProducto' => $query_detalle_venta[$i]['producto_id'],
                      'nKardexCantidad' => $query_detalle_venta[$i]['cantidad'],
                      'nKardexPrecioUnitario' => $query_detalle_venta[$i]['precio'],
                      'nKardexPrecioTotal' => $query_detalle_venta[$i]['detalle_importe'],
                      'cKardexUsuario' => $query_detalle_venta[0]['id_vendedor'],
                      'cKardexOperacion' => VENTA,
                      'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                      'cKardexAlmacen' => $query_detalle_venta[$i]['local_id'],
                      'cKardexTipo' => ENTRADA,
                      'cKardexIdOperacion' => $query_detalle_venta[$i]['id_venta'],
                      'cKardexTipoDocumento' => NOTA_ENTREGA,
                      'cKardexNumeroDocumento' => $query_detalle_venta[$i]['documento_Numero'],
                      'cKardexNumeroSerie' => $query_detalle_venta[$i]['documento_Serie'],
                      'cKardexEstado' => $query_detalle_venta[$i]['venta_status'],
                      'stockUManterior' => $unidades_minimas_inventario,
                      'stockUMactual' => $suma,
                      'cKardexTipoDocumentoFiscal' => $query_detalle_venta[$i]['documento_tipo'],
                      'cKardexNumeroDocumentoFiscal' => $query_detalle_venta[$i]['documento_numero'],
                      'cKardexNumeroSerieFiscal' => $query_detalle_venta[$i]['documento_serie'],
                      'cKardexCliente' => $query_detalle_venta[$i]['id_cliente'],
                      'cKardexProveedor' => null,
                  );
    */

                //$this->db->insert('kardex', $item_kardex);
                // }
            }


            $this->db->where('id_venta', $venta_id);
            $this->db->delete('detalle_venta');
            $venta_backup_items = array();
            /***********COMIENZO A SUMAR EL INVENTARIO DE LA VENTA***************/
            foreach ($detalle as $row) {
                $cantidad_venta = $row->cantidad;
                $unidad_medida_venta = $row->unidad_medida;
                $id_producto = $row->id_producto;
                $precio = $row->precio;
                $precio_sugerido = $row->precio_sugerido;
                $importe = $row->detalle_importe;
                $bono = isset($row->bono) ? $row->bono : false;

                $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
			   FROM inventario where id_producto=' . $id_producto . ' and id_local=' . $venta_cabecera['local_id']);
                $inventario_existente = $query->row_array();
                $cantidad_vieja = 0;
                $fraccion_vieja = 0;

                if (isset($inventario_existente['cantidad'])) {
                    $cantidad_vieja = $inventario_existente['cantidad'];
                }
                if (isset($inventario_existente['fraccion'])) {
                    $fraccion_vieja = $inventario_existente['fraccion'];
                }

                // CALCULOS DE UNDIAD DE MEDIDA
                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

                $unidades_producto = $query->result_array();

                $unidad_maxima = $unidades_producto[0];
                $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
                $unidad_form = 0;
                foreach ($unidades_producto as $um) {
                    if ($um['id_unidad'] == $unidad_medida_venta) {
                        $unidad_form = $um;
                    }
                }

                $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;

                if ($fraccion_vieja < $total_unidades_minimas) {
                    $suma_cantidades = $total_unidades_minimas - $fraccion_vieja;
                } else {
                    $suma_cantidades = $fraccion_vieja - $total_unidades_minimas;
                }

                if ($suma_cantidades >= $unidad_maxima['unidades']) {
                    // echo "0";
                    $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                    $cantidad_nueva = $cantidad_vieja - intval($resultado_division);
                    $resto_division = $suma_cantidades % $unidad_maxima['unidades'];
                    $fraccion_nueva = $resto_division;
                } else {
                    //echo "1";
                    $cantidad_nueva = $cantidad_vieja;
                    $fraccion_nueva = +$suma_cantidades;
                }

                if ($unidad_medida_venta == $unidad_maxima['id_unidad']) {
                    //echo "3";
                    $cantidad_nueva = $cantidad_vieja - $cantidad_venta;
                    $fraccion_nueva = $fraccion_vieja;
                }

                if ($unidad_medida_venta == $unidad_minima['id_unidad']) {

                    //   echo "2";
                    if ($suma_cantidades >= $unidad_maxima['unidades']) {
                        //echo "entro";
                        $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                        // echo $resultado_division ." - ";
                        //echo $cantidad_vieja;
                        $cantidad_nueva = $cantidad_vieja - intval($resultado_division);
                        $resto_division = $suma_cantidades % $unidad_maxima['unidades'];
                        $fraccion_nueva = $resto_division;
                    } else {
                        if ($cantidad_vieja > 0) {

                            $cantidad_nueva = $cantidad_vieja;
                            $fraccion_nueva = $suma_cantidades;
                        } else {
                            if (count($unidades_producto) > 1) {
                                $cantidad_nueva = 0;
                                $fraccion_nueva = $cantidad_venta;
                            } else {
                                $cantidad_nueva = $cantidad_venta;
                                $fraccion_nueva = 0;
                            }
                        }
                    }
                }

                /********************CALCULAO DE UTILIDAD****/


                ///busco el costo unitario del producto

                $query_costo_u = $this->db->query("select producto_id,  costo_unitario from producto
                  WHERE producto_id=" . $id_producto . " ");
                $costo_unitario = $query_costo_u->row_array();

                if ($costo_unitario['costo_unitario'] == null or $costo_unitario['costo_unitario'] == '0') {

                    $query_compra = $this->db->query("SELECT detalleingreso.*, ingreso.fecha_registro, unidades_has_producto.* FROM detalleingreso
                    JOIN ingreso ON ingreso.id_ingreso=detalleingreso.id_ingreso
                    JOIN unidades ON unidades.id_unidad=detalleingreso.unidad_medida
                    JOIN unidades_has_producto ON unidades_has_producto.id_unidad=detalleingreso.unidad_medida
                    AND unidades_has_producto.producto_id=detalleingreso.id_producto
                    WHERE detalleingreso.id_producto=" . $id_producto . " AND  fecha_registro=(SELECT MAX(fecha_registro) FROM ingreso
                    JOIN detalleingreso ON detalleingreso.id_ingreso=ingreso.id_ingreso WHERE detalleingreso.id_producto=" . $id_producto . ")  ");

                    $result_ingreso = $query_compra->result_array();
                    if (count($result_ingreso) > 0) {

                        $calcular_costo_u = ($result_ingreso[0]['precio'] / $result_ingreso[0]['unidades']) * $unidad_maxima['unidades'];
                        $promedio_compra = ($calcular_costo_u / $unidad_maxima['unidades']) * $unidad_form['unidades'];
                    } else {
                        $promedio_compra = 0;
                    }

                } else {
                    $promedio_compra = ($costo_unitario['costo_unitario'] / $unidad_maxima['unidades']) * $unidad_form['unidades'];

                }

                $utilidad = ($precio - $promedio_compra) * $cantidad_venta;

                $detalle_item = array(
                    'id_venta' => $venta_id,
                    'id_producto' => $id_producto,
                    'precio' => $precio,
                    'precio_sugerido' => $precio_sugerido,
                    'cantidad' => $cantidad_venta,
                    'unidad_medida' => $unidad_medida_venta,
                    'detalle_costo_promedio' => $promedio_compra,
                    'detalle_utilidad' => $utilidad,
                    'detalle_importe' => $importe,
                    'bono' => $bono == 'true' ? 1 : 0,

                );

                $this->db->insert('detalle_venta', $detalle_item);
                $detalle_id_inserte = $this->db->insert_id();

                if (count($inventario_existente) > 0) {


                    $inventario_nuevo = array(
                        'id_inventario' => $inventario_existente['id_inventario'],
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva
                    );
                    // Actualizar Inventario
                    //   echo $cantidad_nueva ."*";
                    //  echo $venta_cabecera['venta_status'];

                    if ($venta_cabecera['venta_status'] == COMPLETADO || $venta_cabecera['venta_status'] == PEDIDO_GENERADO || $venta_cabecera['venta_status'] == ESPERA || $venta_cabecera['venta_status'] == PEDIDO_DEVUELTO) {

                        // var_dump($inventario_nuevo);


                        $result_inv = $this->db->update('inventario', $inventario_nuevo, array('id_inventario' => $inventario_nuevo['id_inventario']));
                        // echo $result_inv;
                    }
                } else {
                    $inventario_nuevo = array(
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva
                    );


                    // Nuevo Inventario
                    if ($venta_cabecera['venta_status'] == COMPLETADO || $venta_cabecera['venta_status'] == PEDIDO_GENERADO || $venta_cabecera['venta_status'] == ESPERA || $venta_cabecera['venta_status'] == ESPERA) {
                        $this->db->insert('inventario', $inventario_nuevo);
                    }
                }


                // if ($venta_cabecera['devolver']=='true') {
                /*******Preparo la data para insertar en el kardex********/


                $estadonuevo = 'ENTRADA POR ';
                if ($venta_cabecera['devolver'] == 'true') {
                    $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                } else {
                    $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                }


                $sql_detalle_bk = $this->db->query("SELECT * FROM detalle_venta_backup
            WHERE detalle_venta_backup.id_producto='$id_producto' and detalle_venta_backup.id_venta=" . $venta_id);

                $query_detalle_venta_bk = $sql_detalle_bk->row_array();

                //var_dump($query_detalle_venta_bk);
                // echo $query_detalle_venta_bk['cantidad'];
                // echo $row->cantidad;

                // echo $query_detalle_venta_bk['cantidad'] - $row->cantidad;
                // echo $query_detalle_venta_bk['cantidad']." -- ";
                if (isset($query_detalle_venta_bk['cantidad'])) {
                    if ($query_detalle_venta_bk['cantidad'] - $row->cantidad > 0) {
                        $item_kardex = array(
                            'dkardexFecha' => date('Y-m-d H:i:s:u'),
                            'ckardexReferencia' => $estadonuevo,
                            'cKardexProducto' => $row->id_producto,
                            'nKardexCantidad' => $query_detalle_venta_bk['cantidad'] - $row->cantidad,
                            'nKardexPrecioUnitario' => $row->precio,
                            'nKardexPrecioTotal' => $row->precio * ($query_detalle_venta_bk['cantidad'] - $row->cantidad),
                            'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                            'cKardexOperacion' => VENTA,
                            'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                            'cKardexAlmacen' => $venta_cabecera['local_id'],
                            'cKardexTipo' => ENTRADA,
                            'cKardexIdOperacion' => $venta_id,
                            'cKardexTipoDocumento' => NOTA_ENTREGA,
                            'cKardexNumeroDocumento' => $query_detalle_venta[0]['documento_Numero'],
                            'cKardexNumeroSerie' => $query_detalle_venta[0]['documento_Serie'],
                            'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                            'stockUManterior' => $unidades_minimas_inventario,
                            'stockUMactual' => $suma,
                            'cKardexTipoDocumentoFiscal' => $query_detalle_venta[0]['documento_tipo'],
                            'cKardexNumeroDocumentoFiscal' => $query_detalle_venta[0]['documento_numero'],
                            'cKardexNumeroSerieFiscal' => $query_detalle_venta[0]['documento_serie'],
                            'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                            'cKardexProveedor' => null,
                        );

                        $this->db->insert('kardex', $item_kardex);
                    } else {

                        $estadonuevo = 'SALIDA POR ';
                        if ($venta_cabecera['devolver'] == 'true') {
                            $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_DEVUOLUCION : VENTA_DEVOLUCION;
                        } else {
                            $estadonuevo .= ($query_detalle_venta[0]['venta_tipo'] == VENTA_ENTREGA) ? PEDIDO_EDICION : VENTA_EDICION;
                        }

                        $item_kardex = array(
                            'dkardexFecha' => date('Y-m-d H:i:s:u'),
                            'ckardexReferencia' => $estadonuevo,
                            'cKardexProducto' => $row->id_producto,
                            'nKardexCantidad' => $row->cantidad - $query_detalle_venta_bk['cantidad'],
                            'nKardexPrecioUnitario' => $row->precio,
                            'nKardexPrecioTotal' => $row->precio * ($row->cantidad - $query_detalle_venta_bk['cantidad']),
                            'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                            'cKardexOperacion' => VENTA,
                            'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                            'cKardexAlmacen' => $venta_cabecera['local_id'],
                            'cKardexTipo' => SALIDA,
                            'cKardexIdOperacion' => $venta_id,
                            'cKardexTipoDocumento' => NOTA_ENTREGA,
                            'cKardexNumeroDocumento' => $query_detalle_venta[0]['documento_Numero'],
                            'cKardexNumeroSerie' => $query_detalle_venta[0]['documento_Serie'],
                            'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                            'stockUManterior' => $unidades_minimas_inventario,
                            'stockUMactual' => $suma,
                            'cKardexTipoDocumentoFiscal' => $query_detalle_venta[0]['documento_tipo'],
                            'cKardexNumeroDocumentoFiscal' => $query_detalle_venta[0]['documento_numero'],
                            'cKardexNumeroSerieFiscal' => $query_detalle_venta[0]['documento_serie'],
                            'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                            'cKardexProveedor' => null,
                        );

                        $this->db->insert('kardex', $item_kardex);
                    }
                } else {

                    // CALCULOS DE UNDIAD DE MEDIDA
                    $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

                    $unidades_producto = $query->result_array();


                    $unidad_maxima = $unidades_producto[0];
                    $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
                    $unidad_form = 0;
                    foreach ($unidades_producto as $up) {
                        if ($up['id_unidad'] == $unidad_medida_venta) {
                            $unidad_form = $up;
                        }
                    }

                    $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
                    $total_unidades_minimas_viejas = ($unidad_maxima['unidades'] * $cantidad_vieja) + $fraccion_vieja;

                    $suma_cantidades = $total_unidades_minimas_viejas - $total_unidades_minimas;

                    $item_kardex = array(
                        'dkardexFecha' => date('Y-m-d H:i:s:u'),
                        'ckardexReferencia' => $estadonuevo,
                        'cKardexProducto' => $row->id_producto,
                        'nKardexCantidad' => $row->cantidad,
                        'nKardexPrecioUnitario' => $row->precio,
                        'nKardexPrecioTotal' => $row->precio * ($row->cantidad),
                        'cKardexUsuario' => $venta_cabecera['id_vendedor'],
                        'cKardexOperacion' => VENTA,
                        'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                        'cKardexAlmacen' => $venta_cabecera['local_id'],
                        'cKardexTipo' => SALIDA,
                        'cKardexIdOperacion' => $venta_id,
                        'cKardexTipoDocumento' => NOTA_ENTREGA,
                        'cKardexNumeroDocumento' => $query_detalle_venta[0]['documento_Numero'],
                        'cKardexNumeroSerie' => $query_detalle_venta[0]['documento_Serie'],
                        'cKardexEstado' => $query_detalle_venta[0]['venta_status'],
                        'stockUManterior' => $unidades_minimas_inventario,
                        'stockUMactual' => $suma_cantidades,
                        'cKardexTipoDocumentoFiscal' => $query_detalle_venta[0]['documento_tipo'],
                        'cKardexNumeroDocumentoFiscal' => $query_detalle_venta[0]['documento_numero'],
                        'cKardexNumeroSerieFiscal' => $query_detalle_venta[0]['documento_serie'],
                        'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                        'cKardexProveedor' => null,
                    );
                    $this->db->insert('kardex', $item_kardex);
                }


                if ($venta_cabecera['venta_status'] == PEDIDO_GENERADO && $venta_cabecera['devolver'] != 'true') {
                    // $this->db->insert('detalle_venta_backup', $detalle_item);
                    array_push($venta_backup_items, $detalle_item);
                }


            }

        }


        /************************************/


        if ($venta_cabecera['venta_status'] == PEDIDO_GENERADO && $venta_cabecera['devolver'] != 'true') {


            $this->db->where('id_venta', $venta_id);
            $this->db->delete('detalle_venta_backup');
            $this->db->insert_batch('detalle_venta_backup', $venta_backup_items);
        }


        /**Credito o contado*/
        $this->updateVentaTipo($venta_cabecera);


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            return false;
        } else {
            $this->db->trans_commit();
            return $venta_id;
        }

        $this->db->trans_off();
    }


    function updateVentaTipo($venta_cabecera)
    {

        $credito = array(
            'id_venta' => $venta_cabecera['venta_id'],
            'dec_credito_montodeuda' => $venta_cabecera['total'],
            'var_credito_estado' => CREDITO_DEBE,
            'dec_credito_montodebito' => 0.0
        );
        $credito_lista = array();


        if ($venta_cabecera['venta_tipo'] == VENTA_CAJA OR $venta_cabecera['venta_status'] == PEDIDO_DEVUELTO) {

            $credito_lista[0]['totaldeuda'] = $venta_cabecera['total'];


            if ($venta_cabecera['importe'] < $venta_cabecera['total']) {
                $credito_lista[0]['cuota'] = $venta_cabecera['importe'];
                $credito_lista[0]['id_venta'] = $venta_cabecera['venta_id'];


            } else {

                $credito_lista[0]['cuota'] = $venta_cabecera['importe'];
                $credito_lista[0]['id_venta'] = $venta_cabecera['venta_id'];


            }
            if ($venta_cabecera['venta_status'] == PEDIDO_DEVUELTO) {
                $credito_lista[0]['cuota'] = 0;
            }

            $credito_lista[0] = (object)$credito_lista[0];

        }


        if ($venta_cabecera['diascondicionpagoinput'] < 1) {

            if ($venta_cabecera['devolver'] == 'false') {

                $this->db->where('credito_id', $venta_cabecera['venta_id']);
                $this->db->delete('historial_pagos_clientes', array('credito_id' => $venta_cabecera['venta_id']));
                /*******************************************************/////

                $this->db->delete('credito', array('id_venta' => $venta_cabecera['venta_id']));

            }

            if ($venta_cabecera['venta_tipo'] == VENTA_CAJA OR $venta_cabecera['venta_status'] == PEDIDO_DEVUELTO || ($venta_cabecera['venta_tipo'] == VENTA_ENTREGA && $venta_cabecera['devolver'] == 'false')) {

                $select_credito = $this->db->select('*')->from('credito')->where('id_venta', $venta_cabecera['venta_id'])->get()->row_array();

                if (count($select_credito) > 0) {
                    //  echo "actualizao el creido";
                    $this->updateCreditos($credito_lista);
                } else {
                    $this->insertCredito($credito);
                }
            }


        } else {


            $select_credito = $this->db->select('*')->from('credito')->where('id_venta', $venta_cabecera['venta_id'])->get()->row_array();
            if (count($select_credito) > 0) {
                if ($venta_cabecera['venta_tipo'] == VENTA_CAJA OR $venta_cabecera['venta_status'] == PEDIDO_DEVUELTO) {
                    $this->updateCreditos($credito_lista);
                } else {
                    $this->db->where('id_venta', $venta_cabecera['venta_id']);
                    $this->db->update('credito', $credito);
                }
            } else {

                $this->insertCredito($credito);
            }

        }
    }


    function insertCredito($credito)
    {
        $this->db->trans_start(true);
        $this->db->trans_begin();
        $this->db->insert('credito', $credito);


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return true;
        }

        $this->db->trans_off();
    }

//Lo uso para actulizar la tabla credito arbitrariamente
    function actualizarCredito($values, $condition)
    {
        $this->db->trans_start(true);
        $this->db->trans_begin();

        $this->db->where($condition);
        $this->db->update('credito', $values);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return true;
        }

        $this->db->trans_off();
    }

    function updateCreditos($lista_creditos, $nota_credito = false)
    {
        $this->db->trans_start(true);
        $this->db->trans_begin();
        $where = array('id_venta' => $lista_creditos[0]->id_venta);

        if ($nota_credito == false) {
            foreach ($lista_creditos as $row) {
                $where = array('id_venta' => $row->id_venta);
                $queryCredito = $this->getCredito($where);
                $total_venta = $queryCredito['dec_credito_montodeuda'];

                if (isset($row->totaldeuda)) {
                    $credito = array('dec_credito_montodeuda' => $row->totaldeuda);
                }

                /* if (($queryCredito['dec_credito_montodebito'] + $row->cuota) >= $total_venta) {

                     $credito['var_credito_estado'] = CREDITO_CANCELADO;
                     $credito['dec_credito_montodebito'] = $queryCredito['dec_credito_montodebito'] + $row->cuota;

                 } else*/
                if (($queryCredito['dec_credito_montodeuda'] - $queryCredito['dec_credito_montodebito']) > 0) {

                    $credito['var_credito_estado'] = CREDITO_ACUENTA;
                    $credito['dec_credito_montodebito'] = $queryCredito['dec_credito_montodebito'] + $row->cuota;

                } else {

                    $credito['dec_credito_montodebito'] = $queryCredito['dec_credito_montodebito'] + $row->cuota;


                }

            }
        } else {

            $credito = array(
                'var_credito_estado' => CREDITO_NOTACREDITO
            );
        }

        $this->db->where($where);
        $this->db->update('credito', $credito);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return true;
        }

        $this->db->trans_off();
    }

    function getCredito($where)
    {
        $this->db->where($where);
        $query = $this->db->get('credito');
        return $query->row_array();
    }

    function restaurar_venta($id, $campos)
    {
        if (isset($campos['estatus_actual']) and $campos['estatus_actual'] == PEDIDO_DEVUELTO and $campos['venta_status'] != PEDIDO_DEVUELTO) {

            $sql_venta = $this->db->query("SELECT * FROM venta_backup where venta_id='$id' ");

            $query_venta = $sql_venta->result_array();

            foreach ($query_venta as $row) {

                $venta_backup = array(
                    'id_cliente' => $row['id_cliente'],
                    'fecha' => $row['fecha'],
                    'id_vendedor' => $row['id_vendedor'],
                    'local_id' => $row['local_id'],
                    'subtotal' => $row['subtotal'],
                    'numero_documento' => $row['numero_documento'],
                    'venta_status' => $campos['venta_status'],
                    'condicion_pago' => $row['condicion_pago'],
                    'total_impuesto' => $row['total_impuesto'],
                    'total' => $row['total'],
                    'pagado' => $row['pagado'],
                    'venta_tipo' => $row['venta_tipo']

                );
                $this->db->where('venta_id', $id);
                $this->db->update('venta', $venta_backup);
            }


            $this->db->where('id_venta', $id);
            $this->db->delete('detalle_venta');

            $sql_detalle = $this->db->query("SELECT * FROM detalle_venta_backup where id_venta='$id' ");

            $query_detalle_venta_backup = $sql_detalle->result_array();

            foreach ($query_detalle_venta_backup as $row) {

                $detalle_item = array(
                    'id_venta' => $id,
                    'id_producto' => $row['id_producto'],
                    'precio' => $row['precio'],
                    'precio_sugerido' => $row['precio_sugerido'],
                    'cantidad' => $row['cantidad'],
                    'unidad_medida' => $row['unidad_medida'],
                    'detalle_costo_promedio' => $row['detalle_costo_promedio'],
                    'detalle_utilidad' => $row['detalle_utilidad'],
                    'detalle_importe' => $row['detalle_importe'],
                    'bono' => $row['bono']
                );
                $this->db->insert('detalle_venta', $detalle_item);
            }
        }
    }

    function devolver_stock($id, $campos, $status = false)
    {
        $this->db->trans_start(true);
        $this->db->trans_begin();

        $data = array();


        $sql_detalle_venta = $this->db->query("SELECT * FROM detalle_venta
JOIN producto ON producto.producto_id=detalle_venta.id_producto
LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
JOIN venta ON venta.`venta_id`=detalle_venta.`id_venta` join documento_venta on documento_venta.id_tipo_documento=venta.numero_documento
 left join documento_fiscal on documento_fiscal.venta_id=venta.venta_id
WHERE detalle_venta.id_venta='$id' group by detalle_venta.id_detalle");

        $query_detalle_venta = $sql_detalle_venta->result_array();

        $kardex = array();
        /*************COMIENZO A DEVOLVER EL STOCK**********/
        for ($i = 0; $i < count($query_detalle_venta); $i++) {
            $local = $query_detalle_venta[$i]['local_id'];
            $unidad_maxima = $query_detalle_venta[$i]['unidades'];
            $unidad_minima = $query_detalle_venta[sizeof($query_detalle_venta) - 1];

            $producto_id = $query_detalle_venta[$i]['producto_id'];
            $unidad = $query_detalle_venta[$i]['unidad_medida'];
            $cantidad_compra = $query_detalle_venta[$i]['cantidad'];

            $sql_inventario = $this->db->query("SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto='$producto_id' and id_local='$local'");
            $inventario_existente = $sql_inventario->row_array();
            if (count($inventario_existente) > 0) {
                $id_inventario = $inventario_existente['id_inventario'];
                $cantidad_vieja = $inventario_existente['cantidad'];
                $fraccion_vieja = $inventario_existente['fraccion'];
            } else {
                $cantidad_vieja = 0;
                $fraccion_vieja = 0;
            }


            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$producto_id' ORDER BY orden");

            $unidades_producto = $query->result_array();

            foreach ($unidades_producto as $row) {
                if ($row['id_unidad'] == $unidad) {
                    $unidad_form = $row;
                }
            }

            if ($cantidad_vieja >= 1) {
                $unidades_minimas_inventario = ($cantidad_vieja * $query_detalle_venta[$i]['unidades']) + $fraccion_vieja;
            } else {
                $unidades_minimas_inventario = $fraccion_vieja;
            }

            $unidades_minimas_detalle = $unidad_form['unidades'] * $cantidad_compra;

            $suma = $unidades_minimas_inventario + $unidades_minimas_detalle;

            $cont = 0;
            while ($suma >= $unidad_maxima) {
                $cont++;
                $suma = $suma - $unidad_maxima;
            }

            if ($cont < 1) {
                $cantidad_nueva = 0;
                $fraccion_nueva = $suma;
            } else {
                $cantidad_nueva = $cont;
                $fraccion_nueva = $suma;
            }

            $inventario_nuevo = array(
                'cantidad' => $cantidad_nueva,
                'fraccion' => $fraccion_nueva
            );


            $where = array('id_inventario' => $id_inventario);
            $this->update_inventario($inventario_nuevo, $where);


            /*******Preparo la data para insertar en el kardex********/
            $documento_fiscal = $this->db->get_where('documento_fiscal', array('venta_id' => $query_detalle_venta[$i]['id_venta']));
            $documento_fiscal = $documento_fiscal->row_array();

            $item_kardex = array(
                'dkardexFecha' => date('Y-m-d H:i:s:u'),
                'ckardexReferencia' => ($status == PEDIDO_ANULADO) ? 'VENTA ANULADA' : 'VENTA RECHAZADA',
                'cKardexProducto' => $query_detalle_venta[$i]['producto_id'],
                'nKardexCantidad' => $query_detalle_venta[$i]['cantidad'],
                'nKardexPrecioUnitario' => $query_detalle_venta[$i]['precio'],
                'nKardexPrecioTotal' => $query_detalle_venta[$i]['detalle_importe'],
                'cKardexUsuario' => $query_detalle_venta[$i]['id_vendedor'],
                'cKardexOperacion' => VENTA,
                'cKardexUnidadMedida' => $unidad_minima['id_unidad'],
                'cKardexAlmacen' => $query_detalle_venta[$i]['local_id'],
                'cKardexTipo' => ENTRADA,
                'cKardexIdOperacion' => $query_detalle_venta[$i]['id_venta'],
                'cKardexTipoDocumento' => NOTA_ENTREGA,
                'cKardexNumeroDocumento' => $query_detalle_venta[$i]['documento_Numero'],
                'cKardexNumeroSerie' => $query_detalle_venta[$i]['documento_Serie'],
                'cKardexEstado' => $status,
                'stockUManterior' => $unidades_minimas_inventario,
                'stockUMactual' => $suma,
                'cKardexTipoDocumentoFiscal' => isset($documento_fiscal['documento_tipo']) ? $documento_fiscal['documento_tipo'] : null,
                'cKardexNumeroDocumentoFiscal' => isset($documento_fiscal['documento_numero']) ? $documento_fiscal['documento_numero'] : null,
                'cKardexNumeroSerieFiscal' => isset($documento_fiscal['documento_serie']) ? $documento_fiscal['documento_serie'] : null,
                'cKardexCliente' => $query_detalle_venta[0]['id_cliente'],
                'cKardexProveedor' => null,
            );

            array_push($kardex, $item_kardex);
        }

        if (count($query_detalle_venta) > 0) {
            $this->db->insert_batch('kardex', $kardex);
        }


        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {

            if ($status == PEDIDO_ANULADO) {
                $arreglo = array(
                    'id_venta' => $id,
                    'var_venanular_descripcion' => $campos['motivo'],
                    'nUsuCodigo' => $campos['nUsuCodigo']

                );
                $this->db->insert('venta_anular', $arreglo);

            }

            $condicion = array('venta_id' => $id);
            $campos2 = array('venta_status' => $status);

            $data['resultado'] = $this->update_venta($condicion, $campos2);

            $venta_status['fecha'] = date('Y-m-d h:m:s');
            $venta_status['venta_id'] = $id;
            $venta_status['vendedor_id'] = $campos['nUsuCodigo'];
            $venta_status['estatus'] = $status;
            $this->db->insert('venta_estatus', $venta_status);

            return true;

        }

        $this->db->trans_off();
    }

    function get_credito_by_venta($venta)
    {

        $this->db->select('*, consolidado_detalle.confirmacion_monto_cobrado_caja,consolidado_detalle.confirmacion_monto_cobrado_bancos, venta.pagado, venta.total');
        $this->db->from('credito');
        $this->db->join('consolidado_detalle', 'consolidado_detalle.pedido_id=credito.id_venta', 'left');
        $this->db->join('venta', 'venta.venta_id=credito.id_venta');
        $this->db->where('id_venta', $venta);
        $query = $this->db->get();
        return $query->result_array();

    }


//TODO CAMBIAR TODOS LOS DEMAS POR ESTE METODO
    public
    function traer_by_mejorado($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
                               $nombre_or, $where_or,
                               $group = false,
                               $order = false, $retorno = false, $limit = false, $start = 0, $order_dir = false, $like = false, $where_custom)
    {
        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);
        }
        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    // for ($t = 0; $t < count($tipo_join); $t++) {

                    // if ($tipo_join[$t] != "") {

                    $this->db->join($join[$i], $campos_join[$i], $tipo_join[$i]);
                    //}

                    //}

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);
        }
        if ($like != false) {
            $this->db->like($like);
        }
        if ($where_custom != false) {
            $this->db->where($where_custom);
        }

        if ($nombre_in != false) {
            for ($i = 0; $i < count($nombre_in); $i++) {
                $this->db->where_in($nombre_in[$i], $where_in[$i]);
            }
        }

        if ($nombre_or != false) {
            for ($i = 0; $i < count($nombre_or); $i++) {
                $this->db->or_where($where_or);
            }
        }

        if ($limit != false) {
            $this->db->limit($limit, $start);
        }
        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order, $order_dir);
        }

        $query = $this->db->get();

        // echo $this->db->last_query();
        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }


    public
    function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
                      $nombre_or, $where_or,
                      $group = false,
                      $order = false, $retorno = false)
    {
        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);
        }
        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    // for ($t = 0; $t < count($tipo_join); $t++) {

///                        if ($tipo_join[$t] != "") {

                    $this->db->join($join[$i], $campos_join[$i], $tipo_join[$i]);
                    //                     }

                    // }

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);
        }

        if ($nombre_in != false) {
            for ($i = 0; $i < count($nombre_in); $i++) {
                $this->db->where_in($nombre_in[$i], $where_in[$i]);
            }
        }

        if ($nombre_or != false) {
            for ($i = 0; $i < count($nombre_or); $i++) {
                $this->db->or_where($where_or);
            }
        }

        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order);
        }

        $query = $this->db->get();
        //echo $this->db->last_query();

        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $query = $this->db->get('venta');
        return $query->row_array();
    }

    function getProductosZona($select, $where, $retorno, $group, $condicion2)
    {
        $this->db->select($select);
        $this->db->from('detalle_venta');
        $this->db->join('venta', 'venta.venta_id=detalle_venta.id_venta');
        $this->db->join('producto', 'producto.producto_id=detalle_venta.id_producto');
        $this->db->join('cliente', 'venta.id_cliente=cliente.id_cliente');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->join('unidades', 'unidades.id_unidad=detalle_venta.unidad_medida');
        $this->db->join('familia', 'familia.id_familia=producto.producto_familia', 'left');
        $this->db->join('lineas', 'lineas.id_linea=producto.producto_linea', 'left');
        $this->db->join('usuario_has_zona', 'usuario_has_zona.id_usuario=usuario.nUsuCodigo', 'left');
        $this->db->join('zonas', 'usuario_has_zona.id_zona=zonas.zona_id', 'left');
        $this->db->join('ciudades', 'ciudades.ciudad_id=zonas.ciudad_id');
        $this->db->join('grupos', 'grupos.id_grupo=producto.produto_grupo', 'left');
        $this->db->join('consolidado_detalle', 'consolidado_detalle.pedido_id=venta.venta_id', 'left');
        $this->db->join('consolidado_carga', 'consolidado_carga.consolidado_id=consolidado_detalle.consolidado_id', 'left');
        $this->db->where('zonas.status', 1);
        $this->db->where($condicion2);

        $this->db->where($where);


        if ($group != false) {
            $this->db->group_by($group);
        }

        $query = $this->db->get();

        //echo $this->db->last_query();
        if ($retorno == "RESULT") {
            return $query->result();
        }
    }

    function getProductosZonaX($select, $where, $retorno, $group)
    {
        $this->db->select($select);
        $this->db->from('detalle_venta');
        $this->db->join('venta', 'venta.venta_id=detalle_venta.id_venta');
        $this->db->join('producto', 'producto.producto_id=detalle_venta.id_producto');
        $this->db->join('cliente', 'venta.id_cliente=cliente.id_cliente');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $this->db->join('proveedor', 'producto.producto_proveedor=proveedor.id_proveedor');
        $this->db->join('unidades', 'unidades.id_unidad=detalle_venta.unidad_medida');
        $this->db->join('familia', 'familia.id_familia=producto.producto_familia');
        $this->db->join('lineas', 'lineas.id_linea=producto.producto_linea');
        $this->db->join('usuario_has_zona', 'usuario_has_zona.id_usuario=usuario.nUsuCodigo');
        $this->db->join('zonas', 'usuario_has_zona.id_zona=zonas.zona_id');
        $this->db->join('ciudades', 'ciudades.ciudad_id=zonas.ciudad_id');
        $this->db->join('grupos', 'grupos.id_grupo=producto.produto_grupo');
        $this->db->where('zonas.status', 1);

        $this->db->where($where);

        if ($group != false) {
            $this->db->group_by($group);
            $this->db->order_by("count(venta.venta_id)", "desc");
            $this->db->limit(10);
        }

        $query = $this->db->get();

        if ($retorno == "RESULT") {
            return $query->result();
        }
    }


    function getUtilidades($select, $where, $retorno, $group)
    {
        $this->db->select($select);
        $this->db->from('detalle_venta');
        $this->db->join('venta', 'venta.venta_id=detalle_venta.id_venta');
        $this->db->join('producto', 'producto.producto_id=detalle_venta.id_producto');
        $this->db->join('cliente', 'venta.id_cliente=cliente.id_cliente');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $this->db->join('proveedor', 'producto.producto_proveedor=proveedor.id_proveedor');

        $this->db->where($where);
        if ($group != false) {
            $this->db->group_by($group);
            $this->db->group_by($group);
            $this->db->order_by('sum(detalle_utilidad)', "desc");
            $this->db->limit(10);
        }
        $query = $this->db->get();
        if ($retorno == "RESULT") {
            return $query->result();
        }
    }

    function get_ventas_user()
    {
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->join('cliente', 'cliente.id_cliente=venta.id_cliente');
        $this->db->group_by('id_vendedor');
        $query = $this->db->get('venta');
        return $query->result_array();
    }

    function ventas_grafica($where)
    {

        $query = $this->db->query("SELECT  detalle_venta.id_producto,venta.fecha,precio FROM `detalle_venta`
LEFT JOIN venta ON venta.venta_id = detalle_venta.id_venta
WHERE  id_producto =" . $where . " ");
        return $query->result_array();

    }

    function compras_grafica($where)
    {

        $query = $this->db->query("SELECT id_producto,precio,fecha_registro FROM `detalleingreso`
LEFT JOIN ingreso ON ingreso.id_ingreso = detalleingreso.id_ingreso WHERE id_producto=" . $where . " ");
        return $query->result_array();

    }

    function getDetalleVenta($retorno, $order, $where)
    {
        $this->db->select('unidades.*,detalle_venta.*,producto.*,venta.fecha,venta.venta_id AS numero,venta.venta_status AS estado,local_nombre');
        $this->db->from('detalle_venta');
        $this->db->join('producto', 'producto.producto_id=detalle_venta.id_producto');
        $this->db->join('venta', 'venta.venta_id=detalle_venta.id_venta');
        $this->db->join('unidades', 'unidades.id_unidad=detalle_venta.unidad_medida');
        $this->db->join('local', 'local.int_local_id=venta.local_id');
        $this->db->where($where);
        $this->db->order_by($order);

        $query = $this->db->get();

        if ($retorno == "ARRAY") {
            return $query->result_array();
        }
    }


    function update_inventario($campos, $wheres)
    {
        $this->db->trans_start();
        $this->db->where($wheres);
        $this->db->update('inventario', $campos);
        $this->db->trans_complete();
    }

///////////////////////////////////////////////////////////////////////////////////////////
    function update_venta($condicion, $campos)
    {
        $this->db->trans_start();
        $this->db->where($condicion);
        $this->db->update('venta', $campos);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function get_estatus($id)
    {
        $this->db->where('venta_id', $id);
        $query = $this->db->get('venta');
        if ($query->num_rows() > 0) {
            $row = $query->row_array();
            return $row['venta_status'];
        } else {
            return '';
        }
    }

    function buscar_NroVenta_credito($nroVenta)
    {
        $this->db->select('v.venta_id,c.dec_credito_montodeuda,cl.razon_social');
        $this->db->from('venta v');
        $this->db->join('credito c', 'v.venta_id=c.id_venta');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento = v.numero_documento');
        $this->db->join('cliente cl', 'cl.id_cliente = v.id_cliente');
        $this->db->where('v.venta_id', $nroVenta);

        $query = $this->db->get();

        return $query->result();
    }

    function get_venta_by_status($estatus)
    {
        $this->db->select('*');
        $this->db->from('venta');
        $this->db->join('cliente', 'cliente.id_cliente=venta.id_cliente');
        $this->db->join('local', 'local.int_local_id=venta.local_id');
        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->where_in('venta_status', $estatus);
        $query = $this->db->get();

        return $query->result();
    }

    function get_ventas_by($condicion, $completado = FALSE)
    {
        $this->db->select('venta.*, cliente.*, zonas.zona_nombre, local.*,condiciones_pago.*,documento_venta.*,usuario.*,
        (select SUM(metros_cubicos*detalle_venta.cantidad) from unidades_has_producto join detalle_venta
         on detalle_venta.id_producto=unidades_has_producto.producto_id where detalle_venta.id_venta=venta.venta_id
         and detalle_venta.unidad_medida=unidades_has_producto.id_unidad) as total_metos_cubicos,  (select count(id_detalle)
         from detalle_venta where id_venta=venta.venta_id and precio_sugerido>0) as preciosugerido,
            (select count(id_producto) from detalle_venta where id_venta = venta.venta_id ) as cantidad_productos, grupos_cliente.*');

        $this->db->from('venta');
        $this->db->join('cliente', 'cliente.id_cliente=venta.id_cliente');
        $this->db->join('zonas', 'cliente.id_zona=zonas.zona_id');
        $this->db->join('local', 'local.int_local_id=venta.local_id');
        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.id_vendedor');
        $this->db->join('grupos_cliente', 'grupos_cliente.id_grupos_cliente=cliente.grupo_id');
        $this->db->order_by('venta.venta_id', 'desc');
        if ($completado != FALSE) {
            $this->db->where('venta_status !=', PEDIDO_GENERADO);
            $this->db->where('venta_status !=', PEDIDO_ANULADO);
            $this->db->where('venta_status !=', PEDIDO_ENVIADO);
            unset($condicion['venta_status']);
        }
        $this->db->where($condicion);
        $query = $this->db->get();

        // echo $this->db->last_query();

        return $query->result();
    }


    function get_total_ventas_by_date($condicion)
    {
        $sql = "SELECT SUM(total) as suma FROM `venta` WHERE venta_status = 'COMPLETADO' AND DATE(fecha)='" . $condicion . "'";
        $query = $this->db->query($sql);

        return $query->row_array();
    }

    function get_ventas_by_cliente($condicion)
    {
        $this->db->select('SUM(subtotal) AS sub_total,SUM(total_impuesto) AS impuesto,SUM(total) AS totalizado,
         a.*,b.*,c.*,d.*,e.*,f.*');
        $this->db->from('venta a');
        $this->db->join('cliente b', 'b.id_cliente=a.id_cliente');
        $this->db->join('local c', 'c.int_local_id=a.local_id');
        $this->db->join('condiciones_pago d', 'd.id_condiciones=a.condicion_pago');
        $this->db->join('documento_venta e', 'e.id_tipo_documento=a.numero_documento');
        $this->db->join('usuario f', 'f.nUsuCodigo=a.id_vendedor');
        $this->db->group_by('a.id_cliente');
        $this->db->where($condicion);
        $query = $this->db->get();

        return $query->result();
    }

//////////////////////////////////////////////////////////////////////////////////////
    function select_rpt_venta($tipo, $fecha, $anio, $mes, $forma)
    {
        $this->db->select('nVenCodigo,
						cliente.var_cliente_nombre,
						usuario.nombre,
						dat_venta_fecregistro,
						dec_venta_montoTotal,
						documento_venta.documento_Serie,
						documento_venta.documento_Numero,
						nombre_numero_documento,
						var_venta_estado,
						l.var_local_nombre,
						ct.var_constante_descripcion as cFormapago');
        $this->db->from('venta');
        $this->db->join('cliente', 'cliente.nCliCodigo=venta.nCliCodigo');
        $this->db->join('documento_venta', 'documento_venta.nTipDocumento=venta.nTipDocumento');
        $this->db->join('usuario', 'usuario.nUsuCodigo=venta.nUsuCodigo');
        $this->db->join('constante ct', 'ct.int_constante_valor=venta.int_venta_formaPago');
        $this->db->join('local l', 'l.int_local_id=venta.int_venta_local');
        $this->db->where('venta.var_venta_estado != ', 2);
        $this->db->where('ct.int_constante_clase', 3);

        switch ($tipo) {
            case 1:
                $this->db->where('venta.int_venta_formaPago', $forma);
                $this->db->where('DATE(venta.dat_venta_fecregistro)', $fecha);
                break;
            case 2:
                $this->db->where('DATE(venta.dat_venta_fecregistro)', $fecha);
                break;
            case 3:
                $this->db->where('venta.int_venta_formaPago', $forma);
                $this->db->where('YEAR(venta.dat_venta_fecregistro)', $anio);
                $this->db->where('MONTH(venta.dat_venta_fecregistro)', $mes);
                break;
            case 4:
                $this->db->where('YEAR(venta.dat_venta_fecregistro)', $anio);
                $this->db->where('MONTH(venta.dat_venta_fecregistro)', $mes);
                break;
            case 5:
                $this->db->where('venta.int_venta_formaPago', $forma);
                $this->db->where('YEAR(venta.dat_venta_fecregistro)', $anio);
                break;
            case 6:
                $this->db->where('YEAR(venta.dat_venta_fecregistro)', $anio);
                break;
        }
        $query = $this->db->get();

        return $query->result();
    }

    function select_ventas_credito()
    {
        $this->db->select('*');
        $this->db->from('venta');
        $this->db->join('credito', 'credito.id_venta=venta.venta_id');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $query = $this->db->get();

        return $query->result();
    }

    function select_venta_estadocuenta()
    {
        $this->db->select('*');
        $this->db->from('venta');
        $this->db->join('credito', 'credito.id_venta=venta.venta_id');
        $this->db->join('documento_venta', 'documento_venta.id_tipo_documento=venta.numero_documento');
        $query = $this->db->get();

        return $query->result();
    }

    function count_by($field, $where)
    {
        $this->db->select('count(venta_id) as count');
        $this->db->from('venta');
        $this->db->where_in($field, $where);
        $query = $this->db->get();
        return $query->row_array();
    }


    function obtener_venta($id_venta)
    {
        $querystring = "select v.venta_id,v.total as montoTotal,v.subtotal  as subTotal, v.confirmacion_usuario as confirmacion_usuario_pago_adelantado,
 v.total_impuesto as impuesto, v.pagado,cli_dat.valor as clienteDireccion, pd.producto_nombre as nombre,pd.costo_unitario,pd.presentacion,tr.bono,
 pd.producto_cualidad, pd.producto_id as producto_id, pd.venta_sin_stock, tr.precio_sugerido, tr.cantidad as cantidad ,tr.precio as preciounitario, tr.id_detalle,
tr.detalle_importe as importe, v.fecha as fechaemision, cre.dec_credito_montodeuda,
p.nombre as vendedor,p.nUsuCodigo as id_vendedor,t.nombre_tipo_documento as descripcion, t.documento_Serie as serie, t.documento_Numero as numero, t.nombre_tipo_documento,df.documento_tipo,
c.razon_social as cliente, c.id_cliente as cliente_id, cli_dat.valor as direccion_cliente,c.representante as representanteCliente,cli_dat2.valor as telefonoC1,
 c.identificacion as documento_cliente, cp.id_condiciones, cp.nombre_condiciones, v.venta_status,v.venta_tipo, u.id_unidad, u.nombre_unidad, u.abreviatura,
 i.porcentaje_impuesto, up.unidades, up.orden,
 (select config_value from configuraciones where config_key='" . EMPRESA_NOMBRE . "') as RazonSocialEmpresa,
 (select config_value from configuraciones where config_key='" . EMPRESA_DIRECCION . "') as DireccionEmpresa,
 (select config_value from configuraciones where config_key='" . EMPRESA_TELEFONO . "') as TelefonoEmpresa,
 (select abreviatura from unidades_has_producto join unidades on unidades.id_unidad=unidades_has_producto.id_unidad
 where unidades_has_producto.producto_id=pd.producto_id order by orden desc limit 1) as unidad_minima
from venta as v
inner join usuario p on p.nUsuCodigo = v.id_vendedor
inner join documento_venta t on t.id_tipo_documento = v.numero_documento
left join documento_fiscal df on df.venta_id = v.venta_id
inner join detalle_venta tr on tr.id_venta = v.venta_id
inner join cliente c on c.id_cliente = v.id_cliente
inner join producto pd on pd.producto_id = tr.id_producto
inner join condiciones_pago cp on cp.id_condiciones = v.condicion_pago
inner join unidades u on u.id_unidad = tr.unidad_medida
inner join impuestos i on i.id_impuesto = pd.producto_impuesto
inner join unidades_has_producto up on up.producto_id = pd.producto_id and up.id_unidad=tr.unidad_medida
join (SELECT c.cliente_id, c.tipo, c.valor, c.principal, COUNT(*) FROM cliente_datos c WHERE c.tipo =1 GROUP BY c.cliente_id, c.tipo  ) cli_dat on cli_dat.cliente_id = c.id_cliente
left join (SELECT c1.cliente_id, c1.tipo, c1.valor, c1.principal, COUNT(*) FROM cliente_datos c1 WHERE c1.tipo =2 GROUP BY c1.cliente_id, c1.tipo  ) cli_dat2 on cli_dat2.cliente_id = c.id_cliente
left join credito cre on cre.id_venta=v.venta_id
where v.venta_id=" . $id_venta . " group by tr.id_detalle order by 1 ";

        $query = $this->db->query($querystring);
        //   echo $this->db->last_Query();

        return $query->result_array();
    }

    function obtener_venta_backup($id_venta)
    {
        $querystring = "select v.venta_id,v.total as montoTotal,v.subtotal  as subTotal,
 v.total_impuesto as impuesto, v.pagado,cli_dat.valor as clienteDireccion, pd.producto_nombre as nombre,pd.costo_unitario,pd.presentacion,tr.bono,
 pd.producto_cualidad, pd.producto_id as producto_id, pd.venta_sin_stock, tr.precio_sugerido, tr.cantidad as cantidad ,tr.precio as preciounitario, tr.id_detalle,
tr.detalle_importe as importe, v.fecha as fechaemision, cre.dec_credito_montodeuda,
p.nombre as vendedor,p.nUsuCodigo as id_vendedor,t.nombre_tipo_documento as descripcion, t.documento_Serie as serie, t.documento_Numero as numero, t.nombre_tipo_documento,df.documento_tipo,
c.razon_social as cliente, c.id_cliente as cliente_id, cli_dat.valor as direccion_cliente,c.representante as representanteCliente,cli_dat2.valor as telefonoC1,
 c.identificacion as documento_cliente, cp.id_condiciones, cp.nombre_condiciones, v.venta_status,v.venta_tipo, u.id_unidad, u.nombre_unidad, u.abreviatura,
 i.porcentaje_impuesto, up.unidades, up.orden,
 (select config_value from configuraciones where config_key='" . EMPRESA_NOMBRE . "') as RazonSocialEmpresa,
 (select config_value from configuraciones where config_key='" . EMPRESA_DIRECCION . "') as DireccionEmpresa,
 (select config_value from configuraciones where config_key='" . EMPRESA_TELEFONO . "') as TelefonoEmpresa,
 (select abreviatura from unidades_has_producto join unidades on unidades.id_unidad=unidades_has_producto.id_unidad
 where id_producto=pd.producto_id order by orden desc limit 1) as unidad_minima
from venta_backup as v
inner join usuario p on p.nUsuCodigo = v.id_vendedor
inner join documento_venta t on t.id_tipo_documento = v.numero_documento
left join documento_fiscal df on df.venta_id = v.venta_id
inner join detalle_venta_backup tr on tr.id_venta = v.venta_id
inner join cliente c on c.id_cliente = v.id_cliente
inner join producto pd on pd.producto_id = tr.id_producto
inner join condiciones_pago cp on cp.id_condiciones = v.condicion_pago
inner join unidades u on u.id_unidad = tr.unidad_medida
inner join impuestos i on i.id_impuesto = pd.producto_impuesto
inner join unidades_has_producto up on up.producto_id = pd.producto_id and up.id_unidad=tr.unidad_medida
join (SELECT c.cliente_id, c.tipo, c.valor, c.principal, COUNT(*) FROM cliente_datos c WHERE c.tipo =1 GROUP BY c.cliente_id, c.tipo  ) cli_dat on cli_dat.cliente_id = c.id_cliente
left join (SELECT c1.cliente_id, c1.tipo, c1.valor, c1.principal, COUNT(*) FROM cliente_datos c1 WHERE c1.tipo =2 GROUP BY c1.cliente_id, c1.tipo  ) cli_dat2 on cli_dat2.cliente_id = c.id_cliente
left join credito cre on cre.id_venta=v.venta_id
where v.venta_id=" . $id_venta . " group by tr.id_detalle order by 1 ";

        $query = $this->db->query($querystring);
        //   echo $this->db->last_Query();

        return $query->result_array();
    }

    function documentoVenta($id_venta = null)
    {
        $ventaCol = "'0' as documento_id,
			t.nombre_tipo_documento as descripcion,
			t.documento_Serie as serie,
			t.documento_Numero as numero,";
        $ventaAdd = "inner join documento_venta t on t.id_tipo_documento = v.numero_documento";

        $produCol = "'0' as documento_id,";
        $unidadCol = "dd.unidad_medida";
        $produAdd = "inner join detalle_venta dd on dd.id_venta = v.venta_id";

        $documento = $this->db->query('SELECT venta_id FROM documento_fiscal WHERE venta_id = ' . $id_venta);
        if ($documento->num_rows() > 0) {
            $ventaCol = "df.documento_fiscal_id as documento_id,
				df.documento_tipo as descripcion, 
				df.documento_serie as serie, 
				df.documento_numero as numero, 
				df.documento_tipo,";
            $ventaAdd = "inner join documento_fiscal df on df.venta_id = v.venta_id";

            $produCol = "dd.documento_fiscal_id as documento_id,";
            $unidadCol = "dd.id_unidad";
            $produAdd = "inner join documento_detalle dd on dd.id_venta = v.venta_id";
        }

        $descripcion = $this->db->query("select
				v.venta_id,
				v.total as montoTotal,
				v.subtotal as subTotal,
				v.total_impuesto as impuesto, 
				v.pagado, 

				v.fecha as fechaemision, 
				cre.dec_credito_montodeuda,
				p.nombre as vendedor,
				cami.camiones_placa as placa,
				cami.id_trabajadores,
				p.nUsuCodigo,

				" . $ventaCol . "
				c.razon_social as cliente, 
				c.id_cliente as cliente_id, 
				cli_dat.valor as direccion_cliente,
				c.identificacion as documento_cliente, 
				cp.id_condiciones, 
				cp.nombre_condiciones,
				v.venta_status,
				(select config_value from configuraciones where config_key='" . EMPRESA_NOMBRE . "') as RazonSocialEmpresa,
				(select config_value from configuraciones where config_key='" . EMPRESA_DIRECCION . "') as DireccionEmpresa,
				(select config_value from configuraciones where config_key='" . EMPRESA_TELEFONO . "') as TelefonoEmpresa
			from 
				venta as v
				inner join usuario p on p.nUsuCodigo = v.id_vendedor
				" . $ventaAdd . "
				inner join cliente c on c.id_cliente = v.id_cliente

				inner join condiciones_pago cp on cp.id_condiciones = v.condicion_pago
				left join credito cre on cre.id_venta = v.venta_id
				left join camiones cami on cami.id_trabajadores = p.nUsuCodigo
                left join (SELECT c.cliente_id, c.tipo, c.valor, c.principal, COUNT(*) FROM cliente_datos c WHERE c.tipo =1 GROUP BY c.cliente_id, c.tipo  ) cli_dat on cli_dat.cliente_id = c.id_cliente

			where
				v.venta_id = " . $id_venta . " order by 1");

        $data = array();
        $ventas = $descripcion->result_array();

        $data['ventas'] = array();

        foreach ($ventas as $venta) {
            $productos = $this->db->query("select
				v.venta_id,
				" . $produCol . "
				pd.producto_nombre as nombre,
			dd.documento_fiscal_id as documento_id,
				pd.producto_cualidad,

				pd.producto_id as producto_id,
				dv.precio as precioV,
				dv.id_producto as productoDV,
				dd.cantidad as cantidad,
				dd.id_producto as ddproductoID,
				dd.precio as preciounitario,
				dd.detalle_importe as importe,
				    dv.bono,
				u.id_unidad, 
				u.nombre_unidad, 
				u.abreviatura,
				i.porcentaje_impuesto, 
				up.unidades, 
				up.orden,
				(select abreviatura from unidades_has_producto join unidades on unidades.id_unidad = unidades_has_producto.id_unidad
			where 
				ddproductoID = pd.producto_id order by orden desc limit 1) as unidad_minima


			from
				venta as v
				" . $produAdd . "
				inner join producto pd on pd.producto_id = dd.id_producto
				inner join detalle_venta dv on dv.id_producto = pd.producto_id
				inner join unidades u on u.id_unidad = " . $unidadCol . "
				inner join impuestos i on i.id_impuesto = pd.producto_impuesto
				inner join unidades_has_producto up on up.producto_id = pd.producto_id and up.id_unidad = " . $unidadCol . "
			where
				v.venta_id = " . $id_venta . " and dd.documento_fiscal_id=" . $venta['documento_id'] . " group by documento_detalle_id  order by 1,productoDV desc ");

            $productos = $productos->result_array();
//echo $this->db->last_query();
            //  var_dump($productos);

            foreach ($productos as $p) {
                $data['productos'][] = $p;
            }

            $venta['productos'] = $productos;
            $data['ventas'][] = $venta;

        }

//var_dump( $data['productos']);
        return $data;
    }

    function update_status($id_venta, $estatus, $vendedor_id)
    {
        $this->db->trans_start();

        $data = array('venta_status' => $estatus);
        //CAMBIO DE ESTATUS EN EL DE LA VENTA
        $this->db->where('venta_id', $id_venta);
        $this->db->update('venta', $data);
        //INSERCION PARA LLEVAR REGISTRO DE CAMBIO DE ESTATUS

        $dataregistro = array('venta_id' => $id_venta, 'vendedor_id' => $vendedor_id,
            'estatus' => $estatus, 'fecha' => date('Y-m-d h:m:s'));
        $this->db->insert('venta_estatus', $dataregistro);

        $this->db->trans_complete();
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function ventas_contado($where)
    {
        $w = "DATE(fecha) >= DATE('" . $where['fecha'] . "') AND DATE(fecha) <= DATE('" . $where['fecha'] . "')";
        $this->db->select('SUM(pagado) as totalV');
        $this->db->where($w);
        $this->db->where('condiciones_pago.dias', 0);
        $this->db->where_in('venta.venta_status', array(COMPLETADO));
        $this->db->from('venta');
        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->row_array();
    }

    function cobrosadelantados_caja($where)
    {
        $w = "DATE(venta.confirmacion_fecha) >= DATE('" . $where['fecha'] . "') AND DATE(venta.confirmacion_fecha) <= DATE('" . $where['fecha'] . "') AND venta.confirmacion_caja IS NOT NULL AND pagado > 0.00
        and venta.confirmacion_usuario IS NOT NULL AND venta_tipo='ENTREGA' ";
        $this->db->select('SUM(pagado) as adelantoCaja');
        $this->db->where($w);

        //$this->db->where_in('venta.venta_status', array(PEDIDO_DEVUELTO, PEDIDO_ENTREGADO, COMPLETADO, PEDIDO_GENERADO));

        $this->db->from('venta');

        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->row_array();
    }

    function cobrosadelantados_banco($where)
    {
        $w = "DATE(venta.confirmacion_fecha) >= DATE('" . $where['fecha'] . "') AND DATE(venta.confirmacion_fecha) <= DATE('" . $where['fecha'] . "') AND venta.confirmacion_banco IS NOT NULL AND pagado > 0.00
       and venta.confirmacion_usuario IS NOT NULL AND venta_tipo='ENTREGA' ";
        $this->db->select('SUM(pagado) as adelantoBanco');
        $this->db->where($w);

        //   $this->db->where_in('venta.venta_status', array(PEDIDO_DEVUELTO, PEDIDO_ENTREGADO, COMPLETADO, PEDIDO_GENERADO));

        $this->db->from('venta');

        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->row_array();
    }

    function cuadre_caja_cobros_caja($where)
    {

        /* $w = array('DATE(liquidacion_fecha) >=' => $where['fecha'],
             'DATE(liquidacion_fecha) <=' => $where['fecha'],
             'historial_estatus' => 'CONFIRMADO'
         );
         $this->db->select('liquidacion_cobranza.liquidacion_id as liq,historial_pagos_clientes.*,liquidacion_cobranza_detalle.*'); //SUM(historial_monto) as suma

         $this->db->from('liquidacion_cobranza_detalle');
         $this->db->join('historial_pagos_clientes', 'liquidacion_cobranza_detalle.pago_id=historial_pagos_clientes.historial_id');
         $this->db->join('liquidacion_cobranza', 'liquidacion_cobranza.liquidacion_id=liquidacion_cobranza_detalle.liquidacion_id');
         $this->db->where($w);
         $this->db->order_by('liquidacion_cobranza_detalle.liquidacion_id', 'desc');
         $query = $this->db->get();

         return $query->result_array();*/


        $w = array('DATE(liquidacion_fecha) >=' => $where['fecha'],
            'DATE(liquidacion_fecha) <=' => $where['fecha'],
            'historial_estatus' => 'CONFIRMADO'
        );
        $this->db->select('*,SUM(historial_monto) as suma');
        $this->db->where($w);
        $this->db->from('historial_pagos_clientes');
        $this->db->join('liquidacion_cobranza_detalle', 'liquidacion_cobranza_detalle.pago_id=historial_id');
        $this->db->join('liquidacion_cobranza', 'liquidacion_cobranza.liquidacion_id=liquidacion_cobranza_detalle.liquidacion_id');
        $query = $this->db->get();

        //  echo $this->db->last_query();
        return $query->row_array();
    }

    function cuadre_caja_cobros_banco($where)
    {
        $w = array('DATE(liquidacion_fecha) >=' => $where['fecha'],
            'DATE(liquidacion_fecha) <=' => $where['fecha'],
            'historial_estatus' => 'CONFIRMADO');
        $this->db->select('*,SUM(historial_monto) as duedaBanco');
        $this->db->where($w);
        $this->db->where('historial_banco_id IS NOT NULL');
        $this->db->from('historial_pagos_clientes');
        $this->db->join('liquidacion_cobranza_detalle', 'liquidacion_cobranza_detalle.pago_id=historial_id');
        $this->db->join('liquidacion_cobranza', 'liquidacion_cobranza.liquidacion_id=liquidacion_cobranza_detalle.liquidacion_id');
        $query = $this->db->get();

        return $query->row_array();
    }

    function pagos_adelantados($where)
    {
        $this->db->select('venta.*, banco.banco_nombre, cliente.razon_social, usuario.nombre as nombreVendedor,cliente.id_cliente as cod_cliente, consolidado_carga.status');
        $this->db->from('venta');
        $this->db->join('usuario', 'usuario.nUsuCodigo = venta.id_vendedor');
        $this->db->join('cliente', 'cliente.id_cliente  = venta.id_cliente');
        $this->db->join('consolidado_detalle', 'consolidado_detalle.pedido_id  = venta.venta_id', 'left');
        $this->db->join('banco', 'banco.banco_id  = venta.confirmacion_banco', 'left');
        $this->db->join('consolidado_carga', 'consolidado_detalle.consolidado_id  = consolidado_carga.consolidado_id', 'left');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    function pagos_caja_banco($where)
    {
        $this->db->select('venta.*, usuario.nombre as nombreVendedor,cliente.id_cliente as cod_cliente');
        $this->db->from('venta');
        $this->db->join('usuario', 'usuario.nUsuCodigo = venta.id_vendedor');
        $this->db->join('cliente', 'cliente.id_cliente  = venta.id_cliente');
        $this->db->where('venta_id', $where);
        $query = $this->db->get();
        return $query->result_array();
    }

    function pagos_adelantados_filtro($where)
    {
        /* if($where == 1){
             $where = 'confirmacion_usuario IS NOT NULL';
         }elseif($where == 2){
             $where = 'confirmacion_usuario IS NULL';
         }*/

        $this->db->select('*, usuario.nombre as nombreVendedor,cliente.id_cliente as cod_cliente');
        $this->db->from('venta');
        $this->db->join('usuario', 'usuario.nUsuCodigo = venta.id_vendedor');
        $this->db->join('cliente', 'cliente.id_cliente  = venta.id_cliente');
        $this->db->where($where);
        $query = $this->db->get();
        return $query->result_array();
    }

    function pagos_caja_cobrado($datos, $id, $monto)
    {
        $this->db->where('venta_id', $id);
        $this->db->update('venta', $datos);
        $where = array('id_venta' => $id);
        $queryCredito = $this->getCredito($where);
        $credito['var_credito_estado'] = CREDITO_ACUENTA;
        if (($queryCredito['dec_credito_montodebito'] + $monto) >= $queryCredito['dec_credito_montodeuda']) {

            $credito['var_credito_estado'] = CREDITO_CANCELADO;
            $credito['dec_credito_montodebito'] = $queryCredito['dec_credito_montodebito'] + $monto;

        }


        $credito['dec_credito_montodebito'] = $monto;

        $condition = array('id_venta' => $id);
        return $this->actualizarCredito($credito, $condition);
    }

    function pagos_banco_cobrado($datos, $id, $monto)
    {
        $this->db->where('venta_id', $id);
        $result1 = $this->db->update('venta', $datos);

        $credito['dec_credito_montodebito'] = $monto;
        $credito['var_credito_estado'] = CREDITO_ACUENTA;

        $where = array('id_venta' => $id);

        $queryCredito = $this->getCredito($where);
        if (sizeof($queryCredito)) {

            if (($queryCredito['dec_credito_montodebito'] + $monto) >= $queryCredito['dec_credito_montodeuda']) {

                $credito['var_credito_estado'] = CREDITO_CANCELADO;
                $credito['dec_credito_montodebito'] = $queryCredito['dec_credito_montodebito'] + $monto;

            }


            $condition = array('id_venta' => $id);
            return $this->actualizarCredito($credito, $condition);
        } else {
            return $result1;
        }
    }


    function zonaVendedor($vendedor_id = FALSE, $dia)
    {
        $this->db->select('*');
        $this->db->from('usuario u');
        $this->db->join('usuario_has_zona uhz', 'uhz.id_usuario = u.nUsuCodigo');
        $this->db->join('zonas z', 'z.zona_id = uhz.id_zona');
        if ($dia != null) {
            $this->db->join('zona_dias zd', 'zd.id_zona = z.zona_id ');
            $this->db->where('zd.dia_semana', $dia);
        }
        if ($vendedor_id != FALSE)
            $this->db->where('u.nUsuCodigo', $vendedor_id);

        $query = $this->db->group_by('z.zona_id')->get();
        return $query->result_array();
    }

    function clienteDireccion($cliente_id)
    {
        $this->db->select('*');
        $this->db->from('cliente c');
        $this->db->join('cliente_datos cd', 'cd.cliente_id = c.id_cliente', 'left');
        $this->db->where('c.id_cliente', $cliente_id);
        $this->db->where('cd.tipo', 1);

        $query = $this->db->get();
        return $query->result_array();
    }

    function dataCliente($cliente_id)
    {
        $this->db->select('*');
        $this->db->from('cliente c');
        $this->db->where('c.id_cliente', $cliente_id);

        $query = $this->db->get();
        return $query->result_array();
    }

    function getDeudaCliente($cliente_id)
    {
        $this->db->select("
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
        $this->db->where('cliente.id_cliente', $cliente_id);

        $cliente = $this->db->get()->row();

        if ($cliente == NULL) {
            return array('deuda' => 0);
        }

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
            ->where('venta.id_cliente', $cliente_id)
            ->where('historial_pagos_clientes.historial_estatus', 'PENDIENTE');

        $pagado_pendientes = $this->db->get()->row();

        $cliente->pagado_pendientes = $pagado_pendientes->monto != NULL ? $pagado_pendientes->monto : 0;
        $cliente->subtotal_pago -= $cliente->pagado_pendientes;

        return array('deuda' => $cliente->subtotal_venta - $cliente->subtotal_pago);
    }

    function dataClienteIdZona($id_zona)
    {
        $this->db->select('*');
        $this->db->from('cliente c');
        $this->db->where('c.id_zona', $id_zona);
        $this->db->order_by('c.representante', 'asc');

        $query = $this->db->get();
        return $query->result_array();
    }
}

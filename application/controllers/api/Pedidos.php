<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class pedidos extends REST_Controller
{
    private $columnas = array();

    protected $uid = null;

    protected $methods = array(
        'index_get' => array('level' => 0),
        'ver_get' => array('level' => 0),
        'create_get' => array('level' => 0),
        'update_get' => array('level' => 0),
    );

    function __construct()
    {
        parent::__construct();
        $this->load->model('historial/historial_pedido_model');
        $this->load->model('consolidadodecargas/consolidado_model');
        $this->load->model('venta/venta_model', 'ventas');
        $this->load->model('venta/venta_estatus_model');
        $this->load->model('condicionespago/condiciones_pago_model');
        $this->load->model('api/api_model', 'api');
        $this->load->model('escalas/escalas_model', 'escalas');
        $this->load->model('bonificaciones/bonificaciones_model', 'bonificacion');
        $this->load->model('unidades/unidades_model');
        $this->load->model('precio/precios_model');
        $this->load->model('inventario/inventario_model');
        $this->load->model('venta/venta_cobro_model');

        $this->very_auth();
    }

    function very_auth()
    {
        // Request Header
        $reqHeader = $this->input->request_headers();

        // Key
        $key = null;
        if (isset($reqHeader['X-api-key'])) {
            $key = $reqHeader['X-api-key'];
        } else if ($key_get = $this->get('x-api-key')) {
            $key = $key_get;
        } else if ($key_post = $this->post('x-api-key')) {
            $key = $key_post;
        } else {
            $key = null;
        }

        // Auth ID
        $auth_id = $this->api->getAuth($key);

        // ID ?
        if (!empty($auth_id)) {
            $this->uid = $auth_id;
        } else {
            $this->uid = null;
        }
    }

    // All
    public function index_get()
    {
        $condicion = array();
        if ($this->input->get('id_local') != "") {
            $condicion['local_id'] = $this->input->get('id_local');
        }
        /*if ($this->input->get('desde') != "") {
            $condicion['fecha >= '] = date('Y-m-d', strtotime($this->input->get('desde'))) . " " . date('H:i:s', strtotime('00:00:00'));
        }
        if ($this->input->get('hasta') != "") {
            $condicion['fecha <='] = date('Y-m-d', strtotime($this->input->get('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
        }*/
        if ($this->input->get('status') != "") {
            $condicion['venta_status'] = $this->input->get('status');
        }
        if ($this->input->get('cliente') != "") {
            $condicion['id_cliente'] = $this->input->get('cliente');
        }
        if ($this->input->get('vendedor') != "") {
            $condicion['id_vendedor'] = $this->input->get('vendedor');
        }


        $condicion['venta_tipo'] = 'entrega';

        $datas = array();
        $datas['pedidos'] = $this->ventas->get_ventas_by($condicion);

        for ($i = 0; $i < count($datas['pedidos']); $i++) {
            $detalles = $this->db->get_where('detalle_venta', array('id_venta' => $datas['pedidos'][$i]->venta_id))->result();
            $datas['pedidos'][$i]->total_sugerido = 0;
            $datas['pedidos'][$i]->pendiente = 0;
            foreach ($detalles as $detalle) {
                if ($detalle->precio_sugerido == 0)
                    $datas['pedidos'][$i]->total_sugerido += $detalle->precio * $detalle->cantidad;
                else {
                    $datas['pedidos'][$i]->total_sugerido += $detalle->precio_sugerido * $detalle->cantidad;

                    if ($detalle->precio_sugerido < $detalle->precio)
                        $datas['pedidos'][$i]->pendiente = 1;
                }
            }
        }

        if ($datas) {
            $this->response($datas, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Show
    public function ver_get()
    {
        $get = $this->input->get(null, true);

        $id = $get['pedido'];
        $local = $get['local'];
        $status = isset($get['status']) ? $get['status'] : null;

        if (empty($id) or empty($local)) {
            $this->response(array("error" => 'Faltan parametros'), 200);
        }

        $data['pedido'] = null;
        $pedido_detalle = $this->ventas->obtener_venta($id);

        $data['pedido']['venta_id'] = $pedido_detalle[0]['venta_id'];
        $data['pedido']['montoTotal'] = $pedido_detalle[0]['subTotal'];
        $data['pedido']['impuesto'] = $pedido_detalle[0]['impuesto'];
        $data['pedido']['documento_tipo'] = $pedido_detalle[0]['tipo_doc_fiscal'];
        $data['pedido']['cliente_id'] = $pedido_detalle[0]['cliente_id'];
        $data['pedido']['cliente'] = $pedido_detalle[0]['cliente'];
        $data['pedido']['id_condiciones'] = $pedido_detalle[0]['id_condiciones'];
        $data['pedido']['nombre_condiciones'] = $pedido_detalle[0]['nombre_condiciones'];
        $data['pedido']['venta_status'] = $pedido_detalle[0]['venta_status'];
        $data['pedido']['confirmacion_usuario_pago_adelantado'] = $pedido_detalle[0]['confirmacion_usuario_pago_adelantado'];
        $lista_productos = array();
        $lista_bonos_existentes = array();

        foreach ($pedido_detalle as $pedido) {
            $detalle = $this->db->get_where('detalle_venta', array('id_detalle' => $pedido['id_detalle']))->row();
            $importe_sugerido = 0;

            if ($detalle->precio_sugerido == 0)
                $importe_sugerido += $detalle->precio * $detalle->cantidad;
            else {
                $importe_sugerido += $detalle->precio_sugerido * $detalle->cantidad;

            }

            $cantidad = 0;
            if ($status == 'devolver') {
                $venta_hist = $this->ventas->get_venta_hist_cant($id);

                foreach ($venta_hist->detalles as $detalle) {
                    if ($detalle->producto_id == $pedido['producto_id'] && $detalle->bono == 0) {
                        $cantidad = $detalle->cantidad;
                    }
                }

            } else {
                $cantidad = $pedido['cantidad'];
            }

            if ($pedido['bono'] == false) {
                $producto['producto_id'] = $pedido['producto_id'];
                $producto['producto_id_cero'] = sumCod($pedido['producto_id']);
                $producto['nombre'] = $pedido['nombre'];
                $producto['precio'] = $pedido['preciounitario'];
                $producto['cantidad'] = $cantidad;
                $producto['detalle_importe'] = $pedido['importe'];
                $producto['detalle_importe_sugerido'] = $importe_sugerido;
                $producto['producto_cualidad'] = $pedido['producto_cualidad'];
                $producto['precio_sugerido'] = $pedido['precio_sugerido'];
                $producto['unidad_medida'] = $pedido['id_unidad'];
                $producto['nombre_unidad'] = $pedido['nombre_unidad'];
                $producto['abreviatura'] = $pedido['abreviatura'];
                $producto['porcentaje_impuesto'] = $pedido['porcentaje_impuesto'];
                $producto['unidades'] = $pedido['unidades'];
                $producto['bonificaciones'] = $this->bonificacion->get_all_by_condiciones($producto['producto_id']);
                $producto['precios'] = $this->precios_model->get_all_by_producto($producto['producto_id']);
                $producto['escalas'] = $this->escalas->get_by('producto', $producto['producto_id'], true);

                $inventario = $this->inventario_model->get_by_id_row($producto['producto_id'], $local);
                $unidades = $this->unidades_model->get_by_producto($producto['producto_id']);
                $maxima_unidades = $unidades[0]['unidades'];
                $cantidad_total = ($inventario['cantidad'] * $maxima_unidades) + $inventario['fraccion'];

                $producto['existencia'] = $cantidad_total;

                $lista_productos[] = $producto;

            }

            if ($pedido['bono'] == true) {
                $bono['producto_id'] = $pedido['producto_id'];
                $bono['producto_id_cero'] = sumCod($pedido['producto_id']);
                $bono['nombre'] = $pedido['nombre'];
                $bono['precio'] = $pedido['preciounitario'];
                $bono['cantidad'] = $pedido['cantidad'];
                $bono['detalle_importe'] = $pedido['importe'];
                $bono['detalle_importe_sugerido'] = $importe_sugerido;
                $bono['producto_cualidad'] = $pedido['producto_cualidad'];
                $bono['precio_sugerido'] = $pedido['precio_sugerido'];
                $bono['unidad_medida'] = $pedido['id_unidad'];
                $bono['nombre_unidad'] = $pedido['nombre_unidad'];
                $bono['abreviatura'] = $pedido['abreviatura'];
                $bono['porcentaje_impuesto'] = $pedido['porcentaje_impuesto'];
                $bono['unidades'] = $pedido['unidades'];
                $bono['bonificaciones'] = $this->bonificacion->get_all_by_condiciones($producto['producto_id']);
                $bono['precios'] = $this->precios_model->get_all_by_producto($producto['producto_id']);
                $bono['escalas'] = $this->escalas->get_by('producto', $producto['producto_id'], true);

                $inventario = $this->inventario_model->get_by_id_row($bono['producto_id'], $local);
                $unidades = $this->unidades_model->get_by_producto($bono['producto_id']);
                $maxima_unidades = $unidades[0]['unidades'];
                $cantidad_total = ($inventario['cantidad'] * $maxima_unidades) + $inventario['fraccion'];

                $bono['existencia'] = $cantidad_total;

                $lista_bonos_existentes[] = $bono;

            }
        }

        $data['pedido']['items_pedido'] = $lista_productos;
        $data['pedido']['bonos_existentes'] = $lista_bonos_existentes;

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Save
    /**
     *
     */
    public function create_get()
    {

        $post = $this->input->get(null, true);

        $venta_status = (isset($post['venta_status'])) ? $post['venta_status'] : 'GENERADO';
        $total = 0;
        $subtotal = 0;
        $total_impuesto = 0;
        $lista = isset($post['lista_producto']) ? $post['lista_producto'] : null;
        $lista_bonos = isset($post['lista_bono']) ? $post['lista_bono'] : null;
        if (count($lista) > 0) {
            $detalle = json_decode($lista);

            $bonos = json_decode($lista_bonos);
            foreach ($detalle as $item) {
                $totali = ($item->precio * $item->cantidad);
                $impuestoi = 0;
                if ($item->porcentaje_impuesto > 0) {
                    $impuestoi = (($totali * $item->porcentaje_impuesto) / 100);
                }
                $subtotali = $totali - $impuestoi;
                $total = $total + $totali;
                $total_impuesto = $total_impuesto + $impuestoi;
                $subtotal = $subtotal + $subtotali;
            }

            if ($bonos) {
                foreach ($bonos as $item) {
                    $bono = Array();
                    $bono['cantidad'] = floatval($item->bono_cantidad);
                    $bono['unidad_medida'] = $item->bono_unidad;
                    $bono['id_producto'] = $item->bono_producto;
                    $bono['precio'] = 0;
                    $bono['detalle_importe'] = 0;
                    $bono['precio_sugerido'] = 0;
                    $bono['bono'] = true;
                    $bono['porcentaje_impuesto'] = 0;
                    $object = (object)$bono;
                    array_push($detalle, $object);
                }
            }


            $condicion_pago = $this->condiciones_pago_model->get_by('id_condiciones', $post['condicion_pago']);
            $pedido = array(
                'fecha' => date('Y-m-d H:i:s'),
                'id_cliente' => $post['id_cliente'],
                'id_vendedor' => isset($post['id_vendedor']) ? $post['id_vendedor'] : null,
                'local_id' => isset($post['id_local']) ? $post['id_local'] : null,
                'condicion_pago' => isset($post['condicion_pago']) ? $post['condicion_pago'] : null,
                'venta_status' => $venta_status,
                'subtotal' => $subtotal,
                'total_impuesto' => $total_impuesto,
                'total' => $total,
                'importe' => $post['monto'],
                'diascondicionpagoinput' => isset($condicion_pago['dias']) ? $condicion_pago['dias'] : null,
                'tipo_documento' => $post['tipo_documento'],
                'venta_tipo' => VENTA_ENTREGA

            );


            if (!empty($post)) {
                $montoboletas = $post['MONTO_BOLETAS_VENTA'];
                $pedido['retencion'] = $post['retencion'];

                $save = $this->ventas->insertar_venta($pedido, $detalle, $montoboletas);
                if ($save != false) {
                            $this->historial_pedido_model->insertar_pedido(PROCESO_GENERAR, array(
                                'pedido_id' => $save,
                                'responsable_id' => $post['id_vendedor']
                            ));
                        }
                if ($save === false) {
                    $this->response(array('status' => 'failed'));
                } else {
                    $this->response(array('status' => 'success', 'detalle' => $detalle, 'bonos' => $bonos));
                }
            }
        } else {
            $this->response(array('status' => 'failed', 'get' => $post));
        }

    }

    // Update
    public function update_get()
    {
        $post = $this->input->get(null, true);
        $montoboletas = $post['MONTO_BOLETAS_VENTA'];
        $newclient = $post['id_cliente'];

        // Lista de pedido
        $total = 0;
        $subtotal = 0;
        $total_impuesto = 0;
        $lista = isset($post['lista_producto']) ? $post['lista_producto'] : null;
        $lista_bonos = isset($post['lista_bono']) ? $post['lista_bono'] : null;
        if (count($lista) > 0) {
            $detalle = json_decode($lista);

            $bonos = json_decode($lista_bonos);
            foreach ($detalle as $item) {
                $totali = ($item->precio * $item->cantidad);
                $impuestoi = 0;
                if ($item->porcentaje_impuesto > 0) {
                    $impuestoi = (($totali * $item->porcentaje_impuesto) / 100);
                }
                $subtotali = $totali - $impuestoi;
                $total = $total + $totali;
                $total_impuesto = $total_impuesto + $impuestoi;
                $subtotal = $subtotal + $subtotali;
            }

            if ($bonos) {
                foreach ($bonos as $item) {
                    $bono = Array();
                    $bono['cantidad'] = floatval($item->bono_cantidad);
                    $bono['unidad_medida'] = $item->bono_unidad;
                    $bono['id_producto'] = $item->bono_producto;
                    $bono['precio'] = 0;
                    $bono['detalle_importe'] = 0;
                    $bono['precio_sugerido'] = 0;
                    $bono['bono'] = true;
                    $bono['porcentaje_impuesto'] = 0;
                    $object = (object)$bono;
                    array_push($detalle, $object);
                }
            }

            $id = $post['id_pedido'];
            $venta = $this->ventas->get_by('venta_id', $id);

            $venta_status = $post['venta_status'];

            $pedido = array(
                'fecha' => $venta['fecha'],
                'id_cliente' => $newclient,
                'id_vendedor' => $venta['id_vendedor'],
                'local_id' => $venta['local_id'],
                'condicion_pago' => $venta['condicion_pago'],
                'venta_status' => $venta_status,
                'subtotal' => $subtotal, // esto se debe calcular en base al total
                'total_impuesto' => $total_impuesto, // esto se debe calcula en base al total
                'total' => $total,
                'importe' => $post['monto'],
                'venta_id' => $id,
                'devolver' => ($venta_status == PEDIDO_DEVUELTO) ? true : false,
                'diascondicionpagoinput' => $venta['dias'],
                'venta_tipo' => VENTA_ENTREGA,
                'retencion' => $post['retencion']

            );
            if (!empty($post)) {
                $save = $this->ventas->actualizar_venta($pedido, $detalle, $montoboletas);
                $this->historial_pedido_model->editar_pedido(PROCESO_GENERAR, $save);
                if ($save === false) {
                    $this->response(array('status' => 'failed'));
                } else {
                    $this->response(array('status' => 'success'));
                }
            }
        } else {
            $this->response(array('status' => 'failed', 'error' => 'No se ha recibido el parametro lista_producto'));
        }

        // }
    }

    function ventas_credito_get()
    {

        $post = $this->input->get();


        $id_cliente = null;
        $fechaDesde = null;
        $fechaHasta = null;

        $nombre_or = false;
        $where_or = false;

        $where = "((`venta_status` IN ('" . PEDIDO_ENTREGADO . "', '" . PEDIDO_DEVUELTO . "') and venta.venta_tipo='ENTREGA' and consolidado_detalle.confirmacion_usuario IS NOT NULL) OR (`venta_status` ='" . COMPLETADO . "' and venta.venta_tipo='CAJA')) ";

        if (!empty($post['cliente'])) {

            $where = $where . " AND venta.id_cliente =" . $post['cliente'];
        }
        if (!empty($post['fecha_ini'])) {

            $where = $where . " AND date(fecha) >= '" . date('Y-m-d', strtotime($post['fecha_ini'])) . "'";
        }
        if (!empty($post['fecha_fin'])) {

            $where = $where . " AND  date(fecha) <= '" . date('Y-m-d', strtotime($post['fecha_fin'])) . "'";
        }

        if (!empty($post['vendedor'])) {
            $vendedor = $this->db->get_where('usuario', array('nUsuCodigo' => $post['vendedor']))->row();
            if ($vendedor->grupo == '2')
                $where = $where . " AND venta.id_vendedor =" . $post['vendedor'];
        }
        //echo $where;

        $where_in[0] = array(CREDITO_DEBE, CREDITO_ACUENTA);
        $nombre_in[0] = 'var_credito_estado';
        $order = "venta_id desc";
        /*$nombre_in[1] = 'venta_status';
        $where_in[1] = array(PEDIDO_ENTREGADO, PEDIDO_DEVUELTO, COMPLETADO, PEDIDO_GENERADO, PEDIDO_ENVIADO);*/

        $select = 'venta.venta_id, venta_tipo, venta.id_cliente, razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones, condiciones_pago.dias,venta.id_cliente as clientV, venta.pagado,consolidado_detalle.confirmacion_monto_cobrado_caja,consolidado_detalle.confirmacion_monto_cobrado_bancos,
            ciudades.ciudad_nombre as cliente_distrito_nombre, zonas.zona_nombre as cliente_zona_nombre,
            (select SUM(historial_monto) from historial_pagos_clientes where historial_pagos_clientes.credito_id = venta.venta_id and historial_estatus="PENDIENTE" ) as confirmar,usuario.nUsuCodigo as vendedor_id, usuario.nombre as vendedor_nombre,consolidado_detalle.consolidado_id';
        $from = "venta";
        $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'consolidado_detalle', 'usuario', 'ciudades', 'zonas');
        $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago',
            'consolidado_detalle.pedido_id=venta.venta_id', 'usuario.nUsuCodigo=venta.id_vendedor',
            'ciudades.ciudad_id=cliente.ciudad_id', 'cliente.id_zona=zonas.zona_id');
        $tipo_join = array('left', null, null, null, 'left', null, null, null);

        $result['ventas'] = $this->ventas->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, $order, "RESULT_ARRAY");


        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array(), 200);
        }

    }

    function ventas_pagos_get(){

        $get = $this->get(null, true);
        $id_vendedor = isset($get['vendedor']) ? $get['vendedor'] : null;

        if ($id_vendedor) {
            $data['clientes'] = $this->venta_cobro_model->get_pagos_pendientes(array(
                'fecha_ini' => date('Y-m-01'),
                'fecha_fin' => date('Y-m-d'),
                'fecha_flag' => 0,
                'vendedor_id' => $id_vendedor
            ));

        } else {
            $data['clientes'] = $this->venta_cobro_model->get_pagos_pendientes(array(
                'fecha_ini' => date('Y-m-01'),
                'fecha_fin' => date('Y-m-d'),
                'fecha_flag' => 0
            ));
        }


        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    function ventas_historial_get(){

        $get = $this->get(null, true);
        $id_pedido = isset($get['pedido']) ? $get['pedido'] : null;
        $data = null;

        if ($id_pedido) {
            $data['pagos_historial'] = $this->venta_cobro_model->get_pagos($id_pedido);
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    function ventas_liquidar_get() {

        $get = $this->get(null, true);
        $id_vendedor = isset($get['vendedor']) ? $get['vendedor'] : null;
        $data = null;

        if ($id_vendedor) {
            $data['pagos_liquidar'] = $this->venta_cobro_model->get_pagos_by_vendedor($id_vendedor);
        }

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    function liquidar_cgc_get() {

        $get = $this->get(null, true);

        $estatus = $get['estatus'];
        $id_pedido = $get['id_pedido_liquidacion'];
        $pago = $get['pago_id'];
        $banco = $get['banco_id'];
        $num_oper = $get['num_oper'];
        $vendedor = $get['vendedor'];
        $monto = $get['monto'] != NULL ? $get['monto'] : 0;
        $fecha_oper = $get['fecha_oper'];

        $venta = $this->ventas->get_by('venta_id', $id_pedido);

        if ($estatus != PEDIDO_RECHAZADO) {

            $this->ventas->actualizarCredito(array('dec_credito_montodeuda' => $venta['total']), array('id_venta' => $id_pedido));
        }

        $this->consolidado_model->updateDetalle(array(
            'pedido_id' => $id_pedido,
            'liquidacion_monto_cobrado' => $monto,
            'pago_id' => $pago,
            'banco_id' => $banco,
            'num_oper' => $num_oper,
            'vendedor' => $vendedor,
            'fecha_documento' => $fecha_oper
        ));

        $result = $this->ventas->update_status($id_pedido, $estatus);

        if ($result === false) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }
    }

    public function devolver_pedido_get() {

        $get = $this->get(null, true);

        $venta_id = $get['venta_id'];
        $total_importe = $get['total_importe'];
        $devoluciones = json_decode($get['devoluciones']);

        $result = $this->ventas->devolver_venta($venta_id, $total_importe, $devoluciones);

        if ($result === false) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }
    }

    public function estatus_get()
    {
        $post = $this->input->get(null, true);
        if (!$post) {
            $post = $this->input->post(null, true);
        }
        $id = (isset($post['id'])) ? $post['id'] : null;
        $value = (isset($post['value'])) ? $post['value'] : null;

        if (!empty($id) && !empty($value)) {
            $venta_id = $id;
            $estatus = $value;
        } else {
            $venta_id = (isset($post['venta_id'])) ? $post['venta_id'] : null;
            $estatus = (isset($post['estatus'])) ? $post['estatus'] : null;
        }
        $estatus = strtoupper($estatus);
        $vendedor_id = (isset($post['vendedor_id'])) ? $post['vendedor_id'] : $this->uid;

        if (empty($venta_id)) {
            $this->response(array('status' => 'failed'));
        }

        // Data
        $data = array();

        // Estatus
        if (!empty($estatus)) {
            $array = array('venta_id' => $venta_id, 'vendedor_id' => $vendedor_id, 'estatus' => $estatus);

            // Venta Estatus
            $ventaEstatus = $this->ventas->get_estatus($venta_id);

            $data = $this->venta_estatus_model->insert_estatus($array);

            if ($data) {
                if ($estatus == PEDIDO_ANULADO OR $estatus == PEDIDO_RECHAZADO) {
                    if ($ventaEstatus == PEDIDO_GENERADO OR $ventaEstatus == PEDIDO_ENVIADO OR $ventaEstatus == COMPLETADO
                        OR $ventaEstatus == PEDIDO_ENTREGADO) {
                        $campos = array('venta_status' => $estatus, 'id_venta' => $venta_id, 'motivo' => 'Pedido anulado', 'nUsuCodigo' => $vendedor_id);

                        $data = $this->ventas->devolver_stock($venta_id, $campos, $estatus);
                        if($estatus == PEDIDO_RECHAZADO)
                            $this->consolidado_model->updateDetalle(array('pedido_id' => $venta_id, 'liquidacion_monto_cobrado' => 0.0));

                    } else {
                        $value = $ventaEstatus;
                    }
                } else {
                    if ($estatus == PEDIDO_ENTREGADO) {
                        $venta = $this->ventas->get_by('venta_id', $venta_id);
                        $monto = $this->input->get('monto');
                        $venta['venta_status'] = PEDIDO_ENTREGADO;
                        $venta['importe'] = $venta['pagado'];
                        $venta['devolver'] = 'false';
                        $venta['diascondicionpagoinput'] = $venta['dias'];
                        $this->consolidado_model->updateDetalle(array('pedido_id' => $venta_id, 'liquidacion_monto_cobrado' => $monto));
                        $result = $this->ventas->update_status($venta_id, PEDIDO_ENTREGADO, $vendedor_id);


                    } else {
                        $condicion = array('venta_id' => $venta_id);
                        $campos = array('venta_status' => $estatus);

                        $data = $this->ventas->update_venta($condicion, $campos);
                    }

                }
            } else {
                $value = $ventaEstatus;
            }
        } else {
            $data['ventas'] = $this->venta_estatus_model->get_by('venta_id', $venta_id, true);
        }

        // Estatus ?
        if (!empty($id) && !empty($value)) {
            echo $value;
        } else {
            if ($data) {
                $this->response(array('status' => 'success'));
            } else {
                $this->response(array('status' => 'failed'));
            }
        }
    }

    public function comprobar_stock_get() {
        $get = $this->input->get(null, true);
        $id_local = $get['id_local'];

        $temp = array();
        $temp2 = array();

        $lista = isset($get['prodstock']) ? $get['prodstock'] : null;

        if (count($lista) > 0) {
            $listastock = json_decode($lista);

            foreach($listastock as $data) {
                $temp['producto_id'] = $data->id_producto;
                $temp['local_id'] = $id_local;
                $temp['cantidad'] = $data->cantidad;
                $temp['unidad_id'] = $data->unidad_medida;
                $temp2[] = $temp;
            }

            $result['sinstock'] = $this->inventario_model->check_stock($temp2);

            if ($result) {
                $this->response($result, 200);
            } else {
                $this->response(array(), 200);
            }
        } else {
            $this->response(array(), 200);
        }
    }

    public function entregas_items_get() {
        $get = $this->input->get(null, true);

        $venta_id = $get['venta_id'];
        $result = $this->ventas->get_venta_detalle($venta_id);

        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array(), 200);
        }
    }
}

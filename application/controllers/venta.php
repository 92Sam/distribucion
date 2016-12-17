<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('historial/historial_pedido_model');
        $this->load->model('venta/venta_model');
        $this->load->model('local/local_model');
        $this->load->model('cliente/cliente_model');
        $this->load->model('producto/producto_model', 'pd');
        $this->load->model('precio/precios_model', 'precios');
        $this->load->model('proveedor/proveedor_model', 'pv');
        $this->load->model('condicionespago/condiciones_pago_model');
        $this->load->model('metodosdepago/metodos_pago_model');
        $this->load->model('usuario/usuario_model');
        $this->load->model('zona/zona_model');
        $this->load->model('camiones/camiones_model');
        $this->load->model('venta/venta_estatus_model', 'venta_estatus');
        $this->load->model('historial_pagos_clientes/historial_pagos_clientes_model');
        $this->load->model('consolidadodecargas/consolidado_model');
        $this->load->model('banco/banco_model');
        $this->load->model('liquidacioncobranza/liquidacion_cobranza_model');
        $this->load->model('ingreso/ingreso_model');
        $this->load->model('gastos/gastos_model');
        $this->load->library('phpword');

        $this->load->library('Pdf');
        $this->load->library('session');
        $this->load->library('phpExcel/PHPExcel.php');

    }


    function index()
    {
        $idventa = $this->input->post('idventa');
        $data["condiciones_pago"] = $this->condiciones_pago_model->get_all();


        $vendedor = null;
        $useradmin = $this->session->userdata('admin');

        if ($useradmin == 1) {
            $data["clientes"] = $this->cliente_model->get_all();
        } else {
            $vendedor = $this->session->userdata('nUsuCodigo');

            $data["clientes"] = $this->cliente_model->get_all($vendedor);
        }


        $data["productos"] = $this->pd->select_all_producto();
        // $data["precios"] = $this->precios->get_precios();
        $data["venta"] = array();
        if ($idventa != FALSE) {
            $data["venta"] = $this->venta_model->obtener_venta($idventa);
            if ($this->input->post('devolver') == 1) {
                $data['devolver'] = 1;
            }
        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/generarVenta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function pedidos()
    {
        $idventa = $this->input->post('idventa');
        $vendedor = null;
        $data = array();
        $useradmin = $this->session->userdata('admin');
        if ($useradmin == 1) {
            $data["clientes"] = $this->cliente_model->get_all();
            $data['zonas'] = $this->venta_model->zonaVendedor(FALSE, date('N'));
        } else {
            $vendedor = $this->session->userdata('nUsuCodigo');
            $data["clientes"] = $this->cliente_model->get_all($vendedor);
            $data['zonas'] = $this->venta_model->zonaVendedor($vendedor, date('N'));
        }
//var_dump($data["clientes"]);

        $data['coso_id'] = $this->input->post('coso_id');
        $data["venta_id"] = $idventa;
        $data["estatus_actual"] = $this->input->post('estatus_actual');;
        // $data["condiciones_pago"] = $this->condiciones_pago_model->get_all();
        /*$data["productos"] = $this->pd->select_all_producto();
        $data["precios"] = $this->precios->get_precios();*/
        $data["venta"] = array();

        $data['estatus_consolidado'] = $this->input->post('estatus_consolidado');
        if ($idventa !== FALSE) {
            $data["venta"] = $this->venta_model->obtener_venta($idventa);
            if ($this->input->post('devolver') == 1) {
                $data['devolver'] = 1;
            }
            if ($this->input->post('preciosugerido') == 1) {
                $data['preciosugerido'] = 1;
            }
        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/PedidosVentas', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function editar_pedido()
    {
        $venta_id = $this->input->post('idventa');

        $data["clientes"] = $this->cliente_model->get_all();

        $venta = $this->db->get_where('venta', array('venta_id' => $venta_id))->row();
        $data['cliente'] = $this->db->select('
            cliente.id_cliente as cliente_id,
            cliente.razon_social as cliente_nombre,
            cliente.grupo_id as grupo_id,
            grupos_cliente.nombre_grupos_cliente as grupo_nombre
            ')
            ->from('cliente')
            ->join('grupos_cliente', 'grupos_cliente.id_grupos_cliente = cliente.grupo_id')
            ->where('cliente.id_cliente', $venta->id_cliente)->get()->row();

        $data["venta_id"] = $venta_id;
        $data["precios"] = $this->precios->get_precios();

        $data["venta"] = $this->venta_model->obtener_venta($venta_id);

        echo $this->load->view('menu/ventas/EditarPedidosVentas', $data, true);
    }

    function venta_backup()
    {
        $idventa = $this->input->post('idventa');

        $data = array();

        if ($idventa !== FALSE) {
            $data['venta'] = $this->venta_model->obtener_venta_backup($idventa);
        }

        echo json_encode($data);
    }

    function registrar_venta()
    {
        $dataresult = array();
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('cboCliente', 'cboCliente', 'requiered');
            $this->form_validation->set_rules('cboTipDoc', 'cboTipDoc', 'requiered');
            $this->form_validation->set_rules('cboModPag', 'cboModPag', 'requiered');
            $this->form_validation->set_rules('subTotal', 'subTotal', 'requiered');
            $this->form_validation->set_rules('igv', 'igv', 'requiered');
            $this->form_validation->set_rules('montoigv', 'montoigv', 'requiered');
            $this->form_validation->set_rules('totApagar', 'totApagar', 'requiered');
            $this->form_validation->set_rules('importe', 'importe', 'requiered');

            if ($this->form_validation->run() == false):
                echo "no guardo";
            else:
                if ($_POST['subTotal'] != "" && $_POST['montoigv'] != "" && $_POST['totApagar'] != "") {
                    $venta = array(
                        'fecha' => date("Y-m-d H:i:s"),
                        'id_cliente' => $this->input->post('id_cliente', true),
                        'id_vendedor' => $this->session->userdata('nUsuCodigo'),
                        'venta_tipo' => $this->input->post('venta_tipo'),

                        'condicion_pago' => $this->input->post('condicion_pago', true),
                        'venta_status' => $this->input->post('venta_status', true),
                        'local_id' => $this->session->userdata('id_local'),

                        'subtotal' => $this->input->post('subTotal', true),
                        'total_impuesto' => $this->input->post('montoigv', true),
                        'total' => $this->input->post('totApagar', true),

                        'importe' => $this->input->post('importe', true),
                        'diascondicionpagoinput' => $this->input->post('diascondicionpagoinput', true),
                        'tipo_documento' => $this->input->post('tipo_documento', true)

                    );

                    $lista_bonos = $this->input->post('lst_bonos', true);
                    if (empty($lista_bonos)) $lista_bonos = null;
                    $bonos = json_decode($lista_bonos);
                    $detalle = json_decode($this->input->post('lst_producto', true));
                    //var_dump($bonos);
                    if ($bonos) {
                        foreach ($bonos as $item) {
                            $bono = Array();
                            $bono['cantidad'] = floatval($item->bono_cantidad);
                            $bono['unidad_medida'] = $item->bono_unidad_id;
                            $bono['id_producto'] = $item->bono_id;
                            $bono['precio'] = 0;
                            $bono['detalle_importe'] = 0;
                            $bono['precio_sugerido'] = 0;
                            $bono['bono'] = true;
                            $bono['porcentaje_impuesto'] = 0;
                            $object = (object)$bono;
                            array_push($detalle, $object);
                        }
                    }

                    $id = $this->input->post('idventa');
                    $montoboletas = $this->session->userdata('MONTO_BOLETAS_VENTA');
                    if (empty($id)) {
                        $resultado = $this->venta_model->insertar_venta($venta, $detalle, $montoboletas);
                        $id = $resultado;
                        if ($resultado != false) {
                            $this->historial_pedido_model->insertar_pedido(PROCESO_GENERAR, array(
                                'pedido_id' => $resultado,
                                'responsable_id' => $this->session->userdata('nUsuCodigo')
                            ));
                        }
                    } else {
                        if ($this->input->post('accion_resetear')) {
                            $venta['accion'] = $this->input->post('accion_resetear');
                        }

                        $venta['venta_id'] = $id;
                        $venta['devolver'] = $this->input->post('devolver');
                        $resultado = $this->venta_model->actualizar_venta($venta, $detalle, $montoboletas);
                    }
                    if ($resultado != false) {
                        if ($this->input->post('devolver') == 'true') {
                            $this->consolidado_model->updateDetalle(array('pedido_id' => $id, 'liquidacion_monto_cobrado' => $this->input->post('importe', true)));
                        }
                        $this->ventaEstatus($id, $this->input->post('venta_status', true));

                        $dataresult['estatus_consolidado'] = $this->input->post('estatus_consolidado', true);;
                        $dataresult['msj'] = "guardo";
                        $dataresult['idventa'] = $resultado;
                    } else {
                        $dataresult['msj'] = "no guardo";
                    }
                } else {
                    $dataresult['msj'] = "no guardo";
                }

                echo json_encode($dataresult);
            endif;
        } else {
            redirect(base_url() . 'ventas/', 'refresh');
        }
    }


    function guardarPago()
    {

        if ($this->input->is_ajax_request()) {


            $json = array();
            $detalle = json_decode($this->input->post('lst_producto', true));
            $opciones = array();
            $tipo_metodo = $this->metodos_pago_model->get_by('id_metodo', $detalle[0]->metodo);

            // echo $tipo_metodo['tipo_metodo'];
            if ($tipo_metodo['tipo_metodo'] == "BANCO") {
                $opciones['banco'] = $this->input->post('banco');
            } else {
                $caja = $this->session->userdata('caja');
                // echo $caja;
                if (!empty($caja)) {
                    $opciones['caja'] = $caja;
                } else {
                    $json['error'] = 'No hay caja asociada al usuario';
                }
            }
            if (!isset($json['error'])) {

                $save_historial = $this->historial_pagos_clientes_model->guardar($detalle, $opciones);

                if ($save_historial == true) {

                    $credito = $this->venta_model->updateCreditos($detalle, false);
                    $result['credito'] = $this->venta_model->get_credito_by_venta($detalle[0]->id_venta);

                    $detalle[0]->monto_restante = floatval($result['credito'][0]['dec_credito_montodeuda']) - (floatval($result['credito'][0]['dec_credito_montodebito']) + floatval($credito[0]['confirmacion_monto_cobrado_caja']) + floatval($credito[0]['confirmacion_monto_cobrado_bancos']) + floatval($credito[0]['pagado']));
                    $detalle[0]->usuario = $this->session->userdata('nUsuCodigo');
                    //echo floatval($result['credito'][0]['dec_credito_montodeuda'])-(floatval($result['credito'][0]['dec_credito_montodebito'])+ floatval($credito[0]['confirmacion_monto_cobrado_caja'])+floatval($credito[0]['confirmacion_monto_cobrado_bancos'])+floatval($credito[0]['pagado']));

                    $json['success'] = 'success';
                    $json['id_venta'] = $detalle[0]->id_venta;
                    $json['id_historial'] = $save_historial;

                } else {

                    if ($save_historial == false) {
                        $json['error'] = 'Por favor intente nuevamente';
                    } else {
                        $json['error'] = $save_historial;
                    }
                }
            }

            echo json_encode($json);
        }


    }


    function guardar_notacredito()
    {

        if ($this->input->is_ajax_request()) {


            $venta = json_decode($this->input->post('lst_venta', true));


            $credito = $this->venta_model->updateCreditos($venta, true);
            if ($credito != FALSE) {
                $json['success'] = 'Se ha generado una nota de credito exitoxamente';
            } else {
                $json['error'] = 'No se ha podido generar la nota de credito';
            }

            echo json_encode($json);
        }


    }


    function buscarmetodo()
    {
        if ($this->input->is_ajax_request()) {


            $metodo = json_decode($this->input->post('metodo', true));

            $tipo_metodo = $this->metodos_pago_model->get_by('id_metodo', $metodo);

            if ($tipo_metodo['tipo_metodo'] == "BANCO") {

                $json['bancos'] = $this->banco_model->get_all();
            } else {
                $json = false;
            }
            echo json_encode($json);
        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////


    function ventas_by_cliente()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['ventatodos'] = "TODOS";
        $condicion = array('a.id_cliente >=' => 0);
        $data['ventas'] = $this->venta_model->get_ventas_by_cliente($condicion);
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/ventas_by_cliente', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function show_venta_cliente($id = FALSE)
    {

        if ($id != FALSE) {


            $condicion = array('venta.id_cliente' => $id);
            $data['ventas'] = $this->venta_model->get_ventas_by($condicion);
            $data['ventatodos'] = "CLIENTE";
            $this->load->view('menu/ventas/show_venta_cliente', $data);

        }
    }

    function cancelar()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $estatus = array('COMPLETADO');
        $data["ventas"] = $this->venta_model->get_venta_by_status($estatus);
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/cancelarVenta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function devolver()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $estatus = array('COMPLETADO');
        $data["ventas"] = $this->venta_model->get_venta_by_status($estatus);
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/devolverventa', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function ventaEstatus($venta_id, $estatus)
    {
        $vendedor_id = $this->session->userdata('nUsuCodigo');
        $estatus = array('venta_id' => $venta_id, 'vendedor_id' => $vendedor_id, 'estatus' => $estatus);
        $this->venta_estatus->insert_estatus($estatus);
    }

    function anular_venta()
    {
        $id = $this->input->post('id');

        $campos = array('motivo' => $this->input->post('motivo'), 'nUsuCodigo' => $this->session->userdata('nUsuCodigo'));
        $data['resultado'] = $this->venta_model->devolver_stock

        ($id, $campos, PEDIDO_ANULADO);

        if ($data['resultado'] != FALSE) {
            $this->ventaEstatus($id, PEDIDO_ANULADO);
            $json['success'] = 'Se ha anulado exitosamente';
        } else {
            $json['error'] = 'Ha ocurrido un error al anular la venta';
        }

        echo json_encode($json);
    }

    function get_ventas()
    {
        $completado = false;
        $condicion = array();
        if ($this->input->post('id_local') != "") {
            $condicion['local_id'] = $this->input->post('id_local');
            $data['local'] = $this->input->post('id_local');
        }

        $fecha_flag = $this->input->post('fecha_flag');
        if ($fecha_flag == 1) {
            if ($this->input->post('desde') != "") {
                $condicion['fecha >= '] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
                $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
            }
            if ($this->input->post('hasta') != "") {
                $condicion['fecha <='] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
                $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
            }
        }

        if ($this->input->post('estatus') != "") {

            if ($this->input->post('estatus') == PEDIDO_ENTREGADO) {
                $completado = true;
            } else
                $condicion['venta_status'] = $this->input->post('estatus');

            $data['estatus'] = $this->input->post('estatus');
        }
        if ($this->input->post('vendedor') != "") {
            $condicion['id_vendedor'] = $this->input->post('vendedor');
            $data['vendedor'] = $this->input->post('vendedor');
        }
        if ($this->input->post('listar') == 'pedidos') {
            $condicion['venta_tipo'] = VENTA_ENTREGA;
            $data['listar'] = $this->input->post('listar');
        }
        if ($this->input->post('listar') == 'ventas') {
            $condicion['venta_tipo'] = VENTA_CAJA;
            $data['listar'] = $this->input->post('listar');
        }
        if ($this->input->post('client') != "") {
            $condicion['venta.id_cliente'] = $this->input->post('client');
            $data['client'] = $this->input->post('client');
        }
        if ($this->input->post('zona') != "") {
            $condicion['cliente.id_zona'] = $this->input->post('zona');
            $data['zona'] = $this->input->post('zona');
        }
        if ($this->input->post('id_consolidado') != "") {
            $id_consolidado = $this->input->post('id_consolidado');
            //$data['productos_cons'] = $this->consolidado_model->get_pedido('consolidado_id', $id_consolidado);
            $condicionpedidos = array();
            $condicionpedidos['consolidado_id'] = $id_consolidado;
            $data['productos_cons'] = $this->consolidado_model->get_pedidos_by($condicionpedidos);
        }

        $data['venta'] = $this->venta_model->get_ventas_by($condicion, $completado);
        $ventas = $data['venta'];
        foreach ($ventas as $venta) {
            $id_cliente = $venta->id_cliente;
            $deuda = $this->venta_model->getDeudaCliente($id_cliente);
            if ($deuda['deuda'] > 0) {
                $venta->deudor = 1;
                $venta->deuda = $deuda['deuda'];
            }
        }

        $data['ventas'] = $ventas;

        if ($this->input->post('listar') == 'ventas') {

            $this->load->view('menu/ventas/lista_ventas', $data);
        } else {
            $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/lista_pedidos', $data, true);
            if ($this->input->is_ajax_request()) {
                echo $dataCuerpo['cuerpo'];
            }
        }
    }


    function get_ventas_por_status()
    {

        $condicion = array('local_id' => $this->session->userdata('id_local'));
        $data['local'] = $this->session->userdata('id_local');


        $condicion['venta_status'] = $this->input->post('estatus');
        $data['estatus'] = $this->input->post('estatus');


        $data['ventas'] = $this->venta_model->get_ventas_by($condicion);

        $this->load->view('menu/ventas/lista_ventas_status', $data);

    }


    function pdf($local, $fecha_desde, $fecha_hasta, $estatus, $totalventas)
    {

        if ($local != 0) {
            $condicion = array('local_id' => $local);
        }
        if ($fecha_desde != 0) {

            $condicion['fecha >= '] = date('Y-m-d', strtotime($fecha_desde)) . " " . date('H:i:s');
        }
        if ($fecha_hasta != 0) {

            $condicion['fecha <='] = date('Y-m-d', strtotime($fecha_hasta)) . " " . date('H:i:s');
        }
        if ($estatus != 0) {
            $condicion['venta_status'] = $estatus;
        }

        if ($totalventas == "TODOS") {

            $condicion = array('a.id_cliente >=' => 0);
            $total = $this->venta_model->get_ventas_by_cliente($condicion);

        } elseif ($totalventas != 0 and $totalventas != "TODOS") {

            $condicion = array('venta.id_cliente' => $totalventas);
            $clientes = $this->venta_model->get_ventas_by($condicion);


        } else {

            $ventas = $this->venta_model->get_ventas_by($condicion);

        }


        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('VENTAS');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.â€¢.G.â€¢.D.â€¢.G.â€¢.A.â€¢.D.â€¢.U.â€¢.<br>Gran Logia de la RepÃºblica de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));

// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config.php de libraries/config

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        //  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        //  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//relaciÃ³n utilizada para ajustar la conversiÃ³n de los pÃ­xeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir carÃ¡cteres ASCII estÃ¡ndar, puede utilizar las fuentes bÃ¡sicas como
// Helvetica para reducir el tamaÃ±o del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// AÃ±adir una pÃ¡gina
// Este mÃ©todo tiene varias opciones, consulta la documentaciÃ³n para mÃ¡s informaciÃ³n.
        $pdf->AddPage();

        $pdf->SetFontSize(8);

        $textoheader = "";
        $pdf->writeHTMLCell(
            $w = 0, $h = 0, $x = '60', $y = '',
            $textoheader, $border = 0, $ln = 1, $fill = 0,
            $reseth = true, $align = 'C', $autopadding = true);

//fijar efecto de sombra en el texto
//        $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        $pdf->SetFontSize(12);

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>LISTA</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '<script type="text/javascript">print();</script>';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        $html .= "<br><b>VENTA:</b> " . "<br>";

        if (isset($ventas)) {

            $html .= "<table><tr> <th>N&uacute;mero de Venta</th><th>Cliente</th><th>Vendedor</th> <th>Fecha</th><th>Tipo de Documento</th> ";
            $html .= "<th>Estatus</th><th>Local</th><th>Condici&oacute;n Pago</th> <th>Sub Total</th><th>Total Impuesto</th></tr>";


            foreach ($ventas as $venta) {
                $html .= " <tr><td >" . $venta->venta_id . "</td><td >" . $venta->razon_social . "</td><td >" . $venta->nombre . "</td>";
                $html .= "<td >" . date('d-m-Y H:i:s', strtotime($venta->fecha)) . "</td><td >" . $venta->nombre_tipo_documento . "</td>";
                $html .= "<td >" . $venta->venta_status . "</td><td>" . $venta->local_nombre . "</td><td>" . $venta->nombre_condiciones . "</td>";
                $html .= "<td>" . $venta->subtotal . "</td><td>" . $venta->total_impuesto . "</td></tr>";

            }
        } elseif (isset($total)) {
            $html .= "<table><tr><th>N&uacute;mero de Venta</th><th>Fecha</th><th>Vendedor</th><th>Cliente</th><th>Formato</th>";
            $html .= "<th>Forma de Pago</th><th>Estado</th><th>D&iacute;as Plazo</th><th>Sub total</th><th>Impuesto</th>";
            $html .= " <th>Total</th></tr> ";

            foreach ($total as $rows) {
                $html .= " <tr><td >" . $rows->venta_id . "</td><td >" . date('d-m-Y H:i:s', strtotime($rows->fecha)) . "</td><td >" . $rows->nombre . "</td>";
                $html .= "<td >" . $rows->razon_social . "</td><td >formato</td>";
                $html .= "<td >forma de pago</td><td>" . $rows->venta_status . "</td><td>" . $rows->dias . "</td>";
                $html .= "<td>" . $rows->sub_total . "</td><td>" . $rows->impuesto . "</td><td>" . $rows->totalizado . "</td></tr>";

            }

        } elseif (isset($clientes)) {

            $html .= "<table><tr> <th>N&uacute;mero de Venta</th><th>Fecha</th><th>Vendedor</th><th>Razon Social</th>";
            $html .= "<th>Condiciones de Pago</th><th>Estado</th><th>Local</th><th>Sub total</th><th>Impuesto</th><th>Total</th></tr>";

            foreach ($clientes as $cliente) {
                $html .= " <tr><td >" . $cliente->venta_id . "</td><td >" . date('d-m-Y H:i:s', strtotime($cliente->fecha)) . "</td><td >" . $cliente->nombre . "</td>";
                $html .= "<td >" . $cliente->razon_social . "</td><td >" . $cliente->nombre_condiciones . "</td>";
                $html .= "<td>" . $cliente->venta_status . "</td><td>" . $cliente->local_nombre . "</td>";
                $html .= "<td>" . $cliente->subtotal . "</td><td>" . $cliente->total_impuesto . "</td><td>" . $cliente->total . "</td></tr>";

            }


        }

        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este mÃ©todo tiene varias opciones, consulte la documentaciÃ³n para mÃ¡s informaciÃ³n.
        $nombre_archivo = utf8_decode("Ventas.pdf");
        $pdf->Output($nombre_archivo, 'I');


    }


    function pdfReporteZona($zona, $desde = '', $hasta = '')
    {


        $condicion = array();

        $condicion2 = "venta_status IN ('" . COMPLETADO . "','" . PEDIDO_DEVUELTO . "','" . PEDIDO_ENTREGADO . "','" . PEDIDO_GENERADO . "')";
        $condicion['venta_tipo'] = "ENTREGA";
        $retorno = "RESULT";
        $select = "(SELECT COUNT(venta.id_cliente)) as clientes_atendidos, (SELECT SUM(detalle_venta.cantidad))
                    as cantidad_vendida, usuario.nombre, detalle_venta.*,venta.*, producto.*,grupos.*,familia.*,lineas.*,unidades.*,usuario_has_zona.*,
                    zonas.*,ciudades.*,cliente.razon_social, consolidado_carga.fecha";

        $group = "zona_id,id_producto,unidad_medida";

        if ($zona != "TODAS") {

            $condicion['zona_id'] = $zona;
            $data['zona'] = $this->input->post('id_zona');


        }
        if (($desde != "")) {


            $condicion['date(consolidado_carga.fecha) >= '] = date('Y-m-d', strtotime($desde));

        }
        if (($hasta != "")) {


            $condicion['date(consolidado_carga.fecha) <='] = date('Y-m-d', strtotime($hasta));


            $data['fecha_hasta'] = date('Y-m-d', strtotime($hasta));
        }


        $ventas = $this->venta_model->getProductosZona($select, $condicion, $retorno, $group, $condicion2);

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('REPORTE UTILIDADES');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.â€¢.G.â€¢.D.â€¢.G.â€¢.A.â€¢.D.â€¢.U.â€¢.<br>Gran Logia de la RepÃºblica de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));

// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config.php de libraries/config

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        //  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        //  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//relaciÃ³n utilizada para ajustar la conversiÃ³n de los pÃ­xeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir carÃ¡cteres ASCII estÃ¡ndar, puede utilizar las fuentes bÃ¡sicas como
// Helvetica para reducir el tamaÃ±o del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// AÃ±adir una pÃ¡gina
// Este mÃ©todo tiene varias opciones, consulta la documentaciÃ³n para mÃ¡s informaciÃ³n.
        $pdf->AddPage();

        $pdf->SetFontSize(8);

        $textoheader = "";
        $pdf->writeHTMLCell(
            $w = 0, $h = 0, $x = '60', $y = '',
            $textoheader, $border = 0, $ln = 1, $fill = 0,
            $reseth = true, $align = 'C', $autopadding = true);

//fijar efecto de sombra en el texto
//        $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        $pdf->SetFontSize(12);

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>LISTA</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        if (isset($zona_nombre)) {
            $html .= "<br><b>" . $zona_nombre['zona_nombre'] . ":</b> " . "<br>";
        } else {

            $html .= "<br><b>Utilidades:</b> " . "<br>";
        }

        $html .= "<table><tr><th>Distrito</th><th>Urbanización</th><th>Vendedor</th><th>Grupo</th><th>Familia</th><th>Linea</th>";
        $html .= "<th>Producto</th><th>Cantidad Vendida</th><th>Unidad</th><th>Clientes</th></tr>";

        foreach ($ventas as $venta) {
            $html .= " <tr><td>" . $venta->ciudad_nombre . "</td><td >" . $venta->urb . "</td>";
            $html .= " <td>" . $venta->nombre . " </td><td >" . $venta->nombre_grupo . "</td>";
            $html .= " <td>" . $venta->nombre_familia . "</td><td >" . $venta->nombre_linea . "</td>";
            $html .= " <td>" . $venta->producto_nombre . "</td><td>" . $venta->cantidad_vendida . "</td>";
            $html .= " <td>" . $venta->nombre_unidad . "</td><td>" . $venta->clientes_atendidos . "</td></tr>";


        }

        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este mÃ©todo tiene varias opciones, consulte la documentaciÃ³n para mÃ¡s informaciÃ³n.

        $nombre_archivo = utf8_decode("ReporteZonaProductos.pdf");

        $pdf->Output($nombre_archivo, 'D');


    }


    function pdfReporteUtilidades($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        if ($local != 0) {
            $condicion = array('local_id' => $local);
            $local_nombre = $this->local_model->get_by('int_local_id', $local);
        }
        if ($fecha_desde != 0) {

            $condicion['date(fecha) >= '] = date('Y-m-d', strtotime($fecha_desde));
        }
        if ($fecha_hasta != 0) {

            $condicion['date(fecha) <='] = date('Y-m-d', strtotime($fecha_hasta));
        }

        $condicion = "venta_status IN ('" . COMPLETADO . "','" . PEDIDO_DEVUELTO . "','" . PEDIDO_ENTREGADO . "','" . PEDIDO_GENERADO . "')";
        $retorno = "RESULT";

        if ($utilidades == "TODO") {
            $select = "documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";

            $group = false;

        } elseif ($utilidades == "PRODUCTO") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $group = "producto_id";

        } elseif ($utilidades == "CLIENTE") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";

            $group = "cliente.id_cliente";
        } elseif ($utilidades == "PROVEEDOR") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente,
 proveedor.proveedor_nombre,proveedor.id_proveedor";

            $group = "producto.producto_proveedor";
        }
        $ventas = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);


        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('REPORTE UTILIDADES');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.â€¢.G.â€¢.D.â€¢.G.â€¢.A.â€¢.D.â€¢.U.â€¢.<br>Gran Logia de la RepÃºblica de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));

// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config.php de libraries/config

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        //  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        //  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//relaciÃ³n utilizada para ajustar la conversiÃ³n de los pÃ­xeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir carÃ¡cteres ASCII estÃ¡ndar, puede utilizar las fuentes bÃ¡sicas como
// Helvetica para reducir el tamaÃ±o del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// AÃ±adir una pÃ¡gina
// Este mÃ©todo tiene varias opciones, consulta la documentaciÃ³n para mÃ¡s informaciÃ³n.
        $pdf->AddPage();

        $pdf->SetFontSize(8);

        $textoheader = "";
        $pdf->writeHTMLCell(
            $w = 0, $h = 0, $x = '60', $y = '',
            $textoheader, $border = 0, $ln = 1, $fill = 0,
            $reseth = true, $align = 'C', $autopadding = true);

//fijar efecto de sombra en el texto
//        $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        $pdf->SetFontSize(12);

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>LISTA</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        if (isset($local_nombre)) {
            $html .= "<br><b>" . $local_nombre['local_nombre'] . ":</b> " . "<br>";
        } else {

            $html .= "<br><b>Utilidades:</b> " . "<br>";
        }

        if ($utilidades == "TODO") {

            $html .= "<table><tr> <th>Fecha y Hora</th><th>C&oacute;digo</th><th>N&uacute;mero</th><th>Cantidad</th><th>Producto</th> ";
            $html .= " <th>Cliente</th><th>Vendedor</th><th>Costo</th><th>Precio</th><th>Utilidad</th></tr>";
            foreach ($ventas as $venta) {
                $html .= " <tr><td >" . date('d-m-Y H:i:s', strtotime($venta->fecha)) . "</td><td >" . $venta->venta_id . "</td>";
                $html .= " <td >" . $venta->documento_Serie . " " . $venta->documento_Numero . "</td><td >" . $venta->cantidad . "</td>";
                $html .= "  <td >" . $venta->producto_nombre . "</td><td >" . $venta->razon_social . "</td>";
                $html .= " <td>" . $venta->nombre . "</td><td>" . $venta->detalle_costo_promedio . "</td><td>" . $venta->precio . "</td>";
                $html .= " <td>" . $venta->detalle_utilidad . "</td></tr>";

            }
        } elseif ($utilidades == "PRODUCTO") {
            $html .= "<table><tr> <th>C&oacute;digo</th><th>Producto</th><th>Utilidad</th></tr>";
            foreach ($ventas as $venta) {
                $html .= " <tr><td >" . $venta->id_producto . "</td><td >" . $venta->producto_nombre . "</td> <td >" . $venta->suma . "</td></tr>";
            }
        } elseif ($utilidades == "CLIENTE") {
            $html .= "<table><tr> <th>C&oacute;digo</th><th>Cliente</th><th>Utilidad</th></tr>";
            foreach ($ventas as $venta) {
                $html .= " <tr><td >" . $venta->id_producto . "</td><td >" . $venta->razon_social . "</td> <td >" . $venta->suma . "</td></tr>";
            }
        } elseif ($utilidades == "PROVEEDOR") {
            $html .= "<table><tr> <th>C&oacute;digo</th><th>Proveedor</th><th>Utilidad</th></tr>";
            foreach ($ventas as $venta) {
                $html .= " <tr><td >" . $venta->id_proveedor . "</td><td >" . $venta->proveedor_nombre . "</td> <td >" . $venta->suma . "</td></tr>";
            }
        }

        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este mÃ©todo tiene varias opciones, consulte la documentaciÃ³n para mÃ¡s informaciÃ³n.
        if ($utilidades == "TODO") {
            $nombre_archivo = utf8_decode("ReporteUtilidades.pdf");
        } elseif ($utilidades == "PRODUCTO") {
            $nombre_archivo = utf8_decode("ReporteUtilidadesPorProducto.pdf");
        } elseif ($utilidades == "CLIENTE") {
            $nombre_archivo = utf8_decode("ReporteUtilidadesPorCliente.pdf");
        } elseif ($utilidades == "PROVEEDOR") {
            $nombre_archivo = utf8_decode("ReporteUtilidadesPorProveedor.pdf");
        }


        $pdf->Output($nombre_archivo, 'D');


    }

    function pdfResumenLiquidacion($_id_vend, $_vendedor, $_id_liquidacion, $_fecha, $_montototal)
    {
        $id_vendedor = $_id_vend;
        $vendedor = $_vendedor;
        $id_liquidacion = $_id_liquidacion;
        $fecha = $_fecha;
        $montototal = $_montototal;

        //Tabla
        //////////////////////
        $nombre_or = false;
        $where_or = false;
        $nombre_in = false;
        $where_in = false;

        $where = array(
            'historial_usuario' => $id_vendedor,
            'liquidacion_id' => $id_liquidacion,
            'historial_estatus' => "CONFIRMADO"
        );

        $select = 'documento_Serie, documento_Numero, cliente.razon_social, historial_monto, metodos_pago.*';
        $from = "historial_pagos_clientes";
        $join = array('venta', 'cliente', 'documento_venta', 'metodos_pago', 'liquidacion_cobranza_detalle');
        $campos_join = array('historial_pagos_clientes.credito_id=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'metodos_pago.id_metodo=historial_pagos_clientes.historial_tipopago',
            'liquidacion_cobranza_detalle.pago_id=historial_pagos_clientes.historial_id');

        $resultado = $this->venta_model->traer_by($select, $from, $join, $campos_join, false,
            $where, $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");

        //PDF
        //////////////////
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('P');
        $pdf->SetTitle('Hoja de Liquidacion');
        $pdf->SetPrintHeader(false);
        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 14, '', true);
        $pdf->SetFontSize(8);

        $pdf->AddPage();

        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";
        $count = 0;

        $html .= "<br><br><b><u>HOJA DE LIQUIDACION</u></b><br><br>";
        if (isset($zona_nombre)) {
            $html .= "<br><b>" . $zona_nombre['zona_nombre'] . ":</b> " . "<br>";
        } else {

            $html .= "<br><b>Liquidacion:</b> " . "<br>";
        }

        $html .= "<table><tr><th>No.</th><th>N&uacute;mero Documento</th><th>Cliente</th><th>Vendedor</th>";
        $html .= "<th>M&eacute;todo</th><th>Fecha</th><th>Monto</th></tr>";

        foreach ($resultado as $row) {
            $documento = $row['documento_Serie'] . "-" . $row['documento_Numero'];
            $cliente = $row['razon_social'];
            $metodo = $row['tipo_metodo'];
            $monto = $row['historial_monto'];
            $count++;

            $html .= "<tr>
                            <td>" . $count . "</td>
                            <td>" . $documento . "</td>
                            <td>" . $cliente . "</td>
                            <td>" . urldecode($vendedor) . "</td>
                            <td>" . $metodo . "</td>
                            <td>" . urldecode($fecha) . "</td>
                            <td>" . $monto . "</td>
                        </tr>";
        }
        $html .= "<tr><td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>TOTAL: " . $montototal . "</td></tr>";

        $html .= "<tr><td>CHEQUE:</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td></tr>";

        $html .= "<tr><td>DEPOSITO BBVA:</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td></tr>";

        $html .= "<tr><td>DEPOSITO BCP:</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td></tr>";

        $html .= "<tr><td>DEPOSITO V&M:</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td></tr>";

        $html .= "<tr><td>BILLETE:</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td></tr>";

        $html .= "<tr><td>MONEDA:</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td></tr>";

        $html .= "<tr><td>TOTAL:</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td>";
        $html .= "<td>" . " " . "</td></tr>";

        $html .= "</table>";

        $pdf->writeHTML($html, true, 0, true, 0);
        $pdf->lastPage();
        $pdf->output('HojaLiquidacion.pdf', 'D');
    }

    function excel($local, $fecha_desde, $fecha_hasta, $estatus, $totalventas)
    {

        if ($local != 0) {
            $condicion = array('local_id' => $local);
        }
        if ($fecha_desde != 0) {

            $condicion['fecha >= '] = date('Y-m-d', strtotime($fecha_desde)) . " " . date('H:i:s');
        }
        if ($fecha_hasta != 0) {

            $condicion['fecha <='] = date('Y-m-d', strtotime($fecha_hasta)) . " " . date('H:i:s');
        }
        if ($estatus != 0) {
            $condicion['venta_status'] = $estatus;
        }

        if ($totalventas == "TODOS") {

            $condicion = array('a.id_cliente >=' => 0);
            $total = $this->venta_model->get_ventas_by_cliente($condicion);

        } elseif ($totalventas != 0 and $totalventas != "TODOS") {

            $condicion = array('venta.id_cliente' => $totalventas);
            $clientes = $this->venta_model->get_ventas_by($condicion);


        } else {

            $ventas = $this->venta_model->get_ventas_by($condicion);

        }
        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Ventas")
            ->setSubject("Ventas")
            ->setDescription("Ventas")
            ->setKeywords("Ventas")
            ->setCategory("Ventas");

        if (isset($ventas)) {
            $columna[0] = "NUMERO DE VENTA";
            $columna[1] = "CLIENTE";
            $columna[2] = "VENDEDOR";
            $columna[3] = "FECHA";
            $columna[4] = "TIPO DE DOCUMENTO";
            $columna[5] = "ESTATUS";
            $columna[6] = "LOCAL";
            $columna[7] = "CONDICION DE PAGO";
            $columna[8] = "SUB TOTAL";
            $columna[9] = "TOTAL IMPUESTO";
        } elseif (isset($total)) {

            $columna[0] = "NUMERO DE VENTA";
            $columna[1] = "FECHA";
            $columna[2] = "VENDEDOR";
            $columna[3] = "CLIENTE";
            $columna[4] = "FORMATO";
            $columna[5] = "FORMA DE PAGO";
            $columna[6] = "ESTADO";
            $columna[7] = "DIAS DE PLAZO";
            $columna[8] = "SUB TOTAL";
            $columna[9] = "IMPUESTO";
            $columna[10] = "TOTAL";
        } elseif (isset($clientes)) {

            $columna[0] = "NUMERO DE VENTA";
            $columna[1] = "FECHA";
            $columna[2] = "VENDEDOR";
            $columna[3] = "CLIENTE";
            $columna[4] = "CONDICIONES DE PAGO";
            $columna[5] = "ESTADO";
            $columna[6] = "LOCAL";
            $columna[7] = "SUB TOTAL";
            $columna[8] = "IMPUESTO";
            $columna[9] = "TOTAL";
        }

        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }

        $row = 2;

        if (isset($ventas)) {
            $col = 0;
            foreach ($ventas as $venta) {


                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->venta_id);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->razon_social);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->nombre);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, date('d-m-Y H:i:s', strtotime($venta->fecha)));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->nombre_tipo_documento);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->venta_status);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->dias);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->sub_total);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->impuesto);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->totalizado);

                $row++;
            }
        } elseif (isset($total)) {
            $row = 2;
            foreach ($total as $totales) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $totales->venta_id);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, date('d-m-Y H:i:s', strtotime($totales->fecha)));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $totales->nombre);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $totales->razon_social);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "FORMATO1");

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "forma de pago 1");

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $totales->venta_status);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $totales->dias);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $totales->sub_total);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $totales->impuesto);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $totales->totalizado);

                $row++;
            }

        } elseif (isset($clientes)) {
            $row = 2;
            foreach ($clientes as $cliente) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->venta_id);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, date('d-m-Y H:i:s', strtotime($cliente->fecha)));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->nombre);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->razon_social);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->nombre_condiciones);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->venta_status);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->local_nombre);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->subtotal);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->total_impuesto);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cliente->total);

                $row++;
            }

        }

// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Ventas');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Ventas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }


    function excelReporteZona($zona, $desde = '', $hasta = '')
    {

        $condicion = array();

        $condicion2 = "venta_status IN ('" . COMPLETADO . "','" . PEDIDO_DEVUELTO . "','" . PEDIDO_ENTREGADO . "','" . PEDIDO_GENERADO . "')";
        $condicion['venta_tipo'] = "ENTREGA";
        $retorno = "RESULT";
        $select = "(SELECT COUNT(venta.id_cliente)) as clientes_atendidos, (SELECT SUM(detalle_venta.cantidad))
                    as cantidad_vendida, usuario.nombre, detalle_venta.*,venta.*, producto.*,grupos.*,familia.*,lineas.*,unidades.*,usuario_has_zona.*,
                    zonas.*,ciudades.*,cliente.razon_social, consolidado_carga.fecha";

        $group = "zona_id,id_producto,unidad_medida";

        if ($zona != "TODAS") {

            $condicion['zona_id'] = $zona;
            $data['zona'] = $zona;


        }
        if ($desde != "") {


            $condicion['date(consolidado_carga.fecha) >= '] = date('Y-m-d', strtotime($desde));
            $data['fecha_desde'] = date('Y-m-d', strtotime($desde));
        }
        if (($hasta != "")) {


            $condicion['date(consolidado_carga.fecha) <='] = date('Y-m-d', strtotime($hasta));


            $data['fecha_hasta'] = date('Y-m-d', strtotime($hasta));
        }

        $ventas = $this->venta_model->getProductosZona($select, $condicion, $retorno, $group, $condicion2);

        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("ReporteZonaProductos")
            ->setSubject("ReporteZonaProductos")
            ->setDescription("ReporteZonaProductos")
            ->setKeywords("ReporteZonaProductos")
            ->setCategory("ReporteZonaProductos");

        $columna[0] = "DISTRITO";
        $columna[1] = "URBANIZACIÓN";
        $columna[2] = "VENDEDOR";
        $columna[3] = "GRUPO";
        $columna[4] = "FAMILIA";
        $columna[5] = "LINEA";
        $columna[6] = "PRODUCTO";
        $columna[7] = "CANTIDAD VENDIDA";
        $columna[8] = "UNIDAD";
        $columna[9] = "CLIENTES";

        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }

        $row = 2;

        foreach ($ventas as $venta) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->ciudad_nombre);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->urb);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->nombre);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->nombre_grupo);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->nombre_familia);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->nombre_linea);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->producto_nombre);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->cantidad_vendida);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->nombre_unidad);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $venta->clientes_atendidos);
            $row++;

        }

// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('ReporteZonaProductos');

// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteZonaProductos.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }


    function excelReporteUtilidades($local, $fecha_desde, $fecha_hasta, $utilidades)
    {

        if ($local != 0) {
            $condicion = array('local_id' => $local);
        }
        if ($fecha_desde != 0) {

            $condicion['date(fecha) >= '] = date('Y-m-d', strtotime($fecha_desde));
        }
        if ($fecha_hasta != 0) {

            $condicion['date(fecha) <='] = date('Y-m-d', strtotime($fecha_hasta));
        }


        $condicion = "venta_status IN ('" . COMPLETADO . "','" . PEDIDO_DEVUELTO . "','" . PEDIDO_ENTREGADO . "','" . PEDIDO_GENERADO . "')";
        $retorno = "RESULT";

        if ($utilidades == "TODO") {
            $select = "documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";

            $group = false;

        } elseif ($utilidades == "PRODUCTO") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $group = "producto_id";

        } elseif ($utilidades == "CLIENTE") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";

            $group = "cliente.id_cliente";
        } elseif ($utilidades == "PROVEEDOR") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente,
 proveedor.proveedor_nombre,proveedor.id_proveedor";

            $group = "producto.producto_proveedor";
        }
        $ventas = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);

        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("ReporteUtilidades")
            ->setSubject("ReporteUtilidades")
            ->setDescription("ReporteUtilidades")
            ->setKeywords("ReporteUtilidades")
            ->setCategory("ReporteUtilidades");

        if ($utilidades == "TODO") {
            $columna[0] = "FECHA Y HORA";
            $columna[1] = "CODIGO";
            $columna[2] = "NUMERO";
            $columna[3] = "CANTIDAD";
            $columna[4] = "PRODUCTO";
            $columna[5] = "CLIENTE";
            $columna[6] = "VENDEDOR";
            $columna[7] = "COSTO";
            $columna[8] = "PRECIO";
            $columna[9] = "UTILIDAD";
        } elseif ($utilidades == "PRODUCTO") {
            $columna[0] = "CODIGO";
            $columna[1] = "PRODUCTO";
            $columna[2] = "UTILIDAD";

        } elseif ($utilidades == "CLIENTE") {
            $columna[0] = "CODIGO";
            $columna[1] = "CLIENTE";
            $columna[2] = "UTILIDAD";

        } elseif ($utilidades == "PROVEEDOR") {
            $columna[0] = "CODIGO";
            $columna[1] = "PROVEEDOR";
            $columna[2] = "UTILIDAD";

        }

        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }

        $row = 2;

        if ($utilidades == "TODO") {

            foreach ($ventas as $venta) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, date('d-m-Y H:i:s', strtotime($venta->fecha)));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->venta_id);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->documento_Serie . " " . $venta->documento_Numero);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->cantidad);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->producto_nombre);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->razon_social);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->nombre);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->detalle_costo_promedio);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->precio);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->detalle_utilidad);
                $row++;

            }
        } elseif ($utilidades == "PRODUCTO") {

            foreach ($ventas as $venta) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->id_producto);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->producto_nombre);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->suma);
                $row++;
            }
        } elseif ($utilidades == "CLIENTE") {

            foreach ($ventas as $venta) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->id_cliente);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->razon_social);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->suma);
                $row++;
            }
        } elseif ($utilidades == "PROVEEDOR") {

            foreach ($ventas as $venta) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->id_proveedor);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->proveedor_nombre);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $venta->suma);
                $row++;
            }
        }

// Renombramos la hoja de trabajo
        if ($utilidades == "TODO") {
            $this->phpexcel->getActiveSheet()->setTitle('ReporteUtilidades');
        } elseif ($utilidades == "CLIENTE") {
            $this->phpexcel->getActiveSheet()->setTitle('ReporteUtilidadesPorCliente');
        } elseif ($utilidades == "PROVEEDOR") {
            $this->phpexcel->getActiveSheet()->setTitle('ReporteUtilidadesPorProveedor');
        } elseif ($utilidades == "PRODUCTO") {
            $this->phpexcel->getActiveSheet()->setTitle('ReporteUtilidadesPorProducto');
        }


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        if ($utilidades == "TODO") {
            header('Content-Disposition: attachment;filename="ReporteUtilidades.xlsx"');
        } elseif ($utilidades == "CLIENTE") {
            header('Content-Disposition: attachment;filename="ReporteUtilidadesPorCliente.xlsx"');
        } elseif ($utilidades == "PROVEEDOR") {
            header('Content-Disposition: attachment;filename="ReporteUtilidadesPorProveedor.xlsx"');
        } elseif ($utilidades == "PRODUCTO") {
            header('Content-Disposition: attachment;filename="ReporteUtilidadesPorProducto.xlsx"');
        }


        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }


    function buscar_NroVenta_credito()
    {
        $validar_cronograma = $this->input->post('validar_cronograma');

        if ($this->input->is_ajax_request()) {
            $venta = $this->venta_model->buscar_NroVenta_credito($this->input->post('nro_venta', true));

            if (count($venta) > 0) {
                if (!empty($validar_cronograma)) {
                    $cronogrma = $this->venta_model->get_cronograma_by_venta($venta[0]->venta_id);
                    if (count($cronogrma) > 0) {
                        echo json_encode(array('error' => 'Ya existe un crongrama para la venta seleccionada'));
                    } else {
                        echo json_encode($venta);
                    }
                } else {

                    echo json_encode($venta);
                }
            } else {
                echo json_encode(array('error' => 'El número de venta ingresado no existe o no es una venta a credito'));
            }
        } else {
            redirect(base_url() . 'ventas/', 'refresh');
        }


    }


    function consultar()
    {
        $data['locales'] = $this->local_model->get_all();


        if (isset($_GET['buscar']) and $_GET['buscar'] == 'pedidos') {
            $data['vendedores'] = $this->usuario_model->select_all_by_roll('Vendedor');
            $data['clientes'] = $this->cliente_model->get_all();
            $data['zonas'] = $this->zona_model->get_all();
            $data['consolidados'] = $this->consolidado_model->getData(array(
                'estado' => 'ABIERTO',
                'fecha_ini' => NULL,
                'fecha_fin' => NULL
            ));
            $vista = 'bandejaPedidos';
        } else {
            $vista = 'reporteVenta';
        }
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/' . $vista, $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteUtilidades()
    {


        $data['locales'] = $this->local_model->get_all();
        $data['todo'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteUtilidades', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteUtilidadesProductos()
    {


        $data['locales'] = $this->local_model->get_all();
        $data['productos'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteUtilidades', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteUtilidadesCliente()
    {


        $data['locales'] = $this->local_model->get_all();
        $data['cliente'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteUtilidades', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteUtilidadesProveedor()
    {


        $data['locales'] = $this->local_model->get_all();
        $data['proveedor'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteUtilidades', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function reporteRotacionZona()
    {

        $data['locales'] = $this->local_model->get_all();
        $data['zonas'] = $this->zona_model->get_all();
        $data['todo'] = 1;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/reporteRotacionZona', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function pedidosZona($id = FALSE)
    {

        $this->load->view('menu/estadisticas/estadisticaZonaPedidos');

    }

    function getProductoZona()
    {
        $condicion = array();

        $condicion2 = "venta_status IN ('" . COMPLETADO . "','" . PEDIDO_DEVUELTO . "','" . PEDIDO_ENTREGADO . "','" . PEDIDO_GENERADO . "')";
        $condicion['venta_tipo'] = "ENTREGA";
        $retorno = "RESULT";
        $select = "(SELECT COUNT(venta.id_cliente)) as clientes_atendidos, (SELECT SUM(detalle_venta.cantidad))
                    as cantidad_vendida, usuario.nombre, detalle_venta.*,venta.*, producto.*,grupos.*,familia.*,lineas.*,unidades.*,usuario_has_zona.*,
                    zonas.*,ciudades.*,cliente.razon_social, consolidado_carga.fecha";

        $group = "zona_id,id_producto,unidad_medida";

        if (($this->input->post('id_zona') != "") && ($this->input->post('id_zona') != "TODAS")) {

            $condicion['zona_id'] = $this->input->post('id_zona');
            $data['zona'] = $this->input->post('id_zona');


        }
        if (($this->input->post('desde') != "")) {


            $condicion['date(consolidado_carga.fecha) >= '] = date('Y-m-d', strtotime($this->input->post('desde')));
            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde')));
        }
        if (($this->input->post('hasta') != "")) {


            $condicion['date(consolidado_carga.fecha) <='] = date('Y-m-d', strtotime($this->input->post('hasta')));


            $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta')));
        }


        $data['ventas'] = $this->venta_model->getProductosZona($select, $condicion, $retorno, $group, $condicion2);
        $this->load->view('menu/ventas/listaReporteProductoZona', $data);
    }

    function getUtiidadesVentas()
    {


        if ($this->input->post('id_local') != "") {
            $condicion = array('local_id' => $this->input->post('id_local'));
            $data['local'] = $this->input->post('id_local');
        }
        if ($this->input->post('desde') != "") {

            $condicion['date(fecha) >= '] = date('Y-m-d', strtotime($this->input->post('desde')));
            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde')));
        }
        if ($this->input->post('hasta') != "") {

            $condicion['date(fecha) <='] = date('Y-m-d', strtotime($this->input->post('hasta')));
            $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta')));
        }

        $condicion = "venta_status IN ('" . COMPLETADO . "','" . PEDIDO_DEVUELTO . "','" . PEDIDO_ENTREGADO . "','" . PEDIDO_GENERADO . "')";
        $retorno = "RESULT";
        if ($this->input->post('utilidades') == "TODOS") {
            $select = "documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $data['utilidades'] = "TODO";
            $group = false;

        } elseif ($this->input->post('utilidades') == "PRODUCTOS") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $data['utilidades'] = "PRODUCTO";
            $group = "producto_id";
        } elseif ($this->input->post('utilidades') == "CLIENTE") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";
            $data['utilidades'] = "CLIENTE";
            $group = "cliente.id_cliente";
        } elseif ($this->input->post('utilidades') == "PROVEEDOR") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente,
 proveedor.proveedor_nombre,proveedor.id_proveedor";
            $data['utilidades'] = "PROVEEDOR";
            $group = "producto.producto_proveedor";
        }
        $data['ventas'] = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);
        $this->load->view('menu/ventas/listaReporteUtilidades', $data);
    }

    function estadocuenta()
    {
        $data = "";
        $data["lstCliente"] = $this->cliente_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/estadocuentaVenta', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function lst_reg_estadocuenta_json()
    {
        if ($this->input->is_ajax_request()) {


            $id_cliente = null;
            $fechaDesde = null;
            $fechaHasta = null;

            $nombre_or = false;
            $where_or = false;
            // Pagination Result
            $array = array();
            $array['productosjson'] = array();

            $total = 0;
            $start = 0;
            $limit = false;
            $draw = $this->input->get('draw');

            $draw = $this->input->get('draw');
            if (!empty($draw)) {

                $start = $this->input->get('start');
                $limit = $this->input->get('length');
            }

            $where = "((`venta_status` IN ('" . PEDIDO_ENTREGADO . "', '" . PEDIDO_DEVUELTO . "') and venta.venta_tipo='ENTREGA' and consolidado_detalle.confirmacion_usuario IS NOT NULL) OR (`venta_status` ='" . COMPLETADO . "' and venta.venta_tipo='CAJA')) ";

            if ($this->input->get('cboCliente', true) != -1) {

                $where = $where . " AND venta.id_cliente =" . $this->input->get('cboCliente');
            }
            if ($_GET['fecIni'] != "") {

                $where = $where . " AND date(fecha) >= '" . date('Y-m-d', strtotime($this->input->get('fecIni'))) . "'";
            }
            if ($_GET['fecFin'] != "") {

                $where = $where . " AND  date(fecha) <= '" . date('Y-m-d', strtotime($this->input->get('fecFin'))) . "'";
            }
            //  echo $where;
            $nombre_in[0] = 'var_credito_estado';
            $where_in[0] = array(CREDITO_DEBE, CREDITO_ACUENTA, CREDITO_NOTACREDITO, CREDITO_CANCELADO);
            $nombre_in[1] = 'venta_status';
            $where_in[1] = array(PEDIDO_ENTREGADO, PEDIDO_DEVUELTO, COMPLETADO, PEDIDO_GENERADO, PEDIDO_ENVIADO);
            ///////////////////////
            $select = 'venta.venta_id,venta.pagado, venta.id_cliente, venta.pagado,
             razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones, confirmacion_monto_cobrado_caja, confirmacion_monto_cobrado_bancos,
            (select SUM(historial_monto) from historial_pagos_clientes where historial_pagos_clientes.credito_id = venta.venta_id and historial_estatus="PENDIENTE" ) as confirmar,
            usuario.nombre as vendedor';
            $from = "venta";
            $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'consolidado_detalle', 'usuario');
            $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
                'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago',
                'consolidado_detalle.pedido_id=venta.venta_id', 'usuario.nUsuCodigo=venta.id_vendedor');
            $tipo_join = array('left', null, null, null, 'left', null);


            $where_custom = false;
            $ordenar = $this->input->get('order');
            $order = false;
            $order_dir = 'desc';
            if (!empty($ordenar)) {
                $order_dir = $ordenar[0]['dir'];
                if ($ordenar[0]['column'] == 0) {
                    $order = 'venta.venta_id';
                }
                if ($ordenar[0]['column'] == 1) {
                    $order = 'documento_venta.nombre_tipo_documento';
                }
                if ($ordenar[0]['column'] == 2) {
                    $order = 'documento_venta.nombre_tipo_documento ';
                }
                if ($ordenar[0]['column'] == 3) {
                    $order = 'cliente.razon_social';
                }
                if ($ordenar[0]['column'] == 4) {
                    $order = 'venta.fecha';
                }
                if ($ordenar[0]['column'] == 5) {
                    $order = 'total';
                }
                if ($ordenar[0]['column'] == 6) {
                    $order = 'dec_credito_montodebito';
                }
                if ($ordenar[0]['column'] == 7) {
                    $order = 'dec_credito_montodebito';
                }
                if ($ordenar[0]['column'] == 8) {
                    $order = 'dec_credito_montodebito';
                }
                if ($ordenar[0]['column'] == 9) {
                    $order = 'dec_credito_montodebito';
                }
                if ($ordenar[0]['column'] == 9) {
                    $order = 'dec_credito_montodebito';
                }
                if ($ordenar[0]['column'] == 9) {
                    $order = 'credito.var_credito_estado';
                }

            }

            $group = false;


            $total = $this->venta_model->traer_by_mejorado('COUNT(venta.venta_id) as total', $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, $group, $order, "RESULT_ARRAY", false, false, $order_dir, false, $where_custom);


            $lstVenta = $this->venta_model->traer_by_mejorado($select, $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, $group, $order, "RESULT_ARRAY", $limit, $start, $order_dir, false, $where_custom);
            if (count($lstVenta) > 0) {

                foreach ($lstVenta as $v) {

                    $pendiente = 0;
                    $PRODUCTOjson = array();

                    $PRODUCTOjson[] = $v['venta_id'];
                    $PRODUCTOjson[] = $v['nombre_tipo_documento'];
                    $PRODUCTOjson[] = $v['documento_Serie'] . "-" . $v['documento_Numero'];

                    $PRODUCTOjson[] = $v['razon_social'];
                    $PRODUCTOjson[] = date("d-m-Y H:i:s", strtotime($v['fecha']));
                    $PRODUCTOjson[] = number_format($v['total'], 2);

                    $montoancelado = $montoancelado = number_format(floatval($v['dec_credito_montodebito']), 2);

                    $PRODUCTOjson[] = $montoancelado;

                    //Este es liquidacion pero fue cambiado por el nombre del vendedor
                    //$PRODUCTOjson[] = number_format($v['confirmar'], 2);
                    $PRODUCTOjson[] = $v['vendedor'];

                    $days = (strtotime(date('d-m-Y')) - strtotime($v['fecha'])) / (60 * 60 * 24);
                    if ($days < 0)
                        $days = 0;

                    $label = "<div><label class='label ";
                    if (floor($days) < 8) {
                        $label .= "label-success";
                    } elseif (floor($days) < 31) {
                        $label .= "label-warning";
                    } else {
                        $label .= "label-danger";
                    }
                    $label .= "'>" . floor($days) . "</label></div>";
                    $PRODUCTOjson[] = $label;

                    if ($v['var_credito_estado'] == CREDITO_ACUENTA) {
                        $PRODUCTOjson[] = "A Cuenta";
                    } elseif ($v['var_credito_estado'] == CREDITO_CANCELADO) {
                        $PRODUCTOjson[] = utf8_encode("Cancelado");
                    } elseif ($v['var_credito_estado'] == CREDITO_DEBE) {
                        $PRODUCTOjson[] = "DB";
                    } else {
                        $PRODUCTOjson[] = utf8_encode("Nota de Crédito");
                    }


                    $botonas = '<div class="btn-group"><a class=\'btn btn-default tip\' title="Ver Venta" onclick="visualizar(' . $v["venta_id"] . ')"><i
								class="fa fa-search"></i> Historial</a>';

                    $botonas .= '</div>';
                    $PRODUCTOjson[] = $botonas;
                    $array['productosjson'][] = $PRODUCTOjson;

                }
            }
            $array['data'] = $array['productosjson'];
            $array['draw'] = $draw;//esto debe venir por post
            $array['recordsTotal'] = $total[0]['total'];
            $array['recordsFiltered'] = $total[0]['total']; // esto dbe venir por post

            echo json_encode($array);
        } else {
            redirect(base_url() . 'venta/', 'refresh');
        }
    }


    function lst_reg_estadocuenta()
    {
        if ($this->input->is_ajax_request()) {
            $result = array();

            $this->load->view('menu/ventas/tbl_listareg_estaodcuenta', $result);
        } else {
            redirect(base_url() . 'venta/', 'refresh');
        }
    }

    function deudasElevadas()
    {
        $data = "";
        $data["lstTrabajador"] = $this->venta_model->get_ventas_user();
        $data["zonas"] = $this->zona_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/reportes/deudasElevadas', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function lst_reg_deudasElevadas()
    {
        if ($this->input->is_ajax_request()) {

            if ($this->input->post('cboTrabajador', true) != -1) {
                $where = array('venta.id_vendedor' => $this->input->post('cboTrabajador', true));
            }
            if ($this->input->post('cboZona', true) != -1) {
                $where = array('zonas.zona_id' => $this->input->post('cboZona', true));
            }
            if ($_POST['fecIni'] != "") {
                $where['fecha >= '] = date('Y-m-d', strtotime($this->input->post('fecIni')));
            }
            if ($_POST['fecFin'] != "") {
                $where['fecha <= '] = date('Y-m-d', strtotime($this->input->post('fecFin')));
            }
            if (empty($where)) {
                $where = false;
            }
            ////////////////////////
            $nombre_or = false;
            $where_or = false;
            ///////////////////////
            $nombre_in[0] = 'var_credito_estado';
            $where_in[0] = array('DEBE', 'A_CUENTA');
            $nombre_in[1] = 'venta_status';
            $where_in[1] = array(PEDIDO_ENTREGADO, PEDIDO_DEVUELTO, COMPLETADO);
            ///////////////////////
            $select = 'venta.venta_id, venta.id_cliente,venta.id_vendedor, razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones,usuario.nombre,zonas.zona_nombre';
            $from = "venta";
            $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'usuario', 'zonas');
            $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
                'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago',
                'usuario.nUsuCodigo=venta.id_vendedor', 'zonas.zona_id=cliente.id_zona');
            $tipo_join = false;

            $result['lstVenta'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");
            // var_dump($result);

            $this->load->view('menu/ventas/tbl_listareg_deudaselevadas', $result);
        } else {
            redirect(base_url() . 'venta/', 'refresh');
        }
    }


    public function verVentaCredito()
    {
        $idventa = $this->input->post('idventa');


        //echo "idventa " . $idventa;
        if ($idventa != FALSE) {

            $result['ventas'] = $this->venta_model->obtener_venta($idventa);
            $result['consolidado_detalle'] = $this->consolidado_model->get_detalle_by(array('pedido_id' => $idventa));
            $result['credito'] = $this->venta_model->get_credito_by_venta($idventa);

            $select = 'historial_pagos_clientes.*, credito.*, usuario.nombre';
            $from = "historial_pagos_clientes";
            $join = array('credito', 'usuario');
            $campos_join = array('credito.id_venta=historial_pagos_clientes.credito_id', 'usuario.nUsuCodigo=historial_pagos_clientes.historial_usuario');
            $tipo_join[0] = "";
            $tipo_join[1] = "left";
            $where = array('credito_id' => $idventa);
            $result['historial'] = $this->historial_pagos_clientes_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where, false, false, false, false, false, false, "RESULT_ARRAY");

            $this->load->view('menu/ventas/visualizar_venta_credito', $result);
        }
    }


    function imprimir_pago_pendiente()
    {

        if ($this->input->is_ajax_request()) {

            $id_historial = json_decode($this->input->post('id_historial', true));
            $id_venta = json_decode($this->input->post('id_venta', true));


            $select = '*';
            $from = "historial_pagos_clientes";
            $join = array('credito');
            $campos_join = array('credito.id_venta=historial_pagos_clientes.credito_id');
            // var_dump($id_historial);
            $where = array('historial_id' => $id_historial);
            $result['credito'] = $this->historial_pagos_clientes_model->traer_by($select, $from, $join, $campos_join, false, $where, false, false, false, false, false, false, "RESULT_ARRAY");

            $select = '*';
            $from = "venta";
            $join = array('cliente', 'documento_venta', '(SELECT c.cliente_id, c.tipo, c.valor as direccion, c.principal, COUNT(*) FROM cliente_datos c WHERE c.tipo =1 GROUP BY c.cliente_id, c.tipo ) cli_dat', '(SELECT c1.cliente_id, c1.tipo, c1.valor as telefono1, c1.principal, COUNT(*) FROM cliente_datos c1 WHERE c1.tipo =2 GROUP BY c1.cliente_id, c1.tipo  ) cli_dat2');
            $campos_join = array('cliente.id_cliente=venta.id_cliente', 'venta.numero_documento=documento_venta.id_tipo_documento', 'cli_dat.cliente_id = cliente.id_cliente', 'cli_dat2.cliente_id = cliente.id_cliente');
            $where = array(
                'venta_id' => $id_venta
            );

            $tipo_join = array(null, null, null, 'left');


            $result['cliente'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where, false, false, false, false, false, false, "ROW_ARRAY");
            $result['metodo_pago'] = $this->metodos_pago_model->get_by('id_metodo', $result['credito'][0]['historial_tipopago']);
            // var_dump($result['credito']);
            $result['cuota'] = $result['credito'][0]['historial_monto'];
            // var_dump($result['cuota']);
            $result['id_historial'] = true;
            //var_dump($result['cliente']);
            //////////////////////////////////////////////////////////////////////////busco lo que resta de deuda
            $where = array(
                'credito_id' => $result['credito'][0]['id_venta'],
                'historial_id' => $id_historial
            );
            $select = 'monto_restante';
            $from = "historial_pagos_clientes";
            $order = "historial_fecha desc";
            $buscar_restante = $this->venta_model->traer_by($select, $from, false, false, false, $where, false, false, false, false, false, $order, "RESULT_ARRAY");

            $result['restante'] = $buscar_restante[0]['monto_restante'];
            //var_dump($result);
            $this->load->view('menu/ventas/visualizarCuentaPendiente', $result);
        }


    }

    public function verVenta()
    {
        $idventa = $this->input->post('idventa');
        $result['ventas'] = array();
        if ($idventa != FALSE) {
            $result['notasdentrega'][]['ventas'] = $this->venta_model->obtener_venta_backup($idventa);
            $where = array('consolidado_detalle.pedido_id' => $idventa);
            $result['detalleC'] = $this->consolidado_model->get_detalle_by($where);
            $result['id_venta'] = $idventa;
            $result['retorno'] = 'venta/consultar';
            $this->load->view('menu/ventas/visualizarVenta', $result);
        }
    }

    public function rtfNotaDeEntrega($id = null, $tipo)
    {

        if ($tipo == 'VENTA') {
            $result['notasdentrega'][]['ventas'] = $this->venta_model->obtener_venta_backup($id);
            $where = array('consolidado_detalle.pedido_id' => $id);
            $result['detalleC'] = $this->consolidado_model->get_detalle_by($where);
        } else {

            $where = array('consolidado_detalle.consolidado_id' => $id);
            $result['detalleC'] = $this->consolidado_model->get_detalle_by($where);
            $result['notasdentrega'] = array();
            foreach ($result['detalleC'] as $pedido) {
                $id_pedido = $pedido['pedido_id'];
                if ($id != FALSE) {
                    $result['retorno'] = 'consolidadodecargas';
                    $result['id_venta'] = $id_pedido;
                    $result['notasdentrega'][]['ventas'] = $this->venta_model->obtener_venta_backup($id_pedido);
                }
            }
        }
        //$html = $this->load->view('menu/reportes/rtfNotaDeEntrega', $result,true);
        $notasdentrega = $result['notasdentrega'];

        // documento
        $phpword = new \PhpOffice\PhpWord\PhpWord();
        $styles = array(
            'pageSizeW' => '12755.905511811',
            'pageSizeH' => '7937.007874016',
            'marginTop' => '566.929133858',
            'marginLeft' => '866.858267717',
            'marginRight' => '866.929133858',
            'marginBottom' => '283.464566929',
        );


        $phpword->addFontStyle('rStyle', array('size' => 15, 'allCaps' => true, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));
        $phpword->addParagraphStyle('pStyle', array('align' => 'center', 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));
        $styleTable = array('borderSize' => 6, 'borderColor' => '999999', 'width' => 50 * 100);
        $tablastyle = array('width' => 50 * 100, 'unit' => 'pct', 'align' => 'left');
        if (isset($notasdentrega[0])) {
            $i = 0;
            foreach ($notasdentrega as $nota) {
                $section = $phpword->addSection($styles);
                $header = $section->addHeader();

                if (isset($nota['ventas'][0])) {
                    $ventas[0] = $nota['ventas'][0];

                    // tabla titulos
                    $table = $header->addTable($tablastyle);
                    $cell = $table->addRow(650, array('exactHeight' => true))->addCell(3000, array('valign ' => 'center', 'align' => 'center'));

                    $cell->addText(htmlspecialchars('NOTA DE ENTREGA '), 'rStyle', 'pStyle');

                    $header->addTableStyle('Border', $styleTable);
                    $innerCell = $table->addCell(2000, array('align' => 'right'))->addTable($styleTable)->addRow(200)->addCell(3000, array('align' => 'center'));
                    $innerCell->addText(htmlspecialchars('NOTA DE ENTREGA Nº'), array('size' => 12, 'align ' => 'center'), 'pStyle');
                    $innerCell->addText((isset($ventas[0]['serie']) AND isset($ventas[0]['numero'])) ? $ventas[0]['serie'] . $ventas[0]['numero'] : '', array('size' => 12, 'align ' => 'center'), 'pStyle');
                    if (isset($result['detalleC'][0])) {
                        $table->addCell()->addText(htmlspecialchars("CGC: " + $result['detalleC'][0]['consolidado_id']));
                    }

                    $header->addTextBreak(1);
                    // tabla de datos basicos

                    $phpword->addFontStyle('rBasicos', array('size' => 8, 'allCaps' => true, 'spaceBefore' => 0, 'spaceAfter' => 0, 'spacing' => 0));
                    $table1 = $header->addTable($tablastyle);
                    $cell = $table1->addRow(150, array('exactHeight' => true))->addCell(566);
                    $cell->addText(htmlspecialchars('CLIENTE'), 'rBasicos');
                    $table1->addCell(7000)->addText(htmlspecialchars(strtoupper($ventas[0]['cliente'])), 'rBasicos');

                    $table1->addCell(4000)->addText(htmlspecialchars('COD. CLIE: ' . $ventas[0]['cliente_id']), 'rBasicos');
                    $table1->addCell(4000)->addText(htmlspecialchars('F. EMISION: ' . date('Y-m-d', strtotime($ventas[0]['fechaemision']))), 'rBasicos');
                    $table1->addCell(4000)->addText(htmlspecialchars('USUA: ' . strtoupper($ventas[0]['vendedor'])), 'rBasicos');

                    $table1->addRow(150, array('exactHeight' => true))->addCell(566)->addText(htmlspecialchars('DIRECCION: '), 'rBasicos');
                    $table1->addCell(7000, array('gridSpan' => 2))->addText(htmlspecialchars((isset($ventas[0]['clienteDireccion'])) ? strtoupper($ventas[0]['clienteDireccion']) : ''), 'rBasicos');

                    $table1->addCell(4000)->addText(htmlspecialchars('F. VENC.: ' . (isset($result['detalleC'][0]) ? date('Y-m-d', strtotime($result['detalleC'][0]['fecha'])) : '')), 'rBasicos');
                    $table1->addCell(4000)->addText(htmlspecialchars('HORA: ' . (isset($result['detalleC'][0]) ? date('H:i:s', strtotime($result['detalleC'][0]['fecha'])) : '')), 'rBasicos');

                    $table1->addRow(150, array('exactHeight' => true))->addCell(566)->addText(htmlspecialchars('CONTACTO: '), 'rBasicos');
                    $table1->addCell(7000)->addText(htmlspecialchars(((isset($ventas[0]['representanteCliente'])) ? strtoupper($ventas[0]['representanteCliente']) : '')), 'rBasicos');
                    $table1->addCell(4000)->addText((htmlspecialchars('TELEFONO: ' . (isset($ventas[0]['telefonoC1']) ? $ventas[0]['telefonoC1'] : ''))), 'rBasicos');

                    $table1->addCell(4000)->addText(htmlspecialchars('COND. VENTA:' . strtoupper($ventas[0]['nombre_condiciones'])), 'rBasicos');
                    $table1->addCell(4000)->addText(htmlspecialchars('VEND.:' . ((isset($ventas[0]['id_vendedor'])) ? $ventas[0]['id_vendedor'] : '')), 'rBasicos');

                    $header->addTextBreak(1);
                    $table1 = $section->addTable($tablastyle);
                    $table1->addRow(200, array('exactHeight' => true, 'tblHeader' => true))->addCell(1000, array('valign ' => 'bottom'))->addText(htmlspecialchars('CODIGO'), 'rBasicos');
                    $table1->addCell(9000)->addText(htmlspecialchars('DESCRIPCION'), 'rBasicos');
                    $table1->addCell(2000)->addText(htmlspecialchars('PRESENTACION'), 'rBasicos');
                    $table1->addCell(1500)->addText(htmlspecialchars('CANTIDAD'), 'rBasicos');
                    $table1->addCell(1500)->addText(htmlspecialchars('PREC. UNIT.'), 'rBasicos');
                    $table1->addCell(1000)->addText(htmlspecialchars('TOTAL'), 'rBasicos');
                    $table1->addRow(250, array('exactHeight' => true, 'tblHeader' => true))->addCell(null, array('valign' => 'top', 'gridSpan' => 6))
                        ->addText('___________________________________________________________________________________________________');

                    // tabla de productos
                    $table1 = $section->addTable($tablastyle);
                    foreach ($nota['ventas'] as $venta) {
                        $um = isset($venta['abreviatura']) ? $venta['abreviatura'] : $venta['nombre_unidad'];
                        $cantidad_entero = intval($venta['cantidad'] / 1) > 0 ? intval($venta['cantidad'] / 1) : '';
                        $cantidad_decimal = fmod($venta['cantidad'], 1);

                        $cantidad = $cantidad_entero;

                        if ($cantidad_decimal > 0) {
                            if (!empty($cantidad_entero)) {
                                $cantidad = $cantidad_entero . "." . $cantidad_decimal;

                            } else
                                $cantidad = $cantidad_decimal;

                            if ($cantidad_decimal == 0.25 or $cantidad_decimal == 0.250)
                                $cantidad = $cantidad_entero . " " . '1/4';
                            if ($cantidad_decimal == 0.5 or $cantidad_decimal == 0.50 or $cantidad_decimal == 0.500)
                                $cantidad = $cantidad_entero . " " . '1/2';
                            if ($cantidad_decimal == 0.75 or $cantidad_decimal == 0.750)
                                $cantidad = $cantidad_entero . " " . '3/4';
                        }


                        if ($venta['producto_cualidad'] == 'MEDIBLE') {

                            if ($venta['unidades'] == 12 or $venta['orden'] == 1) {
                                $cantidad = floatval($venta['cantidad']);

                            } else {
                                $cantidad = floatval($venta['cantidad'] * $venta['unidades']);
                                $um = $venta['unidad_minima'];
                            }
                        }

                        $table1->addRow(150, array('exactHeight' => true));
                        $table1->addCell(1000)->addText(htmlspecialchars($venta['producto_id']), 'rBasicos');
                        $table1->addCell(9000)->addText(htmlspecialchars(strtoupper($venta['nombre']) . (($venta['bono'] == 1) ? ' --- BONIFICACION' : '')), 'rBasicos');
                        $table1->addCell(2000)->addText(htmlspecialchars(strtoupper($venta['presentacion'])), 'rBasicos');
                        $table1->addCell(1500)->addText($cantidad . " " . $um, 'rBasicos');
                        $table1->addCell(1500)->addText($venta['preciounitario'], 'rBasicos');
                        $table1->addCell(1000)->addText($venta['importe'], 'rBasicos');

                    }
                    $footer = $section->addFooter();
                    // $footer->addTextBreak(1);
                    $table1 = $footer->addTable($tablastyle);
                    $table1->addRow(150, array('exactHeight' => true))->addCell(9000, array('gridSpan' => 3))->addText(htmlspecialchars('SON:' . MONEDA . $this->numtoletras($ventas[0]['montoTotal'] * 10 / 10)), 'rBasicos');

                    $table1->addRow(150, array('exactHeight' => true))->addCell(4900)->addText(htmlspecialchars('*CANJEAR POR BOLETA O FACTURA '), 'rBasicos');
                    $table1->addCell()->addText(htmlspecialchars('____________________________ '), 'rBasicos', 'pStyle');
                    $table1->addCell(2000);
                    // $section->addTextBreak(1);
                    $table1->addRow(150, array('exactHeight' => true))->addCell(4900)->addText(htmlspecialchars(' *GRACIAS POR SU COMPRA. VUELVA PRONTO'), 'rBasicos');
                    $table1->addCell(7000)->addText(htmlspecialchars('RECIBO CONFORME'), 'rBasicos', 'pStyle');
                    $table1->addCell(2000)->addText(htmlspecialchars('Total: ' . MONEDA . ' ' . ceil($ventas[0]['montoTotal'] * 10) / 10), 'rBasicos');

                    // $section->addTextBreak(1);
                }


            }
        }


        $file = 'NotaDeEntrega' . $id . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007');
        $xmlWriter->save("php://output");

    }

    public function rtfBoleta($id, $tipo)
    {

        if ($tipo == 'VENTA') {

            $result = $this->venta_model->documentoVenta($id);
            $result['id_venta'] = $id;
            if ($result['ventas'][0]['descripcion'] != FACTURA) {

                $nombre = 'BOLETA';
                $result['boletas'][0] = $result;
                //  $html = $this->load->view('menu/reportes/rtfVentasBoletas', $result, true);
            }


        } else {
            $c = 0;
            $where = array('consolidado_detalle.consolidado_id' => $id);
            $result['detalleC'] = $this->consolidado_model->get_detalle_by($where);
            $result['boletas'] = array();
            foreach ($result['detalleC'] as $pedido) {
                $id_pedido = $pedido['pedido_id'];
                if ($id != FALSE) {
                    if ($pedido['documento_tipo'] != FACTURA) {
                        $result['id_venta'] = $id_pedido;
                        $boletas = $this->venta_model->documentoVenta($id_pedido);

                        // var_dump($boletas['productos']);
                        $result['boletas'][] = $boletas;
                        $c++;
                    } else {

                    }
                }
            }
            if ($c >= 1) {

            } else {
                $result['boletas'] = "";

            }
        }


        //$html = $this->load->view('menu/reportes/rtfNotaDeEntrega', $result,true);
        $boletas = $result['boletas'];

        // documento
        $phpword = new \PhpOffice\PhpWord\PhpWord();
        $styles = array(
            'pageSizeW' => '7256.692913386',
            'pageSizeH' => '8798.74015748',
            'marginTop' => '396.850393701',
            'marginLeft' => '170.078740157',
            'marginRight' => '170.078740157',
            'marginBottom' => '396.850393701',
        );
        $section = $phpword->addSection($styles);

        $phpword->addFontStyle('rStyle', array('size' => 18, 'allCaps' => true));
        $phpword->addParagraphStyle('pStyle', array('align' => 'center'));
        $phpword->addFontStyle('rBasicos', array('size' => 7, 'allCaps' => true));
        $tablastyle = array('width' => 50 * 100, 'unit' => 'pct', 'align' => 'left');

        if (isset($boletas[0])) {
            foreach ($boletas as $boleta) {
                foreach ($boleta['ventas'] as $venta) {
                    $totalboleta = 0;

                    // tabla titulos
                    $table = $section->addTable($tablastyle);
                    $table->addRow()->addCell(5000, array('valign ' => 'center', 'align' => 'center'));

                    $innerCell = $table->addCell(4000, array('align' => 'right'))->addTable()->addRow()->addCell(3000, array('align' => 'center'));
                    $innerCell->addText($venta['serie'] . "-" . $venta['numero'], array('size' => 12, 'align ' => 'center'), 'pStyle');
                    $section->addTextBreak(1);

                    // tabla de datos basicos

                    $table1 = $section->addTable($tablastyle);
                    $table1->addRow(100);
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['cliente'])), 'rBasicos');
                    $table1->addRow(100);
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['direccion_cliente'])), 'rBasicos');
                    $table1->addRow(100);
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['direccion_cliente'])), 'rBasicos');


                    $section->addTextBreak(1);
                    $table1 = $section->addTable($tablastyle);
                    $table1->addRow(200);
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addRow(200);
                    $table1->addCell()->addText(htmlspecialchars($venta['serie'] . "-" . $venta['numero']), 'rBasicos');
                    $table1->addCell()->addText(htmlspecialchars($venta['nombre_condiciones']), 'rBasicos');
                    $table1->addCell()->addText();
                    $table1->addCell()->addText();
                    $table1->addCell()->addText(date('Y-m-d', strtotime($venta['fechaemision'])), 'rBasicos');
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['vendedor'])), 'rBasicos');
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['cliente'])), 'rBasicos');


                    // tabla de productos
                    $section->addTextBreak(1);
                    $table1 = $section->addTable($tablastyle);
                    $table1->addRow(200)->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();

                    foreach ($venta['productos'] as $producto) {
                        $totalboleta = $totalboleta + $producto['importe'];

                        if ($venta['documento_id'] == $producto['documento_id']) {
                            $um = isset($producto['abreviatura']) ? $producto['abreviatura'] : $producto['nombre_unidad'];
                            $cantidad_entero = intval($producto['cantidad'] / 1) > 0 ? intval($producto['cantidad'] / 1) : '';
                            $cantidad_decimal = fmod($producto['cantidad'], 1);

                            $cantidad = $cantidad_entero;

                            if ($cantidad_decimal > 0) {
                                if (!empty($cantidad_entero)) {
                                    $cantidad = $cantidad_entero . "." . $cantidad_decimal;
                                } else
                                    $cantidad = $cantidad_decimal;

                                if ($cantidad_decimal == 0.25 or $cantidad_decimal == 0.250)
                                    $cantidad = $cantidad_entero . " " . '1/4';
                                if ($cantidad_decimal == 0.5 or $cantidad_decimal == 0.50 or $cantidad_decimal == 0.500)
                                    $cantidad = $cantidad_entero . " " . '1/2';
                                if ($cantidad_decimal == 0.75 or $cantidad_decimal == 0.750)
                                    $cantidad = $cantidad_entero . " " . '3/4';
                            }

                            if ($producto['producto_cualidad'] == 'MEDIBLE') {
                                if ($producto['unidades'] == 12 || $producto['orden'] == 1) {
                                    $cantidad = floatval($producto['cantidad']);
                                } else {
                                    $cantidad = floatval($producto['cantidad'] * $producto['unidades']);
                                    $um = $producto['unidad_minima'];
                                }
                            }

                            $table1->addRow(150, array('exactHeight' => true));
                            $table1->addCell()->addText(htmlspecialchars($producto['ddproductoID']), 'rBasicos');
                            $table1->addCell()->addText(htmlspecialchars(strtoupper($producto['nombre']) . ($producto['importe'] == 0 ? ' --- BONIFICACION' : '')), 'rBasicos');
                            $table1->addCell()->addText($um, 'rBasicos');
                            $table1->addCell()->addText($cantidad, 'rBasicos');
                            $table1->addCell()->addText($producto['preciounitario'], 'rBasicos');
                            $table1->addCell()->addText(ceil($producto['importe'] * 10) / 10, 'rBasicos');
                        }

                    }

                    $table1->addRow(200)->addCell(null, array('gridSpan' => 5));
                    $table1->addCell()->addText(MONEDA . ceil($totalboleta * 10) / 10, 'rBasicos');
                    $section->addTextBreak(1);
                    $innertable = $section->addTable();
                    $innertable->addRow()->addCell(2000)->addText();
                    $innertable->addCell()->addText(htmlspecialchars($venta['placa']), 'rBasicos');
                    $innertable->addRow()->addCell(2000)->addText(htmlspecialchars($venta['vendedor']), 'rBasicos');
                    $innertable->addCell();

                    $table1->addRow(200);
                    $table1->addCell(2000, array('gridSpan' => 7))->addText();
                    $table1->addRow()->addCell(null, array('gridSpan' => 7))->addText($this->numtoletras(ceil($totalboleta * 10) / 10, 'rBasicos'));
                    $section->addTextBreak(1);

                }
            }
        }


        $file = 'BoletadeVenta' . $id . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007');
        $xmlWriter->save("php://output");
    }


    public function rtfFactura($id, $tipo)
    {

        if ($tipo == 'VENTA') {

            $result = $this->venta_model->documentoVenta($id);
            $result['id_venta'] = $id;
            if ($result['ventas'][0]['descripcion'] == FACTURA) {

                $nombre = 'BOLETA';
                $result['boletas'][0] = $result;
                //  $html = $this->load->view('menu/reportes/rtfVentasBoletas', $result, true);
            }


        } else {
            $c = 0;
            $where = array('consolidado_detalle.consolidado_id' => $id);
            $result['detalleC'] = $this->consolidado_model->get_detalle_by($where);
            $result['boletas'] = array();
            foreach ($result['detalleC'] as $pedido) {
                $id_pedido = $pedido['pedido_id'];
                if ($id != FALSE) {
                    if ($pedido['documento_tipo'] == FACTURA) {
                        $result['id_venta'] = $id_pedido;
                        $boletas = $this->venta_model->documentoVenta($id_pedido);

                        // var_dump($boletas['productos']);
                        $result['boletas'][] = $boletas;
                        $c++;
                    } else {

                    }
                }
            }
            if ($c >= 1) {

            } else {
                $result['boletas'] = "";

            }
        }


        //$html = $this->load->view('menu/reportes/rtfNotaDeEntrega', $result,true);
        $boletas = $result['boletas'];

        // documento
        $phpword = new \PhpOffice\PhpWord\PhpWord();
        $styles = array(
            'pageSizeW' => '12812.598425197',
            'pageSizeH' => '8617.322834646',
            'marginTop' => '396.850393701',
            'marginLeft' => '113.385826772',
            'marginRight' => '113.385826772',
            'marginBottom' => '340.157480315',
        );
        $section = $phpword->addSection($styles);

        $phpword->addFontStyle('rStyle', array('size' => 18, 'allCaps' => true));
        $phpword->addParagraphStyle('pStyle', array('align' => 'center'));
        $phpword->addFontStyle('rBasicos', array('size' => 7, 'allCaps' => true));
        $tablastyle = array('width' => 50 * 100, 'unit' => 'pct', 'align' => 'left');
        $phpword->addParagraphStyle('totales', array('align' => 'right'));
        if (isset($boletas[0])) {
            foreach ($boletas as $boleta) {
                foreach ($boleta['ventas'] as $venta) {


                    // tabla titulos
                    $table = $section->addTable($tablastyle);
                    $table->addRow()->addCell(5000, array('valign ' => 'center', 'align' => 'center'));

                    $innerCell = $table->addCell(4000, array('align' => 'right'))->addTable()->addRow()->addCell(3000, array('align' => 'center'));
                    $innerCell->addText($venta['serie'] . "-" . $venta['numero'], array('size' => 12, 'align ' => 'center'), 'pStyle');
                    $section->addTextBreak(1);

                    // tabla de datos basicos

                    $table1 = $section->addTable($tablastyle);
                    $table1->addRow(100);
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['cliente'])), 'rBasicos');
                    $table1->addRow(100);
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['direccion_cliente'])), 'rBasicos');
                    $table1->addRow(100);
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['direccion_cliente'])), 'rBasicos');


                    $section->addTextBreak(1);
                    $table1 = $section->addTable($tablastyle);
                    $table1->addRow(200);
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addRow(200);
                    $table1->addCell()->addText(htmlspecialchars($venta['serie'] . "-" . $venta['numero']), 'rBasicos');
                    $table1->addCell()->addText(htmlspecialchars($venta['nombre_condiciones']), 'rBasicos');
                    $table1->addCell()->addText();
                    $table1->addCell()->addText();
                    $table1->addCell()->addText(date('Y-m-d', strtotime($venta['fechaemision'])), 'rBasicos');
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['vendedor'])), 'rBasicos');
                    $table1->addCell()->addText(htmlspecialchars(strtoupper($venta['cliente'])), 'rBasicos');


                    // tabla de productos
                    $section->addTextBreak(1);
                    $table1 = $section->addTable($tablastyle);
                    $table1->addRow(200)->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();
                    $table1->addCell();

                    foreach ($venta['productos'] as $producto) {


                        if ($venta['documento_id'] == $producto['documento_id']) {
                            $um = isset($producto['abreviatura']) ? $producto['abreviatura'] : $producto['nombre_unidad'];
                            $cantidad_entero = intval($producto['cantidad'] / 1) > 0 ? intval($producto['cantidad'] / 1) : '';
                            $cantidad_decimal = fmod($producto['cantidad'], 1);

                            $cantidad = $cantidad_entero;

                            if ($cantidad_decimal > 0) {
                                if (!empty($cantidad_entero)) {
                                    $cantidad = $cantidad_entero . "." . $cantidad_decimal;
                                } else
                                    $cantidad = $cantidad_decimal;

                                if ($cantidad_decimal == 0.25 or $cantidad_decimal == 0.250)
                                    $cantidad = $cantidad_entero . " " . '1/4';
                                if ($cantidad_decimal == 0.5 or $cantidad_decimal == 0.50 or $cantidad_decimal == 0.500)
                                    $cantidad = $cantidad_entero . " " . '1/2';
                                if ($cantidad_decimal == 0.75 or $cantidad_decimal == 0.750)
                                    $cantidad = $cantidad_entero . " " . '3/4';
                            }

                            if ($producto['producto_cualidad'] == 'MEDIBLE') {
                                if ($producto['unidades'] == 12 || $producto['orden'] == 1) {
                                    $cantidad = floatval($producto['cantidad']);
                                } else {
                                    $cantidad = floatval($producto['cantidad'] * $producto['unidades']);
                                    $um = $producto['unidad_minima'];
                                }
                            }

                            $table1->addRow(200);
                            $table1->addCell()->addText(htmlspecialchars($producto['ddproductoID']), 'rBasicos');
                            $table1->addCell()->addText(htmlspecialchars(strtoupper($producto['nombre']) . ($producto['importe'] == 0 ? ' --- BONIFICACION' : '')), 'rBasicos');
                            $table1->addCell()->addText($um, 'rBasicos');
                            $table1->addCell()->addText($cantidad, 'rBasicos');
                            $table1->addCell()->addText($producto['precioV'], 'rBasicos', 'totales');
                            $table1->addCell()->addText(0.00, 'rBasicos', 'totales');
                            $table1->addCell()->addText(ceil($producto['importe'] * 10) / 10, 'rBasicos', 'totales');
                        }


                    }


                    $table1->addRow(500)->addCell(null, array('gridSpan' => 6));
                    $table1->addCell()->addText(MONEDA . ceil($venta['subTotal'] * 10) / 10, 'rBasicos', 'totales');
                    $table1->addRow(500)->addCell(null, array('gridSpan' => 6));
                    $table1->addCell()->addText(MONEDA . ceil($venta['impuesto'] * 10) / 10, 'rBasicos', 'totales');
                    $table1->addRow(500)->addCell(null, array('gridSpan' => 6));
                    $table1->addCell()->addText(MONEDA . ceil($venta['montoTotal'] * 10) / 10, 'rBasicos', 'totales');


                    $table1->addRow(500);
                    $table1->addCell(null, array('gridSpan' => 7))->addText();
                    $table1->addRow()->addCell(null, array('gridSpan' => 7))->addText($this->numtoletras(ceil($venta['montoTotal'] * 10) / 10, 'rBasicos'));


                    $section->addPageBreak();

                }


            }
        }


        $file = 'Factura' . $id . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007');
        $xmlWriter->save("php://output");
    }

    public function verDocumentoFisal()
    {
        $idventa = $this->input->post('idventa');
        if ($idventa != FALSE) {
            $result = $this->venta_model->documentoVenta($idventa);
            $result['id_venta'] = $idventa;
            if ($result['ventas'][0]['descripcion'] == FACTURA) {
                $result['descripcion'] = FACTURA;
                $result['facturas'][0] = $result;
                $this->load->view('menu/ventas/visualizarVentas', $result);
            } else {
                $result['descripcion'] = 'BOLETA';
                $result['boletas'][0] = $result;
                $this->load->view('menu/ventas/visualizarVentasBoletas', $result);
            }

        }
    }

    public function verVentaJson()
    {
        $idventa = $this->input->post('idventa');

        if ($idventa != FALSE) {
            $result['ventas'] = $this->venta_model->obtener_venta($idventa);
            $this->output
                ->set_content_type('application/json')
                ->set_output(json_encode($result));
        }
    }

    public function vercredito()
    {
        $idventa = $this->input->post('idventa');

        //echo "idventa " . $idventa;
        //var_dump($idventa);
        if ($idventa != FALSE) {

            //   $result['venta'] = $this->venta_model->obtener_venta($idventa);
            $result['metodos'] = $this->metodos_pago_model->get_all();
            $result['credito'] = $this->venta_model->get_credito_by_venta($idventa);
            $result['id_venta'] = $idventa;
            /* $this->output
                 ->set_content_type('application/json')
                 ->set_output(json_encode(array('aaData' => $result)));*/

            //var_dump($result);
            $this->load->view('menu/ventas/tbl_venta_credito_pago', $result);
        }
    }

    public function cargarCamion()
    {
        if ($this->input->post('id_consolidado') != "") {
            $id = $this->input->post('id_consolidado');
            $where = array('consolidado_id' => $id);
            $data['consolidado'] = $this->consolidado_model->get_consolidado_by($where);
        }
        $data['camiones'] = $this->camiones_model->get_all();
        $data['metros'] = $this->input->post('metros_c');
        $data['pedidos'] = $_POST["pedidos"];
        $this->load->view('menu/ventas/formCamiones', $data);
    }

    public
    function obtenerMetros()
    {
        $id_camion = $this->input->post('id_camion');
        $data['carga'] = $this->camiones_model->get_by('camiones_id', $id_camion);

        if ($this->input->is_ajax_request()) {
            // $msg = ['success' => 'un mensaje','error'=> $this->form->validation_errors()];
            //echo json_encode($msg);
            $metro_cubico = $data['carga']['metros_cubicos'];
            //$datos = ['capacidad' => $metro_cubico];
            echo $metro_cubico;
        }
    }

    public
    function historial_liquidacion()
    {
        $data = "";
        $data['vendedores'] = $this->usuario_model->select_all_user();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/historial_liquidacion', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    public
    function pagosadelantados()
    {
        $data = "";
        //  $where = array('venta_tipo' => "ENTREGA", 'pagado > ' => 0, 'consolidado_carga.status'=>'CERRADO');
        $where = "venta_tipo ='ENTREGA' and venta.pagado>0 and (consolidado_carga.status IN ('CERRADO','ABIERTO','IMPRESO') or consolidado_carga.status IS NULL)";
        $data['pagos'] = $this->venta_model->pagos_adelantados($where);

        $data['ltsVendedores'] = $this->venta_model->get_ventas_user();
        $data['ltsClientes'] = $this->cliente_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/pagosadelantados', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function verPagoAdelantado($id)
    {
        $data = "";
        $where = array('venta_id' => $id);
        $data['pago'] = $this->venta_model->pagos_adelantados($where);

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/verPagoAdelantado', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function pagoCaja($id)
    {
        $data = "";
        $where = array('venta_id' => $id);
        $data['pago'] = $this->venta_model->pagos_adelantados($where);
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/confirmarPagoCaja', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function pagoBanco($id)
    {
        $data = "";
        $where = array('venta_id' => $id);
        $data['pago'] = $this->venta_model->pagos_adelantados($where);
        $data['banco'] = $this->banco_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/confirmarPagoBanco', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function pagoCajaCobrado()
    {
        $data = "";
        $where = array('venta_tipo' => 'ENTREGA', 'pagado > ' => 0);
        $monto = $this->input->post('monto');
        $id = $this->input->post('id');
        $user = $this->session->userdata('nUsuCodigo');
        $caja = $this->session->userdata('caja');

        if ($caja == '') {
            $json['error'] = 'El usuario no tine caja asignada';
        } else {
            $fecha = date('Y-m-d H:i:s');
            $datos = array(
                'confirmacion_caja' => $caja,
                'confirmacion_fecha' => $fecha,
                'confirmacion_usuario' => $user,
            );

            $result = $data['pago'] = $this->venta_model->pagos_caja_cobrado($datos, $id, $monto);


            if ($result != FALSE) {

                $json['success'] = 'success';


            } else {

                $json['error'] = 'Ha ocurrido un error al confirmar el pago';
            }
        }
        echo json_encode($json);


    }

    function pagoBancoCobrado()
    {
        $data = "";
        $id = $this->input->post('id');
        $monto = $this->input->post('monto');
        $banco = $this->input->post('banco');
        $user = $this->session->userdata('nUsuCodigo');
        $fecha = date('Y-m-d H:i:s');
        $datos = array(
            'confirmacion_banco' => $banco,
            'confirmacion_fecha' => $fecha,
            'confirmacion_usuario' => $user,
        );
        $where = array('venta_tipo' => 'ENTREGA', 'pagado > ' => 0);


        $result = $this->venta_model->pagos_banco_cobrado($datos, $id, $monto);
        if ($result != FALSE) {

            $json['success'] = 'success';


        } else {

            $json['error'] = 'Ha ocurrido un error al confirmar';
        }
        echo json_encode($json);

    }


    public function liquidacion()
    {
        $data = "";
        $data['vendedores'] = $this->usuario_model->select_all_user();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/liquidacion', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    public function lst_liquidaciones_confirmadas()
    {
        if ($this->input->is_ajax_request()) {

            $where = array(

                'historial_estatus' => "CONFIRMADO"
            );

            if ($this->input->post('cajero', true) != -1) {
                $where['liquidacion_cobranza.liquidacion_cajero'] = $this->input->post('cajero');
            }

            $where['historial_fecha >='] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha_ini') . " 00:00:00"));
            $where['historial_fecha <='] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha_fin') . " 23:59:59"));

            ////////////////////////
            $nombre_or = false;
            $where_or = false;
            ///////////////////////
            $nombre_in = false;
            $where_in = false;
            ///////////////////////
            $select = 'usuario.nUsuCodigo, usuario.nombre, historial_pagos_clientes.*, liquidacion_fecha,
                liquidacion_cobranza.liquidacion_id,cajero.nombre as cajero';
            $from = "historial_pagos_clientes";
            $join = array('liquidacion_cobranza_detalle', 'liquidacion_cobranza', 'usuario', 'usuario as cajero');
            $campos_join = array(
                'liquidacion_cobranza_detalle.pago_id=historial_pagos_clientes.historial_id',
                'liquidacion_cobranza.liquidacion_id=liquidacion_cobranza_detalle.liquidacion_id',
                'usuario.nUsuCodigo=historial_pagos_clientes.historial_usuario', 'cajero.nUsuCodigo=liquidacion_cobranza.liquidacion_cajero');
            $tipo_join = false;

            $group = "liquidacion_cobranza.liquidacion_id";
            $result['lstVenta'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, $group, false, "RESULT_ARRAY");

            for ($i = 0; $i < count($result['lstVenta']); $i++) {
                $suma = $this->db->select_sum('historial_monto', 'suma')
                    ->from('historial_pagos_clientes')
                    ->join('venta', 'historial_pagos_clientes.credito_id=venta.venta_id')
                    ->join('liquidacion_cobranza_detalle', 'liquidacion_cobranza_detalle.pago_id=historial_pagos_clientes.historial_id')
                    ->where('liquidacion_cobranza_detalle.liquidacion_id', $result['lstVenta'][$i]['liquidacion_id'])
                    ->where('historial_estatus', 'CONFIRMADO')
                    ->get()->row();

                $result['lstVenta'][$i]['suma'] = $suma->suma;
            }

            /// var_dump($result);
            $this->load->view('menu/ventas/tbl_historial_liquidacion', $result);


            //echo json_encode($this->v->select_venta_estadocuenta(date("y-m-d", strtotime($this->input->post('fecIni',true))),date("y-m-d", strtotime($this->input->post('fecFin',true)))));
        } else {
            redirect(base_url() . 'venta/', 'refresh');
        }
    }

    function imprimir_historial_liquidacion()
    {

        $historial = $this->input->post('id_historial');
        $venta_id = $this->input->post('id_venta');

        $liquidacion = $this->input->post('id_liquidacion');
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        $nombre_or = false;
        $where_or = false;
        $nombre_in = false;
        $where_in = false;

        $where = array(

            'liquidacion_cobranza_detalle.liquidacion_id' => $liquidacion,
            'historial_estatus' => "CONFIRMADO"
        );

        $select = 'SUM(historial_monto) AS suma, historial_caja_id, historial_banco_id,historial_estatus,
         cliente.razon_social, direccion,telefono1,
        documento_Serie, documento_Numero, usuario.nombre,liquidacion_fecha, ,cajero.nombre as cajero,
          metodos_pago.*';
        $from = "historial_pagos_clientes";
        $join = array('venta', 'cliente', 'documento_venta', 'metodos_pago', 'liquidacion_cobranza_detalle', 'usuario', 'liquidacion_cobranza', 'usuario as cajero', '(SELECT c.cliente_id, c.tipo, c.valor as direccion, c.principal, COUNT(*) FROM cliente_datos c WHERE c.tipo =1 GROUP BY c.cliente_id, c.tipo ) cli_dat', '(SELECT c1.cliente_id, c1.tipo, c1.valor as telefono1, c1.principal, COUNT(*) FROM cliente_datos c1 WHERE c1.tipo =2 GROUP BY c1.cliente_id, c1.tipo  ) cli_dat2');
        $campos_join = array('historial_pagos_clientes.credito_id=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'metodos_pago.id_metodo=historial_pagos_clientes.historial_tipopago',
            'liquidacion_cobranza_detalle.pago_id=historial_pagos_clientes.historial_id',
            'usuario.nUsuCodigo=historial_pagos_clientes.historial_usuario',
            'liquidacion_cobranza.liquidacion_id=liquidacion_cobranza_detalle.liquidacion_id', 'cajero.nUsuCodigo=liquidacion_cobranza.liquidacion_cajero', 'cli_dat.cliente_id = cliente.id_cliente', 'cli_dat2.cliente_id = cliente.id_cliente');

        $tipo_join = array(null, null, null, null, null, null, null, null, null, 'left');

        $group_by = "nombre_metodo";
        $order = "nombre_metodo";
        $result['resultado'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join,
            $where, $nombre_in, $where_in, $nombre_or, $where_or, $group_by, $order, "RESULT_ARRAY");

        $result['historial'] = true;

        $this->load->view('menu/ventas/visualizarHistorialLiquidacion', $result);

    }

    function filtroPagosAdl()
    {
        $condicion = "venta.venta_tipo` ='ENTREGA' and `venta`.`pagado`>0  and (consolidado_carga.status IN ('CERRADO','ABIERTO','IMPRESO') or consolidado_carga.status IS NULL)";
        if ($this->input->post('pedido') != "") {
            $pedido = $this->input->post('pedido');
            $condicion .= " and `venta_id`='$pedido' ";
            $data['pedido'] = $this->input->post('pedido');
        }
        if ($this->input->post('vendedor') != 0) {
            $vendedor = $this->input->post('vendedor');
            $condicion .= " and `id_vendedor`='$vendedor' ";
            $data['vendedor'] = $this->input->post('vendedor');
        }
        if ($this->input->post('cliente') != 0) {
            $cliente = $this->input->post('cliente');
            $condicion .= " and `venta`.`id_cliente`='$cliente' ";

        }
        if ($this->input->post('fecha') != "") {
            $fecha = $this->input->post('fecha');
            $fecha = date('Y-m-d', strtotime($fecha));
            $condicion .= " and `date(fecha)` >=" . $fecha . " AND `date(fecha)` <= " . $fecha;

        }

        if ($this->input->post('estado') != "") {

            if ($this->input->post('estado') == "CONFIRMADO") {
                $condicion .= " and venta.confirmacion_usuario is not null ";
            } else {
                $condicion .= " and venta.confirmacion_usuario is null";
            }
        }

        //var_dump($condicion);

        $data['pagosAdl'] = $this->venta_model->pagos_adelantados($condicion);

        $this->load->view('menu/ventas/lista_pagos_adelantados', $data);

    }

    public function lst_liquidaciones()
    {
        if ($this->input->is_ajax_request()) {
            if ($this->input->post('vendedor', true) != -1) {
                $where['historial_pagos_clientes.historial_usuario'] = $this->input->post('vendedor');
            }

            $where['historial_estatus'] = "PENDIENTE";

            $where['historial_fecha >='] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha_ini') . " 00:00:00"));
            $where['historial_fecha <='] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha_fin') . " 23:59:59"));

            ////////////////////////
            $nombre_or = false;
            $where_or = false;
            ///////////////////////
            $nombre_in = false;
            $where_in = false;
            ///////////////////////
            $select = 'usuario.nUsuCodigo, usuario.nombre, credito.dec_credito_montodeuda, historial_pagos_clientes.*,
                 metodos_pago.*, documento_venta.documento_Serie, documento_Numero, venta.venta_id,
                 cliente.razon_social';
            $from = "historial_pagos_clientes";
            $join = array('usuario', 'metodos_pago', 'venta', 'cliente', 'documento_venta', 'credito');
            $campos_join = array('usuario.nUsuCodigo=historial_pagos_clientes.historial_usuario',
                'metodos_pago.id_metodo=historial_pagos_clientes.historial_tipopago',
                'venta.venta_id=historial_pagos_clientes.credito_id',
                'venta.id_cliente=cliente.id_cliente',
                'venta.numero_documento=documento_venta.id_tipo_documento',
                'credito.id_venta=historial_pagos_clientes.credito_id');
            $tipo_join = false;

            $result['lstVenta'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");

            $this->load->view('menu/ventas/tbl_liquidacion_cobranza', $result);


            //echo json_encode($this->v->select_venta_estadocuenta(date("y-m-d", strtotime($this->input->post('fecIni',true))),date("y-m-d", strtotime($this->input->post('fecFin',true)))));
        } else {
            redirect(base_url() . 'venta/', 'refresh');
        }
    }

    function guardar_liquidar()
    {
        $id = $this->input->post('historial');
        $vendedor = $this->input->post('vendedor');
        $result['id_vend'] = $vendedor;

        $liquidacion = array(
            'liquidacion_cajero' => $this->session->userdata('nUsuCodigo'),
            'liquidacion_fecha' => date('Y-m-d H:i:s'),
            'liquidacion_vendedor' => $vendedor,
        );

        $id_liquidacion = $this->liquidacion_cobranza_model->guardar_liquidacion($liquidacion);
        $result['liquidacion'] = $id_liquidacion;
        $data['resultado'] = $this->historial_pagos_clientes_model->update_historial($id, $id_liquidacion);

        //Tabla
        //////////////////////
        $nombre_or = false;
        $where_or = false;
        $nombre_in = false;
        $where_in = false;

        $where = array(
            'historial_usuario' => $vendedor,
            'liquidacion_id' => $id_liquidacion,
            'historial_estatus' => "CONFIRMADO"
        );

        $select = 'SUM(historial_monto) AS suma, historial_caja_id, historial_banco_id, cliente.razon_social, direccion,telefono1,
        documento_Serie, documento_Numero,
          metodos_pago.*';
        $from = "historial_pagos_clientes";
        $join = array('venta', 'cliente', 'documento_venta', 'metodos_pago', 'liquidacion_cobranza_detalle', '(SELECT c.cliente_id, c.tipo, c.valor as direccion, c.principal, COUNT(*) FROM cliente_datos c WHERE c.tipo =1 GROUP BY c.cliente_id, c.tipo ) cli_dat', '(SELECT c1.cliente_id, c1.tipo, c1.valor as telefono1, c1.principal, COUNT(*) FROM cliente_datos c1 WHERE c1.tipo =2 GROUP BY c1.cliente_id, c1.tipo  ) cli_dat2');
        $campos_join = array('historial_pagos_clientes.credito_id=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'metodos_pago.id_metodo=historial_pagos_clientes.historial_tipopago',
            'liquidacion_cobranza_detalle.pago_id=historial_pagos_clientes.historial_id', 'cli_dat.cliente_id = cliente.id_cliente', 'cli_dat2.cliente_id = cliente.id_cliente');

        $tipo_join = array(null, null, null, null, null, null, 'left');


        $group_by = "nombre_metodo";
        $order = "nombre_metodo";
        $result['resultado'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join,
            $where, $nombre_in, $where_in, $nombre_or, $where_or, $group_by, $order, "RESULT_ARRAY");

        //Cajero
        ///////////////////
        $select = 'nombre';
        $from = "usuario";
        $where = array(
            'nUsuCodigo' => $this->session->userdata('nUsuCodigo')
        );
        $result['cajero'] = $this->usuario_model->traer_by($select, $from, false, false, false,
            $where, false, false, false, false, false, false, "ROW_ARRAY");

        //Vendedor
        ///////////////////
        $select = 'nombre';
        $from = "usuario";
        $where = array(
            'nUsuCodigo' => $vendedor
        );
        $result['vendedor'] = $this->usuario_model->traer_by($select, $from, false, false, false,
            $where, false, false, false, false, false, false, "ROW_ARRAY");

        $this->load->view('menu/ventas/visualizarLiquidacion', $result);
    }

    function editar_historialcobranza()
    {

        $historial = $this->input->post('historial_aeditar');
        $venta_id = $this->input->post('venta_aeditar');
        $montonuevo = $this->input->post('montonuevo');
        $vendedor = $this->input->post('vendedor');

        $guardar_historial_pago = $this->historial_pagos_clientes_model->actualizar_historial_editado($historial, $venta_id, $montonuevo);

        if ($guardar_historial_pago != false) {

            $dataresult['exito'] = true;
        } else {
            $dataresult['error'] = true;
        }
        echo json_encode($dataresult);

    }

    function anular_pago()
    {

        $historial = $this->input->post('historial');

        $update = array(
            'historial_estatus' => "ANULADO"
        );
        $guardar_anular = $this->historial_pagos_clientes_model->actualizar($historial, $update, "historial_pagos_clientes");
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($guardar_anular != false) {

            $dataresult['exito'] = true;
        } else {
            $dataresult['error'] = true;
        }
        echo json_encode($dataresult);


    }


    function pagospendientepdf()
    {

        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');


        $id_cliente = null;
        $fechaDesde = null;
        $fechaHasta = null;

        $nombre_or = false;
        $where_or = false;

        $where = "((`venta_status` IN ('" . PEDIDO_ENTREGADO . "', '" . PEDIDO_DEVUELTO . "') and venta.venta_tipo='ENTREGA' and consolidado_detalle.confirmacion_usuario IS NOT NULL) OR (`venta_status` ='" . COMPLETADO . "' and venta.venta_tipo='CAJA')) ";

        if ($this->input->post('cboCliente2', true) != -1) {

            $where = $where . " AND venta.id_cliente =" . $this->input->post('cboCliente2');
        }
        if ($_POST['fecIni2'] != "") {

            $where = $where . " AND date(fecha) >= '" . date('Y-m-d', strtotime($this->input->post('fecIni2'))) . "'";
        }
        if ($_POST['fecFin2'] != "") {

            $where = $where . " AND  date(fecha) <= '" . date('Y-m-d', strtotime($this->input->post('fecFin2'))) . "'";
        }
        // echo $where;

        $where_in[0] = array(CREDITO_DEBE, CREDITO_ACUENTA);
        $nombre_in[0] = 'var_credito_estado';

        /*$nombre_in[1] = 'venta_status';
        $where_in[1] = array(PEDIDO_ENTREGADO, PEDIDO_DEVUELTO, COMPLETADO, PEDIDO_GENERADO, PEDIDO_ENVIADO);*/

        $select = 'venta.venta_id, venta_tipo, venta.id_cliente, razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones, condiciones_pago.dias,venta.id_cliente as clientV, venta.pagado,consolidado_detalle.confirmacion_monto_cobrado_caja,consolidado_detalle.confirmacion_monto_cobrado_bancos,
             (select SUM(historial_monto) from historial_pagos_clientes where historial_pagos_clientes.credito_id = venta.venta_id and historial_estatus="PENDIENTE" ) as confirmar';
        $from = "venta";
        $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'consolidado_detalle');
        $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago', 'consolidado_detalle.pedido_id=venta.venta_id');
        $tipo_join = array('left', null, null, null, 'left');

        $result['lstVenta'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");


        // Aqui llamo a la vista html y le paso la data
        $html = $this->load->view('menu/reportes/pdfPagoPendiente', $result, true);

        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("PagosPendiente.pdf");
        $pdf->Output($nombre_archivo, 'I');

    }

    function toExcel_pagoPendiente()
    {

        $id_cliente = null;
        $fechaDesde = null;
        $fechaHasta = null;

        $nombre_or = false;
        $where_or = false;

        $where = "((`venta_status` IN ('" . PEDIDO_ENTREGADO . "', '" . PEDIDO_DEVUELTO . "') and venta.venta_tipo='ENTREGA' and consolidado_detalle.confirmacion_usuario IS NOT NULL) OR (`venta_status` ='" . COMPLETADO . "' and venta.venta_tipo='CAJA')) ";

        if ($this->input->post('cboCliente1', true) != -1) {

            $where = $where . " AND venta.id_cliente =" . $this->input->post('cboCliente1');
        }
        if ($_POST['fecIni1'] != "") {

            $where = $where . " AND date(fecha) >= '" . date('Y-m-d', strtotime($this->input->post('fecIni1'))) . "'";
        }
        if ($_POST['fecFin1'] != "") {

            $where = $where . " AND  date(fecha) <= '" . date('Y-m-d', strtotime($this->input->post('fecFin1'))) . "'";
        }
        // echo $where;

        $where_in[0] = array(CREDITO_DEBE, CREDITO_ACUENTA);
        $nombre_in[0] = 'var_credito_estado';

        /*$nombre_in[1] = 'venta_status';
        $where_in[1] = array(PEDIDO_ENTREGADO, PEDIDO_DEVUELTO, COMPLETADO, PEDIDO_GENERADO, PEDIDO_ENVIADO);*/

        $select = 'venta.venta_id, venta_tipo, venta.id_cliente, razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones, condiciones_pago.dias,venta.id_cliente as clientV, venta.pagado,consolidado_detalle.confirmacion_monto_cobrado_caja,consolidado_detalle.confirmacion_monto_cobrado_bancos,
             (select SUM(historial_monto) from historial_pagos_clientes where historial_pagos_clientes.credito_id = venta.venta_id and historial_estatus="PENDIENTE" ) as confirmar';
        $from = "venta";
        $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'consolidado_detalle');
        $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago', 'consolidado_detalle.pedido_id=venta.venta_id');
        $tipo_join = array('left', null, null, null, 'left');

        $result['lstVenta'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");

        // Aqui llamo a la vista html y le paso la data
        $this->load->view('menu/reportes/excelPagoPendientes', $result);
    }


    function toPDF_estadoCuenta()
    {

        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(true);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');

        $id_cliente = null;
        $fechaDesde = null;
        $fechaHasta = null;

        $nombre_or = false;
        $where_or = false;
        // Pagination Result
        $array = array();
        $array['productosjson'] = array();

        $total = 0;
        $start = 0;
        $limit = false;
        $draw = $this->input->get('draw');

        $draw = $this->input->get('draw');
        if (!empty($draw)) {

            $start = $this->input->get('start');
            $limit = $this->input->get('length');
        }

        $where = "((`venta_status` IN ('" . PEDIDO_ENTREGADO . "', '" . PEDIDO_DEVUELTO . "') and venta.venta_tipo='ENTREGA' and consolidado_detalle.confirmacion_usuario IS NOT NULL) OR (`venta_status` ='" . COMPLETADO . "' and venta.venta_tipo='CAJA')) ";

        if ($this->input->post('cboCliente2', true) != -1) {

            $where = $where . " AND venta.id_cliente =" . $this->input->post('cboCliente2');
        }
        if ($_POST['fecIni2'] != "") {

            $where = $where . " AND date(fecha) >= '" . date('Y-m-d', strtotime($this->input->post('fecIni2'))) . "'";
        }
        if ($_POST['fecFin2'] != "") {

            $where = $where . " AND  date(fecha) <= '" . date('Y-m-d', strtotime($this->input->post('fecFin2'))) . "'";
        }
        //  echo $where;
        $nombre_in[0] = 'var_credito_estado';
        $where_in[0] = array(CREDITO_DEBE, CREDITO_ACUENTA, CREDITO_NOTACREDITO, CREDITO_CANCELADO);
        $nombre_in[1] = 'venta_status';
        $where_in[1] = array(PEDIDO_ENTREGADO, PEDIDO_DEVUELTO, COMPLETADO, PEDIDO_GENERADO, PEDIDO_ENVIADO);
        ///////////////////////
        $select = 'venta.venta_id,venta.pagado, venta.id_cliente, venta.pagado,
             razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones, confirmacion_monto_cobrado_caja, confirmacion_monto_cobrado_bancos,
            (select SUM(historial_monto) from historial_pagos_clientes where historial_pagos_clientes.credito_id = venta.venta_id and historial_estatus="PENDIENTE" ) as confirmar,
            usuario.nombre as vendedor';
        $from = "venta";
        $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'consolidado_detalle', 'usuario');
        $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago',
            'consolidado_detalle.pedido_id=venta.venta_id', 'usuario.nUsuCodigo=venta.id_vendedor');
        $tipo_join = array('left', null, null, null, 'left', null);

        $result['lstVenta'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");

        // Aqui llamo a la vista html y le paso la data
        $html = $this->load->view('menu/reportes/pdfEstadoCuenta', $result, true);

        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("EstadoCuenta.pdf");
        $pdf->Output($nombre_archivo, 'I');

    }


    function toPDF_cuadre_caja()
    {

        $pdf = new Pdf('L', 'mm', 'LETTER', true, 'UTF-8', false, false);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->SetPrintHeader(false);
        $pdf->SetPrintFooter(false);
        $pdf->setHeaderData('', 0, '', '', array(0, 0, 0), array(255, 255, 255));
        $pdf->AddPage('L');

        $fecha = date('Y-m-d', strtotime($this->input->post('fecha')));
        $where = array('fecha' => $fecha);


        $result['fecha'] = $fecha;

        $ventas_contado = $this->venta_model->ventas_contado($where);
        $pago_adelantado_caja = $this->venta_model->cobrosadelantados_caja($where);
        $pago_adelantado_banco = $this->venta_model->cobrosadelantados_banco($where);
        $ventas_deudas_caja = $this->venta_model->cuadre_caja_cobros_caja($where);
        $ventas_deudas_banco = $this->venta_model->cuadre_caja_cobros_banco($where);
        $compras_contado = $this->ingreso_model->cuadre_caja_egresos($where);
        $compras_ingreso = $this->ingreso_model->cuadre_caja_pagos($where);
        $gastos_ingreso = $this->gastos_model->cuadre_caja_pagos($where);
        $confirmacion_entregabanco = $this->consolidado_model->confirmacion_entregabanco($where);
        $confirmacion_entregacaja = $this->consolidado_model->confirmacion_entregacaja($where);;
        $ingresoC = $compras_ingreso['montoIngreso'];
        $gastosC = $gastos_ingreso['totalGastos'];
        //$pagoAdelantadoCaja = $pago_adelantado_caja['adelantoCaja'];
        //$pagoAdelantadoBanco = $pago_adelantado_banco['adelantoBanco'];


        //var_dump($ventas_deudas_caja);
        $result['total_ingresos_caja'] = $pago_adelantado_caja['adelantoCaja'] + $ventas_contado['totalV'] + $ventas_deudas_caja['suma'] +
            $confirmacion_entregacaja['pago'];

        $result['total_ingresos_banco'] = $pago_adelantado_banco['adelantoBanco'] + $ventas_deudas_banco['duedaBanco'] +
            $confirmacion_entregabanco['pago'];
        $result['total_egreso_caja'] = $compras_contado['totalC'] + $ingresoC + $gastosC;

        $html = $this->load->view('menu/reportes/cuadreCaja', $result, true);

        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        $nombre_archivo = utf8_decode("cuadrecaja.pdf");
        $pdf->Output($nombre_archivo, 'I');

    }


    function toExcel_estadoCuenta()
    {
        $id_cliente = null;
        $fechaDesde = null;
        $fechaHasta = null;

        $nombre_or = false;
        $where_or = false;
        // Pagination Result
        $array = array();
        $array['productosjson'] = array();

        $total = 0;
        $start = 0;
        $limit = false;
        $draw = $this->input->get('draw');

        $draw = $this->input->get('draw');
        if (!empty($draw)) {

            $start = $this->input->get('start');
            $limit = $this->input->get('length');
        }

        $where = "((`venta_status` IN ('" . PEDIDO_ENTREGADO . "', '" . PEDIDO_DEVUELTO . "') and venta.venta_tipo='ENTREGA' and consolidado_detalle.confirmacion_usuario IS NOT NULL) OR (`venta_status` ='" . COMPLETADO . "' and venta.venta_tipo='CAJA')) ";

        if ($this->input->post('cboCliente1', true) != -1) {

            $where = $where . " AND venta.id_cliente =" . $this->input->post('cboCliente2');
        }
        if ($_POST['fecIni1'] != "") {

            $where = $where . " AND date(fecha) >= '" . date('Y-m-d', strtotime($this->input->post('fecIni1'))) . "'";
        }
        if ($_POST['fecFin1'] != "") {

            $where = $where . " AND  date(fecha) <= '" . date('Y-m-d', strtotime($this->input->post('fecFin1'))) . "'";
        }
        //  echo $where;
        $nombre_in[0] = 'var_credito_estado';
        $where_in[0] = array(CREDITO_DEBE, CREDITO_ACUENTA, CREDITO_NOTACREDITO, CREDITO_CANCELADO);
        $nombre_in[1] = 'venta_status';
        $where_in[1] = array(PEDIDO_ENTREGADO, PEDIDO_DEVUELTO, COMPLETADO, PEDIDO_GENERADO, PEDIDO_ENVIADO);
        ///////////////////////
        $select = 'venta.venta_id,venta.pagado, venta.id_cliente, venta.pagado,
             razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones, confirmacion_monto_cobrado_caja, confirmacion_monto_cobrado_bancos,
            (select SUM(historial_monto) from historial_pagos_clientes where historial_pagos_clientes.credito_id = venta.venta_id and historial_estatus="PENDIENTE" ) as confirmar,
            usuario.nombre as vendedor';
        $from = "venta";
        $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'consolidado_detalle', 'usuario');
        $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago',
            'consolidado_detalle.pedido_id=venta.venta_id', 'usuario.nUsuCodigo=venta.id_vendedor');
        $tipo_join = array('left', null, null, null, 'left', null);

        $result['lstVenta'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");
        // Aqui llamo a la vista html y le paso la data
        $this->load->view('menu/reportes/excelEstadoCuenta', $result);
    }


    function deudaselevadaspdf($fecha_ini = false, $fecha_fin = false, $proveedor = false, $zona = false)
    {

        if ($proveedor != false and $proveedor != -1) {
            $where = array('venta.id_vendedor');
        }
        if ($zona != false and $zona != -1) {
            $where = array('venta.id_vendedor');
        }

        if ($fecha_ini != false and $fecha_ini != "") {
            $where['fecha >= '] = date('Y-m-d', strtotime($fecha_ini));
        }
        if ($fecha_fin != false and $fecha_fin != "") {
            $where['fecha <= '] = date('Y-m-d', strtotime($fecha_fin));
        }
        if (empty($where)) {
            $where = false;
        }
        ////////////////////////
        $nombre_or = false;
        $where_or = false;
        ///////////////////////
        $nombre_in[0] = 'var_credito_estado';
        $where_in[0] = array('DEBE', 'A_CUENTA');
        $nombre_in[1] = 'venta_status';
        $where_in[1] = array('ENTREGADO', 'DEVUELTO PARCIALMENTE', 'COMPLETADO');
        ///////////////////////
        $select = 'venta.venta_id, venta.id_cliente,venta.id_vendedor, razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones,usuario.nombre,zonas.zona_nombre';
        $from = "venta";
        $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'usuario', 'zonas');
        $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago',
            'usuario.nUsuCodigo=venta.id_vendedor', 'zonas.zona_id=cliente.id_zona');
        $tipo_join = false;

        $listaventa = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");


        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Cuentas por Pagar');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.â€¢.G.â€¢.D.â€¢.G.â€¢.A.â€¢.D.â€¢.U.â€¢.<br>Gran Logia de la RepÃºblica de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));

// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config.php de libraries/config

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        //  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        //  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//relaciÃ³n utilizada para ajustar la conversiÃ³n de los pÃ­xeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir carÃ¡cteres ASCII estÃ¡ndar, puede utilizar las fuentes bÃ¡sicas como
// Helvetica para reducir el tamaÃ±o del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// AÃ±adir una pÃ¡gina
// Este mÃ©todo tiene varias opciones, consulta la documentaciÃ³n para mÃ¡s informaciÃ³n.
        $pdf->AddPage();

        $pdf->SetFontSize(8);

        $textoheader = "";
        $pdf->writeHTMLCell(
            $w = 0, $h = 0, $x = '60', $y = '',
            $textoheader, $border = 0, $ln = 1, $fill = 0,
            $reseth = true, $align = 'C', $autopadding = true);

//fijar efecto de sombra en el texto
//        $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        $pdf->SetFontSize(12);

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>Deudas elevadas</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        $html .= "<table>";

        $html .= "<tr> <th class='tip' title='Documento'> Nro Venta</th><th>Cliente</th>";
        $html .= "<th class='tip' title='Fecha Registro'>Fecha de venta.</th><th class='tip' >Monto Cred." . MONEDA . "</th>";
        $html .= "<th class='tip' >Monto Canc " . MONEDA . "</th><th class='tip' >Documento</th>";
        $html .= "<th>Trabajador </th>";
        $html .= "<th>Zona </th>";
        $html .= "<th>D&iacute;as de atraso </th>";
        $html .= " <th class='tip' >Estado</th></tr>";
        if (count($listaventa > 0)) {
            foreach ($listaventa as $row) {
                $html .= "<tr><td style='text-align: center;'>" . $row['documento_Serie'] . "-" . $row['documento_Numero'] . "</td>";
                $html .= "<td>" . $row['razon_social'] . "</td>";
                $html .= "<td style='text-align: center;'>" . date('d-m-', strtotime($row['fecha'])) . "</td>";
                $html .= "<td style='text-align: center;'>" . $row['total'] . "</td>";
                $html .= "<td style='text-align: center;'>" . $row['dec_credito_montodebito'] . "</td>";
                $html .= "<td style='text-align: center;'>" . $row['nombre_tipo_documento'] . "</td>";
                $html .= "<td style='text-align: center;'>" . $row['nombre'] . "</td>";
                $html .= "<td style='text-align: center;'>" . $row['zona_nombre'] . "</td>";
                $html .= "<td style='text-align: center;'>";
                $days = (strtotime(date('d-m-Y')) - strtotime($row['fecha'])) / (60 * 60 * 24);
                $html .= "<div ";
                if (floor($days) < 8) {
                    $html .= " style='color: #00CC00' ";
                } elseif (floor($days) < 16) {
                    $html .= " style='color: gold'";
                } else {
                    $html .= "style='color: #ff0000'";
                }
                $html .= " >";
                $html .= floor($days);
                $html .= "</div>";
                $html .= " </td>";
                $html .= " <td style='text-align: center;' >";
                if ($row['var_credito_estado'] == CREDITO_ACUENTA) {
                    $html .= "A Cuenta";
                } elseif ($row['var_credito_estado'] == CREDITO_CANCELADO) {
                    $html .= "Canceló";
                } elseif ($row['var_credito_estado'] == CREDITO_DEBE) {
                    $html .= "DB";
                } else {
                    $html .= "Nota de Crédito";
                }
                $html .= "</td>";

                $html .= "</tr>";

            }
        }

        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este mÃ©todo tiene varias opciones, consulte la documentaciÃ³n para mÃ¡s informaciÃ³n.
        $nombre_archivo = utf8_decode("DeudasElevadas.pdf");
        $pdf->Output($nombre_archivo, 'D');

    }

    function deudasElevadasexcel($fecha_ini = false, $fecha_fin = false, $proveedor = false, $zona = false)
    {

        if ($proveedor != false and $proveedor != -1) {
            $where = array('venta.id_vendedor');
        }
        if ($zona != false and $zona != -1) {
            $where = array('venta.id_vendedor');
        }

        if ($fecha_ini != false and $fecha_ini != "") {
            $where['fecha >= '] = date('Y-m-d', strtotime($fecha_ini));
        }
        if ($fecha_fin != false and $fecha_fin != "") {
            $where['fecha <= '] = date('Y-m-d', strtotime($fecha_fin));
        }
        if (empty($where)) {
            $where = false;
        }
        ////////////////////////
        $nombre_or = false;
        $where_or = false;
        ///////////////////////
        $nombre_in[0] = 'var_credito_estado';
        $where_in[0] = array('DEBE', 'A_CUENTA');
        $nombre_in[1] = 'venta_status';
        $where_in[1] = array('ENTREGADO', 'DEVUELTO PARCIALMENTE', 'COMPLETADO');
        ///////////////////////
        $select = 'venta.venta_id, venta.id_cliente,venta.id_vendedor, razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
            nombre_condiciones,usuario.nombre,zonas.zona_nombre';
        $from = "venta";
        $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago', 'usuario', 'zonas');
        $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago',
            'usuario.nUsuCodigo=venta.id_vendedor', 'zonas.zona_id=cliente.id_zona');

        $tipo_join = false;

        $listaventa = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");


        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Reporte de Invetario")
            ->setSubject("Reporte de Invetario")
            ->setDescription("Reporte de Invetario")
            ->setKeywords("Reporte de Invetario")
            ->setCategory("Reporte de Invetario");


        $columna[0] = "Nro Venta";
        $columna[1] = "Cliente";
        $columna[2] = "Fecha de venta.";
        $columna[3] = "Monto Cred ";
        $columna[4] = "MMonto Canc";
        $columna[5] = "Documento";
        $columna[6] = "Trabajador";
        $columna[7] = "Zona";
        $columna[8] = "Dias de atraso";
        $columna[9] = "Estado";


        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }

        $row = 2;
        if (count($listaventa) > 0) {

            foreach ($listaventa as $fila) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $fila['documento_Serie'] . "-" . $fila['documento_Numero']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $fila['razon_social']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, date("d-m-Y", strtotime($fila['fecha'])));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $fila['total']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $fila['dec_credito_montodebito']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $fila['nombre_tipo_documento']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $fila['nombre']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $fila['zona_nombre']);


                $days = (strtotime(date('d-m-Y')) - strtotime($fila['fecha'])) / (60 * 60 * 24);;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, floor($days));

                if ($fila['var_credito_estado'] == CREDITO_ACUENTA) {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, "A CUENTA");
                } elseif ($fila['var_credito_estado'] == CREDITO_CANCELADO) {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, "CANCELÓ");
                } elseif ($fila['var_credito_estado'] == CREDITO_DEBE) {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, "DB");
                } else {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, "NOTA DE CRÉDITO");
                }


                $row++;
            }
        }

// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Deudas elevadas');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="DeudasElevadas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    function validar_deuda($id_cliente)
    {
        $where = array('venta.id_cliente' => $id_cliente);
        ////////////////////////
        $nombre_or = false;
        $where_or = false;
        ///////////////////////
        $nombre_in[0] = 'var_credito_estado';
        $where_in[0] = array('DEBE', 'A_CUENTA');
        $nombre_in[1] = 'venta_status';
        $where_in[1] = array('ENTREGADO', 'DEVUELTO PARCIALMENTE', 'COMPLETADO');
        ///////////////////////
        $select = 'venta.venta_id, venta.id_cliente, razon_social,fecha, total,var_credito_estado, dec_credito_montodebito, documento_venta.*,
        nombre_condiciones';
        $from = "venta";
        $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago');
        $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
            'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago');
        $tipo_join = false;

        $result['lstVenta'] = $this->venta_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");
        if ($result['lstVenta'] == true) {
            return true;
        } else {
            return false;
        }
    }

    function numtoletras($xcifra)
    {
        $xarray = array(0 => "Cero",
            1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
            "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
            "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
            100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
        );
//
        $xcifra = trim($xcifra);
        $xlength = strlen($xcifra);
        $xpos_punto = strpos($xcifra, ".");
        $xaux_int = $xcifra;
        $xdecimales = "00";
        if (!($xpos_punto === false)) {
            if ($xpos_punto == 0) {
                $xcifra = "0" . $xcifra;
                $xpos_punto = strpos($xcifra, ".");
            }
            $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
            $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
        }

        $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
        $xcadena = "";
        for ($xz = 0; $xz < 3; $xz++) {
            $xaux = substr($XAUX, $xz * 6, 6);
            $xi = 0;
            $xlimite = 6; // inicializo el contador de centenas xi y establezco el l�mite a 6 d�gitos en la parte entera
            $xexit = true; // bandera para controlar el ciclo del While
            while ($xexit) {
                if ($xi == $xlimite) { // si ya lleg� al l�mite m�ximo de enteros
                    break; // termina el ciclo
                }

                $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
                $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres d�gitos)
                for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                    switch ($xy) {
                        case 1: // checa las centenas
                            if (substr($xaux, 0, 3) < 100) { // si el grupo de tres d�gitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                            } else {
                                $key = (int)substr($xaux, 0, 3);
                                if (TRUE === array_key_exists($key, $xarray)) {  // busco si la centena es n�mero redondo (100, 200, 300, 400, etc..)
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux); // devuelve el subfijo correspondiente (Mill�n, Millones, Mil o nada)
                                    if (substr($xaux, 0, 3) == 100)
                                        $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                                } else { // entra aqu� si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                    $key = (int)substr($xaux, 0, 1) * 100;
                                    $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 0, 3) < 100)
                            break;
                        case 2: // checa las decenas (con la misma l�gica que las centenas)
                            if (substr($xaux, 1, 2) < 10) {

                            } else {
                                $key = (int)substr($xaux, 1, 2);
                                if (TRUE === array_key_exists($key, $xarray)) {
                                    $xseek = $xarray[$key];
                                    $xsub = $this->subfijo($xaux);
                                    if (substr($xaux, 1, 2) == 20)
                                        $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                    $xy = 3;
                                } else {
                                    $key = (int)substr($xaux, 1, 1) * 10;
                                    $xseek = $xarray[$key];
                                    if (20 == substr($xaux, 1, 1) * 10)
                                        $xcadena = " " . $xcadena . " " . $xseek;
                                    else
                                        $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                                } // ENDIF ($xseek)
                            } // ENDIF (substr($xaux, 1, 2) < 10)
                            break;
                        case 3: // checa las unidades
                            if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                            } else {
                                $key = (int)substr($xaux, 2, 1);
                                $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                                $xsub = $this->subfijo($xaux);
                                $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                            } // ENDIF (substr($xaux, 2, 1) < 1)
                            break;
                    } // END SWITCH
                } // END FOR
                $xi = $xi + 3;
            } // ENDDO

            if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
                $xcadena .= " DE";

            if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
                $xcadena .= " DE";

            // ----------- esta l�nea la puedes cambiar de acuerdo a tus necesidades o a tu pa�s -------
            if (trim($xaux) != "") {
                switch ($xz) {
                    case 0:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena .= "UN BILLON ";
                        else
                            $xcadena .= " BILLONES ";
                        break;
                    case 1:
                        if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                            $xcadena .= "UN MILLON ";
                        else
                            $xcadena .= " MILLONES ";
                        break;
                    case 2:
                        if ($xcifra < 1) {
                            $xcadena = "CERO SOLES $xdecimales/100 ";
                        }
                        if ($xcifra >= 1 && $xcifra < 2) {
                            $xcadena = "UN SOLES $xdecimales/100  ";
                        }
                        if ($xcifra >= 2) {
                            $xcadena .= " SOLES $xdecimales/100  "; //
                        }
                        break;
                } // endswitch ($xz)
            } // ENDIF (trim($xaux) != "")
            // ------------------      en este caso, para M�xico se usa esta leyenda     ----------------
            $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
            $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
            $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
            $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
        } // ENDFOR ($xz)
        return trim($xcadena);
    }

// END FUNCTION

    function subfijo($xx)
    { // esta funci�n regresa un subfijo para la cifra
        $xx = trim($xx);
        $xstrlen = strlen($xx);
        if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
            $xsub = "";
        //
        if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
            $xsub = "MIL";
        //
        return $xsub;
    }


    function zonaVendedor()
    {
        $useradmin = $this->session->userdata('admin');
        if ($this->input->post('dia') != '') {
            $dia = $this->input->post('dia');
        } else {
            $dia = null;
        }
        $vendedor = $this->input->post('vendedor_id');

        $useradmin = $this->db->get_where('usuario', array('nUsuCodigo' => $vendedor))->row();

        if ($useradmin->admin == 1)
            $vendedor = FALSE;
        $zona_vendedor = $this->venta_model->zonaVendedor($vendedor, $dia);
        die(json_encode($zona_vendedor));
    }


    function clienteDireccion()
    {
        $cliente_direccion = $this->venta_model->clienteDireccion($this->input->post('cliente_id'));
        die(json_encode($cliente_direccion));
    }

    function dataCliente()
    {
        $cliente_direccion = $this->venta_model->dataCliente($this->input->post('cliente_id'));
        die(json_encode($cliente_direccion));
    }

    function dataClienteDeuda()
    {
        die(json_encode($this->venta_model->getDeudaCliente($this->input->post('cliente_id'))));
    }

    function clientesIdZona()
    {
        $cliente_direccion = $this->venta_model->dataClienteIdZona($this->input->post('zona_id'));
        die(json_encode($cliente_direccion));
    }

    function listaClientes()
    {
        die(json_encode($this->cliente_model->get_all()));
    }

    function get_precio_escalas()
    {
        $this->load->model('unidades_has_precio/unidades_has_precio_model', 'unidadPrecio');
        $producto_id = $this->input->post('producto_id');
        $unidad_id = $this->input->post('unidad_id');
        $grupo_id = $this->input->post('grupo_id');

        echo json_encode($this->unidadPrecio->get_max_min_precio($producto_id, $unidad_id, $grupo_id));
    }
}




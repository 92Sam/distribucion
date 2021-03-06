<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//include FCPATH.'application/libraries/phpodt/phpodt.php';
class consolidadodecargas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('consolidadodecargas/consolidado_model');
        $this->load->model('venta/venta_model');
        $this->load->model('banco/banco_model');

        $this->load->library('Pdf');
        $this->load->library('Phpword');
        $this->load->library('phpExcel/PHPExcel.php');

    }

    function index()
    {
        $data = array();
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/camiones/consolidado', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);

        }

    }

    function lst_consolidado()
    {
        if ($this->input->is_ajax_request()) {
            $where['estado'] = $this->input->post('estado');

            $where['fecha_ini'] = !empty($_POST['fecha_ini']) ? $_POST['fecha_ini'] : null;
            $where['fecha_fin'] = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

            $data['consolidado'] = $this->consolidado_model->getData($where);

            $this->load->view('menu/camiones/tbl_consolidado', $data);
        } else {
            redirect(base_url() . 'consolidadodecargas/', 'refresh');
        }
    }


    function confirmarentregadedinero()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }
        $data = array();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/consolidadodecargas/confirmaciondepago', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);

        }

    }

    function buscarConsolidadoEstado()
    {
        $condicion = array();

        if ($this->input->post('fecha_ini') != "" && $this->input->post('fecha_fin') != "") {
            $condicion['fecha_ini'] = $this->input->post('fecha_ini');
            $condicion['fecha_fin'] = $this->input->post('fecha_fin');
        }

        if ($this->input->post('estado') != "") {
            $condicion['status'] = $this->input->post('estado');
            $data['estado'] = $this->input->post('estado');
        }
        $data['consolidado'] = $this->consolidado_model->get_all_estado($condicion);
        $this->load->view('menu/consolidadodecargas/listaConfirmacionPago', $data);
    }

    function verDetalles($id = FALSE)
    {
        $data = array();
        $data['consolidado'] = $this->consolidado_model->get($id);
        if ($id != FALSE) {
            $data['consolidadoDetalles'] = $this->consolidado_model->get_details_by(array('consolidado_id' => $id));
        }
        $this->load->view('menu/camiones/consolidadoDeDocumentos', $data);
    }

    function guardar()
    {
        $pedidos = array();
        $data['camion'] = $this->input->post('camion');
        $data['metros_cubicos'] = $this->input->post('metros');
        $data['fecha_creacion'] = date('Y-m-d H:i:s');
        $data['generado_por'] = $this->session->userdata('nUsuCodigo');
        $pedidos = $this->input->post('pedidos');
        $data['fecha'] = date('Y-m-d', strtotime($this->input->post('fecha_consolidado'))) . " " . date('H:i:s');
        $data['status'] = 'ABIERTO';

        $guardar = $this->consolidado_model->set_consolidado($data, $pedidos);

        if ($guardar != FALSE) {

            $json['success'] = 'Solicitud Procesada con exito';

        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        echo json_encode($json);
    }

    function editar_consolidado($consolidado_id)
    {
        $data['metros_cubicos'] = $this->input->post('metros_c');
        $data['pedidos_id'] = json_decode($this->input->post('pedidos_id'));

        $this->consolidado_model->editar_consolidado($consolidado_id, $data);
    }

    function eliminar_pedido_consolidado()
    {
        $data['consolidado_id'] = $this->input->post('id');
        $data['venta_id'] = json_decode($this->input->post('venta_id'));

        $result = $this->consolidado_model->eliminar_pedido_consolidado($data['consolidado_id'], $data['venta_id']);

        header('Content-Type: application/json');
        echo json_encode(array('result' => $result));
    }

    function cambiar_fecha()
    {
        $data['consolidado_id'] = $this->input->post('id');
        $data['fecha'] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha') . " " . date('H:i:s')));

        $this->db->where('consolidado_id', $data['consolidado_id']);
        $this->db->update('consolidado_carga', array('fecha' => $data['fecha']));
    }

    function liquidacion()
    {

        $data['metodos_pago'] = $this->db->get_where('metodos_pago', array('status_metodo' => 1))->result();
        $data['bancos'] = $this->db->get_where('banco', array('banco_status' => 1))->result();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/liquidacionCgc', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);

        }

    }

    function verDetallesLiquidacion($id = FALSE, $status)
    {

        $data = array();


        if ($id != FALSE) {
            $where = array('consolidado_id' => $id);
            $data['status'] = $status;
            $data['consolidado'] = $this->consolidado_model->get_details_by($where);
        }

        $data['id_consolidado'] = $id;

        $data['consolidado_detalle'] = $this->db->select('
            c.consolidado_id as consolidado_id,
            camion.camiones_placa AS placa,
            chofer.nombre AS chofer
        ')
            ->from('consolidado_carga AS c')
            ->join('camiones AS camion', 'camion.camiones_id = c.camion')
            ->join('usuario AS chofer', 'chofer.nUsuCodigo = camion.id_trabajadores')
            ->where('c.consolidado_id', $id)
            ->get()->row();

        $this->load->view('menu/consolidadodecargas/consolidadoLiquidacion', $data);
    }

    function cerrar_confirmacion()
    {


        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('confirmar', 'confirmar', 'required');

            $datos['confirmar'] = $this->input->post('confirmar');
            $datos['input_caja'] = $this->input->post('input_caja');
            $datos['bancos'] = $this->input->post('bancos');
            $datos['input_bancos'] = $this->input->post('input_bancos');
            $datos['pedido_id'] = $this->input->post('pedido_id');
            $datos['consolidado_id'] = $this->input->post('consolidado_id');
            $datos['check_caja'] = $this->input->post('check_caja');
            $datos['check_banco'] = $this->input->post('check_banco');
            $validar = $this->consolidado_model->update_varios_detalles($datos);


            if ($validar == false) {
                $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
            } else {

                $json['consolidado'] = $this->consolidado_model->get_all();
            }

        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        echo json_encode($json);
    }

    function infoCobroConslidado($id = FALSE, $status, $tipo)
    {
        $data = array();

        $data['tipo'] = $tipo;
        if ($id != FALSE) {
            $where = array('consolidado_id' => $id);
            $data['status'] = $status;
            $data['consolidado'] = $this->consolidado_model->get_details_by($where);
            $data['bancos'] = $this->banco_model->get_all();

        }

        //var_dump($this->session->userdata);
        $this->load->view('menu/consolidadodecargas/cobroRealizado', $data);
    }

    function get_pedido($pedido_id)
    {
        $venta = $this->db->get_where('venta', array('venta_id' => $pedido_id))->row();

        $temp = $this->db->select('SUM(historial_pedido_detalle.stock * historial_pedido_detalle.precio_unitario) as total')
            ->from('historial_pedido_detalle')
            ->join('historial_pedido_proceso', 'historial_pedido_proceso.id=historial_pedido_detalle.historial_pedido_proceso_id')
            ->where('historial_pedido_proceso.proceso_id', PROCESO_IMPRIMIR)
            ->where('historial_pedido_proceso.pedido_id', $pedido_id)
            ->get()->row();

        $venta->historico_total = $temp->total;
        $venta->historico_impuesto = number_format(($venta->historico_total * 18) / 100, 2);
        $venta->historico_subtotal = $venta->historico_total - $venta->historico_impuesto;

        header('Content-Type: application/json');
        echo json_encode(array('pedido' => $venta));
    }

    function buscarPorEstado()
    {
//
        $condicion = array();
        if ($this->input->post('estado') != "-1")
            $where = array('status' => $this->input->post('estado'));
        else
            $where = array('status !=' => 'ABIERTO');

        if ($this->input->post('fecha_ini') != '' && $this->input->post('fecha_fin') != '') {
            $where['fecha >='] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha_ini') . " 00:00:00"));
            $where['fecha <='] = date('Y-m-d H:i:s', strtotime($this->input->post('fecha_fin') . " 23:59:59"));
        }

        $data['estado'] = $this->input->post('estado');

        $data['consolidado'] = $this->consolidado_model->get_consolidado_by($where);
        $dataCuerpo['cuerpo'] = $this->load->view('menu/consolidadodecargas/listaConsolidado', $data);


    }


    public function docFiscalBoleta()
    {
        $c = 0;
        $id = $this->input->post('id');
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

        $result['consolidado_id'] = $id;
        $this->load->view('menu/ventas/visualizarVentasBoletas', $result);

    }

    public function docFiscalFactura()
    {
        $c = 0;
        $id = $this->input->post('id');
        $where = array('consolidado_detalle.consolidado_id' => $id);
        $result['detalleC'] = $this->consolidado_model->get_detalle_by($where);
        $result['facturas'] = array();
        foreach ($result['detalleC'] as $pedido) {
            $id_pedido = $pedido['pedido_id'];
            if ($pedido['documento_tipo'] == FACTURA) {
                if ($id != FALSE) {
                    $result['id_venta'] = $id_pedido;
                    $result['facturas'][] = $this->venta_model->documentoVenta($id_pedido);
                    $c++;
                }
            }
        }
        if ($c >= 1) {

        } else {
            $result['facturas'] = "";

        }

        $result['consolidado_id'] = $id;
        $this->load->view('menu/ventas/visualizarVentas', $result);
    }

    public function notaEntrega()
    {
        $id = $this->input->post('id');
        $venta_id = $this->input->post('venta_id');

        $where = array('consolidado_detalle.consolidado_id' => $id);
        if ($venta_id != 0)
            $where = array('venta.venta_id' => $venta_id);

        $result['detalleC'] = $this->consolidado_model->get_detalle_by($where);
        $result['notasdentrega'] = array();
        foreach ($result['detalleC'] as $pedido) {
            $id_pedido = $pedido['pedido_id'];
            if ($id != FALSE) {
                $result['retorno'] = 'consolidadodecargas';
                $result['id_venta'] = $id_pedido;
                $result['notasdentrega'][]['ventas'] = $this->venta_model->obtener_venta($id_pedido);
            }
        }

        $result['consolidado'] = $this->db->get_where('consolidado_carga', array('consolidado_id' => $id))->row();
        $result['consolidado_id'] = $id;
        $result['nota_entrega'] = '1';
        $this->load->view('menu/ventas/visualizarVenta', $result);
    }

    function liquidarPedido()
    {
        $estatus = $this->input->post('estatus');
        $id_pedido = $this->input->post('id_pedido_liquidacion');
        $pago = $this->input->post('pago_id');
        $banco = $this->input->post('banco_id');
        $num_oper = $this->input->post('num_oper');
        $monto = $this->input->post('monto') != NULL ? $this->input->post('monto') : 0;
        $fec_ope = $this->input->post('fec_oper');
        $motivo_id = $this->input->post('motivo_id');


        $venta = $this->venta_model->get_by('venta_id', $id_pedido);

        if ($estatus != PEDIDO_RECHAZADO) {
            $this->venta_model->actualizarCredito(array('dec_credito_montodeuda' => $venta['total']), array('id_venta' => $id_pedido));
        }

        $validacion = $this->consolidado_model->updateDetalle(array(
            'pedido_id' => $id_pedido,
            'liquidacion_monto_cobrado' => $monto,
            'pago_id' => $pago,
            'banco_id' => $banco,
            'num_oper' => $num_oper,
            'fecha_documento' => $fec_ope
        ));


        $result = $this->venta_model->update_status($id_pedido, $estatus, $motivo_id);

        if ($result != FALSE && $validacion != false) {
            //$json['success'] = 'Solicitud Procesada con exito';
            echo json_encode(array('success' => 1));
        } else {
            //$json['error'] = 'Ha ocurrido un error al procesar la solicitud';
            echo json_encode(array('error' => 1));
        }

        //echo json_encode($json);
    }

    function cerrarLiquidacion()
    {
        $id = $this->input->post('id');
        $c_detalles = $this->db->get_where('consolidado_detalle', array('consolidado_id' => $id))->result();

        foreach ($c_detalles as $detalle) {
            $pedido = $this->db->get_where('venta', array('venta_id' => $detalle->pedido_id))->row();
            if ($pedido->venta_status == PEDIDO_DEVUELTO) {
                $detalle_historial = $this->db->select('historial_pedido_detalle.*')
                    ->from('historial_pedido_detalle')
                    ->join('historial_pedido_proceso', 'historial_pedido_proceso.id = historial_pedido_detalle.historial_pedido_proceso_id')
                    ->where('historial_pedido_proceso.pedido_id', $detalle->pedido_id)
                    ->where('historial_pedido_proceso.proceso_id', PROCESO_DEVOLVER)
                    ->get()->result();

                if (count($detalle_historial) == 0) {
                    $this->db->where('venta_id', $detalle->pedido_id);
                    $this->db->update('venta', array('venta_status' => 'ENTREGADO'));
                }
            }
        }


        if ($id == FALSE) {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        $estatus = 'CERRADO';
        $cerrar = $this->consolidado_model->cambiarEstatus($id, $estatus);


        foreach ($c_detalles as $detalle) {
            $pedido = $this->db->get_where('venta', array('venta_id' => $detalle->pedido_id))->row();

            if ($pedido->venta_status != PEDIDO_DEVUELTO) {
                $this->venta_model->reset_venta($detalle->pedido_id);
            } else {
                //RETORNO EL STOCK SI ES UN PEDIDO DEVUELTO
//                $this->venta_model->devolver_parcial_stock($detalle->pedido_id);
                $this->venta_model->devolver_parcial($detalle->pedido_id);
            }

            if ($pedido->venta_status == PEDIDO_RECHAZADO) {
                //RETORNO TODO EL STOCK
                $this->venta_model->devolver_all_stock($detalle->pedido_id);
            }

            if ($detalle->liquidacion_monto_cobrado == 0 || $detalle->liquidacion_monto_cobrado == null) {
                $this->historial_pedido_model->insertar_pedido(PROCESO_LIQUIDAR, array(
                    'pedido_id' => $detalle->pedido_id,
                    'responsable_id' => $this->session->userdata('nUsuCodigo')
                ));
            }
        }

        $this->consolidado_model->confirmar_consolidado($id);

        if ($cerrar != FALSE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        echo json_encode($json);

    }


    function excel()
    {

        $cargas = $this->consolidado_model->get_all();

        $this->phpexcel->getProperties()
            ->setTitle("Reporte de Cargas")
            ->setSubject("Reporte de Cargas")
            ->setDescription("Reporte de Cargas")
            ->setKeywords("Reporte de Cargas")
            ->setCategory("Reporte de Cargas");


        $columna_pdf[0] = "ID";
        $columna_pdf[1] = "Fecha";
        $columna_pdf[2] = "Camion";
        $columna_pdf[3] = "Status";

        $col = 0;
        for ($i = 0; $i < count($columna_pdf); $i++) {
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna_pdf[$i]);
        }

        $row = 2;

        foreach ($cargas as $campoCarga) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campoCarga['consolidado_id']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, date('d-m-Y', strtotime($campoCarga['fecha'])));
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campoCarga['camiones_placa']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campoCarga['status']);
            $col++;

            $row++;
        }

        // Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Consolidado de cargas');


        // configuramos el documento para que la hoja


        // de trabajo n�mero 0 sera la primera en mostrarse
        // al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


        // redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ConsolidadoDeCargas.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    function excelModal($id)
    {

        $detalles = $this->consolidado_model->get_details_by(array('consolidado_id' => $id));


        $this->phpexcel->getProperties()
            ->setTitle("Reporte de detalles")
            ->setSubject("Reporte de detalles")
            ->setDescription("Reporte de detalles")
            ->setKeywords("Reporte de detalles")
            ->setCategory("Reporte de detalles");


        $columna_pdf[0] = "Tipo de documento";
        $columna_pdf[1] = "Numero de documento";
        $columna_pdf[2] = "Cantidad productos";
        $columna_pdf[3] = "Total";
        $columna_pdf[4] = "Status";

        $col = 0;
        for ($i = 0; $i < count($columna_pdf); $i++) {
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna_pdf[$i]);
        }

        $row = 2;

        foreach ($detalles as $campodetalles) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campodetalles['nombre_tipo_documento']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campodetalles['documento_Serie'] . "-" . $campodetalles['documento_Numero']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campodetalles['cantidad_prductos']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campodetalles['total']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campodetalles['venta_status']);
            $col++;

            $row++;


            // Renombramos la hoja de trabajo
            $this->phpexcel->getActiveSheet()->setTitle('Detalles de consolidado');


            // configuramos el documento para que la hoja

            // de trabajo n�mero 0 sera la primera en mostrarse
            // al abrir el documento
            $this->phpexcel->setActiveSheetIndex(0);


            // redireccionamos la salida al navegador del cliente (Excel2007)
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="datellasDeConsolidado.xlsx"');
            header('Cache-Control: max-age=0');

            $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
            $objWriter->save('php://output');


        }

    }


    function rtfRemision($id)
    {

        $detalles = $this->consolidado_model->get_detalle($id);
        // documento
        $phpword = new \PhpOffice\PhpWord\PhpWord();
        $styles = array(
            'pageSizeW' => '12755.905511811',
            'pageSizeH' => '15874.015748031',
            'marginTop' => '566.929133858',
            'marginLeft' => '1133.858267717',
            'marginRight' => '283.464566929',
            'marginBottom' => '566.929133858',
        );

        $section = $phpword->addSection($styles);
        $phpword->addFontStyle('rStyle', array('size' => 18, 'allCaps' => true));
        $phpword->addFontStyle('rBasicos', array('size' => 8, 'allCaps' => true));
        $phpword->addParagraphStyle('pStyle', array('align' => 'left'));
        $phpword->addParagraphStyle('totales', array('align' => 'right'));

        $tablastyle = array('width' => 50 * 100, 'unit' => 'pct', 'align' => 'left');

        $section->addTextBreak(6);

        // tabla de productos
        $table1 = $section->addTable($tablastyle);

        foreach ($detalles as $campoDetalles) {


            $table1->addRow(200, array('exactHeight' => true));

            $table1->addCell()->addText(htmlspecialchars($campoDetalles['nombre_unidad']), 'rBasicos');
            $table1->addCell()->addText(htmlspecialchars($campoDetalles['producto_nombre']), 'rBasicos');
            $table1->addCell()->addText(htmlspecialchars($campoDetalles['cantidadTotal']), 'rBasicos');
            $table1->addCell()->addText(htmlspecialchars($campoDetalles['metros_cubicos'] * $campoDetalles['cantidadTotal']), 'rBasicos');
        }


        $file = 'Guiaderemision' . $id . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007');
        $xmlWriter->save("php://output");


    }

    function rtfRemisionBoleta($id)
    {

        $remision = $this->db->select('
            cc.fecha_creacion AS fecha_emision,
            cc.fecha AS fecha_traslado,
            c.camiones_placa AS placa,
            u.nombre AS transportista
        ')->from('consolidado_carga AS cc')
            ->join('camiones AS c', 'c.camiones_id = cc.camion')
            ->join('usuario AS u', 'u.nUsuCodigo = c.id_trabajadores')
            ->where('cc.consolidado_id', $id)
            ->get()->row();

        $consolidado_detalles_temp = $this->db->select('
            dv.id_producto AS producto_id,
            dv.cantidad AS cantidad,
            dv.precio AS precio,
            dv.detalle_importe AS importe,
            dv.bono AS bono,
            um.abreviatura AS um,
            p.producto_nombre AS producto_nombre,
            c.id_zona AS zona_id
        ')->from('detalle_venta AS dv')
            ->join('venta AS v', 'v.venta_id = dv.id_venta')
            ->join('cliente AS c', 'c.id_cliente = v.id_cliente')
            ->join('consolidado_detalle AS cd', 'cd.pedido_id = dv.id_venta')
            ->join('unidades AS um', 'um.id_unidad = dv.unidad_medida')
            ->join('producto AS p', 'p.producto_id = dv.id_producto')
            ->where('cd.consolidado_id', $id)
            ->where('v.tipo_doc_fiscal', 'BOLETA DE VENTA')
            ->get()->result();

        $consolidado_detalles = array();
        $temp_cantidad = array();
        foreach ($consolidado_detalles_temp as $detalles) {
            if (isset($temp_cantidad[$detalles->producto_id . $detalles->um])) {
                $temp_cantidad[$detalles->producto_id . $detalles->um]->cantidad += $detalles->cantidad;
            } else {
                $temp = new stdClass();
                $temp->producto_id = $detalles->producto_id;
                $temp->cantidad = $detalles->cantidad;
                $temp->precio = $detalles->precio;
                $temp->importe = $detalles->importe;
                $temp->um = $detalles->um;
                $temp->producto_nombre = $detalles->producto_nombre;
                $temp->zona_id = $detalles->zona_id;

                $temp_cantidad[$detalles->producto_id . $detalles->um] = $temp;
            }
        }

        foreach ($temp_cantidad as $temp) {
            $consolidado_detalles[] = $temp;
        }

        $distrito = $this->db->select(
            'ciu.ciudad_nombre AS distrito'
        )->from('zonas AS z')
            ->join('ciudades AS ciu', 'ciu.ciudad_id = z.ciudad_id')
            ->where('z.zona_id', $consolidado_detalles[0]->zona_id)
            ->get()->row();

        $cantidad_paginas = 1;
        if (count($consolidado_detalles) > 38)
            $cantidad_paginas = intval(count($consolidado_detalles) / 38) + 1;

        $template_name = 'remision' . $cantidad_paginas . '.docx';
        $word = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor(base_url('recursos/formatos/remision/' . $template_name));

        $detalle_index = 0;
        for ($n = 0; $n < $cantidad_paginas; $n++) {

            $index = $n + 1;
            $template->setValue('fecha_e' . $index, date('d/m/Y', strtotime($remision->fecha_emision)));
            $template->setValue('fecha_t' . $index, date('d/m/Y', strtotime($remision->fecha_traslado)));
            $template->setValue('placa' . $index, htmlspecialchars($remision->placa));
            $template->setValue('transportista' . $index, htmlspecialchars($remision->transportista));

            $template->setValue('cliente' . $index, 'VARIOS');
            $template->setValue('ruc' . $index, '');
            $template->setValue('direccion' . $index, htmlspecialchars($distrito->distrito));
            $template->setValue('llegada' . $index, htmlspecialchars($distrito->distrito));

            for ($i = 0; $i < 38; $i++) {
                $id = '';
                $nombre = '';
                $um = '';
                $cantidad = '';
                $valor_unitario = '';
                $valor_venta = '';

                if (isset($consolidado_detalles[$detalle_index])) {
                    $id = sumCod($consolidado_detalles[$detalle_index]->producto_id, 4);
                    $nombre = $consolidado_detalles[$detalle_index]->producto_nombre;
                    $um = $consolidado_detalles[$detalle_index]->um;
                    $cantidad = intval($consolidado_detalles[$detalle_index]->cantidad);
                    $valor_unitario = $consolidado_detalles[$detalle_index]->precio;
                    $valor_venta = $consolidado_detalles[$detalle_index]->importe;

                    $detalle_index++;
                }

                $index_p = $index . '-' . ($i + 1);
                $template->setValue($index_p, $id);
                $template->setValue('c' . $index_p, $cantidad);
                $template->setValue('u' . $index_p, htmlspecialchars($um));
                $template->setValue('producto' . $index_p, htmlspecialchars($nombre));
                $template->setValue('prc' . $index_p, $valor_unitario);
                $template->setValue('v' . $index_p, $valor_venta);
            }
        }

        $template->saveAs(sys_get_temp_dir() . '/remision_temp.docx');
        header("Content-Disposition: attachment; filename='remision.docx'");
        readfile(sys_get_temp_dir() . '/remision_temp.docx'); // or echo file_get_contents($temp_file);
        unlink(sys_get_temp_dir() . '/remision_temp.docx');
    }

    function rtfRemisionFactura($id)
    {

        $remision = $this->db->select('
            cc.fecha_creacion AS fecha_emision,
            cc.fecha AS fecha_traslado,
            c.camiones_placa AS placa,
            u.nombre AS transportista
        ')->from('consolidado_carga AS cc')
            ->join('camiones AS c', 'c.camiones_id = cc.camion')
            ->join('usuario AS u', 'u.nUsuCodigo = c.id_trabajadores')
            ->where('cc.consolidado_id', $id)
            ->get()->row();

        $facturas = $this->db->select('
            cd.pedido_id AS venta_id,
            c.razon_social AS razon_social,
            c.ruc_cliente AS ruc,
            c.id_cliente AS cliente_id
        ')->from('consolidado_detalle AS cd')
            ->join('venta AS v', 'v.venta_id = cd.pedido_id')
            ->join('cliente AS c', 'c.id_cliente = v.id_cliente')
            ->where('cd.consolidado_id', $id)
            ->where('v.tipo_doc_fiscal', 'FACTURA')
            ->get()->result();

        $cantidad_paginas = count($facturas);

        $template_name = 'remision' . $cantidad_paginas . '.docx';
        $word = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor(base_url('recursos/formatos/remision/' . $template_name));

        for ($n = 0; $n < $cantidad_paginas; $n++) {

            $index = $n + 1;
            $template->setValue('fecha_e' . $index, date('d/m/Y', strtotime($remision->fecha_emision)));
            $template->setValue('fecha_t' . $index, date('d/m/Y', strtotime($remision->fecha_traslado)));
            $template->setValue('placa' . $index, htmlspecialchars($remision->placa));
            $template->setValue('transportista' . $index, htmlspecialchars($remision->transportista));

            $dato = $this->db->get_where('cliente_datos', array(
                'cliente_id' => $facturas[$n]->cliente_id,
                'principal' => 1,
                'tipo' => 1
            ))->row();

            $template->setValue('cliente' . $index, htmlspecialchars($facturas[$n]->razon_social));
            $template->setValue('ruc' . $index, $facturas[$n]->ruc);
            $template->setValue('direccion' . $index, htmlspecialchars($dato->valor));
            $template->setValue('llegada' . $index, htmlspecialchars($dato->valor));

            $consolidado_detalles_temp = $this->db->select('
            dv.id_producto AS producto_id,
            dv.cantidad AS cantidad,
            dv.precio AS precio,
            dv.detalle_importe AS importe,
            dv.bono AS bono,
            um.abreviatura AS um,
            p.producto_nombre AS producto_nombre
        ')->from('detalle_venta AS dv')
                ->join('venta AS v', 'v.venta_id = dv.id_venta')
                ->join('consolidado_detalle AS cd', 'cd.pedido_id = dv.id_venta')
                ->join('unidades AS um', 'um.id_unidad = dv.unidad_medida')
                ->join('producto AS p', 'p.producto_id = dv.id_producto')
                ->where('v.venta_id', $facturas[$n]->venta_id)
                ->get()->result();

            $consolidado_detalles = array();
            $temp_cantidad = array();
            foreach ($consolidado_detalles_temp as $detalles) {
                if (isset($temp_cantidad[$detalles->producto_id . $detalles->um])) {
                    $temp_cantidad[$detalles->producto_id . $detalles->um]->cantidad += $detalles->cantidad;
                } else {
                    $temp = new stdClass();
                    $temp->producto_id = $detalles->producto_id;
                    $temp->cantidad = $detalles->cantidad;
                    $temp->precio = $detalles->precio;
                    $temp->importe = $detalles->importe;
                    $temp->um = $detalles->um;
                    $temp->producto_nombre = $detalles->producto_nombre;

                    $temp_cantidad[$detalles->producto_id . $detalles->um] = $temp;
                }
            }

            foreach ($temp_cantidad as $temp) {
                $consolidado_detalles[] = $temp;
            }

            for ($i = 0; $i < 38; $i++) {
                $id = '';
                $nombre = '';
                $um = '';
                $cantidad = '';
                $valor_unitario = '';
                $valor_venta = '';

                if (isset($consolidado_detalles[$i])) {
                    $id = sumCod($consolidado_detalles[$i]->producto_id, 4);
                    $nombre = $consolidado_detalles[$i]->producto_nombre;
                    $um = $consolidado_detalles[$i]->um;
                    $cantidad = intval($consolidado_detalles[$i]->cantidad);
                    $valor_unitario = $consolidado_detalles[$i]->precio;
                    $valor_venta = $consolidado_detalles[$i]->importe;
                }

                $index_p = $index . '-' . ($i + 1);
                $template->setValue($index_p, $id);
                $template->setValue('c' . $index_p, $cantidad);
                $template->setValue('u' . $index_p, htmlspecialchars($um));
                $template->setValue('producto' . $index_p, htmlspecialchars($nombre));
                $template->setValue('prc' . $index_p, $valor_unitario);
                $template->setValue('v' . $index_p, $valor_venta);
            }
        }

        $template->saveAs(sys_get_temp_dir() . '/remision_temp.docx');
        header("Content-Disposition: attachment; filename='remision.docx'");
        readfile(sys_get_temp_dir() . '/remision_temp.docx'); // or echo file_get_contents($temp_file);
        unlink(sys_get_temp_dir() . '/remision_temp.docx');
    }

    function imprimir_notas($id, $tipo_id = 'CONSOLIDADO')
    {
        $this->db->select('
            k.serie AS serie,
            k.numero AS numero,
            c.razon_social AS razon_social,
            c.ruc_cliente AS ruc,
            c.identificacion AS ruc_or_dni,
            c.id_cliente AS cliente_id,
            c.nombre_boleta AS nombre_boleta,
            cd.consolidado_id AS consolidado_id,
            v.venta_id AS venta_id,
            gc.nombre_grupos_cliente AS tipo_cliente,
            cp.nombre_condiciones AS venta_condicion,
            hpp.created_at AS fecha_venta,
            u.username AS vendedor,
            v.tipo_doc_fiscal AS tipo_doc
        ')->from('consolidado_detalle AS cd')
            ->join('venta AS v', 'v.venta_id = cd.pedido_id')
            ->join('kardex AS k', 'k.ref_id = v.venta_id')
            ->join('historial_pedido_proceso AS hpp', 'hpp.pedido_id = v.venta_id')
            ->join('usuario AS u', 'u.nUsuCodigo = v.id_vendedor')
            ->join('condiciones_pago AS cp', 'cp.id_condiciones = v.condicion_pago')
            ->join('cliente AS c', 'c.id_cliente = v.id_cliente')
            ->join('grupos_cliente AS gc', 'gc.id_grupos_cliente = c.grupo_id')
            ->where('hpp.proceso_id', PROCESO_IMPRIMIR)
            ->where("(v.venta_status = 'RECHAZADO' OR v.venta_status = 'DEVUELTO PARCIALMENTE')");

        if ($tipo_id == 'CONSOLIDADO')
            $this->db->where('cd.consolidado_id', $id);
        elseif ($tipo_id == 'VENTA')
            $this->db->where('v.venta_id', $id);

        $pedidos_rechazados = $this->db->group_by('k.serie, k.numero')
            ->get()->result();

        $notas_credito = array();

        foreach ($pedidos_rechazados as $rechazos) {
            $temp = new stdClass();

            $temp->cliente = $rechazos->tipo_doc == 'FACTURA' || $rechazos->nombre_boleta == 1 ? $rechazos->razon_social : '';
            $temp->ruc = $rechazos->tipo_doc == 'FACTURA' || $rechazos->nombre_boleta == 1 ? $rechazos->ruc : '';

            if ($rechazos->tipo_doc == 'FACTURA' || $rechazos->nombre_boleta == 1) {
                $direccion = $this->db->get_where('cliente_datos', array(
                    'cliente_id' => $rechazos->cliente_id,
                    'tipo' => 1,
                    'principal' => 1,
                ))->row();
                $temp->direccion = $direccion->valor;
            } else
                $temp->direccion = '';

            $temp->consolidado = $rechazos->consolidado_id;

            $kardex = $this->db->get_where('kardex', array(
                'serie' => $rechazos->serie,
                'numero' => $rechazos->numero,
                'ref_id' => $rechazos->venta_id,
                'tipo_doc' => 7,
                'tipo_operacion' => 5,
                'IO' => 2,
            ))->row();

            $temp->documento = $kardex->serie . ' - ' . $kardex->numero;
            $temp->doc_referencia = $kardex->referencia;
            $temp->fecha = $kardex->fecha;

            $temp->pedido = $rechazos->venta_id;
            $temp->tipo_cliente = $rechazos->tipo_cliente;
            $temp->venta_condicion = $rechazos->venta_condicion;
            $temp->fecha_venta = $rechazos->fecha_venta;
            $temp->vendedor = $rechazos->vendedor;
            $temp->cliente_id = $rechazos->cliente_id;

            $productos = $this->db->select('
                k.producto_id AS producto_id,
                p.producto_nombre AS producto_nombre,
                u.nombre_unidad AS unidad_nombre,
                k.cantidad AS cantidad,
                k.costo_unitario AS precio,
                k.total AS importe,
            ')->from('kardex AS k')
                ->join('producto AS p', 'p.producto_id = k.producto_id')
                ->join('unidades AS u', 'u.id_unidad = k.unidad_id')
                ->where('serie', $rechazos->serie)
                ->where('numero', $rechazos->numero)
                ->where('ref_id', $rechazos->venta_id)
                ->where('tipo_doc', 7)
                ->where('tipo_operacion', 5)
                ->where('IO', 2)
                ->get()->result();

            $productos_nota = array();
            foreach ($productos as $p) {
                $temp_productos = new stdClass();
                $temp_productos->codigo = sumCod($p->producto_id, 4);
                $temp_productos->producto = $p->producto_nombre;
                $temp_productos->um = $p->unidad_nombre;
                $temp_productos->cantidad = $p->cantidad >= 0 ? $p->cantidad : $p->cantidad * -1;
                $temp_productos->precio = $p->precio;
                $temp_productos->importe = $p->importe >= 0 ? $p->importe : $p->importe * -1;

                $productos_nota[] = $temp_productos;
            }

            $temp->productos = $productos_nota;
            $notas_credito[] = $temp;
        }


        $cantidad_paginas = count($notas_credito);

        $template_name = 'nota_credito' . $cantidad_paginas . '.docx';
        $word = new \PhpOffice\PhpWord\PhpWord();
        $template = new \PhpOffice\PhpWord\TemplateProcessor(base_url('recursos/formatos/nota_credito/' . $template_name));


        for ($n = 0; $n < $cantidad_paginas; $n++) {

            $index = $n + 1;
            $template->setValue('cliente' . $index, htmlspecialchars($notas_credito[$n]->cliente));
            $template->setValue('ruc' . $index, $notas_credito[$n]->ruc);
            $template->setValue('direccion' . $index, htmlspecialchars($notas_credito[$n]->direccion));
            $template->setValue('consolidado' . $index, $notas_credito[$n]->consolidado);

            $template->setValue('documento' . $index, $notas_credito[$n]->documento);
            $template->setValue('pedido' . $index, $notas_credito[$n]->pedido);
            $template->setValue('tipo_cliente' . $index, htmlspecialchars($notas_credito[$n]->tipo_cliente));
            $template->setValue('venta_cond' . $index, htmlspecialchars($notas_credito[$n]->venta_condicion));
            $template->setValue('fecha' . $index, date('d/m/Y', strtotime($notas_credito[$n]->fecha)));
            $template->setValue('fecha_v' . $index, date('d/m/Y', strtotime($notas_credito[$n]->fecha_venta)));
            $template->setValue('vendedor' . $index, htmlspecialchars($notas_credito[$n]->vendedor));
            $template->setValue('cliente_id' . $index, htmlspecialchars($notas_credito[$n]->cliente_id));

            $detalle_index = 0;
            $total = 0;
            for ($i = 0; $i < 12; $i++) {
                $codigo = '';
                $producto = '';
                $um = '';
                $cantidad = '';
                $precio = '';
                $importe = '';

                if (isset($notas_credito[$n]->productos[$detalle_index])) {
                    $codigo = $notas_credito[$n]->productos[$detalle_index]->codigo;
                    $producto = $notas_credito[$n]->productos[$detalle_index]->producto;
                    $um = $notas_credito[$n]->productos[$detalle_index]->um;
                    $cantidad = $notas_credito[$n]->productos[$detalle_index]->cantidad;
                    $precio = $notas_credito[$n]->productos[$detalle_index]->precio;
                    $importe = $notas_credito[$n]->productos[$detalle_index]->importe;

                    $total += $importe;
                    $detalle_index++;
                }

                $index_p = $index . '-' . ($i + 1);
                $template->setValue('c' . $index_p, $codigo);
                $template->setValue('producto' . $index_p, htmlspecialchars($producto));
                $template->setValue('um' . $index_p, htmlspecialchars($um));
                $template->setValue('ca' . $index_p, $cantidad);
                $template->setValue('pre' . $index_p, $precio);
                $template->setValue('imp' . $index_p, $importe);
            }

            $template->setValue('doc_referencia' . $index, $notas_credito[$n]->doc_referencia);

            $igv = $total * 1.18 - $total;
            $sub = $total - $igv;

            $template->setValue('letras' . $index, numtoletras($total));

            $template->setValue('sub' . $index, number_format($sub, 2));
            $template->setValue('igv' . $index, number_format($igv, 2));
            $template->setValue('total' . $index, number_format($total, 2));
        }

        $template->saveAs(sys_get_temp_dir() . '/nota_credito_temp.docx');
        header("Content-Disposition: attachment; filename='nota_credito.docx'");
        readfile(sys_get_temp_dir() . '/nota_credito_temp.docx'); // or echo file_get_contents($temp_file);
        unlink(sys_get_temp_dir() . '/nota_credito_temp.docx');
    }

    function imprimir_consolidado($id)
    {


        $consolidado = $this->db->select('
            c.*, 
            u_carga.nombre AS responsable,
            chofer.nombre AS chofer,
            camiones.camiones_placa as camion
            ')
            ->from('consolidado_carga AS c')
            ->join('usuario AS u_carga', 'u_carga.nUsuCodigo = c.generado_por')
            ->join('camiones', 'camiones.camiones_id = c.camion')
            ->join('usuario AS chofer', 'chofer.nUsuCodigo = camiones.id_trabajadores')
            ->where('consolidado_id', $id)
            ->get()->row();

        $pedidos_zonas = $this->db->select('z.zona_nombre as zona')
            ->from('venta AS v')
            ->join('consolidado_detalle AS cd', 'cd.pedido_id = v.venta_id')
            ->join('cliente AS c', 'c.id_cliente = v.id_cliente')
            ->join('zonas AS z', 'z.zona_id = c.id_zona')
            ->where('cd.consolidado_id', $id)
            ->group_by('z.zona_id')
            ->get()->result();

        $index = 0;
        $zonas = '';
        foreach ($pedidos_zonas as $p) {
            $zonas .= $p->zona;
            if ($index < count($pedidos_zonas) - 1)
                $zonas .= ', ';
            $index++;
        }

        $split = true;

        if ($consolidado->status == 'ABIERTO') {
            $pedidos = $this->db->get_where('consolidado_detalle', array('consolidado_id' => $id))->result();
            foreach ($pedidos as $pedido) {
//                $proceso = $this->db->get_where('historial_pedido_proceso', array(
//                    'proceso_id' => PROCESO_IMPRIMIR,
//                    'pedido_id' => $pedido->pedido_id,
//                ))->row();

//                if ($proceso == NULL) {
                $this->historial_pedido_model->insertar_pedido(PROCESO_IMPRIMIR, array(
                    'pedido_id' => $pedido->pedido_id,
                    'responsable_id' => $this->session->userdata('nUsuCodigo'),
                    'fecha_plan' => $consolidado->fecha
                ));
                $split = $this->venta_model->generar_documentos_fiscales($pedido->pedido_id);
                if ($split != true) {
                    echo "No se ha podido generar los documentos del pedido " . $pedido->pedido_id . ". Revise el logger y contacta a Antonio Martin.";
                    return false;
                }
//                }
            }

            $this->db->where('consolidado_id', $id);
            $this->db->update('consolidado_carga', array('status' => 'IMPRESO'));
        }

        if ($split == true) {
            $template_name = 'consolidado.docx';
            $word = new \PhpOffice\PhpWord\PhpWord();
            $template = new \PhpOffice\PhpWord\TemplateProcessor(base_url('recursos/formatos/consolidado/' . $template_name));


            //CABECERA
            $template->setValue('empresa', htmlspecialchars(valueOption('EMPRESA_NOMBRE', 'TEAYUDO')));
            $template->setValue('fecha', date('d/m/Y', strtotime($consolidado->fecha)));
            $template->setValue('numero_consolidado', $consolidado->consolidado_id);
            $template->setValue('responsable', htmlspecialchars($consolidado->responsable));
            $template->setValue('camion', htmlspecialchars($consolidado->camion));
            $template->setValue('zona', htmlspecialchars($zonas));
            $template->setValue('chofer', htmlspecialchars($consolidado->chofer));

            //PRODUCTOS
            $productos = $this->db->select('
            hpd.producto_id AS codigo,
            p.producto_nombre AS producto,
            u.nombre_unidad AS um,
            p.presentacion AS medida,
            SUM(hpd.stock) AS cantidad,
            g.nombre_grupo AS grupo
            ')
                ->from('consolidado_detalle AS cd')
                ->join('historial_pedido_proceso AS hpp', 'hpp.pedido_id = cd.pedido_id')
                ->join('historial_pedido_detalle AS hpd', 'hpd.historial_pedido_proceso_id = hpp.id')
                ->join('producto AS p', 'p.producto_id = hpd.producto_id')
                ->join('grupos AS g', 'g.id_grupo = p.produto_grupo')
                ->join('unidades AS u', 'u.id_unidad = hpd.unidad_id')
                ->where('hpp.proceso_id', PROCESO_IMPRIMIR)
                ->where('cd.consolidado_id', $id)
                ->group_by('hpd.producto_id, hpd.unidad_id')
                ->order_by('g.id_grupo, p.producto_nombre')
                ->get()->result();

            $grupos = array();
            $grupo = null;
            $index = -1;
            foreach ($productos as $producto) {

                if ($grupo != $producto->grupo) {
                    $grupos[++$index] = array(
                        'nombre' => $producto->grupo,
                        'productos' => array($producto)
                    );
                    $grupo = $producto->grupo;
                } else {
                    $grupos[$index]['productos'][] = $producto;
                }

            }

            $template->cloneBlock('block', count($grupos));
//        return false;

            $index = 0;
            $total = 0;
            foreach ($grupos as $grupo) {

                $template->setValue('linea_' . ++$index, htmlspecialchars($grupo['nombre']));

                $template->cloneRow('cod_prod_' . $index, count($grupo['productos']));
                $index_p = 0;
                $subtotal = 0;
                foreach ($grupo['productos'] as $producto) {

                    $template->setValue('cod_prod_' . $index . '#' . ++$index_p, sumCod($producto->codigo, 4));
                    $template->setValue('producto_nombre_' . $index . '#' . $index_p, htmlspecialchars($producto->producto));
                    $template->setValue('unidad_' . $index . '#' . $index_p, htmlspecialchars($producto->um));
                    $template->setValue('medida_' . $index . '#' . $index_p, htmlspecialchars($producto->medida));
                    $template->setValue('cantidad_' . $index . '#' . $index_p, $producto->cantidad);

                    $subtotal += $producto->cantidad;
                }

                $total += $subtotal;
                $template->setValue('sub_total_' . $index, $subtotal);
            }

            $template->setValue('total_general', $total);


            //NOTAS DE ENTREGA
            $pedidos = $this->db->select('
            v.venta_id AS venta_id,
            v.venta_status AS estado,
            dv.documento_Serie AS serie,
            dv.documento_Numero AS numero
            ')
                ->from('venta AS v')
                ->join('consolidado_detalle AS cd', 'cd.pedido_id = v.venta_id')
                ->join('documento_venta AS dv', 'dv.id_tipo_documento = v.numero_documento')
                ->join('cliente AS c', 'c.id_cliente = v.id_cliente')
                ->join('zonas AS z', 'z.zona_id = c.id_zona')
                ->where('cd.consolidado_id', $id)
                ->order_by('v.venta_id', 'ASC')
                ->get()->result();

            $nota_entrega1 = '';
            $nota_entrega2 = '';
            $estado1 = '';
            $estado2 = '';
            $n = true;

            foreach ($pedidos as $pedido) {
                if ($n) {
                    $nota_entrega1 .= $pedido->serie . " - " . $pedido->numero . "\r\n";
                    $estado1 .= $pedido->estado . "\r\n";
                    $n = false;
                } else {
                    $nota_entrega2 .= $pedido->serie . " - " . $pedido->numero . "\r\n";
                    $estado2 .= $pedido->estado . "\r\n";
                    $n = true;
                }
            }

            $template->setValue('nota_entrega1', $nota_entrega1);
            $template->setValue('estado1', $estado1);

            $template->setValue('nota_entrega2', $nota_entrega2);
            $template->setValue('estado2', $estado2);


            $template->saveAs(sys_get_temp_dir() . '/consolidado_temp.docx');
            header("Content-Disposition: attachment; filename='consolidado" . $id . ".docx'");
            readfile(sys_get_temp_dir() . '/consolidado_temp.docx'); // or echo file_get_contents($temp_file);
            unlink(sys_get_temp_dir() . '/consolidado_temp.docx');

        } else {
            echo "No se ha podido generar los documentos de este consolidado. Revise el logger y contacta a Antonio Martin.";
        }


    }


    function pedidoDevolucion($id)
    {
        $data['detalleProducto'] = $this->consolidado_model->get_detalle_devueltos($id);

        $data['notasdeentrega'] = $this->consolidado_model->get_documentoVenta_by_id($id, false);
        $data['consolidado'] = $data['detalleProducto'][0];

        // documento
        $phpword = new \PhpOffice\PhpWord\PhpWord();
        $styles = array(
            'pageSizeW' => '12755.905511811',
            'pageSizeH' => '15874.015748031',
            'marginTop' => '396.850393701',
            'marginLeft' => '453.858267717',
            'marginRight' => '866.929133858',
            'marginBottom' => '566.929133858',
        );

        $section = $phpword->addSection($styles);
        $phpword->addFontStyle('rStyle', array('size' => 18, 'allCaps' => true));
        $phpword->addFontStyle('rBasicos', array('size' => 8, 'allCaps' => true));
        $phpword->addParagraphStyle('pStyle', array('align' => 'left'));
        $phpword->addParagraphStyle('totales', array('align' => 'right'));
        $phpword->addParagraphStyle('headtab', array('align' => 'center'));

        $tablastyle = array('width' => 50 * 100, 'unit' => 'pct', 'align' => 'left');
        $subsequent = $section->addHeader();
        $subsequent->addPreserveText(htmlspecialchars('Pag {PAGE} de {NUMPAGES}.'), null, array('align' => 'right'));

        // tabla titulos
        $table = $section->addTable($tablastyle);
        $campo = $data['consolidado'];
        $cell = $table->addRow()->addCell(5000, array('valign ' => 'center', 'align' => 'left'));
        $cell->addText(htmlentities(date('d/m/Y', strtotime($campo['fecha']))), 'rBasicos', 'pStyle');

        $cell = $table->addCell(5000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText(htmlentities(strtoupper($this->session->userdata('EMPRESA_NOMBRE'))), 'rStyle', 'pStyle');

        $cell = $table->addCell(5000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText();

        $cell = $table->addRow()->addCell(5000, array('valign ' => 'center', 'align' => 'center', 'gridSpan' => 3));
        $cell->addText('Consolidado de Guía de Carga - DEVOLUCIONES', array('italic' => true, 'bold' => true, 'size' => 16), 'pStyle');

        $cell = $table->addRow()->addCell(null, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText('LIQUIDACION DE REFERENCIA: ' . $campo['consolidado_id'], 'rBasicos', 'pStyle');
        $table->addCell(null)->addText('RESPONSABLE: ' . $campo['userCarga'], 'rBasicos', 'pStyle');
        $table->addCell(null)->addText('VEHICULO: ' . $campo['camiones_placa'], 'rBasicos', 'pStyle');


        $cell = $table->addRow()->addCell(5000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText('Almacen: ' . $campo['local_nombre'], 'rBasicos', 'pStyle');
        $table->addCell(null)->addText('ZONA: ' . $campo['zona_nombre'], 'rBasicos', 'pStyle');
        $table->addCell(null)->addText('CHOFER: ' . $campo['chofernombre'], 'rBasicos', 'pStyle');

        $pdid = $data['detalleProducto'][0]['id_grupo'];
        $gruponombre = $data['detalleProducto'][0]['nombre_grupo'];
        $cantidadtotalgrupo = 0;
        $cantidad_total = 0;
        $count = 1;

        $w1 = 1530;
        $w2 = 850;
        $w3 = 4138;
        $w4 = 1417;
        $w5 = 1700;
        $w6 = 1417;
        //$section->addTextBreak(1);
        $table1 = $section->addTable($tablastyle);
        $table1->addRow(250, array('exactHeight' => true))->addCell(null, array('align' => 'right', 'gridSpan' => 6))
            ->addText('_____________________________________________________________________________________________________________');
        $table1->addRow(250, array('exactHeight' => true))->addCell($w1, array('valign ' => 'bottom'))->addText(htmlspecialchars('LINEA'), 'rBasicos');
        $table1->addCell($w2)->addText(htmlspecialchars('CODIGO'), 'rBasicos');
        $table1->addCell($w3)->addText(htmlspecialchars('PRODUCTO'), 'rBasicos');
        $table1->addCell($w4)->addText(htmlspecialchars('UNIDAD'), 'rBasicos');
        $table1->addCell($w5)->addText(htmlspecialchars('MEDIDA'), 'rBasicos');
        $table1->addCell($w6)->addText(htmlspecialchars('CANT'), 'rBasicos', 'headtab');
        $table1->addRow(250, array('exactHeight' => true))->addCell(null, array('align' => 'right', 'gridSpan' => 6))
            ->addText('______________________________________________________________________________________________________________');

        // tabla de productos
        $table1 = $section->addTable($tablastyle);
        // var_dump($data['detalleProducto']);
        foreach ($data['detalleProducto'] as $campoProducto) {
            //var_dump($campoProducto);


            $cantidadnueva = 0;

            $cantidad_nueva = $this->consolidado_model->get_cantiad_vieja_by_product($campoProducto['producto_id'], $campoProducto['consolidado_id']);
            //           var_dump($cantidad_nueva);
            if (sizeof($cantidad_nueva) > 0) {
                $cantidadnueva = $cantidad_nueva['cantidadnueva'];
            }


            //  echo $cantidadnueva;
            $cantidadvieja = $campoProducto['cantidadTotal'];


            $cantidad = floatval($cantidadvieja);

            if ($cantidad > 0) {
                if ($count == 1) {
                    $table1->addRow(200, array('exactHeight' => true));
                    if (empty($campoProducto['produto_grupo'])) {
                        $table1->addCell(null, array('gridSpan' => 6))->addText('TOTAL SIN GRUPO', 'rBasicos');
                    } else {
                        $table1->addCell(null, array('gridSpan' => 6))->addText(htmlspecialchars(strtoupper($gruponombre)), 'rBasicos');
                    }
                }

                if ($campoProducto['id_grupo'] != $pdid) {
                    $table1->addRow(200, array('exactHeight' => true));
                    $table1->addCell(null, array('gridSpan' => 5));
                    $table1->addCell(2, array('align' => 'right'))->addText('_____________', 'rBasicos', 'totales');

                    $table1->addRow(200, array('exactHeight' => true));
                    if (empty($campoProducto['produto_grupo'])) {

                        $table1->addCell($w1);
                        $table1->addCell($w2);
                        $table1->addCell($w3);
                        $table1->addCell($w4);
                        $table1->addCell($w5)->addText('TOTAL SIN GRUPO', 'rBasicos');
                        $table1->addCell($w6)->addText(number_format($cantidadtotalgrupo, 2), 'rBasicos', 'totales');

                    } else {

                        $table1->addCell($w1);
                        $table1->addCell($w2);
                        $table1->addCell($w3);
                        $table1->addCell($w4);
                        $table1->addCell($w5)->addText('TOTAL ' . strtoupper($gruponombre), 'rBasicos');
                        $table1->addCell($w6)->addText(number_format($cantidadtotalgrupo, 2), 'rBasicos', 'totales');
                    }
                    $pdid = $campoProducto['id_grupo'];
                    $gruponombre = $campoProducto['nombre_grupo'];
                    $cantidadtotalgrupo = 0;

                    $table1->addRow(200, array('exactHeight' => true));
                    if (empty($campoProducto['produto_grupo'])) {
                        $table1->addCell(null, array('gridSpan' => 6))->addText('SIN GRUPO', 'rBasicos');
                    } else {
                        $table1->addCell(null, array('gridSpan' => 6))->addText(htmlspecialchars(strtoupper($gruponombre)), 'rBasicos');
                    }

                }


                $cantidadtotalgrupo = $cantidadtotalgrupo + $cantidad;

                $table1->addRow(200, array('exactHeight' => true));
                $table1->addCell($w1)->addText();
                $table1->addCell($w2)->addText(htmlspecialchars($campoProducto['producto_id']), 'rBasicos');
                $table1->addCell($w3)->addText(htmlspecialchars(strtoupper($campoProducto['producto_nombre'])), 'rBasicos');
                $table1->addCell($w4)->addText(htmlspecialchars(strtoupper($campoProducto['nombre_unidad'])), 'rBasicos');
                $table1->addCell($w5)->addText(htmlspecialchars(strtoupper($campoProducto['presentacion'])), 'rBasicos');
                $table1->addCell($w6)->addText(htmlspecialchars(strtoupper($cantidad)), 'rBasicos', 'totales');


                if ($count === sizeof($data['detalleProducto'])) {
                    $table1->addRow(200, array('exactHeight' => true));
                    $table1->addCell(null, array('gridSpan' => 5));
                    $table1->addCell($w6, array('align' => 'right'))->addText('_____________', 'rBasicos', 'totales');

                    $table1->addRow(200, array('exactHeight' => true));
                    if (empty($campoProducto['produto_grupo'])) {

                        $table1->addCell($w1);
                        $table1->addCell($w2);
                        $table1->addCell($w3);
                        $table1->addCell($w4);
                        $table1->addCell($w5)->addText('TOTAL SIN GRUPO', 'rBasicos');
                        $table1->addCell($w6)->addText(number_format($cantidadtotalgrupo, 2), 'rBasicos', 'totales');
                    } else {
                        $table1->addCell($w1);
                        $table1->addCell($w2);
                        $table1->addCell($w3);
                        $table1->addCell($w4);
                        $table1->addCell($w5)->addText('TOTAL ' . strtoupper($gruponombre), 'rBasicos');
                        $table1->addCell($w6)->addText(number_format($cantidadtotalgrupo, 2), 'rBasicos', 'totales');
                    }
                    $pdid = $campoProducto['id_grupo'];
                    $gruponombre = $campoProducto['nombre_grupo'];
                    $cantidadtotalgrupo = 0;
                }

                $cantidad_total = $cantidad_total + $cantidad;
            }

            $count++;

        }

        $table1->addRow(200, array('exactHeight' => true));
        $table1->addCell(null, array('gridSpan' => 5));
        $table1->addCell(2, array('align' => 'right'))->addText('_____________', 'rBasicos', 'totales');

        $table1->addRow(200, array('exactHeight' => true));
        $table1->addCell($w1);
        $table1->addCell($w2);
        $table1->addCell($w3);
        $table1->addCell($w4);
        $table1->addCell($w5)->addText('TOTAL ALMACEN ' . strtoupper($campo['local_nombre']), 'rBasicos');
        $table1->addCell($w6)->addText(number_format($cantidad_total, 2), 'rBasicos', 'totales');

        $table1->addRow(200, array('exactHeight' => true));
        $table1->addCell(null, array('gridSpan' => 5));
        $table1->addCell($w6, array('align' => 'right'))->addText('_____________', 'rBasicos', 'totales');

        $table1->addRow(200, array('exactHeight' => true));
        $table1->addCell($w1);
        $table1->addCell($w2);
        $table1->addCell($w3);
        $table1->addCell($w4);
        $table1->addCell($w5)->addText('TOTAL GENERAL', 'rBasicos');
        $table1->addCell($w6)->addText(number_format($cantidad_total, 2), 'rBasicos', 'totales');
        $table1->addRow(250, array('exactHeight' => true))->addCell(null, array('align' => 'left', 'gridSpan' => 6))
            ->addText('_____________________________________________________________________________________________________________');


        $section->addTextBreak(1);
        $table1 = $section->addTable($tablastyle);
        $table1->addRow(250, array('exactHeight' => true));
        $table1->addCell(4000)->addText('NOTA DE ENTREGA', 'rBasicos', 'pStyle');
        $table1->addCell(5000)->addText('ESTADO', 'rBasicos', 'pStyle');
        $table1->addCell($w3)->addText('OBSERVACIONES', 'rBasicos', 'pStyle');
        $table1->addRow(250, array('exactHeight' => true))->addCell(null, array('align' => 'left', 'gridSpan' => 6))
            ->addText('_____________________________________________________________________________________________________________');
        $table1->addRow(380, array('exactHeight' => true));
        $c = 0;
        foreach ($data['notasdeentrega'] as $campoProducto) {
            if ($c >= 1) {
                $table1->addRow(380, array('exactHeight' => true));
            }
            $table1->addCell(4000)->addText($campoProducto['documento_Serie'] . $campoProducto['documento_Numero'], 'rBasicos');
            $table1->addCell(5000)->addText($campoProducto['venta_status'], 'rBasicos', 'pStyle');
            $table1->addCell($w3)->addText('', 'rBasicos', 'pStyle');

            $c++;

        }


        $file = 'ConsolidadoDevolucion' . $id . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007');
        $xmlWriter->save("php://output");
    }

    function pedidoPreCancelacion($id)
    {

        $data['detalleProducto'] = $this->consolidado_model->get_detalle($id);
        $data['notasdeentrega'] = $this->consolidado_model->get_documentoVenta_by_id($id, false);
        $data['consolidado'] = $data['detalleProducto'][0];

        // var_dump($data['detalleProducto']);
        // documento
        $phpword = new \PhpOffice\PhpWord\PhpWord();
        $styles = array(
            'pageSizeW' => '12755.905511811',
            'pageSizeH' => '15874.015748031',
            'marginTop' => '396.850393701',
            'marginLeft' => '453.858267717',
            'marginRight' => '866.929133858',
            'marginBottom' => '566.929133858',
        );

        $section = $phpword->addSection($styles);
        $phpword->addFontStyle('rStyle', array('size' => 18, 'allCaps' => true));
        $phpword->addFontStyle('rBasicos', array('size' => 8, 'allCaps' => true));
        $phpword->addParagraphStyle('pStyle', array('align' => 'left'));
        $phpword->addParagraphStyle('totales', array('align' => 'right'));
        $phpword->addParagraphStyle('headtab', array('align' => 'center'));

        $tablastyle = array('width' => 50 * 100, 'unit' => 'pct', 'align' => 'left');
        $subsequent = $section->addHeader();
        $subsequent->addPreserveText(htmlspecialchars('Pag {PAGE} de {NUMPAGES}.'), null, array('align' => 'right'));

        // tabla titulos
        $table = $section->addTable($tablastyle);
        $campo = $data['consolidado'];
        $cell = $table->addRow()->addCell(5000, array('valign ' => 'center', 'align' => 'left'));
        $cell->addText(htmlentities(date('d/m/Y', strtotime($campo['fecha']))), 'rBasicos', 'pStyle');

        $cell = $table->addCell(5000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText(htmlentities(strtoupper($this->session->userdata('EMPRESA_NOMBRE'))), 'rStyle', 'pStyle');

        $cell = $table->addCell(5000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText();

        $cell = $table->addRow()->addCell(5000, array('valign ' => 'center', 'align' => 'center', 'gridSpan' => 3));
        $cell->addText('Consolidado de Guía de Carga - PRE-CANCELACION', array('italic' => true, 'bold' => true, 'size' => 16), 'pStyle');

        $cell = $table->addRow()->addCell(4000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText('LIQUIDACION DE REFERENCIA: ' . $campo['consolidado_id'], 'rBasicos', 'pStyle');
        $table->addCell(4000)->addText('RESPONSABLE: ' . $campo['userCarga'], 'rBasicos', 'pStyle');
        $table->addCell(4000)->addText('VEHICULO: ' . $campo['camiones_placa'], 'rBasicos', 'pStyle');


        $cell = $table->addRow()->addCell(4000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText('Almacen: ' . $campo['local_nombre'], 'rBasicos', 'pStyle');
        $table->addCell(4000)->addText('ZONA: ' . $campo['zona_nombre'], 'rBasicos', 'pStyle');
        $table->addCell(4000)->addText('CHOFER ' . $campo['chofernombre'], 'rBasicos', 'pStyle');


        $cantidad_total = 0;
        $count = 1;

        $w1 = 1530;
        $w2 = 850;
        $w3 = 4138;
        $w4 = 1417;
        $w5 = 1700;
        $w6 = 1417;
        //$section->addTextBreak(1);


        $table1 = $section->addTable($tablastyle);

        $table1->addRow(250, array('exactHeight' => true))->addCell(null, array('align' => 'left', 'gridSpan' => 6))
            ->addText('_____________________________________________________________________________________________________________');

        $table1->addRow(380, array('exactHeight' => true));
        $table1->addCell($w1)->addText('NOTA DE ENTREGA', 'rBasicos', 'pStyle');
        $table1->addCell($w1)->addText('ESTADO', 'rBasicos', 'pStyle');
        $table1->addCell($w1)->addText('IMPORTE', 'rBasicos', 'pStyle');
        $table1->addCell($w1)->addText('ESTADO', 'rBasicos', 'pStyle');
        $table1->addCell($w3)->addText('OBSERVACIONES', 'rBasicos', 'pStyle');
        $table1->addRow(250, array('exactHeight' => true))->addCell(null, array('align' => 'left', 'gridSpan' => 6))
            ->addText('_____________________________________________________________________________________________________________');
        $table1->addRow(380, array('exactHeight' => true));
        $c = 0;
        $totalImporte = 0;
        foreach ($data['notasdeentrega'] as $campoProducto) {

            $liquidacion_monto_cobrado = floatval($campoProducto['liquidacion_monto_cobrado']);
            $totalImporte = $totalImporte + $liquidacion_monto_cobrado;

            if ($c >= 1) {
                $table1->addRow(380, array('exactHeight' => true));
            }
            $table1->addCell($w2)->addText($campoProducto['documento_Serie'] . $campoProducto['documento_Numero'], 'rBasicos');
            $table1->addCell($w2)->addText($campoProducto['venta_status'], 'rBasicos', 'pStyle');
            $table1->addCell($w2)->addText(MONEDA . $liquidacion_monto_cobrado, 'rBasicos', 'pStyle');
            $table1->addCell($w2)->addText($campoProducto['var_credito_estado'], 'rBasicos', 'pStyle');
            $table1->addCell($w2)->addText('');


            $c++;

        }
        $table1->addRow(250, array('exactHeight' => true))->addCell(null, array('align' => 'left', 'gridSpan' => 6))
            ->addText('____________________________________________');
        $table1->addRow(250, array('exactHeight' => true))->addCell(null, array('align' => 'left', 'gridSpan' => 1));
        $table1->addCell($w1)->addText('TOTAL IMPORTE', 'rBasicos', 'pStyle');
        $table1->addCell($w1)->addText(MONEDA . $totalImporte, 'rBasicos', 'pStyle');

        $file = 'ConsolidadoPreCancelacion' . $id . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007');
        $xmlWriter->save("php://output");
    }

    function pdfModal($id)
    {

        $detalles = $this->consolidado_model->get_details_by(array('consolidado_id' => $id));

        //var_dump($miembro);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Detalle productos');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);

//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.???.G.???.D.???.G.???.A.???.D.???.U.???.<br>Gran Logia de la Rep�blica de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


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

//relaci�n utilizada para ajustar la conversi�n de los p�xeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir car�cteres ASCII est�ndar, puede utilizar las fuentes b�sicas como
// Helvetica para reducir el tama�o del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// A�adir una p�gina
// Este m�todo tiene varias opciones, consulta la documentaci�n para m�s informaci�n.
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

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>CONSOLIDADO DE DOCUMENTOS</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        $html .= "<table><tr>";
        $html .= "<th>Tipo de documento</th>";
        $html .= "<th>Numero de documento</th><th>Cantidad</th>";
        $html .= "<th>Total</th>";
        $html .= "<th>Status</th>";
        $html .= "</tr>";


        foreach ($detalles as $campoDetalles) {

            $html .= "<tr><td>" . $campoDetalles['nombre_tipo_documento'] . "</td>";
            $html .= "<td>" . $campoDetalles['documento_Serie'] . "-" . $campoDetalles['documento_Numero'] . "</td>";
            $html .= "<td>" . $campoDetalles['cantidad_prductos'] . "</td>";
            $html .= "<td>" . $campoDetalles['total'] . "</td>";
            $html .= "<td>" . $campoDetalles['venta_status'] . "</td>";
            $html .= "</tr>";

        }
        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este m�todo tiene varias opciones, consulte la documentaci�n para m�s informaci�n.
        $nombre_archivo = utf8_decode("Consolidado_de_documentos.pdf");
        $pdf->Output($nombre_archivo, 'D');
    }

    function pdfRemision($id)
    {

        $detalles = $this->consolidado_model->get_detalle($id);

        //var_dump($miembro);
        $pdf = new Pdf('P', 'mm', 'A5', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');

        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Guia de remision');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);

//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.???.G.???.D.???.G.???.A.???.D.???.U.???.<br>Gran Logia de la Rep�blica de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


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

//relaci�n utilizada para ajustar la conversi�n de los p�xeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir car�cteres ASCII est�ndar, puede utilizar las fuentes b�sicas como
// Helvetica para reducir el tama�o del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// A�adir una p�gina
// Este m�todo tiene varias opciones, consulta la documentaci�n para m�s informaci�n.
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

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; }";
        $html .= "td{color: #222;}";
        $html .= "body{font-size:15px}";
        $html .= "span{ width:20px; position:absolute; height:10px; border:red solid 1px;}";
        $html .= "</style>";


        $html .= "<br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br /><br />";
        $html .= "<table><tr>";
        $html .= "<th>Unidad de medida</th>";
        $html .= "<th>Nombre</th><th>Cantidad</th>";
        $html .= "<th>Peso total</th>";

        $html .= "</tr>";


        foreach ($detalles as $campoDetalles) {

            $html .= "<tr><td>" . $campoDetalles['nombre_unidad'] . "</td>";
            $html .= "<td>" . $campoDetalles['producto_nombre'] . "</td>";
            $html .= "<td>" . $campoDetalles['cantidadTotal'] . "</td>";
            $html .= "<td>" . $campoDetalles['metros_cubicos'] * $campoDetalles['cantidadTotal'] . "</td>";
            $html .= "</tr>";

        }
        $html .= "</table>";


// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este m�todo tiene varias opciones, consulte la documentaci�n para m�s informaci�n.
        $pdf->IncludeJS('print({bUI: true});');
        $nombre_archivo = utf8_decode("GuiaDeRemision.pdf");
        $pdf->Output($nombre_archivo, 'I');

    }


}

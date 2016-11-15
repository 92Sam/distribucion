<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

//include FCPATH.'application/libraries/phpodt/phpodt.php';
class consolidadodecargas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();
        $this->load->model('consolidadodecargas/consolidado_model');
        $this->load->model('venta/venta_model');
        $this->load->model('banco/banco_model');

        $this->load->library('Pdf');
        $this->load->library('phpword');
        $this->load->library('phpExcel/PHPExcel.php');

    }

    /* function very_sesion()
     {
         if (!$this->session->userdata('nUsuCodigo')) {
             redirect(base_url() . 'inicio');
         }
     }*/

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
            if ($this->input->post('estado') != '-1')
                $where['estado'] = $this->input->post('estado');

            $where['fecha_ini'] = $this->input->post('fecha_ini');
            $where['fecha_fin'] = $this->input->post('fecha_fin');

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
        if ($this->input->post('id_consolidado') != "") {
            $data['consolidado_id'] = $this->input->post('id_consolidado');
            $guardar = $this->consolidado_model->updateConsolidado($data, $pedidos);
        } else {
            $guardar = $this->consolidado_model->set_consolidado($data, $pedidos);
        }

        if ($guardar != FALSE) {

            $json['success'] = 'Solicitud Procesada con exito';

        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        echo json_encode($json);
    }

    function liquidacion()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }
        $data = array();
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
            $data['consolidado'] =
                $this->consolidado_model->get_details_by($where);

        }
        //var_dump($data['consolidado']);
        $data['id_consolidado'] = $id;
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
                $result['notasdentrega'][]['ventas'] = $this->venta_model->obtener_venta_backup($id_pedido);
            }
        }

        $result['consolidado_id'] = $id;
        $result['nota_entrega'] = '1';
        $this->load->view('menu/ventas/visualizarVenta', $result);
    }

    function liquidarPedido()
    {
        $estatus = $this->input->post('estatus');
        $id_pedido = $this->input->post('id_pedido_liquidacion');


        $monto = $this->input->post('monto');
        $estatus_actual = $this->input->post('estatus_actual');
        if (!empty($monto)) {
            $monto = $this->input->post('monto');
        }
        $venta = $this->venta_model->get_by('venta_id', $id_pedido);
        $venta['estatus_actual'] = $estatus_actual;

        if ($estatus == PEDIDO_RECHAZADO) {
            $venta['venta_status'] = PEDIDO_RECHAZADO;
            $venta['nUsuCodigo'] = $this->session->userdata('nUsuCodigo');

            $this->venta_model->restaurar_venta($id_pedido, $venta);
            $result = $this->venta_model->devolver_stock($id_pedido, $venta, $venta['venta_status']);
            $this->consolidado_model->updateDetalle(array('pedido_id' => $id_pedido, 'liquidacion_monto_cobrado' => 0.00));
        }
        if ($estatus == PEDIDO_ENTREGADO) {

            $venta['venta_status'] = PEDIDO_ENTREGADO;

            $venta['importe'] = $venta['pagado'];
            $venta['devolver'] = 'false';
            $venta['diascondicionpagoinput'] = $venta['dias'];
            $this->venta_model->restaurar_venta($id_pedido, $venta);
            $this->consolidado_model->updateDetalle(array('pedido_id' => $id_pedido, 'liquidacion_monto_cobrado' => $monto));
            $result = $this->venta_model->update_status($id_pedido, $venta['venta_status'], $this->session->userdata('nUsuCodigo'));
            $result = $this->venta_model->actualizarCredito(array('dec_credito_montodeuda' => $venta['total']), array('id_venta' => $id_pedido));

        }

        if ($result != FALSE) {

            $json['success'] = 'Solicitud Procesada con exito';

        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        echo json_encode($json);
    }

    function cerrarLiquidacion()
    {
        $id = $this->input->post('id');
        if ($id == FALSE) {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        $estatus = 'CERRADO';
        $cerrar = $this->consolidado_model->cambiarEstatus($id, $estatus);
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


    function pdf($id)
    {
        $data['notasdeentrega'] = $this->consolidado_model->get_documentoVenta_by_id($id, false);
        $data['detalleProducto'] = $this->consolidado_model->get_detalle_backup($id);
        //   var_dump($data['notasdeentrega']);
        $where = array('consolidado_id' => $id);
        $data['consolidado'] = $this->consolidado_model->get_consolidado_by($where);


        foreach ($data['consolidado'] as $campoCarga) {
            if ($campoCarga['status'] == 'ABIERTO') {
                $status = array(
                    'consolidado_id' => $id,
                    'status' => 'IMPRESO'
                );
                $statusVenta = array(
                    'venta_id' => $id,
                    'venta_status' => 'ENVIADO'
                );
                $this->consolidado_model->updateStatus($status);
                $this->consolidado_model->updateStatusVenta($statusVenta);
            }
        }


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
        $campo = $data['consolidado'][0];
        $cell = $table->addRow()->addCell(5000, array('valign ' => 'center', 'align' => 'left'));
        $cell->addText(htmlentities(date('d/m/Y', strtotime($campo['fecha']))), 'rBasicos', 'pStyle');

        $cell = $table->addCell(5000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText(htmlentities(strtoupper($this->session->userdata('EMPRESA_NOMBRE'))), 'rStyle', 'pStyle');

        $cell = $table->addCell(5000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText();

        $cell = $table->addRow()->addCell(3000, array('valign ' => 'center', 'align' => 'center', 'gridSpan' => 3));
        $cell->addText('Consolidado de Guía de Carga', array('italic' => true, 'bold' => true, 'size' => 16), 'pStyle');

        $cell = $table->addRow()->addCell(3000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText('Liquidacion: ' . $campo['consolidado_id'], 'rBasicos', 'pStyle');
        $table->addCell(3000)->addText('Responsable: ' . $campo['userCarga'], 'rBasicos', 'pStyle');
        $table->addCell(3000)->addText('Vehículo: ' . $campo['camiones_placa'], 'rBasicos', 'pStyle');


        $cell = $table->addRow()->addCell(3000, array('valign ' => 'center', 'align' => 'center'));
        $cell->addText('Almacen: ' . $campo['local_nombre'], 'rBasicos', 'pStyle');
        $table->addCell(3000)->addText('Zona: ' . $data['detalleProducto'][0]['zona_nombre'], 'rBasicos', 'pStyle');
        $table->addCell(3000)->addText('Chofer: ' . $campo['chofernombre'], 'rBasicos', 'pStyle');

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
        foreach ($data['detalleProducto'] as $campoProducto) {

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

            $cantidadtotalgrupo = $cantidadtotalgrupo + $campoProducto['cantidadTotal'];

            $table1->addRow(200, array('exactHeight' => true));
            $table1->addCell($w1)->addText();
            $table1->addCell($w2)->addText(htmlspecialchars($campoProducto['producto_id']), 'rBasicos');
            $table1->addCell($w3)->addText(htmlspecialchars(strtoupper($campoProducto['producto_nombre'])), 'rBasicos');
            $table1->addCell($w4)->addText(htmlspecialchars(strtoupper($campoProducto['nombre_unidad'])), 'rBasicos');
            $table1->addCell($w5)->addText(htmlspecialchars(strtoupper($campoProducto['presentacion'])), 'rBasicos');
            $table1->addCell($w6)->addText(htmlspecialchars(strtoupper($campoProducto['cantidadTotal'])), 'rBasicos', 'totales');


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
            $count++;
            $cantidad_total = $cantidad_total + $campoProducto['cantidadTotal'];
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


        $file = 'Consolidadodecarga' . $id . '.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
        $xmlWriter = \PhpOffice\PhpWord\IOFactory::createWriter($phpword, 'Word2007');
        $xmlWriter->save("php://output");


    }

    function pedidoDevolucion($id)
    {
        $in = "('" . PEDIDO_DEVUELTO . "', '" . PEDIDO_RECHAZADO . "')";
        $data['detalleProducto'] = $this->consolidado_model->get_detalle_devueltos($id, $in);

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


            $cantidad = floatval($cantidadvieja) - floatval($cantidadnueva);
            if($campoProducto['venta_status']==PEDIDO_RECHAZADO  ){
                $cantidad=$cantidadvieja;
            }

            if ($cantidad > 0 || $campoProducto['venta_status'] == PEDIDO_RECHAZADO) {
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


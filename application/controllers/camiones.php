<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class camiones extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();
        $this->load->model('camiones/camiones_model');
        $this->load->model('usuario/usuario_model');
        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');

    }

   /* function very_sesion()
    {
        if (!$this->session->userdata('nUsuCodigo')) {
            redirect(base_url() . 'inicio');
        }
    }*/

    /** carga cuando listas los proveedores*/
    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['camiones'] = $this->camiones_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/camiones/camiones', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);

        }

    }
    function form($id = FALSE)
    {
        $data = array();
        $data['trabajadores'] = $this->usuario_model->get_all_transportistas();
        if ($id != FALSE) {
            $data['camiones'] = $this->camiones_model->get_by('camiones_id', $id);
        }
        $this->load->view('menu/camiones/form', $data);
    }


    function guardar()
    {
        $id = $this->input->post('id');
        $transporte = array(
            'camiones_placa' => $this->input->post('camiones_placa'),
            'metros_cubicos' => $this->input->post('metros_cubicos'),
            'id_trabajadores' => $this->input->post('id_trabajadores')
        );

        if (empty($id)) {
            $resultado = $this->camiones_model->insertar($transporte);
        } else {
            $transporte['camiones_id'] = $id;
            $resultado = $this->camiones_model->update($transporte);
        }

        if ($resultado != FALSE) {
            if ($resultado === CAMION_EXISTE) {

                $json['error'] = CAMION_EXISTE;
            } else {
                $json['success'] = 'Solicitud Procesada con exito';
            }
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        echo json_encode($json);
    }



    function eliminar()
    {
        $id = $this->input->post('id');

        $camiones = array(
            'camiones_id' => $id,
            'deleted' => 1
        );


        $data['resultado'] = $this->camiones_model->update($camiones);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el Transporte';
        }

        echo json_encode($json);
    }

    function pdf()
    {

        $camiones = $this->camiones_model->get_all();

        //var_dump($miembro);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('CAMIONES');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.�?�.G.�?�.D.�?�.G.�?�.A.�?�.D.�?�.U.�?�.<br>Gran Logia de la República de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


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

//relación utilizada para ajustar la conversión de los píxeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir carácteres ASCII estándar, puede utilizar las fuentes básicas como
// Helvetica para reducir el tamaño del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// Añadir una página
// Este método tiene varias opciones, consulta la documentación para más información.
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

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>LISTA DE TRANSPORTE</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        $html .= "<table><tr>";
        $html .= "<th>ID</th><th>Placa del Camion</th>";
        $html .= "<th>Metros Cubicos</th>";
        $html .= "<th>Trabajador</th>";
        $html .= "</tr>";
        foreach ($camiones as $campoCamion) {
            $html .= "<tr><td>" . $campoCamion['camiones_id'] . "</td>";
            $html .= "<td>" . $campoCamion['camiones_placa'] . "</td>";
            $html .= "<td>" . $campoCamion['metros_cubicos'] . "</td>";
            $html .= "<td>" . $campoCamion['nombre'] . "</td>";
            $html .= "</tr>";

        }
        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este método tiene varias opciones, consulte la documentación para más información.
        $nombre_archivo = utf8_decode("ListaFCamiones.pdf");
        $pdf->Output($nombre_archivo, 'D');


    }

    function excel()
    {


        $camiones = $this->camiones_model->get_all();

        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Reporte de Clientes")
            ->setSubject("Reporte de Clientes")
            ->setDescription("Reporte de Clientes")
            ->setKeywords("Reporte de Clientes")
            ->setCategory("Reporte de Clientes");


        $columna_pdf[0] = "ID";
        $columna_pdf[1] = "Placa del Camion";
        $columna_pdf[2] = "Metros Cubicos";
        $columna_pdf[3] = "Trabajador";

        $col = 0;
        for ($i = 0; $i < count($columna_pdf); $i++) {
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna_pdf[$i]);
        }

        $row = 2;

        foreach ($camiones as $campoCamion) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campoCamion['camiones_id']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campoCamion['camiones_placa']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campoCamion['metros_cubicos']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $campoCamion['nombre']);
            $col++;

            $row++;
        }

        // Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Reporte Clientes');


        // configuramos el documento para que la hoja


        // de trabajo número 0 sera la primera en mostrarse
        // al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


        // redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteCamiones.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }


}
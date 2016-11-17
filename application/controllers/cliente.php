<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('cliente/cliente_model');
        $this->load->model('cliente_datos/cliente_datos_model');
        $this->load->model('clientesgrupos/clientes_grupos_model');
        $this->load->model('pais/pais_model');
        $this->load->model('estado/estado_model');
        $this->load->model('ciudad/ciudad_model');
        $this->load->model('zona/zona_model');
        $this->load->model('usuario/usuario_model');
        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');

    }

   /* function very_sesion()
    {
        if (!$this->session->userdata('nUsuCodigo')) {
            redirect(base_url() . 'inicio');
        }
    }
*/
    /** carga cuando listas los clientes*/
    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['clientes'] = $this->cliente_model->get_all();
        $data['vendedores'] = $this->usuario_model->get_all_u();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/cliente/cliente', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        $data['grupos'] = $this->clientes_grupos_model->get_all();
        $data['paises'] = $this->pais_model->get_all();
        $data['estados'] = $this->estado_model->get_all();
        $data['ciudades'] = $this->ciudad_model->get_all();
        if ($id != FALSE) {
            $data['cliente'] = $this->cliente_model->get_by('id_cliente', $id);
            $data['cliente_datos'] = $this->cliente_datos_model->get_all_by($id);
            $data['cliente_v'] = $this->usuario_model->get_all_u2($id);
        }
        $data['vendedores'] = $this->usuario_model->get_all_u();
        $data['zonas'] = $this->zona_model->get_all();
        $this->load->view('menu/cliente/form', $data);
    }

    function guardar()
    {




        $id = $_POST['formData'][0]['value'];

        $vendedor_id = $_POST['formData'][9]['value'];
        $zona = $_POST['formData'][8]['value'];

        if($_POST['formData'][8]['value']=='on'){
            $linea_libre = true;
        }else{
            $linea_libre = false;
        }

        $cliente = array(
            'tipo_cliente' => $_POST['formData'][1]['value'],
            'ciudad_id' => $_POST['formData'][7]['value'],
            'grupo_id' => $_POST['formData'][14]['value'],
            'representante' => $_POST['formData'][4]['value'],
            'razon_social' => $_POST['formData'][3]['value'],
            'linea_credito_valor' => $_POST['formData'][10]['value'],

            'agente_retencion' => $_POST['formData'][12]['value'],
            'linea_credito_valor' => $_POST['formData'][13]['value'],
            'linea_libre' => $linea_libre,
            'linea_libre_valor' => $_POST['formData'][10]['value'],


            'identificacion' => $_POST['formData'][2]['value'],
            'latitud' => $_POST['formData'][18]['value'],
            'longitud' => $_POST['formData'][19]['value'],
            'id_zona' => !empty($zona) ? $zona : null,
            'vendedor_a' => !empty($vendedor_id) ? $vendedor_id : null,
        );
        if (empty($id)) {
            $resultado = $this->cliente_model->insertar($cliente, $_POST['items']);
        } else {
            $cliente['id_cliente'] = $id;
            $resultado = $this->cliente_model->update($cliente, $_POST['items']);
        }

        if ($resultado == TRUE) {
            $json['success']='Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }


        if($resultado===CEDULA_EXISTE){
            //  $this->session->set_flashdata('error', NOMBRE_EXISTE);
            $json['error']= CEDULA_EXISTE;
        }
       echo json_encode($json);

    }


    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $cliente = array(
            'id_cliente' => $id,
            'razon_social' => $nombre . time(),
            'cliente_status' => 0

        );

        $data['resultado'] = $this->cliente_model->update2($cliente);

        if ($data['resultado'] != FALSE) {

            $json['success']= 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el Cliente';
        }

        echo json_encode($json);
    }

    function pdf()
    {

        $clientes = $this->cliente_model->get_all();

        //var_dump($miembro);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('CLIENTES');
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

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>LISTA DE CLIENTES</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        $html .= "<table><tr>";
        $html .= "<th>ID</th><th>Razon Social</th>";
        $html .= "<th>Rep.</th>";
        $html .= "<th>Identificacion</th>";
        $html .= "<th>Direccion</th>";
        $html .= "<th>Distrito</th>";
        $html .= " <th>Zona</th>";
        $html .= "<th>Telefono</th>";
        $html .= "<th>Vendedor</th>";
        $html .= "</tr>";
        foreach ($clientes as $familia) {
            $html .= "<tr><td>" . $familia['id_cliente'] . "</td>";
            $html .= "<td>" . $familia['razon_social'] . "</td>";
            $html .= "<td>" . $familia['representante'] . "</td>";
            $html .= "<td>" . $familia['identificacion'] . "</td>";
            $html .= "<td>" . $familia['direccion'] . "</td>";

$html .= "<td>" .$familia['ciudad_nombre']. "</td>";
$html .= "<td>" .$familia['zona_nombre']. "</td>";
            $html .= "<td>" . $familia['telefono1'] . "</td>";
            $html .= "<td>" . $familia['nombre'] . "</td>";
            $html .= "</tr>";

        }
        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este método tiene varias opciones, consulte la documentación para más información.
        $nombre_archivo = utf8_decode("Clientes.pdf");
        $pdf->Output($nombre_archivo, 'D');


    }

    function excel()
    {



        $clientes = $this->cliente_model->get_all();


        $columna_pdf[0] = "ID";
        $columna_pdf[1] = "Razón social";
        $columna_pdf[2] = "Represenante";
        $columna_pdf[3] = "Identificación";
        $columna_pdf[4] = "Dirección";
        $columna_pdf[5] = "Distrito";
        $columna_pdf[6] = "Zona";
        $columna_pdf[7] = "Teléfono ";
        $columna_pdf[8] = "Vendedor ";




        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Stock")
            ->setSubject("Stock")
            ->setDescription("Stock")
            ->setKeywords("Stock")
            ->setCategory("Stock");

        $this->phpexcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize('true');
        $this->phpexcel->getActiveSheet()->getColumnDimension('C')->setAutoSize('true');
        $this->phpexcel->getActiveSheet()->getColumnDimension('E')->setAutoSize('true');
        $this->phpexcel->getActiveSheet()->getColumnDimension('F')->setAutoSize('true');
        $this->phpexcel->getActiveSheet()->getColumnDimension('G')->setAutoSize('true');
        $this->phpexcel->getActiveSheet()->getColumnDimension('I')->setAutoSize('true');
        $this->phpexcel->getActiveSheet()->getColumnDimension('J')->setAutoSize('true');
        $this->phpexcel->getActiveSheet()->getColumnDimension('K')->setAutoSize('true');
        $c = 0;
        foreach ($columna_pdf as $col) {

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($c, 1, $col);
                $c++;

        }
        $col = 0;
        $row = 2;
        foreach ($clientes as $cliente) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['id_cliente']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['razon_social']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['representante']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['identificacion']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['direccion']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['ciudad_nombre']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['zona_nombre']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['telefono1']);
            $col++;
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col, $row, $cliente['nombre']);
            $col++;

            $row++;
        }

// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Lista Stock');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Clientes.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }


}
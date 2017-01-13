<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class descuentos extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();
        $this->load->model('descuentos/descuentos_model');
        $this->load->model('producto/producto_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('clientesgrupos/clientes_grupos_model');

        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');

        //$this->very_sesion();
    }

    /* function very_sesion()
    {
        if (!$this->session->userdata('nUsuCodigo')) {
            redirect(base_url() . 'inicio');
        }
    }*/

    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data["descuentos"] = $this->descuentos_model->get_all();

        $data["grupos"] = $this->clientes_grupos_model->get_all();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/descuentos/descuentos', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

	function lst_descuentos() {

        if ($this->input->is_ajax_request()) {

            $id = $this->input->post('grupos');

            $data["grupo_id"] = $id;

            if($id == 0){
                $data["descuentos"] = $this->descuentos_model->get_all();
            }
            else{
                $data["descuentos"] = $this->descuentos_model->get_by_groupclie($id);
            }

            $this->load->view('menu/descuentos/tbl_descuentos', $data);
        }
        else {
            redirect(base_url() . 'descuentos/', 'refresh');
        }
    }

    function verReglaDescuento($id){

        $data['escalas'] = $this->descuentos_model->get_escalas_descuento($id);
        $data['escalas_h'] = $this->descuentos_model->get_escalas_descuento_head($id);
        $data['id_desc'] = $id;
        $this->load->view('menu/descuentos/reglaDescuento',$data);

    }
    function form($id = FALSE, $grupoid)
    {

        $datax = array();
        $group = "producto.producto_id";

        $grupo_id = $grupoid;

        $datax['grupo_clie_id'] = $grupo_id;

        $grupo_name = $this->clientes_grupos_model->get_by('id_grupos_cliente', $grupo_id);
        $datax['grupo_clie'] = $grupo_name['nombre_grupos_cliente'];


        if ($id != FALSE) {

            $datax['descuentos'] = $this->descuentos_model->get_by('descuento_id', $id);

			$datax['escalas'] = $this->descuentos_model->get_escalas_by_descuento($id);

			$where = " where  descuentos.descuento_id='" . $id . "'";

			$datax['productosnoagrupados'] = $this->descuentos_model->edit_descuentos($where, false);


			$datax['sizenoagrupados'] = sizeof($datax['productosnoagrupados']);

			$datax['sizeescalas'] = sizeof($datax['escalas']);

            $datax['prod_precios'] = $this->descuentos_model->get_prod_precioventa();


        }

        $where_all = " where descuentos.status=1 AND descuentos.id_grupos_cliente ='" . $grupo_id . "'";

        $datax['productosenreglasdedescuento'] = $this->descuentos_model->edit_descuentos($where_all, $group);

        $datax["lstProducto"] = $this->producto_model->select_all_producto();

        $this->load->view('menu/descuentos/form', $datax);

    }

    function listado($id = FALSE)
    {

        $da['escalas'] = $this->descuentos_model->get_escalas_by_descuento($id);
        $where = " where  descuentos.descuento_id='" . $id . "'";
        $da['prod'] = $this->descuentos_model->edit_descuentos($where, false);

        header('Content-Type: application/json');
        echo json_encode($da);
    }

    function lista_descuento(){

        $id = $this->input->post('desID');
        $condicion = false;
        if ($this->input->post('id_des') != "") {
            $condicion['producto_id'] = $this->input->post('id_des');
        }
        if ($this->input->post('nombre_des') != "") {
            $condicion['producto_nombre'] = $this->input->post('nombre_des');
        }
        $data['escalas_h'] = $this->descuentos_model->get_escalas_descuento_head_list($id,$condicion);
        $data['escalas'] = $this->descuentos_model->get_escalas_descuento_list($id,$condicion);

        $this->load->view('menu/descuentos/lista_descuento', $data);
    }
    function guardar()
    {

        $id = $this->input->post('id');

        $descuento = array(
            'nombre' => $this->input->post('nombre'),
        );

        if (empty($id)) {
            $resultado = $this->descuentos_model->insertar($descuento);

        } else {
            $descuento['descuento_id'] = $id;
            $resultado = $this->descuentos_model->update($descuento);
        }

        if ($resultado == TRUE) {
            $json['success']= 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);

    }


    function eliminar()
    {
        $id = $this->input->post('id');

        $descuento = array(
            'descuento_id' => $id,
            'status' => 0

        );

        $data['resultado'] = $this->descuentos_model->delete($descuento);

        if ($data['resultado'] != FALSE) {

            $json['success'] ='Se ha eliminado exitosamente';


        } else {

            $json['error']= 'Ha ocurrido un error al eliminar el descuento';
        }

        echo json_encode($json);
    }

    function get_by_descuento()
    {
        if ($this->input->is_ajax_request()) {
            $descuento_id = $this->input->post('descuento_id');

            $descuento = $this->descuentos_model->get_by('descuento_id', $descuento_id);

            echo json_encode($descuento);
        } else {
            redirect(base_url . 'principal');
        }
    }

    function get_unidades_has_producto(){

        $id_producto=$this->input->post('id_producto');
        $data['unidades']=$this->unidades_model->get_by_producto($id_producto);
        header('Content-Type: application/json');
        echo json_encode( $data );
    }

    function registrar_descuento()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('nombre', 'nombre', 'required');

            if ($this->form_validation->run() == false) {
                $json['error'] = 'Algunos campos son requeridos';
            } else {

                $id_grupo = $this->input->post('grupos');

                $comp_cab_pie = array(
                    'nombre' => $this->input->post('nombre', true),
                );

                if ($this->input->post('id_de_descuento') == "") {

                    $rs = $this->descuentos_model->insertar_descuento($comp_cab_pie,
                        json_decode($this->input->post('lst_escalas', true)),
                        json_decode($this->input->post('lst_producto', true)),
                        $this->input->post('precio'),$id_grupo);

                } else {

                    $comp_cab_pie["descuento_id"] = $this->input->post('id_de_descuento');
                    $rs = $this->descuentos_model->actualizar_descuento($comp_cab_pie,
                        json_decode($this->input->post('lst_escalas', true)),
                        json_decode($this->input->post('lst_producto', true)),
                        $this->input->post('precio'));
                }
                    if ($rs != false) {
                        $json['success'] = 'Solicitud Procesada con exito';
                        $json['id'] = $rs;

                    } else {
                        $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
                    }
            }
        } else {

            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';

        }
        echo json_encode($json);
    }

    function pdfExport($id) {

        $desc_row = $this->descuentos_model->get_by('descuento_id', $id);
        $grupo_id = $desc_row['id_grupos_cliente'];

        $grupo_clie_row = $this->clientes_grupos_model->get_by('id_grupos_cliente', $grupo_id);
        $grupo_name = $grupo_clie_row['nombre_grupos_cliente'];

        $escalas = $this->descuentos_model->get_escalas_descuento($id);
        $escalas_h = $this->descuentos_model->get_escalas_descuento_head($id);

        //PDF
        //////////////////
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('P');
        $pdf->SetTitle('Reporte Descuentos');
        $pdf->SetPrintHeader(false);
        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        //$pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
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


        $html .= "<br><b><u>DESCUENTOS</u></b><br>";

        $html .= "<br>Grupo: " . $grupo_name . "</b><br><br>";

        $html .= "<table><tr><thead>
                        <th>C&oacute;digo</th>
                        <th>Producto</th>";

        foreach($escalas_h as $escala){
            $html .= "<th>" . $escala['cantidad_minima'] . "--" . $escala['cantidad_maxima'] . "</th>";
        }

        $html .= "</tr></thead>";

        $array_destino = array();
        $valor = array();

        foreach($escalas as $escala) {

            $valor['nombre'] = $escala['producto_nombre'];
            $valor['id'] = $escala['producto_id'];
            if (!in_array($valor,$array_destino)){
                $array_destino[] = $valor;
            }
        }

        $html .= "<tbody>";
        foreach ($array_destino as $valor) {
            $html .= "<tr><td>" . sumCod($valor['id']) . "</td>";

            $html .= "<td>" . $valor['nombre'] . "</td>";

                foreach($escalas as $escala) {
                    if ($valor['nombre'] == $escala['producto_nombre']) {
                        $html .= "<td>" . $escala['precio'] . "</td>";

                    }
                }

            $html .= "</tr>";
        }

        $html .= "</tbody></table>";

        $pdf->writeHTML($html, true, 0, true, 0);
        $pdf->lastPage();
        $pdf->output('Descuentos.pdf', 'D');
    }


    function excelExport($id) {

        $escalas = $this->descuentos_model->get_escalas_descuento($id);

        $escalas_h = $this->descuentos_model->get_escalas_descuento_head($id);

        $this->phpexcel->getProperties()
            ->setTitle("ReporteDescuentos")
            ->setSubject("ReporteDescuentos")
            ->setDescription("ReporteDescuentos")
            ->setKeywords("ReporteDescuentos")
            ->setCategory("ReporteDescuentos");

        $columna[0] = "Codigo";
        $columna[1] = "Producto";

        $c = 2;

        foreach($escalas_h as $escala){
            $columna[$c] = $escala['cantidad_minima'] . "--" . $escala['cantidad_maxima'];
            $c++;
        }

        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }

        $array_destino = array();
        $valor = array();

        foreach($escalas as $escala) {

            $valor['nombre'] = $escala['producto_nombre'];
            $valor['id'] = $escala['producto_id'];
            if (!in_array($valor,$array_destino)){
                $array_destino[] = $valor;
            }
        }

        $row = 2;

        foreach ($array_destino as $valor) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, sumCod($valor['id']));

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $valor['nombre']);

            foreach($escalas as $escala) {
                if ($valor['nombre'] == $escala['producto_nombre']) {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $escala['precio']);
                }
            }

            $row++;
        }

        $this->phpexcel->getActiveSheet()->setTitle('ReporteDescuentos');

        $this->phpexcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteDescuentos.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

}

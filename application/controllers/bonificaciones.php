<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class bonificaciones extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('bonificaciones/bonificaciones_model');
        $this->load->model('producto/producto_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('familia/familias_model');
        $this->load->model('grupos/grupos_model');
        $this->load->model('marca/marcas_model');
        $this->load->model('linea/lineas_model');
        $this->load->model('subfamilia/subfamilias_model');
        $this->load->model('subgrupos/subgrupos_model');
        $this->load->model('clientesgrupos/clientes_grupos_model');

        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');

    }

    /*function very_sesion()
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

        $data['bonificacioness'] = array();
        $bonificaciones = $this->bonificaciones_model->get_all();

        $data["grupos"] = $this->clientes_grupos_model->get_all();

        foreach ($bonificaciones as $b) {

            $b['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $b['id_bonificacion']);
          //  var_dump($b);
            $data['bonificacioness'][]=$b;
        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/bonificaciones/bonificaciones', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function lst_bonificaciones()
        {

            if ($this->input->is_ajax_request()) {

                $id = $this->input->post('grupos');

                $data['bonificacioness'] = array();

                $data['id_grupoclie'] = $id;

                if ($id!=0)
                {
                    $bonificaciones = $this->bonificaciones_model->get_by_groupclie($id);

                    foreach ($bonificaciones as $b)
                        {
                            $b['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $b['id_bonificacion']);

                            $data['bonificacioness'][]=$b;
                        }

                        $this->load->view('menu/bonificaciones/tbl_bonificaciones', $data);
                }
                else{

                    $bonificaciones = $this->bonificaciones_model->get_all();

                    foreach ($bonificaciones as $b)
                        {
                            $b['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $b['id_bonificacion']);

                            $data['bonificacioness'][]=$b;
                        }

                        $this->load->view('menu/bonificaciones/tbl_bonificaciones', $data);
                }
            }

            else {
                redirect(base_url() . 'bonificaciones/', 'refresh');
                }
    }



    function verproductos($id=FALSE)
    {

        $data = array();
        if ($id != FALSE && $id != 'false') {


            $data['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $id);

        }

        $this->load->view('menu/bonificaciones/verproductos', $data);

    }




    function form($id = FALSE, $p1 = FALSE, $p2 = FALSE, $grupoid)
    {

        $data = array();

        $grupo_id = $grupoid;

        $data['grupo_clie_id'] = $grupo_id;

        $grupo_name = $this->clientes_grupos_model->get_by('id_grupos_cliente', $grupo_id);
        $data['grupo_clie'] = isset($grupo_name['nombre_grupos_cliente']) ? $grupo_name['nombre_grupos_cliente'] : null;

        if ($id != FALSE && $id != 'false') {
            $data['bonificaciones'] = $this->bonificaciones_model->get_by('id_bonificacion', $id);
            $data['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $id);
        }

        if ($p1 != FALSE && $p1 != 'false') {
            $data['unidades'] = $this->unidades_model->get_by_producto($p1);

        }

        if ($p2 != FALSE && $p2 != 'false') {
            $data['unidades_bono'] = $this->unidades_model->get_by_producto($p2);

        }

        $productos=$this->producto_model->select_all_producto();
        $data['productos'] = $productos;
        $data['familias'] = $this->familias_model->get_familias();
        $data['grupos'] = $this->grupos_model->get_grupos();
        $data['marcas'] = $this->marcas_model->get_marcas();
        $data['lineas'] = $this->lineas_model->get_lineas();
        $data['bonoproducto'] = $productos;
        $data['bonounidad'] = $this->unidades_model->get_unidades();
        $data['subgrupos'] = $this->subgrupos_model->get_subgrupos();
        $data['subfamilias'] = $this->subfamilias_model->get_subfamilias();

        $this->load->view('menu/bonificaciones/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $producto_condicion = $this->input->post('producto_condicion');
        $unidad_condicion = $this->input->post('unidad_condicion');
        $familia_condicion = $this->input->post('familia_condicion');
        $grupo_condicion = $this->input->post('grupo_condicion');
        $marca_condicion = $this->input->post('marca_condicion');
        $linea_condicion = $this->input->post('linea_condicion');

        $bonificaciones = array(
            'fecha' => date('Y-m-d', strtotime($this->input->post('fecha_bonificacion'))),

            'id_unidad' => empty($unidad_condicion) ? NULL : $this->input->post('unidad_condicion'),
            'id_familia' => empty($familia_condicion) ? NULL : $this->input->post('familia_condicion'),
            'id_grupo' => empty($grupo_condicion) ? NULL : $this->input->post('grupo_condicion'),
            'id_marca' => empty($marca_condicion) ? NULL : $this->input->post('marca_condicion'),
            'id_linea' => empty($linea_condicion) ? NULL : $this->input->post('linea_condicion'),
            'cantidad_condicion' => $this->input->post('cantidad_condicion'),
            'bono_producto' => $this->input->post('bono_producto'),
            'bono_unidad' => $this->input->post('bono_unidad'),
            'bono_cantidad' => $this->input->post('bono_cantidad'),
            'bonificacion_status' => 1
        );

        if ($this->input->post('subfamilia') == '') {
            $bonificaciones['subfamilia_id'] = null;

        } else {
            $bonificaciones['subfamilia_id'] = $this->input->post('subfamilia');
        }
        if ($this->input->post('subgrupos') == '') {
            $bonificaciones['subgrupo_id'] = null;

        } else {
            $bonificaciones['subgrupo_id'] = $this->input->post('subgrupos');
        }

        $bonificaciones['id_grupos_cliente'] = $this->input->post('grupos');

        $productos = $this->input->post('producto_condicion', true);
        
                $where = array(
            'bono_producto' => $this->input->post('bono_producto'),
            // 'bono_cantidad'=> $this->input->post('bono_cantidad'),

        );
        $bonificacioerronea = false;
        $coincidecantidad= false;
        $bono_coicidencias = $this->bonificaciones_model->get_where($where);
        if (sizeof($bono_coicidencias) > 0) {

            foreach ($bono_coicidencias as $coincidencia) {
                $coincidecantidad = false;
                if ($coincidencia['id_bonificacion'] != $id) {
                    // var_dump($coincidencia);
                    $bonificaciones_has_prod = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $coincidencia['id_bonificacion']);
                    //var_dump($bonificaciones_has_prod);
                    //var_dump($productos);
                    //  var_dump($bonificaciones_has_prod);
                    if (sizeof($productos) != sizeof($bonificaciones_has_prod)) {
                        //echo "aqui 1";
                        $bonificacioerronea = true;
                    } else {
                        $existe = false;
                        foreach ($bonificaciones_has_prod as $bono) {
                            foreach ($productos as $prod) {
                                if ($bono['id_producto'] == $prod) {
                                    $existe = true;
                                }
                            }
                        }
                        if ($existe == false) {
                            $bonificacioerronea = true;
                        }
                    }

                    if ($bonificacioerronea == false) {
                        //var_dump($coincidencia);
                        if ($coincidencia['bono_cantidad'] == $this->input->post('bono_cantidad')) {
                            //echo "entr";
                            $coincidecantidad = true;
                        }
                    }
                }
            }
        }

        if ($bonificacioerronea == false) {
            if ($coincidecantidad) {
                $bonificacioerronea = true;
            }
        }
        // echo '$bonificacioerronea';
        // echo $bonificacioerronea;


        //var_dump($bono_coicidencias);

        if ($bonificacioerronea) {
            $json['error'] = 'La bonificacion no  puede ser creada, por favor utilice una bonificacion existente que bonifica el mismo producto';
            if ($coincidecantidad) {
                $json['error'] = 'La bonificacion no  puede ser creada, ya existe otra bonificacion con la misma configuracion que bonifica el mismo producto con la misma cantidad';

            }
        } else {

	
			if (empty($id)) {
				$resultado = $this->bonificaciones_model->insertar($bonificaciones, $productos);
	
			} else {
				$bonificaciones['id_bonificacion'] = $id;
				$resultado = $this->bonificaciones_model->update($bonificaciones, $productos);
			}
	
			if ($resultado == TRUE) {
				$json['success'] = 'Solicitud Procesada con exito';
			} else {
				$json['error'] = 'Ha ocurrido un error al procesar la solicitud';
			}
        }

        echo json_encode($json);

    }

    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $bonificaciones = array(
            'id_bonificacion' => $id,
            'bonificacion_status' => 0

        );

        $data['resultado'] = $this->bonificaciones_model->update($bonificaciones, null);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar la Bonificación';
        }

        echo json_encode($json);
    }

    function get_unidades_has_producto()
    {

        $id_producto = $this->input->post('id_producto');
        $data['unidades'] = $this->unidades_model->get_by_producto($id_producto);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_unidades_en_comun()
    {

        $id_producto = $this->input->post('id_producto');
        $data['unidades'] = array();
        $productos = array();
        foreach ($id_producto as $p) {

            $producto = array();
            $producto['id_producto'] = $p;
            $producto['unidades'] = $this->unidades_model->get_by_producto($p);
            $productos[] = $producto;
        }
        foreach ($productos as $p) {
            $unidades = $p['unidades'];

            foreach ($unidades as $u) {

                $eliminar = false;

                foreach ($productos as $p2) {
                    $unidades2 = $p2['unidades'];
                    foreach ($unidades2 as $u2) {
                        if ($u['id_unidad'] != $u2['id_unidad']) {
                            $eliminar = true;
                        }
                    }
                }
                if ($eliminar == false) {
                    $data['unidades'][] = $u;
                }
            }
        }


        $data['unidades'] = @array_unique($data['unidades']);
        //  var_dump($data['unidades']);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function pdfExport($id) {

        $grupo_clie_row = $this->clientes_grupos_model->get_by('id_grupos_cliente', $id);
        $grupo_name = $grupo_clie_row['nombre_grupos_cliente'];

        $bonificacioness = array();
        $bonificaciones = $this->bonificaciones_model->get_by_groupclie($id);

        foreach ($bonificaciones as $b) {

            $b['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $b['id_bonificacion']);
            //  var_dump($b);
            $bonificacioness[]=$b;
        }

        //PDF
        //////////////////
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('P');
        $pdf->SetTitle('Reporte Bonificaciones');
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


        $html .= "<br><b><u>BONIFICACIONES</u></b><br>";

        $html .= "<br>Grupo: " . $grupo_name . "</b><br><br>";

        $html .= "<table><tr><thead>
                        <th>ID</th>
                        <th>Vencimiento</th>
                        <th>Estado</th>
                        <th>Productos</th>
                        <th>Marca Condici&oacute;n</th>
                        <th>Grupo Condici&oacute;n</th>
                        <th>Sub Grupo Condici&oacute;n</th>
                        <th>Familia Condici&oacute;n</th>
                        <th>Sub Familia Condici&oacute;n</th>
                        <th>L&iacute;nea Condici&oacute;n</th>
                        <th>Unidad Condici&oacute;n</th>
                        <th>Cantidad Condici&oacute;n</th>
                        <th>Bono Producto</th>
                        <th>Bono Unidad</th>
                        <th>Bono Cantidad</th>";

        $html .= "</tr></thead>";

        $html .= "<tbody>";


        if (count($bonificacioness) > 0) {
            foreach ($bonificacioness as $bonificaciones) {
                $html .= "<tr><td>" . $bonificaciones['id_bonificacion'] . "</td>";

                $html .= "<td>" . $bonificaciones['fecha'] . "</td>";

                $days = (strtotime(date('d-m-Y')) - strtotime($bonificaciones['fecha'])) / (60 * 60 * 24);
                if ($days < 0)
                    $days = 0;

                if (floor($days) <= 0) {
                    $estado = "Activa";
                } else {
                    $estado = "Vencida";
                }

                $html .= "<td>" . $estado . "</td>";

                $html .= "<td>";

                foreach($bonificaciones['bonificaciones_has_producto'] as $produc){
                    $prod = sumCod($produc['id_producto']). " " .$produc['producto_nombre'];
                    $html .= $prod;
                    $html .= "<br>";
                }

                $html .= "</td>";

                $html .= "<td>" . $bonificaciones['nombre_marca'] . "</td>";
                $html .= "<td>" . $bonificaciones['nombre_grupo'] . "</td>";
                $html .= "<td>" . $bonificaciones['nombre_subgrupo'] . "</td>";
                $html .= "<td>" . $bonificaciones['nombre_familia'] . "</td>";
                $html .= "<td>" . $bonificaciones['nombre_subfamilia'] . "</td>";
                $html .= "<td>" . $bonificaciones['nombre_linea'] . "</td>";
                $html .= "<td>" . $bonificaciones['nombre_unidad'] . "</td>";
                $html .= "<td>" . $bonificaciones['cantidad_condicion'] . "</td>";
                $html .= "<td>" . $bonificaciones['producto_bonificacion'] . "</td>";
                $html .= "<td>" . $bonificaciones['unidad_bonificacion'] . "</td>";
                $html .= "<td>" . $bonificaciones['bono_cantidad'] . "</td>";

                $html .= "</tr>";

            }
        }

        $html .= "</tbody></table>";

        $pdf->writeHTML($html, true, 0, true, 0);
        $pdf->lastPage();
        $pdf->output('Bonificaciones.pdf', 'D');
    }


    function excelExport($id) {

        $bonificacioness = array();
        $bonificaciones = $this->bonificaciones_model->get_by_groupclie($id);

        foreach ($bonificaciones as $b) {

            $b['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $b['id_bonificacion']);
            //  var_dump($b);
            $bonificacioness[]=$b;
        }

        $this->phpexcel->getProperties()
            ->setTitle("ReporteBonificaciones")
            ->setSubject("ReporteBonificaciones")
            ->setDescription("ReporteBonificaciones")
            ->setKeywords("ReporteBonificaciones")
            ->setCategory("ReporteBonificaciones");

            $estiloInformacion = array(
            'font' => array(
                'name'  => 'Arial',
                'size' =>10,
                'color' => array(
                    'rgb' => '000000'
                )
            ),
            'borders' => array(
                    'allborders' => array(
                        'outline' => PHPExcel_Style_Border::BORDER_THIN ,
                        'color' => array(
                          'rgb' => '3a2a47'
                        )
                    )
            )
        );

        $estiloTituloReporte = array(
            'font' => array(
                'name'      => 'Arial',
                'bold'      => true,
                'italic'    => false,
                'strike'    => false,
                'size'      =>  12,
                'color'     => array(
                    'rgb' => 'FFFFFF'
                )
            ),
            'fill' => array(
              'type'  => PHPExcel_Style_Fill::FILL_SOLID,
              'color' => array(
                    'argb' => 'FF459136')
          ),
            'borders' => array(
                'allborders' => array(
                    'style' => PHPExcel_Style_Border::BORDER_THIN
                )
            ),
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER,
                'rotation' => 0,
                'wrap' => TRUE
            )
        );

        $this->phpexcel->getActiveSheet()->getStyle('A2:J2')->applyFromArray($estiloTituloReporte);

         // Llenado de Titulo
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 1,'Bonificaciones');
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 1, 'Fecha '.date('d-m-Y h:m:s'));

                // Columnas de A a Z 26 elementos Maximo
        $columnas = range("A","Z");

        // Configuraci´on de Elementos Titulo
        for($i = 'A'; $i <= 'J'; $i++){
            $this->phpexcel->getActiveSheet()->getColumnDimension($i)->setAutoSize('true');
        }

        // $columna[0] = "ID";
        // $columna[1] = "Vencimiento";
        // $columna[2] = "Estado";
        // $columna[3] = "Productos";
        // $columna[4] = "Marca condicion";
        // $columna[5] = "Grupo condicion";
        // $columna[6] = "Sub grupo condicion";
        // $columna[7] = "Familia condicion";
        // $columna[8] = "Sub familia condicion";
        // $columna[9] = "Linea condicion";
        // $columna[10] = "Unidad condicion";
        // $columna[11] = "Cantidad condicion";
        // $columna[12] = "Bono producto";
        // $columna[13] = "Bono unidad";
        // $columna[14] = "Bono cantidad";

        $columna[] = "ID";
        $columna[] = "Vencimiento";
        $columna[] = "Estado";
        $columna[] = "Productos";
        $columna[] = "Marca condicion";
        $columna[] = "Grupo condicion";
        $columna[] = "Unidad condicion";
        $columna[] = "Cantidad condicion";
        $columna[] = "Bono producto";
        $columna[] = "Bono cantidad";


        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 2, $columna[$i]);

        }

        $row = 3;

        if (count($bonificacioness) > 0) {
            foreach ($bonificacioness as $b) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $b['id_bonificacion']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $b['fecha']);

                $days = (strtotime(date('d-m-Y')) - strtotime($b['fecha'])) / (60 * 60 * 24);
                if ($days < 0)
                    $days = 0;

                if (floor($days) <= 0) {
                    $estado = "Activa";
                } else {
                    $estado = "Vencida";
                }

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $estado);

                $prod = "";

                foreach($b['bonificaciones_has_producto'] as $produc){
                    $prod .= sumCod($produc['id_producto']). " " .$produc['producto_nombre'] . ", ";

                }

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $prod);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $b['nombre_marca']);

                // $this->phpexcel->setActiveSheetIndex(0)
                //     ->setCellValueByColumnAndRow($col++, $row, $b['nombre_grupo']);

                // $this->phpexcel->setActiveSheetIndex(0)
                //     ->setCellValueByColumnAndRow($col++, $row, $b['nombre_subgrupo']);

                // $this->phpexcel->setActiveSheetIndex(0)
                //     ->setCellValueByColumnAndRow($col++, $row, $b['nombre_familia']);

                // $this->phpexcel->setActiveSheetIndex(0)
                //     ->setCellValueByColumnAndRow($col++, $row, $b['nombre_subfamilia']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $b['nombre_linea']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $b['nombre_unidad']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $b['cantidad_condicion']);

                // $this->phpexcel->setActiveSheetIndex(0)
                //     ->setCellValueByColumnAndRow($col++, $row, $b['producto_bonificacion']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $b['unidad_bonificacion']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $b['bono_cantidad']);

                $row++;

            }
        }

        $this->phpexcel->getActiveSheet()->setTitle('ReporteBonificaciones');

        $this->phpexcel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteBonificaciones.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class inventario extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('inventario/inventario_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('producto/producto_model');
        $this->load->model('ajusteinventario/ajusteinventario_model');
        $this->load->model('ajustedetalle/ajustedetalle_model');
        $this->load->model('kardex/kardex_model');
        $this->load->model('local/local_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('columnas/columnas_model');
        $this->load->model('venta/venta_model');
        $this->load->model('detalle_ingreso/detalle_ingreso_model');
        $this->load->model('kardex/kardex_model');
        $this->load->model('precio/precios_model');
        $this->load->model('cliente/cliente_model');
        $this->load->model('unidades_has_precio/unidades_has_precio_model');
        $this->load->helper('form');

        $this->columnas = $this->columnas_model->get_by('tabla', 'producto');
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
    function ajuste()
    {
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }
        $data['ajustes'] = array();
        $data['locales'] = $this->local_model->get_all();
        //$data['ajustes'] = $this->ajusteinventario_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/ajuste', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function ajusteinventario_by_local()
    {
        if ($this->input->post('id_local') != "seleccione") {
            $local = $this->input->post('id_local');
            $data['ajustes'] = $this->ajusteinventario_model->get_ajuste_inventario($local);

            $this->load->view('menu/inventario/lista_ajustes', $data);
        }
    }

    function addajuste()
    {
        $data['locales'] = $this->local_model->get_all();
        $data['productos'] = $this->producto_model->select_all_producto();

        $this->load->view('menu/inventario/addajuste', $data);
    }

    function movimiento()
    {
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }
        $data['locales'] = $this->local_model->get_all();


        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/movimiento', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function kardex($id = false, $local = false, $mes = false, $year = false)
    {
        if($local == 'TODOS')
            $local = false;

        $data['producto'] = $this->db->select('producto.producto_nombre as nombre, unidades.nombre_unidad as um_nombre')
            ->from('producto')
            ->join('unidades_has_producto', 'unidades_has_producto.producto_id = producto.producto_id AND unidades_has_producto.orden = 1')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->where('producto.producto_id', $id)
            ->get()->row();

        $data['periodo'] = getMes($mes).' '.$year;

        $data['kardex'] = $this->kardex_model->get_kardex($id, $local, $mes, $year);
        $data['tipo_kardex'] = 'FISCAL';
        $data['producto_id'] = $id;

        $this->load->view('menu/inventario/formMovimiento', $data);
    }

    function kardex_pdf($id = false, $local = false, $mes = false, $year = false)
    {
        if($local == 'TODOS')
            $local = false;

        $data['producto'] = $this->db->select('producto.producto_nombre as nombre, unidades.nombre_unidad as um_nombre')
            ->from('producto')
            ->join('unidades_has_producto', 'unidades_has_producto.producto_id = producto.producto_id AND unidades_has_producto.orden = 1')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->where('producto.producto_id', $id)
            ->get()->row();

        $data['periodo'] = getMes($mes).' '.$year;
        $data['kardex'] = $this->kardex_model->get_kardex_interno($id, $local, $mes, $year);
        $html = $this->load->view('menu/inventario/kardex_pdf', $data, true);

//        echo $html;
        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4-L');
        $mpdf->WriteHTML($html);
        $mpdf->Output();
    }

    function kardex_excel($id = false, $local = false, $mes = false, $year = false)
    {
        if($local == 'TODOS')
            $local = false;

        $data['producto'] = $this->db->select('producto.producto_nombre as nombre, unidades.nombre_unidad as um_nombre')
            ->from('producto')
            ->join('unidades_has_producto', 'unidades_has_producto.producto_id = producto.producto_id AND unidades_has_producto.orden = 1')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->where('producto.producto_id', $id)
            ->get()->row();

        $data['periodo'] = getMes($mes).' '.$year;
        $data['kardex'] = $this->kardex_model->get_kardex($id, $local, $mes, $year);
        $this->load->view('menu/inventario/kardex_excel', $data);
    }

    function kardex_interno($id = false, $local = false, $mes = false, $year = false)
    {
        if($local == 'TODOS')
            $local = false;

        $data['producto'] = $this->db->select('producto.producto_nombre as nombre, unidades.nombre_unidad as um_nombre')
            ->from('producto')
            ->join('unidades_has_producto', 'unidades_has_producto.producto_id = producto.producto_id AND unidades_has_producto.orden = 1')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->where('producto.producto_id', $id)
            ->get()->row();

        $data['periodo'] = getMes($mes).' '.$year;
        $data['kardex'] = $this->kardex_model->get_kardex_interno($id, $local, $mes, $year);
        $data['tipo_kardex'] = 'INTERNO';
        $data['producto_id'] = $id;
        $this->load->view('menu/inventario/formMovimiento', $data);
    }

    function kardex_interno_excel($id = false, $local = false, $mes = false, $year = false)
    {
        if($local == 'TODOS')
            $local = false;

        $data['producto'] = $this->db->select('producto.producto_nombre as nombre, unidades.nombre_unidad as um_nombre')
            ->from('producto')
            ->join('unidades_has_producto', 'unidades_has_producto.producto_id = producto.producto_id AND unidades_has_producto.orden = 1')
            ->join('unidades', 'unidades.id_unidad = unidades_has_producto.id_unidad')
            ->where('producto.producto_id', $id)
            ->get()->row();

        $data['periodo'] = getMes($mes).' '.$year;
        $data['kardex'] = $this->kardex_model->get_kardex_interno($id, $local, $mes, $year);
        $this->load->view('menu/inventario/kardex_excel', $data);
    }

    function getbylocal()
    {
        $local = $this->input->post('local');
        if ($local != "TODOS") {
            if ($this->session->userdata('VENTA_SIN_STOCK') == 1) {
                $where = 'orden = 1 and (id_local =' . $local . ' or id_local IS NULL)';
            } else {
                $where = 'orden = 1 and id_local =' . $local;
            }
        } else {
            $where = array('orden' => 1);
        }
        $datas = array();
        $produtos = $this->inventario_model->getIventarioProducto($where);
        foreach ($produtos as $producto) {

                $producto['producto_id'] = $producto['producto_id'];
                $producto['producto_id_cero'] = sumCod($producto['producto_id']);
                $datas['productos'][] = $producto;

        }
        echo json_encode($datas['productos']);
    }

    function getbyJson()
    {
        $local = $this->input->post('local');
        $array = array();
        $array['productosjson'] = array();

        if ($local != "TODOS") {

            $where = 'orden = 1 and (id_local =' . $local . ' or id_local IS NULL)';

        } else {
            $where = array('orden' => 1);
        }


        $total = $this->producto_model->count_all();
        $start = 0;
        $limit = false;

        $draw = $this->input->get('draw');
        if (!empty($draw)) {

            $start = $this->input->get('start');
            $limit = $this->input->get('length');
        }


            $where = array();
            $where['producto_activo'] = 1;
            $where['producto_estatus'] = 1;
            $nombre_or = false;
            $where_or = false;
            $nombre_in = false;
            $where_in = false;
            $select = '*';
            $from = "producto";
            $join = array('inventario', 'unidades_has_producto', 'unidades');

            $campos_join = array('producto.producto_id=inventario.id_producto', 'unidades_has_producto.producto_id=producto.producto_id',
                'unidades.id_unidad=unidades_has_producto.id_unidad');
            $tipo_join = array('inner', 'left', 'left');

            $search = $this->input->get('search');
            $buscar = $search['value'];
            $where_custom = false;
        $columns = $this->input->get('columns');
            if (!empty($search['value'])) {
                $buscarcod = $buscar;
                if (is_numeric($buscar)) {
                    $buscarcod = restCod($buscar);
                }

                $where_custom = "(producto.producto_id = '" . $buscarcod . "' or producto.producto_nombre LIKE '%" . $buscar . "%'
            or unidades.nombre_unidad LIKE '%" . $buscar . "%' or cantidad LIKE '%" . $buscar . "%'
            or fraccion LIKE '%" . $buscar . "%')";
            }


            $ordenar = $this->input->get('order');

            $order = false;
            $order_dir = 'desc';
            if (!empty($ordenar)) {
                $order_dir = $ordenar[0]['dir'];
                if ($ordenar[0]['column'] == 0) {
                    $order = 'producto.producto_id';
                }
                if ($ordenar[0]['column'] == 1) {
                    $order = 'producto.producto_nombre';
                }
                if ($ordenar[0]['column'] == 2) {
                    $order = 'unidades.nombre_unidad ';
                }
                if ($ordenar[0]['column'] == 3) {
                    $order = 'cantidad';
                }
                if ($ordenar[0]['column'] == 4) {
                    $order = 'fraccion';
                }


            }

            $group = 'producto.producto_id';


            $productos = $this->inventario_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, $group, $order, "RESULT_ARRAY", $limit, $start, $order_dir, false, $where_custom);

            foreach ($productos as $pd):
                $PRODUCTOjson = array();

                $PRODUCTOjson[] = sumCod($pd['producto_id']);
                $PRODUCTOjson[] = $pd['producto_nombre'] . " " . $pd['producto_descripcion'];
                $PRODUCTOjson[] = $pd['nombre_unidad'];
                $PRODUCTOjson[] = $pd['cantidad'];
                $PRODUCTOjson[] = $pd['fraccion'];
                $array['productosjson'][] = $PRODUCTOjson;
            endforeach;

            $array['data'] = $array['productosjson'];
            $array['draw'] = $draw;//esto debe venir por post
            $array['recordsTotal'] = $total;
            $array['recordsFiltered'] = $total; // esto dbe venir por post

            echo json_encode($array);

    }


    function existencia_producto()
    {

        $this->load->view('menu/inventario/existencia_producto');
    }

    function buscarproducto()
    {
        $id = $this->input->post('id');
        $data = $this->producto_model->get_by_id($id);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function verajuste($id = FALSE)
    {

        $data = array();

        $data['detalles'] = $this->ajustedetalle_model->get_ajuste_by_inventario($id);


        $this->load->view('menu/inventario/verajuste', $data);
    }

    function get_unidades_has_producto()
    {
        $id_producto = $this->input->post('id_producto');
        $data['unidades'] = $this->unidades_model->get_by_producto($id_producto);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function guardar()
    {
        $id_productos = $this->input->post('id_producto');
        $nombre_producto = $this->input->post('nombre_producto');
        $cantidad_producto = $this->input->post('cantidad_producto');
        $fraccion_producto = $this->input->post('fraccion_producto');
        $costo_unitario_producto = $this->input->post('costo_unitario');

        $ajuste = array(
            'fecha' => date('Y-m-d', strtotime($this->input->post('fecha'))) . " " . date('H:i:s'),
            'descripcion' => $this->input->post('descripcion'),
            'local_id' => $this->input->post('local'),
            'usuario' => $this->session->userdata('nUsuCodigo'),
        );

        $inventario = array(
            'id_local' => $this->input->post('local')
        );

        $id_ajuste = $this->ajusteinventario_model->set_ajuste($ajuste);

        $kardex = array();
        for ($i = 0; $i < count($id_productos); $i++) {
            $buscar = sizeof($this->inventario_model->get_by_id_row($id_productos[$i], $this->input->post('local')));

            $inventario['cantidad'] = $cantidad_producto[$i];
            $inventario['fraccion'] = $fraccion_producto[$i];

            if ($buscar < 1) {

                $inventario['id_producto'] = $id_productos[$i];

                $id_inventario = $this->inventario_model->set_inventario($inventario);

                $detalles = array(
                    'id_ajusteinventario' => $id_ajuste,
                    'cantidad_detalle' => $cantidad_producto[$i],
                    'fraccion_detalle' => $fraccion_producto[$i],
                    'id_inventario' => $id_inventario,
                    'old_cantidad' => 0,
                    'old_fraccion' => 0,
                );
                $result = $this->ajustedetalle_model->set_ajuste_detalle($detalles);
            } else {
                $local_inventario = array('id_producto' => $id_productos[$i], 'id_local' => $this->input->post('local'));

                $resultado = $this->inventario_model->get_by($local_inventario);

                $detalles = array(
                    'id_ajusteinventario' => $id_ajuste,
                    'cantidad_detalle' => $cantidad_producto[$i],
                    'fraccion_detalle' => $fraccion_producto[$i],
                    'id_inventario' => $resultado['id_inventario'],
                    'old_cantidad' => $resultado['cantidad'],
                    'old_fraccion' => $resultado['fraccion']
                );

                $result = $query_detalle = $this->ajustedetalle_model->set_ajuste_detalle($detalles);

                $wheres = array(
                    'id_local' => $this->input->post('local'),
                    'id_producto' => $id_productos[$i]
                );

                $result = $this->inventario_model->update_inventario($inventario, $wheres);

                $this->inventario_model->update_costo_unitario($costo_unitario_producto[$i], $id_productos[$i]);
            }


            /***********traigo el inventario actual de este producto y lo llevo a unidad minima******/
            $existencia = $this->inventario_model->get_by_id_row($id_productos[$i], $this->input->post('local'));

            $unidades = $this->unidades_model->get_by_producto($id_productos[$i]);

            $data['unidad_maxima'] = "";
            $data['existencia_unidad'] = 0;
            $data['maxima_unidades'] = 0;
            $data['unidad_minima'] = "";
            $data['existencia_fraccion'] = 0;

            if (sizeof($unidades) > 0) {
                $data['unidad_maxima'] = $unidades[0]['nombre_unidad'];
                $data['maxima_unidades'] = $unidades[0]['unidades'];
                $data['unidad_minima'] = $unidades[sizeof($unidades) - 1]['id_unidad'];

                if (sizeof($existencia) > 1) {
                    $data['existencia_fraccion'] = $existencia['fraccion'];
                    $data['existencia_unidad'] = $existencia['cantidad'];

                }

                $data['nombre'] = $unidades[0]['producto_nombre'];
                $data['cualidad'] = $unidades[0]['producto_cualidad'];
                $data['stock_status'] = $unidades[0]['venta_sin_stock'];
            }


            $existencia_actual = ($existencia['cantidad'] * $data['maxima_unidades']) + $existencia['fraccion'];

            $existencia_nueva = ($cantidad_producto[$i] * $data['maxima_unidades']) + $fraccion_producto[$i];


            $item_kardex = array(
                'dkardexFecha' => date('Y-m-d H:i:s'),
                'ckardexReferencia' => AJUSTE_INVENTARIO,
                'cKardexProducto' => $id_productos[$i],
                'nKardexCantidad' => $cantidad_producto[$i],
                'nKardexPrecioUnitario' => $costo_unitario_producto[$i],
                'nKardexPrecioTotal' => $costo_unitario_producto[$i],
                'cKardexUsuario' => $this->session->userdata('nUsuCodigo'),
                'cKardexOperacion' => AJUSTE_INVENTARIO,
                'cKardexUnidadMedida' => $data['unidad_minima'],
                'cKardexAlmacen' => $this->input->post('local'),
                'cKardexTipo' => ($existencia_nueva > $existencia_actual) ? ENTRADA : SALIDA,
                'cKardexIdOperacion' => $id_ajuste,
                'cKardexTipoDocumento' => NULL,
                'cKardexNumeroDocumento' => NULL,
                'cKardexNumeroSerie' => NULL,
                'cKardexEstado' => 'EMITIDO',
                'stockUManterior' => $existencia_actual,
                'stockUMactual' => $existencia_nueva,
                'cKardexTipoDocumentoFiscal' => NULL,
                'cKardexNumeroDocumentoFiscal' => NULL,
                'cKardexNumeroSerieFiscal' => NULL,
                'cKardexCliente' => NULL,
                'cKardexProveedor' => null,
            );

            array_push($kardex, $item_kardex);
        }
        $this->kardex_model->set_batch($kardex);

        if ($result != FALSE) {


            $this->session->set_flashdata('success', 'Operación realizada exitosamente');
            $json['success'] = 'Operación realizada exitosamente';
        } else {
            $this->session->set_flashdata('error', 'Ha ocurrido un error al ajustar el inventario');
            $json['error'] = 'Ha ocurrido un error al ajustar el inventario';
        }

        echo json_encode($json);
    }

    function get_existencia_producto()
    {
        $producto = $this->input->post('producto');
        $local = $this->session->userdata('id_local');
        $existencia = $this->inventario_model->get_by_id_row($producto, $local);


        $unidades = $this->unidades_model->get_by_producto($producto);
        $data["precios_normal"] = $this->precios_model->get_precios();
        if ($this->input->is_ajax_request()) {
            $data['unidad_maxima'] = "";
            $data['existencia_unidad'] = 0;
            $data['maxima_unidades'] = 0;
            $data['unidad_minima'] = "";
            $data['existencia_fraccion'] = 0;

            if (sizeof($unidades) > 0) {
                $data['unidad_maxima'] = $unidades[0]['nombre_unidad'];
                $data['maxima_unidades'] = $unidades[0]['unidades'];
                $data['unidad_minima'] = $unidades[sizeof($unidades) - 1]['nombre_unidad'];

                if (sizeof($existencia) > 1) {
                    $data['existencia_fraccion'] = $existencia['fraccion'];
                    $data['existencia_unidad'] = $existencia['cantidad'];

                }

                $data['nombre'] = $unidades[0]['producto_nombre'];
                $data['cualidad'] = $unidades[0]['producto_cualidad'];
                $data['stock_status'] = $unidades[0]['venta_sin_stock'];
            }
            echo json_encode($data);
        } else {
            redirect('productos');
        }
    }

    function view_reporte()
    {

        if ($this->input->post('id_local') != "seleccione") {
            $local = $this->input->post('id_local');
            $tipo = $this->input->post('tipo');
            $porcentaje = 30;
            if ($tipo == "MINIMA") {
                $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
 JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad <= producto_stockminimo";
            } elseif ($tipo == "ALTA") {

                $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
  JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad >= producto_stockminimo + (producto_stockminimo * 30)/100";
            } elseif ($tipo == "BAJA") {
                $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
  JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad < producto_stockminimo + (producto_stockminimo * 30)/100 and cantidad > producto_stockminimo ";
            }


            $data['inventarios'] = $this->inventario_model->get_all_by_array($arreglo);;
            $data['tipo_reporte'] = $tipo;
            $data['local'] = $local;
            $this->load->view('menu/inventario/lista_reporte', $data);

        }
    }

    function existencia_minima()
    {

        $data['locales'] = $this->local_model->get_all();
        $data['tipo'] = "MINIMA";
        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/reportes', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function existencia_alta()
    {

        $data['locales'] = $this->local_model->get_all();
        $data['tipo'] = "ALTA";
        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/reportes', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function existencia_baja()
    {

        $data['locales'] = $this->local_model->get_all();
        $data['tipo'] = "BAJA";
        $dataCuerpo['cuerpo'] = $this->load->view('menu/inventario/reportes', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function pdf($id, $local)
    {
        $porcentaje = 30;
        if ($id == "MINIMO") {
            $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
 JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad <= producto_stockminimo";
        } elseif ($id == "ALTA") {

            $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
  JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad >= producto_stockminimo + (producto_stockminimo * 30)/100";
        } elseif ($id == "BAJA") {
            $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
  JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad < producto_stockminimo + (producto_stockminimo * 30)/100 and cantidad > producto_stockminimo ";
        }


        $inventarios = $this->inventario_model->get_all_by_array($arreglo);

        //var_dump($miembro);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('P');
        // $pdf->SetCreator(PDF_CREATOR);
        if ($id == 1) {
            $pdf->SetTitle('Existencia Minima');
        } elseif ($id == 2) {
            $pdf->SetTitle('Existencia Alta');
        } else {
            $pdf->SetTitle('Existencia Bajas');
        }

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
        /*
                $html .= "<b>Apellidos y Nombres:</b>  " . $miembro['nombre'] . " " . $miembro['apellido'] . "<br>";
                $html .= "<b>Nacido el: </b> " . date('Y-m-d', strtotime($miembro['fecha_nac'])) . "     Edad:" . $this->utils->calcular_edad(date('Y-m-d', strtotime($miembro['fecha_nac']))) . "<br>";
                $html .= "<b>ProfesiÃ³n: </b> " . $profesion . "<br>";
                $html .= "<b>Grado Actual: </b> " . $miembro['grad_nombre'] . "<br><br>";
                $html .= "<b>Grados Alcanzados:</b> " . "<br>";
        */
        foreach ($inventarios as $inventario) {

            $nombre_local = $inventario->local_nombre;
        }

        $html .= "<br><b>" . $nombre_local . " :</b> " . "<br>";

        $minima = 5;
        $alta = 50;
        $baja = 3;

        $html .= "<table><tr><th>ID</th><th>Nombre</th><th>Existencia</th><th>Fracci&oacute;n</th></tr>";
        foreach ($inventarios as $inventario) {
            $html .= "<tr><td>" . $inventario->id_producto . "</td>";
            $html .= "<td>" . $inventario->producto_nombre . "</td>";
            $html .= "<td>" . $inventario->cantidad . "</td>";
            $html .= "<td>" . $inventario->fraccion . "</td> </tr>";

        }
        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este mÃ©todo tiene varias opciones, consulte la documentaciÃ³n para mÃ¡s informaciÃ³n.
        if ($id == "MINIMA") {
            $nombre_archivo = utf8_decode("ExistenciaMinima.pdf");
        } elseif ($id == "ALTA") {
            $nombre_archivo = utf8_decode("ExistenciaAlta.pdf");
        } else {
            $nombre_archivo = utf8_decode("ExistenciaBaja.pdf");
        }

        $pdf->Output($nombre_archivo, 'D');


    }

    function pdfKardex($id, $local, $documento_fiscal = FALSE)
    {

        if ($documento_fiscal != false) {
            $data['operacion'] = "VENTA";
        }

        if ($local != "TODOS") {

            $order = "dkardexFecha DESC,cKardexAlmacen";
            $where = array('cKardexProducto' => $id,
                'cKardexAlmacen' => $local);

            $local_nombre = $this->local_model->get_by('int_local_id', $local);
        } else {
            $order = "dkardexFecha DESC";
            $where = array('cKardexProducto' => $id);
            $local_todo = $local;
        }

        $order_ingreso = "dkardexFecha DESC";
        $data['ingresos'] = $this->kardex_model->getKardex($where, $order_ingreso);


        //var_dump($miembro);
        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Movimiento de Inventario');


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
        $pdf->SetFont('helvetica', '', 8, '', true);

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

        $pdf->SetFontSize(8);


        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{  background-color: #fff; font-size:6px; text-align:center; }";
        $html .= "td{color: #222;  background-color: #fff;  font-size:7px; text-align:center;}";
        $html .= "body{font-size:8px}";
        $html .= "</style>";
        /*
                $html .= "<b>Apellidos y Nombres:</b>  " . $miembro['nombre'] . " " . $miembro['apellido'] . "<br>";
                $html .= "<b>Nacido el: </b> " . date('Y-m-d', strtotime($miembro['fecha_nac'])) . "     Edad:" . $this->utils->calcular_edad(date('Y-m-d', strtotime($miembro['fecha_nac']))) . "<br>";
                $html .= "<b>ProfesiÃ³n: </b> " . $profesion . "<br>";
                $html .= "<b>Grado Actual: </b> " . $miembro['grad_nombre'] . "<br><br>";
                $html .= "<b>Grados Alcanzados:</b> " . "<br>";
        */
        $html .= '<table><tr><td align="left">FORMATO: "REGISTRO DE INVENTARIO PERMANENTE VALORIZADO - DETALLE DEL INVENTARIO VALORIZADO"</td><td></td></tr> ';
        $html .= '<tr><td align="left">PERIODO:</td><td></td></tr>';
        $html .= '<tr><td align="left">RUC:</td><td></td></tr>';
        $html .= '<tr><td align="left">APELLIDO Y NOMBRE, DENOMINACION O RAZON SOCIAL:</td><td></td></tr>';
        if (isset($local_nombre)) {
            $html .= '<tr><td align="left">ESTABLECIMIENTO: ' . $local_nombre['local_nombre'] . '</td><td></td></tr>';
        } else {
            $html .= '<tr><td align="left">ESTABLECIMIENTO: </td><td></td></tr>';
        }
        $html .= '<tr><td align="left">CODIGO DE LA EXISTENCIA:</td>';
        $html .= '<td  align="center"><font color="red"></font> </td></tr>';
        $html .= '<tr><td></td><td  align="center"><font color="red"></font></td></tr>';
        $html .= '<tr><td align="left">TIPO:</td><td></td></tr>';
        $html .= '<tr><td align="left">DESCRIPCION:</td><td></td></tr>';
        $html .= '<tr><td align="left">METODO DE VALUACION:</td><td></td></tr>';
        $html .= '<tr><td align="left">PRESENTACION DEL PRODUCTO:</td><td></td></tr>';
        $html .= '</table>';
        $html .= '<br /><br />';


        $html .= "<table><tr><th>DOCUMENTO </th><th>DE TRANSLADO</th><th>COMPROBANTE</th><th>DE PAGO</th><th>TIPO DE </th><th>ESTADO</th><th>UNIDAD</th><th></th><th>ENTRADAS</th><th></th><th></th><th>SALIDAS</th><th></th><th></th><th>SALDO FINAL</th><th></th>";
        if (!isset($data['operacion'])) {
            $html .= "<th>CLIENTE</th><th>PROVEEDOR</th>";
        }
        $html .= "</tr>";
        $html .= "<tr><th>DOCUMENTO </th><th>INTERNO</th><th>O SIMILAR</th><th></th><th></th><th></th><th>DE</th><th></th><th></th><th></th><th></th><th></th><th></th></tr>";
        $html .= "<tr><th>FECHA</th><th>TIPO</th><th>SERIE</th><th>NUMERO</th><th>OPERACION</th>";
        $html .= "<th></th><th>MEDIDA</th><th>CANTIDAD</th><th>C. UNITARIO</th><th>C. TOTAL</th><th>CANTIDAD</th><th>C. UNITARIO</th><th>C. TOTAL</th>";
        $html .= "<th>CANTIDAD</th><th>C. UNITARIO</th><th>C. TOTAl</th><th></th><th></th>";


        $html .= "</tr>";

//      if(!isset($local_nombre)){ $html .= "<th>Local</th>"; }    $html .= "</tr>";

        foreach ($data['ingresos'] as $arreglo) {

            $html .= "<tr><td>" . date('d-m-Y', strtotime($arreglo['dkardexFecha'])) . "</td>";

            if (isset($data['operacion']) && ($arreglo['cKardexOperacion'] == "VENTA")) {
                $html .= "<td>" . $arreglo['cKardexTipoDocumentoFiscal'] . "</td>";
                $html .= "<td>" . $arreglo['cKardexNumeroSerieFiscal'] . "</td>";
                $html .= "<td>" . $arreglo['cKardexNumeroDocumentoFiscal'] . "</td>";
            } else {
                $html .= "<td>" . $arreglo['cKardexTipoDocumento'] . "</td>";
                $html .= "<td>" . $arreglo['cKardexNumeroSerie'] . "</td>";
                $html .= "<td>" . $arreglo['cKardexNumeroDocumento'] . "</td>";

            }

            $html .= "<td>" . $arreglo['cKardexTipo'] . "</td>";
            $html .= "<td>" . $arreglo['cKardexEstado'] . "</td>";
            $html .= "<td>" . $arreglo['nombre_unidad'] . "</td>";

            if ($arreglo['cKardexTipo'] == "ENTRADA") {
                $html .= "<td>" . $arreglo['nKardexCantidad'] . "</td>";
                $html .= "<td>" . $arreglo['nKardexPrecioUnitario'] . "</td>";
                $html .= "<td>" . $arreglo['nKardexPrecioTotal'] . "</td>";
            } else {
                $html .= "<td></td>";
                $html .= "<td></td>";
                $html .= "<td></td>";
            }

            if ($arreglo['cKardexTipo'] == "SALIDA") {
                $html .= "<td>" . $arreglo['nKardexCantidad'] . "</td>";
                $html .= "<td>" . $arreglo['nKardexPrecioUnitario'] . "</td>";
                $html .= "<td>" . $arreglo['nKardexPrecioTotal'] . "</td>";
            } else {
                $html .= "<td></td>";
                $html .= "<td></td>";
                $html .= "<td></td>";
            }

            $html .= "<td>" . $arreglo['stockUMactual'] . "</td>";
            $html .= "<td>" . $arreglo['nKardexPrecioUnitario'] . "</td>";
            $html .= "<td>" . $arreglo['nKardexPrecioTotal'] . "</td>";
            if ($documento_fiscal == false) {
                if (!isset($data['operacion']) && ($arreglo['cKardexOperacion'] == VENTA)) {

                    $html .= "<td>" . $arreglo['razon_social'] . "</td>";
                    $html .= "<td></td>";
                } elseif (!isset($data['operacion']) && ($arreglo['cKardexOperacion'] == INGRESO)) {

                    $html .= "<td></td>";
                    $html .= "<td>" . $arreglo['proveedor_nombre'] . "</td>";
                } elseif (!isset($data['operacion']) && ($arreglo['cKardexOperacion'] == AJUSTE_INVENTARIO)) {

                    $html .= "<td></td>";
                    $html .= "<td>" . $this->session->userdata('EMPRESA_NOMBRE') . "</td>";
                }
            }

            $html .= "</tr>";

        }

        $html .= "</table>";
        $html .= "<span></span>";
        /*
        if(!isset($local_nombre)){
            $html .=" <td>".$arreglo['local_nombre']."</td>";
         } $html .=" </tr>";

                }
                $html .= "</table>"; */

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este mÃ©todo tiene varias opciones, consulte la documentaciÃ³n para mÃ¡s informaciÃ³n.

        $nombre_archivo = utf8_decode("ReporteKardex.pdf");

        $pdf->Output($nombre_archivo, 'D');


    }


    function excel($id, $local)
    {

        $porcentaje = 30;
        if ($id == "MINIMA") {
            $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
 JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad <= producto_stockminimo";
        } elseif ($id == "ALTA") {

            $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
  JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad >= producto_stockminimo + (producto_stockminimo * 30)/100";
        } elseif ($id == "BAJA") {
            $arreglo = "SELECT * FROM inventario JOIN producto ON producto.`producto_id`=inventario.`id_producto`
  JOIN local ON local.`int_local_id`=inventario.`id_local` WHERE id_local='$local'
AND cantidad < producto_stockminimo + (producto_stockminimo * 30)/100 and cantidad > producto_stockminimo ";
        }


        $inventarios = $this->inventario_model->get_all_by_array($arreglo);


        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Reporte de Invetario")
            ->setSubject("Reporte de Invetario")
            ->setDescription("Reporte de Invetario")
            ->setKeywords("Reporte de Invetario")
            ->setCategory("Reporte de Invetario");


        $columna[0] = "ID";
        $columna[1] = "NOMBRE";
        $columna[2] = "EXISTENCIA";
        $columna[3] = "FRACCION";


        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }

        $row = 2;
        foreach ($inventarios as $inventario) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $inventario->id_producto);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $inventario->producto_nombre);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $inventario->cantidad);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $inventario->fraccion);

            $row++;
        }

// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Reporte Inventario');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteGrupo.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    function excelKardex($id, $local, $documento_fiscal = FALSE)
    {

        if ($documento_fiscal != false) {
            $data['operacion'] = "VENTA";
        }

        if ($local != "TODOS") {

            $order = "dkardexFecha DESC,cKardexAlmacen";
            $where = array('cKardexProducto' => $id,
                'cKardexAlmacen' => $local);

            $local_nombre = $this->local_model->get_by('int_local_id', $local);
        } else {
            $order = "dkardexFecha DESC";
            $where = array('cKardexProducto' => $id);
            $local_todo = $local;
        }

        $order_ingreso = "dkardexFecha DESC";
        $data['ingresos'] = $this->kardex_model->getKardex($where, $order_ingreso);
        if ($local != "TODOS") {

            $order = "dkardexFecha DESC,cKardexAlmacen";
            $where = array('cKardexProducto' => $id,
                'cKardexAlmacen' => $local);

            $local_nombre = $this->local_model->get_by('int_local_id', $local);
        } else {
            $order = "dkardexFecha DESC";
            $where = array('cKardexProducto' => $id);
            $local_todo = $local;
        }

        $order_ingreso = "dkardexFecha DESC";
        $data['ingresos'] = $this->kardex_model->getKardex($where, $order_ingreso);


        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Movimiento de Inventario")
            ->setSubject("Movimiento de Inventario")
            ->setDescription("Movimiento de Inventario")
            ->setKeywords("Movimiento de Inventario")
            ->setCategory("Movimiento de Inventario");


        $columna[0] = "FECHA";
        $columna[1] = "TIPO";
        $columna[2] = "SERIE";
        $columna[3] = "NUMERO";
        $columna[4] = "";
        $columna[5] = "";
        $columna[6] = "";
        $columna[7] = "CANTIDAD";
        $columna[8] = "C. UNITARIO";
        $columna[9] = "C. TOTAL";
        $columna[10] = "CANTIDAD";
        $columna[11] = "C. UNITARIO";
        $columna[12] = "C. TOTAL";
        $columna[13] = "CANTIDAD";
        $columna[14] = "C. UNITARIO";
        $columna[15] = "C. TOTAL";

        if (!isset($data['operacion'])) {
            $this->phpexcel->setActiveSheetIndex(0)->mergeCells('Q13:Q15')
                ->setCellValueByColumnAndRow(16, 13, 'CLIENTE');

            $this->phpexcel->setActiveSheetIndex(0)->mergeCells('R13:R15')
                ->setCellValueByColumnAndRow(17, 13, 'PROVEEDOR');


        }


        $this->phpexcel->getActiveSheet()->getStyle('A13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->phpexcel->getActiveSheet()->getStyle('B13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->phpexcel->getActiveSheet()->getStyle('C13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->phpexcel->getActiveSheet()->getStyle('D13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $this->phpexcel->getActiveSheet()->getStyle('A13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('B13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('C13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('D13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('F13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('F13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('G13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('G13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('K4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('K4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('R13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('R13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('Q13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('Q13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('Q12')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('A13:D14')->getAlignment()->setWrapText(true);
        $this->phpexcel->getActiveSheet()->getStyle('E13:E15')->getAlignment()->setWrapText(true);
        $this->phpexcel->getActiveSheet()->getStyle('G13:G15')->getAlignment()->setWrapText(true);
        $this->phpexcel->getActiveSheet()->getStyle('K4:P5')->getAlignment()->setWrapText(true);


        $this->phpexcel->getActiveSheet()->getStyle('E13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->phpexcel->getActiveSheet()->getStyle('E14')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
        $this->phpexcel->getActiveSheet()->getStyle('E15')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);

        $this->phpexcel->getActiveSheet()->getStyle('E13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('E14')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('E15')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $this->phpexcel->getActiveSheet()->getColumnDimension('D')->setWidth(13);
        $this->phpexcel->getActiveSheet()->getColumnDimension('R')->setWidth(15);


        $this->phpexcel->getActiveSheet()->getStyle('I13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('H13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('J13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('I13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('H13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('J13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


        $this->phpexcel->getActiveSheet()->getStyle('K13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('L13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('M13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('K13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('L13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('M13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


        $this->phpexcel->getActiveSheet()->getStyle('N13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('O13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('P13')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

        $this->phpexcel->getActiveSheet()->getStyle('N13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('O13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $this->phpexcel->getActiveSheet()->getStyle('P13')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);


        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A1:T1')
            ->setCellValueByColumnAndRow(0, 1, 'REGISTRO DE INVENTARIO PERMANENTE VALORIZADO - DETALLE DEL INVENTARIO VALORIZADO');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A2:T2');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A3:T3')
            ->setCellValueByColumnAndRow(0, 3, 'PERÍODO:');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A4:J4')
            ->setCellValueByColumnAndRow(0, 4, 'RUC:');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('K4:P5')
            ->setCellValueByColumnAndRow(10, 4, '');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A5:J5')
            ->setCellValueByColumnAndRow(0, 5, 'APELLIDOS Y NOMBRES, DENOMINACIÓN O RAZÓN SOCIAL:');

        // $this->phpexcel->cellColor('A1:T1', 'F28A8C');


        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A6:T6')
            ->setCellValueByColumnAndRow(0, 6, 'ESTABLECIMIENTO (1):');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A7:T7')
            ->setCellValueByColumnAndRow(0, 7, 'CÓDIGO DE LA EXISTENCIA:');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A8:T8')
            ->setCellValueByColumnAndRow(0, 8, 'TIPO (TABLA 5):');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A9:T9')
            ->setCellValueByColumnAndRow(0, 9, 'DESCRIPCIÓN:');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A10:T10')
            ->setCellValueByColumnAndRow(0, 10, 'MÉTODO DE VALUACIÓN:');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A11:T11')
            ->setCellValueByColumnAndRow(0, 11, 'PRESENTACION DEL PRODUCTO');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A13:D14')
            ->setCellValueByColumnAndRow(0, 13, 'DOCUMENTO DE TRANSLADO,OMPROBANTE DE PAGO OCUMENTO INTERNO O SIMILAR ');


        // $this->phpexcel->setActiveSheetIndex(0)->mergeCells('A14:D14')->setCellValueByColumnAndRow(0, 14, 'DOCUMENTO INTERNO O SIMILAR');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('E13:E15')
            ->setCellValueByColumnAndRow(4, 13, 'TIPO DE OPERACION');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('F13:F15')
            ->setCellValueByColumnAndRow(5, 13, 'ESTADO');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('G13:G15')
            ->setCellValueByColumnAndRow(6, 13, 'UNIDAD DE MEDIDA');


        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('H13:J14')
            ->setCellValueByColumnAndRow(7, 13, 'ENTRADAS');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('K13:M14')
            ->setCellValueByColumnAndRow(10, 13, 'SALIDAS');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('N13:P14')
            ->setCellValueByColumnAndRow(13, 13, 'SALDO FINAL');

        $this->phpexcel->setActiveSheetIndex(0)->mergeCells('Q12:R12')
            ->setCellValueByColumnAndRow(16, 12, '');


        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 15, $columna[$i]);

        }

        $row = 16;

        foreach ($data['ingresos'] as $arreglo) {
            $col = 0;

            $this->phpexcel->getActiveSheet()->getColumnDimension('A')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('C')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('F')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('B')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('H')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('I')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('J')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('K')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('L')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('M')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('N')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('O')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('P')->setAutoSize('true');
            $this->phpexcel->getActiveSheet()->getColumnDimension('Q')->setAutoSize('true');
            // $this->phpexcel->getActiveSheet()->getStyle('K4')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'F6B4B4')));
            // $this->phpexcel->getActiveSheet()->getStyle('K5')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'F6B4B4')));
            // $this->phpexcel->getActiveSheet()->getStyle('Q12')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID,'startcolor' => array('rgb' =>'F6B4B4')));
            $this->phpexcel->getActiveSheet()->getStyle('R13')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'B4F6C6')));
            $this->phpexcel->getActiveSheet()->getStyle('Q13')->getFill()->applyFromArray(array('type' => PHPExcel_Style_Fill::FILL_SOLID, 'startcolor' => array('rgb' => 'B4F6C6')));


            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, date('d-m-Y', strtotime($arreglo['dkardexFecha'])));


            if (isset($data['operacion']) && ($arreglo['cKardexOperacion'] == VENTA)) {

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $arreglo['cKardexTipoDocumentoFiscal']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $arreglo['cKardexNumeroSerieFiscal']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, strval($arreglo['cKardexNumeroDocumentoFiscal']));

            } else {
                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, strval($arreglo['cKardexTipoDocumento']));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, strval($arreglo['cKardexNumeroSerie']));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, strval($arreglo['cKardexNumeroDocumento']));
            }

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $arreglo['cKardexTipo']);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $arreglo['cKardexEstado']);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $arreglo['nombre_unidad']);

            if ($arreglo['cKardexTipo'] == "ENTRADA") {

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $arreglo['nKardexCantidad']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $arreglo['nKardexPrecioUnitario']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $arreglo['nKardexPrecioTotal']);
            } else {

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "");

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "");

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "");

            }

            if ($arreglo['cKardexTipo'] == "SALIDA") {

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $arreglo['nKardexCantidad']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $arreglo['nKardexPrecioUnitario']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $arreglo['nKardexPrecioTotal']);

            } else {

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "");

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "");

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "");

            }

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $arreglo['stockUMactual']);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $arreglo['nKardexPrecioUnitario']);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $arreglo['nKardexPrecioTotal']);

            if ($documento_fiscal == false) {
                if (!isset($data['operacion']) && ($arreglo['cKardexOperacion'] == VENTA)) {

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $arreglo['razon_social']);

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, "");

                } elseif
                (!isset($data['operacion']) && ($arreglo['cKardexOperacion'] == INGRESO)
                ) {

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, "");

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $arreglo['proveedor_nombre']);

                } elseif
                (!isset($data['operacion']) && ($arreglo['cKardexOperacion'] == AJUSTE_INVENTARIO)
                ) {

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, "");

                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $this->session->userdata('EMPRESA_NOMBRE'));

                }
            }

            $row++;
        }

// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Reporte Kardex');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ReporteKardex.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }
}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class producto extends MY_Controller
{

    private $columnas = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('producto/producto_model');
        $this->load->model('marca/marcas_model');
        $this->load->model('linea/lineas_model');
        $this->load->model('familia/familias_model');
        $this->load->model('grupos/grupos_model');
        $this->load->model('subfamilia/subfamilias_model');
        $this->load->model('subgrupos/subgrupos_model');
        $this->load->model('proveedor/proveedor_model');
        $this->load->model('impuesto/impuestos_model');
        $this->load->model('venta/venta_model');

        $this->load->model('unidades/unidades_model');
        $this->load->model('bonificaciones/bonificaciones_model');
        $this->load->model('descuentos/descuentos_model');
        $this->load->model('columnas/columnas_model');
        $this->load->model('precio/precios_model');
        $this->load->model('local/local_model');
        $this->load->model('unidades_has_precio/unidades_has_precio_model');
        $this->load->model('clientesgrupos/clientes_grupos_model');

        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');
        //$this->very_sesion();


        $this->columnas = $this->columnas_model->get_by('tabla', 'producto');
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


        $data['locales'] = $this->local_model->get_all();
        $data["lstProducto"] = $this->producto_model->get_all_by_local($data["locales"][0]["int_local_id"], false);
        $data['columnas'] = $this->columnas;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/producto/producto', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }

    function reporteEstado()
    {
        $data['productos'] = $this->producto_model->select_all_producto();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/producto/reporteEstadoProducto', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }

    function estadoProducto($id = FALSE)
    {

        if ($id != FALSE) {

            $precio_venta = $this->precios_model->get_by('nombre_precio', 'Precio Venta');
            $data['producto_unidad'] = $this->unidades_model->get_by_producto($id);

            //var_dump($data);
            $select = '*';
            $from = "unidades_has_precio";
            $join = false;
            $campos_join = false;
            $tipo_join = false;

            $group = false;
            $where = array(
                'id_unidad' => $data['producto_unidad'][0]['id_unidad'],
                'id_producto' => $id,
                'id_precio' => $precio_venta['id_precio']
            );
            $data['precio_venta'] = $this->unidades_has_precio_model->traer_by($select, $from, $join,
                $campos_join, $tipo_join, $where, false, false, false, false, $group, false, "ROW_ARRAY");

            $select = 'count(id_producto) as cantidad_bonificada';
            $from = "detalle_venta";
            $where = array(
                'id_producto' => $id,
                'bono' => 1
            );
            $data['cantidad_comprada'] = $this->producto_model->cantidad_comprada($id);
            $data['producto_bonificado'] = $this->venta_model->traer_by($select, $from, $join,
                $campos_join, $tipo_join, $where, false, false, false, false, $group, false, "ROW_ARRAY");

            $data['datos_producto'] = $this->producto_model->estado_producto_est($id);

        }
        $this->load->view('menu/ventas/estadoProducto', $data);
    }

    function evolucionCostos($id = FALSE)
    {

        if ($id != FALSE) {

            $precio_venta = $this->precios_model->get_by('nombre_precio', 'Precio Venta');
            $data['productos'] = $this->producto_model->estadoDelProducto($id);

        }
        $this->load->view('menu/ventas/evolucionDeCostos', $data);

    }

    function stock()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }


        $data['locales'] = $this->local_model->get_all();
        $data["lstProducto"] = $this->producto_model->get_all_by_local($data["locales"][0]["int_local_id"], true);
        $data['columnas'] = $this->columnas;
        $dataCuerpo['cuerpo'] = $this->load->view('menu/producto/stock', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }


    function getbylocal()
    {
        $local = $this->input->post('local');
        $data['lstProducto'] = $this->producto_model->get_all_by_local($local, false);
        $data['columnas'] = $this->columnas;


        //echo json_encode($produtos);
        $this->load->view('menu/producto/producto_table', $data);
    }


    function get_by_json()
    {
        $datas = array();
        $columnas = $this->columnas;
        $local = $this->input->get('local');

        // Pagination Result
        $array = array();
        $array['productosjson'] = array();

        $total = $this->producto_model->count_all();
        $start = 0;
        $limit = false;
        $search=$this->input->get('search');

        $draw = $this->input->get('draw');
        if (!empty($draw)) {

            $start = $this->input->get('start');
            $limit = $this->input->get('length');
        }

            $where = array();
            $where['producto_estatus'] = 1;
            $nombre_or = false;
            $where_or = false;
            $nombre_in = false;
            $where_in = false;
            $select = 'producto.*, lineas.nombre_linea, marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto, impuestos.porcentaje_impuesto,
         subfamilia.nombre_subfamilia,subgrupo.nombre_subgrupo';
            $from = "producto";
            $join = array('lineas', 'marcas', 'familia', 'grupos', 'proveedor', 'impuestos', 'subgrupo', 'subfamilia');


            $campos_join = array('lineas.id_linea=producto.producto_linea', 'marcas.id_marca=producto.producto_marca',
                'familia.id_familia=producto.producto_familia', 'grupos.id_grupo=producto.produto_grupo',
                'proveedor.id_proveedor=producto.producto_proveedor', 'impuestos.id_impuesto=producto.producto_impuesto',  'subgrupo.id_subgrupo = producto.producto_subgrupo',
                'subfamilia.id_subfamilia = producto.producto_subfamilia');
            $tipo_join = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'left');


            $search = $this->input->get('search');
            $columns = $this->input->get('columns');
            $buscar = $search['value'];
            $where_custom = false;
            if (!empty($search['value'])) {

                $buscarcod = $buscar;
                if (is_numeric($buscar)) {
                    $buscarcod = restCod($buscar);
                }

                $where_custom = "(producto.producto_id = '" . $buscarcod . "' or producto.producto_nombre LIKE '%" . $buscar . "%'
            or marcas.nombre_marca LIKE '%" . $buscar . "%' or grupos.nombre_grupo LIKE '%" . $buscar . "%'
            or subgrupo.nombre_subgrupo LIKE '%" . $buscar . "%' or familia.nombre_familia LIKE '%" . $buscar . "%'
            or subfamilia.nombre_subfamilia LIKE '%" . $buscar . "%' or lineas.nombre_linea LIKE '%" . $buscar . "%'
            or producto.presentacion LIKE '%" . $buscar . "%' or producto.producto_activo LIKE '%" . $buscar . "%')";
            }


            $ordenar = $this->input->get('order');
            $order = false;
            $order_dir = 'asc';
            if (!empty($ordenar)) {
                $order_dir = $ordenar[0]['dir'];
                if ($ordenar[0]['column'] == 0) {
                    $order = 'producto.producto_id';
                }
                if ($ordenar[0]['column'] == 1) {
                    $order = 'producto.producto_nombre';
                }
                if ($ordenar[0]['column'] == 2) {
                    $order = 'marcas.nombre_marca ';
                }
                if ($ordenar[0]['column'] == 3) {
                    $order = 'familia.nombre_familia';
                }
                if ($ordenar[0]['column'] == 4) {
                    $order = 'subfamilia.nombre_subfamilia';
                }
               
                

            }

            $group = 'producto_id';

            $productos = $this->producto_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, $group, $order, "RESULT_ARRAY", $limit, $start, $order_dir, false, $where_custom);


       

        foreach ($productos as $producto) {
            $PRODUCTOjson = array();
            foreach ($columnas as $col) {
                if (array_key_exists($col->nombre_columna, $producto) and $col->mostrar == TRUE) {

                    if ($col->nombre_columna != 'producto_activo') {

                        $unidades = $this->unidades_model->get_by_producto($producto['producto_id']);
                        $producto['existencia'] = 0;
                        if (isset($unidades[0]) && isset($producto['cantidad'])) {
                            $maxima_unidades = $unidades[0]['unidades'];
                            $cantidad_total = ($producto['cantidad'] * $maxima_unidades) + $producto['fraccion'];
                            $producto['existencia'] = $cantidad_total;
                        }

                        if ($col->nombre_columna == 'producto_id') {
                            $PRODUCTOjson[] = sumCod($producto['producto_id']);
                        } else {

                            $PRODUCTOjson[] = isset($producto[$col->nombre_join]) ? $producto[$col->nombre_join] : '';
                        }


                    }
                }

            }


            $PRODUCTOjson[] = ($producto['producto_activo'] == 1) ? "Activo" : "Inactivo";

            $array['productosjson'][] = $PRODUCTOjson;
        }

        $array['data'] = $array['productosjson'];
        $array['draw'] = $draw;//esto debe venir por post
        $array['recordsTotal'] = $total;
        $array['recordsFiltered'] = $total; // esto dbe venir por post

        echo json_encode($array);
    }


    function get_by_json_stock()
    {
        $datas = array();
        $columnas = $this->columnas;

        // Pagination Result
        $array = array();
        $array['productosjson'] = array();

        $total = $this->producto_model->count_all();
        $start = 0;
        $limit = false;
        $search=$this->input->get('search');

        $draw = $this->input->get('draw');
        if (!empty($draw)) {

            $start = $this->input->get('start');
            $limit = $this->input->get('length');
        }

            $where = array();
            $where['producto_estatus'] = 1;
            $nombre_or = false;
            $where_or = false;
            $nombre_in = false;
            $where_in = false;
            $select = 'producto.*, unidades_has_producto.id_unidad, unidades.nombre_unidad, inventario.id_inventario, inventario.id_local, inventario.cantidad, inventario.fraccion ,lineas.nombre_linea,
         marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto, impuestos.porcentaje_impuesto,
         subfamilia.nombre_subfamilia,subgrupo.nombre_subgrupo';
            $from = "producto";
            $join = array('lineas', 'marcas', 'familia', 'grupos', 'proveedor', 'impuestos', '(SELECT DISTINCT inventario.id_producto, inventario.id_inventario, inventario.cantidad, inventario.fraccion, inventario.id_local FROM inventario  ORDER by id_inventario DESC ) as inventario',
                'unidades_has_producto', 'unidades', 'subgrupo', 'subfamilia');

            /*
$join = array('lineas', 'marcas', 'familia', 'grupos', 'proveedor', 'impuestos', '(SELECT DISTINCT inventario.id_producto, inventario.id_inventario, inventario.cantidad, inventario.fraccion, inventario.id_local FROM inventario WHERE inventario.id_local=' . $local . '  ORDER by id_inventario DESC ) as inventario',
                'unidades_has_producto', 'unidades', 'subgrupo', 'subfamilia');
            */

            $campos_join = array('lineas.id_linea=producto.producto_linea', 'marcas.id_marca=producto.producto_marca',
                'familia.id_familia=producto.producto_familia', 'grupos.id_grupo=producto.produto_grupo',
                'proveedor.id_proveedor=producto.producto_proveedor', 'impuestos.id_impuesto=producto.producto_impuesto', 'inventario.id_producto=producto.producto_id',
                'unidades_has_producto.producto_id=producto.producto_id and unidades_has_producto.orden=1', 'unidades.id_unidad=unidades_has_producto.id_unidad', 'subgrupo.id_subgrupo = producto.producto_subgrupo',
                'subfamilia.id_subfamilia = producto.producto_subfamilia');
            $tipo_join = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left');


            $search = $this->input->get('search');
            $columns = $this->input->get('columns');
            $buscar = $search['value'];
            $where_custom = false;
            if (!empty($search['value'])) {

                $buscarcod = $buscar;
                if (is_numeric($buscar)) {
                    $buscarcod = restCod($buscar);
                }

                $where_custom = "(producto.producto_id = '" . $buscarcod . "' or producto.producto_nombre LIKE '%" . $buscar . "%'
            or marcas.nombre_marca LIKE '%" . $buscar . "%' or grupos.nombre_grupo LIKE '%" . $buscar . "%'
            or subgrupo.nombre_subgrupo LIKE '%" . $buscar . "%' or familia.nombre_familia LIKE '%" . $buscar . "%'
            or subfamilia.nombre_subfamilia LIKE '%" . $buscar . "%' or lineas.nombre_linea LIKE '%" . $buscar . "%'
            or producto.presentacion LIKE '%" . $buscar . "%' or unidades.nombre_unidad LIKE '%" . $buscar . "%')";
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
                    $order = 'marcas.nombre_marca ';
                }
                if ($ordenar[0]['column'] == 3) {
                    $order = 'familia.nombre_familia';
                }
                if ($ordenar[0]['column'] == 4) {
                    $order = 'subfamilia.nombre_subfamilia';
                }
                if ($ordenar[0]['column'] == 5) {
                    $order = 'lineas.nombre_linea';
                }
                if ($ordenar[0]['column'] == 6) {
                    $order = 'producto.presentacion';
                }
                if ($ordenar[0]['column'] == 7) {
                    $order = 'unidades.nombre_unidad';
                }

            }

            $group = 'producto_id';

            $productos = $this->producto_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where, $nombre_in, $where_in, $nombre_or, $where_or, false, $order, "RESULT_ARRAY", $limit, $start,$order_dir,false,$where_custom);



        foreach ($productos as $producto) {
            $PRODUCTOjson = array();
            foreach ($columnas as $col) {
                if (array_key_exists($col->nombre_columna, $producto) and $col->mostrar == TRUE) {

                    if ($col->nombre_columna != 'producto_activo') {

                        $unidades = $this->unidades_model->get_by_producto($producto['producto_id']);
                        $producto['existencia'] = 0;
                        if (isset($unidades[0]) && isset($producto['cantidad'])) {
                            $maxima_unidades = $unidades[0]['unidades'];
                            $cantidad_total = ($producto['cantidad'] * $maxima_unidades) + $producto['fraccion'];
                            $producto['existencia'] = $cantidad_total;
                        }

                        if ($col->nombre_columna == 'producto_id') {
                            $PRODUCTOjson[] = sumCod($producto['producto_id']);
                        } else {

                            $PRODUCTOjson[] = isset($producto[$col->nombre_join]) ? $producto[$col->nombre_join] : '';
                        }


                    }
                }

            }

            $PRODUCTOjson[] = isset($producto['nombre_unidad']) ? $producto['nombre_unidad'] : 'NO POSEEE UNIDADES';
            $PRODUCTOjson[] = isset($producto['cantidad']) ? $producto['cantidad'] : 0;
            $PRODUCTOjson[] = isset($producto['fraccion']) ? $producto['fraccion'] : 0;

            $PRODUCTOjson[] = ($producto['producto_activo'] == 1) ? "Activo" : "Inactivo";

            $array['productosjson'][] = $PRODUCTOjson;
        }

        $array['data'] = $array['productosjson'];
        $array['draw'] = $draw;//esto debe venir por post
        $array['recordsTotal'] = $total;
        $array['recordsFiltered'] = $total; // esto dbe venir por post

        echo json_encode($array);
    }


    function agregar($id = FALSE)
    {

        $data["grupos_clie"] = $this->clientes_grupos_model->get_all();

        $data["marcas"] = $this->marcas_model->get_marcas();
        $data["lineas"] = $this->lineas_model->get_lineas();
        $data["familias"] = $this->familias_model->get_familias();
        $data["grupos"] = $this->grupos_model->get_grupos();
        $data["subfamilias"] = $this->subfamilias_model->get_subfamilias();
        $data["subgrupos"] = $this->subgrupos_model->get_subgrupos();
        $data["proveedores"] = $this->proveedor_model->select_all_proveedor();
        $data["impuestos"] = $this->impuestos_model->get_impuestos();
        $data["unidades"] = $this->unidades_model->get_unidades();
        $data['promociones'] = array();
        $data['descuentos'] = array();
        $data["precios"] = $this->precios_model->get_all_by('mostrar_precio', 1);
        $data['columnas'] = $this->columnas;

        // var_dump($data['columnas']);
        $data['precios_producto'] = array();
        if ($id != FALSE) {
            $data['producto'] = $this->producto_model->get_by_id($id);
            $data['promociones'] = $this->bonificaciones_model->get_all_by_condiciones($id);
            $data['descuentos'] = $this->descuentos_model->descuentoProducto('producto_id', $id);
            $data['unidades_producto'] = $this->unidades_model->get_by_producto($id);
            $data['precios_producto'] = array();
            $countunidad = 0;

            $duplicar = $this->input->post('duplicar');
            if (!empty($duplicar)) {
                $data['duplicar'] = 1;
                $data['promociones'] = array();
                $data['descuentos'] = array();
            }
            foreach ($data['unidades_producto'] as $unidad) {

                $precios = $this->precios_model->get_by_unidad_and_producto($id, $unidad['id_unidad']);
                $countprecio = 0;

                if (sizeof($precios) > 0) {
                    foreach ($precios as $precio) {

                        $preciodata[$countprecio] = $precio;

                        $countprecio++;
                    }
                    $data['precios_producto'][$countunidad] = $preciodata;
                }

                $countunidad++;
            }
            //var_dump( $data['precios_producto']);
        }

        $this->load->view('menu/producto/form', $data);


    }


    function verunidades($id = FALSE)
    {

        $data["marcas"] = $this->marcas_model->get_marcas();
        $data["lineas"] = $this->lineas_model->get_lineas();
        $data["familias"] = $this->familias_model->get_familias();
        $data["grupos"] = $this->grupos_model->get_grupos();
        $data["proveedores"] = $this->proveedor_model->select_all_proveedor();
        $data["impuestos"] = $this->impuestos_model->get_impuestos();
        $data["unidades"] = $this->unidades_model->get_unidades();
        $data["precios"] = $this->precios_model->get_all_by('mostrar_precio', 1);
        $data['columnas'] = $this->columnas;

        // var_dump($data['columnas']);
        $data['precios_producto'] = array();
        if ($id != FALSE) {
            $data["grupos_clie"] = $this->clientes_grupos_model->get_all();
            $data['producto'] = $this->producto_model->get_by_id($id);
            $data['promociones'] = $this->bonificaciones_model->get_all_by_condiciones($id);
            $data['descuentos'] = $this->descuentos_model->descuentoProducto('producto_id', $id);
            $data['unidades_producto'] = $this->unidades_model->get_by_producto($id);
            $data['precios_producto'] = array();
            $countunidad = 0;

            $duplicar = $this->input->post('duplicar');
            if (!empty($duplicar)) {
                $data['duplicar'] = 1;
            }
            foreach ($data['unidades_producto'] as $unidad) {

                $precios = $this->precios_model->get_by_unidad_and_producto($id, $unidad['id_unidad']);
                $countprecio = 0;

                if (sizeof($precios) > 0) {
                    foreach ($precios as $precio) {

                        $preciodata[$countprecio] = $precio;

                        $countprecio++;
                    }
                    $data['precios_producto'][$countunidad] = $preciodata;
                }

                $countunidad++;
            }
            //var_dump( $data['precios_producto']);
        }

        $this->load->view('menu/producto/formunidades', $data);


    }


    function consultarStock()
    {
        $data["lstProducto"] = $this->producto_model->select_all_producto();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/mantenimiento/consultarProducto', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function registrar()
    {
        $this->form_validation->set_rules('nombre', 'nombre', 'requiered');
        $this->form_validation->set_rules('stockmin', 'stockmin', 'requiered');


        if ($this->form_validation->run() == false):
            $json['error'] = validation_errors();
        else:

            $id = $this->input->post('id');
            $marca = $this->input->post('producto_marca');
            $linea = $this->input->post('producto_linea');
            $familia = $this->input->post('producto_familia');
            $grupo = $this->input->post('produto_grupo');
            $subfamilia = $this->input->post('producto_subfamilia');
            $subgrupo = $this->input->post('producto_subgrupo');
            $proveedor = $this->input->post('producto_proveedor');
            $impuesto = $this->input->post('producto_impuesto');
            $cualidad = $this->input->post('producto_cualidad');
            $producto_activo = $this->input->post('producto_activo');
            $desccripcion = $this->input->post('producto_descripcion');
            $nota = $this->input->post('producto_nota');

            $codigo_barra = $this->input->post('producto_codigo_barra');
            $producto = array(
                'producto_codigo_barra' => !empty($codigo_barra) ? $codigo_barra : null,
                'producto_nombre' => $this->input->post('producto_nombre'),
                'producto_descripcion' => (empty($desccripcion)) ? null : $desccripcion,
                'producto_marca' => !empty($marca) ? $marca : null,
                'producto_linea' => !empty($linea) ? $linea : null,
                'producto_familia' => !empty($familia) ? $familia : null,
                'produto_grupo' => !empty($grupo) ? $grupo : null,
                'producto_subfamilia' => !empty($subfamilia) ? $subfamilia : null,
                'producto_subgrupo' => !empty($subgrupo) ? $subgrupo : null,
                'producto_proveedor' => !empty($proveedor) ? $proveedor : null,
                'producto_stockminimo' => $this->input->post('producto_stockminimo'),
                'producto_impuesto' => !empty($impuesto) ? $impuesto : null,
                'producto_largo' => $this->input->post('producto_largo'),
                'producto_ancho' => $this->input->post('producto_ancho'),
                'producto_alto' => $this->input->post('producto_alto'),
                'producto_peso' => $this->input->post('producto_peso'),
                'producto_nota' => (empty($nota)) ? null : $nota,
                'presentacion' => $this->input->post('presentacion'),
                'costo_unitario' => $this->input->post('costo_unitario'),
                'producto_cualidad' => !empty($cualidad) ? $cualidad : null,
                'producto_activo' => $producto_activo,
            );

            $medidas = $this->input->post('medida');
            $unidades = $this->input->post('unidad');
            $metrosCubicos = $this->input->post('metros_cubicos');

            if (empty($id)) {
                $rs = $this->producto_model->insertar($producto, $medidas, $unidades, $metrosCubicos);
            } else {
                $producto['producto_id'] = $id;
                $rs = $this->producto_model->update($producto, $medidas, $unidades, $metrosCubicos);
            }
            if ($rs) {
                $json['success'] = 'El producto se ha guardado de forma exitosa';

                $cabeceras = array(

                    'Content-Type: application/json',
                    'x-api-key: ' . $this->session->userdata('api_key'),
                );
                $campos = array('mensaje' => 'Se ha modificado el producto ');
                //MANDO LA NOTIFICACION DE QUE S EHA MODIFICADO EL PRODUCTO
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, URL_CURL_GCM);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $cabeceras);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($campos));
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
                $resultado = curl_exec($ch);
                if (curl_errno($ch) or $resultado == null or $resultado === FALSE or json_decode($resultado) == null) {

                    //  var_dump( array("result" => 'false', 'GCM error: ' => curl_error($ch),  'error'=> $resultado));

                }
                curl_close($ch);
                // echo $resultado;
            } else {
                $json['error'] = 'Ocurrió un error al guardar el producto';
            }

            if ($rs === NOMBRE_EXISTE) {
                $this->session->set_flashdata('error', NOMBRE_EXISTE);
                $json['error'] = NOMBRE_EXISTE;
            }

        endif;

        echo json_encode($json);

    }

    function eliminar()
    {
        $id = $this->input->post('id');
        $product = $this->producto_model->get_by_id($id);
        $nombre = $product['producto_nombre'];

        $producto = array(
            'producto_id' => $id,
            'producto_nombre' => $nombre . time(),
            'producto_estatus' => 0
        );

        $data['resultado'] = $this->producto_model->delete($producto);

        if ($data['resultado'] != FALSE) {
            $json['success'] = 'Se ha eliminado exitosamente';
        } else {
            $json['error'] = 'ha ocurrido un error al eliminar el producto';
        }

        echo json_encode($json);
    }

    function uproducto_modelate()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('producto_id', true);
            $producto = array(
                'var_producto_nombre' => $this->input->post('nombre_udp', true),
                'dec_producto_cantidad' => $this->input->post('stock_udp', true),
                'dec_producto_preciocompra' => $this->input->post('precio_comp_udp', true),
                'dec_producto_precioventa' => $this->input->post('precio_vent_udp', true),
                'dec_producto_utilidad' => $this->input->post('utilidad_udp', true),
                'var_producto_descripcion' => $this->input->post('desc_udp', true),
                'int_producto_unidmed' => $this->input->post('cboUnidMed_udp', true),
                'nCatCodigo' => $this->input->post('cboCategoria_udp', true),
                'var_producto_marca' => $this->input->post('cboTipoTelf_udp', true),
                'var_producto_codproveedor' => $this->input->post('codprodprov_udp', true),
                'dec_producto_stockminimo' => $this->input->post('stockmin_udp', true),
                'var_producto_estado' => '1'
            );
            $rs = $this->producto_model->uproducto_modelate($id, $producto);
            if ($rs) {
                echo "actualizo";
            } else {
                echo "no actualizo";
            }
        } else {
            redirect(base_url() . 'producto/', 'refresh');
        }
    }

    function buscar_id()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id', true);
            $producto_model = $this->producto_model->buscar_id($id);
            echo json_encode($producto_model);
        } else {
            redirect(base_url() . 'producto/', 'refresh');
        }
    }

    function get_by_proveedor()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id', true);
            $producto_model = $this->producto_model->get_all_by('producto_proveedor', $id);
            header('Content-Type: application/json');
            echo json_encode($producto_model);
        } else {
            redirect(base_url() . 'producto/', 'refresh');
        }
    }

    function costos_json($id)
    {

        $data['ventasG'] = $this->venta_model->ventas_grafica($id);
        $data['comprasG'] = $this->venta_model->compras_grafica($id);
        for ($i = 1; $i <= 12; $i++) {
            $c['cant' . $i] = 0;
            $c['cantC' . $i] = 0;
            $result['producto' . $i] = 0;
            $result['productoComp' . $i] = 0;
        }
        foreach ($data['ventasG'] as $ventasG) {
            $anio = date('Y');
            $fecha = date('Y-n-d', strtotime($ventasG['fecha']));
            for ($i = 1; $i <= 12; $i++) {
                if (($fecha >= $anio . '-' . $i . '-01') AND ($fecha <= $anio . '-' . $i . '-31')) {
                    $c['cant' . $i]++;
                    $result['producto' . $i] = $result['producto' . $i] + $ventasG['precio'];

                }

            }

        }
        foreach ($data['comprasG'] as $comprasG) {
            $anio = date('Y');
            $fecha = date('Y-n-d', strtotime($comprasG['fecha_registro']));
            for ($i = 1; $i <= 12; $i++) {
                if (($fecha >= $anio . '-' . $i . '-01') AND ($fecha <= $anio . '-' . $i . '-31')) {
                    $c['cantC' . $i]++;
                    $result['productoComp' . $i] = $result['productoComp' . $i] + $comprasG['precio'];

                }

            }

        }

        $data_grafico = array();
        $ventas = array();
        $compras = array();

        for ($i = 1; $i <= 12; $i++) {
            if ($c['cant' . $i] == 0) {
                $c['cant' . $i] = 1;
                $newData['data'] = array(array(1, 12));
                $newData = array();
                $newData[] = $i;
                $newData[] = intval(0); // ESto es el valor del eje X
                $ventas[] = $newData;
            } else {
                $newData['data'] = array(array(1, 12));
                $newData = array();
                $newData[] = $i;
                $newData[] = intval($result['producto' . $i] / $c['cant' . $i]); // ESto es el valor del eje X
                $ventas[] = $newData;
            }
        }
        $data_grafico['ventas'] = $ventas;

        for ($i = 1; $i <= 12; $i++) {
            if ($c['cantC' . $i] == 0) {
                $c['cantC' . $i] = 1;
                $newData['data'] = array(array(1, 12));
                $newData = array();
                $newData[] = $i;
                $newData[] = intval(0); // ESto es el valor del eje X
                $compras[] = $newData;
            } else {
                $newData['data'] = array(array(1, 12));
                $newData = array();
                $newData[] = $i;
                $newData[] = intval($result['productoComp' . $i] / $c['cantC' . $i]); // ESto es el valor del eje X
                $compras[] = $newData;
            }
        }

        $data_grafico['compras'] = $compras;
        echo json_encode($data_grafico);

    }


    function autocomplete_marca()
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->producto_model->autocomplete_marca($this->input->get('term', true)));
        } else {
            redirect(base_url() . 'producto/', 'refresh');
        }
    }

    function editcolumnas()
    {
        $data['columnas'] = $this->columnas;
        $this->load->view('menu/producto/columnas', $data);
    }


    function listaprecios()
    {

        /* $data['lstProducto'] = $this->producto_model->get_all_by_local_producto($this->session->userdata('id_local'));


         $data['productos'] = $this->unidades_has_precio_model->get_precio_has_producto();*/

        $data['precios'] = $this->precios_model->get_precios();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/producto/listaprecios', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function listaprecios_json()
    {

        $pago = $this->input->get('pago');

        // Pagination Result
        $array = array();
        $array['productosjson'] = array();

        $local = $this->session->userdata('id_local');
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
        if ($pago == 2) {
            $where = 'producto_activo = 1 AND producto_estatus = 1 AND precio > 0  AND unidades_has_precio.id_unidad IS NOT NULL';
        }
        if ($pago == 1) {
            $where = 'producto_activo = 1 AND producto_estatus = 1 AND precio > 0  AND unidades_has_precio.id_unidad IS NOT NULL';
        } elseif ($pago == 0) {
            $where = 'producto_activo = 1 AND producto_estatus = 1 AND precio < 1  OR unidades_has_precio.id_unidad = "" OR unidades_has_precio.id_unidad IS NULL';
        }
        $nombre_or = false;
        $where_or = false;
        $nombre_in = false;
        $where_in = false;
        $select = 'unidades_has_precio.precio,producto.producto_nombre as nombre, producto.*, unidades_has_producto.id_unidad, unidades.nombre_unidad, inventario.id_inventario, inventario.id_local, inventario.cantidad, inventario.fraccion ,lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto,grupos.id_grupo';
        $from = "producto";
        $join = array('unidades_has_precio', 'lineas', 'marcas', 'familia', 'grupos', 'proveedor', 'impuestos', '(SELECT DISTINCT inventario.id_producto, inventario.id_inventario, inventario.cantidad, inventario.fraccion, inventario.id_local FROM inventario WHERE inventario.id_local=' . $local . '  ORDER by id_inventario DESC ) as inventario',
            'unidades_has_producto', 'unidades', 'subgrupo', 'subfamilia');

        $campos_join = array('unidades_has_precio.id_producto=producto.producto_id', 'lineas.id_linea=producto.producto_linea', 'marcas.id_marca=producto.producto_marca',
            'familia.id_familia=producto.producto_familia', 'grupos.id_grupo=producto.produto_grupo',
            'proveedor.id_proveedor=producto.producto_proveedor', 'impuestos.id_impuesto=producto.producto_impuesto', 'inventario.id_producto=producto.producto_id',
            'unidades_has_producto.producto_id=producto.producto_id and unidades_has_producto.orden=1', 'unidades.id_unidad=unidades_has_producto.id_unidad', 'subgrupo.id_subgrupo = producto.producto_subgrupo',
            'subfamilia.id_subfamilia = producto.producto_subfamilia');
        $tipo_join = array('left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left', 'left');


        $search = $this->input->get('search');
        $columns = $this->input->get('columns');
        $buscar = $search['value'];
        $where_custom = false;
        if (!empty($search['value'])) {
            $buscarcod = $buscar;
            if (is_numeric($buscar)) {
                $buscarcod = restCod($buscar);
            }
            $where_custom = "(producto.producto_id LIKE '%" . $buscarcod . "%' or producto.producto_nombre LIKE '%" . $buscar . "%'
            or marcas.nombre_marca LIKE '%" . $buscar . "%' or grupos.nombre_grupo LIKE '%" . $buscar . "%'
            or subgrupo.nombre_subgrupo LIKE '%" . $buscar . "%' or familia.nombre_familia LIKE '%" . $buscar . "%'
            or subfamilia.nombre_subfamilia LIKE '%" . $buscar . "%' or lineas.nombre_linea LIKE '%" . $buscar . "%'
            or producto.presentacion LIKE '%" . $buscar . "%' or unidades.nombre_unidad LIKE '%" . $buscar . "%')";
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
                $order = 'marcas.nombre_marca ';
            }
            if ($ordenar[0]['column'] == 3) {
                $order = 'familia.nombre_familia';
            }
            if ($ordenar[0]['column'] == 4) {
                $order = 'subfamilia.nombre_subfamilia';
            }
            if ($ordenar[0]['column'] == 5) {
                $order = 'lineas.nombre_linea';
            }
            if ($ordenar[0]['column'] == 6) {
                $order = 'producto.presentacion';
            }
            if ($ordenar[0]['column'] == 7) {
                $order = 'unidades.nombre_unidad';
            }

        }

        $group = 'producto_id';


        $lstProducto = $this->producto_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, $group, $order, "RESULT_ARRAY", $limit, $start, $order_dir, false, $where_custom);


        // $lstProducto = $this->producto_model->get_all_by_local_producto($this->session->userdata('id_local'));

        $precios = $this->precios_model->get_precios();
        if ($pago == 2) {
            $productos = $this->unidades_has_precio_model->get_precio_has_producto();
        }
        if ($pago == 1) {
            $condicion = "AND precio > 0  AND unidades_has_precio.id_unidad IS NOT NULL";
            $productos = $this->unidades_has_precio_model->get_precio_has_producto_list($condicion);
        } elseif ($pago == 0) {
            $condicion = "AND precio < 1  OR unidades_has_precio.id_unidad ='' OR unidades_has_precio.id_unidad IS NULL";
            $productos = $this->unidades_has_precio_model->get_precio_has_producto_list($condicion);
        }


        if (count($lstProducto) > 0) {
            foreach ($lstProducto as $row) {
                $PRODUCTOjson = array();

                $PRODUCTOjson[] = sumCod($row['producto_id']);
                $PRODUCTOjson[] = $row['producto_nombre'];
                $PRODUCTOjson[] = $row['nombre_grupo'];


                foreach ($precios as $precio) {

                    $unidades = "";

                    foreach ($productos as $producto) {
                        if ($row['producto_id'] == $producto['id_producto']) {
                            if ($producto['id_precio'] == $precio['id_precio'] and $producto['id_grupo'] == $row['id_grupo']) {

                                $unidades .= $producto['nombre_unidad'] . ": " . number_format($producto['precio'], 2) . " <br>";

                            }
                        }
                    }


                }
                $PRODUCTOjson[] = $unidades;


                $array['productosjson'][] = $PRODUCTOjson;
            }


        }

        $array['data'] = $array['productosjson'];
        $array['draw'] = $draw;//esto debe venir por post
        $array['recordsTotal'] = $total;
        $array['recordsFiltered'] = $total; // esto dbe venir por post
        echo json_encode($array);


    }

    function preciosporproducto()
    {


        $producto = $this->input->post('producto');
        $precio = $this->input->post('precio');

        if ($this->input->is_ajax_request()) {
            echo json_encode($this->precios_model->get_by_precio_and_producto($producto, $precio));
        } else {
            redirect(base_url() . 'producto / ', 'refresh');
        }
    }

    function guardarcolumnas()
    {
        $columnas_id = $this->input->post('columna_id');

        $result = $this->columnas_model->insert($columnas_id);

        if ($result != FALSE) {

            $json['success'] = 'Se ha guardaro la configuracion';

        } else {

            $json['error'] = 'ha ocurrido un error al guardar';
        }

        echo json_encode($json);

    }

    function pdf($valor)
    {


        $lstProducto = $this->producto_model->get_all_by_local_producto($this->session->userdata('id_local'), $valor);
        if ($valor == 1 OR $valor == 2) {
            $condicion = "AND precio > 0  AND unidades_has_precio.id_unidad IS NOT NULL";
            $productos = $this->unidades_has_precio_model->get_precio_has_producto_list($condicion);

        } elseif ($valor == 0) {
            $condicion = "AND precio < 1  OR unidades_has_precio.id_unidad ='' OR unidades_has_precio.id_unidad IS NULL";
            $productos = $this->unidades_has_precio_model->get_precio_has_producto_list($condicion);
        }
        $precios = $this->precios_model->get_precios();
        //  $productos = $this->unidades_has_precio_model->get_precio_has_producto();

        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF - 8', false);

        $pdf->setPageOrientation('L');
        $pdf->SetTitle('Listado de Precios');
        $pdf->SetPrintHeader(false);
        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
        $pdf->setFontSubsetting(false);
        $pdf->SetFont('helvetica', '', 14, '', true);
        $pdf->AddPage();
        $pdf->SetFontSize(8);


        $result['productos'] = $productos;
        $result['precios'] = $precios;
        $result['lstProducto'] = $lstProducto;
        $html = $this->load->view('menu/producto/listapreciospdf', $result, true);

        // creo el pdf con la vista
        $pdf->WriteHTML($html);
        //  $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);
        $nombre_archivo = utf8_decode("ListadoDePrecios.pdf");
        $pdf->Output($nombre_archivo, 'D');
    }

    function excel($valor)
    {
        $lstProducto = $this->producto_model->get_all_by_local_producto($this->session->userdata('id_local'), $valor);
        if ($valor == 1 OR $valor == 2) {
            $condicion = "AND precio > 0  AND unidades_has_precio.id_unidad IS NOT NULL";
            $productoslista = $this->unidades_has_precio_model->get_precio_has_producto_list($condicion);

        } elseif ($valor == 0) {
            $condicion = "AND precio < 1  OR unidades_has_precio.id_unidad ='' OR unidades_has_precio.id_unidad IS NULL";
            $productoslista = $this->unidades_has_precio_model->get_precio_has_producto_list($condicion);
        }
        //$lstProducto = $this->producto_model->get_all_by_local_producto($this->session->userdata('id_local'),$valor = 1);

        $precios = $this->precios_model->get_precios();
        //$productoslista = $this->unidades_has_precio_model->get_precio_has_producto();

        // configuramos las propiedades del documento

        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")

            ->
            setTitle("Listado de Precios")
            ->setSubject("Listado de Precios")
            ->setDescription("Listado de Precios")
            ->setKeywords("Listado de Precios")
            ->setCategory("Listado de Precios");


        $columna[0] = "ID PRODUCTO";
        $columna[1] = "NOMBRE";
        $columna[2] = "GRUPO";
        $r = 3;
        foreach ($precios as $precio) {

            $columna[$r] = $precio['nombre_precio'];
            $r++;
        }
        $var = array();
        $pro = array();

        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {
            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }
        $row = 2;
        $p = 0;

        foreach ($lstProducto as $productos) {
            $col = 0;

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $productos['producto_id']);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $productos['producto_nombre']);

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($col++, $row, $productos['nombre_grupo']);

            $contador = 0;

            foreach ($precios as $precio) {
                $arreglo = array();

                foreach ($productoslista as $producto) {

                    if ($productos['producto_id'] == $producto['id_producto']) {

                        if ($producto['id_precio'] == $precio['id_precio'] and $producto['id_grupo'] == $productos['id_grupo']) {
                            $arreglo[] .= $producto['nombre_unidad'] . ": " . number_format($producto['precio'], 2) . " ";
                        }
                    }
                }
                if (count($arreglo) > 0) {

                    foreach ($arreglo as $arr) {
                        $this->phpexcel->setActiveSheetIndex(0)
                            ->setCellValueByColumnAndRow($col++, $row, $arr);
                    }
                }

            }
            $pro = '';
            $p = 0;
            $row++;
        }
// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Reporte Inventario');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application /vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ListadoDePrecios.xlsx"');
        header('Cache-Control: max-age= 0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    function pdfStock()
    {

        $data['locales'] = $this->local_model->get_all();
        $data["lstProducto"] = $this->producto_model->get_all_by_local($data["locales"][0]["int_local_id"], false);
        $data['columnas'] = $this->columnas;


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
        $pdf->SetFont('helvetica', '', 10, '', true);

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

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>LISTA DE STOCK</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px; font-size:10px;}";
        $html .= "body{font-size:10px;}";
        $html .= "</style>";


        $html .= "<table><tr>";
        foreach ($data['columnas'] as $col) {
            if ($col->mostrar == TRUE) {

                $html .= "<th>" . $col->nombre_mostrar . "</th>";
            }
        }
        $html .= "<th>UM</th>";
        $html .= "<th>Cantidad</th>";
        $html .= "<th>Fracci&oacute;n</th>";
        $html .= "</tr>";
        foreach ($data['lstProducto'] as $pd):
            if ($pd['producto_activo'] == 1) $pd['producto_activo'] = "Activo"; else $pd['producto_activo'] = "Inactivo";

            $html .= "<tr>";
            foreach ($data['columnas'] as $col):
                if (array_key_exists($col->nombre_columna, $pd) and $col->mostrar == TRUE) {
                    $html .= "<td>" . $pd[$col->nombre_join] . "</td>";
                }
            endforeach;
            $html .= "<td>" . $pd['nombre_unidad'] . "</td>";
            $html .= "<td>" . $pd['cantidad'] . "</td>";
            $html .= "<td>" . $pd['fraccion'] . "</td>";
            $html .= "</tr>";

        endforeach;
        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este método tiene varias opciones, consulte la documentación para más información.
        $nombre_archivo = utf8_decode("ListaDeStock.pdf");
        $pdf->Output($nombre_archivo, 'D');


    }

    function excelStock()
    {
        $data['locales'] = $this->local_model->get_all();
        $data["lstProducto"] = $this->producto_model->get_all_by_local($data["locales"][0]["int_local_id"], false);
        $data['columnas'] = $this->columnas;


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

        // configuramos las propiedades del documento
        $this->phpexcel->getProperties()
            //->setCreator("Arkos Noem Arenom")
            //->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Stock")
            ->setSubject("Stock")
            ->setDescription("Stock")
            ->setKeywords("Stock")
            ->setCategory("Stock");

        // Columnas de A a Z 26 elementos Maximo    
        $columnas = range("A","Z");

        // Configuraci´on de Elementos Titulo
        for($i = 'A'; $i <= 'M'; $i++){
            if($i == 'A' || $i == 'C' || $i == 'E'){   
            $this->phpexcel->getActiveSheet()->getStyle($i)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            }
            $this->phpexcel->getActiveSheet()->getColumnDimension($i)->setAutoSize('true');
        }
        
        $this->phpexcel->getActiveSheet()->getStyle('A2:M2')->applyFromArray($estiloTituloReporte);
        // Configuraci´on de Elementos

        // Llenado de Titulo
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(0, 1,'ListaStock');
        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow(1, 1, 'Fecha '.date('d-m-Y h:m:s'));


        $data['columnas_new'][] = ['nombre_columna' => 'nombre_unidad','nombre_mostrar' => 'UM','mostrar' => 1];
        $data['columnas_new'][] = ['nombre_columna' => 'cantidad','nombre_mostrar' => 'Cantidad','mostrar' => 1];
        $data['columnas_new'][] = ['nombre_columna' => 'fraccion','nombre_mostrar' => 'Fraccion','mostrar' => 1];
        $data['columnas_new'][] = ['nombre_columna' => 'activo','nombre_mostrar' => 'Fraccion','mostrar' => 1];

        $columShow = ['producto_id','producto_nombre','producto_marca','produto_grupo','producto_subgrupo','producto_familia','producto_subfamilia','producto_linea','presentacion','nombre_unidad','cantidad','fraccion','producto_activo'];
        $columnasNomalizadas = [];

        foreach ($data['columnas_new'] as $col) {
            $tmp = new stdClass;
            $tmp->nombre_columna = $col['nombre_columna'];
            $tmp->nombre_mostrar = $col['nombre_mostrar'];
            $tmp->mostrar = $col['mostrar'];
            array_push($data['columnas'], $tmp);
        }

        $c = 0;
        // Nuevo recorrido de elementos
        foreach ($columShow as $key2 => $value) {
            foreach ($data['columnas'] as $key => $col) {
                if ($col->mostrar == TRUE ) {
                    if($col->nombre_columna == $value){
                        if($col->nombre_mostrar == "Sub Grupo"){
                            $nombre_mostrar = 'Linea';
                        }elseif($col->nombre_mostrar == 'Familia') {
                            $nombre_mostrar = 'Sub Linea';
                        }elseif($col->nombre_mostrar == 'Linea') {
                            $nombre_mostrar = 'Talla';
                        }else{
                            $nombre_mostrar = $col->nombre_mostrar;
                        }
                        array_push($columnasNomalizadas, $col);
                        $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($c,2,$nombre_mostrar);
                    $c++;
                    }
                }
            }
        }

        $co = 0;
        $row = 3;
        // Llenado de Titulo

        // Llenado de Elementos
        $c = 0;
        foreach ($data['lstProducto'] as $pd):
            if ($pd['producto_activo'] == 1) $pd['producto_activo'] = "Activo"; else $pd['producto_activo'] = "Inactivo";
            foreach ($columnasNomalizadas as $coll) {
                if (array_key_exists($coll->nombre_columna, $pd) and $coll->mostrar == TRUE)
                {
                    $value = isset($coll->nombre_join) ? $coll->nombre_join : $coll->nombre_columna;
                    $this->phpexcel->setActiveSheetIndex(0)->setCellValueByColumnAndRow($co++, $row, ucwords(strtolower($pd[$value])));

                    // Se setean los valores del Para el Estilo de la columnas
                    $this->phpexcel->getActiveSheet()->getStyle($columnas[$c].$row)->applyFromArray($estiloInformacion);

                    $c++;
                }
            }

            $this->phpexcel->getActiveSheet()->getStyle($columnas[$c].$row)->applyFromArray($estiloInformacion);


            $row++;
            $co = 0;
            $c = 0; //Reinicio las columnas
        endforeach;
        // Llenado de Elementos


// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Lista Stock');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="ListaStock.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

}
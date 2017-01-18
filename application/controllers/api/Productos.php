<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class productos extends REST_Controller
{
    private $columnas = array();

    protected $uid = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('producto/producto_model');
        $this->load->model('marca/marcas_model');
        $this->load->model('linea/lineas_model');
        $this->load->model('familia/familias_model');
        $this->load->model('grupos/grupos_model');
        $this->load->model('proveedor/proveedor_model');
        $this->load->model('impuesto/impuestos_model');
        $this->load->model('precio/precios_model');
        $this->load->model('escalas/escalas_model', 'escalas');
        $this->load->model('bonificaciones/bonificaciones_model', 'bonificacion');

        $this->load->model('unidades/unidades_model');

        $this->load->model('precio/precios_model');
        $this->load->model('local/local_model');

        $this->load->model('unidades/unidades_model');

        $this->load->library('form_validation');

        //$this->columnas = $this->columnas_model->get_by('tabla', 'producto');

        $this->load->model('api/api_model', 'api');
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
        $datas = array();

        $local = $this->get('local');


        if (!empty($local)) {
            $productos = $this->producto_model->get_all_by_local($local, true, false);
            foreach ($productos as $producto) {
                $producto['producto_id_cero'] = sumCod($producto['producto_id']);
                $producto['producto_id'] = $producto['producto_id'];
                $producto['precios'] = $this->precios_model->get_all_by_producto($producto['producto_id']);

                if (sizeof($producto['precios']) > 0) {

                    $bonificaciones = $this->bonificacion->get_all_by_condiciones($producto['producto_id']);
                    foreach ($bonificaciones as $bono) {

                        $bono['bonificaciones_has_producto'] = $this->bonificacion->bonificaciones_has_producto('id_bonificacion', $bono['id_bonificacion']);

                        $producto['bonificaciones'][] = $bono;
                    }

                    $producto['escalas'] = $this->escalas->get_by('producto', $producto['producto_id'], true);

                    $unidades = $this->unidades_model->get_by_producto($producto['producto_id']);
                    $producto['existencia'] = 0;
                    if (isset($unidades[0])) {
                        $maxima_unidades = $unidades[0]['unidades'];
                        $cantidad_total = ($producto['cantidad'] * $maxima_unidades); //+ $producto['fraccion'];
                        $producto['existencia'] = $cantidad_total;
                    }
                    $datas['productos'][] = $producto;

                }

            }
        } else {
            $productos = $this->producto_model->select_all_producto();

            foreach ($productos as $producto) {
                $producto['producto_id'] = $producto['producto_id'];
                $producto['producto_id_cero'] = sumCod($producto['producto_id']);
                $datas['productos'][] = $producto;
            }
        }

        if ($datas) {
            $this->response($datas, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Show
    public function existencia_get()
    {
        $local = $this->get('local');
        $unidad = $this->get('unidad');
        $cantidad_get = $this->get('cantidad');
        $producto = $this->get('producto');
        if (empty($local)) {
            $this->response(array(), 200);
        }
        $id = false;
        if (!empty($producto)) {
            $id = $producto;
        }
        $data = array();
        $data['productos'] = $this->producto_model->get_all_by_local($local, true, $id);
        foreach ( $data['productos'] as $producto) {
            $producto['producto_id'] = $producto['producto_id'];
            $producto['producto_id_cero'] = sumCod($producto['producto_id']);
            $datas['productos'][] = $producto;
        }
        $cantidad_comparar = null;
        $cantidad_total = null;
        $get_unidades = null;
        if (!empty($producto) && !empty($data['productos'])) {

            $unidades = $this->unidades_model->get_by_producto($id);

            $unidad_maxima = $unidades[0]['nombre_unidad'];
            $maxima_unidades = $unidades[0]['unidades'];
            $unidad_minima = $unidades[sizeof($unidades) - 1]['nombre_unidad'];
            $cantidad = $data['productos'][0]['cantidad'];
            $fraccion = $data['productos'][0]['fraccion'];

            $cantidad_total = ($data['productos'][0]['cantidad'] * $maxima_unidades) + $data['productos'][0]['fraccion'];

            foreach ($unidades as $u) {
                if ($u['id_unidad'] == $unidad)
                    $get_unidades = $u['unidades'];
            }
            $cantidad_comparar = ($cantidad_get * $get_unidades);


        }

        if (empty($producto)) {
            if ($data) {
                $this->response($data, 200);
            } else {
                $this->response(array(), 200);
            }
        } else {
            if ($cantidad_comparar <= $cantidad_total) {
                $this->response(true, 200);
            } else {
                $this->response(false, 200);
            }
        }
    }

    // Save
    public function create_post()
    {
        $this->form_validation->set_rules('nombre', '', 'required|trim|xss_clean');

        if ($this->form_validation->run() === false) {
            $this->response(array('status' => 'failed', 'errors' => validation_errors()), 400);
        } else {
            $post = $this->input->post(null, true);
            if (!empty($post)) {
                $save = $this->metodos_pago_model->insertar($post);
                if ($save === false) {
                    $this->response(array('status' => 'failed'));
                } else {
                    $this->response(array('status' => 'success'));
                }
            }
        }
    }

    // Update
    public function update_post()
    {
        $this->form_validation->set_rules('nombre', '', 'required|trim|xss_clean');

        if ($this->form_validation->run() === false) {
            $this->response(array('status' => 'failed', 'errors' => validation_errors()), 400);
        } else {
            $post = $this->input->post(null, true);
            if (!empty($post)) {
                //$post['id'] = $id;
                $update = $this->metodos_pago_model->update($post);
                if ($update === false) {
                    $this->response(array('status' => 'failed'));
                } else {
                    $this->response(array('status' => 'success'));
                }
            }
        }
    }
}
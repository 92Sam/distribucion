<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class precios extends REST_Controller
{
    protected $uid = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('precio/precios_model');
        $this->load->model('unidades_has_precio/unidades_has_precio_model', 'unidadPrecio');

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

        if (!empty($auth_id)) {
            $this->uid = $auth_id;
        } else {
            $this->uid = null;
        }
    }

    // All
    public function index_get()
    {
        $data = array();
        $data['precios'] = $this->precios_model->get_precios();

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Show
    public function unidad_get()
    {
        $id_unidad = $this->get('id_unidad');
        $id_producto = $this->get('id_producto');

        if (empty($id_unidad) || empty($id_producto)) {
            $this->response(array(), 200);
        }

        $data = array();
        $data['unidad_precio'] = $this->unidadPrecio->get_all_by($id_unidad, $id_producto);

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Show
    public function ver_get()
    {
        $id = $this->get('id');
        if (empty($id)) {
            $this->response(array(), 200);
        }

        $data = $this->precios_model->get_by('id_precio', $id);

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Save
    public function create_post()
    {
        //
    }

    // Update
    public function update_post()
    {
        //
    }
}
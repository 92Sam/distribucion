<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class grupos_clientes extends REST_Controller
{
    protected $uid = null;

    function __construct()
    {
        parent::__construct();
        $this->load->model('clientesgrupos/clientes_grupos_model');
        $this->load->model('api/api_model', 'api');

        $this->load->library('form_validation');
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
        $datas['grupos_clientes'] = $this->clientes_grupos_model->get_all();

        if ($datas) {
            $this->response($datas, 200);
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

        $data = $this->cliente_model->get_by('id_cliente', $id);

        if ($data) {
            $array = array();
            $array['id_cliente'] = $data['id_cliente'];
            $array['nombre'] = $data['razon_social'];
            $array['linea_credito'] = null;
            $array['saldo_actual'] = 0;
            $array['cantidad_aprobados'] = 0;
            $array['cantidad_rechazado'] = 0;
            $this->response($array, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Save
    public function create_get()
    {
        //
    }

    // Update
    public function update_get()
    {
        //
    }
}
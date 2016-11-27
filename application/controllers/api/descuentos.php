<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class descuentos extends REST_Controller
{
    protected $uid = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('descuentos/descuentos_model', 'descuentos');
        $this->load->model('escalas/escalas_model', 'escalas');

        $this->load->library('form_validation');

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
        $datas['descuentos'] = $this->descuentos->get_all();
        $descuentos = array();
        foreach ($datas['descuentos'] as $descuento) {
            $descuento['escalas'] = $this->escalas->get_by('escalas.regla_descuento', $descuento['descuento_id'], true);
            $descuentos[] = $descuento;
        }
        $datas['descuentos'] = $descuentos;
        if ($datas) {
            $this->response($datas, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Show
    public function ver_get()
    {
        //
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
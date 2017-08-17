<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class zonas extends REST_Controller
{
    protected $uid = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('zona/zona_model');
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
		$data['zonas'] = $this->zona_model->get_all();

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

        $data = array();
		$data['zonas'] = $this->zona_model->get_by('zona_id', $id);

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    public function ver_by_user_get() {

        $id = $this->get('id');
        $dia = $this->get('dia');
        //$dia = '1';
        $data = array();
        $valor = array();
        $result = array();

        if (empty($id)) {
            $zonas = $this->zona_model->get_all();

            foreach($zonas as $z) {
                $valor['zona_id'] = $z['zona_id'];
                $valor['zona_nombre'] = $z['zona_nombre'];
                $valor['ciudad_id'] = $z['ciudad_id'];
                $valor['today'] = 0;

                $result[] = $valor;
            }

        } else {
            $zonas = $this->zona_model->get_all_by_user($id);

            foreach($zonas as $z) {
                $valor['zona_id'] = $z['zona_id'];
                $valor['zona_nombre'] = $z['zona_nombre'];
                $valor['ciudad_id'] = $z['ciudad_id'];

                $id_zona = $z['zona_id'];
                $today = $this->zona_model->get_zona_hoy($id, $id_zona, $dia);

                if ($today) {
                    $valor['today'] = "1";
                } else {
                    $valor['today'] = "0";
                }

                $result[] = $valor;
            }
        }

        $data['zonas'] = $result;

        if ($data) {
            $this->response($data, 200);
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
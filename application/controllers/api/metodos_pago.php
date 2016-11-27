<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class metodos_pago extends REST_Controller
{
    protected $uid = null;

    protected $methods = array(
        'index_get' => array('level' => 0),
        'ver_get' => array('level' => 0, 'limit' => 10),
        'create_get' => array('level' => 1),
        'update_get' => array('level' => 1, 'limit' => 5),
    );

    function __construct()
    {
        parent::__construct();

        $this->load->model('metodosdepago/metodos_pago_model', 'pagos');
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
        $datas['metodos_pagos'] = $this->pagos->get_all();

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

        $data = array();
        $data['pagos'] = $this->pagos->get_by('id_metodo', $id);

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
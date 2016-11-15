<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class gcm_users extends REST_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('gcm/gcm_users_model', 'gcm_users_model');
        $this->load->model('api/api_model', 'apiModel');
        $this->load->library('user_agent');
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

    public function index_get()
    {
        $result['usuarios'] = $this->gcm_users_model->getAll();
        if ($result)
            $this->response($result, 200);
        else
            $this->response(array(), 200);
    }

    public function get_get()
    {
        $username = $get = $this->input->get('usuario', true);
        $result['usuario'] = $this->gcm_users_model->getUser($username);
        if (!empty($result))
            $this->response($result, 200);
        else
            $this->response(array(), 200);
    }


    public function save_get()
    {
        $get = $this->input->get(null, true);
        $response = array("success" => "0");


        $result = $this->gcm_users_model->saveUser($get);
        if ($result != false)
            $response["success"] = "1";

        $this->response($response, 200);
    }


}
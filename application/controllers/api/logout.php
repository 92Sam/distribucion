<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class logout extends REST_Controller
{
	function __construct()
    {
        parent::__construct();
        $this->load->model('api/api_model', 'api');
    }
	
	// All
    public function index_get()
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

        // Auth
        $auth = $this->api->removeAuth($key);
		
		// Remove
		if ($auth == true) {
            $this->response(array('status' => 'success'), 200);
        } else {
            $this->response(array('status' => 'failed'), 400);
        }
    }
}
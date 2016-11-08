<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class escalas extends REST_Controller
{
    protected $uid = null;
	
	function __construct()
    {
        parent::__construct();
        
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
		$datas['escalas'] = $this->escalas->get_all();
        
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
        $data['escalas'] = $this->escalas->get_by('producto', $id, true);
		
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
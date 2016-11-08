<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class consolidado_carga extends REST_Controller
{
    protected $uid = null;

    protected $methods = array(
        'index_get' => array('level' => 0),
        'ver_get' => array('level' => 0),
        'create_get' => array('level' => 0),
        'update_get' => array('level' => 0),
    );

    function __construct()
    {
        parent::__construct();

        $this->load->model('consolidadodecargas/consolidado_model', 'consolidado_model');

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

        $post= $this->input->get();

        $fecha_desde=null;
        $fecha_hasta=null;
        $datas = array();
        $where = 'consolidado_id is not null ';
        //$where = array('status' => 'IMPRESO');
        if (!empty($post['fecha_ini'])) {

            $where = $where . " AND date(consolidado_carga.fecha) >= '" . date('Y-m-d', strtotime($post['fecha_ini'])) . "'";
        }
        if (!empty($post['fecha_fin'])) {

            $where = $where . " AND  date(consolidado_carga.fecha) <= '" . date('Y-m-d', strtotime($post['fecha_fin'])) . "'";
        }



        if (!empty($transportista)) {
            $transportista = $post['transportista'];
            $where = $where." and id_trabajadores =".$transportista;
        }

        $consolidados = $this->consolidado_model->get_consolidado_by($where);
        foreach ($consolidados as $consolidado) {
            //$where = array('consolidado_id' => $consolidado['consolidado_id'], 'venta.venta_status' => PEDIDO_ENVIADO);
            $where = array('consolidado_id' => $consolidado['consolidado_id']);
            $consolidado['pedidos'] = $this->consolidado_model->get_details_by($where);
            $datas['consolidados'][] = $consolidado;

        }


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
        $id = $this->input->get('id');
        if ($id == FALSE) {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }
        $estatus = $this->input->get('status');
        $cerrar = $this->consolidado_model->cambiarEstatus($id, $estatus);

        if ($cerrar) {
            $this->response(array('status' => 'success'));
        } else {
            $this->response(array('status' => 'failed'));
        }

    }
}
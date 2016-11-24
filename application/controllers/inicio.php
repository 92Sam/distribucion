<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class inicio extends MY_Controller
{
    function __construct()
    {
        parent::__construct();
        $this->load->model('login/login_model', 'login');
        $this->load->model('local/local_model', 'local');
        $this->load->model('opciones/opciones_model');

        $this->load->model('api/api_model', 'apiModel');
        $this->load->library('session');
    }
    function very_sesion()
    {

        $ver=$this->login->very_session();
        if($ver==false){
            echo json_encode(false);





        }else {
            echo json_encode($ver);
        }
    }


    function renew_sesion()
    {
        $this->login->refresh_session();

    }
    public function index()
    {
        $data['lstLocal'] = $this->local->get_all();
        $this->load->view('login', $data);
    }

    public function validarTema()
    {
        $ruta = array('tema' => $this->input->post('ruta'));
        $this->session->set_userdata($ruta);
        echo json_encode($ruta);
    }

    function validar_login()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('user', 'user', 'requiered');
            $this->form_validation->set_rules('pw', 'pw', 'requiered');
            if ($this->form_validation->run() == false) {
                echo validation_errors();
            } else {
                $password = md5($this->input->post('pw', true));
                $data = array(
                    'username' => $this->input->post('user', true),
                    'password' => $password
                );

                // Auth
                $auth = $this->login->verificar_usuario($data);
                if ($auth) {
                    $data = array();
                    $this->session->set_userdata($auth);
                    $configuraciones = $this->opciones_model->get_opciones();
                    if ($configuraciones == TRUE) {
                        foreach ($configuraciones as $configuracion) {
                            $index = $configuracion['config_key'];
                            $data[$index] = $configuracion['config_value'];
                        }
                    }

                    // Nuevo Api Key
                    $data['api_key'] = $this->apiModel->new_api_key($auth['nUsuCodigo'], $level = false, $ignore_limits = false, $is_private_key = false, $ip_addresses = '');

                    // Session Data
                    $this->session->set_userdata($data);
                    //$this->session->set_userdata('id_local', $this->input->post('cboTienda', true));
                    echo "ok";
                } else {
                    echo "no ok";
                }
            }
        } else {
            echo json_encode(array('status' => 'failed', 'paraments' => 'Nombre de usuario o Contraseña invalida.'));
        }
    }
}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class usuario extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('usuario/usuario_model');
        $this->load->model('usuariosgrupos/usuarios_grupos_model');
        $this->load->model('local/local_model');
        $this->load->model('zona/zona_model');
        //$this->very_sesion();
    }

    /* function very_sesion()
   {
       if (!$this->session->userdata('nUsuCodigo')) {
           redirect(base_url() . 'inicio');
       }
   }*/

    function index()
    {
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data["lstUsuario"] = $this->usuario_model->select_all_user();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/usuario/usuario', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function get_by_usuario(){

        if ($this->input->is_ajax_request()) {
            $zona_id = $this->input->post('zona_id');

            $zonas = $this->usuario_model->get_by_form('id_zona', $zona_id);

            echo json_encode($zonas);
        } else {
            redirect(base_url . 'principal');
        }
    }
    function form($id = FALSE)
    {

        $data = array();
        $data['grupos'] = $this->usuarios_grupos_model->get_all();
        $data['locales'] = $this->local_model->get_all();
        if ($id != FALSE) {
            $data['usuario'] = $this->usuario_model->buscar_id($id);
            $data['usuario_has_zona'] = $this->zona_model->get_all_by_user($id);
        }
        $data['zonas'] = $this->zona_model->get_all();
        $data['zona_dias'] = $this->zona_model->get_all_dias();

        $this->load->view('menu/usuario/form', $data);
    }

    function zonas($id)
    {

        $data = array();
        $data['usuario_has_zona'] = $this->zona_model->get_all_by_user($id);
        $data['zonas'] = $this->zona_model->get_all();
        $data['zona_dias'] = $this->zona_model->get_all_dias();
        $this->load->view('menu/usuario/zonas', $data);
    }

    function guardarsession()
    {

        $resultado = false;
        $password = $this->input->post('var_usuario_clave');
        $id = $this->input->post('nUsuCodigo');
        $usuario = array(
            'nombre' => $this->input->post('nombre', true),
            // 'admin' => !empty($admin) ? true : false,

        );
        $username = $this->input->post('username');
        $smovil = $this->input->post('smovil');
        $local = $this->input->post('id_local', true);
        $grupo = $this->input->post('grupo', true);
        $genero = $this->input->post('genero', true);
        $sueldo = $this->input->post('sueldo', true);
        $latitud = $this->input->post('latitud', true);
        $longitud = $this->input->post('longitud', true);

        $obser = $this->input->post('obser', true);
        $fent = $this->input->post('fent', true);
        $fnac = $this->input->post('fnac', true);

        $identificacion = $this->input->post('identificacion', true);
        $activo = !empty($activo) ? true : false;
        $smovil = !empty($smovil) ? true : false;

        if (!empty($local))
            $usuario['id_local'] = $local;
        if (!empty($grupo))
            $usuario['grupo'] = $grupo;
        if (!empty($identificacion))
            $usuario['identificacion'] = $identificacion;
        if (!empty($genero))
            $usuario['genero'] = $genero;
        if (!empty($sueldo))
            $usuario['sueldo'] = $sueldo;
        if (!empty($smovil))
            $usuario['smovil'] = $smovil;
        if (!empty($sueldo))
            $usuario['sueldo'] = $sueldo;
        if (!empty($latitud))
            $usuario['latitud'] = $latitud;
        if (!empty($longitud))
            $usuario['longitud'] = $longitud;

        if (!empty($fnac))
            $usuario['fnac'] = $fnac;
        if (!empty($fent))
            $usuario['fent'] = $fent;
        if (!empty($obser))
            $usuario['obser'] = $obser;
        if ($username != '') {
            $usuario['username'] = $username;
        } else {
            $usuario['username'] = null;
        }

        if (!empty($password)) {
            $usuario['var_usuario_clave'] = md5($password);
        }


        $usuario['nUsuCodigo'] = $id;
        $resultado = $this->usuario_model->update($usuario);


///var_dump($data);


        $this->session->set_userdata($usuario);


        if ($resultado != FALSE) {
            if ($resultado === USERNAME_EXISTE) {

                $json['error'] = USERNAME_EXISTE;
            } else {
                $json['success'] = 'Solicitud Procesada con exito';
            }

        } else {

            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }


        echo json_encode($json);


    }


    function registrar()
    {

        $password = $this->input->post('var_usuario_clave');
        $activo = $this->input->post('activo');
        $smovil = $this->input->post('smovil');
        $admin = $this->input->post('admin');
        $id = $this->input->post('nUsuCodigo');
        $iduhz = $this->input->post('iduz');
        $usuario = array(
            'username' => $this->input->post('username', true),

            'nombre' => $this->input->post('nombre', true),

        );

        $local = $this->input->post('id_local', true);
        $grupo = $this->input->post('grupo', true);
        $genero = $this->input->post('genero', true);
        $sueldo = $this->input->post('sueldo', true);
        $latitud = $this->input->post('latitud', true);
        $longitud = $this->input->post('longitud', true);
        $identificacion = $this->input->post('identificacion', true);
        $obser = $this->input->post('obser', true);
        $fent = date('Y-m-d', strtotime($this->input->post('fent', true)));
        $fnac = date('Y-m-d', strtotime($this->input->post('fnac', true)));

        $activo = !empty($activo) ? true : 0;
        $smovil = !empty($smovil) ? true : 0;
        $admin = !empty($admin) ? true : 0;

        if (!empty($fnac))
            $usuario['fnac'] = $fnac;
        if (!empty($fent))
            $usuario['fent'] = $fent;
        if (!empty($obser))
            $usuario['obser'] = $obser;
        if (!empty($local))
            $usuario['id_local'] = $local;
        if (!empty($grupo))
            $usuario['grupo'] = $grupo;
        if (!empty($identificacion))
            $usuario['identificacion'] = $identificacion;

        $usuario['activo'] = $activo;

        $usuario['admin'] = $admin;
        if (!empty($genero))
            $usuario['genero'] = $genero;
        if (!empty($sueldo))
            $usuario['sueldo'] = $sueldo;

        $usuario['smovil'] = $smovil;
        if (!empty($sueldo))
            $usuario['sueldo'] = $sueldo;
        if (!empty($latitud))
            $usuario['latitud'] = $latitud;
        if (!empty($longitud))
            $usuario['longitud'] = $longitud;


        if (!empty($password)) {
            $usuario['var_usuario_clave'] = md5($password);
        }

        if (empty($id)) {
            $resultado = $this->usuario_model->insertar($usuario);

        } else {

            $usuario['nUsuCodigo'] = $id;
            $resultado = $this->usuario_model->update($usuario);
        }


        if ($resultado != FALSE) {
            if ($resultado === USERNAME_EXISTE) {

                $json['error'] = USERNAME_EXISTE;
            } else {
                $json['success'] = 'Solicitud Procesada con exito';
            }

        } else {

            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }


        echo json_encode($json);


    }

    function buscar_id()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id', true);
            $pd = $this->usu->buscar_id($id);
            echo json_encode($pd);
        } else {
            redirect(base_url() . 'usuario/', 'refresh');
        }
    }

    function buscar_lstlocal_id()
    {
        if ($this->input->is_ajax_request()) {
            $id = $this->input->post('id', true);
            $pd = $this->usu->select_lista_local($id);
            echo json_encode($pd);
        } else {
            redirect(base_url() . 'usuario/', 'refresh');
        }
    }

    function eliminar()
    {


        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $grupo = array(
            'nUsuCodigo' => $id,
            'username' => $nombre . time(),
            'deleted' => 1
        );

        $data['resultado'] = $this->usuario_model->update($grupo);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha Eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el impuesto';
        }

        echo json_encode($json);
    }

}

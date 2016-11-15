<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class local extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('local/local_model');
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
        //$data="";

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data["locales"] = $this->local_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/local/local', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        if ($id != FALSE) {
            $data['local'] = $this->local_model->get_by('int_local_id', $id);
        }
        $this->load->view('menu/local/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $local = array(
            'local_nombre' => $this->input->post('local_nombre'),
        );

        if (empty($id)) {
            $resultado = $this->local_model->insertar($local);

        } else {
            $local['int_local_id'] = $id;
            $resultado = $this->local_model->update($local);
        }

        if ($resultado == TRUE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] ='Ha ocurrido un error al procesar la solicitud';
        }
        if($resultado===NOMBRE_EXISTE){
            //  $this->session->set_flashdata('error', NOMBRE_EXISTE);
            $json['error']= NOMBRE_EXISTE;
        }

      echo json_encode($json);

    }

    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $local = array(
            'int_local_id' => $id,
            'local_nombre' => $nombre . time(),
            'local_status' => 0

        );

        $data['resultado'] = $this->local_model->update($local);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el local';
        }

        echo json_encode($json);
    }


}
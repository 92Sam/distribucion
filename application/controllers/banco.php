<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class banco extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('banco/banco_model');
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

        $data["banco"] = $this->banco_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/banco/banco', $data, true);
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
            $data['banco'] = $this->banco_model->get_by('banco_id', $id);
        }
        $this->load->view('menu/banco/form', $data);
    }

  
    function guardar()
    {

        $id = $this->input->post('id');

        $banco = array(
            'banco_nombre' => $this->input->post('nombre'),
            'banco_numero_cuenta' => $this->input->post('nro_cuenta'),
            'banco_saldo' => $this->input->post('saldo'),
            'banco_cuenta_contable' => $this->input->post('cuenta_contable'),
            'banco_titular' => $this->input->post('titular'),
            'banco_status' => 1,
        );

        if (empty($id)) {
            $resultado = $this->banco_model->insertar($banco);

        } else {
            $banco['banco_id'] = $id;
            $resultado = $this->banco_model->update($banco);
        }

        if ($resultado == TRUE) {
            $json['success']= 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la Solicitud';
        }

        echo json_encode($json);

    }


    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $banco = array(
            'banco_id' => $id,
            'banco_status' => 0

        );

        $data['resultado'] = $this->banco_model->update($banco);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el Banco';
        }

        echo json_encode($json);
    }

}
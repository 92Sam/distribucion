<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class pais extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('pais/pais_model');
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

        $data["paises"] = $this->pais_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/pais/pais', $data, true);

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
            $data['pais'] = $this->pais_model->get_by('id_pais', $id);
        }
        $this->load->view('menu/pais/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $pais = array(
            'nombre_pais' => $this->input->post('nombre_pais'),
        );

        if (empty($id)) {
            $resultado = $this->pais_model->insertar($pais);

        } else {
            $pais['id_pais'] = $id;
            $resultado = $this->pais_model->update($pais);
        }

        if ($resultado == TRUE) {
            $json['success']= 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);

    }


}
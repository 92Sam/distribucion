<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class estados extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('estado/estado_model');
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

              $data["estados"] = $this->estado_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/estado/estado', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        $data['estados'] = array();
        $data['paises'] = $this->pais_model->get_all();
        if ($id != FALSE) {
            $data['estado'] = $this->estado_model->get_by('estados_id', $id);
        }
        $this->load->view('menu/estado/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $estado = array(
            'pais_id' => $this->input->post('pais_id'),
            'estados_nombre' => $this->input->post('estados_nombre'),
        );

        if (empty($id)) {
            $resultado = $this->estado_model->insertar($estado);

        } else {
            $estado['estados_id'] = $id;
            $resultado = $this->estado_model->update($estado);
        }

        if ($resultado == TRUE) {
            $json['success']= 'Solicitud Procesada con exito';
        } else {
            $json['error']= 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);

    }


    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $metodospago = array(
            'id_metodo' => $id,
            'nombre_metodo' => $nombre . time(),
            'status_metodo' => 0

        );

        $data['resultado'] = $this->metodos_pago_model->update($metodospago);

        if ($data['resultado'] != FALSE) {

            $this->session->set_flashdata('success', 'Se ha eliminado exitosamente');


        } else {

            $this->session->set_flashdata('error', 'Ha ocurrido un error al eliminar el M&eacute;todos');
        }

        redirect('metodosdepago');
    }

    function get_by_pais()
    {
        if ($this->input->is_ajax_request()) {
            $pais_id = $this->input->post('pais_id');

            $estados = $this->estado_model->get_all_by('pais_id', $pais_id);
            header('Content-Type: application/json');
            echo json_encode($estados);
        } else {
            redirect(base_url . 'principal');
        }
    }


}

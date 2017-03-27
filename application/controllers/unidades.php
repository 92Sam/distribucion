<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class unidades extends MY_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->model('caja/caja_model','c');
        $this->load->model('unidades/unidades_model');
        $this->load->helper('form');

        //$this->very_sesion();
    }

    /* function very_sesion()
    {
        if (!$this->session->userdata('nUsuCodigo')) {
            redirect(base_url() . 'inicio');
        }
    }*/
    function index(){
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }
        $data['lstMovimiento'] = array();
        $data['unidades']=$this->unidades_model->get_unidades();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/unidades/unidades',$data, true);
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
            $data['unidad'] = $this->unidades_model->get_by('id_unidad', $id);
        }


        $this->load->view('menu/unidades/form', $data);
    }

    function guardar()
    {


        $id = $this->input->post('id');
        $grupo = array(
            'nombre_unidad' => $this->input->post('nombre'),
            'abreviatura' => $this->input->post('abreviatura')
        );
        if (empty($id)) {
            $data['resultado'] = $this->unidades_model->set_unidades($grupo);
        } else {
            $grupo['id_unidad'] = $id;
            $data['resultado'] = $this->unidades_model->update_unidades($grupo);
        }

        if ($data['resultado'] != FALSE) {

            $json['success']= 'Solicitud Procesada con exito';

        } else {

            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        if($data['resultado']===NOMBRE_EXISTE){
            //  $this->session->set_flashdata('error', NOMBRE_EXISTE);
            $json['error']= NOMBRE_EXISTE;
        }
        echo json_encode($json);


    }

    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        //Primero verificaremos que la unidad de medida no este asociado a ningun producto vigente
        $resultado = $this->unidades_model->consultarUnidad($id);

        if ($resultado == false) {
                $grupo = array(
                'id_unidad' => $id,
                'nombre_unidad' => $nombre . time(),
                'estatus_unidad' => 0
            );
            $data['resultado'] = $this->unidades_model->update_unidades($grupo);

            if ($data['resultado'] != FALSE) {

                $json['success'] = 'Se ha eliminado la unidad';

            } else {

                $json['error']= 'Ha ocurrido un error al eliminar la unidad ';
            }
        }
        else
        {
            $json['error']= 'La unidad de medida que desea eliminar esta asociado a uno o mas productos';
        }

        echo json_encode($json);
    }


}
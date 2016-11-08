<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class zona extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('zona/zona_model');
        $this->load->model('ciudad/ciudad_model');
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

        $data["zonas"] = $this->zona_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/zona/zona', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        $data['ciudades'] = $this->ciudad_model->get_all();
        $data['estados'] = $this->estado_model->get_all();
        $data['paises'] = $this->pais_model->get_all();
        if ($id != FALSE) {
            $data['zona'] = $this->zona_model->buscar_id($id);
        }
        $this->load->view('menu/zona/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $zona = array(

            'ciudad_id' => $this->input->post('ciudad_id'),
            'urb' => $this->input->post('urb'),
            'zona_nombre' => $this->input->post('zona_nombre'),
        );

        if (empty($id)) {
            $resultado = $this->zona_model->insertar($zona);

        } else {
            $zona['zona_id'] = $id;
            $resultado = $this->zona_model->update($zona);
        }

        if ($resultado == TRUE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);

    }


    function eliminar()
    {
        $id = $this->input->post('id');

        $zona = array(
            'zona_id' => $id,
            'status' => 0

        );

        $data['resultado'] = $this->zona_model->update($zona);


        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha Eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el impuesto';
        }

        echo json_encode($json);
    }

    function get_by_zona()
    {
        if ($this->input->is_ajax_request()) {
            $zona_id = $this->input->post('zona_id');

            $zonas = $this->zona_model->get_by('zona_id', $zona_id);

            echo json_encode($zonas);
        } else {
            redirect(base_url . 'principal');
        }
    }

    function get_by_usuario_zona()
    {

        if ($this->input->is_ajax_request()) {
            $user_id = $this->input->post('vendedor');

            $usuarios = $this->zona_model->get_by_form('id_usuario', $user_id);

            echo json_encode($usuarios);
        } else {
            redirect(base_url . 'principal');
        }
    }

    function get_by_ciudad()
    {
        if ($this->input->is_ajax_request()) {
            $ciudad_id = $this->input->post('ciudad_id');

            $zonas = $this->zona_model->get_all_by('ciudad_id', $ciudad_id);
            header('Content-Type: application/json');
            echo json_encode($zonas);
        } else {
            redirect(base_url . 'principal');
        }
    }


}

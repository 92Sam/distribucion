<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class tiposdegasto extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('tiposdegasto/tipos_gasto_model');
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

        $data['tipos'] = $this->tipos_gasto_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/tiposdegasto/tiposdegasto', $data, true);


        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        if ($id != FALSE) {
            $data['tiposgasto'] = $this->tipos_gasto_model->get_by('id_tipos_gasto', $id);
        }
        $this->load->view('menu/tiposdegasto/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $tiposgasto = array(
            'nombre_tipos_gasto' => $this->input->post('nombre_tipos_gasto'),
        );

        if (empty($id)) {
            $resultado = $this->tipos_gasto_model->insertar($tiposgasto);
        } else {
            $tiposgasto['id_tipos_gasto'] = $id;
            $resultado = $this->tipos_gasto_model->update($tiposgasto);
        }

        if ($resultado == TRUE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
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

        $tiposgasto = array(
            'id_tipos_gasto' => $id,
            'nombre_tipos_gasto' => $nombre . time(),
            'status_tipos_gasto' => 0

        );

        $data['resultado'] = $this->tipos_gasto_model->update($tiposgasto);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

           $json['error'] = 'Ha ocurrido un error al eliminar el Tipo de Gasto';
        }

        echo json_encode($json);
    }


}

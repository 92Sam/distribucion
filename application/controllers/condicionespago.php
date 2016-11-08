<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class condicionespago extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('condicionespago/condiciones_pago_model');
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

        $data['condiciones'] = $this->condiciones_pago_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/condicionespago/condicionespago', $data, true);

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
            $data['condicionespago'] = $this->condiciones_pago_model->get_by('id_condiciones', $id);
        }
        $this->load->view('menu/condicionespago/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $condionespago = array(
            'nombre_condiciones' => $this->input->post('nombre_condiciones'),
            'dias' => $this->input->post('dias'),
        );

        if (empty($id)) {
            $resultado = $this->condiciones_pago_model->insertar($condionespago);
        }
        else{
            $condionespago['id_condiciones'] = $id;
            $resultado = $this->condiciones_pago_model->update($condionespago);
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
        $nombre = $this->input->post('nombre');

        $condicionespago = array(
            'id_condiciones' => $id,
            'nombre_condiciones' => $nombre . time(),
            'status_condiciones' => 0

        );

        $data['resultado'] = $this->condiciones_pago_model->update($condicionespago);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] ='Ha ocurrido un error al eliminar la Condici&oacute;n';
        }

        echo json_encode($json);
    }


}

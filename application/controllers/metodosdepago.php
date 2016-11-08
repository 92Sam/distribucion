<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class metodosdepago extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('metodosdepago/metodos_pago_model');
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

        $data['metodos'] = $this->metodos_pago_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/metodosdepago/metodosdepago', $data, true);

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
            $data['metodospago'] = $this->metodos_pago_model->get_by('id_metodo', $id);
        }
        $this->load->view('menu/metodosdepago/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $metodospago = array(
            'nombre_metodo' => $this->input->post('nombre_metodo'),
            'tipo_metodo' => $this->input->post('tipo')
        );

        if (empty($id)) {
            $resultado = $this->metodos_pago_model->insertar($metodospago);
        }
        else{
            $metodospago['id_metodo'] = $id;
            $resultado = $this->metodos_pago_model->update($metodospago);
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

        $metodospago = array(
            'id_metodo' => $id,
            'nombre_metodo' => $nombre . time(),
            'status_metodo' => 0

        );

        $data['resultado'] = $this->metodos_pago_model->update($metodospago);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el M&eacute;todos';
        }

        echo json_encode($json);
    }


}

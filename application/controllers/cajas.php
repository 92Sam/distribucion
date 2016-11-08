<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('cajas/cajas_model');
        $this->load->model('local/local_model');
        $this->load->model('usuario/usuario_model');
                $this->load->model('zona/zona_model');

      
    }

    /*function very_sesion()
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

        $data['cajas'] = $this->cajas_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/cajas/cajas', $data, true);

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
            $data['cajas'] = $this->cajas_model->get_by('caja_id', $id);

        }
        $data['locales'] = $this->local_model->get_all();
        $data['usuarios'] = $this->cajas_model->get_all_user();
        $data['caja_has_usuario'] = $this->cajas_model->get_all_user();
        $data['zonas'] = $this->zona_model->get_all();
        $this->load->view('menu/cajas/form', $data);

    }

  function guardar()
    {

        $id = $this->input->post('id');
        $activo = $this->input->post('activo');

        $cajas = array(
            'local' => $this->input->post('local'),
            'activo' => empty($activo) ? 0 : $this->input->post('activo'),
            'cuenta_contable' => $this->input->post('cuenta_contable'),
            'responsable' => $this->input->post('responsable'),
            'status' => 1,
        );

        if (empty($id)) {
            $resultado = $this->cajas_model->insertar($cajas);

        } else {
            $cajas['caja_id'] = $id;
            $resultado = $this->cajas_model->update($cajas);
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

        $cajas = array(
            'caja_id' => $id,
            'status' => 0

        );

        $data['resultado'] = $this->cajas_model->update($cajas);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar la Caja';
        }

        echo json_encode($json);
    } 

}

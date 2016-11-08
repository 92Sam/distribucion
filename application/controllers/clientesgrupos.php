<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class clientesgrupos extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('clientesgrupos/clientes_grupos_model');
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

        $data['grupos'] = $this->clientes_grupos_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/clientesgrupos/clientesgrupos', $data, true);

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
            $data['clientesgrupos'] = $this->clientes_grupos_model->get_by('id_grupos_cliente', $id);
        }
        $this->load->view('menu/clientesgrupos/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $clientesgrupos = array(
            'nombre_grupos_cliente' => $this->input->post('nombre_grupos_cliente'),
        );

        if (empty($id)) {
            $resultado = $this->clientes_grupos_model->insertar($clientesgrupos);
        }
        else{
            $clientesgrupos['id_grupos_cliente'] = $id;
            $resultado = $this->clientes_grupos_model->update($clientesgrupos);
        }

        if ($resultado == TRUE) {
            $json['success']= 'Solicitud Procesada con exito';
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

        $clientesgrupos = array(
            'id_grupos_cliente' => $id,
            'nombre_grupos_cliente' => $nombre . time(),
            'status_grupos_cliente' => 0

        );

        $data['resultado'] = $this->clientes_grupos_model->update($clientesgrupos);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] ='Ha ocurrido un error al eliminar el Grupo';
        }

        echo json_encode($json);

    }


}

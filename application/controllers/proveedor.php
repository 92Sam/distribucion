<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class proveedor extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('proveedor/proveedor_model');
    }



    /** carga cuando listas los proveedores*/
    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['proveedores'] = $this->proveedor_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/proveedor/proveedor', $data, true);


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
            $data['proveedor'] = $this->proveedor_model->get_by('id_proveedor', $id);
        }
        $this->load->view('menu/proveedor/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $proveedor = array(
            'proveedor_ruc' => $this->input->post('proveedor_ruc'),
            'proveedor_nombre' => $this->input->post('proveedor_nombre'),
            'proveedor_direccion1' => $this->input->post('proveedor_direccion1'),
            'proveedor_telefono1' => $this->input->post('proveedor_telefono1'),
            'proveedor_email' => $this->input->post('proveedor_email'),
            'proveedor_contacto' => $this->input->post('proveedor_contacto'),
            'proveedor_telefono2' => $this->input->post('proveedor_telefono2'),
            'proveedor_paginaweb' => $this->input->post('proveedor_paginaweb'),
            'proveedor_observacion' => $this->input->post('proveedor_observacion')
        );

        if (empty($id)) {
            $resultado = $this->proveedor_model->insertar($proveedor);
        }
        else{
            $proveedor['id_proveedor'] = $id;
            $resultado = $this->proveedor_model->update($proveedor);
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

        $proveedor = array(
            'id_proveedor' => $id,
            'proveedor_nombre' => $nombre . time(),
            'proveedor_status' => 0
        );

        $data['resultado'] = $this->proveedor_model->update($proveedor);

        if ($data['resultado'] != FALSE) {

            $json['success']  = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el Proveedor';
        }

       echo json_encode($json);
    }

    public function cuentas_por_pagar()
    {
        $data["lstproveedor"] = $this->proveedor_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/proveedor/cuentasporpagar', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }

}
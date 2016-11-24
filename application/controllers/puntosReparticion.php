<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class puntosReparticion extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();
        $this->load->model('consolidadodecargas/consolidado_model');
        $this->load->model('usuario/usuario_model');
        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');


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
        $data['consolidado'] = array();
        $data["lstUsuario"] = $this->usuario_model->select_all_user();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/camiones/puntosReparticion', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);

        }

    }

function verMapa($id = FALSE){

    if ($id != FALSE) {
        $data['puntoMapa'] = $this->consolidado_model->getMap($id);
    }
    $this->load->view('menu/camiones/mapaReparticion', $data);

}
    function buscarReparticion()
 {

     $where=array();
    if ($this->input->post('trabajador') != "") {
        $campo = $this->input->post('trabajador');
        $where['nUsuCodigo'] = $campo;

    }

    if ($this->input->post('desde') != "") {
         $campo= date('Y-m-d', strtotime($this->input->post('desde')));
        $where['date(fecha) >='] = $campo;
        // $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s', strtotime('0:0:0'));
     }
     if ($this->input->post('hasta') != "") {
         $campo= date('Y-m-d', strtotime($this->input->post('hasta')));
         $where['date(fecha) <='] = $campo;
         //$data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s', strtotime('23:59:59'));
     }

     $data['reparticion'] = $this->consolidado_model->getReparticion($where);
     $this->load->view('menu/camiones/listaPunto', $data);
 }




}


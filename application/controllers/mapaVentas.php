<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class mapaVentas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();
        $this->load->model('consolidadodecargas/consolidado_model');
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
        $data['consolidado'] = $this->consolidado_model->mapClientesPorAtender();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ventas/mapaVentas', $data, true);



        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);

        }

    }

    function puntoReparticion()
    {
        $where = '';
        if ($this->input->post('desde') != "") {
            $campo= date('Y-m-d', strtotime($this->input->post('desde')));
            $where = 'date(consolidado_carga.fecha) >="' . $campo . '"';

        }
        if ($this->input->post('hasta') != "") {
            $campo= date('Y-m-d', strtotime($this->input->post('hasta')));
            if ($this->input->post('desde') != "") {
                $where .= " and ";
            }
            $where .= 'date(consolidado_carga.fecha) <="' . $campo . '"';

        }

        $data['consolidado'] = $this->consolidado_model->getMapaFecha($where);
        $this->load->view('menu/ventas/mapaReparticionFecha', $data);
    }

 }





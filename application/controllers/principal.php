<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class principal extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('venta/venta_model');
        $this->load->model('ingreso/ingreso_model');
        $this->load->model('cliente/cliente_model');
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
        $data['ventashoy'] = count($this->venta_model->get_ventas_by(array('DATE(fecha)'=>date('Y-m-d'))));
        $data['ventastotalhoy'] = $this->venta_model->get_total_ventas_by_date(date('Y-m-d'));
        $data['comprashoy'] = count($this->ingreso_model->get_ingresos_by(array('DATE(fecha_registro)'=>date('Y-m-d'))));


        $dataCuerpo['cuerpo'] = $this->load->view('menu/principal', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }


    }

    function getPage()
    {
        if (!$_POST['page']) die("0");
        $html = "";
        $page = $_POST['page'];

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $page);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $html .= curl_exec($curl);
        curl_close($curl);
        echo $html;

    }

}
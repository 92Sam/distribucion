<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class opciones extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('opciones/opciones_model');
        $this->load->model('usuario/usuario_model');
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
        $data="";

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['configuraciones'] = $this->opciones_model->get_opciones();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/opciones/opciones', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function save()
    {

        $data =array();
        $configuraciones[] = array(
            'config_key' => EMPRESA_NOMBRE,
            'config_value' => $this->input->post('EMPRESA_NOMBRE')
        );

        $configuraciones[] = array(
            'config_key' => EMPRESA_DIRECCION,
            'config_value' => $this->input->post('EMPRESA_DIRECCION')
        );

        $configuraciones[] = array(
            'config_key' => EMPRESA_TELEFONO,
            'config_value' => $this->input->post('EMPRESA_TELEFONO')
        );
        $configuraciones[] = array(
            'config_key' => MONTO_BOLETAS_VENTA,
            'config_value' => $this->input->post('MONTO_BOLETAS_VENTA')
        );
        $configuraciones[] = array(
            'config_key' => VENTA_SIN_STOCK,
            'config_value' => $this->input->post('VENTA_SIN_STOCK')
        );
        $configuraciones[] = array(
            'config_key' => DATABASE_IP,
            'config_value' => $this->input->post('DATABASE_IP')
        );

        $configuraciones[] = array(
            'config_key' => DATABASE_NAME,
            'config_value' => $this->input->post('DATABASE_NAME')
        );

        $configuraciones[] = array(
            'config_key' => DATABASE_USERNAME,
            'config_value' => $this->input->post('DATABASE_USERNAME')
        );

        $configuraciones[] = array(
            'config_key' => MONEDA_OPCION,
            'config_value' => $this->input->post('MONEDA')
        );
         $configuraciones[] = array(
             'config_key' => REFRESCAR_PEDIDOS_OPCION,
            'config_value' => $this->input->post('REFRESCAR_PEDIDOS')
        );

        $configuraciones[] = array(
            'config_key' => "FACTURA_NEXT",
            'config_value' => $this->input->post('FACTURA_NEXT')
        );

        $configuraciones[] = array(
            'config_key' => "FACTURA_SERIE",
            'config_value' => $this->input->post('FACTURA_SERIE')
        );

        $configuraciones[] = array(
            'config_key' => "FACTURA_MAX",
            'config_value' => $this->input->post('FACTURA_MAX')
        );

        $configuraciones[] = array(
            'config_key' => "BOLETA_NEXT",
            'config_value' => $this->input->post('BOLETA_NEXT')
        );

        $configuraciones[] = array(
            'config_key' => "BOLETA_SERIE",
            'config_value' => $this->input->post('BOLETA_SERIE')
        );

        $configuraciones[] = array(
            'config_key' => "BOLETA_MAX",
            'config_value' => $this->input->post('BOLETA_MAX')
        );



        $password = $this->input->post('DATABASE_PASWORD');

        if (!empty($password)) {
            $configuraciones[] = array(
                'config_key' => DATABASE_PASWORD,
                'config_value' => $password
            );
        }

        $updateproductos = array('venta_sin_stock' => $this->input->post('VENTA_SIN_STOCK'));

        $result = $this->opciones_model->guardar_configuracion($configuraciones, $updateproductos);

        $configuraciones = $this->opciones_model->get_opciones();

        if ($configuraciones == TRUE) {
            foreach ($configuraciones as $configuracion) {

                $clave = $configuracion['config_key'];

                    $data[$clave] = $configuracion['config_value'];

            }


        }


        $this->session->set_userdata($data);


        if($result) {
            $json['success'] = 'Las configuraciones se han guardado exitosamente';
        }
        else{
            $json['error']='Ha ocurido un error al guardar las configuraciones';
        }


        echo json_encode($json);


    }

}
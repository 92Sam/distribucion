<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte_modals extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('reporte/rcobranza_model');
        $this->load->model('reporte/rcliente_estado_model');
        $this->load->model('reporte/rstock_transito_model');
        $this->load->model('reporte/rventas_model');
        $this->load->model('usuario/usuario_model');
        $this->load->model('zona/zona_model');
        $this->load->model('cliente/cliente_model');
        $this->load->model('venta/venta_model');
    }

    function detalle_nota_entrega($id)
    {
        $data['venta_id'] = $id;
        $data['venta'] = $this->venta_model->get_nota_entrega($id);

        echo $this->load->view('menu/reports/modals/nota_entrega_modal', $data, true);
    }



}
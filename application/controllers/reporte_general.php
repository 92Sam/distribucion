<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte_general extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('reporte/rventa_compra_model');
    }

    function ventas_compras($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Ventas vs Compras';

        switch ($action) {
            case 'filter': {
                $data['ventas_compras'] = $this->rventa_compra_model->get_ventas_compras(array(
                    'year' => $this->input->post('year')
                ));

                echo $this->load->view('menu/reports/ventas_compras/tabla', $data, true);
                break;
            }
            default: {

                $data['ventas_compras'] = $this->rventa_compra_model->get_ventas_compras(array(
                    'year' => date('Y')
                ));

                $data['reporte_filtro'] = $this->load->view('menu/reports/ventas_compras/filtros', null, true);

                $data['reporte_tabla'] = $this->load->view('menu/reports/ventas_compras/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reports/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }


    }


}
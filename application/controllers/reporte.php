<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('reporte/rcobranza_model');
    }

    function cobranzas($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Cobranzas';

        switch ($action) {
            case 'filter': {
                break;
            }
            case 'pdf': {
                break;
            }
            case 'excel': {
                break;
            }
            case 'graphic': {
                break;
            }
            default: {

                $data['cobranzas'] = $this->rcobranza_model->get_cobranzas(array());

                $data['reporte_filtro'] = $this->load->view('menu/reports/cobranzas/filtros', $data, true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/cobranzas/tabla', $data, true);
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
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('reporte/rcobranza_model');
        $this->load->model('reporte/rcliente_estado_model');
        $this->load->model('usuario/usuario_model');
        $this->load->model('zona/zona_model');
        $this->load->model('cliente/cliente_model');
    }

    function cobranzas($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Cobranzas';

        switch ($action) {
            case 'filter': {
                $data['cobranzas'] = $this->rcobranza_model->get_cobranzas(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'vendedor_id' => $this->input->post('vendedor_id'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'zonas_id' => json_decode($this->input->post('zonas_id')),
                    'atraso' => $this->input->post('atraso'),
                    'dif_deuda' => $this->input->post('dif_deuda'),
                    'dif_deuda_value' => $this->input->post('dif_deuda_value')
                ));

                $data['mostrar_detalles'] = $this->input->post('mostrar_detalles');

                echo $this->load->view('menu/reports/cobranzas/tabla', $data, true);
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

                $data['cobranzas'] = $this->rcobranza_model->get_cobranzas(array(
                    'fecha_ini' => date('Y-m-d'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 0
                ));

                $data['mostrar_detalles'] = 0;

                $data['reporte_filtro'] = $this->load->view('menu/reports/cobranzas/filtros', array(
                    'vendedores' => $this->usuario_model->select_all_by_roll('Vendedor'),
                    'vendedor_zonas' => $this->db->get('usuario_has_zona')->result(),
                    'zonas' => $this->zona_model->get_all(),
                    'clientes' => $this->cliente_model->get_all(),
            ), true);
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

    function cliente_estado($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Estado de Cuenta del Cliente';

        switch ($action) {
            case 'filter': {
                $data['clientes'] = $this->rcliente_estado_model->get_estado_cuenta(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'vendedor_id' => $this->input->post('vendedor_id'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'zonas_id' => json_decode($this->input->post('zonas_id')),
                    'estado' => $this->input->post('estado')
                ));


                echo $this->load->view('menu/reports/cliente_estado/tabla', $data, true);
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

                $data['clientes'] = $this->rcliente_estado_model->get_estado_cuenta(array(
                    'fecha_ini' => date('Y-m-d'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 0
                ));


                $data['reporte_filtro'] = $this->load->view('menu/reports/cliente_estado/filtros', array(
                    'vendedores' => $this->usuario_model->select_all_by_roll('Vendedor'),
                    'vendedor_zonas' => $this->db->get('usuario_has_zona')->result(),
                    'zonas' => $this->zona_model->get_all(),
                    'clientes' => $this->cliente_model->get_all(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/cliente_estado/tabla', $data, true);
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
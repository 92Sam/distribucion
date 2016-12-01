<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte_compra extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->load->model('reporte/rcredito_model');
        //$this->load->model('reporte/compra_cliente_estado_model');
        $this->load->model('reporte/rcompras_model');
    }

    function credito($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Cr&eacute;dito';

        switch ($action) {
            case 'filter': {
                $data['cobranzas'] = $this->rcredito_model->get_cobranzas(array(
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
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1
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

    function proveedor_estado($action = '')
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
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1
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

    function compras($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Resumen de Compras';

        switch ($action) {
            case 'filter': {
                $data['compras'] = $this->rcompras_model->get_compras(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'proveedor_id' => $this->input->post('proveedor_id'),
                    'tipo_documento' => $this->input->post('tipo_documento'),
                    'desglose' => $this->input->post('desglose'),
                ));

                if ($this->input->post('desglose') == 1)
                    $data['desglose'] = 'Proveedores';
                if ($this->input->post('desglose') == 2)
                    $data['desglose'] = 'Tipo de Documento';
                if ($this->input->post('desglose') == 3)
                    $data['desglose'] = 'Tipo de Pago';

                echo $this->load->view('menu/reports/compras/tabla', $data, true);
                break;
            }
            default: {

                $data['compras'] = $this->rcompras_model->get_compras(array(
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1,
                    'desglose' => 1
                ));

                $data['desglose'] = 'Proveedores';

                $data['reporte_filtro'] = $this->load->view('menu/reports/compras/filtros', array(
                    'proveedores' => $this->db->get_where('proveedor', array('proveedor_status' => 1))->result()
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/compras/tabla', $data, true);
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
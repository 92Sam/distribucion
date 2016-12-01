<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte_compra extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('reporte/rcuentas_model');
        $this->load->model('reporte/rproveedor_model');
        $this->load->model('reporte/rcompras_model');
    }

    function cuentas($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Cuentas por Cobrar';

        switch ($action) {
            case 'filter': {
                $data['cuentas'] = $this->rcuentas_model->get_cuentas(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'proveedor_id' => $this->input->post('proveedor_id'),
                    'tipo_documento' => $this->input->post('tipo_documento'),
                    'atraso' => $this->input->post('atraso'),
                    'dif_deuda' => $this->input->post('dif_deuda'),
                    'dif_deuda_value' => $this->input->post('dif_deuda_value')
                ));

                $data['mostrar_detalles'] = $this->input->post('mostrar_detalles');

                echo $this->load->view('menu/reports/cuentas/tabla', $data, true);
                break;
            }
            default: {

                $data['cuentas'] = $this->rcuentas_model->get_cuentas(array(
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1
                ));

                $data['mostrar_detalles'] = 0;

                $data['reporte_filtro'] = $this->load->view('menu/reports/cuentas/filtros', array(
                    'proveedores' => $this->db->get_where('proveedor', array('proveedor_status' => 1))->result()
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/cuentas/tabla', $data, true);
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
        $data['reporte_nombre'] = 'Reporte de Estado de Pago del Proveedor';

        switch ($action) {
            case 'filter': {
                $data['proveedores'] = $this->rproveedor_model->get_estado_pago(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'proveedor_id' => $this->input->post('proveedor_id'),
                    'tipo_documento' => $this->input->post('tipo_documento'),
                    'estado' => $this->input->post('estado')
                ));


                echo $this->load->view('menu/reports/proveedor_estado/tabla', $data, true);
                break;
            }
            default: {

                $data['proveedores'] = $this->rproveedor_model->get_estado_pago(array(
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1
                ));


                $data['reporte_filtro'] = $this->load->view('menu/reports/proveedor_estado/filtros', array(
                    'proveedores' => $this->db->get_where('proveedor', array('proveedor_status' => 1))->result()
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/proveedor_estado/tabla', $data, true);
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
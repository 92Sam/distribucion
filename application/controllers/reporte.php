<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class reporte extends MY_Controller
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


    function stock_transito($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Stock Comprometido';

        switch ($action) {
            case 'filter': {
                $data['stocks'] = $this->rstock_transito_model->get_stock_transito(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'vendedor_id' => $this->input->post('vendedor_id'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'zonas_id' => json_decode($this->input->post('zonas_id')),
                    'proceso_transito' => $this->input->post('proceso_transito')
                ));


                echo $this->load->view('menu/reports/stock_transito/tabla', $data, true);
                break;
            }
            default: {

                $data['stocks'] = $this->rstock_transito_model->get_stock_transito(array(
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1,
                    'proceso_transito' => PROCESO_ASIGNAR
                ));


                $data['reporte_filtro'] = $this->load->view('menu/reports/stock_transito/filtros', array(
                    'vendedores' => $this->usuario_model->select_all_by_roll('Vendedor'),
                    'vendedor_zonas' => $this->db->get('usuario_has_zona')->result(),
                    'zonas' => $this->zona_model->get_all(),
                    'clientes' => $this->cliente_model->get_all(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/stock_transito/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reports/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }


    }

    function ventas($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Ventas';

        switch ($action) {
            case 'filter': {
                $data['ventas'] = $this->rventas_model->get_ventas(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'vendedor_id' => $this->input->post('vendedor_id'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'zonas_id' => json_decode($this->input->post('zonas_id')),
                    'desglose' => $this->input->post('desglose'),
                ));

                if ($this->input->post('desglose') == 1)
                    $data['desglose'] = 'Zonas';
                if ($this->input->post('desglose') == 2)
                    $data['desglose'] = 'Vendedores';
                if ($this->input->post('desglose') == 3)
                    $data['desglose'] = 'Clientes';

                echo $this->load->view('menu/reports/ventas/tabla', $data, true);
                break;
            }
            default: {

                $data['ventas'] = $this->rventas_model->get_ventas(array(
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1,
                    'desglose' => 1
                ));

                $data['desglose'] = 'Zonas';

                $data['reporte_filtro'] = $this->load->view('menu/reports/ventas/filtros', array(
                    'vendedores' => $this->usuario_model->select_all_by_roll('Vendedor'),
                    'vendedor_zonas' => $this->db->get('usuario_has_zona')->result(),
                    'zonas' => $this->zona_model->get_all(),
                    'clientes' => $this->cliente_model->get_all(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/ventas/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reports/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }

    function nota_entrega($action = '')
    {

        $data['reporte_nombre'] = 'Notas de Entrega';

        switch ($action) {
            case 'filter': {
                $data['ventas'] = $this->rventas_model->get_nota_entrega(array());

                echo $this->load->view('menu/reports/nota_entrega/tabla', $data, true);
                break;
            }
            default: {

                $data['ventas'] = $this->rventas_model->get_nota_entrega(array());

                $data['reporte_filtro'] = $this->load->view('menu/reports/nota_entrega/filtros', array(
                    'clientes' => $this->cliente_model->get_all(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/nota_entrega/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reports/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }
    
    function nota_entrega_form($venta_id){
        $data['venta'] = $this->db->select("
            v.fecha AS fecha,
            dv.documento_Serie as serie,
            dv.documento_Numero as numero,
            c.razon_social as razon_social
            ")
            ->from('venta as v')
            ->join('documento_venta as dv', 'dv.id_tipo_documento = v.numero_documento')
            ->join('cliente as c', 'c.id_cliente = v.id_cliente')
            ->where('v.venta_id', $venta_id)
            ->get()->row();

        $data['detalles'] = $this->rventas_model->get_nota_entrega_detalle($venta_id);
        $this->load->view('menu/reports/nota_entrega/detalle', $data);
    }

    function documentos($action = '')
    {

        $data['reporte_nombre'] = 'Documentos';

        switch ($action) {
            case 'filter': {
                $data['ventas'] = $this->rventas_model->get_documentos(array());

                echo $this->load->view('menu/reports/doocumentos/tabla', $data, true);
                break;
            }
            default: {

                $data['ventas'] = $this->rventas_model->get_documentos(array());

                $data['reporte_filtro'] = $this->load->view('menu/reports/doocumentos/filtros', array(
                    'clientes' => $this->cliente_model->get_all(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/doocumentos/tabla', $data, true);
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
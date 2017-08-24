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
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $data['cobranzas'] = $this->rcobranza_model->get_cobranzas(array(
                    'fecha_ini' => date('Y-m-d', strtotime($params->fecha_ini)),
                    'fecha_fin' => date('Y-m-d', strtotime($params->fecha_fin)),
                    'fecha_flag' => $params->fecha_flag,
                    'vendedor_id' => $params->vendedor_id,
                    'cliente_id' => $params->cliente_id,
                    'zonas_id' => json_decode($params->zonas_id),
                    'atraso' => $params->atraso,
                    'dif_deuda' => $params->dif_deuda,
                    'dif_deuda_value' => $params->dif_deuda_value
                ));

                $data['fecha_ini'] = $params->fecha_ini;
                $data['fecha_fin'] = $params->fecha_fin;
                $data['fecha_flag'] = $params->fecha_flag;

                $data['mostrar_detalles'] = $this->input->post('mostrar_detalles');

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4-L');
                $html = $this->load->view('menu/reports/cobranzas/tabla_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
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

                $data['form_filter'] = true;


                echo $this->load->view('menu/reports/cliente_estado/tabla', $data, true);
                break;
            }
            default: {

//                $data['clientes'] = $this->rcliente_estado_model->get_estado_cuenta(array(
//                    'fecha_ini' => date('Y-m-01'),
//                    'fecha_fin' => date('Y-m-d'),
//                    'fecha_flag' => 1
//                ));
                $data['clientes'] = array();

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
                $data['ventas'] = $this->rventas_model->get_nota_entrega(array(
                    'cliente_id' => $this->input->post('cliente_id'),
                    'estado' => $this->input->post('estado'),
                    'estado_ne' => $this->input->post('estado_ne'),
                    'year' => $this->input->post('year'),
                    'mes' => $this->input->post('mes'),
                    'dia_min' => $this->input->post('dia_min'),
                    'dia_max' => $this->input->post('dia_max')
                ));

                echo $this->load->view('menu/reports/nota_entrega/tabla', $data, true);
                break;
            }
            default: {
                $data['ventas'] = $this->rventas_model->get_nota_entrega(array(
                    'year' => date('Y'),
                    'mes' => date('m'),
                    'dia_min' => date('d'),
                    'dia_max' => date('d')
                ));


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

    function nota_entrega_form($venta_id)
    {
        $data['venta'] = $this->db->select("
            hpd.created_at AS fecha,
            dv.documento_Serie as serie,
            dv.documento_Numero as numero,
            c.razon_social as razon_social, 
            cd.consolidado_id as consolidado_id
            ")
            ->from('venta as v')
            ->join('historial_pedido_proceso as hpd', 'hpd.pedido_id = v.venta_id')
            ->join('documento_venta as dv', 'dv.id_tipo_documento = v.numero_documento')
            ->join('cliente as c', 'c.id_cliente = v.id_cliente')
            ->join('consolidado_detalle as cd', 'cd.pedido_id = v.venta_id')
            ->where('v.venta_id', $venta_id)
            ->where('hpd.proceso_id', PROCESO_IMPRIMIR)
            ->get()->row();

        $data['detalles'] = $this->rventas_model->get_nota_entrega_detalle($venta_id);
        $this->load->view('menu/reports/nota_entrega/detalle', $data);
    }

    function documentos($action = '')
    {

        $data['reporte_nombre'] = 'Documentos';

        switch ($action) {
            case 'filter': {
                $data['ventas'] = $this->rventas_model->get_documentos(array(
                    'cliente_id' => $this->input->post('cliente_id'),
                    'estado' => $this->input->post('estado'),
                    'year' => $this->input->post('year'),
                    'mes' => $this->input->post('mes'),
                    'dia_min' => $this->input->post('dia_min'),
                    'dia_max' => $this->input->post('dia_max')
                ));

                echo $this->load->view('menu/reports/documentos/tabla', $data, true);
                break;
            }
            default: {

                $data['ventas'] = $this->rventas_model->get_documentos(array(
                    'year' => date('Y'),
                    'mes' => date('m'),
                    'dia_min' => date('d'),
                    'dia_max' => date('d')
                ));

                $data['reporte_filtro'] = $this->load->view('menu/reports/documentos/filtros', array(
                    'clientes' => $this->cliente_model->get_all(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/documentos/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reports/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }

    function historial_cobranzas($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Historial de Cobranzas';

        switch ($action) {
            case 'filter': {
                $data['ventas'] = $this->rventas_model->get_historial_cobranzas(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => $this->input->post('fecha_flag'),
                    'vendedor_id' => $this->input->post('vendedor_id'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'zonas_id' => json_decode($this->input->post('zonas_id'))
                ));

                echo $this->load->view('menu/reports/historial_cobranza/tabla', $data, true);
                break;
            }
            case 'pdf': {
                $params = json_decode($this->input->get('data'));
                $data['ventas'] = $this->rventas_model->get_historial_cobranzas(array(
                    'fecha_ini' => date('Y-m-d', strtotime($params->fecha_ini)),
                    'fecha_fin' => date('Y-m-d', strtotime($params->fecha_fin)),
                    'fecha_flag' => $params->fecha_flag,
                    'vendedor_id' => $params->vendedor_id,
                    'cliente_id' => $params->cliente_id,
                    'zonas_id' => json_decode($params->zonas_id),
                ));

                $data['fecha_ini'] = $params->fecha_ini;
                $data['fecha_fin'] = $params->fecha_fin;
                $data['fecha_flag'] = $params->fecha_flag;

                $this->load->library('mpdf53/mpdf');
                $mpdf = new mPDF('utf-8', 'A4-L');
                $html = $this->load->view('menu/reports/historial_cobranza/tabla_pdf', $data, true);
                $mpdf->WriteHTML($html);
                $mpdf->Output();
                break;

            }
            default: {

                $data['ventas'] = $this->rventas_model->get_historial_cobranzas(array(
                    'fecha_ini' => date('Y-m-d'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 1
                ));

                $data['reporte_filtro'] = $this->load->view('menu/reports/historial_cobranza/filtros', array(
                    'vendedores' => $this->usuario_model->select_all_by_roll('Vendedor'),
                    'vendedor_zonas' => $this->db->get('usuario_has_zona')->result(),
                    'zonas' => $this->zona_model->get_all(),
                    'clientes' => $this->cliente_model->get_all(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/historial_cobranza/tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/reports/report_template', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }

    function por_productos($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Ventas por Productos';

        switch ($action) {
            case 'filter': {
                $data['productos_list'] = $this->rventas_model->getVentasProducto(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'proveedor_id' => $this->input->post('proveedor_id'),
                    'tipo_documento' => $this->input->post('tipo_documento'),
                    'producto_id' => $this->input->post('producto_id'),
                    'estado' => $this->input->post('estado'),
                    'grupo_id' => $this->input->post('grupo_id'),
                    'marca_id' => $this->input->post('marca_id'),
                    'linea_id' => $this->input->post('linea_id'),
                    'sublinea_id' => $this->input->post('sublinea_id'),
                ));

                echo $this->load->view('menu/reports/por_productos_v/tabla', $data, true);
                break;
            }
            default: {

                $data['productos_list'] = $this->rventas_model->getVentasProducto(array(
                    'fecha_ini' => date('Y-m-d'),
                    'fecha_fin' => date('Y-m-d'),
                    'estado' => 'ENTREGADO'
                ));

                $data['reporte_filtro'] = $this->load->view('menu/reports/por_productos_v/filtros', array(
                    'grupos' => $this->db->get_where('grupos', array('estatus_grupo' => 1))->result(),
                    'marcas' => $this->db->get_where('marcas', array('estatus_marca' => 1))->result(),
                    'lineas' => $this->db->get_where('subgrupo', array('estatus_subgrupo' => 1))->result(),
                    'sublineas' => $this->db->get_where('familia', array('estatus_familia' => 1))->result(),
                    'productos' => $this->db->get_where('producto', array('producto_activo' => 1))->result(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/por_productos_v/tabla', $data, true);
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
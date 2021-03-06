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


    function por_productos($action = '')
    {
        $data['reporte_nombre'] = 'Reporte de Inventario por Productos';

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

                echo $this->load->view('menu/reports/por_productos_i/tabla', $data, true);
                break;
            }
            default: {

                $data['productos_list'] = $this->rventas_model->getVentasProducto(array(
                    'fecha_ini' => date('Y-m-d'),
                    'fecha_fin' => date('Y-m-d'),
                    'estado' => 'ENTREGADO'
                ));

                $data['reporte_filtro'] = $this->load->view('menu/reports/por_productos_i/filtros', array(
                    'grupos' => $this->db->get_where('grupos', array('estatus_grupo' => 1))->result(),
                    'marcas' => $this->db->get_where('marcas', array('estatus_marca' => 1))->result(),
                    'lineas' => $this->db->get_where('subgrupo', array('estatus_subgrupo' => 1))->result(),
                    'sublineas' => $this->db->get_where('familia', array('estatus_familia' => 1))->result(),
                    'productos' => $this->db->get_where('producto', array('producto_activo' => 1))->result(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/reports/por_productos_i/tabla', $data, true);
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
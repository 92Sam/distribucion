<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class pago_pendiente extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('historial/historial_pedido_model');
        $this->load->model('cajas/cajas_model');
        $this->load->model('cliente/cliente_model');
        $this->load->model('usuario/usuario_model');
        $this->load->model('zona/zona_model');
        $this->load->model('historial_pagos_clientes/historial_pagos_clientes_model');
        $this->load->model('consolidadodecargas/consolidado_model');
        $this->load->model('banco/banco_model');
        $this->load->model('venta/venta_cobro_model');

    }


    function pagos($action = "")
    {

        switch ($action) {
            case 'filter': {
                $data['clientes'] = $this->venta_cobro_model->get_pagos_pendientes(array(
                    'fecha_ini' => date('Y-m-d', strtotime($this->input->post('fecha_ini'))),
                    'fecha_fin' => date('Y-m-d', strtotime($this->input->post('fecha_fin'))),
                    'fecha_flag' => 0,
                    'vendedor_id' => $this->input->post('vendedor_id'),
                    'cliente_id' => $this->input->post('cliente_id'),
                    'zonas_id' => json_decode($this->input->post('zonas_id'))
                ));


                echo $this->load->view('menu/pagos_pendientes/tbl_listareg_pagospendiente', $data, true);
                break;
            }
            default: {

                $data['clientes'] = $this->venta_cobro_model->get_pagos_pendientes(array(
                    'fecha_ini' => date('Y-m-01'),
                    'fecha_fin' => date('Y-m-d'),
                    'fecha_flag' => 0
                ));


                $data['reporte_filtro'] = $this->load->view('menu/pagos_pendientes/pagospendientes_filtro', array(
                    'vendedores' => $this->usuario_model->select_all_by_roll('Vendedor'),
                    'vendedor_zonas' => $this->db->get('usuario_has_zona')->result(),
                    'zonas' => $this->zona_model->get_all(),
                    'clientes' => $this->cliente_model->get_all(),
                ), true);
                $data['reporte_tabla'] = $this->load->view('menu/pagos_pendientes/tbl_listareg_pagospendiente', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/pagos_pendientes/pagospendientesVenta', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
            }
        }
    }

    function confirmar_pago($action = "", $id = FALSE)
    {

        switch ($action) {
            case 'filter': {
                $data['pagos'] = $this->venta_cobro_model->get_pagos_by_vendedor($id);

                $data['cajas'] = $this->cajas_model->getCajasSelect();

                $data['consolidados'] = $this->db->get_where('consolidado_carga', array('status' => 'CERRADO'))->result();

                echo $this->load->view('menu/pagos_pendientes/confirmar_tabla', $data, true);
                break;
            }
            default: {
                $data['pagos'] = $this->venta_cobro_model->get_pagos_by_vendedor();
                $data['vendedores'] = $this->usuario_model->select_all_by_roll('Vendedor');
                $data['cajas'] = $this->cajas_model->getCajasSelect();

                $data['consolidados'] = $this->db->get_where('consolidado_carga', array('status' => 'CERRADO'))->result();

                $data['reporte_tabla'] = $this->load->view('menu/pagos_pendientes/confirmar_tabla', $data, true);
                $dataCuerpo['cuerpo'] = $this->load->view('menu/pagos_pendientes/confirmar_pago', $data, true);
                if ($this->input->is_ajax_request()) {
                    echo $dataCuerpo['cuerpo'];
                } else {
                    $this->load->view('menu/template', $dataCuerpo);
                }
                break;
            }
        }


    }

    function ver_pagos($id)
    {
        $data['pagos'] = $this->venta_cobro_model->get_pagos($id);
        $data['venta'] = $this->venta_cobro_model->get_cobranza_by_venta($id);

        $this->load->view('menu/pagos_pendientes/dialog_ver_pagos', $data);
    }

    function pagar_nota_pedido($id)
    {
        $data['venta'] = $this->venta_cobro_model->get_cobranza_by_venta($id);

        $data['retencion'] = $this->venta_cobro_model->get_retencion($id);

        $data['metodos_pago'] = $this->db->get_where('metodos_pago', array('status_metodo' => 1))->result();
        $data['bancos'] = $this->db->get_where('banco', array('banco_status' => 1))->result();

        $this->load->view('menu/pagos_pendientes/dialog_pagar_pedido', $data);
    }

    function ejecutar_pagar_nota_pedido($id)
    {
        $data = array(
            'pago_id' => $this->input->post('pago_id'),
            'banco_id' => $this->input->post('banco_id'),
            'num_oper' => $this->input->post('num_oper'),
            'retencion' => $this->input->post('retencion'),
            'importe' => $this->input->post('importe'),
            'fecha_documento' => $this->input->post('fec_oper'),

        );
        $this->venta_cobro_model->pagar_nota_pedido($id, $data);

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }


    function pagar_cliente($id)
    {
        $data['cliente'] = $this->venta_cobro_model->get_cobranza_by_cliente($id);

        $data['metodos_pago'] = $this->db->get_where('metodos_pago', array('status_metodo' => 1))->result();
        $data['bancos'] = $this->db->get_where('banco', array('banco_status' => 1))->result();

        $this->load->view('menu/pagos_pendientes/dialog_pagar_cliente', $data);
        //var_dump($data);
    }

    function ejecutar_pagar_cliente($id)
    {
        $data = array(
            'pago_id' => $this->input->post('pago_id'),
            'banco_id' => $this->input->post('banco_id'),
            'num_oper' => $this->input->post('num_oper'),
            'importe' => $this->input->post('importe'),
            'fecha_documento' => $this->input->post('fec_oper'),
        );
        $this->venta_cobro_model->pagar_cliente($id, $data);

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function liquidar_pago($id)
    {
        $data['pagos'] = $this->venta_cobro_model->get_pagos_by_vendedor($id);
        $data['venta'] = $this->db->select('
            usuario.nombre as vendedor_nombre,
            usuario.nUsuCodigo as vendedor_id
        ')->from('usuario')->where('nUsuCodigo', $id)->get()->row();

        $data['metodos_pago'] = $this->db->get_where('metodos_pago', array('status_metodo' => 1))->result();
        $data['bancos'] = $this->db->get_where('banco', array('banco_status' => 1))->result();

        $this->load->view('menu/pagos_pendientes/dialog_liquidar_pago', $data);
    }

    function ejecutar_liquidar_pago($id)
    {
        $data = array(
            'pago_id' => $this->input->post('pago_id'),
            'banco_id' => $this->input->post('banco_id'),
            'num_oper' => $this->input->post('num_oper'),
            'importe' => $this->input->post('importe'),
            'fecha_documento' => $this->input->post('fec_oper'),
            'historial_id' => json_decode($this->input->post('historial_id'))
        );

        $this->venta_cobro_model->pagar_by_vendedor($id, $data);

        $this->liquidar_pago($id);
    }

    function confirmar_liquidar_pago($id)
    {
        $cuenta_id = $this->input->post('cuenta_id');
        $this->venta_cobro_model->confirmar_pago($id, $cuenta_id);

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function confirmar_consolidado_pago($id)
    {
        $cuenta_id = $this->input->post('cuenta_id');
        $this->venta_cobro_model->confirmar_pago($id, $cuenta_id);

        $pedido = $this->db->get_where('historial_pagos_clientes', array('historial_id' => $id))->row();

        $this->historial_pedido_model->insertar_pedido(PROCESO_LIQUIDAR, array(
            'pedido_id' => $pedido->credito_id,
            'responsable_id' => $this->session->userdata('nUsuCodigo')
        ));

        $consolidado = $this->db->get_where('consolidado_detalle', array('pedido_id' => $pedido->credito_id))->row();
        $this->consolidado_model->confirmar_consolidado($consolidado->consolidado_id);

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function confirmar_liquidar_pago_seleccion()
    {
        $historial_id = json_decode($this->input->post('historial_id'));
        foreach ($historial_id as $hid) {

            $this->venta_cobro_model->confirmar_pago($hid->id, $hid->cuenta_id);
        }

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function confirmar_consolidado_seleccion()
    {
        $historial_id = json_decode($this->input->post('historial_id'));
        foreach ($historial_id as $hid) {

            $this->venta_cobro_model->confirmar_pago($hid->id, $hid->cuenta_id);

            $pedido = $this->db->get_where('historial_pagos_clientes', array('historial_id' => $hid->id))->row();

            $this->historial_pedido_model->insertar_pedido(PROCESO_LIQUIDAR, array(
                'pedido_id' => $pedido->credito_id,
                'responsable_id' => $this->session->userdata('nUsuCodigo')
            ));

            $consolidado = $this->db->get_where('consolidado_detalle', array('pedido_id' => $pedido->credito_id))->row();
            $this->consolidado_model->confirmar_consolidado($consolidado->consolidado_id);
        }

        header('Content-Type: application/json');
        echo json_encode(array('success' => 1));
    }

    function eliminar_liquidar_pago($id, $vendedor_id)
    {
        $this->venta_cobro_model->eliminar_pago($id);

        $this->liquidar_pago($vendedor_id);
    }
}




<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class historial_pagos extends REST_Controller
{
    protected $uid = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('historial_pagos_clientes/historial_pagos_clientes_model', 'historial');
        $this->load->model('metodosdepago/metodos_pago_model');
        $this->load->model('venta/venta_model');
        $this->load->model('usuario/usuario_model');
        $this->load->model('venta/venta_cobro_model');
        $this->load->library('form_validation');

        $this->load->model('api/api_model', 'api');
        $this->very_auth();
    }

    function very_auth()
    {
        // Request Header
        $reqHeader = $this->input->request_headers();

        // Key
        $key = null;
        if (isset($reqHeader['X-api-key'])) {
            $key = $reqHeader['X-api-key'];
        } else if ($key_get = $this->get('x-api-key')) {
            $key = $key_get;
        } else if ($key_post = $this->post('x-api-key')) {
            $key = $key_post;
        } else {
            $key = null;
        }

        // Auth ID
        $auth_id = $this->api->getAuth($key);

        if (!empty($auth_id)) {
            $this->uid = $auth_id;
        } else {
            $this->uid = null;
        }
    }

    // All
    public function index_get()
    {
        $id_venta = $this->input->get('pedido');
        $vendedor = $this->input->get('vendedor');
        $fecha = $this->input->get('fecha');

        $select = 'historial_pagos_clientes.*, credito.*, usuario.nombre, metodos_pago.nombre_metodo';
        $from = "historial_pagos_clientes";
        $join = array('credito', 'usuario', 'venta','metodos_pago');
        $campos_join = array('credito.id_venta=historial_pagos_clientes.credito_id', 'usuario.nUsuCodigo=historial_pagos_clientes.historial_usuario',
            'credito.id_venta=venta.venta_id','metodos_pago.id_metodo=historial_pagos_clientes.historial_tipopago');
        $tipo_join[0] = "";
        $tipo_join[1] = "left";
        $where = array();

        if (!empty($id_venta)) {
            $where = array('credito_id' => $id_venta);
        }

        if (!empty($vendedor)) {
            $where['id_vendedor'] = $vendedor;
        }
        if (!empty($fecha)) {
            $where['historial_fecha'] = $fecha;
        }
        $result['historial'] = $this->historial->traer_by($select, $from, $join, $campos_join, $tipo_join, $where, false, false, false, false, false, false, "RESULT_ARRAY");


        if ($result) {
            $this->response($result, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Show
    public function ver_get()
    {
        //
    }

    // Save
    public function create_get()
    {

        $get = $this->input->get(null, true);

        $detalle = array();
        $detalle[0]['cuota'] = $get['importe'];
        $detalle[0]['id_venta'] = $get['venta_id'];
        $detalle[0]['metodo'] = $get['metodo'];
        $detalle[0]['usuario'] = $get['vendedor'];
        $detalle[0] = (object)$detalle[0];


        $result = $this->historial->guardar($detalle);

        if ($result == true) {
            $credito = $this->venta_model->updateCreditos($detalle, false);
            $result = array();
            $result['credito'] = $this->venta_model->get_credito_by_venta($detalle[0]->id_venta);

            // $detalle[0]->monto_restante = $result['credito'][0]['dec_credito_montodeuda'] - $result['credito'][0]['dec_credito_montodebito'];
            $detalle[0]->monto_restante = floatval($result['credito'][0]['dec_credito_montodeuda']) - (floatval($result['credito'][0]['dec_credito_montodebito']) + floatval($credito[0]['confirmacion_monto_cobrado_caja']) + floatval($credito[0]['confirmacion_monto_cobrado_bancos']) + floatval($credito[0]['pagado']));

            $opciones = array();
            $tipo_metodo = $this->metodos_pago_model->get_by('id_metodo', $detalle[0]->metodo);

            if ($tipo_metodo['tipo_metodo'] == "BANCO") {
                $opciones['banco'] = $get['banco'];
            } else {
                $opciones['caja'] = $get['caja'];
            }

            $result = $this->historial->guardar($detalle, $opciones);
        }

      // echo $result;
        if ($result !=false ) {
            $this->response(array('status' => 'success'));

        } else {
            $this->response(array('status' => 'failed','error',$result));
        }
    }

    // Update
    public function update_get()
    {
        $get = $this->input->get(null, true);

        $historial = $get['pago_id'];
        $venta_id = $get['venta_id'];
        $montonuevo = $get['importe'];
        $vendedor = $get['vendedor'];
        $result = $this->historial->actualizar_historial_editado($historial, $venta_id, $montonuevo);


        if ($result === false) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }
    }

    public function pagar_cliente_get() {

        $get = $this->input->get(null, true);

        $id_cliente = $get['cliente'];

        $data = array(
            'pago_id' => $get['pago_id'],
            'banco_id' => $get['banco_id'],
            'num_oper' => $get['num_oper'],
            'importe' => $get['importe'],
            'vendedor' => $get['vendedor']
        );

        $result = null;

        if ($id_cliente) {
            $result = $this->venta_cobro_model->pagar_cliente($id_cliente, $data);
        }

        if ($result === false) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }
    }

    public function pagar_documento_get() {

        $get = $this->input->get(null, true);

        $id_documento = $get['documento'];

        $data = array(
            'pago_id' => $get['pago_id'],
            'banco_id' => $get['banco_id'],
            'num_oper' => $get['num_oper'],
            'importe' => $get['importe'],
            'vendedor' => $get['vendedor']
        );

        $result = null;

        if ($id_documento) {
            $result = $this->venta_cobro_model->pagar_nota_pedido($id_documento, $data);
        }

        if ($result === false) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }
    }

    public function liquidar_pagos_get() {

        $get = $this->input->get(null, true);

        $id_vendedor = $get['vendedor'];
        $lista = $get['historial'];

        $data = array(
            'pago_id' => $get['pago_id'],
            'banco_id' => $get['banco_id'],
            'num_oper' => $get['num_oper'],
            'importe' => $get['importe'],
            'vendedor' => $id_vendedor,
            'historial_id' => json_decode($lista)
        );

        $hist = json_decode($lista);

        foreach ($hist as $l) {
            echo $l->id;
        }

        $result = null;

        /*if ($id_vendedor) {
            $result = $this->venta_cobro_model->pagar_by_vendedor($id_vendedor, $data);
        }*/

        if ($result === false) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }

    }

    public function delete_liquidacion_get() {

        $get = $this->input->get(null, true);

        $id_historial = $get['id'];

        $result = $this->venta_cobro_model->eliminar_pago($id_historial);

        if ($result === false) {
            $this->response(array('status' => 'failed'));
        } else {
            $this->response(array('status' => 'success'));
        }
    }
}
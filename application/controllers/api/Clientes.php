<?php

// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

class clientes extends REST_Controller
{
    protected $uid = null;

    function __construct()
    {
        parent::__construct();

        $this->load->model('cliente/cliente_model');
        $this->load->model('cliente_datos/cliente_datos_model');
        $this->load->model('usuario/usuario_model');
        $this->load->model('api/api_model', 'api');
        $this->load->model('venta/venta_model', 'venta');
        $this->load->library('form_validation');

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

        // ID ?
        if (!empty($auth_id)) {
            $this->uid = $auth_id;
        } else {
            $this->uid = null;
        }
    }

    // All
    public function index_get()
    {
        $vendedor = $this->input->get('vendedor');


        // Pagination Result
        $total = $this->cliente_model->count_all();

        $datas = array();

        $where = array();

        if ($vendedor != '') {
            $isadmin = $this->usuario_model->buscar_id($vendedor);
            if ($isadmin->admin == 1) {
                $vendedor = '';
            }
        }


        if (!empty($vendedor)) {
            $where['cliente.vendedor_a'] = $vendedor;
        }

        $where['cliente.cliente_status'] = '1';

        $search = $this->input->get('search');
        $select2 = $this->input->get('select2');

        if (!empty($select2)) {

            $buscar = $search;
        } else {
            $buscar = $search['value'];
        }


        $where_custom = false;
        if (!empty($search['value'])) {
            $where_custom = "(cliente.id_cliente LIKE '%" . $buscar . "%' or cliente.razon_social LIKE '%" . $buscar . "%'
            or cliente.ruc_cliente LIKE '%" . $buscar . "%'
            or cli_dat.valor LIKE '%" . $buscar . "%'
            or ciudad_nombre LIKE '%" . $buscar . "%' or zona_nombre LIKE '%" . $buscar . "%'
            or nombre LIKE '%" . $buscar . "%')";
        }
        //  var_dump($like);

        $nombre_or = false;
        $where_or = false;
        $nombre_in = false;
        $where_in = false;
        $select = 'distinct(cliente.id_cliente),cliente.*,cli_dat.*, ciudades.*,estados.*,pais.*, grupos_cliente.*, zonas.*, usuario.nombre';
        $from = "cliente";
        $join = array('ciudades', 'estados', 'pais', 'grupos_cliente', 'zonas', 'usuario', '(SELECT c.cliente_id, c.tipo, c.valor, c.principal, COUNT(*) FROM cliente_datos c WHERE c.tipo =1 GROUP BY c.cliente_id, c.tipo ) cli_dat');
        $campos_join = array('ciudades.ciudad_id=cliente.ciudad_id', 'ciudades.estado_id=estados.estados_id',
            'pais.id_pais=estados.pais_id', 'grupos_cliente.id_grupos_cliente=cliente.grupo_id', 'zonas.zona_id=cliente.id_zona', 'usuario.nUsuCodigo=cliente.vendedor_a', 'cli_dat.cliente_id = cliente.id_cliente');
        $tipo_join = false;

        $ordenar = $this->input->get('order');
        $order = false;
        $order_dir = 'desc';
        if (!empty($ordenar)) {
            $order_dir = $ordenar[0]['dir'];
            if ($ordenar[0]['column'] == 0) {
                $order = 'id_cliente';
            }
            if ($ordenar[0]['column'] == 1) {
                $order = 'ruc_cliente';
            }
            if ($ordenar[0]['column'] == 2) {
                $order = 'razon_social';
            }
            if ($ordenar[0]['column'] == 3) {
                $order = 'tipo_cliente';
            }
            if ($ordenar[0]['column'] == 4) {
                $order = 'nombre_grupos_cliente';
            }
            if ($ordenar[0]['column'] == 5) {
                $order = 'valor'; //'direccion2';
            }
            if ($ordenar[0]['column'] == 6) {
                $order = 'ciudad_nombre';
            }
            if ($ordenar[0]['column'] == 7) {
                $order = 'zona_nombre';
            }
            if ($ordenar[0]['column'] == 8) {
                $order = 'nombre';
            }
        }


        $start = 0;
        $limit = false;
        $draw = $this->input->get('draw');
        if (!empty($draw)) {

            $start = $this->input->get('start');
            $limit = $this->get('length');
            //$order=
        }


        // echo $limit;
        // var_dump($this->input->get(null));

        $datas['clientes'] = $this->cliente_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
            $nombre_in, $where_in, $nombre_or, $where_or, false, $order, "RESULT_ARRAY", $limit, $start, $order_dir, false, $where_custom);
        // ($vendedor, $filter, $page, $limit);


        $array = array();
        $array['clientes_total'] = $total;
        $array['clientes'] = array();
        $array['clientesjson'] = array();

        $count = 0;
        foreach ($datas['clientes'] as $data) {

            $cliente_tipo = '';
            if($data['tipo_cliente'] == 0)
                $cliente_tipo = 'JURIDICO';
            if($data['tipo_cliente'] == 1 && $data['identificacion'] == 1)
                $cliente_tipo = 'NATURAL NEGOCIO';
            if($data['tipo_cliente'] == 1 && $data['identificacion'] == 2)
                $cliente_tipo = 'NATURAL';

            $arr = $data;
            $clientjson = array();
            $clientjson[0] = $data['id_cliente'];
            $clientjson[1] = $data['ruc_cliente'];
            $clientjson[2] = $data['razon_social'];
            $clientjson[3] = $cliente_tipo;
            $clientjson[4] = $data['nombre_grupos_cliente'];
            $clientjson[5] = $data['valor'];
            $clientjson[6] = $data['ciudad_nombre'];
            $clientjson[7] = $data['zona_nombre'];
            $clientjson[8] = $data['nombre'];
            $clientjson[9] = null;

            $arr['cliente_datos'] = $this->cliente_datos_model->get_all_by($data['id_cliente']);

            $arr['id_cliente'] = $data['id_cliente'];
            $arr['id'] = $data['id_cliente'];
            $arr['nombre'] = $data['razon_social'];

            $where = array('venta.id_cliente' => $data['id_cliente']);
            $where['condiciones_pago.dias >'] = '0';
            $nombre_or = false;
            $where_or = false;
            $nombre_in[0] = 'var_credito_estado';
            $where_in[0] = array('DEBE', 'A_CUENTA');
            $nombre_in[1] = 'venta_status';
            $where_in[1] = array('ENTREGADO', 'DEVUELTO PARCIALMENTE', 'COMPLETADO');
            $select = 'venta.venta_id, venta.id_cliente, razon_social,fecha, total,var_credito_estado, sum(dec_credito_montodebito) as suma, documento_venta.*,
            nombre_condiciones';
            $from = "venta";
            $join = array('credito', 'cliente', 'documento_venta', 'condiciones_pago');
            $campos_join = array('credito.id_venta=venta.venta_id', 'cliente.id_cliente=venta.id_cliente',
                'documento_venta.id_tipo_documento=venta.numero_documento', 'condiciones_pago.id_condiciones=venta.condicion_pago');
            $tipo_join = false;
            $result['lstVenta'] = $this->venta->traer_by($select, $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, false, false, "RESULT_ARRAY");

            $arr['saldo_actual'] = $result['lstVenta'][0]['suma'];

            $aprobaos = $this->venta->count_by('venta_status', Array(PEDIDO_ENTREGADO, PEDIDO_DEVUELTO));
            $rechazads = $this->venta->count_by('venta_status', Array(PEDIDO_RECHAZADO));
            $arr['cantidad_aprobados'] = $aprobaos['count'];
            $arr['cantidad_rechazado'] = $rechazads['count'];
            $array['clientes'][] = $arr;
            $array['clientesjson'][] = $clientjson;

            $count++;
        }

        $array['data'] = $array['clientesjson'];
        $array['draw'] = $draw;//esto debe venir por post
        $array['recordsTotal'] = $total;
        $array['recordsFiltered'] = $total; // esto dbe venir por post
//var_dump($array['data']);
        if ($datas) {
            $this->response($array, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Show
    public function ver_get()
    {
        $id = $this->get('id');
        if (empty($id)) {
            $this->response(array(), 200);
        }

        $data['cliente'] = $this->cliente_model->get_by('id_cliente', $id);

        if ($data) {
            $this->response($data, 200);
        } else {
            $this->response(array(), 200);
        }
    }

    // Save
    public function guardar_get()
    {
        $post = $this->input->get();

        //Datos variables
        $id = $post['cliente_id'];
        $ciudad_id = $post['ciudad_id'];
        $vendedor_id = $post['vendedor_a'];
        $razon_social = $post['razon_social'];
        $ruc_cliente = $post['ruc_cliente'];
        $latitud = $post['latitud'];
        $longitud = $post['longitud'];
        $zona_id = $post['zona_id'];
        $direccion = $post['direccion'];
        $celular = $post['celular'];
        $correo = $post['correo'];

        //Datos fijos
        $descuento = 2;
        $excento_impuesto = 0;
        $grupo_id = 1;
        $representante = null;
        $limite_credito = null;
        $identificacion = 2;
        $cliente_status = 1;
        $tipo_cliente = 1;
        $agente_retencion = 0;
        $linea_credito_valor = null;
        $linea_libre = 1;
        $linea_libre_valor = null;
        $importe_deuda = null;
        $deuda_bolera = null;

        $cliente = array(
            'tipo_cliente' => $tipo_cliente,
            'ciudad_id' => $ciudad_id,
            'grupo_id' => $grupo_id,
            'representante' => $representante,
            'razon_social' => $razon_social,

            'agente_retencion' => $agente_retencion,
            'linea_credito_valor' => $linea_credito_valor,
            'descuento' => $descuento,
            'linea_libre' => $linea_libre,
            'linea_libre_valor' => $linea_libre_valor,

            'identificacion' => $identificacion,
            'ruc_cliente' => $ruc_cliente,
            'latitud' => $latitud,
            'longitud' => $longitud,
            'importe_deuda' => $importe_deuda,
            'id_zona' => $zona_id,
            'vendedor_a' => $vendedor_id,
        );

        $items = array();
        if ($direccion != null) {
            $data = array(
                'tipo' => 1,
                'valor' => $direccion,
                'principal' => true
            );

            array_push($items, $data);
        }

        if ($celular != null) {
            $data = array(
                'tipo' => 2,
                'valor' => $celular,
                'principal' => false
            );

            array_push($items, $data);
        }

        if ($correo != null) {
            $data = array(
                'tipo' => 3,
                'valor' => $correo,
                'principal' => false
            );

            array_push($items, $data);
        }

        $datos = array(
            'gerente_dni' => null,
            'representante' => null,
            'representante_dni' => null
        );

        if (empty($id)) {
            $result = $this->cliente_model->insertar($cliente, $_POST['items'], $datos);

        } else {
            $cliente['id_cliente'] = $id;
            $result = $this->cliente_model->update($cliente, $_POST['items'], $datos);
        }

        if ($result === false) {
            $this->response(array('status' => 'failed'));

        } else {
            $this->response(array('status' => 'success'));
        }
    }
}
<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ingresos extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('cliente/cliente_model', 'cl');
        $this->load->model('local/local_model');
        $this->load->model('producto/producto_model');
        $this->load->model('precio/precios_model', 'precios');
        $this->load->model('proveedor/proveedor_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('ingreso/ingreso_model');
        $this->load->model('impuesto/impuestos_model');
        $this->load->model('detalle_ingreso/detalle_ingreso_model');
        $this->load->model('pagos_ingreso/pagos_ingreso_model');
//pd producto pv proveedor
        $this->load->library('Pdf');
        $this->load->library('phpExcel/PHPExcel.php');
        //$this->very_sesion();
    }

    /* function very_sesion()
    {
        if (!$this->session->userdata('nUsuCodigo')) {
            redirect(base_url() . 'inicio');
        }
    }*/

    function index()
    {

        $idingreso = $this->input->post('idingreso');
        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data["locales"] = $this->local_model->get_all();
        $data["impuestos"] = $this->impuestos_model->get_impuestos();
        $data["lstProducto"] = $this->producto_model->select_all_producto();
        $data["lstProveedor"] = $this->proveedor_model->select_all_proveedor();
        $data['costos'] = !empty($_GET['costos']) ? $_GET['costos'] : $_POST['costos'];

        $data["ingreso"] = array();
        if ($idingreso != FALSE) {
            $data["ingreso"] = $this->ingreso_model->get_ingresos_by(array('ingreso.id_ingreso' => $idingreso));
            $data["detalles"] = $this->ingreso_model->get_detalles_by('ingreso.id_ingreso', $idingreso);
            $data["ingreso"] = $data["ingreso"][0];

        }

        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso/ingresos', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }


    function get_unidades_has_producto()
    {

        $id_producto = $this->input->post('id_producto');
        $data['unidades'] = $this->unidades_model->get_by_producto($id_producto);
        $data['producto'] = $this->producto_model->get_by('producto_id', $id_producto);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function detallesProducto($id = FALSE)
    {
        if ($id != FALSE) {
            $data['productos'] = $this->ingreso_model->get_detalles_by('id_producto', $id);
        }
        $this->load->view('menu/ventas/detallesProducto', $data);
    }

    function registrar_ingreso()
    {
        if ($this->input->is_ajax_request()) {
            $this->form_validation->set_rules('fecEmision', 'fecEmision', 'requiered');
            $this->form_validation->set_rules('doc_serie', 'doc_serie', 'requiered');
            $this->form_validation->set_rules('doc_numero', 'doc_numero', 'requiered');
            $this->form_validation->set_rules('cboTipDoc', 'cboTipDoc', 'requiered');
            $this->form_validation->set_rules('cboProveedor', 'cboProveedor', 'requiered');
            $this->form_validation->set_rules('subTotal', 'subTotal', 'requiered');
            $this->form_validation->set_rules('igv', 'igv', 'requiered');
            $this->form_validation->set_rules('montoigv', 'montoigv', 'requiered');
            $this->form_validation->set_rules('totApagar', 'totApagar', 'requiered');
            //
            if ($this->form_validation->run() == false):
                echo "no guardo";
            else:
                if (isset($_POST['subTotal']) && $_POST['subTotal'] != "" && isset($_POST['montoigv']) && $_POST['montoigv'] != "" && isset($_POST['totApagar']) && $_POST['totApagar'] != "") {

                    if ($this->input->post('costos') === 'true') {
                        $status = INGRESO_COMPLETADO;
                    } else {
                        $status = INGRESO_PENDIENTE;
                    }
                    $comp_cab_pie = array(
                        // 'fecReg' => date("y-m-d", strtotime($this->input->post('fecReg',true))),
                        'fecReg' => date("Y-m-d H:i:s"),
                        'local' => $this->input->post('local', true),
                        'costos' => $this->input->post('costos', true),
                        'fecEmision' => date("y-m-d", strtotime($this->input->post('fecEmision', true))),
                        'documento_vence' => date("y-m-d", strtotime($this->input->post('documento_vence', true))),
                        'doc_serie' => $this->input->post('doc_serie', true),
                        'doc_numero' => $this->input->post('doc_numero', true),
                        'cboTipDoc' => $this->input->post('cboTipDoc', true),
                        'cboProveedor' => $this->input->post('cboProveedor', true),
                        'subTotal' => $this->input->post('subTotal', true),
                        'igv' => $this->input->post('igv', true),
                        'montoigv' => $this->input->post('montoigv', true),
                        'totApagar' => $this->input->post('totApagar', true),
                        'tipo_ingreso' => $this->input->post('tipo_ingreso', true),
                        'pago' => $this->input->post('pago', true),
                        'status' => $status
                    );


                    $id = $this->input->post('id_ingreso', true);

                    if (empty($id)) {
                        if($this->ingreso_model->documento_existe($comp_cab_pie['doc_serie'], $comp_cab_pie['doc_numero'], $comp_cab_pie['cboTipDoc']) == false){
                            $rs = $this->ingreso_model->insertar_compra($comp_cab_pie, json_decode($this->input->post('lst_producto', true)));
                        }
                        else{
                            $rs = false;
                            $json['error'] = 'Error. Este documento ya existe. Por favor cambielo';
                        }
                    } else {
                        $comp_cab_pie['id_ingreso'] = $id;
                        $comp_cab_pie['status'] = INGRESO_COMPLETADO;
                        if($this->ingreso_model->documento_existe($comp_cab_pie['doc_serie'], $comp_cab_pie['doc_numero'], $comp_cab_pie['cboTipDoc'], $id) == false){
                            $rs = $this->ingreso_model->update_compra($comp_cab_pie, json_decode($this->input->post('lst_producto', true)));
                        }
                        else{
                            $rs = false;
                            $json['error'] = 'Error. Este documento ya existe. Por favor cambielo';
                        }
                    }
                    if ($rs != false) {
                        $json['success'] = 'Solicitud Procesada con exito';
                        $json['id'] = $rs;

                    } else {
                        if(!isset($json['error']))
                            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
                    }


                } else {
                    $json['error'] = 'Algunos campos son requeridos';

                }
            endif;
        } else {


            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';


        }
        echo json_encode($json);
    }


    function lst_cuentas_porpagar_json()
    {
        if ($this->input->is_ajax_request()) {
            $id_cliente = null;
            $fechaDesde = null;
            $fechaHasta = null;


            $where = "pago = 'CREDITO'";
            $where = $where . " AND ingreso_status = '" . COMPLETADO . "'";


            $nombre_or = false;
            $where_or = false;
            // Pagination Result
            $array = array();
            $array['productosjson'] = array();

            $total = 0;
            $start = 0;
            $limit = false;

            $draw = $this->input->get('draw');
            if (!empty($draw)) {

                $start = $this->input->get('start');
                $limit = $this->input->get('length');
            }

            if ($this->input->get('proveedor', true) != -1) {

                $where = $where . " AND int_Proveedor_id= '" . $this->input->get('proveedor', true) . "'";

            }
            if ($_GET['fecIni'] != "") {

                $where = $where . " AND date(fecha_registro) >= '" . date('Y-m-d', strtotime($this->input->get('fecIni'))) . "'";
            }
            if ($_GET['fecFin'] != "") {

                $where = $where . " AND  date(fecha_registro) <= '" . date('Y-m-d', strtotime($this->input->get('fecFin'))) . "'";
            }


            $select = 'ingreso.*, pagos_ingreso.*, proveedor.*, sum(pagoingreso_monto) as suma';
            $from = "ingreso";
            $join = array('proveedor', 'pagos_ingreso');
            $campos_join = array('proveedor.id_proveedor=ingreso.int_Proveedor_id', 'pagos_ingreso.pagoingreso_ingreso_id=ingreso.id_ingreso');

            $tipo_join[0] = "left";
            $tipo_join[1] = "left";

            $group = "id_ingreso";

            $where_custom = false;
            $ordenar = $this->input->get('order');
            $order = false;
            $order_dir = 'desc';
            if (!empty($ordenar)) {
                $order_dir = $ordenar[0]['dir'];
                if ($ordenar[0]['column'] == 0) {
                    $order = 'ingreso.id_ingreso';
                }
                if ($ordenar[0]['column'] == 1) {
                    $order = 'tipo_documento';
                }
                if ($ordenar[0]['column'] == 2) {
                    $order = 'documento_numero';
                }
                if ($ordenar[0]['column'] == 3) {
                    $order = 'proveedor_nombre';
                }
                if ($ordenar[0]['column'] == 4) {
                    $order = 'fecha_registro';
                }
                if ($ordenar[0]['column'] == 5) {
                    $order = 'total_ingreso';
                }
                if ($ordenar[0]['column'] == 6) {
                    $order = 'total_ingreso';
                }
                if ($ordenar[0]['column'] == 7) {
                    $order = 'total_ingreso';
                }


            }


            $nombre_in = false;
            $where_in = false;
            $total = $this->ingreso_model->traer_by_mejorado('COUNT(ingreso.id_ingreso) as total', $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, $group, $order, "RESULT_ARRAY", false, false, $order_dir, false, $where_custom);

            $cuentas = $this->ingreso_model->traer_by_mejorado($select, $from, $join, $campos_join, $tipo_join, $where,
                $nombre_in, $where_in, $nombre_or, $where_or, $group, $order, "RESULT_ARRAY", $limit, $start, $order_dir, false, $where_custom);

            if (count($cuentas) > 0) {

                foreach ($cuentas as $v) {


                    $PRODUCTOjson = array();

                    $PRODUCTOjson[] = $v['id_ingreso'];
                    $PRODUCTOjson[] = $v['tipo_documento'];
                    $PRODUCTOjson[] = $v['documento_serie'] . "-" . $v['documento_numero'];
                    $PRODUCTOjson[] = $v['proveedor_nombre'];
                    $PRODUCTOjson[] = date("d-m-Y", strtotime($v['fecha_registro']));
                    $PRODUCTOjson[] = number_format($v['total_ingreso'], 2);

                    if ($v['suma'] != null) {
                        $abono = $v['suma'];
                    } else {
                        $abono = "0.00";
                    }
                    $PRODUCTOjson[] = $abono;

                    if ($v['suma'] != null) {
                        $deuda = number_format($v['total_ingreso'] - $v['suma'], 2);
                    } else {
                        $deuda = number_format($v['total_ingreso'], 2);
                    }
                    $PRODUCTOjson[] = $deuda;


                    $days = (strtotime(date('d-m-Y')) - strtotime($v['fecha_registro'])) / (60 * 60 * 24);
                    if ($days < 0)
                        $days = 0;
                    $label = "<div><label class='label ";
                    if (floor($days) < 8 or $v['suma'] >= $v['total_ingreso']) {
                        $label .= "label-success";
                    } elseif (floor($days) < 16) {
                        $label .= "label-info";
                    } else {
                        $label .= "label-warning";
                    }
                    $label .= "'>" . floor($days) . "</label></div>";
                    $PRODUCTOjson[] = $label;
                    $PRODUCTOjson[] = ($v['suma'] >= $v['total_ingreso']) ? PAGO_CANCELADO : INGRESO_PENDIENTE;


                    $botonas = '<div class="btn-group">';
                    // $botonas =.'<a class="btn btn-default tip" title="Ver Venta" onclick="visualizar(\'' . $v["id_ingreso"] . '\')" ><i class="fa fa-search"></i> Ver</a>';

                    if ($v['suma'] < $v['total_ingreso']) {
                        $botonas .= '<a onclick="pagar_venta(\'' . $v['id_ingreso'] . '\')" class="btn btn-default tip" title="Pagar"><i
                                class="fa fa-paypal"></i> Pagar</a>';
                    }
                    $botonas .= '
                    </div>';
                    $PRODUCTOjson[] = $botonas;
                    $array['productosjson'][] = $PRODUCTOjson;

                }
                $total = $total[0]['total'];
            }
            $array['data'] = $array['productosjson'];
            $array['draw'] = $draw;//esto debe venir por post
            $array['recordsTotal'] = $total;
            $array['recordsFiltered'] = $total; // esto dbe venir por post

            echo json_encode($array);
        } else {
            redirect(base_url() . 'venta/', 'refresh');
        }
    }


    function lst_cuentas_porpagar()
    {
        $params = array();
        if ($this->input->post('proveedor', true) != -1) {
            $params['proveedor_id'] = $this->input->post('proveedor', true);
        }

        if ($this->input->post('documento', true) != -1) {
            $params['documento'] = $this->input->post('documento', true);
        }

        $data["lstproveedor"] = $this->proveedor_model->get_cuentas_pagar($params);


        if ($this->input->is_ajax_request()) {

            $this->load->view('menu/proveedor/tbl_lst_cuentasporpagar', $data);

        } else {
            redirect(base_url() . 'proveedor/', 'refresh');
        }
    }

    public function ver_deuda()
    {
        $id_ingreso = $this->input->post('id_ingreso');

        if ($id_ingreso != FALSE) {

            $result['ingreso'] = $this->ingreso_model->get_deuda_detalle($id_ingreso);
            $result['metodos_pago'] = $this->db->get_where('metodos_pago', array('status_metodo' => 1))->result();
            $result['bancos'] = $this->db->get_where('banco', array('banco_status' => 1))->result();


            $this->load->view('menu/proveedor/form_montoapagar', $result);
        }
    }

    function imprimir_pago_pendiente()
    {

        if ($this->input->is_ajax_request()) {

            $id_historial = json_decode($this->input->post('id_historial', true));
            $id_ingreso = json_decode($this->input->post('ingreso_id', true));

            $where = array(
                'id_ingreso' => $id_ingreso
            );
            $select = 'ingreso.*, proveedor.*, pagos_ingreso.*, sum(pagoingreso_monto) as suma';
            $from = "ingreso";
            $join = array('pagos_ingreso', 'proveedor');
            $campos_join = array('pagos_ingreso.pagoingreso_ingreso_id=ingreso.id_ingreso', 'proveedor.id_proveedor=int_Proveedor_id');

            $group = " id_ingreso";
            $dataresult['cuentas'] = $this->ingreso_model->traer_by($select, $from, $join, $campos_join, false, $where, false, false, false, false, $group, false, "RESULT_ARRAY");

//  var_dump($dataresult);
            $dataresult['id_historial'] = true;
            $dataresult['cuota'] = $dataresult['cuentas'][0]['pagoingreso_monto'];

///////////////////busco lo que resta de deuda
            $where = array(
                'pagoingreso_ingreso_id' => $id_ingreso,
                'pagoingreso_id' => $id_historial
            );
            $select = 'pagoingreso_restante';
            $from = "pagos_ingreso";
            $order = "pagoingreso_fecha desc";
            $buscar_restante = $this->pagos_ingreso_model->traer_by($select, $from, false, false, $where, false, $order, "RESULT_ARRAY");

            $dataresult['restante'] = $buscar_restante[0]['pagoingreso_restante'];

            $this->load->view('menu/proveedor/visualizarIngresoPendiente', $dataresult);
        }


    }

    function guardarPago()
    {

        if ($this->input->is_ajax_request()) {

            $detalle = array(
                'pagoingreso_ingreso_id' => $this->input->post('ingreso_id'),
                'pagoingreso_fecha' => date("Y-m-d H:i:s"),
                'pagoingreso_monto' => number_format($this->input->post('cantidad_a_pagar'), 2, '.', ''),
                'pagoingreso_restante' => number_format($this->input->post('total_pendiente') - $this->input->post('cantidad_a_pagar'), 2, '.', ''),
                'medio_pago_id' => $this->input->post('pago_id'),
                'banco_id' => $this->input->post('banco_id', NULL),
                'operacion' => $this->input->post('num_oper', NULL)
                );

            $save_historial = $this->pagos_ingreso_model->guardar($detalle);

            $json = array();
            if ($save_historial != false) {
                if ($save_historial != false) {
                    $json['success'] = 'success';
                    $json['ingreso_id'] = $detalle['pagoingreso_ingreso_id'];
                    $json['id_historial'] = $save_historial;
                } else {
                    $json['error'] = 'error';
                }
            }

            echo json_encode($json);

        }
    }

    public function vertodoingreso()
    {
        $id_ingreso = $this->input->post('id_ingreso');


        if ($id_ingreso != FALSE) {


            $select = 'ingreso.documento_serie,ingreso.documento_numero, ingreso.fecha_registro,ingreso.id_ingreso, detalleingreso.cantidad,
                detalleingreso.precio, detalleingreso.total_detalle,
                producto.producto_nombre, proveedor.proveedor_nombre';
            $from = "ingreso";
            $join = array('detalleingreso', 'producto', 'proveedor');
            $campos_join = array('detalleingreso.id_ingreso=ingreso.id_ingreso', 'detalleingreso.id_producto=producto.producto_id',
                'proveedor.id_proveedor=ingreso.int_Proveedor_id');

            /* $tipo_join[0]="";
            $tipo_join[1]="left";*/

            $where = array(
                'ingreso.id_ingreso' => $id_ingreso
            );

            $dataresult['detalle'] = $this->ingreso_model->traer_by($select, $from, $join, $campos_join, false, $where, false, false, false, false, false, false, "RESULT_ARRAY");


            $select = 'sum(pagoingreso_monto) as monto_abonado';
            $from = "pagos_ingreso";
            $where = array(
                'pagoingreso_ingreso_id' => $id_ingreso
            );
            $dataresult['abonado'] = $this->ingreso_model->traer_by($select, $from, false, false, false, $where, false, false, false, false, false, false, "ROW_ARRAY");


            $select = 'sum(total_detalle) as total_ingreso ';
            $from = "ingreso";
            $join = array('detalleingreso');
            $campos_join = array('detalleingreso.id_ingreso=ingreso.id_ingreso');
            $where = array(
                'ingreso.id_ingreso' => $id_ingreso
            );

            $dataresult['total_ingreso'] = $this->ingreso_model->traer_by($select, $from, $join, $campos_join, false, $where, false, false, false, false, false, false, "ROW_ARRAY");


            $select = '*';
            $from = "pagos_ingreso";
            $where = array(
                'pagoingreso_ingreso_id' => $id_ingreso
            );
            $dataresult['cuentas'] = $this->ingreso_model->traer_by($select, $from, false, false, false, $where, false, false, false, false, false, false, "RESULT_ARRAY");


            $this->load->view('menu/ingreso/ingresos', $dataresult);
        }
    }

    function lst_reg_ingreso()
    {
        if ($this->input->is_ajax_request()) {
//$data['lstCompra'] = $this->v->select_compra(date("y-m-d", strtotime($this->input->post('fecIni',true))),date("y-m-d", strtotime($this->input->post('fecFin',true))));
//$this->load->view('menu/ventas/tbl_listareg_compra',$data);
            echo json_encode($this->ingreso_model->select_compra(date("y-m-d", strtotime($this->input->post('fecIni', true))), date("y-m-d", strtotime($this->input->post('fecFin', true)))));
        } else {
            redirect(base_url() . 'ingresos/', 'refresh');
        }
    }

    function consultar()
    {

        $data['locales'] = $this->local_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso/consultar_ingreso', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function devolucion()
    {


        $data['locales'] = $this->local_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/ingreso/devolucion', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function anular_ingreso()
    {

        if ($this->ingreso_model->consultar_devolucion()) {
            $resultado = $this->ingreso_model->anular_ingreso();
            if ($resultado) {

                $json['success'] = 'Se ha anulado exitosamente';

            } else {
                $json['error'] = 'Ha ocurrido un error al anular el ingreso';

            }
        } else {

            $json['error'] = 'No se puede devolver el ingreso, uno de los productos devueltos no tiene stock suficiente';
                  }

        echo json_encode($json);
    }


    function get_ingresos()
    {
        $condicion = array();
        if ($this->input->post('id_local') != "seleccione") {
            $condicion = array('local_id' => $this->input->post('id_local'));
            $data['local_id'] = $this->input->post('id_local');
        }
        if ($this->input->post('status') != "seleccione") {
            $condicion['ingreso_status'] = $this->input->post('status');
            $data['status'] = $this->input->post('status');
        }

        $mes = $this->input->post('mes');
        $year = $this->input->post('year');
        $dia_min = $this->input->post('dia_min');
        $dia_max = $this->input->post('dia_max');

        $desde = "";
        $hasta = "";

        if($mes != "" && $year != "" && $dia_min != "" && $dia_max != ""){
            $last_day = last_day($year, sumCod($mes, 2));
            if($last_day > $dia_max)
                $last_day = $dia_max;

            $desde = $year . '-' . sumCod($mes, 2) . '-'. $dia_min. " 00:00:00";
            $hasta = $year . '-' . sumCod($mes, 2) . '-' . $last_day . " 23:59:59";
        }

        if ($desde != "") {
            $condicion['fecha_registro >= '] = $desde;
            $data['fecha_desde'] = $desde;
        }
        if ($hasta != "") {
            $condicion['fecha_registro <='] = $hasta;
            $data['fecha_hasta'] = $hasta;
        }



        if ($this->input->post('anular') != 0) {

            $data['anular'] = 1;
        }
        $data['ingresos'] = $this->ingreso_model->get_ingresos_by($condicion);
        $data['ingreso_totales'] = $this->ingreso_model->sum_ingresos_by($condicion);

        $this->load->view('menu/ingreso/lista_ingreso', $data);

    }

    function form($id = FALSE, $local = false)
    {

        $data = array();
        if ($id != FALSE and $local != false) {
            $data['detalles'] = $this->detalle_ingreso_model->get_by_result('detalleingreso.id_ingreso', $id);
            $data['id_detalle'] = $id;

            $select = array('*');
            $join = array('ingreso', 'local');
            $campos_join = array('ingreso.id_ingreso=detalleingreso.id_ingreso', 'local.int_local_id=ingreso.local_id');
            $where = array('detalleingreso.id_ingreso' => $id, 'local_id' => $local);
            $group = array('local_id');
// $data['local']=$this->detalle_ingreso_model->get_by('local_nombre', $local);
            $data['local'] = $this->detalle_ingreso_model->get_by($select, $join, $campos_join, $where, $group, true);

        }


        $this->load->view('menu/ingreso/form_detalle_ingreso', $data);
    }

    function cuentasporpagarexcel($fecha_ini = false, $fecha_fin = false, $proveedor = false)
    {

        if ($proveedor != false and $proveedor != -1) {

            $where = array(
                'int_Proveedor_id' => $this->input->post('proveedor', true)
            );
        }
        if ($fecha_ini != false and $fecha_ini != "") {

            $where['fecha_registro >='] = date('Y-m-d', strtotime($fecha_ini));
        }

        if ($fecha_fin != false and $fecha_fin != "") {

            $where['fecha_registro <='] = date('Y-m-d', strtotime($fecha_fin));

        }

        $where['pago'] = "CREDITO";
        $select = 'ingreso.*, pagos_ingreso.*, proveedor.*, sum(pagoingreso_monto) as suma ';
        $from = "ingreso";
        $join = array('proveedor', 'pagos_ingreso');
        $campos_join = array('proveedor.id_proveedor=ingreso.int_Proveedor_id', 'pagos_ingreso.pagoingreso_ingreso_id=ingreso.id_ingreso');

        $tipo_join[0] = "";
        $tipo_join[1] = "left";

        $group = " id_ingreso";
        $order = "fecha_registro desc";
        $cuentas = $this->ingreso_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where, false, false, false, false, $group, $order, "RESULT_ARRAY");


// configuramos las propiedades del documento
        $this->phpexcel->getProperties()
//->setCreator("Arkos Noem Arenom")
//->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Reporte de Invetario")
            ->setSubject("Reporte de Invetario")
            ->setDescription("Reporte de Invetario")
            ->setKeywords("Reporte de Invetario")
            ->setCategory("Reporte de Invetario");


        $columna[0] = "Documento";
        $columna[1] = "Proveedor";
        $columna[2] = "Fecha Reg.";
        $columna[3] = "Tipo de Doc.";
        $columna[4] = "Monto Ingreso " . MONEDA;
        $columna[5] = "Monto abonado" . MONEDA;
        $columna[6] = "Monto Deudor" . MONEDA;
        $columna[7] = "Dias de atraso";
        $columna[8] = "Estatus";


        $col = 0;
        for ($i = 0; $i < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }

        $row = 2;
        if (count($cuentas) > 0) {

            foreach ($cuentas as $cuenta) {
                $col = 0;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cuenta['documento_serie'] . "-" . $cuenta['documento_numero']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cuenta['proveedor_nombre']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, date("d-m-Y", strtotime($cuenta['fecha_registro'])));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cuenta['tipo_documento']);

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, $cuenta['total_ingreso']);

                if ($row['suma'] != null) {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $cuenta['suma']);
                } else {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, "0");
                }


                if ($row['suma'] != null) {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $cuenta['total_ingreso'] - $cuenta['suma']);
                } else {
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col++, $row, $cuenta['total_ingreso']);
                }


                $days = (strtotime(date('d-m-Y')) - strtotime($cuenta['fecha_registro'])) / (60 * 60 * 24);;

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, floor($days));

                $this->phpexcel->setActiveSheetIndex(0)
                    ->setCellValueByColumnAndRow($col++, $row, "PENDIENTE");

                $row++;
            }
        }

// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Cuentas por Pagar');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="CuentasporPagar.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }

    function cuentasporpagarpdf($fecha_ini = false, $fecha_fin = false, $proveedor = false)
    {


        if ($proveedor != false and $proveedor != -1) {

            $where = array(
                'int_Proveedor_id' => $this->input->post('proveedor', true)
            );
        }
        if ($fecha_ini != false and $fecha_ini != "") {

            $where['fecha_registro >='] = date('Y-m-d', strtotime($fecha_ini));
        }

        if ($fecha_fin != false and $fecha_fin != "") {

            $where['fecha_registro <='] = date('Y-m-d', strtotime($fecha_fin));

        }

        $where['pago'] = "CREDITO";
        $select = 'ingreso.*, pagos_ingreso.*, proveedor.*, sum(pagoingreso_monto) as suma ';
        $from = "ingreso";
        $join = array('proveedor', 'pagos_ingreso');
        $campos_join = array('proveedor.id_proveedor=ingreso.int_Proveedor_id', 'pagos_ingreso.pagoingreso_ingreso_id=ingreso.id_ingreso');

        $tipo_join[0] = "";
        $tipo_join[1] = "left";

        $group = " id_ingreso";
        $order = "fecha_registro desc";
        $cuentas = $this->ingreso_model->traer_by($select, $from, $join, $campos_join, $tipo_join, $where, false, false, false, false, $group, $order, "RESULT_ARRAY");


        $pdf = new Pdf('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->setPageOrientation('L');
        // $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetTitle('Cuentas por Pagar');
        // $pdf->SetSubject('FICHA DE MIEMBROS');
        $pdf->SetPrintHeader(false);
//echo K_PATH_IMAGES;
// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config_alt.php de libraries/config
        // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "AL.â€¢.G.â€¢.D.â€¢.G.â€¢.A.â€¢.D.â€¢.U.â€¢.<br>Gran Logia de la RepÃºblica de Venezuela", "Gran Logia de la <br> de Venezuela", array(0, 64, 255), array(0, 64, 128));


        $pdf->setFooterData($tc = array(0, 64, 0), $lc = array(0, 64, 128));

// datos por defecto de cabecera, se pueden modificar en el archivo tcpdf_config.php de libraries/config

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        $pdf->SetMargins(PDF_MARGIN_LEFT, 0, PDF_MARGIN_RIGHT);
        //  $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// se pueden modificar en el archivo tcpdf_config.php de libraries/config
        //  $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

//relaciÃ³n utilizada para ajustar la conversiÃ³n de los pÃ­xeles
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);


// ---------------------------------------------------------
// establecer el modo de fuente por defecto
        $pdf->setFontSubsetting(true);

// Establecer el tipo de letra

//Si tienes que imprimir carÃ¡cteres ASCII estÃ¡ndar, puede utilizar las fuentes bÃ¡sicas como
// Helvetica para reducir el tamaÃ±o del archivo.
        $pdf->SetFont('helvetica', '', 14, '', true);

// AÃ±adir una pÃ¡gina
// Este mÃ©todo tiene varias opciones, consulta la documentaciÃ³n para mÃ¡s informaciÃ³n.
        $pdf->AddPage();

        $pdf->SetFontSize(8);

        $textoheader = "";
        $pdf->writeHTMLCell(
            $w = 0, $h = 0, $x = '60', $y = '',
            $textoheader, $border = 0, $ln = 1, $fill = 0,
            $reseth = true, $align = 'C', $autopadding = true);

//fijar efecto de sombra en el texto
//        $pdf->setTextShadow(array('enabled' => true, 'depth_w' => 0.2, 'depth_h' => 0.2, 'color' => array(196, 196, 196), 'opacity' => 1, 'blend_mode' => 'Normal'));

        $pdf->SetFontSize(12);

        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', "<br><br><b><u>Cuentas por Pagar</u></b><br><br>", $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = 'C', $autopadding = true);


        //preparamos y maquetamos el contenido a crear
        $html = '';
        $html .= "<style type=text/css>";
        $html .= "th{color: #000; font-weight: bold; background-color: #CED6DB; }";
        $html .= "td{color: #222; font-weight: bold; background-color: #fff;}";
        $html .= "table{border:0.2px}";
        $html .= "body{font-size:15px}";
        $html .= "</style>";


        $html .= "<br><b>Cuentas por pagar:</b> " . "<br>";


        $html .= "<table>";

        $html .= "<tr> <th class='tip' title='Documento'> Documento</th><th>Proveedor</th>";
        $html .= "<th class='tip' title='Fecha Registro'>Fecha Reg.</th><th class='tip' >Tipo de Doc.</th>";
        $html .= "<th class='tip' >Monto Ingreso" . MONEDA . "</th><th class='tip' >Monto abonado " . MONEDA . "</th>";
        $html .= "<th class='tip' >Monto Deudor " . MONEDA . "</th><th>D&iacute;as de atraso </th>";
        $html .= " <th class='tip' >Estatus</th></tr>";
        if (count($cuentas > 0)) {
            foreach ($cuentas as $row) {
                $html .= " <tr><td style='text-align: center;'>" . $row['documento_serie'] . "-" . $row['documento_numero'] . "</td>";
                $html .= " <td>" . $row['proveedor_nombre'] . "</td>";
                $html .= "<td style='text-align: center;'>" . date('d-m-Y', strtotime($row['fecha_registro'])) . "</td>";
                $html .= "<td style='text-align: center;'>" . $row['tipo_documento'] . "</td>";
                $html .= "<td style='text-align: center;'>" . $row['total_ingreso'] . "</td>";
                $html .= "<td style='text-align: center;'>";
                if ($row['suma'] != null) {
                    $html .= $row['suma'];
                } else {
                    $html .= "0";
                }
                $html .= "</td>";
                $html .= " <td style='text-align: center;'>";
                if ($row['suma'] != null) {
                    $html .= $row['total_ingreso'] - $row['suma'];
                } else {
                    $html .= $row['total_ingreso'];
                }
                $html .= "</td>";

                $html .= "<td style='text-align: center;'>";
                $days = (strtotime(date('d-m-Y')) - strtotime($row['fecha_registro'])) / (60 * 60 * 24);
                $html .= "<div ";
                if (floor($days) < 8) {
                    $html .= " style='color: #00CC00' ";
                } elseif (floor($days) < 16) {
                    $html .= " style='color: gold'";
                } else {
                    $html .= "style='color: #ff0000'";
                }
                $html .= " >";
                $html .= floor($days);
                $html .= "</div>";
                $html .= " </td>";
                $html .= " <td style='text-align: center;'>PENDIENTE</td>";
                $html .= "</tr>";

            }
        }

        $html .= "</table>";

// Imprimimos el texto con writeHTMLCell()
        $pdf->writeHTMLCell($w = 0, $h = 0, $x = '', $y = '', $html, $border = 0, $ln = 1, $fill = 0, $reseth = true, $align = '', $autopadding = true);

// ---------------------------------------------------------
// Cerrar el documento PDF y preparamos la salida
// Este mÃ©todo tiene varias opciones, consulte la documentaciÃ³n para mÃ¡s informaciÃ³n.
        $nombre_archivo = utf8_decode("Cuentasporpagar.pdf");
        $pdf->Output($nombre_archivo, 'D');

    }

    function pdf($local, $estado, $year, $mes, $dia_min, $dia_max)
    {
        $this->load->library('mpdf53/mpdf');
        $mpdf = new mPDF('utf-8', 'A4-L');

        $condicion = array();
        if ($local != "seleccione") {
            $condicion = array('local_id' => $local);
        }
        if ($estado != "seleccione") {
            $condicion['ingreso_status'] = $estado;
        }

        $desde = "";
        $hasta = "";

        if($mes != "" && $year != "" && $dia_min != "" && $dia_max != ""){
            $last_day = last_day($year, sumCod($mes, 2));
            if($last_day > $dia_max)
                $last_day = $dia_max;

            $desde = $year . '-' . sumCod($mes, 2) . '-'. $dia_min. " 00:00:00";
            $hasta = $year . '-' . sumCod($mes, 2) . '-' . $last_day . " 23:59:59";
        }

        if ($desde != "") {
            $condicion['fecha_registro >= '] = $desde;
            $data['fecha_desde'] = $desde;
        }
        if ($hasta != "") {
            $condicion['fecha_registro <='] = $hasta;
            $data['fecha_hasta'] = $hasta;
        }



        if ($this->input->post('anular') != 0) {

            $data['anular'] = 1;
        }
        $data['ingresos'] = $this->ingreso_model->get_ingresos_by($condicion);
        $data['ingreso_totales'] = $this->ingreso_model->sum_ingresos_by($condicion);

        $html = $this->load->view('menu/ingreso/lista_ingreso_pdf', $data, true);
        $mpdf->WriteHTML($html);
        $mpdf->Output();

    }

    function excel($local, $fecha_desde, $fecha_hasta, $detalle)
    {


        if ($local != 0 and $detalle == 0) {
            $condicion = array('local_id' => $local);
        }
        if ($fecha_desde != 0) {

            $condicion['fecha_registro >= '] = date('Y-m-d', strtotime($fecha_desde)) . " " . date('H:i:s');
        }
        if ($fecha_hasta != 0) {

            $condicion['fecha_registro <='] = date('Y-m-d', strtotime($fecha_hasta)) . " " . date('H:i:s');
        }

        if ($detalle != 0 and $local != 0) {

            $compras = $this->detalle_ingreso_model->get_by_result('detalleingreso.id_ingreso', $detalle);

            $select = array('*');
            $join = array('ingreso', 'local');
            $campos_join = array('ingreso.id_ingreso=detalleingreso.id_ingreso', 'local.int_local_id=ingreso.local_id');
            $where = array('detalleingreso.id_ingreso' => $detalle, 'local_id' => $local);
            $group = array('local_id');
            $local_detalle = $this->detalle_ingreso_model->get_by($select, $join, $campos_join, $where, $group, true);
        } else {


            $ingresos = $this->ingreso_model->get_ingresos_by($condicion);

        }
// configuramos las propiedades del documento
        $this->phpexcel->getProperties()
//->setCreator("Arkos Noem Arenom")
//->setLastModifiedBy("Arkos Noem Arenom")
            ->setTitle("Ingresos")
            ->setSubject("Ingresos")
            ->setDescription("Ingresos")
            ->setKeywords("Ingresos")
            ->setCategory("Ingresos");

        if (isset($ingresos)) {
            $columna[0] = "NUMERO DE DOCUMENTO";
            $columna[1] = "TIPO DE DOCUMENTO";
            $columna[2] = "FECHA REGISTRO";
            $columna[3] = "FECHA EMISION";
            $columna[4] = "PROVEEDOR";
            $columna[5] = "RESPONSABLE";
            $columna[6] = "LOCAL";
            $columna[7] = "TOTAL";


            $columnas[0] = "documento_serie";
            $columnas[1] = "tipo_documento";
            $columnas[2] = "fecha_registro";
            $columnas[3] = "fecha_emision";
            $columnas[4] = "proveedor_nombre";
            $columnas[5] = "nombre";
            $columnas[6] = "local_nombre";
            $columnas[7] = "total_ingreso";

        } elseif (isset($compras)) {

            $columna[0] = "ID";
            $columna[1] = "PRODUCTO";
            $columna[2] = "CANTIDAD";
            $columna[3] = "PRECIO";
            $columna[4] = "UNIDAD DE MEDIDA";


            $columnas[0] = "id_detalle_ingreso";
            $columnas[1] = "producto_nombre";
            $columnas[2] = "cantidad";
            $columnas[3] = "precio";
            $columnas[4] = "nombre_unidad";
        }
        $col = 0;
        for ($i = 0; $i
        < count($columna); $i++) {

            $this->phpexcel->setActiveSheetIndex(0)
                ->setCellValueByColumnAndRow($i, 1, $columna[$i]);

        }


        if (isset($ingresos)) {
            $row = 2;
            foreach ($ingresos as $ingresos) {
                $col = 0;
                foreach ($columnas as $columna) {

                    if ($columna == ("fecha_emision") or $columna == ("fecha_registro")) {
                        $logia[$columna] = date('d-m-Y H:i:s', strtotime($ingresos->$columna));
                    }
                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col, $row, $ingresos->$columna);
                    $col++;
                }

                $row++;
            }
        } elseif (isset($compras)) {

            $row = 2;
            foreach ($compras as $compra) {
                $col = 0;
                foreach ($columnas as $columna) {


                    $this->phpexcel->setActiveSheetIndex(0)
                        ->setCellValueByColumnAndRow($col, $row, $compra->$columna);
                    $col++;
                }

                $row++;
            }
        }

// Renombramos la hoja de trabajo
        $this->phpexcel->getActiveSheet()->setTitle('Ingreso');


// configuramos el documento para que la hoja
// de trabajo nÃºmero 0 sera la primera en mostrarse
// al abrir el documento
        $this->phpexcel->setActiveSheetIndex(0);


// redireccionamos la salida al navegador del cliente (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Ingresos.xlsx"');
        header('Cache-Control: max-age=0');

        $objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
        $objWriter->save('php://output');

    }
}
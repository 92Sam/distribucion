<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class banco extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->model('banco/banco_model');
    }

    function fiscal()
    {

        $this->load->model('venta/venta_fiscal_model');

        $productos[] = array(
            'producto_id' => 126,
            'unidad_id' => 6,
            'precio' => 8,
            'cantidad' => 100,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 127,
            'unidad_id' => 6,
            'precio' => 73,
            'cantidad' => 30,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 44,
            'unidad_id' => 6,
            'precio' => 11,
            'cantidad' => 100,
            'bono' => 0,
        );


        $productos[] = array(
            'producto_id' => 44,
            'unidad_id' => 6,
            'precio' => 0,
            'cantidad' => 3,
            'bono' => 1,
        );
//
        $productos[] = array(
            'producto_id' => 125,
            'unidad_id' => 6,
            'precio' => 0,
            'cantidad' => 11,
            'bono' => 1,
        );

        $productos[] = array(
            'producto_id' => 1,
            'unidad_id' => 1,
            'precio' => 71,
            'cantidad' => 50,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 2,
            'unidad_id' => 1,
            'precio' => 3,
            'cantidad' => 5,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 3,
            'unidad_id' => 1,
            'precio' => 55,
            'cantidad' => 100,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 4,
            'unidad_id' => 1,
            'precio' => 0,
            'cantidad' => 30,
            'bono' => 0,
        );


        $productos[] = array(
            'producto_id' => 5,
            'unidad_id' => 1,
            'precio' => 0,
            'cantidad' => 35,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 6,
            'unidad_id' => 1,
            'precio' => 10.20,
            'cantidad' => 120,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 7,
            'unidad_id' => 1,
            'precio' => 10,
            'cantidad' => 55,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 8,
            'unidad_id' => 1,
            'precio' => 16.50,
            'cantidad' => 100,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 9,
            'unidad_id' => 1,
            'precio' => 15.50,
            'cantidad' => 100,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 10,
            'unidad_id' => 1,
            'precio' => 18.80,
            'cantidad' => 125,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 11,
            'unidad_id' => 1,
            'precio' => 41.50,
            'cantidad' => 45,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 12,
            'unidad_id' => 1,
            'precio' => 25.50,
            'cantidad' => 20,
            'bono' => 0,
        );

        $productos[] = array(
            'producto_id' => 13,
            'unidad_id' => 1,
            'precio' => 0,
            'cantidad' => 9,
            'bono' => 1,
        );

        $productos[] = array(
            'producto_id' => 14,
            'unidad_id' => 1,
            'precio' => 0,
            'cantidad' => 3,
            'bono' => 1,
        );

        $productos[] = array(
            'producto_id' => 15,
            'unidad_id' => 1,
            'precio' => 0,
            'cantidad' => 3,
            'bono' => 1,
        );


        //configura los parametros aqui
        $params = array(
            'max_items' => 10,
            'max_importe' => 0
        );
        //doc pueder ser 'BOLETA' o 'FACTURA'

        $total_pedido = 0;
        foreach ($productos as $p)
            $total_pedido += $p['cantidad'] * $p['precio'];

        echo 'TOTAL PEDIDO: '.$total_pedido.'<br><br>';

        $data = $this->venta_fiscal_model->split_documents($params, $productos);

        $total_boleta = 0;
        foreach ($data as $key => $prods) {
            echo 'DOC: '.($key + 1).'<br>';
            $total_importe = 0;
            foreach ($prods as $d) {
                echo 'Prod id: ' . $d['producto_id']. ($d['bono'] == 1 ? " -- Bono" : "") . '<br>';
                echo 'Cantidad: ' . $d['cantidad'] . '<br>';
                echo 'Precio: ' . $d['precio'] . '<br>';
                echo 'Importe: ' . $d['importe'] . '<br>';
                $total_importe += $d['importe'];
                echo '<br>';
            }
            echo 'TOTAL: '.$total_importe;
            $total_boleta += $total_importe;
            echo '<br>--------------------------------<br><br>';
        }

        echo 'TOTAL BOLETAS: '.$total_boleta;

    }


    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data["banco"] = $this->banco_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/banco/banco', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        if ($id != FALSE) {
            $data['banco'] = $this->banco_model->get_by('banco_id', $id);
            $data['caja_actual'] = $this->db->select('caja.*')->from('caja')
                ->join('caja_desglose', 'caja_desglose.caja_id = caja.id')
                ->where('caja_desglose.id', $data['banco']['cuenta_id'])->get()->row();
        } else
            $data['caja_actual'] = $this->db->get_where('caja', array('moneda_id' => '1', 'local_id' => $this->session->userdata('int_local_id')))->row();

        $data['cajas'] = $this->db->get_where('caja', array('estado' => 1))->result();
        $data['caja_cuentas'] = $this->db->get_where('caja_desglose', array('estado' => 1))->result();
        $this->load->view('menu/banco/form', $data);
    }


    function guardar()
    {

        $id = $this->input->post('id');

        $banco = array(
            'banco_nombre' => $this->input->post('nombre'),
            'banco_numero_cuenta' => $this->input->post('nro_cuenta'),
            'banco_saldo' => $this->input->post('saldo'),
            'banco_cuenta_contable' => $this->input->post('cuenta_contable'),
            'banco_titular' => $this->input->post('titular'),
            'banco_status' => 1,
            'cuenta_id' => $this->input->post('cuentas_select')
        );

        if (empty($id)) {
            $resultado = $this->banco_model->insertar($banco);

        } else {
            $banco['banco_id'] = $id;
            $resultado = $this->banco_model->update($banco);
        }

        if ($resultado == TRUE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la Solicitud';
        }

        echo json_encode($json);

    }


    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $banco = array(
            'banco_id' => $id,
            'banco_status' => 0

        );

        $data['resultado'] = $this->banco_model->update($banco);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar el Banco';
        }

        echo json_encode($json);
    }

    //Funcion para validar numero de operacion

    function validaNumeroOperacion($num_operacion)
    {
        $resultado = $this->banco_model->buscarNumeroOperacion($num_operacion);

        $json = array();
        if ($resultado == true) {
            $json['error'] = '1';
        }

        echo json_encode($json);

    }

    function DniRucEnBd()
    {
        $resultado = $this->cliente_model->DniRucEnBd($this->input->post('dni_ruc'), !empty($_POST['cliente_id']) ? $_POST['cliente_id'] : '');

        $json = array();
        if ($resultado == true) {
            $json['error'] = '1';
        }

        echo json_encode($json);

    }
}
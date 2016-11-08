<?php

class estadisticas extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('venta/venta_model');


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


        $data = array();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/estadisticas/utilidades', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }

    }

    function pedidoZona()
    {

        $condicion['venta_status'] = "COMPLETADO";
        $condicion['venta_tipo'] = "ENTREGA";

        $retorno = "RESULT";
        $select = "(SELECT count(venta.venta_id)) as suma, usuario.nombre, detalle_venta.*,venta.*, producto.*,grupos.*,familia.*,lineas.*,unidades.*,usuario_has_zona.*,
                    zonas.*,ciudades.*,cliente.razon_social";
        $group = "zona_id";
        //   $group = "producto_id";
        //   $group = "unidad_medida";


        $data['ventas'] = $this->venta_model->getProductosZonaX($select, $condicion, $retorno, $group);


        //  $newData[0]['data'] = array(array());
        //var_dump($data['ventas']);
        $count = 0;


        foreach ($data['ventas'] as $venta) {
            $newData[$count]['data'] = array(array(1, 100));
            $newData[$count] = array();
            $newData[$count][] = $venta->zona_nombre;
            $newData[$count][] = intval($venta->suma);
            $count++;
        }

        echo json_encode($newData);

    }


    function utilidades()
    {


        if ($this->input->post('id_local') != "") {
            $condicion = array('local_id' => $this->input->post('id_local'));
            $data['local'] = $this->input->post('id_local');
        }
        if ($this->input->post('desde') != "") {

            $condicion['fecha >= '] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s');
            $data['fecha_desde'] = date('Y-m-d', strtotime($this->input->post('desde'))) . " " . date('H:i:s');
        }
        if ($this->input->post('hasta') != "") {

            $condicion['fecha <='] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s');
            $data['fecha_hasta'] = date('Y-m-d', strtotime($this->input->post('hasta'))) . " " . date('H:i:s');
        }

        $condicion= "venta_status IN ('".COMPLETADO."','".PEDIDO_DEVUELTO."','".PEDIDO_ENTREGADO."','".PEDIDO_GENERADO."')";
        $retorno = "RESULT";
        if ($this->input->post('utilidades') == "TODOS") {
            $select = "documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $data['utilidades'] = "TODO";
            $group = false;

        } elseif ($this->input->post('utilidades') == "PRODUCTOS") {

            $select = "(SELECT SUM(detalle_utilidad))  AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social";
            $data['utilidades'] = "PRODUCTO";
            $group = "producto_id";
        } elseif ($this->input->post('utilidades') == "CLIENTE") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,  documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente";
            $data['utilidades'] = "CLIENTE";
            $group = "cliente.id_cliente";
        } elseif ($this->input->post('utilidades') == "PROVEEDOR") {

            $select = "(SELECT SUM(detalle_utilidad)) AS suma,documento_venta.documento_Numero,documento_venta.documento_Serie,
 usuario.nombre,detalle_venta.*,venta.*, producto.producto_nombre,producto.producto_id,cliente.razon_social,cliente.id_cliente,
 proveedor.proveedor_nombre,proveedor.id_proveedor";
            $data['utilidades'] = "PROVEEDOR";
            $group = "producto.producto_proveedor";
        }
        $data['ventas'] = $this->venta_model->getUtilidades($select, $condicion, $retorno, $group);

        $newData=array();
       // var_dump($data['ventas']);
        $count = 0;

        foreach ($data['ventas'] as $venta) {
            $newData[$count]['data'] = array(array(1, 100));
            $newData[$count] = array();
            if ($this->input->post('utilidades') == "PRODUCTOS")
                $newData[$count][] = $venta->producto_nombre;
            if ($this->input->post('utilidades') == "CLIENTE")
                $newData[$count][] = $venta->razon_social;
            if ($this->input->post('utilidades') == "PROVEEDOR")
                $newData[$count][] = $venta->proveedor_nombre;

            $newData[$count][] = intval($venta->suma);

            $count++;
        }

        echo json_encode($newData);

    }


}

?>
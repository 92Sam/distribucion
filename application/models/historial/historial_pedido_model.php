<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class historial_pedido_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

        $this->load->model('producto/producto_model');
    }

    function insertar_pedido($proceso, $campos = array())
    {
        $this->db->where(array(
            'pedido_id' => $campos['pedido_id']
        ));
        $this->db->update('historial_pedido_proceso', array('actual' => 0));

        $pedido_proceso = $this->db->get_where('historial_pedido_proceso', array(
            'proceso_id' => $proceso,
            'pedido_id' => $campos['pedido_id']
        ))->row();

        $array['proceso_id'] = $proceso;
        $array['pedido_id'] = $campos['pedido_id'];
        $array['responsable_id'] = $campos['responsable_id'];
        $array['created_at'] = date('Y-m-d H:i:s');
        $array['actual'] = 1;
        $array['fecha_plan'] = isset($campos['fecha_plan']) ? $campos['fecha_plan'] : $array['created_at'];

        if ($pedido_proceso == null) {
            $this->db->insert('historial_pedido_proceso', $array);
            $pedido_proceso_id = $this->db->insert_id();
        }
        else{
            $this->db->where(array(
                'id' => $pedido_proceso->id
            ));
            $this->db->update('historial_pedido_proceso', $array);
        }

        $detalles = $this->db->get_where('detalle_venta', array('id_venta' => $campos['pedido_id']))->result();
        foreach ($detalles as $detalle) {
            $this->db->insert('historial_pedido_detalle', array(
                'historial_pedido_proceso_id' => $pedido_proceso_id,
                'producto_id' => $detalle->id_producto,
                'unidad_id' => $detalle->unidad_medida,
                'stock' => $detalle->cantidad,
                'costo_unitario' => $this->producto_model->get_costo_promedio($detalle->id_producto),
                'precio_unitario' => $detalle->precio,
                'bonificacion' => $detalle->bono,
            ));
        }
    }

    function editar_pedido($proceso, $campos = array())
    {

    }


}

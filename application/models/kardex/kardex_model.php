<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class kardex_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model('producto/producto_model');
    }

    //campo obligatorios
    //local_id, producto_id, unidad_id, serie, numero, tipo_doc, tipo_operacion,
    //cantidad, costo_unitario (solo cuando IO = 1), IO
    function insert_kardex($data = array()){
        $last_record = $this->db->select('cantidad_final, total_final')
            ->from('kardex')
            ->where('producto_id', $data['producto_id'])
            ->where('unidad_id', $data['unidad_id'])
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get()->row();

        $data['fecha'] = date('Y-m-d H:i:s');

        //Si es una venta de factura o boleta inserto en ref_id el id del pedido
        if(!isset($data['ref_id']) && ($data['tipo_doc'] == 1 || $data['tipo_doc'] == 3) && $data['IO'] == 2){
            $venta = $this->db->get_where('documento_fiscal', array(
                'documento_serie'=>$data['serie'],
                'documento_numero'=>$data['numero'],
                'documento_tipo'=>strtoupper(get_tipo_doc($data['tipo_doc']))
            ))->row();

            $data['ref_id'] = $venta != NULL ? $venta->venta_id : 0;
        }

        //Calculo los saldos
        $data['costo_unitario_final'] = $this->producto_model->get_costo_promedio($data['producto_id']);
        if($data['IO'] == 2)
            $data['costo_unitario'] = $data['costo_unitario_final'];

        if(!isset($data['total']))
            $data['total'] = $data['cantidad'] * $data['costo_unitario'];

        //Calculo los saldos finales
        if($last_record != NULL){
            $data['cantidad_final'] = $data['IO'] == 2 ? $last_record->cantidad_final - $data['cantidad'] : $last_record->cantidad_final + $data['cantidad'];
            $data['total_final'] = $data['IO'] == 2 ? $last_record->total_final - $data['total'] : $last_record->total_final + $data['total'];
        }
        else {
            $data['cantidad_final'] = $data['cantidad'];
            $data['total_final'] = $data['total'];
        }
        $data['costo_unitario_final'] = $data['cantidad_final'] != 0 ? $data['total_final'] / $data['cantidad_final'] : 0;

        $this->db->insert('kardex', $data);
        return $this->db->insert_id();
    }


    function get_kardex($producto_id, $local_id, $mes, $year){
        $where['producto_id'] = $producto_id;
        if($local_id != false)
            $where['local_id'] = $local_id;

        if($mes != false && $year != false){
            $where['fecha >='] = $year . '-' . sumCod($mes, 2) . '-01';
            $where['fecha <='] = $year . '-' . sumCod($mes, 2) . '-' . last_day($year, sumCod($mes, 2));
        }

        $kardex_inicial = $this->db->select('costo_unitario_final, cantidad_final, total_final, unidad_id')
            ->from('kardex')
            ->where('fecha <', $year . '-' . sumCod($mes, 2) . '-01')
            ->order_by('id', 'DESC')
            ->get()->row();

        $kardex = $this->db->get_where('kardex', $where)->result();

        return array('fiscal'=>$kardex, 'inicial'=>$kardex_inicial);
    }

}

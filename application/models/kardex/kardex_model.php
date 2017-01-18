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
        $last_record = $this->db->select('cantidad_final, total_final, costo_unitario_final')
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
        if($data['IO'] == 2){
            if($last_record != NULL){
                $data['costo_unitario'] = $last_record->costo_unitario_final;
            }else{
                if(isset($data['total']))
                    $data['costo_unitario'] = $data['total'] / $data['cantidad'];
                else
                    $data['costo_unitario'] = 0;
            }
        }

        if(!isset($data['total']))
            $data['total'] = $data['cantidad'] * $data['costo_unitario'];

        if(($data['tipo_operacion'] == 2 || $data['tipo_operacion'] == 6 || $data['tipo_operacion'] == 9) && $data['IO'] == 1)
            $data['total'] = $data['total'] * 1.18;

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

        $this->db->select('costo_unitario_final, cantidad_final, total_final, kardex.unidad_id,unidades.nombre_unidad')
            ->from('kardex')
            ->join('unidades','kardex.unidad_id=unidades.id_unidad')
            ->where('producto_id', $producto_id)
            ->where('fecha <', $year . '-' . sumCod($mes, 2) . '-01')
            ->order_by('id', 'DESC');

        if($local_id != false)
            $this->db->where('local_id', $local_id);

        $kardex_inicial = $this->db->get()->row();

        $where['producto_id'] = $producto_id;
        if($local_id != false)
            $where['local_id'] = $local_id;

        if($mes != false && $year != false){
            $where['fecha >='] = $year . '-' . sumCod($mes, 2) . '-01';
            $where['fecha <='] = $year . '-' . sumCod($mes, 2) . '-' . last_day($year, sumCod($mes, 2));
        }

        $this->db->join('unidades','kardex.unidad_id=unidades.id_unidad', 'join');
        $kardex = $this->db->get_where('kardex', $where)->result();

        return array('fiscal'=>$kardex, 'inicial'=>$kardex_inicial);
    }

    function get_kardex_interno($producto_id, $local_id, $mes, $year){

        $this->db->select('costo_unitario_final, cantidad_final, total_final, kardex.unidad_id,unidades.nombre_unidad')
            ->from('kardex')
            ->join('unidades','kardex.unidad_id=unidades.id_unidad')
            ->where('producto_id', $producto_id)
            ->where('fecha <', $year . '-' . sumCod($mes, 2) . '-01')
            ->order_by('id', 'DESC');

        if($local_id != false)
            $this->db->where('local_id', $local_id);

        $kardex_inicial = $this->db->get()->row();

        $this->db->select(
            'local_id , fecha,
            producto_id,
            serie,
            numero,
            unidades.nombre_unidad,
            tipo_doc,
            tipo_operacion,
            IO,
            SUM(cantidad) as cantidad,
            costo_unitario,
            SUM(total) as total,
            ref_id,
            ref_val'
            )
            ->from('kardex')
            ->join('unidades','kardex.unidad_id=unidades.id_unidad')
            ->group_by('ref_id, tipo_doc, tipo_operacion')
            ->order_by('id');

        $this->db->where('producto_id', $producto_id);
        if($local_id != false)
            $this->db->where('local_id', $local_id);

        if($mes != false && $year != false){
            $this->db->where('fecha >=' ,$year . '-' . sumCod($mes, 2) . '-01');
            $this->db->where('fecha <=', $year . '-' . sumCod($mes, 2) . '-' . last_day($year, sumCod($mes, 2)));
        }

        $kardex = $this->db->get()->result();

        $cantidad_inicial = 0;
        $total_inicial = 0;
        if($kardex_inicial != NULL){
            $cantidad_inicial = $kardex_inicial->cantidad_final;
            $total_inicial = $kardex_inicial->total_final;
        }

        foreach($kardex as $k){
            $k->referencia = '';
            if($k->IO == 2){
                $venta = $this->db->select('documento_Serie as doc_serie, documento_Numero as doc_numero')
                    ->from('venta')
                    ->join('documento_venta', 'venta.numero_documento = documento_venta.id_tipo_documento')
                    ->where('venta.venta_id', $k->ref_id)->get()->row();

                $k->serie = $venta->doc_serie;
                $k->numero = $venta->doc_numero;
            }

            if($k->IO == 2){
                $k->cantidad_final = $cantidad_inicial - $k->cantidad;
                $cantidad_inicial = $k->cantidad_final;

                $k->total_final = $total_inicial - $k->total;
                $total_inicial = $k->total_final;
                if($k->cantidad_final != 0)
                    $k->costo_unitario_final = $k->total_final / $k->cantidad_final;
                else
                    $k->costo_unitario_final = 0;
            }
            else if($k->IO == 1){
                $k->cantidad_final = $cantidad_inicial + $k->cantidad;
                $cantidad_inicial = $k->cantidad_final;

                $k->total_final = $total_inicial + $k->total;
                $total_inicial = $k->total_final;
                if($k->cantidad_final != 0)
                    $k->costo_unitario_final = $k->total_final / $k->cantidad_final;
                else
                    $k->costo_unitario_final = 0;
            }
        }

        return array('fiscal'=>$kardex, 'inicial'=>$kardex_inicial);
    }

}

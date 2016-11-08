<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class historial_pagos_clientes_model extends CI_Model
{

    private $tabla = "historial_pagos_clientes";

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
                             $nombre_or, $where_or,
                             $group = false,
                             $order = false, $retorno = false)
    {
        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);
        }
        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    for ($t = 0; $t < count($tipo_join); $t++) {

                        if ($tipo_join[$t] != "") {

                            $this->db->join($join[$i], $campos_join[$i], $tipo_join[$t]);
                        }

                    }

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);
        }

        if ($nombre_in != false) {
            for ($i = 0; $i < count($nombre_in); $i++) {
                $this->db->where_in($nombre_in[$i], $where_in[$i]);
            }
        }

        if ($nombre_or != false) {
            for ($i = 0; $i < count($nombre_or); $i++) {
                $this->db->or_where($where_or);
            }
        }

        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order);
        }

        $query = $this->db->get();

        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }

    function guardar($lista_credito, $opciones = false)
    {
        $this->db->trans_start(true);

        $this->db->trans_begin();
//var_dump($opciones);
        //echo $opciones['banco'];
        if (isset($opciones['banco'])) {

            $banco = $opciones['banco'];
        } else {
            $banco = null;
        }

        if (isset($opciones['caja'])) {
            $caja = $opciones['caja'];

        } else {
            $caja = null;
        }

        //echo $caja;

        if ($caja === null && $banco === null) {

            return "No se ha especificado caja ni banco";

        } else {
            foreach ($lista_credito as $row) {


                $list_cp = array(
                    'credito_id' => $row->id_venta,
                    'historial_fecha' => date("Y-m-d H:i:s"),
                    'historial_monto' => $row->cuota,
                    'historial_tipopago' => $row->metodo,
                    'monto_restante' => isset($row->monto_restante) ? $row->monto_restante : 0.00,
                    'historial_usuario' => $row->usuario,
                    'historial_estatus' => "PENDIENTE",
                    'historial_banco_id' => $banco,
                    'historial_caja_id' => $caja

                );

                $this->db->insert($this->tabla, $list_cp);

               // echo $this->db->last_query();
                $id = $this->db->insert_id();

            }


            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {

                return false;
            } else {

                return $id;
            }

            $this->db->trans_off();
        }

    }


    function actualizar($arreglo, $update, $tabla)
    {
        $this->db->trans_start(true);

        $this->db->trans_begin();

        for ($j = 0; $j < count($arreglo); $j++) {

            $where = array(
                'historial_id' => $arreglo[$j]
            );
            $this->db->where($where);
            $this->db->update($tabla, $update);
        }
        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;

    }


    function update_historial($ids, $id_liquidacion)
    {
        $this->db->trans_start(true);

        $this->db->trans_begin();

        for ($j = 0; $j < count($ids); $j++) {

            $where = array(
                'historial_id' => $ids[$j]
            );

            $select = $this->db->query('select * from historial_pagos_clientes join credito on credito.id_venta=historial_pagos_clientes.credito_id where historial_id=' . $ids[$j]);
            $credito_actual = $select->result_array();

            $update = array(
                'historial_estatus' => "CONFIRMADO"
            );
            $this->db->where($where);
            $this->db->update('historial_pagos_clientes', $update);

            $liquidacion = array(
                'liquidacion_id' => $id_liquidacion,
                'pago_id' => $ids[$j]
            );
            $this->db->insert('liquidacion_cobranza_detalle', $liquidacion);


            if (sizeof($credito_actual) > 0) {

                if ((floatval($credito_actual[0]['historial_monto']) + floatval($credito_actual[0]['dec_credito_montodebito'])) >= floatval($credito_actual[0]['dec_credito_montodeuda'])) {
                    $credito['var_credito_estado'] = CREDITO_CANCELADO;

                } else {
                    $credito['var_credito_estado'] = CREDITO_ACUENTA;
                }

                $condition = array('id_venta' => $credito_actual[0]['id_venta']);
                $this->venta_model->actualizarCredito($credito, $condition);
            }


        }


        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;

    }


    function actualizar_historial_editado($historial, $venta_id, $montonuevo)
    {
        $this->db->trans_start(true);

        $this->db->trans_begin();

        $select = '*';
        $from = "historial_pagos_clientes";
        $where = array(
            'credito_id' => $venta_id
        );
        $join = array('credito');
        $campos_join = array('credito.id_venta=historial_pagos_clientes.credito_id');


        $historialcompleto = $this->traer_by($select, $from, $join, $campos_join, false, $where, false, false, false, false, false, false, "RESULT_ARRAY");

        $suma = 0;
        $cont = 0;


        /*  $select_v=$this->db->query('select * from venta join consolidado_detalle on consolidado_detalle.pedido_id=venta.venta_id where venta.venta_id='.$venta_id);
          $venta_actual=$select_v->row_array();*/

        $restante = $historialcompleto[0]['monto_restante'];
        $update = array();
        for ($j = 0; $j < count($historialcompleto); $j++) {
            $monto_viejo = $historialcompleto[$j]['historial_monto'];
            $restante_viejo = $historialcompleto[$j]['monto_restante'];
            if ($historialcompleto[$j]['historial_id'] == $historial) {

                $monto_viejo_que_es = $historialcompleto[$j]['historial_monto'];
                $cont++;
                $where = array(
                    'historial_id' => $historial
                );

                if (count($historialcompleto) > 1 and $cont != 1) {
                    $update = array(
                        'historial_monto' => $montonuevo,
                        'monto_restante' => $update['monto_restante'] - $montonuevo
                    );
                    // $restante=$restante-$montonuevo;
                } else {
                    //   echo "entro aqui el primero ".$restante;
                    $update = array(
                        'historial_monto' => $montonuevo,
                        'monto_restante' => ($restante + $monto_viejo) - $montonuevo //$historialcompleto[$j]['dec_credito_montodeuda'] - $montonuevo
                    );
                }

            } else {
                $cont++;

                $where = array(
                    'historial_id' => $historialcompleto[$j]['historial_id']
                );
                if (count($historialcompleto) > 1 and $cont != 1) {

                    $update = array(
                        'monto_restante' => $update['monto_restante'] - $historialcompleto[$j]['historial_monto']
                    );
                } else {
                    $update = array(
                        'monto_restante' => $restante_viejo
                    );
                }
            }


            //  $restante=$update['monto_restante'];
            $this->db->where($where);
            $this->db->update('historial_pagos_clientes', $update);

        }

        $where = array(
            'id_venta' => $venta_id
        );
        $update = array(
            'dec_credito_montodebito' => ($historialcompleto[0]['dec_credito_montodebito'] - $monto_viejo_que_es) + $montonuevo
        );
        $this->db->where($where);
        $this->db->update('credito', $update);

        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;

    }

    /*function update($condicionespago)
    {

        $produc_exite = $this->get_by('nombre_condiciones', $condicionespago['nombre_condiciones']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_condiciones'] == $condicionespago ['id_condiciones']))) {
            $this->db-FV>trans_start();
            $this->db->where('id_condiciones', $condicionespago['id_condiciones']);
            $this->db->update('condiciones_pago', $condicionespago);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }*/


}
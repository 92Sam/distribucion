<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class ingreso_model extends CI_Model
{


    function __construct()
    {
        parent::__construct();
        $this->load->model('kardex/kardex_model');
        $this->load->model('cajas/cajas_model');
    }

    function get_deuda_detalle($ingreso_id){
        $consulta = "
            SELECT 
                ingreso.id_ingreso AS ingreso_id, 
                ingreso.total_ingreso AS total,
                ingreso.tipo_documento AS documento,
                CONCAT( ingreso.documento_serie, ' - ', ingreso.documento_numero) AS documento_numero, 
                (SELECT 
                        SUM(pagos_ingreso.pagoingreso_monto)
                    FROM
                        pagos_ingreso
                    WHERE
                        pagos_ingreso.pagoingreso_ingreso_id = ingreso.id_ingreso) AS monto_pagado
            FROM 
                ingreso 
            WHERE 
                ingreso.id_ingreso = ".$ingreso_id." 
        ";

        $ingreso = $this->db->query($consulta)->row();

        $ingreso->detalles = $this->db->get_where('pagos_ingreso', array('pagoingreso_ingreso_id'=>$ingreso_id))->result();

        return $ingreso;
    }

    function documento_existe($serie, $numero, $tipo, $id = FALSE){

        $consulta = "
            SELECT 
                COUNT(*) as existe
            FROM
                ingreso
            WHERE
                documento_serie = '".$serie."' 
                AND documento_numero = '".$numero."' 
                AND tipo_documento = '".$tipo."' 
        ";
        
        if($id != FALSE){
            $consulta .= " AND id_ingreso != '".$id."'";
        }
            

        $query = $this->db->query($consulta)->row();
        

        if($query->existe == 0)
            return false;

        return true;
    }

    function insertar_compra($cab_pie, $detalle)
    {

        $this->db->trans_start(true);

        $this->db->trans_begin();

        if ($cab_pie['costos'] === 'true') {
            $status = 'COMPLETADO';
        } else {
            $status = 'PENDIENTE';
        }
        $compra = array(
            'fecha_registro' => $cab_pie['fecReg'],
            'int_Proveedor_id' => $cab_pie['cboProveedor'],
            'nUsuCodigo' => $this->session->userdata('nUsuCodigo'),
            'fecha_emision' => $cab_pie['fecEmision'],
            'documento_vence' => $cab_pie['documento_vence'],
            'local_id' => $cab_pie['local'],
            'tipo_documento' => $cab_pie['cboTipDoc'],
            'documento_serie' => $cab_pie['doc_serie'],
            'documento_numero' => $cab_pie['doc_numero'],
            'ingreso_status' => $cab_pie['status'],
            'impuesto_ingreso' => $cab_pie['montoigv'],
            'tipo_ingreso' => $cab_pie['tipo_ingreso'],
            'sub_total_ingreso' => $cab_pie['subTotal'],
            'total_ingreso' => $cab_pie['totApagar'],
            'pago' => $cab_pie['pago'],
        );

        $this->db->insert('ingreso', $compra);
        $insert_id = $this->db->insert_id();

        if($status == 'COMPLETADO' && $compra['total_ingreso'] > 0 && $compra['pago'] == 'CONTADO'){
            $this->cajas_model->save_pendiente(array(
                'monto'=>$compra['total_ingreso'],
                'tipo'=>'INGRESO',
                'IO'=>2,
                'ref_id'=>$insert_id
            ));
        }

        $data = array();

        $local_id = $this->session->userdata('id_local');


        if ($detalle != null) {
            foreach ($detalle as $row) {
                $cantidad = $row->Cantidad;
                $id_producto = $row->Codigo;
                $unidad = $row->unidad;

                $list_p = array(
                    'id_ingreso' => $insert_id,
                    'id_producto' => $row->Codigo,
                    'cantidad' => $row->Cantidad,
                    'precio' => ($row->PrecUnt === 'null') ? 0 : $row->PrecUnt,
                    'precio_valor' => ($row->ValorUnitario === 'null') ? 0 : $row->ValorUnitario,
                    'unidad_medida' => $row->unidad,
                    'total_detalle' => (!isset($row->Importe) || $cab_pie['status'] == INGRESO_PENDIENTE) ? 0 : $row->Importe
                );

                if($status == 'COMPLETADO'){
                    $tipo_doc = 0;
                    if($compra['tipo_documento'] == 'FACTURA')
                        $tipo_doc = 1;
                    elseif($compra['tipo_documento'] == 'BOLETA DE VENTA')
                        $tipo_doc = 3;

                    $this->kardex_model->insert_kardex(array(
                        'local_id'=>$compra['local_id'],
                        'producto_id'=>$row->Codigo,
                        'unidad_id'=>$row->unidad,
                        'serie'=>$compra['documento_serie'],
                        'numero'=>$compra['documento_numero'],
                        'tipo_doc'=>$tipo_doc,
                        'tipo_operacion'=>$compra['tipo_ingreso'] == 'COMPRA' ? 2 : 9,
                        'cantidad'=>$row->Cantidad,
                        'costo_unitario'=>$row->PrecUnt,
                        'IO'=>1,
                        'ref_id'=>$insert_id,
                        'total'=>$list_p['total_detalle'],
                    ));
                }


                $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto=' . $row->Codigo . ' and id_local=' . $local_id);
                $inventario_existente = $query->row_array();

                if (count($inventario_existente) > 0) {
                    $cantidad_vieja = $inventario_existente['cantidad'];
                    $fraccion_vieja = $inventario_existente['fraccion'];
                } else {
                    $cantidad_vieja = 0;
                    $fraccion_vieja = 0;
                }
                $cantidad_compra = $row->Cantidad;


                //  CALCLOS DE UNDIAD DE MEDIDA

                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order BY orden ASC");

                $unidades_producto = $query->result_array();

                // var_dump($unidades_producto);

                $unidad_maxima = $unidades_producto[0];
                $unidad_minima = $unidades_producto[0];
                if (count($unidades_producto) > 1) {
                    $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
                }

                if ($unidad_maxima['id_unidad'] == $row->unidad) {

                    $arreglo = array(
                        'costo_unitario' => $list_p['precio']
                    );

                } else {

                    $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' and id_unidad='" . $list_p['unidad_medida'] . "' ");
                    $unidades_de_unidad = $query->row_array();
                    $arreglo = array(
                        'costo_unitario' => ($list_p['precio'] / $unidades_de_unidad['unidades']) * $unidad_maxima['unidades']
                    );
                }

                $where = array(
                    'producto_id' => $id_producto
                );
                if ($list_p['precio'] > 0) {
                    $this->db->where($where);
                    $this->db->update('producto', $arreglo);
                }


                foreach ($unidades_producto as $up) {
                    if ($up['id_unidad'] == $unidad) {
                        $unidad_form = $up;
                    }
                }

                $total_unidades_minimas = $unidad_form['unidades'] * $cantidad;
                $suma_cantidades = $fraccion_vieja + $total_unidades_minimas;

                if ($suma_cantidades >= $unidad_maxima['unidades']) {

                    $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                    $cantidad_nueva = intval($resultado_division) + $cantidad_vieja;
                    $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                    $fraccion_nueva = $resto_division;
                } else {
                    $cantidad_nueva = $cantidad_vieja;
                    $fraccion_nueva = $suma_cantidades;

                }
                // var_dump($unidad);
                //  var_dump($unidad_maxima['id_unidad']);

                if ($unidad == $unidad_maxima['id_unidad']) {
                    $cantidad_nueva = $cantidad_vieja + $cantidad_compra;
                    $fraccion_nueva = $fraccion_vieja;
                }
                if (isset($unidad_minima) and $unidad == $unidad_minima['id_unidad']) {
                    // var_dump($unidad_minima['id_unidad']);
                    /* var_dump($unidad_minima['id_unidad']);
                     var_dump($unidad);*/

                    $suma_cantidades = $fraccion_vieja + $cantidad;
                    /*  var_dump($fraccion_vieja);
                      var_dump($cantidad);
                      var_dump($unidad_maxima['unidades']);*/
                    if ($suma_cantidades >= $unidad_maxima['unidades']) {

                        $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                        $cantidad_nueva = intval($resultado_division) + $cantidad_vieja;
                        $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                        $fraccion_nueva = $resto_division;
                    } else {
                        // var_dump($cantidad_vieja);
                        if ($cantidad_vieja > 0) {
                            $cantidad_nueva = $cantidad_vieja;
                            $fraccion_nueva = $suma_cantidades;
                        } else {

                            if (count($unidades_producto) > 1) {
                                $cantidad_nueva = 0;
                                $fraccion_nueva = $suma_cantidades;
                            } else {

                                $cantidad_nueva = $cantidad;
                                $fraccion_nueva = 0;
                            }
                        }


                    }

                }
                //var_dump($inventario_existente);
                $inventario_nuevo_in = array();
                $inventario_nuevo_ac = array();
                if (count($inventario_existente) > 0) {

                    $where = array('id_inventario' => $inventario_existente['id_inventario']);
                    $inventario_nuevo_ac = array(
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva
                    );

                } else {
                    $inventario_nuevo_in = array(
                        'id_producto' => $id_producto,
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva,
                        'id_local' => $this->session->userdata('id_local')
                    );

                }

                array_push($data, $list_p);
                //var_dump($inventario_nuevo_ac);
                if (isset($inventario_nuevo_ac) && count($inventario_nuevo_ac) > 0) {
                    $this->update_inventario($inventario_nuevo_ac, $where);
                } else {

                    $this->db->insert('inventario', $inventario_nuevo_in);
                }




            }
        }


        $this->db->insert_batch('detalleingreso', $data);


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {
            return $insert_id;
        }
        $this->db->trans_off();

    }


    function update_compra($cab_pie, $detalle)
    {

        $this->db->trans_start(true);

        $this->db->trans_begin();

        $compra = array(

            'usuario_costos' => $this->session->userdata('nUsuCodigo'),
            'ingreso_status' => $cab_pie['status'],
            'impuesto_ingreso' => $cab_pie['montoigv'],
            'sub_total_ingreso' => $cab_pie['subTotal'],
            'total_ingreso' => $cab_pie['totApagar']

        );

        $id_ingreso = $cab_pie['id_ingreso'];

        $this->db->update('ingreso', $compra, array('id_ingreso' => $id_ingreso));

        $ingreso = $this->db->get_where('ingreso', array('id_ingreso'=>$id_ingreso))->row();
        if($compra['total_ingreso'] > 0 && $ingreso->pago == 'CONTADO'){

            $this->cajas_model->update_pendiente(array(
                'monto'=>$compra['total_ingreso'],
                'tipo'=>'INGRESO',
                'ref_id'=>$id_ingreso
            ));
        }

        $data = array();

        $local_id = $this->session->userdata('id_local');


        $compra_id = $cab_pie['id_ingreso'];

        /**********SUMOO AL INVETARIO TODOS LOS ITEMS DE LA COMPRA***********/
        //quito esto siempre .- jhainey
        // if ($venta_cabecera['devolver'] == 'true') {
        $sql_detalle_ingreso = $this->db->query("SELECT * FROM detalleingreso
JOIN producto ON producto.producto_id=detalleingreso.id_producto
LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
JOIN ingreso ON ingreso.`id_ingreso`=detalleingreso.`id_ingreso`
WHERE detalleingreso.id_ingreso='$compra_id'");

        $query_detalle_ingreso = $sql_detalle_ingreso->result_array();



        $countQuery = count($query_detalle_ingreso);
        $cab_pie['local']=$query_detalle_ingreso[0]['local_id'];
        $cab_pie['cboProveedor']=$query_detalle_ingreso[0]['int_Proveedor_id'];

        // Detalle de la Venta
        foreach ($query_detalle_ingreso as $row) {


            $cantidad_venta = $row['cantidad'];
            $unidad_medida_venta = $row['unidad_medida'];
            $id_producto = $row['id_producto'];
            $precio = $row['precio'];
            $importe = $row['total_detalle'];

            $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
				FROM inventario where id_producto=' . $id_producto . ' and id_local=' . $local_id);
            $inventario_existente = $query->row_array();
            $cantidad_vieja = 0;
            $fraccion_vieja = 0;

            if (isset($inventario_existente['cantidad'])) {
                $cantidad_vieja = $inventario_existente['cantidad'];
            }
            if (isset($inventario_existente['fraccion'])) {
                $fraccion_vieja = $inventario_existente['fraccion'];
            }

            // CALCULOS DE UNDIAD DE MEDIDA
            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order by orden asc");

            $unidades_producto = $query->result_array();

            $unidad_maxima = $unidades_producto[0];
            $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
            $unidad_form = 0;
            foreach ($unidades_producto as $up) {
                if ($up['id_unidad'] == $unidad_medida_venta) {
                    $unidad_form = $up;
                }
            }

            $total_unidades_minimas = $unidad_form['unidades'] * $cantidad_venta;
            $total_unidades_minimas_viejas = ($unidad_maxima['unidades'] * $cantidad_vieja) + $fraccion_vieja;

            $suma_cantidades = $total_unidades_minimas_viejas - $total_unidades_minimas;

            if ($suma_cantidades >= $unidad_maxima['unidades']) {
                $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                $cantidad_nueva = intval($resultado_division);
                $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                $fraccion_nueva = $resto_division;
            } else {
                if ($suma_cantidades < $unidad_maxima['unidades']) {
                    $cantidad_nueva = 0;
                    $fraccion_nueva = +$suma_cantidades;
                } else {
                    $cantidad_nueva = $cantidad_vieja;
                    $fraccion_nueva = +$suma_cantidades;
                }
            }

            if ($unidad_medida_venta == $unidad_maxima['id_unidad']) {
                $cantidad_nueva = $cantidad_vieja - $cantidad_venta;
                $fraccion_nueva = $fraccion_vieja;
            }

            if ($unidad_medida_venta == $unidad_minima['id_unidad']) {
                if ($suma_cantidades >= $unidad_maxima['unidades']) {
                    $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                    $cantidad_nueva = intval($resultado_division);
                    $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                    $fraccion_nueva = $resto_division;
                } else {
                    if ($suma_cantidades < $unidad_maxima['unidades']) {
                        $cantidad_nueva = 0;
                        $fraccion_nueva = +$suma_cantidades;
                    } else {
                        if ($cantidad_vieja > 0) {
                            $cantidad_nueva = $cantidad_vieja;
                            $fraccion_nueva = $suma_cantidades;
                        } else {
                            if (count($unidades_producto) > 1) {
                                $cantidad_nueva = 0;
                                $fraccion_nueva = $cantidad_venta;
                            } else {
                                $cantidad_nueva = $cantidad_venta;
                                $fraccion_nueva = 0;
                            }
                        }
                    }
                }
            }


            if (count($inventario_existente) > 0) {
                $inventario_nuevo = array(
                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva
                );
                $this->update_inventario($inventario_nuevo, array('id_inventario' => $inventario_existente['id_inventario']));
            } else {
                $inventario_nuevo = array(
                    'cantidad' => $cantidad_nueva,
                    'fraccion' => $fraccion_nueva
                );
                $this->db->insert('inventario', $inventario_nuevo);
            }
        }

        /***********TERMINO DE DEVOLVER EL INVENTARIO********/



        if ($detalle != null) {


            foreach ($detalle as $row) {

                if($ingreso->ingreso_status == 'COMPLETADO'){
                    $tipo_doc = 0;
                    if($ingreso->tipo_documento == 'FACTURA')
                        $tipo_doc = 1;
                    elseif($ingreso->tipo_documento == 'BOLETA DE VENTA')
                        $tipo_doc = 3;

                    $this->kardex_model->insert_kardex(array(
                        'local_id'=>$ingreso->local_id,
                        'producto_id'=>$row->Codigo,
                        'unidad_id'=>$row->unidad,
                        'serie'=>$ingreso->documento_serie,
                        'numero'=>$ingreso->documento_numero,
                        'tipo_doc'=>$tipo_doc,
                        'tipo_operacion'=>$ingreso->tipo_ingreso == 'COMPRA' ? 2 : 9,
                        'cantidad'=>$row->Cantidad,
                        'costo_unitario'=>($row->PrecUnt === 'null') ? 0 : $row->PrecUnt,
                        'IO'=>1,
                        'ref_id'=>$id_ingreso,
                        'total'=>(!isset($row->Importe)) ? 0 : $row->Importe,
                    ));
                }

                $cantidad = $row->Cantidad;
                $id_producto = $row->Codigo;
                $unidad = $row->unidad;

                $list_p = array(
                    'id_ingreso' => $id_ingreso,
                    'id_producto' => $row->Codigo,
                    'cantidad' => $row->Cantidad,
                    'precio' => ($row->PrecUnt === 'null') ? 0 : $row->PrecUnt,
                    'precio_valor' => ($row->ValorUnitario === 'null') ? 0 : $row->ValorUnitario,
                    'unidad_medida' => $row->unidad,
                    'total_detalle' => (!isset($row->Importe)) ? 0 : $row->Importe
                );


                $query = $this->db->query('SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto=' . $row->Codigo . ' and id_local=' . $local_id);
                $inventario_existente = $query->row_array();

                if (count($inventario_existente) > 0) {
                    $cantidad_vieja = $inventario_existente['cantidad'];
                    $fraccion_vieja = $inventario_existente['fraccion'];
                } else {
                    $cantidad_vieja = 0;
                    $fraccion_vieja = 0;
                }
                $cantidad_compra = $row->Cantidad;


                // ACTUALIZACION DEL COSTO UNITARIO DEL PRODUCTO

                $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' order BY orden ASC");

                $unidades_producto = $query->result_array();

                // var_dump($unidades_producto);

                $unidad_maxima = $unidades_producto[0];
                if (count($unidades_producto) > 1) {
                    $unidad_minima = $unidades_producto[count($unidades_producto) - 1];
                }

                if ($unidad_maxima['id_unidad'] == $row->unidad) {

                    $arreglo = array(
                        'costo_unitario' => $list_p['precio']
                    );

                } else {

                    $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$id_producto' and id_unidad='" . $list_p['unidad_medida'] . "' ");
                    $unidades_de_unidad = $query->row_array();
                    $arreglo = array(
                        'costo_unitario' => ($list_p['precio'] / $unidades_de_unidad['unidades']) * $unidad_maxima['unidades']
                    );
                }

                $where = array(
                    'producto_id' => $id_producto
                );

                $this->db->update('producto', $arreglo, $where);


                foreach ($unidades_producto as $up) {
                    if ($up['id_unidad'] == $unidad) {
                        $unidad_form = $up;
                    }
                }

                $total_unidades_minimas = $unidad_form['unidades'] * $cantidad;
                $suma_cantidades = $fraccion_vieja + $total_unidades_minimas;

                if ($suma_cantidades >= $unidad_maxima['unidades']) {

                    $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                    $cantidad_nueva = intval($resultado_division) + $cantidad_vieja;
                    $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                    $fraccion_nueva = $resto_division;
                } else {
                    $cantidad_nueva = $cantidad_vieja;
                    $fraccion_nueva = $suma_cantidades;

                }
                // var_dump($unidad);
                //  var_dump($unidad_maxima['id_unidad']);

                if ($unidad == $unidad_maxima['id_unidad']) {
                    $cantidad_nueva = $cantidad_vieja + $cantidad_compra;
                    $fraccion_nueva = $fraccion_vieja;
                }
                if (isset($unidad_minima) and $unidad == $unidad_minima['id_unidad']) {
                    // var_dump($unidad_minima['id_unidad']);
                    /* var_dump($unidad_minima['id_unidad']);
                     var_dump($unidad);*/

                    $suma_cantidades = $fraccion_vieja + $cantidad;
                    /*  var_dump($fraccion_vieja);
                      var_dump($cantidad);
                      var_dump($unidad_maxima['unidades']);*/
                    if ($suma_cantidades >= $unidad_maxima['unidades']) {

                        $resultado_division = $suma_cantidades / $unidad_maxima['unidades'];
                        $cantidad_nueva = intval($resultado_division) + $cantidad_vieja;
                        $resto_division = fmod($suma_cantidades, $unidad_maxima['unidades']);
                        $fraccion_nueva = $resto_division;
                    } else {
                        // var_dump($cantidad_vieja);
                        if ($cantidad_vieja > 0) {
                            $cantidad_nueva = $cantidad_vieja;
                            $fraccion_nueva = $suma_cantidades;
                        } else {

                            if (count($unidades_producto) > 1) {
                                $cantidad_nueva = 0;
                                $fraccion_nueva = $suma_cantidades;
                            } else {

                                $cantidad_nueva = $cantidad;
                                $fraccion_nueva = 0;
                            }
                        }


                    }

                }
                //var_dump($inventario_existente);
                $inventario_nuevo_in = array();
                $inventario_nuevo_ac = array();
                if (count($inventario_existente) > 0) {

                    $where = array('id_inventario' => $inventario_existente['id_inventario']);
                    $inventario_nuevo_ac = array(
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva
                    );

                } else {
                    $inventario_nuevo_in = array(
                        'id_producto' => $id_producto,
                        'cantidad' => $cantidad_nueva,
                        'fraccion' => $fraccion_nueva,
                        'id_local' => $this->session->userdata('id_local')
                    );

                }

                array_push($data, $list_p);
                // var_dump($inventario_nuevo_ac);
                if (isset($inventario_nuevo_ac) && count($inventario_nuevo_ac) > 0) {
                    $this->update_inventario($inventario_nuevo_ac, $where);
                } else {

                    $this->db->insert('inventario', $inventario_nuevo_in);
                }







            }
        }


//var_dump($lista_inventario_actualizar);
        $this->db->delete('detalleingreso', array('id_ingreso' => $compra_id));
        $this->db->insert_batch('detalleingreso', $data);


        $this->db->trans_complete();
        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {
            return $id_ingreso;
        }
        $this->db->trans_off();

    }


    function update_inventario($campos, $wheres)
    {


        $this->db->trans_start();
        $this->db->where($wheres);
        $this->db->update('inventario', $campos);
        $this->db->trans_complete();


    }

    //TODO CAMBIAR TODOS LOS DEMAS POR ESTE METODO
    public function traer_by_mejorado($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
                                      $nombre_or, $where_or,
                                      $group = false,
                                      $order = false, $retorno = false, $limit = false, $start = 0, $order_dir = false, $like = false, $where_custom)
    {
        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);
        }
        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    // for ($t = 0; $t < count($tipo_join); $t++) {

                    // if ($tipo_join[$t] != "") {

                    $this->db->join($join[$i], $campos_join[$i], $tipo_join[$i]);
                    //}

                    //}

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);
        }
        if ($like != false) {
            $this->db->like($like);
        }
        if ($where_custom != false) {
            $this->db->where($where_custom);
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

        if ($limit != false) {
            $this->db->limit($limit, $start);
        }
        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order, $order_dir);
        }

        $query = $this->db->get();

        //echo $this->db->last_query();
        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

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

    function anular_ingreso()
    {

        $this->db->trans_start(true);

        $this->db->trans_begin();

        $data = array();
        $id = $this->input->post('id');
        $local = $this->input->post('local');
        $anular_serie = $this->input->post('serie');
        $anular_numero = $this->input->post('numero');
        $anular_fecha = date('Y-m-d H:i:s', strtotime($this->input->post('fecha') . ' '.date('H:i:s')));


        $sql_detalle_ingreso = $this->db->query("SELECT * FROM detalleingreso
JOIN producto ON producto.producto_id=detalleingreso.id_producto
LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
JOIN ingreso ON ingreso.`id_ingreso`=detalleingreso.`id_ingreso`
WHERE detalleingreso.id_ingreso='$id'");

        $query_detalle_ingreso = $sql_detalle_ingreso->result_array();

        for ($i = 0; $i < count($query_detalle_ingreso); $i++) {

            $this->kardex_model->insert_kardex(array(
                'fecha'=>$anular_fecha,
                'local_id'=>$query_detalle_ingreso[$i]['local_id'],
                'producto_id'=>$query_detalle_ingreso[$i]['producto_id'],
                'unidad_id'=>$query_detalle_ingreso[$i]['unidad_medida'],
                'serie'=>$anular_serie,
                'numero'=>$anular_numero,
                'tipo_doc'=>7,
                'tipo_operacion'=>6,
                'cantidad'=>($query_detalle_ingreso[$i]['cantidad'] * -1),
                'costo_unitario'=>$query_detalle_ingreso[$i]['precio'],
                'IO'=>1,
                'ref_id'=>$id,
                'referencia'=>$id,
                'total'=>($query_detalle_ingreso[$i]['total_detalle'] * -1),
            ));

            $local = $query_detalle_ingreso[$i]['local_id'];
            $unidad_maxima = $query_detalle_ingreso[$i]['unidades'];
            $unidad_minima = $query_detalle_ingreso[count($query_detalle_ingreso) - 1];

            $producto_id = $query_detalle_ingreso[$i]['producto_id'];
            $unidad = $query_detalle_ingreso[$i]['unidad_medida'];
            $cantidad_compra = $query_detalle_ingreso[$i]['cantidad'];

            $sql_inventario = $this->db->query("SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto='$producto_id' and id_local='$local'");
            $inventario_existente = $sql_inventario->row_array();

            $id_inventario = $inventario_existente['id_inventario'];
            $cantidad_vieja = $inventario_existente['cantidad'];
            $fraccion_vieja = $inventario_existente['fraccion'];

            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$producto_id'");

            $unidades_producto = $query->result_array();

            foreach ($unidades_producto as $row) {
                if ($row['id_unidad'] == $unidad) {
                    $unidad_form = $row;
                }
            }


            //var_dump($query_detalle_ingreso[$i]['unidades']);
            if ($cantidad_vieja >= 1) {

                $unidades_minimas_inventario = ($cantidad_vieja * $query_detalle_ingreso[$i]['unidades']) + $fraccion_vieja;
            } else {
                $unidades_minimas_inventario = $fraccion_vieja;
            }

            $unidades_minimas_detalle = $unidad_form['unidades'] * $cantidad_compra;


            $resta = $unidades_minimas_inventario - $unidades_minimas_detalle;

            if ($resta > 0) {
                $cont = 0;
                while ($resta >= $unidad_maxima) {
                    $cont++;
                    $resta = $resta - $unidad_maxima;
                }
                if ($cont < 1) {
                    $cantidad_nueva = 0;
                    $fraccion_nueva = $resta;
                } else {
                    $cantidad_nueva = $cont;
                    $fraccion_nueva = $resta;
                }
            } else {
                $cantidad_nueva = 0;
                $fraccion_nueva = 0;
            }


            $inventario_nuevo = array(
                'cantidad' => $cantidad_nueva,
                'fraccion' => $fraccion_nueva
            );
            $where = array('id_inventario' => $id_inventario);
            $this->update_inventario($inventario_nuevo, $where);


        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            //  $this->db->trans_rollback();
            return false;
        } else {
            $condicion = array('id_ingreso' => $id);
            $this->db->where($condicion);
            $campos = array('ingreso_status' => INGRESO_DEVUELTO);
            $this->db->update('ingreso', $campos);

            $this->cajas_model->delete_pendiente(array(
                'tipo'=>'INGRESO',
                'ref_id'=>$id
            ));

            return true;
        }

        $this->db->trans_off();

    }


    // consulta si es posible hacer la devilucion de un ingreso
    function consultar_devolucion()
    {
        $cantidad_comparar = null;
        $cantidad_total = null;

        $this->db->trans_start(true);

        $this->db->trans_begin();

        $data = array();
        $id = $this->input->post('id');
        $local = $this->input->post('local');


        $sql_detalle_ingreso = $this->db->query("SELECT * FROM detalleingreso
JOIN producto ON producto.producto_id=detalleingreso.id_producto
LEFT JOIN unidades_has_producto ON unidades_has_producto.producto_id=producto.producto_id AND unidades_has_producto.orden=1
LEFT JOIN unidades ON unidades.id_unidad=unidades_has_producto.id_unidad
JOIN ingreso ON ingreso.`id_ingreso`=detalleingreso.`id_ingreso`
WHERE detalleingreso.id_ingreso='$id'");

        $query_detalle_ingreso = $sql_detalle_ingreso->result_array();

        $devolver = true;
        for ($i = 0; $i < count($query_detalle_ingreso); $i++) {


            $local = $query_detalle_ingreso[$i]['local_id'];
            $unidad_maxima = $query_detalle_ingreso[$i]['unidades'];
            $unidad_minima = $query_detalle_ingreso[count($query_detalle_ingreso) - 1];

            $producto_id = $query_detalle_ingreso[$i]['producto_id'];
            $unidad = $query_detalle_ingreso[$i]['unidad_medida'];
            $cantidad_compra = $query_detalle_ingreso[$i]['cantidad'];

            $sql_inventario = $this->db->query("SELECT id_inventario, cantidad, fraccion
            FROM inventario where id_producto='$producto_id' and id_local='$local'");
            $inventario_existente = $sql_inventario->row_array();


            $id_inventario = $inventario_existente['id_inventario'];
            $cantidad_vieja = $inventario_existente['cantidad'];
            $fraccion_vieja = $inventario_existente['fraccion'];

            $query = $this->db->query("SELECT * FROM unidades_has_producto WHERE producto_id='$producto_id'");

            $unidades_producto = $query->result_array();

            foreach ($unidades_producto as $row) {
                if ($row['id_unidad'] == $unidad) {
                    $unidad_form = $row;
                }
            }


            //var_dump($query_detalle_ingreso[$i]['unidades']);
            if ($cantidad_vieja >= 1) {

                $unidades_minimas_inventario = ($cantidad_vieja * $query_detalle_ingreso[$i]['unidades']) + $fraccion_vieja;
            } else {
                $unidades_minimas_inventario = $fraccion_vieja;
            }

            $unidades_minimas_detalle = $unidad_form['unidades'] * $cantidad_compra;


            $resta = $unidades_minimas_inventario - $unidades_minimas_detalle;

            if ($resta > 0) {
                $cont = 0;
                while ($resta >= $unidad_maxima) {
                    $cont++;
                    $resta = $resta - $unidad_maxima;
                }
                if ($cont < 1) {
                    $cantidad_nueva = 0;
                    $fraccion_nueva = $resta;
                } else {
                    $cantidad_nueva = $cont;
                    $fraccion_nueva = $resta;
                }
            } else {
                $cantidad_nueva = 0;
                $fraccion_nueva = 0;
            }

            $cantidad_total = ($cantidad_vieja * $query_detalle_ingreso[$i]['unidades']) + $fraccion_vieja;
            $cantidad_comparar = ($cantidad_compra * $unidad_form['unidades']);

            if ($cantidad_comparar <= $cantidad_total) {

            } else {
                return false;
            }


        }


        $this->db->trans_complete();


        $this->db->trans_off();
        return true;


    }

    function select_compra($fecInicio, $fecFin)
    {
        $this->db->select('*');
        $this->db->from('v_consulta_compras c');
        $this->db->where('c.FecRegistro >= ', $fecInicio);
        $this->db->where('c.FecRegistro <= ', $fecFin);
        $query = $this->db->get();
        return $query->result();
    }

    function costosDeCompra()
    {
        $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto', 'left');
        //$this->db->group_by("id_producto");
        $query = $this->db->get('detalleingreso');
        return $query->result_array();
    }

    function get_detalles_by($campo, $valor)
    {
        $this->db->select('*');
        $this->db->from('detalleingreso');
        $this->db->join('ingreso', 'ingreso.id_ingreso=detalleingreso.id_ingreso', 'left');
        $this->db->join('unidades', 'unidades.id_unidad=detalleingreso.unidad_medida', 'left');
        $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto', 'left');
        $this->db->where($campo, $valor);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_ingresos_by($condicion)
    {
        $this->db->select('*');
        $this->db->from('ingreso');
        $this->db->join('proveedor', 'proveedor.id_proveedor=ingreso.int_Proveedor_id');
        $this->db->join('local', 'local.int_local_id=ingreso.local_id');
        $this->db->join('usuario', 'usuario.nUsuCodigo=ingreso.nUsuCodigo');
        //  $this->db->join('detalleingreso', 'detalleingreso.id_ingreso=ingreso.id_ingreso');
        $this->db->where($condicion);
        $query = $this->db->get();
        return $query->result();
    }

    function sum_ingresos_by($condicion)
    {
        $this->db->select("
            SUM(ingreso.sub_total_ingreso) as subtotal,
            SUM(ingreso.impuesto_ingreso) as impuesto,
            SUM(ingreso.total_ingreso) as total
            ");
        $this->db->from('ingreso');
        $this->db->join('proveedor', 'proveedor.id_proveedor=ingreso.int_Proveedor_id');
        $this->db->join('local', 'local.int_local_id=ingreso.local_id');
        $this->db->join('usuario', 'usuario.nUsuCodigo=ingreso.nUsuCodigo');
        //  $this->db->join('detalleingreso', 'detalleingreso.id_ingreso=ingreso.id_ingreso');
        $this->db->where($condicion);
        $query = $this->db->get();
        return $query->row();
    }

    function update_ingreso($tabla, $campos, $where)
    {
        $this->db->trans_start(true);
        $this->db->update($tabla, $campos);
        $this->db->where($where);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {

            return false;
        } else {
            return true;

        }
    }

    function cuadre_caja_egresos($where)
    {
        $w = array('DATE(fecha_registro) >=' => $where['fecha'],
            'DATE(fecha_registro) <=' => $where['fecha'],
            'ingreso_status' => COMPLETADO);
        $this->db->select('*,SUM(total_ingreso) as totalC');
        $this->db->where($w);
        $this->db->where('condiciones_pago.dias', 0);
        $this->db->from('ingreso');
        $this->db->join('condiciones_pago', 'condiciones_pago.id_condiciones=ingreso.pago');
        $query = $this->db->get();
        return $query->row_array();
    }

    function cuadre_caja_pagos($where)
    {
        $w = array('DATE(pagoingreso_fecha) >=' => $where['fecha'],
            'DATE(pagoingreso_fecha) <=' => $where['fecha']);
        $this->db->select('*,SUM(pagoingreso_monto) as montoIngreso');
        $this->db->where($w);
        $this->db->from('pagos_ingreso');

        $query = $this->db->get();
        return $query->row_array();
    }

}

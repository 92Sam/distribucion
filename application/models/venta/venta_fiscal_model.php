<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_fiscal_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    function split_documents($params, $products)
    {

        if ($this->validate_data($params, $products) == false)
            return array();

        $old_products = new ArrayObject($products);


        $max_items = $params['max_items'];
        $max_importe = isset($params['max_importe']) ? $params['max_importe'] : 0;
        $subgrupos = array();
        $subgrupos_result = array();
        $total_products = count($products);

        $total_cantidades = 0;
        foreach ($products as $p)
            $total_cantidades += $p['cantidad'];


        $cantidad_subgrupos = 1;
        //calculos la cantidad de subgrupos de las boletas
        if ($total_products > $max_items) {
            $cantidad_subgrupos = ceil($total_products / $max_items);
        }

        //distribuyo 1ro los productos normales y despues los bonos por los distintos grupos
        if ($cantidad_subgrupos > 1) {
            $index = 0;
            while (isset($products[$index])) {
                for ($i = 0; $i < $cantidad_subgrupos; $i++) {
                    if (!isset($products[$index]))
                        break;
                    if ($products[$index]['bono'] == 0)
                        $subgrupos[$i][] = $products[$index];

                    $index++;
                }
            }

            $index = 0;
            while (isset($products[$index])) {
                for ($i = 0; $i < $cantidad_subgrupos; $i++) {
                    if (!isset($products[$index]))
                        break;
                    if ($products[$index]['bono'] == 1)
                        $subgrupos[$i][] = $products[$index];

                    $index++;
                }
            }
        } else
            $subgrupos[0] = $products;


        //empiezo la iteracion de cada subgrupo
        foreach ($subgrupos as $products) {

            $total_importe = 0;
            foreach ($products as $p) {
                $total_importe += $p['cantidad'] * $p['precio'];
            }

            //aqui determina la cantidad de boletas que puede haber por importe. Si es factura o no hay importe maximo la cantidad es 1
            $cbi = $max_importe > 0 && $total_importe > $max_importe ? ceil($total_importe / $max_importe) : 1;
            $result = array();

            //voy iterando y vaciando las cantidades de los productos entre los n documentos
            for ($i = 0; $i < count($products); $i++) {
                $p_index = $products[$i]['producto_id'] . '_' . $products[$i]['unidad_id'] . '_' . $products[$i]['bono'];
                $flag = true;

                // Creo una condicion de para finita para el while
                $max_iteraciones = $cbi * $total_cantidades * $total_products * 100;

                //mientras no vacie este producto por todos los documentos no salgo
                while ($flag && (--$max_iteraciones > 0)) {
                    $counter_full = 0;
                    //Aqui itero sobre los documentos para ir vaciando de 1 en 1 las cantidades de los productos
                    for ($j = 0; $j < $cbi; $j++) {

                        if (!isset($result[$j]))
                            $result[$j] = array();

                        $importe_doc = 0;
                        foreach ($result[$j] as $doc) {
                            $importe_doc += $doc['importe'];
                        }

                        //aqui pregunto si tengo cantidad disponible del producto para vaciarlo en el documento
                        if ($products[$i]['cantidad'] > 0) {

                            //si aun no cree un registro de este producto, aqui lo creo
                            if (!isset($result[$j][$p_index])) {
                                $result[$j][$p_index] = array(
                                    'producto_id' => $products[$i]['producto_id'],
                                    'unidad_id' => $products[$i]['unidad_id'],
                                    'precio' => $products[$i]['precio'],
                                    'cantidad' => 0,
                                    'importe' => 0,
                                    'bono' => $products[$i]['bono']
                                );
                            }

                            //estas son las condiciones para permitir vaciar el producto
                            // si el importe actual del docuemnto + el precio es menor que el maximo importe permitido
                            // por documento.
                            // o si el maximo importe es igual a 0, este caso se daria en las bonificaciones y las facturas.
                            // si cumple alguna de las condiciones entonces hago el vaciado de ese producto
                            if ($importe_doc + $products[$i]['precio'] <= $max_importe || $max_importe == 0) {
                                $result[$j][$p_index]['cantidad']++;
                                $products[$i]['cantidad']--;
                                $result[$j][$p_index]['importe'] += $products[$i]['precio'];
                            } elseif ($importe_doc + $products[$i]['precio'] > $max_importe) {
                                $counter_full++;
                            }

                        }
                    }

                    //Si se llenaron todas las cantidades por limite de importe
                    //creo un nuevo espacio
                    if ($cbi == $counter_full)
                        $cbi++;
                    $counter_full = 0;

                    //aqui compruebo si esta vaciado el producto, sino vuelvo al ciclo.
                    if ($products[$i]['cantidad'] > 0) {
                        $flag = true;
                    } else
                        $flag = false;

                }


                if ($max_iteraciones <= 0) {
                    $this->save_logger('MAX_ITER', "Numero maximo de iteraciones superado", $params, $old_products, $subgrupos_result);
                    return array();
                }

            }

            //voy guardando el resultado de esta distribucion
            $subgrupos_result[] = $result;
        }

        //aqui uno los n resultados para ser devueltos
        $return = array();
        foreach ($subgrupos_result as $result)
            foreach ($result as $r)
                $return[] = $r;


        if ($this->split_test($params, $old_products, $return) == true) {
            return $return;
        } else {
            return array();
        }


    }

    function validate_data($params, $products)
    {

        $max_imp = $params['max_importe'];
        //validacion de precios de productos mayor al limite de importe
        if ($max_imp > 0) {
            foreach ($products as $prod) {
                if ($prod['precio'] > $max_imp) {
                    $this->save_logger('DATA', "Error en el precio de los productos", $params, $products);
                    return false;
                }
            }
        }

        if (count($products) == 0) {
            $this->save_logger('DATA', "Productos vacios", $params, $products);
            return false;
        }

        //validacion de items unicos
        foreach ($products as $prod) {
            $count = 0;
            foreach ($products as $prod_in) {
                $prod_item = $prod['producto_id'] . $prod['unidad_id'] . $prod['bono'];
                $prod_in_item = $prod_in['producto_id'] . $prod_in['unidad_id'] . $prod_in['bono'];
                if ($prod_item == $prod_in_item) {
                    $count++;
                }

                if ($count > 1) {
                    $this->save_logger('DATA', "Error en items unicos", $params, $products);
                    return false;
                }
            }
        }


        //validacion de parametros
        if ($params['max_items'] <= 0) {
            $this->save_logger('DATA', "Error en max_items", $params, $products);
            return false;
        }

        if ($params['max_importe'] < 0) {
            $this->save_logger('DATA', "Error en max_importe", $params, $products);
            return false;
        }

        return true;
    }

    function split_test($params, $products, $result)
    {

        $init_importe = 0;
        $limit_items = $params['max_items'];
        $limit_imp = $params['max_importe'];

        //calculo valores iniciales
        foreach ($products as $prod) {
            $init_importe += $prod['cantidad'] * $prod['precio'];
        }

        $fin_importe = 0;

        //calculo valores finales
        foreach ($result as $boletas) {
            $bol_importe = 0;
            foreach ($boletas as $bol) {
                $fin_importe += $bol['importe'];
                $bol_importe += $bol['importe'];
            }

            if ($limit_imp > 0 && $bol_importe > $limit_imp) {
                $this->save_logger('SPLIT', "Error limite de importe por boleta excedido", $params, $products, $result);
                return false;
            }

            if (sizeof($boletas) > $limit_items) {
                $this->save_logger('SPLIT', "Error limite de productos por boleta excedido", $params, $products, $result);
                return false;
            }
        }

        //comparo importes finales
        if (number_format($init_importe) - number_format($fin_importe) != 0) {
            $this->save_logger('SPLIT', "Error importe inicial e importe final no coinciden", $params, $products, $result);
            return false;
        }

        return true;
    }

    function save_logger($tipo, $msg, $params, $products, $result = array())
    {
        $data['fecha'] = date('Y-m-d h:m:s');
        $data['tipo'] = $tipo;
        $data['msg'] = $msg;
        $data['params'] = json_encode($params);
        $data['products'] = json_encode($products);
        $data['result'] = count($result) > 0 ? json_encode($result) : null;

        $this->db->trans_start();
        $this->db->insert('documentos_logger', $data);
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

}

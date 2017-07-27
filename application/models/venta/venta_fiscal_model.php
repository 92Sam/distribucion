<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class venta_fiscal_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
    }


    function split_documents($params, $products)
    {

        $max_items = $params['max_items'];
        $type_doc = $params['doc'];
        $max_importe = isset($params['max_importe']) && $type_doc == 'BOLETA' ? $params['max_importe'] : 0;

        $subgrupos = array();
        $subgrupos_result = array();
        $total_products = count($products);

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

                //mientras no vacie este producto por todos los documentos no salgo
                while ($flag) {
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
                            }
                            elseif($importe_doc + $products[$i]['precio'] > $max_importe){
                                $counter_full++;
                            }

                        }

                    }

                    //Si se llenaron todas las cantidades por limite de importe
                    //creo un nuevo espacio
                    if($cbi == $counter_full)
                        $cbi++;
                    $counter_full = 0;

                    //aqui compruebo si esta vaciado el producto, sino vuelvo al ciclo.
                    if ($products[$i]['cantidad'] > 0) {
                        $flag = true;
                    } else
                        $flag = false;

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

        return $return;

    }

}

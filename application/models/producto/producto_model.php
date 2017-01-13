<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class producto_model extends CI_Model
{

    private $tabla = 'producto';
    private $id = 'producto_id';
    private $codigo_barra = 'producto_codigo_barra';
    private $status = 'producto_estatus';
    private $nombre = 'producto_nombre';
    private $descripcion = 'producto_descripcion';
    private $proveedor = 'producto_proveedor';
    private $marca = 'producto_marca';
    private $linea = 'producto_linea';
    private $familia = 'producto_familia';
    private $grupo = 'produto_grupo';
    private $stock_min = 'producto_stockminimo';
    private $impuesto = 'producto_impuesto';
    private $producto_activo = 'producto_activo';
    private $subgrupo = 'producto_subgrupo';
    private $subfamilia = 'producto_subfamilia';


    function __construct()
    {
        parent::__construct();
    }

    //DEVUELVE EL COSTO UNITARIO PROMEDIO DEL PRODUCTO SEGUN SUS INGRESOS
    function get_costo_promedio($id)
    {
        $costo = $this->db->select('(sum(total_detalle) / sum(cantidad)) as costo_promedio')
            ->from('detalleingreso')
            ->join('ingreso', 'ingreso.id_ingreso = detalleingreso.id_ingreso')
            ->where('id_producto', $id)
            ->where('ingreso_status', 'COMPLETADO')
            ->get()->row();

        return $costo->costo_promedio != NULL ? $costo->costo_promedio : 0;
    }


    function get_all_by_local_producto($local, $precio)
    {
        $this->db->distinct();
        $this->db->select('unidades_has_precio.precio,producto.producto_nombre as nombre, ' . $this->tabla . '.*, unidades_has_producto.id_unidad, unidades.nombre_unidad, inventario.id_inventario, inventario.id_local, inventario.cantidad, inventario.fraccion ,lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto,grupos.id_grupo');
        $this->db->from($this->tabla);
        $this->db->join('unidades_has_precio', 'unidades_has_precio.id_producto=producto.producto_id', 'left');
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        $this->db->join('(SELECT DISTINCT inventario.id_producto, inventario.id_inventario, inventario.cantidad, inventario.fraccion, inventario.id_local FROM inventario WHERE inventario.id_local=' . $local . '  ORDER by id_inventario DESC ) as inventario', 'inventario.id_producto=producto.' . $this->id, 'left');
        $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.' . $this->id . ' and unidades_has_producto.orden=1', 'left');
        $this->db->join('unidades', 'unidades.id_unidad=unidades_has_producto.id_unidad', 'left');

        $this->db->group_by('producto_id');
        $this->db->order_by('nombre_grupo', 'asc');

        $where_in = array('0', '1');
        $where = array(
            $this->status => 1
        );
        $this->db->where_in($this->status, $where_in);
        $this->db->where($where);
        if ($precio == 1 OR $precio == 2) {
            $this->db->where('precio > 0');
            $this->db->where('unidades_has_precio.id_unidad IS NOT NULL');
        }
        if ($precio == 0) {
            $this->db->where('precio < 1');
            $this->db->where('precio < 1  OR unidades_has_precio.id_unidad ="" OR unidades_has_precio.id_unidad IS NULL');
        }
        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
    }


    function insertar($pe, $medidas, $unidades, $metrosCubicos)
    {

        $validar_nombre = sizeof($this->get_by('producto_nombre', $pe['producto_nombre']));
        if ($validar_nombre < 1) {
            $this->db->trans_start();
            $this->db->insert($this->tabla, $pe);
            $id_producto = $this->db->insert_id();

            $countunidad = 0;
            $this->db->where('estatus_precio', 1);
            $this->db->where('mostrar_precio', 1);
            $query = $this->db->get('precios');
            $precios_existentes = $query->result_array();


            $unidad_has_precio = array();
            if ($medidas != false) {
                foreach ($medidas as $medida) {


                    $unidad_has_producto = array(
                        "id_unidad" => $medidas[$countunidad],
                        "producto_id" => $id_producto,
                        "unidades" => $unidades[$countunidad],
                        "orden" => $countunidad + 1,
                        "metros_cubicos" => $metrosCubicos[$countunidad]
                    );

                    $countprecio = 0;

                    $precios_valor = $this->input->post('precio_valor_' . $countunidad);
                    $precios_id = $this->input->post('precio_id_' . $countunidad);


                    foreach ($precios_existentes as $pe) {


                        $unidad_has_precio[$countprecio] = array(
                            "id_precio" => $precios_id[$countprecio],
                            "id_unidad" => $medidas[$countunidad],
                            "id_producto" => $id_producto,
                            "precio" => $precios_valor[$countprecio]

                        );
                        $countprecio++;
                    }
                    $this->db->insert('unidades_has_producto', $unidad_has_producto);
                    $this->db->insert_batch('unidades_has_precio', $unidad_has_precio);

                    $countunidad++;
                }
            }


            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {
            return NOMBRE_EXISTE;
        }
    }

    function update($producto, $medidas, $unidades, $metrosCubicos)
    {
        $produc_exite = $this->get_by('producto_nombre', $producto['producto_nombre']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['producto_id'] == $producto ['producto_id']))) {
            $this->db->trans_start();
            $this->db->where($this->id, $producto['producto_id']);
            $this->db->update($this->tabla, $producto);


            $countunidad = 0;
            $this->db->where('estatus_precio', 1);
            $this->db->where('mostrar_precio', 1);
            $query = $this->db->get('precios');
            $preciose = $query->result_array();

            $id_producto = $producto['producto_id'];

            $this->db->where('producto_id', $id_producto);
            $query = $this->db->get('unidades_has_producto');
            $unidadesexistentes = $query->result_array();

            if ($medidas != false) {
                foreach ($medidas as $medida) {


                    if (isset($medidas[$countunidad])) {
                        $unidad_has_producto = array(
                            "id_unidad" => $medidas[$countunidad],
                            "producto_id" => $id_producto,
                            "unidades" => $unidades[$countunidad],
                            "orden" => $countunidad + 1,
                            "metros_cubicos" => $metrosCubicos[$countunidad]
                        );

                        $this->db->where('id_unidad', $medidas[$countunidad]);
                        $this->db->where('producto_id', $id_producto);
                        $query = $this->db->get('unidades_has_producto');
                        $unidadexiste = $query->num_rows();


                        if ($unidadexiste < 1) {
                            $this->db->insert('unidades_has_producto', $unidad_has_producto);
                        } else {
                            $this->db->where('id_unidad', $medidas[$countunidad]);
                            $this->db->where('producto_id', $id_producto);
                            $this->db->update('unidades_has_producto', $unidad_has_producto);
                        }


                        $countprecio = 0;

                        $precios_valor = $this->input->post('precio_valor_' . $countunidad);
                        $precios_id = $this->input->post('precio_id_' . $countunidad);


                        foreach ($preciose as $pe) {

                            if (isset($precios_id[$countprecio])) {
                                $unidad_has_precio = array(
                                    "id_precio" => $precios_id[$countprecio],
                                    "id_unidad" => $medidas[$countunidad],
                                    "id_producto" => $id_producto,
                                    "precio" => $precios_valor[$countprecio]

                                );


                                $this->db->where('id_precio', $precios_id[$countprecio]);
                                $this->db->where('id_unidad', $medidas[$countunidad]);
                                $this->db->where('id_producto', $id_producto);
                                $query = $this->db->get('unidades_has_precio');
                                $existeprecio = $query->num_rows();
                                if ($existeprecio < 1) {
                                    $this->db->insert('unidades_has_precio', $unidad_has_precio);
                                } else {
                                    $this->db->where('id_precio', $precios_id[$countprecio]);
                                    $this->db->where('id_unidad', $medidas[$countunidad]);
                                    $this->db->where('id_producto', $id_producto);
                                    $this->db->update('unidades_has_precio', $unidad_has_precio);
                                }
                            }
                            $countprecio++;
                        }
                    }

                    $countunidad++;


                }
            }

            foreach ($unidadesexistentes as $ue) {
                $borrarunidad = TRUE;
                $countunidad = 0;
                if ($medidas != false) {
                    foreach ($medidas as $medida) {
                        if (isset($medidas[$countunidad])) {
                            if ($ue['id_unidad'] == $medidas[$countunidad] && $ue['producto_id'] == $id_producto) {
                                $borrarunidad = FALSE;
                            }
                        }
                        $countunidad++;
                    }

                }

                if ($borrarunidad == TRUE or $medidas == false) {
                    $this->db->where('id_unidad', $ue['id_unidad']);
                    $this->db->where('producto_id', $id_producto);
                    $this->db->delete('unidades_has_producto');

                }

            }


            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        } else {

            return NOMBRE_EXISTE;

        }
    }


    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('producto');
        return $query->row_array();
    }

    function get_all_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('producto');
        return $query->result_array();
    }


    function delete($producto)
    {
        $this->db->trans_start();
        $this->db->where($this->id, $producto['producto_id']);
        $this->db->update($this->tabla, $producto);
        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function estadoDelProducto($valor)
    {

        $query = $this->db->query("SELECT `id_detalle`,unidadesV.`nombre_unidad` as nombreUnidadV, `id_venta`, ventas.`id_producto` as productoV, ventas.`precio` as precioV,
        ventas.`cantidad` as cantidadV, ventas.`unidad_medida` as unidadV, ingreso.`unidad_medida` as unidadI, unidadesI.`nombre_unidad` as nombreUnidadI,
        `detalle_importe`, `detalle_costo_promedio`, `detalle_utilidad`, ingreso.precio as precioI,ingreso.id_producto as productoI, ingreso.id_detalle_ingreso as detalleI
        FROM `detalle_venta` as ventas
        LEFT JOIN unidades as unidadesV ON unidadesV.id_unidad=ventas.unidad_medida
        LEFT JOIN detalleingreso as ingreso ON ingreso.id_producto = ventas.id_producto
        LEFT JOIN unidades as unidadesI ON unidadesI.id_unidad=ingreso.unidad_medida
        WHERE ventas.id_producto = " . $valor . " ORDER BY id_detalle,detalleI DESC LIMIT 1");
        return $query->result_array();
    }

    function estado_producto_est($valor)
    {

        $query = $this->db->query("SELECT SUM(detalle_utilidad) AS utilidad, count(id_producto) as cantidad_vendida,
        SUM(detalle_importe) AS promedio
        FROM `detalle_venta`
        WHERE detalle_venta.id_producto = " . $valor . " ");
        return $query->result_array();

    }

    function cantidad_comprada($valor)
    {

        $query = $this->db->query("SELECT  count(id_producto) as cantidad_comprada
        FROM `detalleingreso`
        WHERE detalleingreso.id_producto = " . $valor . " ");
        return $query->row_array();

    }

    function get_by_id($id)
    {
        $query = $this->db->where('producto_id', $id);
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('subgrupo', 'subgrupo.id_subgrupo = producto.producto_subgrupo', 'left');
        $this->db->join('subfamilia', 'subfamilia.id_subfamilia = producto.producto_subfamilia', 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        $query = $this->db->get('producto');
        return $query->row_array();
    }


    function select_all_producto()
    {
        $this->db->select($this->tabla . '.* ,lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto, impuestos.porcentaje_impuesto');
        $this->db->from($this->tabla);
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.' . $this->id . ' and unidades_has_producto.orden=1', 'left');
        $this->db->where($this->status . ' !=', '0');
        //$this->db->where($this->producto_activo . ' !=', '0');
        $this->db->order_by($this->nombre, 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }


    function get_all_by_local($local, $activo = false, $producto = false)
    {
        $this->db->distinct();
        $this->db->select($this->tabla . '.*, unidades_has_producto.id_unidad, unidades.nombre_unidad, inventario.id_inventario, inventario.id_local, inventario.cantidad, lineas.nombre_linea,
		 marcas.nombre_marca, familia.nombre_familia, grupos.nombre_grupo, proveedor.proveedor_nombre, impuestos.nombre_impuesto, impuestos.porcentaje_impuesto,
         subfamilia.nombre_subfamilia,subgrupo.nombre_subgrupo');
        $this->db->from($this->tabla);
        $this->db->join('lineas', 'lineas.id_linea=producto.' . $this->linea, 'left');
        $this->db->join('marcas', 'marcas.id_marca=producto.' . $this->marca, 'left');
        $this->db->join('familia', 'familia.id_familia=producto.' . $this->familia, 'left');
        $this->db->join('grupos', 'grupos.id_grupo=producto.' . $this->grupo, 'left');
        $this->db->join('proveedor', 'proveedor.id_proveedor=producto.' . $this->proveedor, 'left');
        $this->db->join('impuestos', 'impuestos.id_impuesto=producto.' . $this->impuesto, 'left');
        $this->db->join('(SELECT DISTINCT inventario.id_producto, inventario.id_inventario, inventario.cantidad,  inventario.id_local FROM inventario WHERE inventario.id_local=' . $local . '  ORDER by id_inventario DESC ) as inventario', 'inventario.id_producto=producto.' . $this->id, 'left');
        $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=producto.' . $this->id . ' and unidades_has_producto.orden=1', 'left');
        $this->db->join('unidades', 'unidades.id_unidad=unidades_has_producto.id_unidad', 'left');
        $this->db->join('subgrupo', 'subgrupo.id_subgrupo = producto.producto_subgrupo', 'left');
        $this->db->join('subfamilia', 'subfamilia.id_subfamilia = producto.producto_subfamilia', 'left');
        $this->db->group_by('producto_id');

        $this->db->where($this->status, '1');


        if ($producto != false) {
            $this->db->where('producto.producto_id', $producto);
        }

        $query = $this->db->get();
        //echo $this->db->last_query();
        return $query->result_array();
    }

    public function count_all($filter = null)
    {
        // Filter

        $this->db->where('producto_estatus', 1);

        $query = $this->db->get('producto');

        // Total Count
        return $query->num_rows();
    }

    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
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

        // echo $this->db->last_query();
        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }


    function autocomplete_marca($term)
    {
        $this->db->select('var_producto_marca as label');
        $this->db->from('producto');
        $this->db->like('var_producto_marca', $term);
        $query = $this->db->get();
        return $query->result_array();
    }
}

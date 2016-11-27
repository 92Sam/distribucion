<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class unidades_has_precio_model extends CI_Model
{
    private $table = 'unidades_has_precio';

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_all_by($id_unidad, $id_producto)
    {
        $this->db->where('id_unidad', $id_unidad);
        $this->db->where('id_producto', $id_producto);
        $this->db->order_by('id_precio', 'ASC');

        $query = $this->db->get('unidades_has_precio');

        return $query->result_array();
    }

    function get_precio_has_producto_list($condicion)
    {
        $sql=$this->db->query("SELECT precios.nombre_precio,precios.id_precio,
 unidades_has_precio.*, producto.producto_nombre, unidades.nombre_unidad, orden,grupos.id_grupo, grupos.nombre_grupo FROM precios JOIN unidades_has_precio
ON unidades_has_precio.`id_precio`= precios.`id_precio`
JOIN producto ON producto.`producto_id`=unidades_has_precio.`id_producto`
left JOIN grupos ON grupos.`id_grupo`=producto.`produto_grupo`
JOIN unidades ON unidades.`id_unidad`=unidades_has_precio.`id_unidad`
 JOIN unidades_has_producto ON  unidades_has_producto.`id_unidad`=unidades_has_precio.`id_unidad` AND
unidades_has_producto.`producto_id`=unidades_has_precio.`id_producto`
WHERE mostrar_precio=1 AND estatus_precio=1 AND producto.producto_estatus=1 AND producto.producto_activo=1 ".$condicion."
GROUP BY id_producto, precios.id_precio,unidades_has_precio.`id_unidad` ORDER BY orden asc, grupos.nombre_grupo asc");
        return $sql->result_array();
    }

    function get_precio_has_producto()
    {
        $sql=$this->db->query("SELECT precios.nombre_precio,precios.id_precio,
 unidades_has_precio.*, producto.producto_nombre, unidades.nombre_unidad, orden,grupos.id_grupo, grupos.nombre_grupo FROM precios JOIN unidades_has_precio
ON unidades_has_precio.`id_precio`= precios.`id_precio`
JOIN producto ON producto.`producto_id`=unidades_has_precio.`id_producto`
left JOIN grupos ON grupos.`id_grupo`=producto.`produto_grupo`
JOIN unidades ON unidades.`id_unidad`=unidades_has_precio.`id_unidad`
 JOIN unidades_has_producto ON  unidades_has_producto.`id_unidad`=unidades_has_precio.`id_unidad` AND
unidades_has_producto.`producto_id`=unidades_has_precio.`id_producto`
WHERE mostrar_precio=1 AND estatus_precio=1 AND producto.producto_estatus=1  AND producto.producto_activo=1
GROUP BY id_producto, precios.id_precio,unidades_has_precio.`id_unidad` ORDER BY orden asc, grupos.nombre_grupo asc");
        return $sql->result_array();
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
}

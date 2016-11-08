<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class bonificaciones_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $query = $this->db->select('bonificaciones.*,unidades.*, familia.*, grupos.*, marcas.*, lineas.*, p2.producto_nombre as producto_bonificacion, u2.nombre_unidad as unidad_bonificacion,subfamilia.*, subgrupo.*');
        $query = $this->db->where('bonificacion_status', 1);

        $query = $this->db->join('unidades', 'unidades.id_unidad=bonificaciones.id_unidad', 'left');
        $query = $this->db->join('familia', 'familia.id_familia=bonificaciones.id_familia', 'left');
        $query = $this->db->join('grupos', 'grupos.id_grupo=bonificaciones.id_grupo', 'left');
        $query = $this->db->join('marcas', 'marcas.id_marca=bonificaciones.id_marca', 'left');
        $query = $this->db->join('lineas', 'lineas.id_linea=bonificaciones.id_linea', 'left');
        $query = $this->db->join('subfamilia', 'subfamilia.id_subfamilia=bonificaciones.subfamilia_id', 'left');
        $query = $this->db->join('subgrupo', 'subgrupo.id_subgrupo=bonificaciones.subgrupo_id', 'left');

        $query = $this->db->join('producto as p2', 'p2.producto_id=bonificaciones.bono_producto');
        $query = $this->db->join('unidades as u2', 'u2.id_unidad=bonificaciones.bono_unidad');

        $query = $this->db->get('bonificaciones');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->select('bonificaciones.*, unidades.*, p2.producto_nombre as producto_bonificacion, u2.nombre_unidad as unidad_bonificacion');
        $this->db->where('bonificacion_status', 1);
        $this->db->where($campo, $valor);


        $this->db->join('unidades', 'unidades.id_unidad=bonificaciones.id_unidad', 'left');
        $this->db->join('producto as p2', 'p2.producto_id=bonificaciones.bono_producto');
        $this->db->join('unidades as u2', 'u2.id_unidad=bonificaciones.bono_unidad');

        $query = $this->db->get('bonificaciones');


        return $query->row_array();

    }

    function bonificaciones_has_producto($campo, $valor)
    {
        $this->db->select('bonificaciones_has_producto.*,producto.*');
        $this->db->join('producto','producto.producto_id = bonificaciones_has_producto.id_producto');
        $this->db->where($campo, $valor);
        $query = $this->db->get('bonificaciones_has_producto');
        return $query->result_array();

    }

    function get_all_by_condiciones($producto)
    {
        $query = $this->db->select('bonificaciones.*, unidades_has_producto.unidades, unidades.*, familia.*, grupos.*, marcas.*, lineas.*,p2.producto_nombre as producto_bonificacion, u2.nombre_unidad as unidad_bonificacion, p2.venta_sin_stock as venta_sin_stock_bono');
        $query = $this->db->where('bonificacion_status', 1);

        $where = "DATE(bonificaciones.fecha) >='" . date('Y-m-d') . " '
        AND (
             ( bonificaciones_has_producto.id_producto =" . $producto . " AND
                (
                    (producto.produto_grupo = bonificaciones.id_grupo OR bonificaciones.id_grupo IS NULL)  AND  (producto.producto_marca = bonificaciones.id_marca OR bonificaciones.id_marca IS NULL)
                     AND (producto.producto_linea = bonificaciones.id_linea OR bonificaciones.id_linea IS NULL)  AND (producto.producto_familia = bonificaciones.id_familia OR bonificaciones.id_familia IS NULL)
                     AND (producto.producto_subfamilia = bonificaciones.subfamilia_id OR bonificaciones.subfamilia_id IS NULL) AND (producto.producto_subgrupo = bonificaciones.subgrupo_id OR bonificaciones.subgrupo_id IS NULL)
                )
             )
             OR (
                bonificaciones_has_producto.id_producto IS NULL
                AND
                (
                    ((select produto_grupo from producto where producto.producto_id=" . $producto . ") = bonificaciones.id_grupo OR bonificaciones.id_grupo IS NULL)  AND  ((select producto_marca from producto where producto.producto_id=" . $producto . ") = bonificaciones.id_marca OR bonificaciones.id_marca IS NULL)
                     AND ((select producto_linea from producto where producto.producto_id=" . $producto . ") = bonificaciones.id_linea OR bonificaciones.id_linea IS NULL)  AND ((select producto_familia from producto where producto.producto_id=" . $producto . ") = bonificaciones.id_familia OR bonificaciones.id_familia IS NULL)
                     AND ((select producto_subfamilia from producto where producto.producto_id=" . $producto . ") = bonificaciones.subfamilia_id OR bonificaciones.subfamilia_id IS NULL)    AND ((select producto_subgrupo from producto where producto.producto_id=" . $producto . ") = bonificaciones.subgrupo_id OR bonificaciones.subgrupo_id IS NULL)
                )
             )
        )";

        $query = $this->db->join('bonificaciones_has_producto', 'bonificaciones_has_producto.id_bonificacion=bonificaciones.id_bonificacion', 'left');
        $query = $this->db->join('producto', 'bonificaciones_has_producto.id_producto=producto.producto_id', 'left');
        $query = $this->db->join('unidades', 'unidades.id_unidad=bonificaciones.id_unidad', 'left');
        $query = $this->db->join('familia', 'familia.id_familia=bonificaciones.id_familia', 'left');
        $query = $this->db->join('grupos', 'grupos.id_grupo=bonificaciones.id_grupo', 'left');
        $query = $this->db->join('marcas', 'marcas.id_marca=bonificaciones.id_marca', 'left');
        $query = $this->db->join('lineas', 'lineas.id_linea=bonificaciones.id_linea', 'left');
        $query = $this->db->join('producto as p2', 'p2.producto_id=bonificaciones.bono_producto');
        $query = $this->db->join('unidades as u2', 'u2.id_unidad=bonificaciones.bono_unidad');
        $query = $this->db->join('unidades_has_producto', 'unidades_has_producto.producto_id=p2.producto_id AND unidades_has_producto.id_unidad=u2.id_unidad', 'left');

        $this->db->where($where);
        $this->db->order_by('cantidad_condicion','desc');
        $this->db->order_by('bono_cantidad','desc');
        $query = $this->db->get('bonificaciones');
        //echo $this->db->last_query();
        return $query->result_array();

    }


    function insertar($bonificaciones, $productos)
    {
        $this->db->trans_start();
        $this->db->insert('bonificaciones', $bonificaciones);

        $id = $this->db->insert_id();
        if ($productos != null) {
            foreach ($productos as $p) {
                $producto = array(
                    'id_bonificacion' => $id,
                    'id_producto' => $p,
                );
                $this->db->insert('bonificaciones_has_producto', $producto);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update($bonificaciones, $productos)
    {
        $this->db->trans_start();
        $this->db->where('id_bonificacion', $bonificaciones['id_bonificacion']);
        $this->db->update('bonificaciones', $bonificaciones);

        $this->db->trans_complete();
        $data = array('id_bonificacion' => $bonificaciones['id_bonificacion']);
        $this->db->where('bonificaciones_has_producto.id_bonificacion', $bonificaciones['id_bonificacion']);
        $this->db->delete('bonificaciones_has_producto', $data);

        if ($productos != null) {
            foreach ($productos as $p) {
                $producto = array(
                    'id_bonificacion' => $bonificaciones['id_bonificacion'],
                    'id_producto' => $p,
                );
                $this->db->insert('bonificaciones_has_producto', $producto);
            }
        }
        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }
}
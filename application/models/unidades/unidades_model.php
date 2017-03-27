<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class unidades_model extends CI_Model
{

    private $table = 'unidades';

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    //$cantidad es la cantidad normal y su fraccion
    //return la cantidad minima de expresion de un producto
    public function convert_minimo_um($producto_id, $cantidad, $fraccion = 0)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();
        if ($orden_max->orden == 1)
            return $cantidad;

        $orden_min = $this->db->select_min('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();
        $this->db->select('unidades_has_producto.id_unidad as um_id, unidades_has_producto.unidades as um_number, unidades_has_producto.orden as orden');
        $this->db->from('unidades_has_producto');
        $this->db->where('producto_id', $producto_id);
        $this->db->where('orden', $orden_min->orden);
        $unidad = $this->db->get()->row();
        return ($cantidad * $unidad->um_number) + $fraccion;
    }

    //return la cantidad minima basado en su um_id
    public function convert_minimo_by_um($producto_id, $um_id, $cantidad)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $this->db->select('unidades_has_producto.id_unidad as um_id, unidades_has_producto.unidades as um_number, unidades_has_producto.orden as orden');
        $this->db->from('unidades_has_producto');
        $this->db->where('producto_id', $producto_id);
        $this->db->where('id_unidad', $um_id);
        $unidad = $this->db->get()->row();

        if ($unidad->orden == $orden_max->orden) return $cantidad;

        return $unidad->um_number * $cantidad;
    }

    public function get_cantidad_fraccion($producto_id, $cantidad_minima)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $minima_unidad = $this->db->select('id_unidad as um_id,unidades as um_number')
            ->where('producto_id', $producto_id)
            ->where('orden', $orden_max->orden)
            ->get('unidades_has_producto')->row();

        $maxima_unidad = $this->db->select('orden, id_unidad as um_id, unidades as um_number')
            ->where('producto_id', $producto_id)
            ->where('orden', 1)
            ->get('unidades_has_producto')->row();

        $result = array();
        if ($minima_unidad->um_id == $maxima_unidad->um_id) {
            $result['cantidad'] = $cantidad_minima;
            $result['fraccion'] = 0;

            return $result;
        }

        $result['cantidad'] = intval($cantidad_minima / $maxima_unidad->um_number);
        $result['fraccion'] = $cantidad_minima % $maxima_unidad->um_number;

        return $result;
    }

    function get_um_min_by_producto($producto_id)
    {
        $orden_max = $this->db->select_max('orden', 'orden')
            ->where('producto_id', $producto_id)->get('unidades_has_producto')->row();

        $minima_unidad = $this->db->select('id_unidad as um_id')
            ->where('producto_id', $producto_id)
            ->where('orden', $orden_max->orden)
            ->get('unidades_has_producto')->row();

        return $minima_unidad->um_id;
    }

    function get_nombre_unidad($id)
    {
        $temp = $this->get_by('id_unidad', $id);
        return $temp['nombre_unidad'];
    }

    function get_unidades()
    {
        $this->db->where('estatus_unidad', 1);
        $this->db->order_by('nombre_unidad', 'asc');
        $query = $this->db->get($this->table);
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get($this->table);
        return $query->row_array();
    }

    function set_unidades($grupo)
    {
        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_unidad', $nombre));

        if ($validar_nombre < 1) {
            $this->db->trans_start();
            $this->db->insert($this->table, $grupo);

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

    function update_unidades($grupo)
    {


        $produc_exite = $this->get_by('nombre_unidad', $grupo['nombre_unidad']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['id_unidad'] == $grupo ['id_unidad']))) {

            $this->db->trans_start();
            $this->db->where('id_unidad', $grupo['id_unidad']);
            $this->db->update($this->table, $grupo);

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

    function get_by_producto($producto)
    {
        $this->db->select('*');
        $this->db->from('unidades_has_producto');
        $this->db->where('unidades_has_producto.producto_id', $producto);
        $this->db->join('unidades', 'unidades_has_producto.id_unidad=unidades.id_unidad');
        $this->db->join('producto', 'producto.producto_id=unidades_has_producto.producto_id');
        $this->db->order_by('orden', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    /*Consultamos si la unidad de medida que se quiere eliminar esta asignado a algun producto*/
    function consultarUnidad($id)
    {
        $this->db->count_all('unidades_has_producto');
        $this->db->where('id_unidad', $id);
        $query = $this->db->get('unidades_has_producto');
        if ($query->num_rows() > 0)
                return true;//si tiene asociaciones
        else
                return false; //No tiene
    }




}

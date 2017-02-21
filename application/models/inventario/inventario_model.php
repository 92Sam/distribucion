<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class inventario_model extends CI_Model {

    private $table = 'inventario';

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function check_stock($datas){
        $result = array();

        foreach($datas as $data){
            $temp = $this->db->get_where('inventario', array(
                'id_producto'=>$data['producto_id'],
                'id_local'=>$data['local_id'],
            ))->row();

            if($temp == null || $temp->cantidad < $data['cantidad'])
                $result[] = $data['producto_id'];
        }

        return $result;
    }

    function get_by($campos){
        $this->db->where($campos);
        $query=$this->db->get('inventario');
        return $query->row_array();
    }

    function get_all_by_array($campos){


        $query=$this->db->query($campos);
        return $query->result();
    }

    function get_by_id_row($producto,$local){
        $sql = ("SELECT * FROM inventario WHERE id_producto='$producto' AND id_local='$local' ORDER by id_inventario DESC LIMIT 1");
        $query = $this->db->query($sql);


        return $query->row_array();
    }

    function set_inventario($campos){


        $this->db->trans_start ();
        $this->db->insert('inventario',$campos);
        $ultimo_id= $this->db->insert_id();
        $this->db->trans_complete ();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status () === FALSE)
            return FALSE;
        else
            return $ultimo_id;
    }

    function update_inventario($campos,$wheres){


        $this->db->trans_start ();
        $this->db->where($wheres);
        $this->db->update('inventario',$campos);
        $this->db->trans_complete ();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status () === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update_costo_unitario($campo, $where)
    {
        $data = array(
            'costo_unitario' => $campo
        );
        $this->db->where('producto_id', $where);
        $this->db->update('producto', $data);

    }

    function getIventarioProducto($wheres){

        $this->db->select('*');
        $this->db->from('producto');
        $this->db->join('inventario','producto.producto_id=inventario.id_producto', 'left');
        $this->db->join('unidades_has_producto','unidades_has_producto.producto_id=producto.producto_id','left');
        $this->db->join('unidades','unidades.id_unidad=unidades_has_producto.id_unidad', 'left');
        $this->db->where($wheres);
        $this->db->order_by('producto.producto_id','asc');
        $query=$this->db->get();

        return $query->result_array();
    }



    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
                             $nombre_or, $where_or,
                             $group = false,
                             $order = false, $retorno = false, $limit=false, $start=0,$order_dir=false, $like=false,$where_custom)
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
            $this->db->limit($limit,$start);
        }
        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order,$order_dir);
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


}

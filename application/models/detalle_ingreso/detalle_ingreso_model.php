<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class detalle_ingreso_model extends CI_Model {

    private $table = 'detalleingreso';
    function __construct() {
        parent::__construct();
        $this->load->database();

    }


    function get_by($select = false, $join = false, $campos_join = false, $where = false,  $group = false,$filas = false){
//si filas es igual a false entonces es un resutl que trae varios resultados
        //sino es una sola fila

        if($select !=false){
            $this->db->select($select);
            $this->db->from($this->table);


        }
        if($join != false and $campos_join != false){

            for($i=0;$i<count($join);$i++) {
                $this->db->join($join[$i], $campos_join[$i]);
            }
        }
        if($where!=false){
            $this->db->where($where);

        }
        if($group!=false){
            $this->db->group_by($group);
        }
        if($group!=false){
            $this->db->group_by($group);
        }

        $query=$this->db->get();

    if($filas!=false){
        return $query->row_array();

        }else{

        return $query->result();

    }

    }

    function get_by_result($campo, $valor){
        $this->db->select('*');
        $this->db->from('detalleingreso');
       // $this->db->join('ingreso', 'ingreso.id_ingreso=detalleingreso.id_ingreso');
        $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto');
        $this->db->join('unidades', 'unidades.id_unidad=detalleingreso.unidad_medida');
        $this->db->where($campo,$valor);
        $query=$this->db->get();
        return $query->result();
    }

    function get_by_result_array($where){
        $this->db->select('*');
        $this->db->from('detalleingreso');
        //$this->db->join('ingreso', 'ingreso.id_ingreso=detalleingreso.id_ingreso');
        $this->db->join('producto', 'producto.producto_id=detalleingreso.id_producto');
        $this->db->join('unidades', 'unidades.id_unidad=detalleingreso.unidad_medida');
        $this->db->where($where);
        $query=$this->db->get();
        return $query->result_array();
    }


}

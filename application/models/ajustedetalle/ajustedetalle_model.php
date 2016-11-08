<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ajustedetalle_model extends CI_Model {

    private $table = 'ajustedetalle';

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_ajuste_detalle($local=false){

        if($local!=false) {
            $query=$this->db->where('local_id',$local);
            $query=$this->db->get('ajustedetalle');
            return $query->result();
        }
    }

    function get_by($campos,$filas){
//si filas es igual a falso se ejecuta row. sino ejecuta row_array
        $this->db->where($campos);
        $query=$this->db->get('ajustedetalle');

        if($filas!=false) {
            return $query->result();
        }else{
            return $query->row_array();

        }
    }

    function get_ajuste_by_inventario($id_inventario){

        $sql="SELECT  * FROM ajustedetalle JOIN inventario ON ajustedetalle.`id_inventario`=inventario.`id_inventario`
JOIN producto ON producto.`producto_id`=inventario.`id_producto` JOIN ajusteinventario ON
ajusteinventario.`id_ajusteinventario`=ajustedetalle.`id_ajusteinventario`
WHERE ajustedetalle.id_ajusteinventario='$id_inventario'";
        $query = $this->db->query($sql);
        return $query->result();
    }

    function set_ajuste_detalle($campos){


        $this->db->trans_start ();
        $this->db->insert('ajustedetalle',$campos);
        $ultimo_id= $this->db->insert_id();
        $this->db->trans_complete ();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status () === FALSE)
            return FALSE;
        else
            return $ultimo_id;
    }


}

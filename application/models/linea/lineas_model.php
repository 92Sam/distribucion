<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class lineas_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_lineas(){
        $query=$this->db->where('estatus_linea',1);
        $this->db->order_by('nombre_linea', 'asc');
        $query=$this->db->get('lineas');
        return $query->result_array();
    }

    function get_by($campo, $valor){
        $this->db->where($campo,$valor);
        $query=$this->db->get('lineas');
        return $query->row_array();
    }
    function set_lineas(){

        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_linea', $nombre));

        if ($validar_nombre < 1) {

            $query_marca = array(

                'nombre_linea' => $this->input->post('nombre')

            );

            $this->db->trans_start();
            $this->db->insert('lineas', $query_marca);

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        }else{
            return NOMBRE_EXISTE;

        }
    }

    function update_lineas($grupo, $quehago = NULL)
    {

        $produc_exite=$this->get_by('nombre_linea', $grupo['nombre_linea']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_linea']==$grupo ['id_linea']))) {


            $this->db->trans_start();
            $this->db->where('id_linea', $grupo['id_linea']);
            $this->db->update('lineas', $grupo);
            if ($quehago != NULL) {
                $modificar = array("producto_linea" => NULL);
                $this->db->where('producto_linea', $grupo['id_linea']);
                $this->db->update('producto', $modificar);
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


}

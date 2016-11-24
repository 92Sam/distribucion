<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class grupos_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_grupos(){
        $this->db->where('estatus_grupo',1);
        $this->db->order_by('nombre_grupo', 'asc');
        $query=$this->db->get('grupos');
        return $query->result_array();
    }
    function get_by($campo, $valor){
        $this->db->where($campo,$valor);
        $query=$this->db->get('grupos');
        return $query->row_array();
    }

    function set_grupos(){

        $nombre=$this->input->post('nombre');
        $validar_nombre=sizeof($this->get_by('nombre_grupo',$nombre));

        if($validar_nombre<1) {
            $query_grupo = array(

                'nombre_grupo' => $nombre

            );

            $this->db->trans_start();
            $this->db->insert('grupos', $query_grupo);

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

    function update_grupos($grupo, $quehago = NULL)
    {


        $produc_exite=$this->get_by('nombre_grupo', $grupo['nombre_grupo']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_grupo']==$grupo ['id_grupo']))) {

            $this->db->trans_start();
            $this->db->where('id_grupo', $grupo['id_grupo']);
            $this->db->update('grupos', $grupo);
            if ($quehago != NULL) {
                $modificar = array("produto_grupo" => NULL);
                $this->db->where('produto_grupo', $grupo['id_grupo']);
                $this->db->update('producto', $modificar);
            }
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


}

<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class marcas_model extends CI_Model {

    function __construct() {
        parent::__construct();
        $this->load->database();
    }

    function get_marcas(){
        $query=$this->db->where('estatus_marca',1);
        $this->db->order_by('nombre_marca', 'asc');
        $query=$this->db->get('marcas');
        return $query->result_array();
    }

    function get_by($campo, $valor){
        $this->db->where($campo,$valor);
        $query=$this->db->get('marcas');
        return $query->row_array();
    }
    function set_marcas()
    {
        $nombre = $this->input->post('nombre');
        $validar_nombre = sizeof($this->get_by('nombre_marca', $nombre));

        if ($validar_nombre < 1) {

        $query_marca = array(

            'nombre_marca' => $this->input->post('nombre')

        );

        $this->db->trans_start();
        $this->db->insert('marcas', $query_marca);

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

    function update_marcas($grupo, $quehago = NULL)
    {



        $produc_exite=$this->get_by('nombre_marca', $grupo['nombre_marca']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_marca']==$grupo ['id_marca']))) {

            $this->db->trans_start();
            $this->db->where('id_marca', $grupo['id_marca']);
            $this->db->update('marcas', $grupo);
            if ($quehago != NULL) {
                $modificar = array("producto_marca" => NULL);
                $this->db->where('producto_marca', $grupo['id_marca']);
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

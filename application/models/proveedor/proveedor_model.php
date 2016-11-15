<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class proveedor_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $query = $this->db->where('proveedor_status', 1);
        $this->db->order_by('proveedor_nombre', 'asc');
        $query = $this->db->get('proveedor');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('proveedor');
        return $query->row_array();
    }

    function insertar($proveedor)
    {

        $nombre = $this->input->post('proveedor_nombre');
        $validar_nombre = sizeof($this->get_by('proveedor_nombre', $nombre));

        if ($validar_nombre < 1) {
            $this->db->trans_start();
            $this->db->insert('proveedor', $proveedor);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return TRUE;
        }else{
            return NOMBRE_EXISTE;
        }
    }

    function update($proveedor)
    {
        $produc_exite=$this->get_by('proveedor_nombre', $proveedor['proveedor_nombre']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_proveedor']==$proveedor ['id_proveedor']))) {
            $this->db->trans_start();
            $this->db->where('id_proveedor', $proveedor['id_proveedor']);
            $this->db->update('proveedor', $proveedor);

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

    function select_all_proveedor(){
        $this->db->where('proveedor_status !=','0');
        $query = $this->db->get('proveedor');
        return $query->result();
    }

}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente_datos_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    function get_all_by($valor, $where_id = array())
    {
        $this->db->where('cliente_id', $valor);
        if (count($where_id))
            $this->db->where_in('tipo', $where_id);
        $query = $this->db->get('cliente_datos');
        return $query->result_array();
    }

    function get_contacto_data($id)
    {
        $result = array();
        $datas = $this->get_all_by($id, array(
            6, 7, 8
        ));

        foreach ($datas as $data){
            if($data['tipo'] == CGERENTE_DNI)
                $result['gerente_dni'] = $data['valor'];
            elseif($data['tipo'] == CCONTACTO_DNI)
                $result['representante_dni'] = $data['valor'];
            elseif($data['tipo'] == CCONTACTO_NOMBRE)
                $result['representante'] = $data['valor'];
        }

        return $result;
    }

}
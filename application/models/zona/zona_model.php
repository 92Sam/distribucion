<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class zona_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function get_all()
    {
        /* $query = $this->db->where('ciudad_status', 1); */
        $query = $this->db->select('*');
        $query = $this->db->from('zonas');
        $query = $this->db->join('ciudades', 'ciudades.ciudad_id=zonas.ciudad_id');
        $query = $this->db->join('estados', 'estados.estados_id=ciudades.estado_id');
        $query = $this->db->join('pais', 'estados.pais_id=pais.id_pais');
        $query = $this->db->where('zonas.status', 1);
        $query = $this->db->get();
        return $query->result_array();
    }
    function buscar_id($id)
    {
        $query = $this->db->select('*');
        $query = $this->db->from('zonas');
        $query = $this->db->join('ciudades', 'ciudades.ciudad_id=zonas.ciudad_id');
        $query = $this->db->join('estados', 'estados.estados_id=ciudades.estado_id');
        $query = $this->db->join('pais', 'estados.pais_id=pais.id_pais');
        $query = $this->db->where('zona_id', $id);
        $query = $this->db->get();
        return $query->row();
    }

    function get_dias($id)
    {
        $this->db->select('dia_semana');
        $this->db->from('zona_dias');
        $this->db->where('zona_dias.id_zona', $id);
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_all_dias()
    {
        $this->db->select('*');
        $this->db->from('zona_dias');
        $query = $this->db->get();
        return $query->result_array();
    }

    function get_all_by_user($id)
    {
        $query = $this->db->select('*');
        $query = $this->db->join('zonas', 'zonas.zona_id=usuario_has_zona.id_zona');
        $query = $this->db->join('ciudades', 'zonas.ciudad_id=ciudades.ciudad_id');
        $query = $this->db->join('estados', 'ciudades.estado_id=estados.estados_id');
        $query = $this->db->join('pais', 'pais.id_pais=estados.pais_id');
        $query = $this->db->where('id_usuario', $id);
        $query = $this->db->get('usuario_has_zona');
        return $query->result_array();
    }

    function get_by_user_dia($id, $dia)
    {
        $query = $this->db->select('*');
        $query = $this->db->join('zonas', 'zonas.zona_id=usuario_has_zona.id_zona');
        $query = $this->db->join('zona_dias', 'zonas.zona_id=zona_dias.id_zona');
        $query = $this->db->where('id_usuario', $id);
        $query = $this->db->where('dia_semana', $dia);
        $query = $this->db->get('usuario_has_zona');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('zonas');
        return $query->row_array();
    }
    function get_by_form($campo, $valor)
    {
        $this->db->select('*');
        $this->db->from('usuario_has_zona');
        $this->db->join('zonas', 'zonas.zona_id=.usuario_has_zona.id_zona');
        if($valor != 0) {
            $this->db->where($campo, $valor);
        }
        $query = $this->db->get();
        return $query->result_array();
    }
    function get_all_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('zonas');
        return $query->result_array();
    }

    function insertar($zona)
    {

        $this->db->trans_start();
        $this->db->insert('zonas', $zona);
        $id=$this->db->insert_id();

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return $id;

        $this->db->trans_off();
    }

    function insertar_zona_dias($id_zona, $dia)
    {
        $data = array(
            'id_zona' => $id_zona,
            'dia_semana' => $dia,
        );

        $this->db->trans_start();
        $this->db->insert('zona_dias', $data);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update($zona)
    {

        $this->db->trans_start();
        $this->db->where('zona_id', $zona['zona_id']);
        $this->db->update('zonas', $zona);

        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function delete_zona_dias($id)
    {
        $this->db->trans_start();
        $this->db->where('id_zona', $id);
        $this->db->delete('zona_dias');

        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function  delete_usuario_has_zona($id)
    {
        $this->db->trans_start();
        $this->db->where('id_zona', $id);
        $this->db->delete('usuario_has_zona');

        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE)
            return FALSE;
        else
            return TRUE;
    }

}

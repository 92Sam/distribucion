<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gastos_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $query = $this->db->where('status_gastos', 1);
        $query = $this->db->join('tipos_gasto', 'tipos_gasto.id_tipos_gasto=gastos.tipo_gasto');
        $query = $this->db->join('local', 'gastos.local_id=local.int_local_id');
        $query = $this->db->get('gastos');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('gastos');
        return $query->row_array();
    }

    function insertar($gastos)
    {

        $this->db->trans_start();
        $this->db->insert('gastos', $gastos);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    function update($gastos)
    {

        $this->db->trans_start();
        $this->db->where('id_gastos', $gastos['id_gastos']);
        $this->db->update('gastos', $gastos);

        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }
    function cuadre_caja_pagos($where)
    {
        $w = array('DATE(fecha) >=' => $where['fecha'],
            'DATE(fecha) <=' => $where['fecha'] );
        $this->db->select('*,SUM(total) as totalGastos');
        $this->db->where($w);
        $this->db->from('gastos');

        $query = $this->db->get();
        return $query->row_array();
    }

}
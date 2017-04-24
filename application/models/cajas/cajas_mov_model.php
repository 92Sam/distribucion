<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas_mov_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function save_mov($data = array())
    {

        $data['created_at'] = date('Y-m-d H:i:s');

        $this->db->insert('caja_movimiento', $data);
    }

    function get_movimientos_today($cuenta_id, $data = array())
    {
        if(!isset($data['fecha_ini'])) $data['fecha_ini'] = date('Y-m-d') . ' 00:00:00';
        if(!isset($data['fecha_fin'])) $data['fecha_fin'] = date('Y-m-d') . ' 23:59:59';

        $this->db->select('
            caja_movimiento.*,
            usuario.nombre as usuario_nombre,
            caja.moneda_id as moneda_id,
            local.local_nombre as local_nombre
        ')
            ->from('caja_movimiento')
            ->join('caja_desglose', 'caja_desglose.id = caja_movimiento.caja_desglose_id')
            ->join('caja', 'caja.id = caja_desglose.caja_id')
            ->join('local', 'caja.local_id = local.int_local_id')
            ->join('usuario', 'usuario.nUsuCodigo = caja_movimiento.usuario_id')
            ->where('caja_movimiento.caja_desglose_id', $cuenta_id)
            ->where('caja_movimiento.created_at >=', $data['fecha_ini'])
            ->where('caja_movimiento.created_at <=', $data['fecha_fin'])
            ->order_by('caja_movimiento.created_at', 'DESC');

        return $this->db->get()->result();
    }

}
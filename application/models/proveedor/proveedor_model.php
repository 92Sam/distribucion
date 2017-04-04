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

    function get_cuentas_pagar($data = array()){

        $consulta = "
            SELECT 
                ingreso.id_ingreso as ingreso_id,
                ingreso.tipo_documento as documento_nombre,
                ingreso.documento_serie as documento_serie,
                ingreso.documento_numero as documento_numero,
                proveedor.proveedor_nombre as proveedor_nombre,
                ingreso.fecha_emision as fecha_emision,
                ingreso.total_ingreso as monto_venta,
                (SELECT 
                        SUM(pagos_ingreso.pagoingreso_monto)
                    FROM
                        pagos_ingreso
                    WHERE
                        pagos_ingreso.pagoingreso_ingreso_id = ingreso.id_ingreso) AS monto_pagado,
                DATEDIFF(CURDATE(), (ingreso.fecha_emision)) as atraso, 
                ingreso.pago as pago
            FROM
                (ingreso)
                    JOIN
                proveedor ON ingreso.int_Proveedor_id = proveedor.id_proveedor 
                    LEFT JOIN
                pagos_ingreso ON pagos_ingreso.pagoingreso_ingreso_id = ingreso.id_ingreso
            WHERE
                ingreso.ingreso_status = 'COMPLETADO'  
                AND (SELECT 
                        COUNT(pagos_ingreso.pagoingreso_id)
                    FROM
                        pagos_ingreso
                    WHERE
                        pagos_ingreso.pagoingreso_ingreso_id = ingreso.id_ingreso 
                        AND pagos_ingreso.pagoingreso_restante = 0.00) <= 0 
                        AND ingreso.pago = 'CREDITO' 
        ";

        if(isset($data['proveedor_id']))
            $consulta .= " AND ingreso.int_Proveedor_id =".$data['proveedor_id'];

        if(isset($data['documento']))
            $consulta .= " AND ingreso.tipo_documento ='".$data['documento']."'";


        $consulta .= " GROUP BY ingreso.id_ingreso";

        return $this->db->query($consulta)->result();
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

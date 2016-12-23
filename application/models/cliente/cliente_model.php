<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cliente_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function count_all($filter = null)
    {
        // Filter

        $this->db->where('cliente_status', 1);

        $query = $this->db->get('cliente');

        // Total Count
        return $query->num_rows();
    }

    function get_all($id_vendedor = null, $filter = null, $page = 0, $limit = 0)
    {
        $this->db->select('distinct(cliente.id_cliente),cliente.*, cli_dat.*, cli_dat2.*, ciudades.*,estados.*,pais.*, grupos_cliente.*,  zonas.* , usuario.nombre');
        $this->db->from('cliente');
        $this->db->join('ciudades', 'ciudades.ciudad_id=cliente.ciudad_id');
        $this->db->join('estados', 'ciudades.estado_id=estados.estados_id');
        $this->db->join('pais', 'pais.id_pais=estados.pais_id');
        $this->db->join('grupos_cliente', 'grupos_cliente.id_grupos_cliente=cliente.grupo_id');
        $this->db->join('zonas', 'zonas.zona_id=cliente.id_zona');
        $this->db->join('usuario', 'usuario.nUsuCodigo=cliente.vendedor_a');
        $this->db->join('(SELECT c.cliente_id, c.tipo, c.valor as direccion, c.principal, COUNT(*) FROM cliente_datos c WHERE c.tipo =1 GROUP BY c.cliente_id, c.tipo) cli_dat', 'cli_dat.cliente_id = cliente.id_cliente');
        $this->db->join('(SELECT c1.cliente_id, c1.tipo, c1.valor as telefono1, c1.principal, COUNT(*) FROM cliente_datos c1 WHERE c1.tipo =2 GROUP BY c1.cliente_id, c1.tipo  ) cli_dat2', 'cli_dat2.cliente_id = cliente.id_cliente', 'left');

        $this->db->where('cliente.cliente_status', 1);

        // Vendedor ID
        if (!empty($id_vendedor)) {
            $this->db->where('cliente.vendedor_a', $id_vendedor);
        }

        // Pagination
        if ($page >= 0 && $limit > 0) {
            $start = $page * $limit;
            $this->db->limit($limit, $start);
        }

        $query = $this->db->get();

        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $this->db->join('ciudades', 'ciudades.ciudad_id=cliente.ciudad_id');
        $this->db->join('estados', 'ciudades.estado_id=estados.estados_id');
        $this->db->join('pais', 'pais.id_pais=estados.pais_id');
        $query = $this->db->get('cliente');
        return $query->row_array();
    }

    function insertar($cliente, $items, $cdatos = null)
    {
        $validar_nombre = $this->validarTipoCliente($cliente);

        if ($validar_nombre < 1) {
            $fech = date('Y-m-d');
            $this->db->trans_start();
            $this->db->insert('cliente', $cliente);
            $id_usu = $this->db->insert_id();
            $vendedr = $cliente['vendedor_a'];
            $ven = array(
                'f_asinacion' => $fech,
                'id_cliente' => $id_usu,
                'id_vendedor' => $vendedr,
            );


            $this->db->insert('cliente_v', $ven);


            /* $direccion = array(
                 'direccion' => $cliente['direccion'],
                 'cliente_id' => $id_usu,
                 'fecha' => $fech,
             );
             $this->db->insert('cliente_direccion', $direccion);
             */

            if (isset($cdatos['gerente_dni']) && $cdatos['gerente_dni'] != '' && $cliente['tipo_cliente'] == 0) {
                $this->db->insert('cliente_datos', array(
                    'cliente_id' => $id_usu,
                    'tipo' => CGERENTE_DNI,
                    'valor' => $cdatos['gerente_dni'],
                    'principal' => 0
                ));
            }

            if (isset($cdatos['representante_dni']) && $cdatos['representante_dni'] != '') {
                $this->db->insert('cliente_datos', array(
                    'cliente_id' => $id_usu,
                    'tipo' => CCONTACTO_DNI,
                    'valor' => $cdatos['representante_dni'],
                    'principal' => 0
                ));
            }

            if (isset($cdatos['representante']) && $cdatos['representante'] != '') {
                $this->db->insert('cliente_datos', array(
                    'cliente_id' => $id_usu,
                    'tipo' => CCONTACTO_NOMBRE,
                    'valor' => $cdatos['representante'],
                    'principal' => 0
                ));
            }


            for ($i = 0; $i < count($items); $i++) {

                if ($items[$i][2] == 'true') {
                    $principal = true;

                } else {
                    $principal = false;
                }

                $datos = array(
                    'cliente_id' => $id_usu,
                    'tipo' => $items[$i][0],
                    'valor' => $items[$i][1],
                    'principal' => $principal
                );
                $this->db->insert('cliente_datos', $datos);

                /*
                if($items[$i][0]==1){
                    $direccion = array(
                        'direccion' => $items[$i][1],
                        'cliente_id' => $id_usu,
                        'fecha' => $fech,
                        );
                    $this->db->insert('cliente_direccion', $direccion);
                }*/
            }


            try {
                $this->db->trans_complete();
            } catch (Exception $e) {
                return $this->db->_errosr_message();
            }

            if ($this->db->trans_status() === FALSE) {
                return $this->db->_error_message();
            } else {
                return TRUE;
            }
        } else {
            return CEDULA_EXISTE;
        }
    }

    function update($cliente, $items, $cdatos = null)
    {
        $produc_exite = $this->get_by('ruc_cliente', $cliente['ruc_cliente']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 || ($validar_nombre > 0 && ($produc_exite ['id_cliente'] == $cliente ['id_cliente']))) {

            $this->db->trans_start();

            $this->db->select('*');
            $this->db->from('cliente_v');
            $this->db->where('id_cliente', $cliente['id_cliente']);
            $this->db->order_by("id_cv", "desc");
            $this->db->limit(1);
            $query = $this->db->get();
            $vea = $query->row();
            $vendedr = $cliente['vendedor_a'];
            $fech = date('Y-m-d');
            if (count($vea) > 0 && $vendedr != $vea->id_vendedor) {
                $usuario['vendedor_a'] = $vendedr;

                $ven = array(
                    'f_asinacion' => $fech,
                    'id_cliente' => $cliente['id_cliente'],
                    'id_vendedor' => $vendedr,
                );
                $this->db->insert('cliente_v', $ven);
            }
            $this->db->where('id_cliente', $cliente['id_cliente']);
            $this->db->update('cliente', $cliente);


            $this->db->where('cliente_id', $cliente['id_cliente']);
            $this->db->delete('cliente_datos');

            if (isset($cdatos['gerente_dni']) && $cdatos['gerente_dni'] != '' && $cliente['tipo_cliente'] == 0) {
                $this->db->insert('cliente_datos', array(
                    'cliente_id' => $cliente['id_cliente'],
                    'tipo' => CGERENTE_DNI,
                    'valor' => $cdatos['gerente_dni'],
                    'principal' => 0
                ));
            }

            if (isset($cdatos['representante_dni']) && $cdatos['representante_dni'] != '') {
                $this->db->insert('cliente_datos', array(
                    'cliente_id' => $cliente['id_cliente'],
                    'tipo' => CCONTACTO_DNI,
                    'valor' => $cdatos['representante_dni'],
                    'principal' => 0
                ));
            }

            if (isset($cdatos['representante']) && $cdatos['representante'] != '') {
                $this->db->insert('cliente_datos', array(
                    'cliente_id' => $cliente['id_cliente'],
                    'tipo' => CCONTACTO_NOMBRE,
                    'valor' => $cdatos['representante'],
                    'principal' => 0
                ));
            }

            if (count($items) > 0) {


                for ($i = 0; $i < count($items); $i++) {

                    if ($items[$i][2] == 'true') {
                        $principal = true;

                    } else {
                        $principal = false;
                    }

                    $datos = array(
                        'cliente_id' => $cliente['id_cliente'],
                        'tipo' => $items[$i][0],
                        'valor' => $items[$i][1],
                        'principal' => $principal
                    );
                    $this->db->insert('cliente_datos', $datos);

                    /*
                    if($items[$i][0]==1){
                        $direccion = array(
                            'direccion' => $items[$i][1],
                            'cliente_id' => $cliente['id_cliente'],
                            'fecha' => $fech,
                            );
                        $this->db->insert('cliente_direccion', $direccion);
                    }
                    */
                }

            }


            /*     if (isset($direcc['direccion']) or sizeof($direcc)!=0) {
                     if ($direcc['direccion'] != $cliente['direccion']) {
                         $direccion = array(
                             'direccion' => $cliente['direccion'],
                             'cliente_id' => $cliente['id_cliente'],
                             'fecha' => $fech,
                         );
                         $this->db->insert('cliente_direccion', $direccion);
                     }
                 }*/


            $this->db->trans_complete();

            if ($this->db->trans_status() == FALSE) {
                return $this->db->last_query();
            } else {

                return TRUE;
            }
        } else {
            return CEDULA_EXISTE;
        }
    }

    function allcv()
    {
        $this->db->select('*');
        $this->db->from('cliente_v');
        $this->db->where('actual', '1');
        $query = $this->db->get();
        return $query->result_array();

    }

    function update2($cliente)
    {

        $this->db->trans_start();
        $this->db->where('id_cliente', $cliente['id_cliente']);
        $this->db->update('cliente', $cliente);
        $vendedr = $this->input->post('vendedor', true);
        $fech = date('y-m-d');
        if (!empty($vendedr) && $vendedr != 0) {
            $ven = array(
                'f_asinacion' => $fech,
                'id_cliente' => $cliente['id_cliente'],
                'id_vendedor' => $vendedr,
            );
            $this->db->insert('cliente_v', $ven);
        }
        $this->db->trans_complete();

        if ($this->db->trans_status() == FALSE) {
            return FALSE;
        } else {
            return TRUE;

        }
    }

    function get_total_cuentas_por_cobrar()
    {
        $sql = "SELECT SUM(dec_cronpago_pagocuota) as suma FROM `cronogramapago` WHERE dec_cronpago_pagorecibido = 0.00";
        $query = $this->db->query($sql);
        return $query->row_array();
    }


    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
                             $nombre_or, $where_or,
                             $group = false,
                             $order = false, $retorno = false, $limit = false, $start = 0, $order_dir = false, $like = false, $where_custom)
    {
        if ($select != false) {
            $this->db->select($select);
            $this->db->from('cliente');
        }
        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    for ($t = 0; $t < count($tipo_join); $t++) {

                        if ($tipo_join[$t] != "") {

                            $this->db->join($join[$i], $campos_join[$i], $tipo_join[$t]);
                        }

                    }

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);
        }
        if ($like != false) {
            $this->db->like($like);
        }
        if ($where_custom != false) {
            $this->db->where($where_custom);
        }

        if ($nombre_in != false) {
            for ($i = 0; $i < count($nombre_in); $i++) {
                $this->db->where_in($nombre_in[$i], $where_in[$i]);
            }
        }

        if ($nombre_or != false) {
            for ($i = 0; $i < count($nombre_or); $i++) {
                $this->db->or_where($where_or);
            }
        }

        if ($limit != false) {
            $this->db->limit($limit, $start);
        }
        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order, $order_dir);
        }

        $query = $this->db->get();

        // echo $this->db->last_query();
        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }


    function DniRucEnBd($identificacion, $cliente_id)
    {
        $this->db->where('ruc_cliente', $identificacion);
        $this->db->where('id_cliente <>', $cliente_id);
        $sql = $this->db->get('cliente');
        $data = $sql->row_array();

        if (count($data) > 0) {
            return true;
        } else {
            return false;
        }

    }

    function validarTipoCliente($cliente)
    {
        return sizeof($this->get_by('ruc_cliente', $cliente['ruc_cliente']));
    }

}
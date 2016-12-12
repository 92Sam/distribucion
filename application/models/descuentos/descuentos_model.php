<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class descuentos_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


    function get_all()
    {

        $this->db->where('status', 1);
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get('descuentos');
        return $query->result_array();

    }
	
	function get_by_groupclie($id)
    {

        $this->db->where('status', 1);
        $this->db->where('id_grupos_cliente', $id);
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get('descuentos');
        return $query->result_array();

    }
	
    function get_desc_list($condicion)
    {

        $this->db->where('status', 1);
        if(!empty($condicion)) {
            $this->db->where($condicion);
        }
        $this->db->order_by('nombre', 'asc');
        $query = $this->db->get('descuentos');
        return $query->result_array();

    }
    function get_escalas_descuento($id)
    {
        $this->db->select('*');
        $this->db->from('descuentos');
        $this->db->join('escalas', 'escalas.regla_descuento = descuentos.descuento_id', 'left');
        $this->db->join('escala_producto', 'escala_producto.escala = escalas.escala_id', 'left');
        $this->db->join('producto', 'producto.producto_id = escala_producto.producto', 'left');
        $this->db->where('descuento_id', $id);
        $this->db->order_by('cantidad_minima','asc');
        //$this->db->group_by();


        $query = $this->db->get();

        return $query->result_array();

    }
    function get_escalas_descuento_head($id)
    {
        $this->db->select('*');
        $this->db->from('descuentos');
        $this->db->join('escalas', 'escalas.regla_descuento = descuentos.descuento_id', 'left');
        $this->db->join('escala_producto', 'escala_producto.escala = escalas.escala_id', 'left');
        $this->db->where('descuento_id', $id);
        $this->db->group_by('cantidad_minima','cantidad_maxima');
        $this->db->order_by('cantidad_minima','asc');



        $query = $this->db->get();

        return $query->result_array();

    }
    function get_escalas_descuento_list($id,$condicion)
    {
        $this->db->select('*');
        $this->db->from('descuentos');
        $this->db->join('escalas', 'escalas.regla_descuento = descuentos.descuento_id', 'left');
        $this->db->join('escala_producto', 'escala_producto.escala = escalas.escala_id', 'left');
        $this->db->join('producto', 'producto.producto_id = escala_producto.producto', 'left');
        $this->db->where('descuento_id', $id);
        if(!empty($condicion)) {
            $this->db->like($condicion);
        }
        $this->db->order_by('cantidad_minima','asc');
        //$this->db->group_by();


        $query = $this->db->get();

        return $query->result_array();

    }
    function get_escalas_descuento_head_list($id)
    {
        $this->db->select('*');
        $this->db->from('descuentos');
        $this->db->join('escalas', 'escalas.regla_descuento = descuentos.descuento_id', 'left');
        $this->db->join('escala_producto', 'escala_producto.escala = escalas.escala_id', 'left');
        $this->db->join('producto', 'producto.producto_id = escala_producto.producto', 'left');
        $this->db->where('descuento_id', $id);
        if(!empty($condicion)) {
            $this->db->like($condicion);
        }
        $this->db->group_by('cantidad_minima','cantidad_maxima');
        $this->db->order_by('cantidad_minima','asc');



        $query = $this->db->get();

        return $query->result_array();

    }
    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('descuentos');
        return $query->row_array();
    }

    function get_escalas_by_descuento($id)
    {
        $query = $this->db->select('*');
        $query = $this->db->where('regla_descuento', $id);
        $query = $this->db->get('escalas');
        return $query->result_array();
    }

    function get_all_escalas()
    {
        $query = $this->db->select('*');
        $query = $this->db->get('escalas');
        return $query->result_array();
    }

    function get_prod_precioventa()
    {
        $this->db->select('*');
        $this->db->from('producto');
        $this->db->join('unidades_has_precio', 'unidades_has_precio.id_producto = producto.producto_id', 'join');
        $query = $this->db->get();
        return $query->result_array();
    }

    function edit_descuentos($where, $group)
    {

        $sql = "SELECT * FROM escala_producto
                JOIN producto ON escala_producto.producto = producto.producto_id
                JOIN unidades ON escala_producto.unidad = unidades.id_unidad
                JOIN escalas ON escala_producto.escala = escalas.escala_id
                JOIN descuentos ON descuentos.descuento_id = escalas.regla_descuento
        ";

        if ($where != false) {
            $sql .= " " . $where . " ";
        }


        if ($group != false) {

            $sql .= " group by " . $group . " ";
        }


        $sql .= " order by producto_id asc ";
        $query = $this->db->query($sql);
        //echo $this->db->last_query();
        return $query->result_array();

    }


    function descuentoProducto($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $this->db->where('descuentos.status', 1);
        $this->db->join('escala_producto', 'escala_producto.producto=producto.producto_id');
        $this->db->join('escalas', 'escalas.escala_id=escala_producto.escala');
        $this->db->join('descuentos', 'descuentos.descuento_id=escalas.regla_descuento');
        $this->db->join('unidades', 'unidades.id_unidad=escala_producto.unidad');
        $query = $this->db->get('producto');
        return $query->result_array();


    }



    function update($descuento)
    {

        $this->db->trans_start();
        $this->db->where('descuento_id', $descuento['descuento_id']);
        $this->db->update('descuentos', $descuento);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return $descuento;
    }


    function delete($descuento)
    {

        $this->db->trans_start();
        $this->db->where('descuento_id', $descuento['descuento_id']);
        $this->db->update('descuentos', $descuento);

        //$sql = "select * from descuentos join escalas on escalas.regla_descuento=descuentos.descuento_id where descuento_id= ".$descuento['descuento_id'];
        $this->db->select('*');
        $this->db->from('descuentos');
        $this->db->join('escalas', 'escalas.regla_descuento = descuento_id');
        $this->db->where('descuento_id', $descuento['descuento_id']);
        $query = $this->db->get();
        //$query = $this->db->query($sql);
        $buscar_escalas = $query->result_array();

        for ($i = 0; $i < count($buscar_escalas); $i++) {

            $this->db->delete('escala_producto', array('escala'=>$buscar_escalas[$i]["escala_id"]));

        }


        $this->db->delete('escalas', array('regla_descuento'=>$buscar_escalas[0]["descuento_id"]));

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return $descuento;
    }


    function insertar_descuento($cab_pie, $detalle_escala, $detalle_producto, $detalle_precio, $grupo)
    {

        $this->db->trans_start(true);

        $this->db->trans_begin();

        $descuento = array(
            'nombre' => $cab_pie['nombre'],
            'id_grupos_cliente' => $grupo,
        );

        $this->db->insert('descuentos', $descuento);
        $id_descuento = $this->db->insert_id();

        $datas = array();
        $i = 0;
        if ($detalle_producto != null) {
            foreach ($detalle_escala as $row) {

                $list_p = array(
                    'regla_descuento' => $id_descuento,
                    'cantidad_minima' => $row->desde,
                    'cantidad_maxima' => $row->hasta
                );
                $this->db->insert('escalas', $list_p);
                $id_escala = $this->db->insert_id();

                foreach ($detalle_producto as $row) {
                    $list_pa = array(
                        'escala' => $id_escala,
                        'producto' => $row->Codigo,
                        'precio' => $detalle_precio[$i],
                        'unidad' => $row->unidad
                    );
                    $i++;
                    array_push($datas, $list_pa);


                }
            }
        }
        $this->db->insert_batch('escala_producto', $datas);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return $id_descuento;
        }
        $this->db->trans_off();

    }


    function actualizar_descuento($cab_pie, $detalle_escala, $detalle_producto, $detalle_precio)
    {

        $this->db->trans_start(true);

        $this->db->trans_begin();

        $descuento = array(
            'nombre' => $cab_pie['nombre'],
            'descuento_id' => $cab_pie['descuento_id']
        );
        $i = 0;
        ///actualizo el descuento
        $this->update($descuento);

        $sql = "select * from descuentos join escalas on escalas.regla_descuento=descuentos.descuento_id where descuento_id= ".$cab_pie['descuento_id'];
        $query = $this->db->query($sql);
        $buscar_escalas = $query->result_array();

        for ($i = 0; $i < count($buscar_escalas); $i++) {

            $this->db->delete('escala_producto', array('escala'=>$buscar_escalas[$i]["escala_id"]));

        }


        $this->db->delete('escalas', array('regla_descuento'=>$buscar_escalas[0]["descuento_id"]));

        $datas = array();
        $i = 0;

        //var_dump($detalle_producto);
        if ($detalle_producto != null) {

            foreach ($detalle_escala as $row) {


                $list_p = array(
                    'regla_descuento' => $cab_pie['descuento_id'],
                    'cantidad_minima' => $row->desde,
                    'cantidad_maxima' => $row->hasta
                    );
                $this->db->insert('escalas', $list_p);
                $id_escala = $this->db->insert_id();

                foreach ($detalle_producto as $row) {
                    $list_pa = array(
                        'escala' => $id_escala,
                        'producto' => $row->Codigo,
                        'precio' => $detalle_precio[$i],
                        'unidad' => $row->unidad
                    );
                    $i++;
                    $this->db->insert('escala_producto', $list_pa);

                }

            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            return false;
        } else {
            return true;
        }
        $this->db->trans_off();

    }

}

<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class bonificador_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    private $bonificaciones;

    function bonificar($grupo, $productos)
    {

        $this->bonificaciones = array();
        $bonos = array();

        foreach ($productos as $p) {
            $bonos = $this->db->select('
                b.id_bonificacion AS bono_id,
                b.id_unidad AS um_id,
                b.cantidad_condicion AS cantidad_condicion,
                b.bono_producto AS bono_producto_id,
                b.bono_unidad AS bono_um_id,
                b.bono_cantidad AS bono_cantidad
            ')
                ->from('bonificaciones AS b')
                ->where('b.id_grupos_cliente', $grupo)
                ->get()->result();
        }

        $temp = $this->regla1($bonos, $productos);

        return $this->bonificaciones;
    }

    private function regla1($bonos, $productos)
    {
        foreach ($bonos as $b) {
            $cantidad_condicion = 0;
            $prods_id = array();
            $index = $b->bono_producto_id . '_' . $b->bono_um_id;

            $bono_has_producto = $this->db->get_where('bonificaciones_has_producto', array(
                'id_bonificacion' => $b->bono_id
            ))->result();

            foreach ($bono_has_producto as $bhp) {

                foreach ($productos as $p) {
                    if ($p['producto_id'] == $bhp->id_producto && $p['unidad_id'] == $b->um_id) {
                        if (!isset($this->bonificaciones[$index])) {
                            $this->bonificaciones[$index] = array(
                                'producto_id' => $b->bono_producto_id,
                                'unidad_id' => $b->bono_um_id,
                                'cantidad' => 0,
                                'flag' => true
                            );
                        }

                        $prods_id[] = $bhp->id_producto;
                        $cantidad_condicion += $p['cantidad'];


                    }
                }
            }

            if (isset($this->bonificaciones[$index]) && $this->bonificaciones[$index]['flag'] == true) {
                $this->bonificaciones[$index]['flag'] = false;

                $bono_has_producto = $this->db->where_in('id_producto', $prods_id)
                    ->get('bonificaciones_has_producto')->result();

                $bonos_id = array();
                foreach ($bono_has_producto as $bhp){
                    $bonos_id[] = $bhp->id_bonificacion;
                }

                do {
                    $condiciones_bono = $this->db->select('*')
                        ->from('bonificaciones as b')
                        ->where('bono_producto', $this->bonificaciones[$index]['producto_id'])
                        ->where('bono_unidad', $this->bonificaciones[$index]['unidad_id'])
                        ->where('cantidad_condicion <=', $cantidad_condicion)
                        ->where_in('id_bonificacion', $bonos_id)
                        ->order_by('cantidad_condicion', 'DESC')
                        ->get()->result();


                    if (count($condiciones_bono) > 0) {
                        $multi = 0;
                        for ($i = $condiciones_bono[0]->cantidad_condicion; $i <= $cantidad_condicion; $i += $condiciones_bono[0]->cantidad_condicion) {
                            $multi++;
                        }

                        $this->bonificaciones[$index]['cantidad'] += $condiciones_bono[0]->bono_cantidad * $multi;
                        $cantidad_condicion = $cantidad_condicion % $condiciones_bono[0]->cantidad_condicion;
                    }

                } while (count($condiciones_bono) > 0);
            }

        }
    }

}
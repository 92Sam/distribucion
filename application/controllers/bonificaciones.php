<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class bonificaciones extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('bonificaciones/bonificaciones_model');
        $this->load->model('producto/producto_model');
        $this->load->model('unidades/unidades_model');
        $this->load->model('familia/familias_model');
        $this->load->model('grupos/grupos_model');
        $this->load->model('marca/marcas_model');
        $this->load->model('linea/lineas_model');
        $this->load->model('subfamilia/subfamilias_model');
        $this->load->model('subgrupos/subgrupos_model');

    }

    /*function very_sesion()
    {
        if (!$this->session->userdata('nUsuCodigo')) {
            redirect(base_url() . 'inicio');
        }
    }*/

    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['bonificacioness'] = array();
        $bonificaciones = $this->bonificaciones_model->get_all();


        foreach ($bonificaciones as $b) {


            $b['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $b['id_bonificacion']);
          //  var_dump($b);
            $data['bonificacioness'][]=$b;
        }


        $dataCuerpo['cuerpo'] = $this->load->view('menu/bonificaciones/bonificaciones', $data, true);
        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }



    function verproductos($id=FALSE)
    {

        $data = array();
        if ($id != FALSE && $id != 'false') {


            $data['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $id);

        }

        $this->load->view('menu/bonificaciones/verproductos', $data);

    }




    function form($id = FALSE, $p1 = FALSE, $p2 = FALSE)
    {

        $data = array();
        if ($id != FALSE && $id != 'false') {
            $data['bonificaciones'] = $this->bonificaciones_model->get_by('id_bonificacion', $id);
            $data['bonificaciones_has_producto'] = $this->bonificaciones_model->bonificaciones_has_producto('id_bonificacion', $id);

        }


        if ($p1 != FALSE && $p1 != 'false') {
            $data['unidades'] = $this->unidades_model->get_by_producto($p1);

        }
        if ($p2 != FALSE && $p2 != 'false') {
            $data['unidades_bono'] = $this->unidades_model->get_by_producto($p2);

        }
        $productos=$this->producto_model->select_all_producto();
        $data['productos'] = $productos;
        $data['familias'] = $this->familias_model->get_familias();
        $data['grupos'] = $this->grupos_model->get_grupos();
        $data['marcas'] = $this->marcas_model->get_marcas();
        $data['lineas'] = $this->lineas_model->get_lineas();
        $data['bonoproducto'] = $productos;
        $data['bonounidad'] = $this->unidades_model->get_unidades();
        $data['subgrupos'] = $this->subgrupos_model->get_subgrupos();
        $data['subfamilias'] = $this->subfamilias_model->get_subfamilias();

        $this->load->view('menu/bonificaciones/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $producto_condicion = $this->input->post('producto_condicion');
        $unidad_condicion = $this->input->post('unidad_condicion');
        $familia_condicion = $this->input->post('familia_condicion');
        $grupo_condicion = $this->input->post('grupo_condicion');
        $marca_condicion = $this->input->post('marca_condicion');
        $linea_condicion = $this->input->post('linea_condicion');

        $bonificaciones = array(
            'fecha' => date('Y-m-d', strtotime($this->input->post('fecha_bonificacion'))),

            'id_unidad' => empty($unidad_condicion) ? NULL : $this->input->post('unidad_condicion'),
            'id_familia' => empty($familia_condicion) ? NULL : $this->input->post('familia_condicion'),
            'id_grupo' => empty($grupo_condicion) ? NULL : $this->input->post('grupo_condicion'),
            'id_marca' => empty($marca_condicion) ? NULL : $this->input->post('marca_condicion'),
            'id_linea' => empty($linea_condicion) ? NULL : $this->input->post('linea_condicion'),
            'cantidad_condicion' => $this->input->post('cantidad_condicion'),
            'bono_producto' => $this->input->post('bono_producto'),
            'bono_unidad' => $this->input->post('bono_unidad'),
            'bono_cantidad' => $this->input->post('bono_cantidad'),
            'bonificacion_status' => 1
        );

        if ($this->input->post('subfamilia') == '') {
            $bonificaciones['subfamilia_id'] = null;

        } else {
            $bonificaciones['subfamilia_id'] = $this->input->post('subfamilia');
        }
        if ($this->input->post('subgrupos') == '') {
            $bonificaciones['subgrupo_id'] = null;

        } else {
            $bonificaciones['subgrupo_id'] = $this->input->post('subgrupos');
        }
        $productos = $this->input->post('producto_condicion', true);
        if (empty($id)) {
            $resultado = $this->bonificaciones_model->insertar($bonificaciones, $productos);
        } else {
            $bonificaciones['id_bonificacion'] = $id;
            $resultado = $this->bonificaciones_model->update($bonificaciones, $productos);
        }

        if ($resultado == TRUE) {
            $json['success'] = 'Solicitud Procesada con exito';
        } else {
            $json['error'] = 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);

    }

    function eliminar()
    {
        $id = $this->input->post('id');
        $nombre = $this->input->post('nombre');

        $bonificaciones = array(
            'id_bonificacion' => $id,
            'bonificacion_status' => 0

        );

        $data['resultado'] = $this->bonificaciones_model->update($bonificaciones, null);

        if ($data['resultado'] != FALSE) {

            $json['success'] = 'Se ha eliminado exitosamente';


        } else {

            $json['error'] = 'Ha ocurrido un error al eliminar la Bonificación';
        }

        echo json_encode($json);
    }

    function get_unidades_has_producto()
    {

        $id_producto = $this->input->post('id_producto');
        $data['unidades'] = $this->unidades_model->get_by_producto($id_producto);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    function get_unidades_en_comun()
    {

        $id_producto = $this->input->post('id_producto');
        $data['unidades'] = array();
        $productos = array();
        foreach ($id_producto as $p) {

            $producto = array();
            $producto['id_producto'] = $p;
            $producto['unidades'] = $this->unidades_model->get_by_producto($p);
            $productos[] = $producto;
        }
        foreach ($productos as $p) {
            $unidades = $p['unidades'];

            foreach ($unidades as $u) {

                $eliminar = false;

                foreach ($productos as $p2) {
                    $unidades2 = $p2['unidades'];
                    foreach ($unidades2 as $u2) {
                        if ($u['id_unidad'] != $u2['id_unidad']) {
                            $eliminar = true;
                        }
                    }
                }
                if ($eliminar == false) {
                    $data['unidades'][] = $u;
                }
            }
        }


        $data['unidades'] = @array_unique($data['unidades']);
        //  var_dump($data['unidades']);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

}

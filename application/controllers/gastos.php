<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class gastos extends MY_Controller
{

    function __construct()
    {
        parent::__construct();
        //$this->very_sesion();

        $this->load->model('gastos/gastos_model');
        $this->load->model('tiposdegasto/tipos_gasto_model');
        $this->load->model('local/local_model');
        $this->load->model('cajas/cajas_model');
    }

    /* function very_sesion()
    {
        if (!$this->session->userdata('nUsuCodigo')) {
            redirect(base_url() . 'inicio');
        }
    }*/

    /** carga cuando listas los proveedores*/
    function index()
    {

        if ($this->session->flashdata('success') != FALSE) {
            $data ['success'] = $this->session->flashdata('success');
        }
        if ($this->session->flashdata('error') != FALSE) {
            $data ['error'] = $this->session->flashdata('error');
        }

        $data['gastoss'] = $this->gastos_model->get_all();
        $dataCuerpo['cuerpo'] = $this->load->view('menu/gastos/gastos', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        }else{
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function form($id = FALSE)
    {

        $data = array();
        $data['gastos']= array();
            $data['tiposdegasto'] = $this->tipos_gasto_model->get_all();
            $data['local'] = $this->local_model->get_all();
        if ($id != FALSE) {
            $data['gastos'] = $this->gastos_model->get_by('id_gastos', $id);
        }
        $this->load->view('menu/gastos/form', $data);
    }

    function guardar()
    {

        $id = $this->input->post('id');

        $gastos = array(
            'fecha' => date('Y-m-d',strtotime($this->input->post('fecha'))),
            'descripcion' => $this->input->post('descripcion'),
            'total' => $this->input->post('total'),
            'tipo_gasto' => $this->input->post('tipo_gasto'),
            'local_id' => $this->input->post('local_id'),
        );

        if (empty($id)) {
            $resultado = $this->gastos_model->insertar($gastos);

            $this->cajas_model->save_pendiente(array(
                'monto'=>$gastos['total'],
                'tipo'=>'GASTOS',
                'IO'=>2,
                'ref_id'=>$resultado
            ));

        }
        else{
            $gastos['id_gastos'] = $id;
            $resultado = $this->gastos_model->update($gastos);

            $this->cajas_model->update_pendiente(array(
                'monto'=>$gastos['total'],
                'tipo'=>'GASTOS',
                'ref_id'=>$id
            ));
        }

        if ($resultado != FALSE) {
            $json['success']='Solicitud Procesada con exito';
        } else {
            $json['error']= 'Ha ocurrido un error al procesar la solicitud';
        }

        echo json_encode($json);

    }

    function eliminar()
    {
        $id = $this->input->post('id');

        $gastos = array(
            'id_gastos' => $id,
            'status_gastos' => 0
        );

        $this->cajas_model->delete_pendiente(array(
            'tipo'=>'GASTOS',
            'ref_id'=>$id
        ));

        $data['resultado'] = $this->gastos_model->update($gastos);

        if ($data['resultado'] != FALSE) {

            $json['success'] ='Se ha eliminado exitosamente';


        } else {

            $json['error']= 'Ha ocurrido un error al eliminar el Gasto';
        }

        echo json_encode($json);
    }


}

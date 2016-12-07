<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas extends MY_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->model('cajas/cajas_model');
        $this->load->model('local/local_model');
        $this->load->model('usuario/usuario_model');
    }

    function index()
    {
        $data['cajas'] = $this->cajas_model->get_all();

        $dataCuerpo['cuerpo'] = $this->load->view('menu/cajas/cajas', $data, true);

        if ($this->input->is_ajax_request()) {
            echo $dataCuerpo['cuerpo'];
        } else {
            $this->load->view('menu/template', $dataCuerpo);
        }
    }

    function caja_form($id = FALSE)
    {

        if ($id != FALSE) {
            $data['caja'] = $this->cajas_model->get($id);
        }

        $data['locales'] = $this->local_model->get_all();
        $data['usuarios'] = $this->db->get_where('usuario', array('activo' => 1))->result();

        $this->load->view('menu/cajas/form', $data);
    }

    function caja_guardar($id = FALSE)
    {
        $data = array(
            'local_id' => $this->input->post('local_id'),
            'moneda_id' => $this->input->post('moneda_id'),
            'responsable_id' => $this->input->post('responsable_id'),
            'estado' => $this->input->post('estado')
        );

        header('Content-Type: application/json');
        if ($this->cajas_model->valid_caja($data, $id)) {
            $result = $this->cajas_model->save($data, $id);
            echo json_encode(array('success' => $result));
        } else {
            echo json_encode(array('error' => '1'));
        }
    }

    function caja_cuenta_form($caja_id, $id = FALSE)
    {

        if ($id != FALSE) {
            $data['cuenta'] = $this->cajas_model->get_cuenta($id);
        }

        $data['caja_id'] = $caja_id;

        $data['usuarios'] = $this->db->get_where('usuario', array('activo' => 1))->result();

        $this->load->view('menu/cajas/form_cuenta', $data);
    }

    function caja_cuenta_guardar($id = FALSE)
    {
        $data = array(
            'caja_id' => $this->input->post('caja_id'),
            'descripcion' => $this->input->post('descripcion'),
            'responsable_id' => $this->input->post('responsable_id'),
            'saldo' => $this->input->post('saldo'),
            'principal' => $this->input->post('principal'),
            'estado' => $this->input->post('estado')
        );

        header('Content-Type: application/json');
        if ($this->cajas_model->valid_caja_cuenta($data, $id)) {
            $result = $this->cajas_model->save_cuenta($data, $id);
            echo json_encode(array('success' => $result));
        } else {
            echo json_encode(array('error' => '1'));
        }
    }

}

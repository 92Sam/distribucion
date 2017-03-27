<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class migracion extends MY_Controller {

    function __construct() {
        parent::__construct();
        //$this->load->model('caja/caja_model','c');
        $this->load->model('migracion/migracion_model');

        //$this->very_sesion();
    }



	function recuperarDirecciones()
    {
        $this->migracion_model->recuperarDirecciones();
    }

}
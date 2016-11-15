<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class logout extends  MY_Controller{
	
	function __construct() {
		parent::__construct();
		//$this->very_sesion();
	}
	
	function very_sesion(){
		if(!$this->session->userdata('nUsuCodigo')){
			redirect(base_url().'inicio');
		}
	}
	
	function index(){
		$this->session->sess_destroy();
		redirect('inicio', 'refresh');
		/*  $array_sesiones = array('usuario' => '', 'email' => '');
        $this->session->unset_userdata($array_sesiones);
        $this->session->sess_destroy();*/
	}
	
}
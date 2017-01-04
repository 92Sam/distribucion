<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class kardex_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();

    }

}

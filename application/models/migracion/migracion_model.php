<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class migracion_model extends CI_Model
{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }


      function recuperarDirecciones()
    {
        $query = $this->db->get('cliente');
        $aa = $query->result_array();

        $this->db->trans_start();


        for ($i=0; $i < count($aa) ; $i++) {
            if ($aa[$i]['direccion2']!='') {
                $cliente_direccion1 = array(
                    'cliente_id' => $aa[$i]['id_cliente'],
                    'tipo' => 1,
                    'valor' => $aa[$i]['direccion2'],
                    'principal' => true
                    );
                $this->db->insert('cliente_datos', $cliente_direccion1);

            }


            if ($aa[$i]['telefono1']!='') {

                $cliente_telefono1 = array(
                    'cliente_id' => $aa[$i]['id_cliente'],
                    'tipo' => 2,
                    'valor' => $aa[$i]['telefono1'],
                    'principal' => false
                    );
                $this->db->insert('cliente_datos', $cliente_telefono1);
            }

            if ($aa[$i]['telefono2']!='') {

                $cliente_telefono2 = array(
                    'cliente_id' => $aa[$i]['id_cliente'],
                    'tipo' => 2,
                    'valor' => $aa[$i]['telefono2'],
                    'principal' => false
                    );
                $this->db->insert('cliente_datos', $cliente_telefono2);
            }

            if ($aa[$i]['email']!='') {

                $cliente_correo = array(
                    'cliente_id' => $aa[$i]['id_cliente'],
                    'tipo' => 3,
                    'valor' => $aa[$i]['email'],
                    'principal' => false
                    );
                $this->db->insert('cliente_datos', $cliente_correo);
            }



            if ($aa[$i]['nota']!='') {

                $cliente_nota = array(
                    'cliente_id' => $aa[$i]['id_cliente'],
                    'tipo' => 5,
                    'valor' => $aa[$i]['nota'],
                    'principal' => false
                    );
                $this->db->insert('cliente_datos', $cliente_nota);
            }

        }

        $this->db->trans_complete();


        var_dump('data migrada');
        die();

    }

}
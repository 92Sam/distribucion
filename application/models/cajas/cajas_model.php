<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cajas_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {

        $query = $this->db->select('*');
        $query = $this->db->where('status',1);
        $query = $this->db->join('local', 'local.int_local_id=caja.local');
        $query = $this->db->join('usuario', 'usuario.nUsuCodigo=caja.responsable');
        $query = $this->db->get('caja');

        return $query->result_array();

    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('caja');
        return $query->row_array();
    }
  
    function insertar($cajas)
    {

        $this->db->trans_start();
            if ($this->db->insert('caja', $cajas)) {
                $id_caja = $this->db->insert_id();
                $usuarios = $this->input->post('usuarios', true);
                
                if ($usuarios != null) {

                    foreach ($usuarios as $usuario) { 
                        $usuario = array(
                        'caja' => $id_caja,
                    );
                }
                
                    foreach ($usuarios as $user) {
                        $this->db->where('usuario.nUsuCodigo', $user);    
                        $this->db->update('usuario', $usuario);        
                    }

                $this->db->trans_complete();

                return true;
            } else {
                return false;
            }
        }
    }

    function update($cajas)
    {
        $this->db->trans_start();
        
        $this->db->where('caja.caja_id', $cajas['caja_id']);

            if ($this->db->update('caja', $cajas)) {
                $data = array('caja' => NULL,);
                $this->db->where('usuario.caja', $cajas['caja_id']);
                $this->db->update('usuario', $data);

                $usuarios = $this->input->post('usuarios', true);            

                if ($usuarios != null) {
                
                    foreach ($usuarios as $user) {
                        $usuario = array(
                            'caja' => $cajas['caja_id'],
                        );
                    }

                    foreach ($usuarios as $user) {
                        $this->db->where('usuario.nUsuCodigo', $user);    
                        $this->db->update('usuario', $usuario);     
                    }   
                }   
               $this->db->trans_complete();

                return true;
            } else {
                return false;
            } 
        
    }

    function get_all_user()
    {
        $this->db->select('*');
        $this->db->from('usuario');
        $this->db->where('activo', 1);
   //     $this->db->where('deleted', 0);
        $query = $this->db->get();
        return $query->result_array();
    }

}
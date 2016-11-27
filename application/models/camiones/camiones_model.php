<?php if (!defined('BASEPATH')) exit('No direct script access allowed');


class camiones_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $this->db->join('usuario', 'usuario.nUsuCodigo = camiones.id_trabajadores');
        $this->db->where('camiones.deleted', 0);
        $query = $this->db->get('camiones');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('camiones');
        return $query->row_array();
    }


    function insertar($transporte)
    {
        $camionexiste = $this->get_by('camiones_placa', $transporte['camiones_placa']);
        $validar_nombre = sizeof($camionexiste);
        if ($validar_nombre < 1) {
            $this->db->trans_start();
            $this->db->insert('camiones', $transporte);

            $camines_historial = array('camion_id' => $this->db->insert_id(),
                'usuario_id' => $transporte['id_trabajadores'],
                'fecha' => date('Y-m-d H:i:s')
            );

            $this->db->insert('camiones_trabajador_historia', $camines_historial);
            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return CAMION_EXISTE;
        }

    }

    function update($transporte)
    {
        $camionexiste = $this->get_by('camiones_placa', $transporte['camiones_placa']);
        $validar_nombre = sizeof($camionexiste);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($camionexiste ['camiones_id'] == $transporte ['camiones_id']))) {
            $this->db->trans_start();
            $this->db->where('camiones_id', $transporte['camiones_id']);
            $this->db->update('camiones', $transporte);

            $camion = $this->get_by('camiones_id', $transporte['camiones_id']);

            if ($camion['id_trabajadores'] != $transporte['id_trabajadores']) {
                $camines_historial = array('camion_id' => $transporte['camiones_id'],
                    'usuario_id' => $transporte['id_trabajadores'],
                    'fecha' => date('Y-m-d H:i:s')
                );

                $this->db->insert('camiones_trabajador_historia', $camines_historial);
            }

            $this->db->trans_complete();

            ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            if ($this->db->trans_status() === FALSE) {
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return CAMION_EXISTE;
        }

    }


}

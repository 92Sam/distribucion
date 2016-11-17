<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class api_model extends CI_Model
{
    public function new_api_key($id_usuario, $level, $ignore_limits, $is_private_key, $ip_addresses)
    {
        $check_user = $this->db->get_where('keys', array('id_usuario' => $id_usuario));
        while ($check_user->num_rows() > 0) {
            return $check_user->row('key');
        }

        // Generamos la key
        $key = $this->generate_token();

        // Comprobamos si existe
        $check_exists_key = $this->db->get_where('keys', array('key' => $key));

        // Mientras exista la clave en la base de datos buscamos otra
        while ($check_exists_key->num_rows() > 0) {
            $key = '';
            $key = $this->generate_token();
        }

        // Creamos el array con los datos
        $data = array(
            'id_usuario'     => $id_usuario,
            'key'            => $key,
            'level'          => $level,
            'ignore_limits'  => $ignore_limits,
            'is_private_key' => $is_private_key,
            'ip_addresses'   => $ip_addresses
        );

        $this->db->insert('keys', $data);

        return $key;
    }

    public function getAuth($key = null)
    {
        // Check Api Key
        $check_api = $this->db->get_where('keys', array('key' => $key));

        while ($check_api->num_rows() > 0) {
            return $check_api->row('id_usuario');
        }

        return false;
    }

	public function removeAuth($key = null)
    {
        return $this->db->delete('keys', array('key' => $key));
    }
	
    private function generate_token($len = 40)
    {
        // Un array perfecto para crear claves
        $chars = array(
            'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm',
            'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z',
            'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M',
            'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            '0', '1', '2', '3', '4', '5', '6', '7', '8', '9'
        );

        // Desordenamos el array chars
        shuffle($chars);
        $num_chars = count($chars) - 1;
        $token = '';

        // Creamos una key de 40 carácteres
        for ($i = 0; $i < $len; $i++) {
            $token .= $chars[mt_rand(0, $num_chars)];
        }

        return $token;
    }
}
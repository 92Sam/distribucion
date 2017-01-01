<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class usuario_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
    }

    function insertar($usu)
    {
        $produc_exite = $this->get_by('username', $usu['username']);
        $validar_nombre = sizeof($produc_exite);


        if ($validar_nombre < 1) {
            $this->db->trans_start();
            if ($this->db->insert('usuario', $usu)) {
                $id_usu = $this->db->insert_id();
                $zonas = $this->input->post('zonas', true);
                if ($zonas != null) {
                    foreach ($zonas as $zona) {
                        $zona = array(
                            'id_usuario' => $id_usu,
                            'id_zona' => $zona,
                        );
                        $this->db->insert('usuario_has_zona', $zona);
                    }
                }
                $zz = array(
                    'username' => null,
                );
                $this->db->where('usuario.username', '');
                $this->db->update('usuario', $zz);

                $this->db->trans_complete();

                return true;
            } else {
                return false;
            }
        } else {
            return USERNAME_EXISTE;
        }
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('usuario');
        return $query->row_array();
    }

    function update($usu)
    {

        $produc_exite = $this->get_by('username', $usu['username']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or ($validar_nombre > 0 and ($produc_exite ['nUsuCodigo'] == $usu ['nUsuCodigo']))) {
            $this->db->where('usuario.nUsuCodigo', $usu['nUsuCodigo']);

            if ($this->db->update('usuario', $usu)) {
                $data = array('id_usuario' => $usu['nUsuCodigo']);
                $this->db->where('usuario_has_zona.id_usuario', $usu['nUsuCodigo']);
                $this->db->delete('usuario_has_zona', $data);
                $zonas = $this->input->post('zonas', true);
                if ($zonas != null) {
                    foreach ($zonas as $zona) {
                        $zona = array(
                            'id_usuario' => $usu['nUsuCodigo'],
                            'id_zona' => $zona,
                        );
                        $this->db->insert('usuario_has_zona', $zona);
                    }
                }

                return true;
            } else {
                return false;
            }
        } else {
            return USERNAME_EXISTE;
        }
    }


    function delete($id)
    {
        $data = array('activo' => '0');
        $this->db->where('nUsuCodigo', $id);
        if ($this->db->update('usuario', $data)) {
            return true;
        } else {
            return false;
        }
    }

    function get_by_form($campo, $valor)
    {
        {
            $this->db->select('*');
            $this->db->from('usuario');
            $this->db->join('usuario_has_zona', 'usuario_has_zona.id_usuario=usuario.nUsuCodigo');
            if ($valor != 0) {
                $this->db->where($campo, $valor);
            }
            $this->db->where('deleted', 0);
            $this->db->group_by('id_usuario');
            $query = $this->db->get();
            return $query->result_array();
        }

    }

    function buscar_id($id)
    {
        $this->db->select('*');
        $this->db->from('usuario');
        $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo');
        $this->db->join('local', 'local.int_local_id=usuario.id_local');
        $this->db->where('nUsuCodigo', $id);
        $query = $this->db->get();
        return $query->row();
    }

    public function select_all_user()
    {
        $this->db->select('usuario.*,grupos_usuarios.* , local.local_nombre');
        $this->db->from('usuario');
        $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo');
        $this->db->join('local', 'local.int_local_id=usuario.id_local', 'left');
        $this->db->where('usuario.deleted', 0);
        $query = $this->db->get();
        return $query->result();
    }


    public function select_all_by_roll($rol, $id = false)
    {
        $this->db->select('usuario.*,grupos_usuarios.* , local.local_nombre');
        $this->db->from('usuario');
        $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo');
        $this->db->join('local', 'local.int_local_id=usuario.id_local', 'left');
        $this->db->where('usuario.deleted', 0);
        $this->db->where('nombre_grupos_usuarios', $rol);
        if ($id != false)
            $this->db->where('usuario.nUsuCodigo', $id);
        
        $query = $this->db->get();

        return $query->result();
    }

    public function get_all_u2($id)
    {
        $this->db->select('*');
        $this->db->from('cliente_v');
        $this->db->where('id_cliente', $id);
        $this->db->order_by("id_cv", "desc");
        $this->db->limit(1);
        $query = $this->db->get();
        return $query->row();
    }

    public function get_all_u()
    {
        $this->db->select('*');
        $this->db->from('usuario');
        $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo');
        $this->db->join('usuario_has_zona', 'usuario_has_zona.id_usuario=usuario.nUsuCodigo');
        $this->db->where('activo', 1);
        $this->db->where('nombre_grupos_usuarios', 'Vendedor');
        $this->db->where('deleted', 0);
        $this->db->group_by('id_usuario');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_all_transportistas()
    {
        $this->db->select('*');
        $this->db->from('usuario');
        $this->db->join('grupos_usuarios', 'grupos_usuarios.id_grupos_usuarios=usuario.grupo');
        // $this->db->join('usuario_has_zona', 'usuario_has_zona.id_usuario=usuario.nUsuCodigo','left');
        $this->db->where('activo', 1);
        $this->db->where('nombre_grupos_usuarios', 'Chofer');
        $this->db->where('deleted', 0);
        // $this->db->group_by('id_usuario');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function select_lista_local($id_user)
    {
        $query = $this->db->query("SELECT l.int_local_id as id_local, l.var_local_nombre as name_local,
     IFNULL((select lu.var_detLocal_estado from local_has_usuario lu 
       where l.int_local_id = lu.int_local_id and lu.nUsuCodigo =" . $id_user . "),0) as estado
    FROM local l");
        return $query->result_array();
    }

    public function traer_by($select = false, $from = false, $join = false, $campos_join = false, $tipo_join, $where = false, $nombre_in, $where_in,
                             $nombre_or, $where_or,
                             $group = false,
                             $order = false, $retorno = false)
    {
        if ($select != false) {
            $this->db->select($select);
            $this->db->from($from);
        }
        if ($join != false and $campos_join != false) {

            for ($i = 0; $i < count($join); $i++) {

                if ($tipo_join != false) {

                    for ($t = 0; $t < count($tipo_join); $t++) {

                        if ($tipo_join[$t] != "") {

                            $this->db->join($join[$i], $campos_join[$i], $tipo_join[$t]);
                        }

                    }

                } else {

                    $this->db->join($join[$i], $campos_join[$i]);
                }

            }
        }
        if ($where != false) {
            $this->db->where($where);
        }

        if ($nombre_in != false) {
            for ($i = 0; $i < count($nombre_in); $i++) {
                $this->db->where_in($nombre_in[$i], $where_in[$i]);
            }
        }

        if ($nombre_or != false) {
            for ($i = 0; $i < count($nombre_or); $i++) {
                $this->db->or_where($where_or);
            }
        }

        if ($group != false) {
            $this->db->group_by($group);
        }

        if ($order != false) {
            $this->db->order_by($order);
        }

        $query = $this->db->get();

        if ($retorno == "RESULT_ARRAY") {

            return $query->result_array();
        } elseif ($retorno == "RESULT") {
            return $query->result();

        } else {
            return $query->row_array();
        }

    }
}

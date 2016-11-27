<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class usuarios_grupos_model extends CI_Model
{

    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    function get_all()
    {
        $query = $this->db->where('status_grupos_usuarios', 1);
        $query = $this->db->get('grupos_usuarios');
        return $query->result_array();
    }

    function get_by($campo, $valor)
    {
        $this->db->where($campo, $valor);
        $query = $this->db->get('grupos_usuarios');
        return $query->row_array();
    }



    function update($usuariosgrupos)
    {

        $this->db->trans_start();
        $this->db->where('id_grupos_usuarios', $usuariosgrupos['id_grupos_usuarios']);
        $this->db->update('grupos_usuarios', $usuariosgrupos);

        $this->db->trans_complete();

        ///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        if ($this->db->trans_status() === FALSE)
            return FALSE;
        else
            return TRUE;
    }

    /**
     * get details of a role
     *
     * @param	int		$role_id	the unique identifier for the role
     * @return	object	a CodeIgniter row object for role
     * @see		http://ellislab.com/codeigniter/user-guide/database/results.html Documentation for CodeIgniter row object
     * @author	William Duyck <fuzzyfox0@gmail.com>
     *
     * @todo	return permissions associated w/ role as well
     */
    public function get_role($role_id) {
        $role = $this->get_role_by('id_grupos_usuarios', $role_id);
        return ($role !== FALSE) ? $role[0] : FALSE;
    }

    /**
     * get roles by constraint
     *
     * @param	string	$field	the field to constrain
     * @param	mixed	$value	the required value of field
     * @return	object	a CodeIgniter row object for role
     * @see		http://ellislab.com/codeigniter/user-guide/database/results.html Documentation for CodeIgniter row object
     * @author	William Duyck <fuzzyfox0@gmail.com>
     */
    public function get_role_by($field, $value) {
        $this->db->where($field, $value);
        return $this->get_all_roles();
    }


    /**
     * get all roles details
     *
     * @return	array	an array of CodeIgniter row objects for role
     * @see		http://ellislab.com/codeigniter/user-guide/database/results.html Documentation for CodeIgniter result object
     * @author	William Duyck <fuzzyfox0@gmail.com>
     */
    public function get_all_roles() {
        $this->db->select('*');
        $this->db->where('status_grupos_usuarios', FALSE);
        $this->db->from ( 'grupos_usuarios' );
        $query = $this->db->get ();
        return ($query->num_rows() > 0) ? $query->result() : FALSE;
    }


    /**
     * get permission a role has
     *
     * @param	int		$role_id	the unique identifier for the role
     * @return	array	array of CodeIgniter row objects for role permissions
     * @see		http://ellislab.com/codeigniter/user-guide/database/results.html Documentation for CodeIgniter result object
     * @author	William Duyck <fuzzyfox0@gmail.com>
     */
    public function get_role_perms($role_id) {
        $this->db->select('opcion.*')
            ->from('opcion_grupo')
            ->where('grupo', $role_id)
            ->join('opcion', 'opcion.nOpcion = opcion_grupo.Opcion');

        $perms = $this->db->get();


        return ($perms->num_rows() > 0) ? $perms->result() : FALSE;
    }




    public function edit_role_perms($role_id, $perm_array) {
        // bulk delete permissions for the role
        $this->db->delete('opcion_grupo', array('grupo' => $role_id));

        // assume permissions all fail to set
        $rtn = TRUE;

        // add permissions provided in array
        if(!empty($perm_array)) {
            foreach ($perm_array as $item => $perm_id) {
                if (!$this->add_role_perm($role_id, $perm_id)) {
                    $rtn = FALSE;
                }
            }
        }

        // return TRUE if all permissions set
        return $rtn;
    }


    public function edit_role($role_id, $data) {
        $produc_exite=$this->get_by('nombre_grupos_usuarios', $data['nombre_grupos_usuarios']);
        $validar_nombre = sizeof($produc_exite);
        if ($validar_nombre < 1 or( $validar_nombre>0 and ($produc_exite ['id_grupos_usuarios']==$role_id))) {
            return $this->db->update('grupos_usuarios', $data, array('id_grupos_usuarios' => $role_id));
        }else{
            return NOMBRE_EXISTE;
        }
    }

    public function insert_role($data) {

        $nombre = $this->input->post('nombre_grupos_usuarios');
        $validar_nombre = sizeof($this->get_by('nombre_grupos_usuarios', $nombre));

        if ($validar_nombre < 1) {

            $this->db->trans_start();

            $this->db->insert('grupos_usuarios', $data);
            $lastid = $this->db->insert_id();
            $this->edit_role_perms($lastid, $this->input->post('perms'));

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE)
                return FALSE;
            else
                return $lastid;
        }else{
            return NOMBRE_EXISTE;
        }

//		return ($this->db->affected_rows() == 1);
    }


    public function add_role_perm($role_id, $perm_id) {
        $this->db->insert('opcion_grupo', array(
            'grupo' => $role_id,
            'Opcion' => $perm_id
        ));
        return ($this->db->affected_rows() == 1);
    }





    /**
     * get all permissions
     *
     * @return	array	an array of CodeIgniter row objects for permission
     * @see		http://ellislab.com/codeigniter/user-guide/database/results.html Documentation for CodeIgniter result object
     * @author	William Duyck <fuzzyfox0@gmail.com>
     */
    public function get_all_perms() {
        $perms = $this->db->get('opcion');
        return ($perms->num_rows() > 0) ? $perms->result() : FALSE;
    }


    /**
     * get users roles
     *
     * @param	string	$user_id	the unique identifier for the user
     * @return	array	array of CodeIgniter row objects containing the user roles
     * @see		http://ellislab.com/codeigniter/user-guide/database/results.html Documentation for CodeIgniter result object
     * @author	William Duyck <fuzzyfox0@gmail.com>
     */
    public function get_user_roles($user_id) {

        $this->db->select('usuario.*')
            ->from('usuario')
            ->where('nUsuCodigo', $user_id);


        $role = $this->db->get ();

        return ($role->num_rows() > 0) ? $role->result() : FALSE;
    }

    public function user_has_perm($user_id, $slug) {
        $user_perms = $this->get_user_perms($user_id);

        // chek the user has some permissions
        // loop through users permissions and check for the slug
        if(is_array($user_perms)) foreach($user_perms as $perm) {
            // if slug is found then return TRUE
            if($perm->cOpcionDescripcion == $slug) {
                return TRUE;
            }
        }

        // if we get here the user has no permissions
        return FALSE;
    }

    public function get_user_perms($user_id) {
        // hold on tight... this is a complicated one... and will be
        // rolled into a single sql query if possible at a later date w/ diff logic. (might be possible)
        $rtn = array();

        // get users roles
        $role_list = $this->get_user_roles($user_id);

        // check role(s) set
        // for each role get its perms and add them to return array
        if(is_array($role_list)) foreach($role_list as $role) {
            // get role perms
            $perm_list = $this->get_role_perms($role->grupo);

            // check perms assigned to role
            if(is_array($perm_list)) foreach($perm_list as $perm) {
                $rtn[] = $perm;
            }
        }

        // return permission value total and return
        return $rtn;
    }

}

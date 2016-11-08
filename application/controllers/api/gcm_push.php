<?php
// Api Rest
require(APPPATH . '/libraries/REST_Controller.php');

/**
 * Created by PhpStorm.
 * User: Jhainey
 * Date: 01/12/2015
 * Time: 10:22
 */
class gcm_push extends REST_Controller
{

    protected $url = 'https://android.googleapis.com/gcm/send';
    protected $serverApiKey = "AIzaSyAyRb0CrsAZChy7nyImtIDtxAua_dazuVs"; //Para obtener una apikey para tu aplicacion puedes hacerlo en https://code.google.com/apis/console/
    protected $dispositivos = array();

    public function __construct()
    {
        parent::__construct();


        $this->load->model('api/api_model', 'apiModel');
        $this->load->model('gcm/gcm_users_model', 'gcm_users_model');
        $this->load->library('user_agent');
    }


    function very_auth()
    {
        // Request Header
        $reqHeader = $this->input->request_headers();

        // Key
        $key = null;
        if (isset($reqHeader['X-api-key'])) {
            $key = $reqHeader['X-api-key'];
        } else if ($key_get = $this->get('x-api-key')) {
            $key = $key_get;
        } else if ($key_post = $this->post('x-api-key')) {
            $key = $key_post;
        } else {
            $key = null;
        }

        // Auth ID
        $auth_id = $this->api->getAuth($key);

        // ID ?
        if (!empty($auth_id)) {
            $this->uid = $auth_id;
        } else {
            $this->uid = null;
        }
    }

    public function set_dispositivos($dispositivoIds)
    {
        if (is_array($dispositivoIds)) {
            $this->dispositivos = $dispositivoIds;
        } else {
            $this->dispositivos = array(
                $dispositivoIds
            );
        }
    }

    public function enviar_get()
    {

        $get = $this->input->get(null);
        $mensaje = $get['mensaje'];
        //$dispositivos = $get['dispositivos'];

        $alldispo=$this->gcm_users_model->getAll();
     $dispositivos= Array();

        foreach($alldispo as $dp){
            $dispositivos[]=$dp['codigo'];
        }

        $this->set_dispositivos($dispositivos);

        if (!is_array($this->dispositivos) || count($this->dispositivos) == 0) {
            $this->error("No hay dispositivos a los que enviar la notificación");
        }
        if (strlen($this->serverApiKey) < 8) {
            $this->error("No se ha indicado la apiKey");
        }

        $campos = array(
           'registration_ids' => $this->dispositivos,
            //'to' => 'APA91bH0YPaPsVig_GHVL7rXNU9bWYIN3gm90A4-UIrwk4EjC3NpQ6nlg9GP3i0eGpIPI8EUUqDoEZCAj3QXi4atEyyncmaaDUvYD-FBKvsXoDiGA6XrWsr7fWIGZw14nvSmWaSnUph6',
            'data' => array(
                "message" => $mensaje,
             //   "score" => "5x1",
               // "time" => "15:10",
            )
        );

        $cabeceras = array(
            'Authorization: key=' . $this->serverApiKey,
            'Content-Type: application/json'
        );


        $ch = curl_init(); //Para aprender más sobre la librería cURL pues visitar http://web.ontuts.com/tutoriales/aprendiendo-a-utilizar-la-libreria-curl-en-php/

        curl_setopt($ch, CURLOPT_URL, $this->url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $cabeceras);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($campos));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        $resultado = curl_exec($ch);

        // Error handling
        if (curl_errno($ch) or $resultado==null or $resultado===FALSE or json_decode($resultado)==null ) {

            $this->response(array("result" => 'false', 'GCM error: ' => curl_error($ch),  'error'=> $resultado), 200);

        }

        curl_close($ch);

        $this->response(array('result' => json_decode($resultado)));
    }

    function error($msg)
    {
        $this->response(json_encode(array("result" => 'false', 'error' => $msg)), 200);
    }

}
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js"></script>
<script type="text/javascript">
    //hacemos la petición a la api con ajax para obtener un usuario por su id
    $.ajax({
        url: 'http://localhost/distribucion/api/api/user/id/1/x-api-key/8hu8fWMCIhCXyq0U4TP0CMJ9waHkCGNcsrqok8zS',
        type: 'GET',
        success: function (data) {
            console.log(data);
        },
        error: function (response) {
            console.log(response);
        }
    });

</script>

<?php
//hacemos la petición a la api via curl
$ch = curl_init();
curl_setopt_array($ch, array(
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_URL => 'http://localhost/distribucion/api/api/users/format/json/x-api-key/8hu8fWMCIhCXyq0U4TP0CMJ9waHkCGNcsrqok8zS'
));
$response = curl_exec($ch);
echo "<pre>";
var_dump($response);
echo "</pre>";
//como pedimos un json, debemos decodificarlo con json_decode para accederlo
$jsondecode = json_decode($response);
echo $jsondecode->username;
echo $jsondecode->register_date;
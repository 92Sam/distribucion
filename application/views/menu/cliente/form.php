<form name="formagregar" action="<?= base_url() ?>cliente/guardar" method="post" id="formagregar">

    <style>
        .row{margin-bottom: 10px;}
    </style>
    <input type="hidden" name="id" id=""
           value="<?php if (isset($cliente['id_cliente'])) echo $cliente['id_cliente']; ?>">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Cliente</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>DNI o RUC</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="identificacion" id="identificacion" required="true"
                                   class="form-control"
                                   value="<?php if (isset($cliente['identificacion'])) echo $cliente['identificacion']; ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Razón Social o nombre</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="razon_social" id="razon_social" required="true"
                                   class="form-control"
                                   value="<?php if (isset($cliente['razon_social'])) echo $cliente['razon_social']; ?>">
                        </div>




                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Representante</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="representante" id="" class="form-control"
                                   value="<?php if (isset($cliente['representante'])) echo $cliente['representante']; ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Correo</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="email" id="" class="form-control"
                                   value="<?php if (isset($cliente['email'])) echo $cliente['email']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">


                        <div class="col-md-2">
                            <label>Pais</label>
                        </div>
                        <div class="col-md-4">
                            <select name="pais_id" id="id_pais" required="true" class="form-control"
                                    onchange="region.actualizarestados();">

                                <?php
                                $p=1;
                                foreach ($paises as $pais){
                                        $paais['pais'.$p] = $pais['id_pais'];
                                    ?>
                                    <option
                                        value="<?php echo $pais['id_pais'] ?>" <?php if (isset($cliente['id_pais']) and $cliente['pais_id'] == $pais['id_pais']) echo 'selected' ?>><?= $pais['nombre_pais'] ?></option>
                                <?php
                                $p++;
                                } ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label>Ciudad</label>
                        </div>
                        <div class="col-md-4">
                            <?php
                                    $e = 1;
                                    if(isset($paais['pais1'])) {
                                        ?>
                                        <select name="estados_id" id="estado_id" required="true" class="form-control"
                                                onchange="region.actualizardistritos();">

                                                <?php foreach ($estados as $estado){
                                                    $eestado['estado'.$e] = $estado['estados_id'];
                                                    ?>
                                                    <option
                                                        value="<?php echo $estado['estados_id'] ?>" <?php if (isset($cliente['estados_id']) and $cliente['estados_id'] == $estado['estados_id']) echo 'selected' ?>><?= $estado['estados_nombre'] ?></option>
                                                <?php $e++; } ?>

                                        </select>
                                        <?php
                                    }else{
                                        ?>
                                        <select name="estados_id" id="estado_id" required="true" class="form-control"
                                                onchange="region.actualizardistritos();">
                                            <option value="">Seleccione</option>
                                            <?php if (isset($cliente['id_cliente'])):
                                                $eestado['estado'.$e] = $estado['estados_id'];
                                                ?>
                                                <?php foreach ($estados as $estado): ?>
                                                    <option
                                                        value="<?php echo $estado['estados_id'] ?>" <?php if (isset($cliente['estados_id']) and $cliente['estados_id'] == $estado['estados_id']) echo 'selected' ?>><?= $estado['estados_nombre'] ?></option>
                                                <?php $e++; endforeach ?>
                                            <?php endif ?>
                                        </select>
                                        <?php
                                    }
                            ?>
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">
                            <label>Distrito</label>
                        </div>
                        <div class="col-md-4">
                            <?php

                              if(isset($eestado['estado1'])) { ?>
                                  <select name="ciudad_id" id="ciudad_id" required="true" class="form-control"
                                          onchange="region.actualizarzonas();">
                                      <option value="">Seleccione</option>
                                          <?php foreach ($ciudades as $ciudad): ?>
                                              <option
                                                  value="<?php echo $ciudad['ciudad_id'] ?>" <?php if (isset($cliente['ciudad_id']) and $cliente['ciudad_id'] == $ciudad['ciudad_id']) echo 'selected' ?>><?= $ciudad['ciudad_nombre'] ?></option>
                                          <?php endforeach ?>

                                  </select>
                                 <?php
                              }else{
                           ?>
                            <select name="ciudad_id" id="ciudad_id" required="true" class="form-control"
                                    onchange="region.actualizarzonas();">
                                <option value="">Seleccione</option>
                                <?php if (isset($cliente['id_cliente'])): ?>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option
                                            value="<?php echo $ciudad['ciudad_id'] ?>" <?php if (isset($cliente['ciudad_id']) and $cliente['ciudad_id'] == $ciudad['ciudad_id']) echo 'selected' ?>><?= $ciudad['ciudad_nombre'] ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>
                            <?php } ?>
                        </div>
                        <div class="col-md-2">
                            <label>Grupo</label>
                        </div>
                        <div class="col-md-4">

                            <select name="grupo_id" id="grupo_id" required="true" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($grupos as $grupo): ?>
                                    <option
                                        value="<?php echo $grupo['id_grupos_cliente'] ?>" <?php if (isset($cliente['grupo_id']) and $cliente['grupo_id'] == $grupo['id_grupos_cliente']) echo 'selected' ?>><?= $grupo['nombre_grupos_cliente'] ?></option>
                                <?php endforeach ?>
                            </select>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Zona de Reparto</label>
                        </div>
                        <div class="col-md-4">
                            <?php
                             //   if()
                            ?>
                            <select name="zona" id="zona" required="true" class="form-control"
                                    onchange="region.actualizarvendedor();">
                                <option value="0">Seleccione</option>
                                <?php foreach ($zonas as $zona): ?>
                                    <option
                                        value="<?php echo $zona['zona_id'] ?>" <?php if (isset($cliente['id_zona']) and $cliente['id_zona'] == $zona['zona_id']) echo 'selected' ?>><?= $zona['zona_nombre'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        <!--<div class="col-md-2">
                            <label>Direcci&oacute;n </label>
                        </div>-->

                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Vendedor</label>
                        </div>
                        <div class="col-md-4">
                            <select name="vendedor" id="vendedor" required="true" class="form-control"
                                    onchange="region.actualizarzona();" >
                                <option value="0" >Seleccione</option>
                                <?php foreach ($vendedores as $vendedor):

                                    ?>
                                    <option
                                        value="<?php echo $vendedor['nUsuCodigo'] ?>" <?php if (isset($cliente['vendedor_a']) and $cliente['vendedor_a'] == $vendedor['nUsuCodigo']) echo 'selected' ?>><?= $vendedor['nombre'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Direcci&oacute;n entrega</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="direccion2" id="" class="form-control"
                                   value="<?php if (isset($cliente['direccion2'])) echo $cliente['direccion2']; ?>">
                        </div>

                        <!--<div class="col-md-2">
                            <label>C&oacute;digo Postal</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="codigo_postal" id="" class="form-control"
                                   value="<?php //if (isset($cliente['codigo_postal'])) echo $cliente['codigo_postal']; ?>">
                        </div> -->
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">
                            <label>Tel&eacute;fono Celular</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="telefono2" id="" class="form-control"
                                   value="<?php if (isset($cliente['telefono2'])) echo $cliente['telefono2']; ?>">
                        </div>
                        <div class="col-md-2">
                            <label>Tel&eacute;fono Fijo</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="telefono1" id="" class="form-control"
                                   value="<?php if (isset($cliente['telefono1'])) echo $cliente['telefono1']; ?>">
                        </div>
                    </div>
                </div>


               <!-- <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>P&aacute;gina Web</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="pagina_web" id="" class="form-control"
                                   value="<?php //if (isset($cliente['pagina_web'])) echo $cliente['pagina_web']; ?>">
                        </div>

                       <!-- <div class="col-md-2">
                            <label>Descuento</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="descuento" id="" class="form-control"
                                   value="<?php // if (isset($cliente['descuento'])) echo $cliente['descuento']; ?>">
                        </div>

                    </div>
                </div>-->

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>L&iacute;mite de Cr&eacute;dito</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="limite_credito" id="" class="form-control"
                                   value="<?php if (isset($cliente['limite_credito'])) echo $cliente['limite_credito']; ?>">
                        </div>

                        <div class="col-md-2">
                            <label>Nota</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="nota" id="" class="form-control"
                                   value="<?php if (isset($cliente['nota'])) echo $cliente['nota']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-12">

                        <label for="" class="control-label">Direccion en mapa</label>

                        <input type="text" name="direccion" id="location" class="form-control" autocomplete="on"
                               value="<?php if (isset($cliente['direccion'])) echo $cliente['direccion']; ?>">

                        <div id="us2" style="width: 100%; height: 400px;"></div>
                        Lat.: <input type="text" id="latitud" name="latitud" required readonly
                                     value="<?php if (isset($cliente['latitud'])) echo $cliente['latitud']; else echo '-11.86442400794103'; ?>"/>
                        Long.: <input type="text" id="longitud" name="longitud" required readonly
                                      value="<?php if (isset($cliente['longitud'])) echo $cliente['longitud']; else echo '-77.07489067298582'; ?>"/>
                        <script>
                            $('.selectpicker').selectpicker();
                        </script>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>
</form>

<script type="text/javascript">
    $(document).ready(function () {
        $("select").chosen({'width': '100%'});
    });

    if ($('#latitud').val() == '0') {
        (setTimeout(function () {

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (objPosition) {
                    var lon = objPosition.coords.longitude;
                    var lat = objPosition.coords.latitude;

                    $('#longitud').val(lon);
                    $('#latitud').val(lat);

                    $('#us2').locationpicker({
                        location: {latitude: $('#latitud').val(), longitude: $('#longitud').val()},
                        radius: 50,
                        inputBinding: {
                            latitudeInput: $('#latitud'),
                            longitudeInput: $('#longitud'),
                            locationNameInput: $('#location')
                        },
                        enableAutocomplete: true,
                        onchanged: function (currentLocation, radius, isMarkerDropped) {
                            (currentLocation.latitude + ", " + currentLocation.longitude);

                        }
                    });
                }, function (objPositionError) {
                    switch (objPositionError.code) {
                        case objPositionError.PERMISSION_DENIED:
                            alert("No se ha permitido el acceso a la posición del usuario.");
                            break;
                        case objPositionError.POSITION_UNAVAILABLE:
                            alert("No se ha podido acceder a la información de su posición.");
                            break;
                        case objPositionError.TIMEOUT:
                            alert("El servicio ha tardado demasiado tiempo en responder.");
                            break;
                        default:
                            alert("Error desconocido.");
                    }
                }, {
                    maximumAge: 75000,
                    timeout: 15000
                });
            }
            else {
                alert("Su navegador no soporta la API de geolocalización.");
            }
        })(), 5000);
    }
</script>
<script type="text/javascript">


    if ($('#latitud').val() == '0') {
        (setTimeout(function () {

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (objPosition) {
                    var lon = objPosition.coords.longitude;
                    var lat = objPosition.coords.latitude;
                    alert(lat + ' y ' + lon);
                    $('#longitud').val(lon);
                    $('#latitud').val(lat);
                    $('#us2').locationpicker({
                        location: {latitude: lat, longitude: lon},
                        radius: 50,
                        inputBinding: {
                            latitudeInput: $('#latitud'),
                            longitudeInput: $('#longitud'),
                            locationNameInput: $('#location')
                        },
                        enableAutocomplete: true,
                        onchanged: function (currentLocation, radius, isMarkerDropped) {
                            (currentLocation.latitude + ", " + currentLocation.longitude);


                        }
                    });
                }, function (objPositionError) {
                    switch (objPositionError.code) {
                        case objPositionError.PERMISSION_DENIED:
                            alert("No se ha permitido el acceso a la posición del usuario.");
                            break;
                        case objPositionError.POSITION_UNAVAILABLE:
                            alert("No se ha podido acceder a la información de su posición.");
                            break;
                        case objPositionError.TIMEOUT:
                            alert("El servicio ha tardado demasiado tiempo en responder.");
                            break;
                        default:
                            alert("Error desconocido.");
                    }
                }, {
                    maximumAge: 75000,
                    timeout: 15000
                });
            }
            else {
                alert("Su navegador no soporta la API de geolocalización.");
            }
        })(), 5000);
    }
    else {
        $('#us2').locationpicker({
            location: {latitude: $('#latitud').val(), longitude: $('#longitud').val()},
            radius: 50,
            inputBinding: {
                latitudeInput: $('#latitud'),
                longitudeInput: $('#longitud'),
                locationNameInput: $('#location')
            },
            enableAutocomplete: true,
            onchanged: function (currentLocation, radius, isMarkerDropped) {
                (currentLocation.latitude + ", " + currentLocation.longitude);

            }
        });

    }
</script>
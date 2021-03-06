<script type="text/javascript">

    function validarFrm() {

        var items = []

        if (validar_ruc_dni() == false) {
            return false;
        }

        if ($("#razon_social").val() == '') {
            return mensajeAlerta("Debe ingresar la raz&oacute;n social");
        }

        var tipopersona = $("#tipo_cliente").val();
        if (tipopersona == 0) {
            if ($("#gerente_nombre").val() == '') {
                return mensajeAlerta("Debe ingresar el nombre del gerente");
            }
        }

        if ($("#grupo_id").val() == '') {
            return mensajeAlerta("Debe seleccionar el grupo");
        }

        if ($("#id_pais").val() == '') {
            return mensajeAlerta("Debe seleccionar el pais");
        }

        if ($("#estado_id").val() == '') {
            return mensajeAlerta("Debe seleccionar la ciudad");
        }

        if ($("#ciudad_id").val() == '') {
            return mensajeAlerta("Debe seleccionar el distrito");
        }


        if ($("#zona").val() == '') {
            return mensajeAlerta("Debe seleccionar la zona");
        }

        if (/^(''|0)$/.test($("#vendedor").val())) {
            return mensajeAlerta("Debe seleccionar un Vendedor");
        }


        if ($("#s_descuento").val() == 1 && ($('#descuento_id').val() == '' || parseFloat($('#descuento_id').val()) <= 0)) {
            return mensajeAlerta("Descuento no valido");
        }

        var direccion = false
        $(".fila").each(function () {

            if ($(this).find('.tipo').attr('id') == '1') {
                direccion = true
            }
            if ($(this).find('.principal').attr('id') == '1') {
                var principal = true
            } else {
                var principal = false
            }

            items.push([$(this).find('.tipo').attr('id'), $(this).find('.valor').attr('id'), principal])

        })

        if (direccion == false) {
            return mensajeAlerta("Debe ingresar una direccion");
        }

        /***** Evaluaci´on de elementos caso telefonos ******/
        var evaluarElementos = [];
        $.each(items, function () {
            if ($(this)[0] == '1' && $(this)[2] == true) { // Direccion
                evaluarElementos.push($(this));
            }
        });

        if (evaluarElementos.length <= 0) {
            return mensajeAlerta("Debe haber una direccion marcada como principal");
        }

        function mensajeAlerta(mensaje) {
            var growlType = 'warning';
            $("#vendedor").focus()
            $.bootstrapGrowl('<h4>' + mensaje + '</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false
        }

        /******************************************************/


        if ($('#linea_libre').is(':checked')) {
            var linea_libre = 1
        } else {
            var linea_libre = 0
        }

        // $('#tipo').attr('disabled', true)
        // $('#valor').attr('disabled', true)

        if (DniRucEnBd() == false) {
            return false;
        }


        $('#barloadermodal').modal('show');
        $.ajax({
            url: '<?=base_url()?>cliente/guardar',
            type: "post",
            dataType: "json",
            data: {
                'cliente_id': $('#cliente_id').val(),
                'tipo_cliente': $('#tipo_cliente').val(),
                'ciudad_id': $('#ciudad_id').val(),
                'grupo_id': $('#grupo_id').val(),
                'representante': $('#representante_nombre').val(),
                'representante_dni': $('#representante_dni').val(),
                'razon_social': $('#razon_social').val(),
                'descuento': $('#descuento_id').val(),
                'linea_credito_valor': $('#retencion_id').val(),
                'agente_retencion': $('#s_retencion').val(),
                'linea_libre': linea_libre,
                'linea_libre_valor': $('#linea_libre_valor').val(),
                'identificacion': $('#tipo_identificacion').val(),
                'ruc_cliente': $('#ruc_cliente').val(),
                'gerente_nombre': $('#gerente_nombre').val(),
                'gerente_dni': $('#gerente_dni').val(),
                'latitud': $('#latitud').val(),
                'longitud': $('#longitud').val(),
                'id_zona': $('#zona').val(),
                'vendedor_a': $('#vendedor').val(),
                'importe_deuda': $('#importe_deuda').val(),
                'nombre_boleta': $('#nombre_boleta').val(),
                'items': items
            },
            success: function (data) {

                setTimeout(function () {
                    $('#barloadermodal').modal('hide');
                }, 1000)
                if (data != '') {

                    $.bootstrapGrowl('<h4>' + data[Object.keys(data)] + '</h4>', {
                        type: Object.keys(data),
                        delay: 2500,
                        allow_dismiss: true
                    });
                    if (Object.keys(data) == 'success') {
                        $('#agregar').modal('toggle')

                        $("#example").dataTable().fnDestroy();

                        TablesDatatablesJson.init('<?php echo base_url()?>api/Clientes', 0, 'example');
                    } else {
                        return false

                    }


                }
            }

        });

    }

</script>
<form name="formagregar" method="post" id="formagregar">

    <style>
        .row {
            margin-bottom: 10px;
        }

        legend {
            display: block;
            width: auto;
            padding: 0 5px;
            margin-bottom: 0;
            font-size: inherit;
            line-height: inherit;
            border: auto;
            border-bottom: none;
        }

        fieldset {
            border: 3px groove threedface;
            padding: 5px;
        }
    </style>
    <input type="hidden" name="id" id="cliente_id"
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
                            <label class="control-label panel-admin-text">Tipo</label>
                        </div>
                        <div class="col-md-4">
                            <select name="tipo_cliente" id="tipo_cliente" required="true" class="form-control">
                                <option value="-1">Seleccione</option>
                                <option
                                        value="1" <?php if (isset($cliente['tipo_cliente']) and $cliente['tipo_cliente'] == 1) echo 'selected' ?>>
                                    Natural
                                </option>
                                <option
                                        value="0" <?php if (isset($cliente['tipo_cliente']) and $cliente['tipo_cliente'] == 0) echo 'selected' ?>>
                                    Juridico
                                </option>
                            </select>
                        </div>

                    </div>
                </div>


                <div class="row">
                    <h4>Identificaci&oacute;n del Cliente</h4>
                    <div class="form-group">

                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Razón Social</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="razon_social" id="razon_social" required="true"
                                   class="form-control"
                                   value="<?php if (isset($cliente['razon_social'])) echo $cliente['razon_social']; ?>">
                        </div>

                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">RUC / DNI</label>
                        </div>
                        <div class="col-md-2">
                            <select id="tipo_identificacion" class="form-control">
                                <option value="0">Seleccione</option>
                                <option value="1"
                                    <?= isset($cliente['identificacion']) && $cliente['identificacion'] == 1 ? 'selected' : '' ?>>
                                    RUC
                                </option>
                                <option value="2"
                                    <?= isset($cliente['identificacion']) && $cliente['identificacion'] == 2 ? 'selected' : '' ?>>
                                    DNI
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <input type="text" disabled name="ruc_cliente" id="ruc_cliente" required="true"
                                   class="form-control"
                                   value="<?php if (isset($cliente['ruc_cliente'])) echo $cliente['ruc_cliente']; ?>">
                        </div>

                    </div>
                </div>

                <div class="row">

                    <div class="col-md-6">
                        <div class="row">
                            <h4>Representante del Cliente</h4>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label class="control-label panel-admin-text">Nombre</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="representante_nombre" id="representante_nombre"
                                           class="form-control"
                                           value="<?php if (isset($cdatos['representante'])) echo $cdatos['representante']; ?>">
                                </div>

                                <div class="col-md-1">
                                    <label class="control-label panel-admin-text">DNI</label>
                                </div>
                                <div class="col-md-3" id="dni_ruc_cont">
                                    <input type="text" name="representante_dni" id="representante_dni"
                                           required="true"
                                           class="form-control"
                                           value="<?php if (isset($cdatos['representante_dni'])) echo $cdatos['representante_dni']; ?>">

                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="col-md-6 gerente_block" style="display: none;">
                        <div class="row">
                            <h4>Gerente de la empresa juridica</h4>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label class="control-label panel-admin-text">Nombre</label>
                                </div>
                                <div class="col-md-6">
                                    <input type="text" name="gerente_nombre" id="gerente_nombre" class="form-control"
                                           value="<?php if (isset($cliente['representante'])) echo $cliente['representante']; ?>">
                                </div>

                                <div class="col-md-1">
                                    <label class="control-label panel-admin-text">DNI</label>
                                </div>
                                <div class="col-md-3" id="dni_ruc_cont">
                                    <input type="text" name="gerente_dni" id="gerente_dni" required="true"
                                           class="form-control"
                                           value="<?php if (isset($cdatos['gerente_dni'])) echo $cdatos['gerente_dni']; ?>">
                                </div>
                            </div>
                        </div>
                    </div>


                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4" style="display:none">
                            <select name="pais_id" id="id_pais" required="true" class="form-control"
                                    onchange="region.actualizarestados();">
                                <?php
                                $p = 1;
                                foreach ($paises as $pais) {
                                    $paais['pais' . $p] = $pais['id_pais'];
                                    ?>
                                    <option
                                            value="<?php echo $pais['id_pais'] ?>" <?php if (isset($cliente['id_pais']) and $cliente['pais_id'] == $pais['id_pais']) echo 'selected' ?>><?= $pais['nombre_pais'] ?></option>
                                    <?php
                                    $p++;
                                } ?>
                            </select>
                        </div>

                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Ciudad</label>
                        </div>
                        <div class="col-md-4">
                            <?php
                            $e = 1;
                            if (isset($paais['pais1'])) {
                                ?>
                                <select name="estados_id" id="estado_id" required="true" class="form-control"
                                        onchange="region.actualizardistritos();">

                                    <?php foreach ($estados as $estado) {
                                        $eestado['estado' . $e] = $estado['estados_id'];
                                        ?>
                                        <option
                                                value="<?php echo $estado['estados_id'] ?>" <?php if (isset($cliente['estados_id']) and $cliente['estados_id'] == $estado['estados_id']) echo 'selected' ?>><?= $estado['estados_nombre'] ?></option>
                                        <?php $e++;
                                    } ?>

                                </select>
                                <?php
                            } else {
                                ?>
                                <select name="estados_id" id="estado_id" required="true" class="form-control"
                                        onchange="region.actualizardistritos();">
                                    <option value="">Seleccione</option>
                                    <?php if (isset($cliente['id_cliente'])):
                                        $eestado['estado' . $e] = $estado['estados_id'];
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

                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Distrito</label>
                        </div>
                        <div class="col-md-4">
                            <?php

                            if (isset($eestado['estado1'])) { ?>
                                <select name="ciudad_id" id="ciudad_id" required="true" class="form-control"
                                        onchange="region.actualizarzonas();">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option
                                                value="<?php echo $ciudad['ciudad_id'] ?>" <?php if (isset($cliente['ciudad_id']) and $cliente['ciudad_id'] == $ciudad['ciudad_id']) echo 'selected' ?>><?= $ciudad['ciudad_nombre'] ?></option>
                                    <?php endforeach ?>

                                </select>
                                <?php
                            } else {
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
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Zona de Reparto</label>
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

                        <div class="col-md-2">
                            <label>Vendedor</label>
                        </div>
                        <div class="col-md-4">
                            <select name="vendedor" id="vendedor" required="true" class="form-control"
                                    onchange="region.actualizarzona();">
                                <option value="0">Seleccione</option>
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
                            <label class="control-label panel-admin-text">L&iacute;nea de Cr&eacute;dito</label>
                        </div>
                        <div class="col-md-2">
                            <input type="number" name="linea_libre_valor" id="linea_libre_valor" class="form-control"
                                   value="<?php if (isset($cliente['linea_libre_valor'])) echo $cliente['linea_libre_valor']; ?>">

                        </div>

                        <div class="col-md-2">
                            <input type="checkbox" id="linea_libre" name="linea_libre"
                                   style="display: inline" <?php if (isset($cliente['linea_libre']) and $cliente['linea_libre'] == 1) echo 'checked' ?>>
                            <label class="control-label panel-admin-text">Linea Libre</label>

                        </div>

                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Agente Retenci&oacute;n</label>
                        </div>
                        <div class="col-md-2">
                            <select name="s_retencion" id="s_retencion" class="form-control">
                                <option value="">Seleccione</option>
                                <option
                                        value="1" <?php if (isset($cliente['agente_retencion']) and $cliente['agente_retencion'] == 1) echo 'selected' ?>>
                                    Si
                                </option>
                                <option
                                        value="0" <?php if (isset($cliente['agente_retencion']) and $cliente['agente_retencion'] == 0) echo 'selected' ?>>
                                    No
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2" id='div_retencion'>
                            <input type="number" name="retencion" id="retencion_id" class="form-control"
                                   value="<?php if (isset($cliente['linea_credito_valor'])) echo $cliente['linea_credito_valor']; ?>">

                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Grupo</label>
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

                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Descuento</label>
                        </div>
                        <div class="col-md-2">
                            <select name="s_descuento" id="s_descuento" class="form-control">
                                <option value="">Seleccione</option>
                                <option
                                        value="1" <?php if (isset($cliente['descuento']) and $cliente['descuento'] != NULL) echo 'selected' ?>>
                                    Si
                                </option>
                                <option
                                        value="0" <?php if (isset($cliente['descuento']) and $cliente['descuento'] == NULL) echo 'selected' ?>>
                                    No
                                </option>
                            </select>
                        </div>
                        <div class="col-md-2" id='div_descuento'>
                            <input type="number" min="0" name="descuento" id="descuento_id" class="form-control"
                                   value="<?php if (isset($cliente['descuento'])) echo $cliente['descuento']; ?>">

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Importe Deuda</label>
                        </div>

                        <div class="col-md-4">
                            <input disabled type="number" name="importe_deuda" id="importe_deuda" class="form-control"
                                   value="<?php if (isset($cliente['importe_deuda'])) echo $cliente['importe_deuda']; ?>">

                        </div>
                        
                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Nombre en Boleta</label>
                        </div>

                        <div class="col-md-4">
                            <select name="nombre_boleta" id="nombre_boleta" class="form-control">
                                <option
                                        value="0" <?php if (isset($cliente['nombre_boleta']) and $cliente['nombre_boleta'] == 0) echo 'selected' ?>>
                                    No
                                </option>
                                <option
                                        value="1" <?php if (isset($cliente['nombre_boleta']) and $cliente['nombre_boleta'] != 0) echo 'selected' ?>>
                                    Si
                                </option>

                            </select>
                        </div>
                    </div>
                </div>

                <fieldset id="el##">
                    <legend>Datos Adicionales</legend>

                    <div class="col-md-1" style="padding:10px 20px">
                        <label class="control-label panel-admin-text">Valor</label>
                    </div>
                    <div class="col-md-4">
                        <select name="tipo" id="tipo" class="form-control">
                            <option value="">Seleccione</option>
                            <option value="<?= CDIRECCION ?>">Direccion</option>
                            <option value="<?= CTELEFONO ?>">Telefono</option>
                            <option value="<?= CCORREO ?>">Correo</option>
                            <option value="<?= CWEB ?>">Sitio Web</option>
                            <option value="<?= CNOTA ?>">Notas</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="text" name="valor" id="valor" class="form-control">
                    </div>
                    <div class="col-md-2" style="padding-left: 1%; width:12%">
                        <input type="checkbox" name="principal" id='principal' style="display: inline">
                        <label class="control-label panel-admin-text">Principal</label>
                    </div>
                    <div class="col-md-1" onclick="agregar_dato()">
                        <a class="btn btn-default"><i id='agregar_dato' title='Agregar'
                                                      class="gi gi-plus sidebar-nav-icon"></i></a>
                    </div>
                    <div class="col-md-8" id='content_tabla'>
                        <table class="table table-striped" id="">
                            <thead>
                            <tr>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Principal</th>
                                <th>Acciones</th>
                            </tr>
                            </thead>
                            <tbody id='cont_tabla'>
                            <?php if (isset($cliente_datos)) {
                                for ($i = 0; $i < count($cliente_datos); $i++) { ?>

                                    <tr id="<?php echo $i + 1 ?>" class='fila'>
                                        <td id="<?php echo $cliente_datos[$i]['tipo'] ?>" class='tipo'>
                                            <?php
                                            if ($cliente_datos[$i]['tipo'] == CDIRECCION) {
                                                echo 'Direcci&oacute;n';
                                            } elseif ($cliente_datos[$i]['tipo'] == CTELEFONO) {
                                                echo 'Tel&eacute;fono';
                                            } elseif ($cliente_datos[$i]['tipo'] == CCORREO) {
                                                echo 'Correo';
                                            } elseif ($cliente_datos[$i]['tipo'] == CWEB) {
                                                echo 'P&aacute;gina Web';
                                            } elseif ($cliente_datos[$i]['tipo'] == CNOTA) {
                                                echo 'Nota';
                                            }

                                            ?></td>
                                        <td id="<?php echo $cliente_datos[$i]['valor'] ?>"
                                            class="valor"><?php echo $cliente_datos[$i]['valor'] ?></td>
                                        <td id="<?php echo $cliente_datos[$i]['principal'] ?>" class="principal"><?php
                                            if ($cliente_datos[$i]['principal'] == 1) {
                                                echo 'SI';
                                            } else {
                                                echo 'NO';
                                            }
                                            ?></td>
                                        <td><i id="editar_dato" title="Editar" onclick="editar_dato(this)"
                                               class="editar_d gi gi-edit sidebar-nav-icon" style=""></i>&nbsp;&nbsp;<i
                                                    id="eliminar_dato" title="Eliminar"
                                                    onclick="modalEliminarDato(this)"
                                                    class="eliminar_d fa fa-trash sidebar-nav-icon"></i></td>
                                    </tr>

                                <?php }

                            } ?>


                            </tbody>
                        </table>
                    </div>
                </fieldset>
                <br>

                <div class="modal-footer">
                    <button type="button" onclick="validarFrm()" id="" class="btn btn-primary">
                        <li class="glyphicon glyphicon-thumbs-up"></li>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal"> Cancelar
                        <li class="glyphicon glyphicon-thumbs-down"></li>
                    </button>
                </div>

                <br>

                <div class="row">

                    <div class="col-md-12">

                        <label class="control-label panel-admin-text">Direccion en mapa</label>

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


            <!-- /.modal-content -->
        </div>


</form>


<div class="modal-content" id="modal_eliminar_dato"
     style="width:30%; position: absolute; top:40%;left: 30%; display:none">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">Eliminar Dato</h4>
    </div>
    <div class="modal-body">
        <div class="form-group row">
            <h4>¿Está seguro que desea eliminar el dato seleccionado?</h4>
        </div>

        <input type="hidden" name="id" id="id_borrar">
        <input type="hidden" name="nombre" id="nom_borrar">
    </div>
    <div class="modal-footer">
        <button type="button" id="rm_dato" class="btn btn-primary">
            <li class="glyphicon glyphicon-thumbs-up"></li>
            Confirmar
        </button>
        <button type="button" class="btn btn-warning" id='cancelar_rm'>
            <li class="glyphicon glyphicon-thumbs-down"></li>
            Cancelar
        </button>

    </div>
</div>


<script type="text/javascript">
    $(document).ready(function () {

        $('#representante_dni').mask('99999999');
        $('#gerente_dni').mask('99999999');

        $("select").chosen({'width': '100%'});

        $('#div_retencion').hide()

        agenteRetencion();
        change_descuento();

        $('#s_retencion').change(function () {
            agenteRetencion()
        })

        $('#s_descuento').change(function () {
            change_descuento()
        })

        validarLineaCredito()

        $('#linea_libre').change(function () {
            validarLineaCredito()
        })

        $('#dni_ruc').blur(function () {
            validar_ruc_dni()
        })


        // Inicio de validacion de tipo de persona
        <?php if (isset($cliente['tipo_cliente']))
        echo 'verificarTipoCliente(' . $cliente['tipo_cliente'] . ')'
        ?>

        $("#tipo_cliente").change(function () {
            var tipopersona = $("#tipo_cliente").val();
            verificarTipoCliente(tipopersona);
        });

        function verificarTipoCliente(tipopersona) {
            if (tipopersona == 0) {
                $("#tipo_identificacion").attr('disabled', 'disabled');
                $("#tipo_identificacion").val('1');

                $(".gerente_block").show();

            }// Natural es uno "1"
            else if (tipopersona == 1) {

                $("#tipo_identificacion").removeAttr('disabled');
                $("#tipo_identificacion").val('2');

                $(".gerente_block").hide();
            }
            else {

                $("#tipo_identificacion").removeAttr('disabled');
                $("#tipo_identificacion").val('0');

                $(".gerente_block").hide();
            }

            $("#tipo_identificacion").change().trigger('chosen:updated');
        }

        // Fin de validacion de tipo de persona


        $("#tipo_cliente, #tipo_identificacion").change(function () {
            verificarRucDni();

            $("#ruc_cliente").val('');
            $("#gerente_nombre").val('');
            $("#gerente_dni").val('');
            $('#ruc_cliente').focus();
        });

        $('#tipo').change(function () {
            if ($('#tipo').val() == 2) {
                $('#valor').attr('type', 'number');
            } else {
                $('#valor').attr('type', 'text');
            }
        })
    });


    function verificarRucDni() {

        if ($('#tipo_identificacion').val() == 1) {
            $('#ruc_cliente').mask('99999999999');
            $('#ruc_cliente').prop('disabled', false);


        }
        else if ($('#tipo_identificacion').val() == 2) {
            $('#ruc_cliente').mask('99999999');
            $('#ruc_cliente').prop('disabled', false);


        }
        else {
            $('#ruc_cliente').mask('');
            $('#ruc_cliente').prop('disabled', true);


        }
    }

    function verificarDatos() {
        var retorno = true
        $(".fila").each(function () {


            if ($('#tipo').val() == $(this).find('.tipo').attr('id') && $('#valor').val().toUpperCase() == $(this).find('.valor').attr('id').toUpperCase()) {


                $.bootstrapGrowl('<h4>¡Este dato ya ha sido cargado!</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                retorno = false
            }
        })
        return retorno
    }


    function verificarDatosPrincipal() {
        var retorno = true
        $(".fila").each(function () {

            if ($('#principal').is(':checked')) {
                if ($('#tipo').val() == $(this).find('.tipo').attr('id') && $(this).find('.principal').attr('id') == 1) {


                    $.bootstrapGrowl('<h4>¡Este dato ya no puede ser principal!</h4>', {
                        type: 'warning',
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);

                    retorno = false
                }
            }
        })
        return retorno
    }


    function agenteRetencion() {
        if ($('#s_retencion').val() == 1) {
            $('#div_retencion').show()
        } else {
            $('#div_retencion').hide()
            $('#retencion_id').val('')
        }
    }

    function change_descuento() {
        if ($('#s_descuento').val() == 1) {
            $('#div_descuento').show()
        } else {
            $('#div_descuento').hide()
            $('#descuento_id').val('')
        }
    }


    function validarLineaCredito() {

        if ($('#linea_libre').is(':checked')) {
            $('#linea_libre_valor').prop("disabled", true)
            $('#linea_libre_valor').val('')

        } else {
            $('#linea_libre_valor').prop("disabled", false)
        }

    }

    function DniRucEnBd() {

        var result = false;
        if ($('#ruc_cliente').val() != '') {

            $.ajax({
                url: '<?=base_url()?>cliente/DniRucEnBd',
                type: "post",
                dataType: "json",
                async: false,
                data: {'dni_ruc': $('#ruc_cliente').val(), 'cliente_id': $('#cliente_id').val()},
                success: function (data) {
                    if (data.error == undefined) {
                        result = true;
                    } else {
                        $.bootstrapGrowl('<h4>El RUC / DNI ya existe</h4>', {
                            type: 'warning',
                            delay: 2500,
                            allow_dismiss: true
                        });
                        result = false;
                    }
                },
                error: function (data) {
                    result = false;
                }

            });
        }

        return result;

    }

    function validar_ruc_dni() {

        if ($('#tipo_cliente').val() == '-1') {

            $.bootstrapGrowl('<h4>Seleccione el tipo de cliente</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($('#tipo_identificacion').val() == 0) {

            $.bootstrapGrowl('<h4>Seleccione el tipo de identificaci&oacute;n</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            return false;
        }

        if ($('#tipo_cliente').val() == 0 && $('#ruc_cliente').val().length != 11) {

            $.bootstrapGrowl('<h4>¡EL campo RUC debe contener 11 digitos!</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            return false;

        }
        if ($('#tipo_cliente').val() == 1 && $('#tipo_identificacion').val() == 2 && $('#ruc_cliente').val().length != 8) {
            $.bootstrapGrowl('<h4>¡EL campo DNI debe contener 8 digitos!</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            return false;

        }
        if ($('#tipo_cliente').val() == 1 && $('#tipo_identificacion').val() == 1 && $('#ruc_cliente').val().length != 11) {
            $.bootstrapGrowl('<h4>¡EL campo RUC debe contener 11 digitos!</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            return false;

        }

    }

    function agregar_dato() {
        var indice = 0
        if ($('.fila').size() == 0) {
            indice = 1
        } else {
            indice = parseInt($(".fila").last().attr('id')) + 1
        }

        if (verificarDatos() == false) {
            return false
        }

        if (verificarDatosPrincipal() == false) {
            return false
        }

        $('#content_tabla').show()
        var principal = '';
        var principal = false;


        if ($('#tipo').val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el tipo de datos adicional</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false
        } else if ($('#valor').val().length == 0) {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar ' + $('#tipo :selected').html() + '</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false
        }


        if ($('#principal').is(':checked')) {
            principal = 'SI'
            d_principal = 1
        } else {
            principal = 'NO'
            d_principal = 0
        }


        $("#cont_tabla").append('<tr id=' + indice + ' class="fila"><td scope="row" id=' + $('#tipo').val() + ' class="tipo">' + $('#tipo :selected').html() + '</td><td id="' + $('#valor').val() + '" class="valor">' + $('#valor').val() + '</td><td id=' + d_principal + ' class="principal">' + principal + '</td><td><i id="editar_dato" title="Editar" class="editar_d gi gi-edit sidebar-nav-icon" style=""></i>&nbsp;&nbsp;<i id="eliminar_dato" title="Eliminar" onclick="modalEliminarDato(this)" class="eliminar_d fa fa-trash sidebar-nav-icon" ></i></td></tr>');


        $(".editar_d").unbind().click(function () {
        });

        $('.editar_d').bind('click', function () {
            $('#tipo').val($(this).parent().parent().find('.tipo').attr('id')).trigger('chosen:updated');


            $('#valor').val($(this).parent().parent().find('.valor').attr('id'))


            if ($(this).parent().parent().find('.principal').attr('id') == 1) {
                $('#principal').prop('checked', true)
            } else {
                $('#principal').prop('checked', false)

            }

            $(this).parent().parent().remove();


        })


        $('#tipo').val('').trigger('chosen:updated');
        $('#valor').val('')
        $('#principal').prop('checked', false)

    }

    function modalEliminarDato(elem) {
        $('#rm_dato').attr('onclick', 'eliminar_dato(' + $(elem).parent().parent().attr('id') + ')')
        $('#cancelar_rm').click(function () {
            $('#modal_eliminar_dato').hide()

        })
        $('#modal_eliminar_dato').show()
    }

    function eliminar_dato(id) {

        $('#' + id).remove()
        $('#modal_eliminar_dato').hide()

    }

    function editar_dato(elem) {

        $('#tipo').val($(elem).parent().parent().find('.tipo').attr('id')).trigger('chosen:updated');


        $('#valor').val($(elem).parent().parent().find('.valor').attr('id'))


        if ($(elem).parent().parent().find('.principal').attr('id') == 1) {
            $('#principal').prop('checked', true)
        } else {
            $('#principal').prop('checked', false)

        }

        $(elem).parent().parent().remove();

    }

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

<script type="text/javascript" src="<?php echo base_url() ?>recursos/js/jquery.maskedinput.js"></script>
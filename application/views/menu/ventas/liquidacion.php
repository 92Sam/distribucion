<?php $ruta = base_url(); ?>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <form id="frmBuscar">
                <div class="block-title">
                    <h3>LIQUIDAR COBRANZAS</h3>
                </div>


                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Vendedor:</label>
                    </div>
                    <div class="col-md-3">


                        <select name="vendedor" id="vendedor" class='cho form-control filter-input'>
                            <option value="-1">Todos los vendedores</option>
                            <?php if (count($vendedores) > 0): ?>
                                <?php foreach ($vendedores as $vendedor): ?>
                                    <option
                                        value="<?php echo $vendedor->nUsuCodigo; ?>"
                                        id="<?php echo $vendedor->nUsuCodigo; ?>">
                                        <?php echo $vendedor->nombre; ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <!-- <button id="btnBuscar" class="btn btn-default" >Buscar</button>  -->
                </div>

                <br>

                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Desde:</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="fecha_ini" id="fecha_ini" value="<?= date('d-m-Y') ?>"
                               required="true" readonly style="cursor: pointer;"
                               class="form-control fecha input-datepicker filter-input">
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Hasta:</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="fecha_fin" id="fecha_fin" value="<?= date('d-m-Y') ?>"
                               required="true" readonly style="cursor: pointer;"
                               class="form-control fecha input-datepicker filter-input">
                    </div>

                </div>
                <br>
            </form>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>

            <div id="lstTabla" class="table-responsive"></div>
        </div>

        <div class="block-section"></div>
    </div>
</div>

<div class="modal fade" id="visualizarliquidacion" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#pp_excel").hide();
        $("#pp_pdf").hide();

        $('select').chosen();

        $(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});

        $(".filter-input").on('change', function () {
            buscar();
        });

            buscar();


        $('#visualizarliquidacion').on('hidden.bs.modal', function (e) {
            buscar();
        });

    });

    function buscar() {

        $("#lstTabla").html($("#loading").html());

        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'venta/lst_liquidaciones',
            success: function (data) {
                $("#lstTabla").html(data);
                if($("#vendedor").val() == -1){
                    $("#btn_liquidar").attr('disabled', 'disabled');
                    $("#btn_anular").attr('disabled', 'disabled');
                } else {
                    $("#btn_liquidar").removeAttr('disabled');
                    $("#btn_anular").removeAttr('disabled');
                }
            },
            error: function(){
                $("#lstTabla").html('');

                show_msg('warning','Ha ocurrido un error en la opci&oacute;n')
            }
        });
    }

    function editar(historial, monto, venta_numero, venta_id, usuario) {

        setTimeout(function () {
            $("#montoabonado").attr('value', monto);
            $("#historial_aeditar").attr('value', historial);
            $("#venta_aeditar").attr('value', venta_id);
            $("#usuario").attr('value', usuario);
            $("#montonuevo").val('');
            $("#montonuevo").focus();
        }, 1)

        $("#numero_venta").remove();
        $("#mostrar_venta").append('<p id="numero_venta">' + venta_numero + ' </p>');
        $('#editar').modal('show');

    }

    function cerrar() {

        $('#visualizarliquidacion').modal('hide');
        $('#liquidar').modal('hide');
        $(".modal-backdrop").remove();
        buscar();
    }

    function anular() {

        var total = $('input[name="historial[]"]:checked').length;

        if (total < 1) {

            show_msg('warning','Debe seleccionar al menos una opci&oacute;n');

            $(this).prop('disabled', true);

            return false;

        }

        $("#borrar_cantidad_anular").remove();


        $("#mostrar_cantidad_anular").append('<p id="borrar_cantidad_anular">' + total + ' Pagos</p>');
        $('#anular').modal('show');
    }


    function guardar_anular() {

        $.ajax({	//create an ajax request to load_page.php
            type: "POST",
            url: '<?php echo base_url();?>' + 'inicio/very_sesion',
            dataType: "json",	//expect html to be returned
            success: function (sesion) {

                if (sesion == "false")	//if no errors
                {
                    $('#barloadermodal').modal('hide');
                    alert('El tiempo de su sessión ha expirado');
                    location.href = base_url + 'inicio';
                } else {
                    $.ajax({
                        type: 'POST',
                        data: $('#form').serialize(),
                        dataType: "json",
                        url: '<?php echo base_url();?>' + 'venta/anular_pago',
                        success: function (data) {
                            $('#barloadermodal').modal('hide');
                            if (data.exito) {
                                $('#anular').modal('hide');
                                show_msg('success','Los pagos se han anulado con exito')
                                buscar();
                            }

                            if (data.error) {
                                show_msg('warning','Ha ocurrido un error')
                                $(this).prop('disabled', true);
                            }

                        },
                        error: function () {
                            $('#barloadermodal').modal('hide');

                            show_msg('warning','Ha ocurrido un error');

                            $(this).prop('disabled', true);
                        },
                        complete: function () {
                            $(".modal-backdrop").remove();
                        }
                    });
                }

            }
        });


    }

    function guardar_editar() {

        if ($("#montonuevo").val() < 1) {

            $("#montonuevo").focus();

            show_msg('warning','Debe ingresar un monto mayor a 0');
            $(this).prop('disabled', true);
            return false;
        }

        if ($("#montonuevo").val() == "") {

            $("#montonuevo").focus();
            show_msg('warning','Debe ingresar un monto');
            $(this).prop('disabled', true);

            return false;
        }


        $.ajax({
            type: 'POST',
            data: $('#form_editar').serialize() + '&vendedor=' + $("#vendedor").val(),
            dataType: "json",
            url: '<?php echo base_url();?>' + 'venta/editar_historialcobranza',
            success: function (data) {
                if (data.error != undefined)
                    show_msg('warning','<h4>Ha ocurrido un error</h4>')
                else {
                    $('#editar').modal('hide');
                    show_msg('success','<h4>Se ha editado el pago</h4>')
                    buscar();
                }

            },
            error: function () {

                show_msg('warning','<h4>Ha ocurrido un error</h4>')

                $(this).prop('disabled', true);
            },
            complete: function () {
                $(".modal-backdrop").remove();
            }
        });

    }

    function liquidar() {
        var total = $('input[name="historial[]"]:checked').length;

        if (total < 1) {
            var growlType = 'warning';

            show_msg('warning','Debe seleccionar al menos una opci&oacute;n');

            $(this).prop('disabled', true);

            return false;

        }

        $("#borrar_cantidad").remove();


        $("#mostrar_cantidad").append('<p id="borrar_cantidad">' + total + ' Pagos</p>')
        $('#liquidar').modal('show');
        //$("#id").attr('value', id);
    }

    function guardar() {
        $("#barloadermodal").modal({
            show: true,
            backdrop: 'static'
        });
        /*console.log($('#form').serialize() + '&vendedor=' + $("#vendedor").val());
        return false;*/

        $.ajax({	//create an ajax request to load_page.php
            type: "POST",
            url: '<?php echo base_url();?>' + 'inicio/very_sesion',
            dataType: "json",	//expect html to be returned
            success: function (sesion) {
                if (sesion == "false")	//if no errors
                {
                    $('#barloadermodal').modal('hide');
                    alert('El tiempo de su sessión ha expirado');
                    location.href = base_url + 'inicio';
                } else {
                    $.ajax({
                        type: 'POST',
                        data: $('#form').serialize() + '&vendedor=' + $("#vendedor").val(),
                        url: '<?php echo base_url();?>' + 'venta/guardar_liquidar',
                        success: function (data) {
                            $('#barloadermodal').modal('hide');
                            $("#visualizarliquidacion").html(data);
                            $('#visualizarliquidacion').modal('show');

                        },
                        error: function () {
                            $('#barloadermodal').modal('hide');
                            var growlType = 'warning';

                            $.bootstrapGrowl('<h4>Ha ocurrido un error</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });

                            $(this).prop('disabled', true);

                            return false;
                        }
                    });
                }

            }

        });


    }

</script>
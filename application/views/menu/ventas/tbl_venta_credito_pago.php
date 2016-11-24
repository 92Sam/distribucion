<div class="modal-dialog">

    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Pagar Venta</h4>
        </div>
        <div class="modal-body">
            <form id="form">
                <div class="row">

                    <div class="form-group">
                        <div class="col-md-3">Monto total de la venta:</div>
                        <div class="col-md-3">
                            <?= $credito[0]['total'] ?>
                        </div>


                    </div>
                </div>
                <br>

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-3">Monto Pagado:</div>
                        <div class="col-md-3">
                            <?= (floatval($credito[0]['dec_credito_montodebito'])) ?>
                        </div>


                        <div class="col-md-3">Monto Restante:</div>
                        <div class="col-md-3">
                            <?= floatval($credito[0]['dec_credito_montodeuda']) - (floatval($credito[0]['dec_credito_montodebito'])) ?>
                        </div>


                    </div>
                </div>
                <br>

                <div class="row">

                    <div class="form-group">
                        <div class="col-md-3">Metodo de Pago:</div>
                        <div class="col-md-9">
                            <select class="form-control" name="metodo" id="metodo"
                                    style="width:250px">
                                <option value="">Seleccione</option>
                                <?php
                                if (count($metodos) > 0) {
                                    foreach ($metodos as $metodo) { ?>
                                        <option
                                            value="<?= $metodo['id_metodo'] ?>"><?= $metodo['nombre_metodo'] ?></option>
                                    <?php }
                                } ?>
                            </select>
                        </div>
                        <div id="mostrar_bancos">
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">


                        <div class="col-md-3">Monto a Pagar:</div>
                        <div class="col-md-6">
                            <input type="number" onkeydown="return:soloDecimal();" id="cantidad_a_pagar" value=""
                                   class="form-control">
                            <input type="hidden" id="id_venta">

                        </div>
                    </div>
                </div>
            </form>
            <br>


        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-default" id="guardarPago" onclick="guardarPago()"><i class=""></i> Pagar</a>
            <a href="#" class="btn btn-default" id="cerrar" data-dismiss="modal"
            >Cancelar</a>
        </div>
    </div>

</div>


<div class="modal fade" id="pago_modal" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true"></div>

<div class="modal fade" id="visualizarPago" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>
<script>
    var lst_producto = new Array();
    var producto = {};

    $("#metodo").on("change", function () {

        var metodo = $("#metodo").val();

        if (metodo != "") {
            $.ajax({
                type: 'POST',
                data: 'metodo=' + metodo,
                dataType: 'json',
                url: '<?= base_url()?>venta/buscarmetodo',
                success: function (data) {

                    if (data.bancos) {

                        var bancos = data.bancos

                        var options = '';
                        for (var i = 0; i < bancos.length; i++) {
                            options += '<option value="'
                                + bancos[i].banco_id
                                + '">'
                                + bancos[i].banco_nombre
                                + '</option>';

                            console.info(bancos[i]);

                        }


                        $("#borrar_bancos").remove();
                        $("#mostrar_bancos").append('<div id="borrar_bancos"> <div class="col-md-3">Banco:</div> <div class="col-md-9">' +
                            '<select class="form-control" name="banco" id="bancos" ' +
                            'style="width:250px">' +
                            '<option value="">Seleccione </option>' +
                            '</select></div></div>')
                        $("#bancos")
                            .html(
                                '<option value="">Seleccione</option>');

                        $("#bancos")
                            .append(options);

                    } else {
                        $("#borrar_bancos").remove();
                    }

                },
                error: function () {
                    var growlType = 'danger';

                    $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p>Intente nuevamente</p>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $('#pago_modal').modal('hide');
                    return false;

                }
            })
        } else {

            $("#borrar_bancos").remove();
        }


    });


    $(document).ready(function () {


        producto.monto_al_momento =<?=  floatval($credito[0]['dec_credito_montodeuda'])-(floatval($credito[0]['dec_credito_montodebito'])) ?>;
        producto.id_venta = <?=  $credito[0]['id_venta'] ?>;
        producto.total_recibido =<?=  $credito[0]['dec_credito_montodebito'] ?>;
        console.log(producto.monto_al_momento);
        $("#cantidad_a_pagar").attr('max', <?=  $credito[0]['dec_credito_montodeuda']-$credito[0]['dec_credito_montodebito'] ?>);
        $("#id_venta").attr('value', <?=  $credito[0]['id_venta'] ?>);
        $("#cerrar").click(function () {
            $('#pago_modal').modal('hide');
        });
    });


    function cerrar_detalle_historial() {

        $('#visualizarPago').modal('hide');
        $('#pago_modal').modal('hide');
        $('#pagar_venta').modal('hide');
        buscar();
    }

    function pagarcuota() {


        $("#pago_modal").modal('show');
    }

    function guardarPago() {


        lst_producto = new Array();

        if ($("#metodo").val() == "") {

            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe seleccionar un metodo de pago</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false;

        }

        if ($("#bancos").val() == "") {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe seleccionar un banco</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;

        }
        var cantidad_pagar = parseFloat($("#cantidad_a_pagar").val());
        if (cantidad_pagar == '' || isNaN(cantidad_pagar)) {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Ingrese una cantidad</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;

        }
        if (cantidad_pagar > (producto.monto_al_momento)) {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Ha ingresado una cantidad mayor a la cantidad a pendiente</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;

        }

        if (cantidad_pagar <= 0) {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe ingresar un monto mayor a 0</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;

        }


        producto.metodo = $("#metodo").val();
        producto.cuota = $("#cantidad_a_pagar").val();
        producto.usuario = <?= $this->session->userdata('nUsuCodigo'); ?>;
        producto.id_venta =<?= $credito[0]['id_venta']?>;
        lst_producto.push(producto);
        var miJSON = JSON.stringify(lst_producto);
        var nom_doc = $("#cboTipDoc option:selected").html();

        $("#guardarPago").addClass('disabled');

        $("#barloadermodal").modal({
            show: true,
            backdrop: 'static'
        });


        $.ajax({	//create an ajax request to load_page.php
            type: "POST",
            url: '<?php echo base_url(); ?>inicio/very_sesion',
            dataType: "json",
            success: function (data) {


                if (data == "false")	//if no errors
                {
                    $('#barloadermodal').modal('hide');

                    alert('El tiempo de su sessión ha expirado');
                    location.href = '<?php echo base_url() ?>inicio';
                } else {

                    $.ajax({
                        type: 'POST',
                        data: $('#form').serialize() + '&lst_producto=' + miJSON,
                        dataType: 'json',
                        url: '<?= base_url()?>venta/guardarPago',
                        success: function (data) {

                            if (data.success && data.error == undefined) {

                                $.ajax({
                                    type: 'POST',
                                    data: {'id_venta': data.id_venta, 'id_historial': data.id_historial},
                                    url: '<?= base_url()?>venta/imprimir_pago_pendiente',
                                    success: function (data2) {
                                        $('#barloadermodal').modal('hide');

                                        $("#pago_modal").modal('hide');
                                        $("#visualizarPago").html(data2);
                                        $('#visualizarPago').modal('show');


                                    },error:function(error){
                                        $('#barloadermodal').modal('hide');
                                        $("#guardarPago").removeClass('disabled');
                                        var growlType = 'danger';

                                        $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p></p>', {
                                            type: growlType,
                                            delay: 2500,
                                            allow_dismiss: true
                                        });
                                        $('#pago_modal').modal('hide');
                                        return false;
                                    }
                                });

                            }
                            else {
                                $('#barloadermodal').modal('hide');
                                $("#guardarPago").removeClass('disabled');
                                var growlType = 'danger';

                                $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p>'+data.error+'</p>', {
                                    type: growlType,
                                    delay: 2500,
                                    allow_dismiss: true
                                });
                                $('#pago_modal').modal('hide');
                                return false;
                            }


                        },
                        error: function () {
                            $('#barloadermodal').modal('hide');
                            $("#guardarPago").removeClass('disabled');
                            var growlType = 'danger';

                            $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p>Intente nuevamente</p>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });
                            $('#pago_modal').modal('hide');
                            return false;

                        }
                    });
                }
            }
        });
    }

</script>
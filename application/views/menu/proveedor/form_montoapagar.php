<div class="modal-dialog" style="width: 50%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Pagar Ingreso <?=$ingreso->documento_numero?></h4>
        </div>
        <div class="modal-body">
            <form id="form">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Total Compra</label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="input-group-addon"><?= MONEDA ?></div>
                                <input type="text" readonly value="<?=number_format($ingreso->total, 2, '.', '')?>" name="total" id="total" required="true"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Monto Pendiente</label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <div class="input-group-addon"><?= MONEDA ?></div>
                                <input type="text" readonly value="<?=number_format($ingreso->total - $ingreso->monto_pagado, 2, '.', '')?>" name="total_pendiente" id="total_pendiente" required="true"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <br>

                <div class="row pago_block">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Medios de Pago</label>
                        </div>
                        <div class="col-md-8">
                            <select name="pago_id" id="pago_id" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($metodos_pago as $pago): ?>
                                    <?php if ($pago->id_metodo != 7): ?>
                                        <option
                                            value="<?= $pago->id_metodo ?>"><?= $pago->nombre_metodo ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row" id="banco_block" style="display: none;">
                    <br>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Seleccione el Banco</label>
                        </div>
                        <div class="col-md-8">
                            <select name="banco_id" id="banco_id" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($bancos as $banco): ?>
                                    <option
                                        value="<?= $banco->banco_id ?>"><?= $banco->banco_nombre ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row nc_block" style="display: none;">
                    <br>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="fecha_nc">Fecha de NC</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="fecha_nc" name="fecha_nc"
                                   class="form-control"
                                   value="<?=date('d-m-Y')?>" readonly>
                        </div>
                    </div>
                </div>


                <div class="row nc_block" style="display: none;">
                    <br>
                    <div class="form-group">
                        <div class="col-md-4">
                            <label for="desc_nc">Descripcion de NC</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="desc_nc" name="desc_nc"
                                   class="form-control"
                                   value="">
                        </div>
                    </div>
                </div>

                <br>
                <div class="row pago_block" id="operacion_block" style="display: block;">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label id="num_oper_label">Dato Adicional</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="num_oper" name="num_oper"
                                   class="form-control"
                                   value="">
                        </div>
                    </div>
                </div>
                <br>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Monto a Pagar</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" id="cantidad_a_pagar" name="cantidad_a_pagar" value="" onkeydown="return:soloDecimal();"
                                   class="form-control">
                            <input type="checkbox" id="pagar_todo"> <label for="pagar_todo" style="cursor: pointer;">Pagar todo</label>
                            <input type="hidden" id="ingreso_id" name="ingreso_id" value="<?=$ingreso->ingreso_id?>">


                        </div>
                    </div>
                </div>
            </form>
            <br>


        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-default" id="guardarPago"
               onclick="guardarPago()">
                <i class=""></i> Pagar</a>
            <a href="#" class="btn btn-default" data-dismiss="modal"
               onclick="javascript:$('#pagar_venta').hide();">Cancelar</a>
        </div>
    </div>
</div>

<script>


    var lst_producto = new Array();
    var producto = {};
    $(function () {

        $("#fecha_nc").datepicker({format: 'dd-mm-yyyy'});

        $("#pagar_todo").on('change', function(){
            if($(this).prop('checked')){
                $("#cantidad_a_pagar").val($("#total_pendiente").val());
            }
            else{
                $("#cantidad_a_pagar").val('');
            }
        });

        $("#pago_id").on('click', function () {

                $("#banco_id").val('');
                $("#num_oper").val('');
                $("#retencion_block").hide();
                $("#banco_block").hide();

                if ($(this).val() == '4') {
                    $("#banco_block").show();
                    $("#num_oper_label").html('N&uacute;mero de Operaci&oacute;n');
                }
                else if ($(this).val() != '4') {

                    if ($(this).val() == '5'){
                        $("#num_oper_label").html('N&uacute;mero de Cheque');
                    }
                    if ($(this).val() == '6'){
                        $(".nc_block").show();
                        $("#num_oper_label").html('N&uacute;mero de Nota de Cr&eacute;dito');
                    }
                    else{
                        $(".nc_block").hide();
                        $("#num_oper_label").html('Dato Adicional');
                    }

                }

            });

    });


    function guardarPago() {


        var cantidad_pagar = parseFloat($("#cantidad_a_pagar").val());
        var total_pendiente = parseFloat($("#total_pendiente").val());

        if ($("#pago_id").val() == "") {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe seleccionar un pago</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if ($("#pago_id").val() == "4" && $("#banco_id").val() == '') {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe seleccionar un banco</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if (($("#pago_id").val() == "4" || $("#pago_id").val() == "5" || $("#pago_id").val() == "6") && $("#num_oper").val() == '') {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe ingresar una operacion</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if (($("#pago_id").val() == "6" && $("#desc_nc").val() == "")) {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe ingresar un motivo</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if (cantidad_pagar == "" || isNaN(cantidad_pagar)) {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe ingresar una cantidad</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        if (cantidad_pagar > total_pendiente) {
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


        $("#guardarPago").addClass('disabled');

        $.ajax({
            type: 'POST',
            data: $('#form').serialize(),
            dataType: 'json',
            url: '<?= base_url()?>ingresos/guardarPago',
            success: function (data) {
                if (data.success && data.error == undefined) {

                    $("#guardarPago").removeClass('disabled');
                    $.bootstrapGrowl('<h4>Pago realizado con exito</p>', {
                        type: 'success',
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $('#pagar_venta').modal('hide');
                    $("#guardarPago").removeClass('disabled');
                    $(".modal-backdrop").remove();
                    buscar();
                }
                else {
                    var growlType = 'danger';
                    $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p>Intente nuevamente</p>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $("#guardarPago").removeClass('disabled');
                }


            },

            error: function () {
                $("#guardarPago").removeClass('disabled');
                var growlType = 'danger';
                $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p>Intente nuevamente</p>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
            }
        })
    }
</script>


<style>
    table th {
        background-color: #f4f4f4;
    }

    .b-default {
        background-color: #1bb52a;
        color: #fff;
    }

    tr.b-warning:hover {
        background-color: #f7be64 !important;
        color: #fff !important;
    }

    .b-warning {
        background-color: #f7be64;
        color: #fff;
    }

    .b-danger {
        background-color: #e74c3c;
        color: #fff;
    }

    .b-primary {
        background-color: #1493D1 !important;
        color: #fff;
    }

</style>
<div class="modal-dialog" style="width: 80%">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Devolver Venta <span
                        id="venta_numero"><?= sumCod($venta->venta_id, 6) ?></span></h3>
            <input type="hidden" id="venta_id" value="<?= $venta->venta_id ?>">
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <div class="row-fluid">
                    <div class="row" style="font-size: 15px;">
                        <div class="col-md-2"><label class="control-label">Total Pagado:</label></div>
                        <div class="col-md-3"><?= MONEDA ?> <span
                                    id="total_pagado"
                                    data-documento="<?= $venta->documento_id ?>"
                                    data-subtotal="<?= $venta->subtotal ?>">
                                    <?= $venta->total ?>
                                </span></div>

                        <div class="col-md-1"></div>

                        <div class="col-md-3"><label class="control-label">Total Devolver:</label></div>
                        <div id="total_devolver_text" class="col-md-3"><?= MONEDA ?> <span
                                    id="total_devolver">0.00</span></div>
                    </div>

                    <hr class="hr-margin-5">

                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>ID</th>
                            <th>Producto</th>
                            <th>Cantidad</th>
                            <th>Devolver</th>
                            <th>UM</th>
                            <th>Precio</th>
                            <th>Subtotal</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($venta->detalles as $detalle): ?>
                            <tr class="producto_detalles_list <?= $detalle->bono == 1 ? 'b-warning' : '' ?>"
                                data-id="<?= $detalle->detalle_id ?>"
                                data-has_bono="<?= $detalle->bono ?>"
                                data-producto_id="<?= $detalle->producto_id ?>"
                                data-unidad_id="<?= $detalle->unidad_id ?>"
                            >
                                <td id="producto_codigo_<?= $detalle->detalle_id ?>"><?= $detalle->producto_id ?></td>
                                <td id="producto_nombre_<?= $detalle->detalle_id ?>"><?= $detalle->producto_nombre ?> <?= $detalle->bono == 1 ? '(BONO)' : '' ?></td>
                                <td id="cantidad_<?= $detalle->detalle_id ?>"
                                    data-cantidad="<?= $detalle->cantidad ?>"><?= $detalle->cantidad ?></td>
                                <td style="width: 150px;">
                                    <input class="form-control devolver_input"
                                           id="cantidad_devuelta_<?= $detalle->detalle_id ?>"
                                           data-id="<?= $detalle->detalle_id ?>"
                                           type="<?= $detalle->bono == 1 ? 'hidden' : 'number' ?>"
                                           style="text-align: center;"
                                           min="0" <?= $detalle->bono == 1 ? 'readonly' : '' ?>
                                           max="<?= $detalle->cantidad ?>"
                                           value="0">
                                </td>
                                <td id="unidad_nombre_<?= $detalle->detalle_id ?>"><?= $detalle->unidad_nombre ?></td>
                                <td id="precio_<?= $detalle->detalle_id ?>" style="text-align: right">
                                    <?= $detalle->precio ?>
                                </td>
                                <td style="text-align: right; width: 150px;"><?= MONEDA ?>
                                    <span id="subtotal_<?= $detalle->detalle_id ?>" class="subtotales">
                                            <?= number_format($detalle->importe, 2) ?>
                                        </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>

        <div class="modal-footer" align="right">
            <div class="row">
                <div class="text-right">

                    <div class="col-md-12">
                        <input id="devolver_venta_button" type="button" class='btn btn-primary' value="Devolver">

                        <input type="button" class='btn btn-danger' value="Cancelar"
                               data-dismiss="modal">
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="dialog_venta_confirm" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Confirmaci&oacute;n</h4>
            </div>

            <div class="modal-body ">
                <h5 id="confirm_venta_text">Estas Seguro?</h5>
            </div>

            <div class="modal-footer">
                <button id="confirm_venta_button" type="button" class="btn btn-primary">
                    Aceptar
                </button>

                <button type="button" class="btn btn-danger" onclick="$('#dialog_venta_confirm').modal('hide');">
                    Cancelar
                </button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>


</div>

<script>

    var devoluciones = [];

    $('.devolver_input').bind('keyup change click mouseleave', function () {
        var id = $(this).attr('data-id');
        var devolver = isNaN(parseFloat($(this).val())) ? 0 : parseFloat($(this).val());
        var cantidad = isNaN(parseFloat($('#cantidad_' + id).attr('data-cantidad'))) ? 0 : parseFloat($('#cantidad_' + id).attr('data-cantidad'));
        var precio = parseFloat($('#precio_' + id).html().trim());

        var cantidad_td = $('#cantidad_' + id);
        var subtotal_td = $('#subtotal_' + id);

        cantidad_td.html(parseFloat(cantidad - devolver).toFixed(2));
        subtotal_td.html(parseFloat((cantidad - devolver) * precio).toFixed(2));

        var subtotales = 0;
        $('.subtotales').each(function () {
            subtotales += parseFloat($(this).html())
        });

        if ($("#total_pagado").attr('data-documento') == '1') {
            var total_devolver = parseFloat($("#total_pagado").attr('data-subtotal')) - subtotales;
            total_devolver = total_devolver + ((total_devolver * 18) / 100);
            $('#total_devolver').html(formatPrice(total_devolver));
        }
        else
            $('#total_devolver').html(formatPrice(parseFloat($('#total_pagado').html()) - subtotales));
    });

    $('.devolver_input').on('focus', function () {
        $(this).select();
    });

    $('#devolver_venta_button').on('click', function () {


        if (!validar_venta())
            return false;

        prepare_devolucion(function () {

            var template = '<h3>Devoluci&oacute;n de la Venta ' + $('#venta_numero').html().trim() + '</h3>';
            template += '<hr class="hr-margin-10">';
            template += '<h4><label>Productos Devueltos:</label></h4>';
//            console.log(devoluciones);
            for (var i = 0; i < devoluciones.length; i++) {
                if (devoluciones[i].devolver != 0) {
                    var id = devoluciones[i].detalle_id;
                    var producto_codigo = $('#producto_codigo_' + id).html().trim();
                    var producto_nombre = $('#producto_nombre_' + id).html().trim();
                    var unidad_nombre = $('#unidad_nombre_' + id).html().trim();
                    var cantidad_devuelta = devoluciones[i].devolver;
                    var bono_class = devoluciones[i].bono == 1 ? 'b-warning' : '';

                    template += '<div class="row ' + bono_class + '">';
                    template += '<div class="col-md-8">' + producto_codigo + ' - ' + producto_nombre + '</div>';
                    template += '<div class="col-md-4">' + cantidad_devuelta + ' ' + unidad_nombre + '</div>';
                    template += '</div>';
                    template += '<hr class="hr-margin-5">';
                }
            }


            template += '<hr class="hr-margin-10">';
            template += '<h4><label>Total a devolver:</label> ' + $('#total_devolver_text').html().trim() + '</h4>';

            $('#confirm_venta_text').html(template);
            $('#confirm_venta_button').attr('onclick', 'devolver_venta();');

            $('#dialog_venta_confirm').modal('show');
        });

    });

    function validar_venta() {
        var flag = true;
        var n = 0;

        $('.producto_detalles_list').each(function () {
            var id = $(this).attr('data-id');
            var cantidad = parseFloat($('#cantidad_' + id).html());
            var old_cantidad = parseFloat($('#cantidad_' + id).attr('data-cantidad'));

            if (cantidad < 0) {
                $.bootstrapGrowl('<h4>Error.</h4> <p>No puede hacer una devoluci&oacute;n mayor a la cantidad.</p>', {
                    type: 'warning',
                    delay: 5000,
                    allow_dismiss: true
                });
                $('#cantidad_devuelta_' + id).trigger('focus');
                flag = false;
                return false;
            }

            if (cantidad == old_cantidad)
                n++;


        });
        if (n == $('.producto_detalles_list').length) {
            $.bootstrapGrowl('<h4>Error.</h4> <p>Por favor devuelva una cantidad.</p>', {
                type: 'warning',
                delay: 5000,
                allow_dismiss: true
            });
            $('#cantidad_devuelta_' + id).trigger('focus');
            return false;
        }


        if (parseFloat($('#total_pagado').html()) == parseFloat($('#total_devolver').html())) {
            $.bootstrapGrowl('<h4>Error.</h4> <p>No puede devolver todos los productos. Haga un rechazo.</p>', {
                type: 'warning',
                delay: 5000,
                allow_dismiss: true
            });
            $('#cantidad_devuelta_' + id).trigger('focus');
            return false;
        }

        return flag;
    }

    function devolver_venta() {


        $("#barloadermodal").modal({
            show: true,
            backdrop: 'static'
        });

        var venta_id = $("#venta_id").val();

        var total_importe = parseFloat($("#total_pagado").html()) - parseFloat($("#total_devolver").html());
        if ($("#total_pagado").attr('data-documento') == '1') {
            var subtotales = 0;
            $('.subtotales').each(function () {
                subtotales += parseFloat($(this).html())
            });
            total_importe = parseFloat($("#total_pagado").attr('data-subtotal')) - subtotales;
        }

        prepare_devolucion(function () {

            $.ajax({
                url: '<?php echo base_url() . 'venta/devolver_venta'; ?>',
                type: 'POST',
                data: {
                    'venta_id': venta_id,
                    'total_importe': total_importe,
                    'devoluciones': JSON.stringify(devoluciones)
                },

                success: function () {
                    $('#dialog_venta_confirm').modal('hide');
                    $('#ventamodal_devolver').modal('hide');
                    $(".modal-backdrop").remove();
                    $.bootstrapGrowl('<h4>Correcto.</h4> <p>Venta devuelta con exito.</p>', {
                        type: 'success',
                        delay: 5000,
                        allow_dismiss: true
                    });

                    var consolidado_id = $("#con_id").val();
                    $.ajax({
                        url: '<?= base_url()?>consolidadodecargas/get_pedido' + '/' + venta_id,
                        type: 'POST',
                        headers: {
                            Accept: 'application/json'
                        },
                        success: function (data) {

                            $("#id_pedido_liquidacion").val(venta_id);
                            $("#consolidado_id").val(consolidado_id);
                            $("#estatus").val('DEVUELTO PARCIALMENTE').trigger('chosen:updated');
                            $("#pago_id").val('3').trigger('chosen:updated');
                            $(".devolver_block").show();
                            $("#banco_block").hide();
                            $(".pago_block").show();
                            $("#cobrar_todo").prop('checked', false);

                            $("#num_oper").val('');
                            $("#pedido_numero").html(venta_id);
                            $("#total").val(data.pedido.total);
                            $("#monto").val(0);

                            $("#cambiarEstatus").modal('show');
                        },
                        error: function () {
                            $.bootstrapGrowl('<h4>Ha ocurrido un error en la opci&oacute;n</h4>', {
                                type: 'warning',
                                delay: 2500,
                                allow_dismiss: true
                            });
                        },
                        complete: function (data) {
                            $('#barloadermodal').modal('hide');
                        }
                    })
                },
                error: function () {

                    $.bootstrapGrowl('<h4>Error.</h4> <p>Ha ocurrido un error en la operaci&oacute;n</p>', {
                        type: 'danger',
                        delay: 5000,
                        allow_dismiss: true
                    });
                    $('#barloadermodal').modal('hide');
                }
            });
        });


    }

    function prepare_devolucion(callback) {
        var venta_id = $("#venta_id").val();
        var productos = [];
        devoluciones = [];

        $('.producto_detalles_list').each(function () {
            var id = $(this).attr('data-id');
            var bono = $(this).attr('data-has_bono');

            if (bono != 1) {
                productos.push({
                    producto_id: $(this).attr('data-producto_id'),
                    unidad_id: $(this).attr('data-unidad_id'),
                    cantidad: parseFloat($('#cantidad_' + id).html())
                });
            }
        });

        $('#barloadermodal').modal('show');
        $.ajax({
            url: '<?= base_url()?>venta/calcular_devolver_pedido_bonos' + '/' + venta_id,
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            data: {productos: JSON.stringify(productos)},
            success: function (data) {

                $('.producto_detalles_list').each(function () {
                    var temp = {};
                    var id = $(this).attr('data-id');
                    var bono = $(this).attr('data-has_bono');

                    if (bono != 1) {
                        temp.detalle_id = id;
                        temp.producto_id = $(this).attr('data-producto_id');
                        temp.unidad_id = $(this).attr('data-unidad_id');
                        temp.devolver = isNaN(parseFloat($('#cantidad_devuelta_' + id).val())) ? 0 : parseFloat($('#cantidad_devuelta_' + id).val());
                        temp.new_cantidad = parseFloat($('#cantidad_' + id).html());
                        temp.new_importe = parseFloat($('#subtotal_' + id).html());
                        temp.bono = 0;

                    }
                    else {

                        temp.detalle_id = id;
                        temp.producto_id = $(this).attr('data-producto_id');
                        temp.unidad_id = $(this).attr('data-unidad_id');
                        temp.new_cantidad = 0;
                        temp.devolver = parseFloat($('#cantidad_' + id).html());

                        for (var i = 0; i < data.new_bonos.length; i++) {
                            if (data.new_bonos[i].producto_id == $(this).attr('data-producto_id') && data.new_bonos[i].unidad_id == $(this).attr('data-unidad_id')) {
                                temp.new_cantidad = parseFloat(data.new_bonos[i].cantidad);
                                temp.devolver = parseFloat($('#cantidad_' + id).html() - data.new_bonos[i].cantidad);
                                break;
                            }
                        }

                        temp.new_importe = 0;
                        temp.bono = 1;
                    }

                    devoluciones.push(temp);
                });


                callback();

            },
            complete: function (data) {
                $('#barloadermodal').modal('hide');
            },
            error: function (data) {
                alert('not');
            }
        });
    }


</script>

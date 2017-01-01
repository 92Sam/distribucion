<?php $ruta = base_url(); ?>
<style>
    .btn-other {
        background-color: #3b3b1f;
        color: #fff;
    }

    .b-default {
        background-color: #55c862;
        color: #fff;
    }

    .b-warning {
        background-color: #f7be64;
        color: #fff;
    }

    .b-primary {
        background-color: #2CA8E4;
        color: #fff;
    }

    .table td {
        font-weight: normal;
        font-size: 11px;
        vertical-align: middle !important;
    }

    .btn-group-sm > .btn, .btn-sm {
        font-size: 10px;
    }
</style>
<ul class="breadcrumb breadcrumb-top">
    <li>Flujo de Trabajo</li>
    <li>Liquidación CGC</li>
</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
?>
<div class="span12">
    <div class="block">
        <form id="frmBuscar">

            <div class="row">
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Estado:</label>

                    <select name="estado" id="estado" class='cho form-control filter-input'>
                        <option value="-1">TODOS</option>
                        <option value="IMPRESO" selected>IMPRESO</option>
                        <option value="CERRADO">CERRADO</option>
                        <option value="CONFIRMADO">CONFIRMADO</option>

                    </select>
                </div>

                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Desde:</label>

                    <input type="text" name="fecha_ini" id="fecha_ini" value=""
                           required="true" readonly style="cursor: pointer;"
                           class="form-control fecha input-datepicker filter-input">
                </div>
                <div class="col-md-2">
                    <label class="control-label panel-admin-text">Hasta:</label>

                    <input type="text" name="fecha_fin" id="fecha_fin" value=""
                           required="true" readonly style="cursor: pointer;"
                           class="form-control fecha input-datepicker filter-input">
                </div>

                <div class="col-md-3" style="padding:1.5% 1%">
                    <input type="checkbox" name="limpiar_fecha" id="limpiar_f">
                    <label for="habilitar_f" class="control-label panel-admin-text">Limpiar Fechas</label>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a class="btn btn-default" id="btn_buscar">
                        <i class="fa fa-search"> </i>
                    </a>
                </div>


                <div class="col-md-3 text-right" style="padding:2% 1%">
                    <label class="control-label badge btn-other">IMPRESO</label>
                    <label class="control-label badge b-primary">CERRADO</label>
                    <label class="control-label badge b-warning">CONFIRMADO</label>

                </div>

            </div>
            <br>
        </form>


        <div class="block">
            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>

            <div class="table-responsive" id="tablaresultado">


            </div>
        </div>
    </div>

    <script type="text/javascript">

        function VerConsolidado(id, status) {
            estatus_consolidado = status;
            $("#consolidadoLiquidacion").html($('#loading').html());
            $("#consolidadoLiquidacion").load('<?= $ruta ?>consolidadodecargas/verDetallesLiquidacion/' + id + '/' + status);
            $('#consolidadoLiquidacion').modal('show');

        }
    </script>


    <div class="modal fade" id="consolidadoLiquidacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

    </div>


    <div class="modal fade" id="cambiarEstatus" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <form name="formliquidacion" method="post" id="formliquidacion"
              action="<?= base_url() ?>consolidadodecargas/liquidarPedido">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Liquidar pedido</h4>
                    </div>

                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-md-2">
                                <label class="control-label panel-admin-text">
                                    Estado
                                </label>
                            </div>
                            <div class="col-md-10">
                                <select id="estatus" class="" name="estatus" onchange="verificar_select()">
                                    <option value="ENTREGADO" selected> ENTREGADO</option>
                                    <option value="DEVUELTO PARCIALMENTE"> DEVUELTO PARCIALMENTE</option>
                                    <option value="RECHAZADO"> RECHAZADO</option>
                                </select>
                                <input type="hidden" id="estatus_value_entregado" value="">
                                <input type="hidden" id="estatus_value_devuelto" value="">
                                <input type="hidden" id="estatus_value_rechazado" value="0.00">
                            </div>
                        </div>
                        <div class="row devolver_block" style="display: none;">
                            <br>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text">PEDIDO: <span
                                            id="pedido_numero"></span></label>
                                </div>
                                <div class="col-md-8">
                                    <button type="button" style="color: #fff;" id="editar_pedido"
                                            class="form-control btn btn-warning">
                                        <li class="fa fa-edit"></li>
                                        MODIFIQUE SU PEDIDO
                                    </button>
                                </div>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label class="control-label panel-admin-text"> Total Venta</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <div class="input-group-addon"><?= MONEDA ?></div>
                                        <input type="text" readonly value="0" name="total" id="total" required="true"
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
                                            <?php if ($pago->id_metodo != 7 && $pago->id_metodo != 6): ?>
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
                        <div class="row pago_block">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Monto a Cobrar</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" id="monto" name="monto"
                                           class="form-control"
                                           value="">
                                    <input type="checkbox" id="cobrar_todo">
                                    <label for="cobrar_todo"
                                           class="control-label"
                                           style="cursor: pointer;">
                                        Cobrar todo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="id_pedido_liquidacion" id="id_pedido_liquidacion" required="true"
                               class="form-control"
                               style="width:50px;">

                        <input type="hidden" name="consolidado_id" id="consolidado_id"
                               required="true"
                               class="form-control"
                               style="display:none;">

                        <div class="modal-footer">
                            <button type="button" id="" class="btn btn-primary" onclick="validar_estatus()">
                                <li class="glyphicon glyphicon-thumbs-up"></li>
                                Confirmar
                            </button>
                            <button type="button" id="liquidacion_cancelar" class="btn btn-warning" data-dismiss="modal">
                                <li class="glyphicon glyphicon-thumbs-down"></li>
                                Cancelar
                            </button>
                        </div>


                    </div>

                </div>
                <!-- /.modal-content -->
            </div>
        </form>
    </div>
    <div class="modal fade" id="ventamodal_devolver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">


    </div>

    <div class="modal fade" id="confirmacion" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Confirmaci&oacute;n</h4>
                </div>

                <div class="modal-body">Al cambiar el estado el pedido regresara a su estado original,
                    ¿Esta seguro que desea realizar esta acción?
                </div>
                <div class="modal-footer">
                    <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">
                        <li class="glyphicon glyphicon-thumbs-up"></li>
                        Si
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <li class="glyphicon glyphicon-thumbs-down"></li>
                        No
                    </button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

    </div>


    <div class="modal fade" id="ventamodal" style="width: 80%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
                </button>
                <h4>Editar Pedido</h4>
            </div>
            <div class="modal-body" id="ventamodalbody">

            </div>

        </div>

    </div>


    <script>
        var estatus_actual = 'ENTREGADO';
        var estatus_select = 'ENTREGADO';
        var estatus_consolidado;


        $(function () {
            $("#estatus").chosen({width: "100%"});

            listadoajax();

            $("#liquidacion_cancelar").on('click', function(){
                $("#consolidadoLiquidacion").load('<?= $ruta ?>consolidadodecargas/verDetallesLiquidacion/' + $("#consolidado_id").val() + '/IMPRESO');
                $("#cambiarEstatus").modal('hide');
            });

            $("#editar_pedido").on('click', function () {
                devolver();
            });

            $("#pago_id").on('click', function () {

                $("#banco_id").val('');
                $("#num_oper").val('');
                $("#monto").val('0');
                $("#retencion_block").hide();
                $("#banco_block").hide();

                if ($(this).val() == '4') {
                    $("#banco_block").show();
                    $("#num_oper_label").html('N&uacute;mero de Operaci&oacute;n');
                }
                else if ($(this).val() != '4') {

                    if ($(this).val() == '5')
                        $("#num_oper_label").html('N&uacute;mero de Cheque');
                    if ($(this).val() == '6')
                        $("#num_oper_label").html('N&uacute;mero de Nota de Cr&eacute;dito');
                    else
                        $("#num_oper_label").html('Dato Adicional');
                }

            });

            $("#btn_buscar").click(function () {
                if ($('#fecha_ini').val() != '' || $('#fecha_fin').val() != '') {
                    $('#limpiar_f').prop('checked', false)

                    if ($('#fecha_ini').val() == '' || $('#fecha_fin').val() == '') {

                        $.bootstrapGrowl('<h4>Debe completar ambos campos del rango de fechas</h4>', {
                            type: 'warning',
                            delay: 2500,
                            allow_dismiss: true
                        });
                        return false
                    }
                }

                listadoajax();
            });

            $('#limpiar_f').click(function () {
                if ($('#limpiar_f').is(':checked')) {
                    $('#fecha_ini').val('')
                    $('#fecha_fin').val('')

                }
            })

            $('.fecha').change(function () {
                if ($('#fecha_ini').val() != '' || $('#fecha_fin').val() != '') {
                    $('#limpiar_f').prop('checked', false)
                }
            })

            $('#estado').change(function () {
                var table = $('#example').DataTable();

                table
                    .clear()
                    .draw();
            })

            $('#cobrar_todo').prop('checked', false)

            $('#cobrar_todo').click(function () {
                if ($('#cobrar_todo').is(':checked')) {
                    $('#monto').val($('#total').val())
                } else {
                    $('#monto').val(0)
                }
            })

        });

        function devolver() {

            $("#barloadermodal").modal({
                show: true,
                backdrop: 'static'
            });

            var venta_id = $("#pedido_numero").html().trim();

            $.ajax({
                url:  '<?php echo base_url()?>venta/devolver_pedido',
                type: 'POST',
                data: {'venta_id': venta_id},

                success: function (data) {
                    $('#barloadermodal').modal('hide');
                    $("#ventamodal_devolver").html(data);
                    $("#ventamodal_devolver").modal('show');
                },
                error: function (error) {
                    $('#barloadermodal').modal('hide');
                    alert('Ha ocurrido un error');

                }
            });
        }


        function listadoajax() {
            var estado = $("#estado").val();
            $(".table-responsive").html($("#loading").html());
            $.ajax({
                url: '<?= base_url()?>consolidadodecargas/buscarPorEstado',
                data: $('#frmBuscar').serialize(),
                type: 'POST',
                success: function (data) {

                    if (data.length > 0)
                        $(".table-responsive").html(data);

                },
                error: function () {
                    $(".table-responsive").html('');
                    $.bootstrapGrowl('<h4>Ha ocurrido un error en la opci&oacute;n</h4>', {
                        type: 'warning',
                        delay: 2500,
                        allow_dismiss: true
                    });
                }
            })
        }
        function verificar_select() {
            $(".pago_block").hide();
            $(".devolver_block").hide();

            if ($("#estatus").val() == 'RECHAZADO') {
                $("#monto").val('0');
                $("#total").val($("#estatus_value_rechazado").val());
            }
            if ($("#estatus").val() == 'ENTREGADO') {
                $("#monto").val('0');
                $(".pago_block").fadeIn(100);
                $("#total").val($("#estatus_value_entregado").val());
            }
            if ($("#estatus").val() == 'DEVUELTO PARCIALMENTE') {
                $("#monto").val('0');
                $(".pago_block").fadeIn(100);
                $(".devolver_block").fadeIn(100);
                $("#total").val($("#estatus_value_entregado").val());
                devolver();
            }
        }

        function validar_estatus() {
            $("#confirmacion").modal('show');
        }

        var grupo = {
            ajaxgrupo: function () {
                return $.ajax({
                    url: '<?= base_url()?>consolidadodecargas/liquidacion'

                })
            },
            guardar: function () {

                var monto = parseFloat($("#monto").val());
                var total = parseFloat($("#total").val());

                if (monto > total) {
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>Debe ingresar un monto menor a al total de la deuda </h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    return false;
                }

                if (monto < 0) {
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>El monto a liquidar no puede ser negativo</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    return false;
                }


                $.ajax({
                    url: '<?= base_url() ?>consolidadodecargas/liquidarPedido',
                    type: 'POST',
                    data: $("#formliquidacion").serialize(),
                    dataType: 'json',
                    success: function (data) {
                        if (data.error == undefined) {
                            $('#confirmacion').modal('hide');
                            $('#barloadermodal').modal('hide');
                            $('#cambiarEstatus').modal('hide');
                            $("#consolidadoLiquidacion").load('<?= $ruta ?>consolidadodecargas/verDetallesLiquidacion/' + $('#consolidado_id').val() + '/' + estatus_consolidado);

                        }
                    }

                });
            },
            cerrarLiquidacion: function () {
                App.formSubmitAjax($("#formcerrarliquidacion").attr('action'), this.ajaxgrupo, 'consolidadoLiquidacion', 'formcerrarliquidacion');

            }
        }
        function liquidarCdc(id_consolidado) {
            var id = id_consolidado;
            $.ajax({
                url: '<?= base_url()?>consolidadodecargas/cerrarLiquidacion',
                data: {
                    'id': id
                },
                type: 'POST',
                success: function (data) {
                    if (data.length > 0)
                        $("#consolidadoLiquidacion").modal('hide');
                },
                error: function () {

                    alert('Ocurrio un error por favor intente nuevamente');
                }
            })
        }
        function devolverpedido(id, coso_id) {


            $("#barloadermodal").modal({
                show: true,
                backdrop: 'static'
            });

            var id = id;
            //console.log(id);

            $("#ventamodalbody").html('');
            $.ajax({
                url: '<?php echo base_url()?>venta/pedidos',
                data: {
                    'idventa': id,
                    'devolver': 1,
                    'preciosugerido': 0,
                    'coso_id': coso_id,
                    'estatus_actual': estatus_actual,
                    'estatus_consolidado': estatus_consolidado
                },
                type: 'post',
                success: function (data) {
                    $('#barloadermodal').modal('hide');
                    $("#ventamodalbody").html(data);


                }, error: function (error) {

                    $('#barloadermodal').modal('hide');
                    var growlType = 'danger';

                    $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p></p>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $('#pago_modal').modal('hide');
                    return false;
                }
            })
            $("#ventamodal").modal('show');
        }

    </script>



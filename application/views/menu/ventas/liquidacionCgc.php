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

                <div class="col-md-2">
                    <br>
                    <input type="checkbox" id="limpiar_f" name="limpiar_fecha">
                    <label for="limpiar_f"
                           class="control-label panel-admin-text"
                           style="cursor: pointer;">
                        Limpiar Fechas
                    </label>
                </div>

                <div class="col-md-1">
                    <br>
                    <button type="button" title="Buscar" id="btn_buscar"
                            class="btn btn-default form-control btn_buscar">
                        <i class="fa fa-search"></i>
                    </button>
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
         data-backdrop="static" data-keyboard="false"
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

                        <div class="row motivo_block" style="display: none;">
                            <br>
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label>Seleccione el Motivo</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="motivo_id" id="motivo_id" class="form-control">
                                        <option value="">Seleccione</option>
                                        <?php foreach (get_motivo_rechazo() as $key => $val): ?>
                                            <option
                                                    value="<?= $key ?>"><?= $val ?></option>
                                        <?php endforeach ?>
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
                        <div class="row" id="fechaoperacion_block" style="display: none;">
                            <div class="form-group">
                                <div class="col-md-4">
                                    <label id="fec_oper_label">Fecha Operación</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" id="fec_oper" name="fec_oper"
                                           class="form-control input-datepicker"
                                           value="<?= date('d-m-Y') ?>" readonly style="cursor: pointer;">
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
                                           class="form-control" autocomplete="off"
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
                            <button type="button" id="liquidacion_cancelar" class="btn btn-warning"
                                    data-dismiss="modal">
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

        jQuery(document).ready(function () {
            $("#fechaoperacion_block").hide();
        });


        $(function () {
            $("#estatus").chosen({width: "100%"});

            listadoajax();

            $("#liquidacion_cancelar").on('click', function () {
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
                $("#fechaoperacion_block").hide();

                if ($(this).val() == '4') {
                    $("#banco_block").show();
                    $("#num_oper_label").html('N&uacute;mero de Operaci&oacute;n');
                    $("#fechaoperacion_block").show();
                }
                else if ($(this).val() != '4') {

                    if ($(this).val() == '5') {
                        $("#fechaoperacion_block").hide();
                        $("#num_oper_label").html('N&uacute;mero de Cheque');
                    }
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
                        show_msg('warning', 'Debe completar ambos campos del rango de fechas');
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
                $(".table-responsive").html('');
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
                url: '<?php echo base_url()?>venta/devolver_pedido',
                type: 'POST',
                data: {'venta_id': venta_id},

                success: function (data) {
                    $('#fechaoperacion_block').hide();
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
                    show_msg('warning', 'Ha ocurrido un error en la opci&oacute;n');
                }
            })
        }
        function verificar_select() {
            $(".pago_block").hide();
            $(".devolver_block").hide();
            $(".motivo_block").hide();

            if ($("#estatus").val() == 'RECHAZADO') {
                $("#monto").val('0');
                $("#total").val($("#estatus_value_rechazado").val());
                $(".motivo_block").show();
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
            var numeroOperacion = $("#num_oper").val();
            var mediodePago = $('#pago_id option:selected').text();
            var montoaCobrar = $('#monto').val();
            var banco = $("#banco_id").val();

            //EFECTIVO = 3
            //DEPOSITO = 4
            //CHEQUE = 5

            if ($("#estatus").val() == 'RECHAZADO' && $("#motivo_id").val() == '') {
                show_msg('warning', 'Debe seleccionar un motivo');
                return false;
            }


            if ($("#pago_id").val() != 0) {

                if ($("#pago_id").val() == 3)
                    $("#confirmacion").modal('show');

                if ($("#pago_id").val() == 4) {
                    if (banco != '' && numeroOperacion != '') {
                        if (montoaCobrar == '0' || montoaCobrar == ' ') {
                            show_msg('warning', 'El importe del deposito debe ser mayor a cero');
                            $('#monto').trigger('focus');
                        }
                        else
                            $("#confirmacion").modal('show');
                    }
                    else
                        show_msg('warning', 'Es necesario seleccionar un banco e indicar el numero de operación');
                }
                if ($("#pago_id").val() == 5) {
                    if (montoaCobrar == '0' || montoaCobrar == ' ') {
                        show_msg('warning', 'El importe del deposito debe ser mayor a cero');
                        $('#monto').trigger('focus');
                    }
                    else
                        $("#confirmacion").modal('show');
                }
            }
            else {
                show_msg('warning', 'Seleccione un medio medio de pago');
                $("#pago_id").trigger('focus');
            }
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
                    show_msg('warning', 'Debe ingresar un monto menor a al total de la deuda');
                    return false;
                }

                if (monto < 0) {
                    show_msg('warning', 'El monto a liquidar no puede ser negativo');
                    return false;
                }

                $('#barloadermodal').modal('show');
                $.ajax({
                    url: '<?= base_url() ?>consolidadodecargas/liquidarPedido',
                    type: 'POST',
                    data: $("#formliquidacion").serialize(),
                    dataType: 'json',
                    success: function (data) {

                        if (data.success == '1') {
                            $('#confirmacion').modal('hide');
                            $('#cambiarEstatus').modal('hide');
                            $("#consolidadoLiquidacion").load('<?= $ruta ?>consolidadodecargas/verDetallesLiquidacion/' + $('#consolidado_id').val() + '/' + estatus_consolidado);
                        }
                        else if (data.error == '1') {
                            $("#confirmacion").modal('hide');
                            show_msg('warning', '<h4>Error. </h4><p>El numero de operación ingresado ya fue registrado</p>');
                            $("#num_oper").trigger('focus');
                        }
                    },
                    complete: function () {
                        $('#barloadermodal').modal('hide');
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

        //Validamos que el numero de operacion no se repita
        function validarNumeroOperacion() {

            return false;
            var operacion = $("#num_oper").val();
            $.ajax({
                url: '<?= base_url()?>banco/validaNumeroOperacion/' + operacion,
                dataType: 'json',
                async: false,
                data: {'operacion': operacion},
                type: 'POST',

                success: function (data) {
                    if (data.error == undefined)
                        result = false;
                    else
                        result = true;

                },
                error: function () {
                    show_msg('danger', 'Ha ocurrido un error vuelva a intentar');
                }
            })

            return result;

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

                    show_msg('warning', 'Ha ocurrido un error');

                    $('#pago_modal').modal('hide');
                    return false;
                }
            })
            $("#ventamodal").modal('show');
        }

    </script>

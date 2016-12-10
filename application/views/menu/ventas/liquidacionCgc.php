<?php $ruta = base_url(); ?>
<style>
    .btn-other{
        background-color: #3b3b1f;
        color: #fff;
    }

    .b-default{
        background-color: #55c862;
        color: #fff;
    }
    .b-warning{
        background-color: #f7be64;
        color: #fff;
    }
    .b-primary{
        background-color: #2CA8E4;
        color: #fff;
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
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
                            <select id="estatus" class=""  name="estatus" onchange="verificar_select()" data-placeholder="Seleccione una acci&oacute;n">
                            <option></option>
                                <option value="ENTREGADO"> ENTREGADO</option>
                                <option value="DEVUELTO PARCIALMENTE"> DEVUELTO PARCIALMENTE</option>
                                <option value="RECHAZADO"> RECHAZADO</option>

                            </select>
                        </div>
                    </div>
                    <div class="form-group row" id="div_monto" style="display:none;">

                        <div class="col-md-3">
                            <label class="control-label panel-admin-text"> Total Venta</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" readonly value="10" name="total" id="total" required="true"
                                   class="form-control"
                            >
                            <input type="hidden" readonly value="10" name="totalbackup" id="totalbackup" required="true"
                                   class="form-control"
                            >
                        </div>


                        <div class="col-md-3" style="display: none">
                           <label class="control-label panel-admin-text"> Saldo a cuenta</label>
                        </div>
                        <div class="col-md-8" style="display: none">
                            <input type="text" readonly value="10" name="acuenta" id="acuenta" required="true"
                                   class="form-control"
                            >
                        </div>


                        <div class="col-md-3">
                            <label class="control-label panel-admin-text"> Pendiente de pago</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" readonly value="10" name="pendiente" id="pendiente" required="true"
                                   class="form-control"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="control-label panel-admin-text"> Monto cobrado</label>

                        </div>
                        <div class="col-md-4">
                            <input type="number" onkeydown="return soloDecimal(this, event);" name="monto" id="monto"
                                   required="true"
                                   class="form-control"
                            >
                        </div>
                        <div class="col-md-4" >
                            <input type="checkbox" id="cobrar_todo" >
                            <label for="cobrar_todo"
                                   class="control-label panel-admin-text"
                                   style="cursor: pointer;">
                                Cobrar todo
                            </label>
                        </div>
                    </div>
                    <div class="col-md-10">
                        <input type="hidden" name="id_pedido_liquidacion" id="id_pedido_liquidacion" required="true"
                               class="form-control"
                               style="width:50px;"><input type="hidden" name="consolidado_id" id="consolidado_id"
                                                          required="true"
                                                          class="form-control"
                                                          style="display:none;">
                    </div>

                    <div class="modal-footer">
                        <button type="button" id="" class="btn btn-primary" onclick="validar_estatus()">
                            <li class="glyphicon glyphicon-thumbs-up"></li> Confirmar
                        </button>
                        <button type="button" class="btn btn-warning" data-dismiss="modal">
                            <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar
                        </button>
                    </div>


                </div>

            </div>
            <!-- /.modal-content -->
        </div>
    </form>
</div>
<div class="modal fade" id="ventamodal" style="width: 85%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><li class="glyphicon glyphicon-thumbs-up"></li>
            </button>

            <h3>Devolver Pedido</h3>
        </div>
        <div class="modal-body" id="ventamodalbody">


        </div>

    </div>

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
                <li class="glyphicon glyphicon-thumbs-up"></li> Si</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                <li class="glyphicon glyphicon-thumbs-down"></li> No</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>

</div>



<script>
    var estatus_actual = '';
    var estatus_select;
    var estatus_consolidado;
    $(function () {
        $("#estatus").chosen({width: "100%"});
            listadoajax();

             $("#btn_buscar").click(function () {
            if($('#fecha_ini').val() != '' || $('#fecha_fin').val() != ''){
                $('#limpiar_f').prop('checked', false)

                if($('#fecha_ini').val() == '' || $('#fecha_fin').val() == ''){

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

        $('#limpiar_f').click(function(){
             if($('#limpiar_f').is(':checked')){
                    $('#fecha_ini').val('')
                    $('#fecha_fin').val('')

                }
        })

        $('.fecha').change(function(){
            if($('#fecha_ini').val() != '' || $('#fecha_fin').val() != ''){
                $('#limpiar_f').prop('checked', false)
            }
        })

        $('#estado').change(function(){
            var table = $('#example').DataTable();

            table
                .clear()
                .draw();
            })

                    $('#cobrar_todo').prop('checked', false)

        $('#cobrar_todo').click(function(){
            if($('#cobrar_todo').is(':checked')){
                $('#monto').val($('#pendiente').val())
            }else{
                $('#monto').val(0)
            }
        })

    });

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
        if ($("#estatus").val() == 'ENTREGADO') {
            $("#monto").val('0');
            $("#div_monto").fadeIn(100);
        }
        else {
            $("#monto").val('0');
            $("#div_monto").fadeOut(100);
        }
    }
    function liquidarPedido(id, acuenta, total, consolidado_id, estatus_pedido, cobrado,totalbackup) {


        $("#consolidado_id").val(consolidado_id);
        $("#id_pedido_liquidacion").val(id);
        $("#acuenta").val(acuenta);
        $("#total").val(total);
        $("#totalbackup").val(totalbackup);
        $("#monto").val(cobrado);


        $("#pendiente").val(parseFloat(total - acuenta).toFixed(2));
        $("#cambiarEstatus").modal('show');

        estatus_actual = estatus_pedido

        ////guardo en la variable el estatus actual del pedido
        $('#estatus > option[value="' + estatus_pedido + '"]').attr('selected', 'selected');
        $("#estatus").val(estatus_pedido).trigger("chosen:updated");
        if (estatus_pedido == 'ENTREGADO') {
            $("#div_monto").fadeIn(100);
        } else {
            $("#div_monto").fadeOut(100);
        }

    }
    function validar_estatus() {

        if (estatus_actual == 'DEVUELTO PARCIALMENTE' && $('#estatus').val() != 'DEVUELTO PARCIALMENTE') {

            $("#confirmacion").modal('show');
        } else {
            grupo.guardar()
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
            var acuenta = parseFloat($("#acuenta").val());
            var total = parseFloat($("#total").val());


            estatus_select = $('#estatus').val();

            if (estatus_actual == 'DEVUELTO PARCIALMENTE' && $('#estatus').val() != 'DEVUELTO PARCIALMENTE') {
                total=$("#totalbackup").val();
            }


            if (monto > (total - acuenta)) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar un monto menor a al total de la deuda </h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                return false;
            }
            var pasar = true;

            if (estatus_actual == 'RECHAZADO' || estatus_actual == 'ENTREGADO') {

                if (estatus_select == 'DEVUELTO PARCIALMENTE') {
                    pasar = false;
                } else {
                    pasar = true;
                }
            }

            if (estatus_actual == 'ENVIADO' && estatus_select != 'DEVUELTO PARCIALMENTE') {

                pasar = true;
            }

            if (estatus_actual == 'ENVIADO' && estatus_select == 'DEVUELTO PARCIALMENTE') {

                pasar = false;
            }
            if (estatus_actual == 'DEVUELTO PARCIALMENTE' && estatus_select != 'DEVUELTO PARCIALMENTE') {

                pasar = true;
            }

            if (estatus_actual == 'DEVUELTO PARCIALMENTE' && estatus_select == 'DEVUELTO PARCIALMENTE') {

                pasar = false;
            }

            if (pasar == false) {

                var id = $('#id_pedido_liquidacion').val();
                devolverpedido(id, $("#consolidado_id").val());
                return false;
            }
            else {
                //$("#consolidadoLiquidacion").modal('hide');
                $.ajax({
                    url: '<?= base_url() ?>consolidadodecargas/liquidarPedido',
                    type: 'POST',
                    data: $("#formliquidacion").serialize() + '&estatus_actual=' + estatus_actual,
                    dataType: 'json',
                    success: function (data) {
                        if (data.error == undefined) {
                            estatus_actual = estatus_select
                            $('#confirmacion').modal('hide');
                            $('#barloadermodal').modal('hide');
                            $('#cambiarEstatus').modal('hide');
                            $("#consolidadoLiquidacion").load('<?= $ruta ?>consolidadodecargas/verDetallesLiquidacion/' + $('#consolidado_id').val() + '/' + estatus_consolidado);

                        }
                    }

                })

                //App.formSubmitAjax($("#formliquidacion").attr('action'), this.ajaxgrupo, 'cambiarEstatus', 'formliquidacion');
            }
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


            },error:function(error){

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



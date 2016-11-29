<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Flujo de Trabajo</li>
    <li>Consolidado de Carga</li>
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
                        <option value="-1">Todos</option>
                        <option value="ABIERTO" selected>ABIERTO</option>
                        <option value="IMPRESO">IMPRESO</option>
                        <option value="CERRADO">CERRADO</option>
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
                    <label class="control-label badge b-default">ABIERTO</label>
                    <label class="control-label badge btn-other">IMPRESO</label>
                    <label class="control-label badge b-primary">CERRADO</label>
                </div>

            </div>
            <br>
        </form>

<div class="block">
    <!-- Progress btn-defaultrs Wizard Title -->
    <div class="row" id="loading" style="display: none;">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
    <div id="lstTabla" class="table-responsive"></div>




</div>

<script type="text/javascript">
    $(function () {
        $(".fa-print").mouseover(function () {
            $(this).next().css("display", "inline-block");
        });

        $(".fa-print").mouseout(function () {
            $(this).next().css("display", "none");
        });

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

            buscar();
        });

        buscar();
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
    });

    function buscar() {

        $("#lstTabla").html($("#loading").html());
        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'consolidadodecargas/lst_consolidado',
            success: function (data) {
                $("#lstTabla").html(data);
                $('#limpiar_f').prop('checked', false)

            },
            error: function(){
                $("#lstTabla").html('');
                $.bootstrapGrowl('<h4>Ha ocurrido un error en la opci&oacute;n</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });
            }
        });
    }

    function alertImprimir(id) {
        $("#imprimirconso").val(id);
        $("#modalimprimir").modal('show');
    }
    function impirmirGuiaConsolidado(id) {
        $("#modalimprimir").modal('hide');

        var win = window.open('<?= $ruta ?>consolidadodecargas/pdf/' + id, '_blank');
        win.focus();
        setTimeout(function () {
            grupo.ajaxgrupo().success(function (data) {


                $('#page-content').html(data);
            });
        }, 2000)


    }
    function VerConsolidado(id) {

        $("#consolidadoDocumento").html($('#loading').html());
        $("#consolidadoDocumento").load('<?= $ruta ?>consolidadodecargas/verDetalles/' + id);
        $('#consolidadoDocumento').modal('show');

    }
    function editarconsolidado(id, metros) {
        var id = id;
        var metros = metros;
        $.ajax({
            url: '<?= base_url()?>venta/get_ventas',
            data: {
                'listar': 'pedidos',
                'estatus': 'GENERADO',
                'id_consolidado': id

            },
            type: 'POST',
            success: function (data) {
                if (data.length > 0)
                    $("#ventamodalbody").html(data);

            },
            error: function () {

                alert('Ocurrio un error por favor intente nuevamente');
            }
        })
        $("#ventamodal").modal('show');
        $('#suma_metros_cubicos').val(metros);
        $('#añadircamion').val(id);
    }
    function agregarPedidos(id) {
        var id = id;
        var metros_c = $("#suma_metros_cubicos").val();
        if ($('input:checkbox').is(':checked')) {
            var pedidos = [];

            $("input:checkbox:checked").each(function () {
                pedidos.push($(this).val());
            });
            $.ajax({
                url: '<?php echo $ruta . 'venta/cargarCamion'; ?>',
                type: 'POST',
                data: {
                    'metros_c': metros_c,
                    'pedidos': pedidos,
                    'id_consolidado': id
                },
                success: function (data) {
                    $("#visualizarCamiones").html(data);
                    $("#visualizarCamiones").modal('show');
                }
            });
        }
        else {
            $("#seleccionarPedido").modal('show');
        }
    }
</script>

<div class="modal fade" id="consolidadoDocumento" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<script>$(function () {
        TablesDatatables.init();
    });
</script>
<!--MODALS PARA EDITAR EL CONSOLIDADO-->
<div class="modal fade" id="ventamodal" style="width: 85%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
            </button>

            <h3>Editar consolidado</h3>
        </div>


        <div class="modal-body" id="ventamodalbody">
            <div class="form-group row">

                <div class="col-md-2">
                    Metros cúbicos
                </div>
                <div class="col-md-3">
                    <input type="text" name="suma_metros_cubicos" id="suma_metros_cubicos" value="0" readonly="readonly"
                           class=" form-control">
                </div>


                <div class="col-md-3">
                    <button type="button" id="añadircamion" onclick="agregarPedidos(this.value);" value="0"
                            class="btn btn-info"><i
                            class="fa fa-truck"></i>
                        Asignar a camión
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
<!-- confirmar impresion de consolidado -->
<div class="modal fade" id="modalimprimir" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel">

    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
                </button>

                <h4>Imprimir consolidado</h4>
            </div>


            <div class="modal-body" id="ventamodalbody">
                <div class="form-group row">

                    <h4>¿Está seguro que desea imprimir el consolidado? </h4>

                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="imprimirconso" onclick="impirmirGuiaConsolidado(this.value);" value="0"
                        class="btn btn-primary" value="">
                        <li class="glyphicon glyphicon-thumbs-up"></li> Aceptar

                </button>
                <button type="button" data-dismiss="modal" value="0"
                        class="btn btn-warning">Cancelar
                        <li class="glyphicon glyphicon-thumbs-down"></li>
                </button>
            </div>
        </div>
    </div>

</div>
<!--MODALS PARA VISUALIZAR LA CARGA AL CAMION-->
<div class="modal fade" id="visualizarCamiones" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<div class="modal fade" id="seleccionarPedido" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Atención:</h4>
            </div>
            <div class="modal-body">
                <p>Debe elegir algún pedido.</p>
            </div>
            <div class="modal-footer">

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
<script type="text/javascript">
    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>consolidadodecargas'

            })
        },

        guardar: function () {
            if ($("#camion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar un camión</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            if ($("#fecha_consolidado").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar una feha de entrega</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            var metroscamion = parseFloat($("#metroscamion").val());
            var metrospedido = parseFloat($("#metros").val());
            if (metroscamion < metrospedido) {
                $("#confirmarCarga").modal('show');
                return false;

            }
            else {
                $("#ventamodal").modal('hide');
                App.formSubmitAjax($("#formcamion").attr('action'), this.ajaxgrupo, 'visualizarCamiones', 'formcamion');

            }
        },
        guardarconsolidado: function () {
            $("#confirmarCarga").modal('hide');
            $("#ventamodal").modal('hide');
            App.formSubmitAjax($("#formcamion").attr('action'), this.ajaxgrupo, 'visualizarCamiones', 'formcamion');

        }
    }

</script>
<div class="modal fade" id="confirmarCarga" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Desea confirmar</h4>
            </div>

            <div class="modal-body">
                <h4>El pedido excede la cantidad de metros cúbicos que soporta el camión.</h4>

                <h4>Presione "Confirmar" para guardar el pedido de todos modos o "Cancelar" para elegir otro camión.</h4>
            </div>
            <br><br><br>

            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardarconsolidado()">
                   <li class="glyphicon glyphicon-thumbs-up"></li> Confirmar
                </button>
                <button type="button" class="btn btn-warning" data-dismiss="modal"
                <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
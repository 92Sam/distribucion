<?php $ruta = base_url(); ?>
<style>
    .btn-group-sm > .btn, .btn-sm {
        font-size: 10px;
    }

    .b-warning {
        background-color: #f7be64;
        color: #fff;
    }

    .table td {
        font-weight: normal;
        font-size: 11px;
    }
</style>
<div style="float: right; color: #CDCDCD;"><b>Leyenda:<b>
            <label class="label label-danger">Cliente con Deuda</label>
            <label class="label b-warning"><i class="fa fa-edit"></i> Pedido con Precio Sugerido</label>
</div>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered table-condensed table-hover" id="tablaresultado">
        <thead>
        <tr>
            <th id="select_all" style="text-align: center; width: 10px !important;">
                <input type="checkbox" id="seleccionTodo" style="margin-left: 7px;"/>
            </th>
            <th style="text-align: center;">Documento</th>
            <th style="text-align: center;">Cliente</th>
            <th style="text-align: center;">Vendedor</th>
            <th style="text-align: center;">Fecha Pedido</th>

            <th style="text-align: center;">Estado</th>
            <th style="text-align: center;">Zona</th>
            <th style="text-align: center;">Pago</th>
            <th style="text-align: center;">Total</th>
            <th style="text-align: center;" class="acciones">Acciones</th>


        </tr>
        </thead>
        <tbody style="text-align: center;">

        <?php if (isset($productos_cons)):
            if (count($productos_cons) > 0):
                foreach ($productos_cons as $prod_con):
                    $venta_status = $prod_con->venta_status;
                    ?>
                    <tr>
                        <td>
                            <input type="checkbox" name="pedido" id="pedido"
                                   value="<?php echo $prod_con->venta_id ?>" onclick="sumarMetros();" checked>
                        </td>
                        <td>
                            <input type="text" style="display:none;" name="<?php echo $prod_con->venta_id ?>"
                                   id="<?php echo $prod_con->venta_id ?>"
                                   value="<?php echo $prod_con->total_metos_cubicos ?>">
                            <?= $prod_con->nombre_tipo_documento . "-" . $prod_con->documento_Numero ?></td>
                        <td>
                            <label class=" <?= (isset($venta->deudor)) ? 'label label-danger' : '' ?>">
                                <?= $prod_con->razon_social ?>
                            </label>
                        </td>
                        <td><?= $prod_con->nombre ?></td>
                        <td><?= date('d-m-Y', strtotime($prod_con->fecha)) ?></td>
                        <td><?= $venta_status; ?></td>
                        <td><?= $prod_con->zona_nombre ?></td>
                        <td><?= $prod_con->nombre_condiciones ?></td>
                        <td><?= $prod_con->total ?></td>
                        <td>
                            <?php if ($prod_con->venta_status == PEDIDO_ENTREGADO or $prod_con->venta_status == PEDIDO_ENVIADO): ?>
                                <a style="cursor:pointer;"
                                   onclick="cargaData_Impresion(<?php echo $prod_con->venta_id; ?>)"
                                   class='btn btn-sm btn-default tip' title="Ver Venta">
                                    <i class="fa fa-search"></i> Nota de entrega
                                </a>
                                <a style="cursor:pointer;"
                                   onclick="cargaData_DocumentoFiscal(<?php echo $prod_con->venta_id; ?>)"
                                   class='btn btn-sm btn-default tip' title="Ver Venta">
                                    <i class="fa fa-search"></i> Boleta/Factura
                                </a>
                            <?php endif; ?>


                            <?php if ($prod_con->venta_status == PEDIDO_GENERADO && empty($prod_con->confirmacion_usuario)): ?>

                                <div class="btn-group">
                                    <a onclick="precioSugerido(<?php echo $venta->venta_id; ?>)"
                                       class="btn btn-sm <?php echo (isset($venta->preciosugerido) and $venta->preciosugerido > 0) ? 'btn-warning' : 'btn-default' ?>"
                                       data-toggle='tooltip' data-original-title='Editar Pedido'
                                       title="Precio Sugerido"><i class="glyphicon glyphicon-edit"></i> </a>
                                </div>
                            <?php endif; ?>
                        </td>


                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        <?php endif; ?>
        <?php if (count($ventas) > 0):
            foreach ($ventas as $venta):
                $venta_id = $venta->venta_id;
                $venta_status = $venta->venta_status;
                ?>
                <tr>
                    <td align="center"
                        style="display: <?= ($venta->venta_status == PEDIDO_GENERADO) ? 'block' : 'none' ?>;">

                        <input type="checkbox" name="pedido" id="pedido" class="cargarPedido"
                               value="<?= $venta->venta_id ?>" onclick="sumarMetros();">
                        <input type="hidden" name="<?= $venta->venta_id ?>"
                               id="valor_<?= $venta->venta_id ?>"
                               value="<?= isset($venta->total_metos_cubicos) ? $venta->total_metos_cubicos : 0 ?>">
                    </td>
                    <td style="white-space: nowrap;"><?= "NE - " . $venta->documento_Numero ?></td>
                    <td>
                        <label class="<?= isset($venta->deudor) ? 'label label-danger' : '' ?>">
                            <?= $venta->representante ?>
                        </label>
                    </td>
                    <td><?= $venta->nombre ?></td>
                    <td><?= date('d-m-Y', strtotime($venta->fecha)) ?></td>
                    <td><?= $venta_status; ?></td>
                    <td><?= $venta->zona_nombre ?></td>
                    <td><?= $venta->nombre_condiciones ?></td>
                    <td class="text-right" style="white-space: nowrap;"><?= MONEDA . ' ' . $venta->total ?></td>
                    <td class="acciones">
                        <?php if ($venta->venta_status == PEDIDO_ENTREGADO or $venta->venta_status == PEDIDO_ENVIADO or $venta->venta_status == PEDIDO_DEVUELTO): ?>
                            <a style="cursor:pointer;" onclick="cargaData_Impresion(<?php echo $venta->venta_id; ?>)"
                               class='btn btn-sm btn-default tip' title="Ver Venta">
                                <i class="fa fa-search"></i> Nota de entrega
                            </a>
                            <a style="cursor:pointer;"
                               onclick="cargaData_DocumentoFiscal(<?php echo $venta->venta_id; ?>)"
                               class='btn btn-sm btn-default tip' title="Ver Venta">
                                <i class="fa fa-search"></i> Boleta/Factura
                            </a>
                        <?php endif; ?>
                        <?php if ($venta->venta_status == PEDIDO_GENERADO && ((empty($venta->confirmacion_usuario) && $venta->pagado != 0.00) || (floatval($venta->pagado) == 0))): ?>

                            <div class="btn-group">
                                <a onclick="precioSugerido(<?php echo $venta->venta_id; ?>)"
                                   class="btn btn-sm <?php echo (isset($venta->preciosugerido) and $venta->preciosugerido > 0) ? 'btn-warning' : 'btn-default' ?>"
                                   data-toggle='tooltip' data-original-title='Editar Pedido'
                                   title="Editar Pedido"><i class="glyphicon glyphicon-edit"></i> </a>
                            </div>

                        <?php endif; ?>

                        <?php if ($venta->venta_status == PEDIDO_GENERADO): ?>

                            <div class="btn-group">
                                <a onclick="anular(<?php echo $venta->venta_id; ?>)"
                                   class="btn btn-sm <?php echo (isset($venta->preciosugerido) and $venta->preciosugerido > 0) ? 'btn-danger' : 'btn-danger' ?>"
                                   data-toggle='tooltip' data-original-title='Anular Pedido'
                                   title="Anular"><i class="glyphicon glyphicon-trash"></i> </a>
                            </div>

                        <?php endif; ?>
                    </td>


                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
        </tbody>
    </table>
</div>

<a href="<?= $ruta; ?>venta/pdf/<?php if (isset($local)) echo $local;
else echo 0; ?>/<?php if (isset($fecha_desde)) echo $fecha_desde;
else echo 0; ?>
 /<?php if (isset($fecha_hasta)) echo $fecha_hasta;
else echo 0; ?> / <?php if (isset($estatus)) echo $estatus;
else echo 0; ?>/0"
   class="btn  btn-danger btn-lg" data-toggle="tooltip" title="Exportar a PDF"
   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta; ?>venta/excel/<?php if (isset($local)) echo $local;
else echo 0; ?>/<?php if (isset($fecha_desde)) echo $fecha_desde;
else echo 0; ?>
 /<?php if (isset($fecha_hasta)) echo $fecha_hasta;
else echo 0; ?> / <?php if (isset($estatus)) echo $estatus;
else echo 0; ?>/0"
   class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
   data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
<div class="modal fade" id="mvisualizarVenta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
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


<div class="modal fade" id="anular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" method="post" id="formeliminar" action="<?= $ruta ?>venta/anular_venta">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Anular Venta</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-2">
                            Motivo
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="motivo" id="motivo" required="true" class="form-control"
                            >
                            <input type="hidden" name="id" id="id" required="true" class="form-control"
                            >
                        </div>

                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="" class="btn btn-primary" onclick="anularfunction.guardar()">
                        <li class="glyphicon glyphicon-thumbs-up"></li>
                        Confirmar
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <li class="glyphicon glyphicon-thumbs-down"></li>
                        Cancelar
                    </button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>

</div>
<script type="text/javascript">


    $(function () {


        if ($("#estatus").val() == 'GENERADO') {
            $("#select_all").show();
        }
        else {
            $("#select_all").hide();
        }

        $("#seleccionTodo").change(function () {
            if ($("#seleccionTodo").is(':checked')) {
                $(".cargarPedido").prop('checked', true);
                sumarMetros();
            } else {
                $(".cargarPedido").prop('checked', false);
                sumarMetros();

            }

        });

        // TablesDatatables.init(0, 'tablaresultado');
        TablesDatatables.init(1, 'tablaresultado');

        $('.edit_estatus_pedido').editable('<?php echo $ruta; ?>api/pedidos/estatus', {
            indicator: '<img src="<?php echo $ruta; ?>recursos/editable/loading.gif">',
            data: "{'ANULADO':'ANULADO'}",
            type: 'select',
            submit: 'OK',
            style: "inherit",
            callback: function (value, settings) {
                console.log(value);
            }
        });

    });


    function generar() {
        var fecha_desde = $("#fecha_desde").val();
        var fecha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        var estatus = $("#estatus").val();
        $("#agregargrupo").load('<?= $ruta; ?>venta/pdf/' + locales + '/' + fecha_desde + '/' + fecha_hasta + '/' + estatus);
        // TablesDatatables.init();
    }

    function cargaData_Impresion(id_venta) {
        $.ajax({
            url: '<?php echo $ruta . 'venta/verVenta'; ?>',
            type: 'POST',
            data: "idventa=" + id_venta,
            success: function (data) {
                $("#mvisualizarVenta").html(data);
                $("#mvisualizarVenta").modal('show');
            }
        });
    }

    function cargaData_DocumentoFiscal(id_venta) {
        $.ajax({
            url: '<?php echo $ruta . 'venta/verDocumentoFisal'; ?>',
            type: 'POST',
            data: "idventa=" + id_venta,
            success: function (data) {
                $("#mvisualizarVenta").html(data);
                $("#mvisualizarVenta").modal('show');
            }
        });
    }
    function sumarMetros() {
        $('#suma_metros_cubicos').html('0');
        var suma = 0;
        console.log($(".cargarPedido:checked").length);
        $(".cargarPedido:checked").each(
            function () {
                var campo = $(this).val();

                if ($('#valor_' + campo).val() != "") {
                    $('#suma_metros_cubicos').html(suma);

                    suma += parseFloat($('#valor_' + campo).val());
                    console.log(suma);
                    $('#suma_metros_cubicos').html(suma);
                }
                else {
                    suma += 0;
                    $('#suma_metros_cubicos').html(suma);
                }


            }
        );
    }


    function anular(id) {

        $('#anular').modal('show');
        $("#id").attr('value', id);
    }

    var anularfunction = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>venta/consultar',
                data: {buscar: 'pedidos'}

            })
        },
        guardar: function () {
            if ($("#motivo").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar un motivo</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            App.formSubmitAjax($("#formeliminar").attr('action'), this.ajaxgrupo, 'anular', 'formeliminar');
        }
    }
    function precioSugerido(id) {


        $("#barloadermodal").modal({
            show: true,
            backdrop: 'static'
        });

        $("#ventamodalbody").html('');
        $.ajax({
            url: '<?php echo base_url()?>venta/editar_pedido',
            data: {'idventa': id},
            type: 'post',
            success: function (data) {

                $('#barloadermodal').modal('hide');
                $("#ventamodalbody").html(data);
                $("#ventamodal").modal('show');


            },
            error: function (error) {
                $('#barloadermodal').modal('hide');
                alert('Ha ocurrido un error');

            }
        })


    }


</script>
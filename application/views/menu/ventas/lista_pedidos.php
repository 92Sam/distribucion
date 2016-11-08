<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered table-condensed table-hover" id="tablaresultado">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tipo de Documento</th>
            <th>N&uacute;mero de Venta</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Fecha</th>

            <th>Estatus</th>
            <th>Zona</th>
            <th>Condici&oacute;n Pago</th>
            <th>Total</th>
            <th>Acciones</th>
            <th>Cargar Todos <input type="checkbox" id="seleccionTodo"/></th>


        </tr>
        </thead>
        <tbody>

        <?php if (isset($productos_cons)) {
            if (count($productos_cons) > 0) {
                foreach ($productos_cons as $prod_con) {
                    $venta_status = $prod_con->venta_status;

                    ?>
                    <tr>
                        <td><?= $prod_con->venta_id ?></td>
                        <td><?= $prod_con->nombre_tipo_documento ?></td>
                        <td><?= $prod_con->documento_Serie . "-" . $prod_con->documento_Numero ?></td>
                        <td><label class=" <?php
                            if (isset($venta->deudor)) {
                                echo 'label label-danger';
                            }
                            ?>">

                                <?= $prod_con->razon_social ?></label></td>
                        <td><?= $prod_con->nombre ?></td>
                        <td><?= date('d-m-Y H:i:s', strtotime($prod_con->fecha)) ?></td>

                        <td><?= $venta_status; ?></td>
                        <td><?= $prod_con->zona_nombre ?></td>
                        <td><?= $prod_con->nombre_condiciones ?></td>
                        <td><?= $prod_con->total ?></td>
                        <td>
                            <?php if ($prod_con->venta_status == PEDIDO_ENTREGADO or $prod_con->venta_status == PEDIDO_ENVIADO) { ?>
                                <a style="cursor:pointer;"
                                   onclick="cargaData_Impresion(<?php echo $prod_con->venta_id; ?>)"
                                   class='btn btn-default tip' title="Ver Venta">
                                    <i class="fa fa-search"></i> Nota de entrega
                                </a>
                                <a style="cursor:pointer;"
                                   onclick="cargaData_DocumentoFiscal(<?php echo $prod_con->venta_id; ?>)"
                                   class='btn btn-default tip' title="Ver Venta">
                                    <i class="fa fa-search"></i>Boleta/Factura
                                </a>
                            <?php } ?>


                            <?php  if ($prod_con->venta_status == PEDIDO_GENERADO && empty($prod_con->confirmacion_usuario)) { ?>

                                <div class="btn-group">
                                    <a onclick="precioSugerido(<?php echo $venta->venta_id; ?>)"
                                       class="btn <?php echo (isset($venta->preciosugerido) and $venta->preciosugerido > 0) ? 'btn-warning' : 'btn-default' ?>"
                                       data-toggle='tooltip' data-original-title='Editar Pedido'
                                       title="Precio Sugerido"><i class="fa fa-edit"></i> </a>
                                </div>

                            <?php } ?>


                        </td>

                        <td align="center">
                            <input type="checkbox" name="pedido" id="pedido"
                                   value="<?php echo $prod_con->venta_id ?>" onclick="sumarMetros();" checked>
                        </td>
                        <input type="text" style="display:none;" name="<?php echo $prod_con->venta_id ?>"
                               id="<?php echo $prod_con->venta_id ?>"
                               value="<?php echo $prod_con->total_metos_cubicos ?>">

                    </tr>
                <?php }
            }
        } ?>
        <?php if (count($ventas) > 0) {
            foreach ($ventas as $venta) {
                $venta_id = $venta->venta_id;
                $venta_status = $venta->venta_status;
                ?>
                <tr>
                    <td><?= $venta->venta_id ?></td>
                    <td><?= $venta->nombre_tipo_documento ?></td>
                    <td><?= $venta->documento_Serie . "-" . $venta->documento_Numero ?></td>
                    <td><label class=" <?php
                    if (isset($venta->deudor)) {
                        echo 'label label-danger';
                    }
                        ?>">

                            <?= $venta->razon_social ?></label></td>
                    <td><?= $venta->nombre ?></td>
                    <td><?= date('d-m-Y H:i:s', strtotime($venta->fecha)) ?></td>

                    <?php if ($venta_status == PEDIDO_GENERADO) { ?>
                        <td><a href="javascript:void(0)" class="edit_estatus_pedido"
                               id="<?php echo $venta_id; ?>"><?= $venta_status; ?></a></td>
                    <?php } else { ?>
                        <td><?= $venta_status; ?></td>
                    <?php } ?>
                    <td><?= $venta->zona_nombre ?></td>
                    <td><?= $venta->nombre_condiciones ?></td>
                    <td><?= $venta->total ?></td>
                    <td>
                        <?php if ($venta->venta_status == PEDIDO_ENTREGADO or $venta->venta_status == PEDIDO_ENVIADO or $venta->venta_status == PEDIDO_DEVUELTO) { ?>
                            <a style="cursor:pointer;" onclick="cargaData_Impresion(<?php echo $venta->venta_id; ?>)"
                               class='btn btn-default tip' title="Ver Venta">
                                <i class="fa fa-search"></i> Nota de entrega
                            </a>
                            <a style="cursor:pointer;"
                               onclick="cargaData_DocumentoFiscal(<?php echo $venta->venta_id; ?>)"
                               class='btn btn-default tip' title="Ver Venta">
                                <i class="fa fa-search"></i>Boleta/Factura
                            </a>
                        <?php } ?>
                        <?php  if ($venta->venta_status == PEDIDO_GENERADO  && (( empty($venta->confirmacion_usuario) && $venta->pagado!=0.00) || (floatval($venta->pagado)==0)) ) { ?>

                            <div class="btn-group">
                                <a onclick="precioSugerido(<?php echo $venta->venta_id; ?>)"
                                   class="btn <?php echo (isset($venta->preciosugerido) and $venta->preciosugerido > 0) ? 'btn-warning' : 'btn-default' ?>"
                                   data-toggle='tooltip' data-original-title='Editar Pedido'
                                   title="Precio Sugerido"><i class="fa fa-edit"></i> </a>
                            </div>

                        <?php } ?>

                        <?php  if ($venta->venta_status == PEDIDO_GENERADO ) { ?>

                            <div class="btn-group">
                                <a onclick="anular(<?php echo $venta->venta_id; ?>)"
                                   class="btn <?php echo (isset($venta->preciosugerido) and $venta->preciosugerido > 0) ? 'btn-warning' : 'btn-default' ?>"
                                   data-toggle='tooltip' data-original-title='Anular Pedido'
                                   title="Anular"><i class="fa fa-trash"></i> </a>
                            </div>

                        <?php } ?>
                    </td>

                    <td align="center">
                        <?php if ($venta->venta_status == PEDIDO_GENERADO){ ?>
                        <input type="checkbox" name="pedido" id="pedido" class="cargarPedido"
                               value="<?php echo $venta->venta_id ?>" onclick="sumarMetros();">
                        <input type="text" style="display:none;" name="<?php echo $venta->venta_id ?>"
                               id="valor_<?php echo $venta->venta_id ?>"
                               value="<?php echo isset($venta->total_metos_cubicos) ? $venta->total_metos_cubicos : 0 ?>">
                    </td>


                <?php } ?>
                </tr>
            <?php }
        } ?>
        </tbody>
    </table>
</div>

<a href="<?= $ruta; ?>venta/pdf/<?php if (isset($local)) echo $local; else echo 0; ?>/<?php if (isset($fecha_desde)) echo $fecha_desde; else echo 0; ?>
 /<?php if (isset($fecha_hasta)) echo $fecha_hasta; else echo 0; ?> / <?php if (isset($estatus)) echo $estatus; else echo 0; ?>/0"
   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta; ?>venta/excel/<?php if (isset($local)) echo $local; else echo 0; ?>/<?php if (isset($fecha_desde)) echo $fecha_desde; else echo 0; ?>
 /<?php if (isset($fecha_hasta)) echo $fecha_hasta; else echo 0; ?> / <?php if (isset($estatus)) echo $estatus; else echo 0; ?>/0"
   class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
   data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
<div class="modal fade" id="mvisualizarVenta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>


<div class="modal fade" id="ventamodal" style="width: 85%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
            </button>

            <h3>Editar Pedido</h3>
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
                    <button type="button" id="" class="btn btn-primary" onclick="anularfunction.guardar()" >Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </form>

</div>
<script type="text/javascript">


    $(function () {

        $("#seleccionTodo").change(function() {
            if($("#seleccionTodo").is(':checked')) {
                $(".cargarPedido").prop('checked', true);
                sumarMetros();
            } else {
                $(".cargarPedido").prop('checked', false);
                sumarMetros();

            }
        });

        TablesDatatables.init(0, 'tablaresultado');

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
            url: '<?php echo $ruta.'venta/verDocumentoFisal'; ?>',
            type: 'POST',
            data: "idventa=" + id_venta,
            success: function (data) {
                $("#mvisualizarVenta").html(data);
                $("#mvisualizarVenta").modal('show');
            }
        });
    }
    function sumarMetros() {
        $('#suma_metros_cubicos').val('0');
        var suma = 0;
        console.log($(".cargarPedido:checked").length);
        $(".cargarPedido:checked").each(
            function () {
                var campo = $(this).val();

                if ($('#valor_' + campo).val() != "") {
                    $('#suma_metros_cubicos').val(suma);

                    suma += parseFloat($('#valor_' + campo).val());
                    console.log(suma);
                    $('#suma_metros_cubicos').val(suma);
                }
                else {
                    suma += 0;
                    $('#suma_metros_cubicos').val(suma);
                }


            }
        );
    }


    function anular(id) {

        $('#anular').modal('show');
        $("#id").attr('value', id);
    }

    var anularfunction = {
        ajaxgrupo : function(){
            return  $.ajax({
                url:'<?= base_url()?>venta/consultar',
                data:{buscar:'pedidos'}

            })
        },
        guardar : function () {
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
            url: '<?php echo base_url()?>venta/pedidos',
            data: {'idventa': id, 'devolver': 0, 'preciosugerido': 1},
            type: 'post',
            success: function (data) {

                $('#barloadermodal').modal('hide');
                $("#ventamodalbody").html(data);
                $("#ventamodal").modal('show');


            },
            error:function(error){
                $('#barloadermodal').modal('hide');
                alert('Ha ocurrido un error');

            }
        })


    }



</script>
<?php $ruta = base_url(); ?>
<div class="modal-dialog modal-lg">
    <form name="formcerrarliquidacion" method="post" id="formcerrarliquidacion"
          action="<?= base_url() ?>consolidadodecargas/cerrarLiquidacion">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">N° de Liquidación de Guia de Carga =>
                    CNLD: <?= $id_consolidado ?>, Camion: <?= $consolidado_detalle->placa ?>,
                    Chofer: <?= $consolidado_detalle->chofer ?>
                </h4>
                <input type="hidden" value="<?= $id_consolidado ?>" id="con_id">
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped dataTable table-bordered" id="example">
                        <thead>
                        <tr>

                            <th style="text-align: center">Documento</th>
                            <th style="text-align: center">Bultos</th>
                            <th style="text-align: center">Cliente</th>
                            <th style="text-align: center">Importe Actual</th>
                            <th style="text-align: center">Estado</th>
                            <?php if ($status == 'IMPRESO'): ?>
                                <th style="text-align: center">Acciones</th>
                            <?php endif; ?>
                            <th style="text-align: center">Monto Cobrado</th>

                        </tr>
                        </thead>
                        <tbody>
                        <?php $cerrar_consolidado_flag = true; ?>
                        <?php $devolucion_flag = false; ?>
                        <?php $total_liquidado = 0; ?>
                        <?php $total_boleta = 0; ?>
                        <?php foreach ($consolidado as $consolidadoDetalles): ?>
                            <tr>
                                <?php if ($consolidadoDetalles['tipo_doc_fiscal'] == 'BOLETA DE VENTA') $total_boleta++; ?>
                                <td style="text-align: center"><?= 'NE - ' . $consolidadoDetalles['documento_Numero']; ?></td>
                                <td style="text-align: center"><?= number_format($consolidadoDetalles['bulto'], 0) ?></td>
                                <td style="text-align: center"><?= $consolidadoDetalles['razon_social']; ?></td>
                                <td style="text-align: right">
                                    <?= $consolidadoDetalles['venta_status'] == 'DEVUELTO PARCIALMENTE' ? MONEDA . ' ' . number_format($consolidadoDetalles['total'], 2) : '' ?>
                                    <?= $consolidadoDetalles['venta_status'] == 'ENTREGADO' || $consolidadoDetalles['venta_status'] == 'ENVIADO' ? MONEDA . ' ' . number_format($consolidadoDetalles['historico_total'], 2) : '' ?>
                                    <?= $consolidadoDetalles['venta_status'] == 'RECHAZADO' ? MONEDA . ' ' . number_format(0, 2) : '' ?>
                                </td>
                                <td style="text-align: center"><?= $consolidadoDetalles['venta_status'] ?></td>
                                <?php if ($consolidadoDetalles['venta_status'] == 'DEVUELTO PARCIALMENTE') $devolucion_flag = true; ?>
                                <?php if ($consolidadoDetalles['venta_status'] == 'RECHAZADO') $devolucion_flag = true; ?>
                                <?php if ($consolidadoDetalles['venta_status'] == 'ENVIADO') $cerrar_consolidado_flag = false; ?>
                                <?php if ($status == 'IMPRESO'): ?>
                                    <td style="text-align: center">
                                        <button type="button" id="liquidar_p"
                                                data-pedido_id="<?= $consolidadoDetalles['venta_id']; ?>"
                                                class="btn btn-sm btn-<?= $consolidadoDetalles['venta_status'] == 'ENVIADO' ? 'default' : 'warning' ?> liquidar_pedido">
                                            <i
                                                    class="fa fa-refresh"></i>
                                            <?= $consolidadoDetalles['venta_status'] == 'ENVIADO' ? 'Liquidar' : 'Cambiar' ?>
                                        </button>
                                    </td>
                                <?php endif; ?>
                                <?php $total_liquidado += $consolidadoDetalles['montocobradoliquidacion']; ?>
                                <td style="text-align: right;"><?= MONEDA . ' ' . number_format($consolidadoDetalles['montocobradoliquidacion'], 2) ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <br>
                    <h3 style="font-weight:bold;">
                        <label class="control-label badge b-warning"> Monto total cobrado:
                            <span
                                    style="font-weight:bold;"><?= isset($total_liquidado) ? MONEDA . ' ' . number_format($total_liquidado, 2) : 'S/. 0.00'; ?></span>
                        </label>
                    </h3>

                    <input type="hidden" value="<?= $id_consolidado ?>" name="id">


                    <div class="modal-footer" id="">

                        <div class="btn-group">
                            <a class="btn btn-sm btn-primary" data-toggle="tooltip"
                               title="Ver Nota" data-original-title="Ver"
                               href="#"
                               onclick="notaEntrega('<?= $id_consolidado ?>'); ">
                                <span>Notas de Entrega</span>
                            </a>
                        </div>





                            <?php if ($cerrar_consolidado_flag && $status == 'IMPRESO'): ?>
                        <div class="btn-group">
                                <button type="button" id="" class="btn btn-sm btn-primary"
                                        onclick="grupo.cerrarLiquidacion()">
                                    <li class="glyphicon glyphicon-thumbs-up"></li>
                                    Cerrar Liquidación
                                </button>
                        </div>
                            <?php endif; ?>

                            <?php if ($status == 'CONFIRMADO' || $status == 'CERRADO'): ?>
                                <?php if ($devolucion_flag): ?>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info"
                                                onclick="pedidoDevolucion(<?php echo $id_consolidado ?>);">
                                            <i class="fa fa-print"></i> Devoluciones
                                        </button>
                                    </div>

                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info"
                                                onclick="imprimir_notas(<?php echo $id_consolidado ?>);">
                                            <i class="fa fa-print"></i> Notas de Credito
                                        </button>
                                    </div>
                                <?php endif; ?>
                                <?php if ($total_liquidado > 0): ?>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info"
                                                onclick="pedidoPreCancelacion(<?php echo $id_consolidado ?>);">
                                            <i class="fa fa-print"></i> Pre-Cancelación
                                        </button>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <div class="btn-group">
                            <button type="button" class="btn btn-sm btn-danger" data-dismiss="modal">
                                <li class="glyphicon glyphicon-thumbs-down"></li>
                                Salir
                            </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </form>


    <script type="text/javascript">
        function pedidoDevolucion(id) {


            var win = window.open('<?= $ruta ?>consolidadodecargas/pedidoDevolucion/' + id, '_blank');
            win.focus();

            grupo.ajaxgrupo().success(function (data) {

            });


        }

        function notaEntrega(id, venta_id) {
            var venta = 0;
            if (venta_id != undefined)
                venta = venta_id;
            {
                $.ajax({
                    url: '<?php echo $ruta . 'consolidadodecargas/notaEntrega'; ?>',
                    type: 'POST',
                    data: {"id": id, 'venta_id': venta},
                    success: function (data) {
                        $("#noteDeEntrega").html(data);
                        $("#noteDeEntrega").modal('show');
                    }
                });
            }

        }

        function impirmirGuiaBoleta(id) {

            var win = window.open('<?= $ruta ?>consolidadodecargas/rtfRemisionBoleta/' + id, '_blank');
            win.focus();

        }

        function docFiscal(id) {


            $.ajax({
                url: '<?php echo $ruta . 'consolidadodecargas/docFiscalBoleta'; ?>',
                type: 'POST',
                data: "id=" + id,
                success: function (data) {
                    $("#noteDeEntrega").html(data);
                    $("#noteDeEntrega").modal('show');
                }
            });


        }

        function imprimir_notas(id) {

            var win = window.open('<?= $ruta ?>consolidadodecargas/imprimir_notas/' + id, '_blank');
            win.focus();

        }

        function pedidoPreCancelacion(id) {

            var win = window.open('<?= $ruta ?>consolidadodecargas/pedidoPreCancelacion/' + id, '_blank');
            win.focus();

            grupo.ajaxgrupo().success(function (data) {

            });


        }
    </script>
    <script type="text/javascript">
        $(document).ready(function () {

            $(".liquidar_pedido").on('click', function () {

                var consolidado_id = $("#con_id").val();
                var pedido_id = $(this).attr('data-pedido_id');

                $.ajax({
                    url: '<?= base_url()?>consolidadodecargas/get_pedido' + '/' + pedido_id,
                    type: 'POST',
                    headers: {
                        Accept: 'application/json'
                    },
                    success: function (data) {

                        $("#id_pedido_liquidacion").val(pedido_id);
                        $("#consolidado_id").val(consolidado_id);

                        $("#total").val(formatPrice(data.pedido.historico_total));

                        $("#estatus_value_entregado").val(formatPrice(data.pedido.historico_total));
                        $("#estatus_value_devuelto").val(formatPrice(data.pedido.total));
                        $("#estatus_value_rechazado").val('0.00');

                        if (data.pedido.venta_status == 'ENVIADO') {
                            $("#estatus").val('ENTREGADO').trigger('chosen:updated');
                        }
                        else
                            $("#estatus").val(data.pedido.venta_status).trigger('chosen:updated');


                        if (data.pedido.venta_status != 'DEVUELTO PARCIALMENTE')
                            $(".devolver_block").hide();
                        else {
                            $("#total").val(formatPrice(data.pedido.total));
                            $(".devolver_block").show();
                        }

                        if (data.pedido.venta_status != 'RECHAZADO') {
                            $(".pago_block").show();
                            $(".motivo_block").hide();
                        }
                        else {
                            $("#total").val('0.00');
                            $(".pago_block").hide();
                            $(".motivo_block").show();
                        }


                        $("#pago_id").val('3').trigger('chosen:updated');
                        $("#banco_block").hide();
                        $("#cobrar_todo").prop('checked', false);

                        $("#num_oper").val('');
                        $("#pedido_numero").html(pedido_id);
                        $("#monto").val(0);


                        $("#cambiarEstatus").modal('show');
                    },
                    error: function () {
                        $.bootstrapGrowl('<h4>Ha ocurrido un error en la opci&oacute;n</h4>', {
                            type: 'warning',
                            delay: 2500,
                            allow_dismiss: true
                        });
                    }
                })

            });

            $("#myBotons").on("click", function () {
                bootbox.confirm("Confirmar cierre de liquidación", function (result) {
                    if (result == true) {
                        grupo.cerrarLiquidacion();
                    }
                });
            });

        });
    </script>

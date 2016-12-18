<?php $ruta = base_url(); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Consolidado de Documento</h4>

        </div>

        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped dataTable table-bordered" id="example">
                    <thead>

                    <tr class="#alineacion">

                        <th style="text-align: center">ID</th>
                        <th style="text-align: center">Documento</th>
                        <th style="text-align: center">Cantidad Bultos</th>
                        <th style="text-align: center">Cliente</th>
                        <th style="text-align: center">Importe Pedido</th>
                        <th style="text-align: center">Estado</th>
                        <th style="text-align: center">Acciones</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $total_bultos = 0;
                    $total_importe = 0;
                    foreach ($consolidadoDetalles as $detalle) { ?>
                        <tr>

                            <td style="text-align: center"><?php echo $detalle['venta_id']; ?></td>
                            <td style="text-align: center"><?php echo 'NE - ' . $detalle['documento_Numero']; ?></td>
                            <td style="text-align: center"><?php echo number_format($detalle['bulto'], 0);
                                $total_bultos += $detalle['bulto']; ?>
                            </td>
                            <td style="text-align: center"><?php echo $detalle['representante']; ?></td>

                            <td style="text-align: right;"><?php echo MONEDA . ' ' . number_format($detalle['total'], 2);
                                $total_importe += $detalle['total']; ?>
                            </td>
                            <td style="text-align: center"><?php echo $detalle['venta_status']; ?> </td>
                            <td style="text-align: center">
                                <a class="btn btn-sm btn-primary" data-toggle="tooltip"
                                   title="Imprimir" data-original-title="Imprimir"
                                   href="#"
                                   onclick="notaEntrega('<?= isset($consolidado['consolidado_id']) ? $consolidado['consolidado_id'] : 0 ?>', '<?php echo $detalle['venta_id']; ?>'); ">
                                    <i class="fa fa-print fa-fw" id="ic"></i></a>
                                    <?php if ($consolidado['status'] == 'ABIERTO'): ?>
                                    <a class="btn btn-sm btn-warning" data-toggle="tooltip"
                                       style="margin-left: 5px;"
                                       title="Eliminar del Consolidado" data-original-title="Eliminar del Consolidado"
                                       href="#"
                                       onclick="eliminar_pedido('<?= $consolidado['consolidado_id'] ?>', '<?php echo $detalle['venta_id']; ?>'); ">
                                        <i class="fa fa-remove" id="ic"></i></a>
                                        <?php endif; ?>
                            </td>

                        </tr>
                        <?php
                    } ?>

                    <tr style="background-color: #C6EFCE">
                        <td></td>
                        <td style="text-align: right; font-size: 13px"><?php echo '<strong style="fond-family:bold;">Total Bultos => <strong>'; ?></td>
                        <td style="text-align: center; font-weight: bold; font-size: 13px"><?php echo number_format($total_bultos, 0) ?></td>
                        <td style="text-align: right; font-size: 13px"><?php echo '<strong style="fond-family:bold;">Total Importe Pedido => <strong>'; ?></td>
                        <td style="text-align: right; font-weight: bold; font-size: 13px"><?php echo MONEDA . ' ' . number_format($total_importe, 2) ?></td>
                        <td></td>
                        <td></td>


                    </tr>
                    </tbody>
                </table>
            </div>
            <br>


            <div class="btn-group">
                <a class="btn btn-sm btn-default" data-toggle="tooltip"
                   title="Ver" data-original-title="Ver"
                   href="#"
                   onclick="impirmirGuia('<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>'); ">
                    <span>Guia de remision</span>
                </a>
            </div>
            <!-- nuevos btns-->

            <?php if ($consolidado['status'] == 'IMPRESO' || $consolidado['status'] == 'CONFIRMADO' || $consolidado['status'] == 'CERRADO') { ?>
                <div class="btn-group">
                    <a class="btn btn-sm btn-warning" data-toggle="tooltip"
                       title="Ver Nota" data-original-title="Ver"
                       href="#"
                       onclick="notaEntrega('<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>'); ">
                        <span>Notas de Entrega</span>
                    </a>
                </div>
                <div class="btn-group">
                    <a class="btn btn-sm btn-default" data-toggle="tooltip"
                       title="Facturas" data-original-title="Ver"
                       href="#"
                       onclick="docFiscalFact('<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>'); ">
                        <span>Facturas</span>
                    </a>
                </div>

                <div class="btn-group">
                    <a class="btn btn-sm btn-warning" data-toggle="tooltip"
                       title="Boletas" data-original-title="Ver"
                       href="#"
                       onclick="docFiscal('<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>'); ">
                        <span>Boletas de ventas</span>
                    </a>
                </div>
            <?php } ?>

        </div>
    </div>
</div>
<div class="modal fade" id="noteDeEntrega" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>
<script type="text/javascript">
    function impirmirGuia(id) {

        var win = window.open('<?= $ruta ?>consolidadodecargas/rtfRemision/' + id, '_blank');
        win.focus();

    }

    function notaEntrega(id, venta_id) {
        var venta = 0;
        if (venta_id != undefined)
            venta = venta_id
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

    

    function eliminar_pedido(id, venta_id) {

        var venta = 0;
        if (venta_id != undefined)
            venta = venta_id;
        {
            $.ajax({
                url: '<?php echo $ruta . 'consolidadodecargas/eliminar_pedido_consolidado'; ?>',
                type: 'POST',
                data: {"id": id, 'venta_id': venta},
                headers: {
                    Accept: 'application/json'
                },
                success: function (data) {
                    if (data.result == 1) {
                        $('#consolidadoDocumento').modal('hide');
                        $("#btn_buscar").click();
                    }
                    else if (data.result == 2) {
                        $("#consolidadoDocumento").html($('#loading').html());
                        $("#consolidadoDocumento").load('<?= $ruta ?>consolidadodecargas/verDetalles/' + id);
                    }
                }
            });
        }

    }

    function detalleVenta(venta_id) {
        {
            $.ajax({
                url: '<?php echo $ruta . 'consolidadodecargas/notaEntrega/detalle'; ?>',
                type: 'POST',
                data: "venta_id=" + venta_id,
                success: function (data) {
                    $("#noteDeEntrega").html(data);
                    $("#noteDeEntrega").modal('show');
                }
            });
        }

    }

    function docFiscal(id) {

        {
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

    }
    function docFiscalFact(id) {

        {
            $.ajax({
                url: '<?php echo $ruta . 'consolidadodecargas/docFiscalFactura'; ?>',
                type: 'POST',
                data: "id=" + id,
                success: function (data) {
                    $("#noteDeEntrega").html(data);
                    $("#noteDeEntrega").modal('show');
                }
            });
        }

    }
</script>


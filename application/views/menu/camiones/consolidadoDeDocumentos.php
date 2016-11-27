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
                    <tr>

                        <th>ID</th>
                        <th>Tipo de documento</th>
                        <th>Serie</th>
                        <th>Numero</th>
                        <th>Cantidad productos</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Acciones</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($consolidadoDetalles as $detalle) { ?>
                        <tr>

                            <td><?php echo $detalle['venta_id']; ?></td>
                            <td><?php echo $detalle['nombre_tipo_documento']; ?></td>
                            <td><?php echo $detalle['documento_Serie']; ?></td>
                            <td><?php echo $detalle['documento_Numero']; ?></td>
                            <td><?php echo $detalle['cantidad_prductos']; ?></td>
                            <td><?php echo $detalle['total']; ?></td>
                            <td><?php echo $detalle['venta_status']; ?> </td>
                            <td>
                                <a class="btn btn-default" data-toggle="tooltip"
                                   title="Imprimir" data-original-title="Imprimir"
                                   href="#"
                                   onclick="notaEntrega('<?= isset($consolidado['consolidado_id']) ? $consolidado['consolidado_id'] : 0 ?>', '<?php echo $detalle['venta_id']; ?>'); ">
                                    <i class="fa fa-print fa-fw" id="ic"></i>
                            </td>

                        </tr>
                        <?php
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <br>
            <a href="<?= $ruta ?>consolidadodecargas/excelModal/<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>"
               class="btn btn-default btn-lg" data-toggle="tooltip"
               title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
                    class="fa fa-file-excel-o fa-fw"></i></a>
            <a href="<?= $ruta ?>consolidadodecargas/pdfModal/<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>"
               class="btn btn-default btn-lg" data-toggle="tooltip"
               title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i
                    class="fa fa-file-pdf-o fa-fw"></i></a>

            <div class="btn-group">
                <a class="btn btn-default" data-toggle="tooltip"
                   title="Ver" data-original-title="Ver"
                   href="#"
                   onclick="impirmirGuia('<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>'); ">
                    <span>Guia de remision</span>
                </a>
            </div>
            <!-- nuevos btns-->

            <?php if ($consolidado['status'] == 'IMPRESO' || $consolidado['status'] == 'CONFIRMADO' || $consolidado['status'] == 'CERRADO') { ?>
                <div class="btn-group">
                    <a class="btn btn-default" data-toggle="tooltip"
                       title="Ver Nota" data-original-title="Ver"
                       href="#"
                       onclick="notaEntrega('<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>'); ">
                        <span>Notas de Entrega</span>
                    </a>
                </div>
                <div class="btn-group">
                    <a class="btn btn-default" data-toggle="tooltip"
                       title="Facturas" data-original-title="Ver"
                       href="#"
                       onclick="docFiscalFact('<?php if (isset($consolidado['consolidado_id'])) echo $consolidado['consolidado_id']; ?>'); ">
                        <span>Facturas</span>
                    </a>
                </div>

                <div class="btn-group">
                    <a class="btn btn-default" data-toggle="tooltip"
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


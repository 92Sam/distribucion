<?php $ruta = base_url(); ?>
<style>
    #tblresult th {
        font-size: 11px !important;
        padding: 2px 2px;
        text-align: center;
        vertical-align: middle;
    }

    #tblresult td {
        font-size: 10px !important;
    }
</style>
<form name="formagregar" action="<?php echo $ruta; ?>inventario/guardar" method="post">
    <input id="maximahidden" type="hidden">

    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    Movimiento de Inventario
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4"><label class="control-label">Descripcion:</label> <?= $producto->nombre ?>
                    </div>
                    <div class="col-md-4"><label class="control-label">Unidad de
                            Medida:</label> <?= $producto->um_nombre ?></div>
                    <div class="col-md-4"><label class="control-label">Periodo:</label> <?= $periodo ?></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed" id="tblresult">
                        <thead>
                        <tr>
                            <th rowspan="2">Fecha</th>
                            <th rowspan="2">Tipo</th>
                            <th rowspan="2">Serie</th>
                            <th rowspan="2">N&uacute;mero</th>
                            <th rowspan="2">Referencia</th>
                            <th rowspan="2">UM</th>
                            <th rowspan="2">Tipo de Operación</th>
                            <th colspan="3" style="text-align: center;">Entradas</th>
                            <th colspan="3" style="text-align: center;">Salidas</th>
                            <th colspan="3" style="text-align: center;">Saldo Final</th>
                        </tr>
                        <tr>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Costo Total</th>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Costo Total</th>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Costo Final</th>
                        </tr>
                        </thead>
                        <tbody id="columnas">
                        <tr>
                            <td></td>
                            <td>OTROS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?= $kardex['inicial'] != NULL ? $kardex['inicial']->nombre_unidad : $kardex['fiscal'][0]->nombre_unidad ?></td>
                            <td>SALDO ANTERIOR</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td><?= $kardex['inicial'] != NULL ? $kardex['inicial']->cantidad_final : 0 ?></td>
                            <td style="white-space: nowrap;"><?= $kardex['inicial'] != NULL ? MONEDA . ' ' . number_format($kardex['inicial']->costo_unitario_final, 2) : MONEDA . ' 0.00' ?></td>
                            <td style="white-space: nowrap;"><?= $kardex['inicial'] != NULL ? MONEDA . ' ' . number_format($kardex['inicial']->total_final, 2) : MONEDA . ' 0.00' ?></td>
                        </tr>
                        <?php if (count($kardex['fiscal']) > 0): ?>
                            <?php foreach ($kardex['fiscal'] as $detalle): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                                    <?php $tipo_doc = get_tipo_doc($detalle->tipo_doc) ?>
                                    <?php if (($detalle->tipo_doc == 3 || $detalle->tipo_doc == 1) && $tipo_kardex == 'INTERNO') $tipo_doc['value'] = 'Nota de Entrega'; ?>
                                    <td><?= $tipo_doc['value'] ?></td>
                                    <td><?= $detalle->serie ?></td>
                                    <td><?= $detalle->numero ?></td>
                                    <td><?= $detalle->referencia ?></td>
                                    <td><?= $detalle->nombre_unidad ?></td>
                                    <?php $tipo_operacion = get_tipo_operacion($detalle->tipo_operacion) ?>
                                    <td><?= $tipo_operacion['value'] ?></td>
                                    <?php if ($detalle->IO == 1): ?>
                                        <td><?= $detalle->cantidad ?></td>
                                        <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario, 2) ?></td>
                                        <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total, 2) ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <?php endif; ?>
                                    <?php if ($detalle->IO == 2): ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= $detalle->cantidad ?></td>
                                        <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario, 2) ?></td>
                                        <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total, 2) ?></td>
                                    <?php endif; ?>
                                    <td><?= $detalle->cantidad_final ?></td>
                                    <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario_final, 2) ?></td>
                                    <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total_final, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <a href="#" id="exportar_pdf" class="btn  btn-danger btn-lg"
                   data-toggle="tooltip" title="Exportar a PDF"
                   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
                <a href="#" id="exportar_excel" class="btn btn-default btn-lg" data-toggle="tooltip"
                   title="Exportar a Excel"
                   data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Salir</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

        <input type="hidden" value="<?= $tipo_kardex ?>" id="tipo_kardex">
        <input type="hidden" value="<?= $producto_id ?>" id="producto_id">

</form>

<script type="text/javascript">

    $(function () {

        $("#exportar_excel").on('click', function () {
            exportar_excel();
        });

        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });

    });

    function exportar_excel() {
        var id = $('#producto_id').val();
        var local = $("#locales").val();
        var year = $("#year").val();
        var mes = $("#mes").val();

        if ($('#tipo_kardex').val() == 'FISCAL')
            var win = window.open('<?= $ruta ?>inventario/kardex_excel/' + id + '/' + local + '/' + mes + '/' + year);
        else
            var win = window.open('<?= $ruta ?>inventario/kardex_interno_excel/' + id + '/' + local + '/' + mes + '/' + year);

        win.focus();
    }

    function exportar_pdf() {
        var id = $('#producto_id').val();
        var local = $("#locales").val();
        var year = $("#year").val();
        var mes = $("#mes").val();

        var win = window.open('<?= $ruta ?>inventario/kardex_pdf/' + id + '/' + local + '/' + mes + '/' + year);
        win.focus();
    }
</script>

<?php $ruta = base_url(); ?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Documentos Asociados</h4>
        </div>
        <div class="modal-body">
            <h4><?= $venta->razon_social ?></h4>
            <div class="table-responsive">
                <table class="table datatable datatables_filter table-striped" id="tabledetail">

                    <thead>
                    <tr>

                        <th>Fecha</th>
                        <th>Documento</th>
                        <th>No. Documento</th>
                        <th>Importe</th>
                        <th>Nota entrega</th>
                        <th>Nota Credito</th>


                    </tr>
                    </thead>
                    <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($detalles as $detalle): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($venta->fecha)) ?></td>
                            <td><?= $detalle->documento ?></td>
                            <td><?= $detalle->documento_numero ?></td>
                            <?php $total += $detalle->importe ?>
                            <td><?= $detalle->importe ?></td>
                            <td><?= "NE " . $venta->serie . "-" . $venta->numero ?></td>
                            <td><?= $detalle->nota_credito != '' ? $detalle->nota_credito : 'No aplica' ?></td>
                        </tr>
                    <?php endforeach; ?>

                    </tbody>
                </table>


            </div>
            <div class="row">
                <div class="col-md-6"><h4>Consolidado: <?= $venta->consolidado_id ?></h4></div>
                <div class="col-md-6" style="text-align: right;"><h4>
                        Total: <?= MONEDA ?> <?= number_format($total, 2) ?></h4></div>
            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

        </div>
    </div>
    <!-- /.modal-content -->
</div>


<script>

</script>

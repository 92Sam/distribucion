<?php $ruta = base_url(); ?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalles de Pagos</h4>
        </div>
        <div class="modal-body">

            <div class="table-responsive">
                <table class="table table-striped dataTable table-bordered" id="tabledetail">

                    <thead>
                    <tr>
                        <th>No</th>
                        <th>Fecha</th>
                        <th>Monto Pagado</th>
                        <th>Metodo de Pago</th>
                        <th>Banco</th>
                        <th>Operacion</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $n = 1; ?>
                    <?php foreach ($pago_detalles as $detalle): ?>
                        <tr>
                            <td><?= $n++ ?></td>
                            <td><?= date('d/m/Y', strtotime($detalle->pagoingreso_fecha)) ?></td>
                            <td><?= $detalle->pagoingreso_monto ?></td>
                            <td><?= $detalle->nombre_metodo ?></td>
                            <td><?= $detalle->banco_nombre != NULL ? $detalle->banco_nombre : '-' ?></td>
                            <td><?= $detalle->operacion != NULL ? $detalle->operacion : '-' ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>


            </div>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>

        </div>
    </div>
    <!-- /.modal-content -->
</div>


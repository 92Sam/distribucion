<div class="modal-dialog" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Liquidar Pagos de <?= $venta->vendedor_nombre ?></h4>
        </div>
        <div class="modal-body">


            <div class="row">
                <div class="col-md-5">
                    <h4>Cobros Realizados Pendientes</h4>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>DOC</th>
                            <th>Medio de Pago</th>
                            <th>Monto</th>
                            <th>Acci&oacute;n</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $total_efectivo = 0;?>
                        <?php foreach ($pagos->pendientes as $pago): ?>

                        <?php $total_efectivo += $pago->monto;?>
                        <?php endforeach; ?>
                            <tr style="font-weight: bold;">
                                <td colspan="2">TOTAL EFECTIVO</td>
                                <td><?= MONEDA . ' ' . number_format($total_efectivo, 2) ?></td>
                                <td>
                                    <div class="btn-group">
                                        <a class="btn btn-sm btn-primary tip" title="Liquidar">
                                            <i class="fa fa-arrow-right"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php foreach ($pagos->pendientes as $pago): ?>
                            <tr>
                                <td><?= $pago->documento ?></td>
                                <td><?= $pago->pago_nombre ?></td>
                                <td><?= MONEDA . ' ' . number_format($pago->monto, 2) ?></td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <h4>Liquidar Cobranzas</h4>
                    <div class="row">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Medio de Pago</th>
                                <th>Destino</th>
                                <th>Operaci&oacute;n</th>
                                <th>Monto</th>
                                <th>Acci&oacute;n</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($pagos->espera as $pago): ?>
                                <tr>
                                    <td><?= $pago->pago_nombre ?></td>
                                    <td><?= $pago->pago_id == 4 ? 'BANCO: ' . $pago->banco_nombre : 'CAJA' ?></td>
                                    <td><?= $pago->num_oper ?></td>
                                    <td><?= MONEDA . ' ' . number_format($pago->monto, 2) ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <a class="btn btn-sm btn-danger tip" title="Eliminar">
                                                <i class="fa fa-remove"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        <div class="modal-footer">
            <a href="#" class="btn btn-warning" data-dismiss="modal">Cerrar</a>
        </div>
    </div>
</div>
</div>

<script type="text/javascript">

    $(document).ready(function () {


    });


</script>
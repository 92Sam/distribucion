<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalles de Pago</h4>
        </div>
        <div class="modal-body">

            <div class="row">
                <div class="form-group">
                    <div class="col-md-4">
                        <label>DOCUMENTO</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" id="doc_venta" name="doc_venta"
                               class="form-control"
                               data-pedido_id="<?= $venta->venta_id ?>"
                               value="<?= $venta->documento_nombre == 'NOTA DE ENTREGA' ? 'NE' : $venta->documento_nombre ?> - <?= $venta->documento_numero ?>"
                               readonly>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <div class="col-md-4">
                        <label>Venta Total</label>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <div class="input-group-addon"><?= MONEDA ?></div>
                            <input type="text" id="venta_total" name="venta_total"
                                   class="form-control"
                                   value="<?= formatPrice($venta->total_deuda) ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <div class="col-md-4">
                        <label>Saldo</label>
                    </div>
                    <div class="col-md-8">
                        <div class="input-group">
                            <div class="input-group-addon"><?= MONEDA ?></div>
                            <input type="text" id="saldo" name="saldo"
                                   class="form-control"
                                   value="<?= formatPrice($venta->saldo) ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <h4>Pagos Realizados</h4>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Medio de Pago</th>
                    <th>Destino</th>
                    <th>N&uacute;mero de Operaci&oacute;n</th>
                    <th>Monto</th>
                    <th>Estado</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pagos->detalles as $pago): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($pago->fecha)) ?></td>
                        <td><?= $pago->pago_nombre ?></td>
                        <td><?= $pago->pago_id == 4 ? 'BANCO: ' . $pago->banco_nombre : 'CAJA' ?></td>
                        <td><?= $pago->num_oper ?></td>
                        <td><?= MONEDA . ' ' . formatPrice($pago->monto) ?></td>
                        <td>
                            <label style="margin-bottom: 0px;"
                                   class="control-label badge <?= $pago->estado == 'PENDIENTE' ? 'b-danger' : 'b-default' ?>">
                                <?= $pago->estado ?>
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>


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
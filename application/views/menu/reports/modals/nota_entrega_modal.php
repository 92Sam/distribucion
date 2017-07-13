<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                    aria-hidden="true">&times;
            </button>
            <h4 class="modal-title">Detalles de la Nota de Entrega</h4>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-4">NE <?= $venta->serie . '-' . $venta->numero ?></div>
                <div class="col-md-4"></div>
                <div class="col-md-4"></div>
            </div>
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>Codigo</th>
                    <th>Descrpcion</th>
                    <th>Presentacion</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Importe</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($venta->detalles as $detalle): ?>
                    <tr>
                        <td><?= sumCod($detalle->codigo, 4) ?></td>
                        <td><?= $detalle->nombre . ($detalle->bono == 1 ? ' -- BONO' : '') ?></td>
                        <td><?= $detalle->presentacion ?></td>
                        <td><?= $detalle->cantidad ?></td>
                        <td><?= $detalle->precio ?></td>
                        <td><?= $detalle->importe ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-danger" data-dismiss="modal">Cerrar</a>
        </div>
    </div>
</div>







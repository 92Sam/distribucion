<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="$('#dialog_form').modal('hide');"
                    aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalle del dia de la Cuenta <?= $cuenta->descripcion ?></h4>
        </div>
        <div class="modal-body">

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Responsable</th>
                    <th>Movimiento</th>
                    <th>Operacion</th>
                    <th>Pago</th>
                    <th>Saldo</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($cuenta_movimientos as $mov): ?>
                    <tr>
                        <td><?= $mov->id ?></td>
                        <td><?= $mov->created_at ?></td>
                        <td><?= $mov->usuario_nombre ?></td>
                        <td><?= $mov->movimiento ?></td>
                        <td><?= $mov->operacion ?></td>
                        <td><?= $mov->medio_pago ?></td>
                        <td><?= $mov->moneda_id == 1 ? MONEDA : DOLAR ?> <?= number_format($mov->saldo, 2) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-warning" onclick="$('#dialog_form').modal('hide');">Cerrar</a>
        </div>
    </div>

    <script>

        $(document).ready(function () {


        });
    </script>





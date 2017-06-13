<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="$('#dialog_form').modal('hide');"
                    aria-hidden="true">&times;
            </button>
            <h4 class="modal-title">Detalle de la Cuenta <?= $cuenta->descripcion ?></h4>
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
                <?php $total_egresos = 0; ?>
                <?php $total_ingresos = 0; ?>
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

                    <?php if ($mov->movimiento == 'INGRESO') {
                        $total_ingresos += $mov->saldo;
                    } else {
                        $total_egresos += $mov->saldo;
                    }
                    ?>
                <?php endforeach; ?>
                </tbody>
            </table>

            <div class="row">

                <div class="col-md-4">
                    <a href="#" id="exportar_pdf" class="btn  btn-danger btn-lg" data-toggle="tooltip" title=""
                       data-original-title="Exportar a PDF">
                        <i class="fa fa-file-pdf-o fa-fw"></i>
                    </a>
                </div>
                <div class="col-md-4 text-right">
                    <label>Ingresos: </label> <?= $caja->moneda_id == 1 ? MONEDA : DOLAR ?> <?= number_format($total_ingresos, 2) ?>
                </div>
                <div class="col-md-4 text-right">
                    <label>Egresos: </label> <?= $caja->moneda_id == 1 ? MONEDA : DOLAR ?> <?= number_format($total_egresos, 2) ?>
                </div>
            </div>

        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-warning" onclick="$('#dialog_form').modal('hide');">Cerrar</a>
        </div>
    </div>

    <script>

        $(document).ready(function () {

            $('#exportar_pdf').on('click', function () {
                exportar_pdf();
            });

        });


        function exportar_pdf() {
            var data = {
                'fecha_ini': $("#fecha_ini").val(),
                'fecha_fin': $("#fecha_fin").val()
            };

            var win = window.open('<?= base_url()?>cajas/caja_detalle_pdf/<?=$cuenta->id?>?data=' + JSON.stringify(data), '_blank');
            win.focus();
        }
    </script>





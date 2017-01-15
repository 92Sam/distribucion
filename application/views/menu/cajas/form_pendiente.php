<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="$('#dialog_form').modal('hide');"
                    aria-hidden="true">&times;</button>
            <h4 class="modal-title">Saldos Pendientes de la cuenta <?= $cuenta->descripcion ?></h4>
        </div>
        <div class="modal-body">

            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Responsable</th>
                    <th>Tipo</th>
                    <th>Operacion</th>
                    <th>Saldo Pendiente</th>
                    <th>Referencia</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($saldos_pendientes as $mov): ?>
                    <tr>
                        <td><?= $mov->id ?></td>
                        <td><?= $mov->usuario_id ?></td>
                        <td><?= $mov->tipo ?></td>
                        <td><?= $mov->IO == 2 ? 'Salida' : 'Entrada' ?></td>
                        <td><?= MONEDA .' '. number_format($mov->monto, 2) ?></td>
                        <td><?= $mov->ref_id ?></td>
                        <td>
                            <a class="btn_confirmar_saldo btn btn-primary"
                               data-id="<?= $mov->id ?>"
                               data-deglose_id="<?= $cuenta->id ?>">
                                <i class="fa fa-check"></i>
                            </a>
                        </td>
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
            $(".btn_confirmar_saldo").on('click', function () {
                var id = $(this).attr('data-id');
                var deslgose_id = $(this).attr('data-deglose_id');


                var url = '<?php echo base_url('cajas/confirmar_saldo')?>' + '/' + id;

                if ($("#cuenta_id_" + id).val() != undefined)
                    data.cuenta_id = $("#cuenta_id_" + id).val();

                $(this).attr('disabled', 'disabled');
                $.ajax({
                    url: url,
                    headers: {
                        Accept: 'application/json'
                    },
                    type: 'post',
                    success: function (data) {
                        if (data.error == '1') {
                            show_msg('warning', '<h4>Error. </h4><p>No cuentas con suficiente saldo.</p>');
                        }
                        else {
                            show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Confirmaci&oacute;n ejecutada correctamente.</p>');

                            $.ajax({
                                url: '<?php echo base_url('cajas/caja_pendiente_form')?>' + '/' + deslgose_id,
                                type: 'post',
                                success: function (data) {
                                    $("#dialog_form_pendiente").html(data);
                                }
                            });
                        }

                    },
                    error: function (data) {
                        show_msg('danger', '<h4>Error. </h4><p>Error inesperado.</p>');
                    },
                    complete: function (data) {
                        $(this).removeAttr('disabled');
                    }
                });
            });

        });
    </script>

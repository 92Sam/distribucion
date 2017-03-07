<div class="modal-dialog" style="width: 80%;">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Liquidar Pagos de <?= $venta->vendedor_nombre ?></h4>
        </div>
        <div class="modal-body">
            <?php $total_efectivo = 0; ?>
            <?php foreach ($pagos->pendientes as $pago): ?>
                <?php $total_efectivo += $pago->monto; ?>
            <?php endforeach; ?>

            <?php $total_pendiente = 0; ?>
            <?php foreach ($pagos->espera as $pago): ?>
                <?php $total_pendiente += $pago->monto; ?>
            <?php endforeach; ?>
            <input type="hidden" id="vendedor" value="<?= $venta->vendedor_id ?>">
            <input type="hidden" id="total_efectivo" value="<?= $total_efectivo ?>">
            <input type="hidden" id="total_pendiente" value="<?= $total_pendiente ?>">
            <div class="row">
                <div class="col-md-5">
                    <h4>Por Liquidar <span
                            style="float: right;"><?= MONEDA . ' ' . number_format($total_efectivo, 2) ?></span></h4>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Medio de Pago</label>
                        </div>
                        <div class="col-md-8">
                            <select name="pago_id" id="pago_id" class="form-control">
                                <?php foreach ($metodos_pago as $pago): ?>
                                    <?php if ($pago->id_metodo != 7): ?>
                                        <option
                                            value="<?= $pago->id_metodo ?>"
                                            <?= $pago->id_metodo == 3 ? 'selected' : '' ?>><?= $pago->nombre_metodo ?></option>
                                    <?php endif; ?>
                                <?php endforeach ?>
                            </select>
                        </div>

                    </div>
                    <div class="row" id="banco_block" style="display: none;">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label>Banco</label>
                            </div>
                            <div class="col-md-8">
                                <select name="banco_id" id="banco_id" class="form-control">
                                    <?php foreach ($bancos as $banco): ?>
                                        <option
                                            value="<?= $banco->banco_id ?>"><?= $banco->banco_nombre ?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="operacion_block" style="display: block;">
                        <div class="form-group">
                            <div class="col-md-4">
                                <label id="num_oper_label">Operaci&oacute;n</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" id="num_oper" name="num_oper"
                                       class="form-control"
                                       value="">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label>Monto a Confirmar</label>
                        </div>
                        <div class="col-md-6">
                            <input type="number" id="importe" name="importe"
                                   class="form-control"
                                   value="0.00" readonly>
                        </div>
                        <div class="col-md-2">
                            <button id="liquidar" type="button" class="btn btn-sm btn-primary tip" title="Liquidar">
                                <i class="fa fa-arrow-right"></i>
                            </button>
                        </div>
                    </div>
                    <br>
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th><input type="checkbox" id="select_all"></th>
                            <th>DOC</th>
                            <th>Monto</th>
                            <th>Acci&oacute;n</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr style="font-weight: bold;">
                            <td colspan="2">TOTAL EFECTIVO</td>
                            <td><?= MONEDA . ' ' . number_format($total_efectivo, 2) ?></td>
                            <td></td>
                        </tr>
                        <?php foreach ($pagos->pendientes as $pago): ?>
                            <tr id="row_<?= $pago->id ?>">
                                <td><input type="checkbox" class="select_all" value="<?= $pago->id ?>"></td>
                                <td><?= $pago->documento ?></td>
                                <td>
                                    <?= MONEDA . ' ' ?> <?= number_format($pago->monto, 2) ?>
                                    <span style="display: none;" id="monto_<?= $pago->id ?>"><?= number_format($pago->monto, 2, '.', '') ?></span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button class="btn btn-sm btn-danger tip delete_liquidacion"
                                                data-historial_id="<?= $pago->id ?>"
                                                title="Eliminar Liquidacion">
                                            <i class="fa fa-remove"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="col-md-7">
                    <h4>
                        Pendientes a Confirmaci&oacute;n
                        <span style="float: right;">
                            <?= MONEDA . ' ' . number_format($total_pendiente, 2) ?>
                        </span>
                    </h4>
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
                                            <button class="btn btn-sm btn-danger tip delete_liquidacion"
                                                    data-historial_id="<?= $pago->id ?>"
                                                    title="Eliminar Liquidacion">
                                                <i class="fa fa-remove"></i>
                                            </button>
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
    var metodos_pago = [];
    var bancos = [];

    <?php foreach ($metodos_pago as $pago): ?>
    metodos_pago.push({
        'id': '<?=$pago->id_metodo?>',
        'nombre': '<?=$pago->nombre_metodo?>'
    });
    <?php endforeach; ?>

    <?php foreach ($bancos as $banco): ?>
    bancos.push({
        'id': '<?=$banco->banco_id?>',
        'nombre': '<?=$banco->banco_nombre?>',
        'cuenta_id': '<?=$banco->cuenta_id?>'
    });
    <?php endforeach; ?>

    $(document).ready(function () {
        $("#dialog_pagar").on('hidden.bs.modal', function () {
            $('.btn_buscar').click();
        });

        $("#liquidar").on('click', function () {

            if ($("#pago_id").val() == "") {
                show_msg('warning', '<h4>Error. </h4><p>Seleccione un Medio de Pago.</p>');
                return false;
            }

            if ($("#pago_id").val() == "4" && $("#banco_id").val() == "") {
                show_msg('warning', '<h4>Error. </h4><p>Seleccione un Banco.</p>');
                return false;
            }

            if ($("#pago_id").val() == "4" && $("#num_oper").val() == "") {
                show_msg('warning', '<h4>Error. </h4><p>El numero de operaci&oacute;n es requerido.</p>');
                $("#num_oper").trigger('focus');
                return false;
            }

            if ($("#pago_id").val() == "5" && $("#num_oper").val() == "") {
                show_msg('warning', '<h4>Error. </h4><p>El numero de Cheque es requerido.</p>');
                $("#num_oper").trigger('focus');
                return false;
            }

            if ($("#pago_id").val() == "6" && $("#num_oper").val() == "") {
                show_msg('warning', '<h4>Error. </h4><p>El numero de Nota de Cr&eacute;dito es requerido.</p>');
                $("#num_oper").trigger('focus');
                return false;
            }

            var importe = isNaN(parseFloat($("#importe").val())) ? 0 : parseFloat($("#importe").val());
            var saldo = parseFloat($("#total_efectivo").val());

            if (importe <= 0 || importe > saldo) {
                show_msg('warning', '<h4>Error. </h4><p>El importe es menor que cero o mayor que el efectivo total.</p>');
                $("#importe").trigger('focus');
                return false;
            }

            var data = {
                'pago_id': $("#pago_id").val(),
                'banco_id': $("#banco_id").val(),
                'num_oper': $("#num_oper").val(),
                'importe': $("#importe").val(),
                'historial_id': prepare_historial_id()
            };

            $("#liquidar").attr('disabled', 'disabled');
            $.ajax({
                url: '<?php echo base_url('pago_pendiente/ejecutar_liquidar_pago')?>' + '/' + $("#vendedor").val(),
                data: data,
                headers: {
                    Accept: 'application/json'
                },
                type: 'post',
                success: function (data) {
                    if (data.error == '1') {
                        show_msg('warning', '<h4>Error. </h4><p>Ha ocurrido un error interno.</p>');
                    }
                    else {
                        show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Liquidaci&oacute;n ejecutada correctamente.</p>');
                        $("#dialog_pagar").html(data);
                    }

                },
                error: function (data) {
                    show_msg('danger', '<h4>Error. </h4><p>Error inesperado.</p>');
                },
                complete: function (data) {
                    $("#liquidar").removeAttr('disabled');
                }
            });
        });

        $(".delete_liquidacion").on('click', function () {
            var id = $(this).attr('data-historial_id');
            $(this).attr('disabled', 'disabled');
            $.ajax({
                url: '<?php echo base_url('pago_pendiente/eliminar_liquidar_pago')?>' + '/' + id + '/' + $("#vendedor").val(),
                headers: {
                    Accept: 'application/json'
                },
                type: 'post',
                success: function (data) {
                    if (data.error == '1') {
                        show_msg('warning', '<h4>Error. </h4><p>Ha ocurrido un error interno.</p>');
                    }
                    else {
                        show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Liquidaci&oacute;n ejecutada correctamente.</p>');
                        $("#dialog_pagar").html(data);
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

        $("#pago_id").on('click', function () {

            $("#num_oper").val('');
            $("#importe").val('');
            $("#banco_block").hide();

            if ($(this).val() == '4') {
                $("#banco_block").show();
            }


            calcular_monto();

        });

        $("#select_all").on('change', function () {
            if ($(this).prop('checked'))
                $('.select_all').prop('checked', true);
            else
                $('.select_all').prop('checked', false);

            calcular_monto();
        });

        $('.select_all').on('change', function () {
            calcular_monto();
        });
    });

    function calcular_monto() {
        var importe = parseFloat(0);
        $('.select_all').each(function () {
            var id = $(this).val();

            if ($(this).prop('checked')) {
                importe = parseFloat(importe) + parseFloat($('#monto_' + id).html().trim());
            }
        });
        $("#importe").val(formatPrice(importe));
    }

    function prepare_historial_id() {
        var historial_id = [];

        $('.select_all').each(function () {
            if ($(this).prop('checked')) {
                historial_id.push({
                    'id': $(this).val()
                });
            }
        });
        return JSON.stringify(historial_id);

    }


</script>
<?php $total_efectivo = 0; ?>
<?php foreach ($pagos->pendientes as $pago): ?>
    <?php $total_efectivo += $pago->monto; ?>
<?php endforeach; ?>

<?php $total_pendiente = 0; ?>
<?php foreach ($pagos->espera as $pago): ?>
    <?php $total_pendiente += $pago->monto; ?>
<?php endforeach; ?>

<?php $total_consolidado = 0; ?>
<?php foreach ($pagos->consolidado as $pago): ?>
    <?php $total_consolidado += $pago->monto; ?>
<?php endforeach; ?>
<input type="hidden" id="total_efectivo" value="<?= $total_efectivo ?>">
<input type="hidden" id="total_pendiente" value="<?= $total_pendiente ?>">
<input type="hidden" id="total_consolidado" value="<?= $total_consolidado ?>">
<div id="pen_liquidar" style="display: none;">
    <h4>
        Por Liquidar
    <span style="float: right;">
        <?= MONEDA . ' ' . number_format($total_efectivo, 2) ?>
    </span>
    </h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th>DOC</th>
            <th>Vendedor</th>
            <th>Monto</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pagos->pendientes as $pago): ?>
            <tr id="row_<?= $pago->id ?>">
                <td><?= $pago->documento ?></td>
                <td><?= $pago->vendedor_nombre ?></td>
                <td><?= MONEDA . ' ' . number_format($pago->monto, 2) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


<div id="pen_confirmar" style="display: block;">
    <h4>
        Pendientes a Confirmaci&oacute;n:
        <div class="btn-group">
            <button id="confirmar_liquidacion_select" class="btn btn-sm btn-primary tip"
                    title="Confirmar Liquidacion">
                <i class="fa fa-check"></i> Confirmar Selecci&oacute;n
            </button>
        </div>
                        <span style="float: right;">
                            <?= MONEDA . ' ' . number_format($total_pendiente, 2) ?>
                        </span>
    </h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th><input type="checkbox" id="select_all"></th>
            <th>Descripci&oacute;n</th>
            <th>Vendedor</th>
            <th>Medio de Pago</th>
            <th>Destino</th>
            <th>Caja a Depositar</th>
            <th>Operaci&oacute;n</th>
            <th>Monto</th>
            <th>Acci&oacute;n</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pagos->espera as $pago): ?>
            <tr>
                <td><input type="checkbox" class="select_all" value="<?= $pago->id ?>"></td>
                <td><?= $pago->documento ?></td>
                <td><?= $pago->vendedor_nombre ?></td>
                <td><?= $pago->pago_nombre ?></td>
                <td>
                    <?= $pago->pago_id == 4 ? 'BANCO: ' . $pago->banco_nombre : 'CAJA' ?>
                </td>
                <td>
                    <?php if ($pago->pago_id == 4): echo getCajaBanco($pago->banco_id) ?>

                    <?php elseif ($pago->pago_id == 7): echo 'RETENCI&Oacute;N (' . MONEDA . ')' ?>

                    <?php else: ?>
                        <select id="cuenta_id_<?= $pago->id ?>">
                            <?php foreach ($cajas as $caja): ?>
                                <option value="<?= $caja->cuenta_id ?>"
                                    <?= ($caja->principal == 1 && $caja->moneda_id == 1) ? 'selected' : '' ?>>
                                    <?= $caja->descripcion ?>
                                    (<?= $caja->moneda_id == 1 ? MONEDA : DOLAR ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </td>
                <td><?= $pago->num_oper ?></td>
                <td><?= MONEDA . ' ' . number_format($pago->monto, 2) ?></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary tip confirmar_liquidacion"
                                data-historial_id="<?= $pago->id ?>"
                                data-tipo="1"
                                title="Confirmar Liquidacion">
                            <i class="fa fa-check"></i>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div id="pen_consolidado" style="display: none;">
    <h4>
                        <span style="float: right;">
                            <?= MONEDA . ' ' . number_format($total_consolidado, 2) ?>
                        </span>
    </h4>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th><input type="checkbox" id="select_all_c"></th>
            <th>Descripci&oacute;n</th>
            <th>Consolidado</th>
            <th>Vendedor</th>
            <th>Medio de Pago</th>
            <th>Destino</th>
            <th>Caja a Depositar</th>
            <th>Operaci&oacute;n</th>
            <th>Monto</th>
            <th>Acci&oacute;n</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pagos->consolidado as $pago): ?>
            <tr>
                <td><input type="checkbox" class="select_all_c" value="<?= $pago->id ?>"></td>
                <td><?= $pago->documento ?></td>
                <td><?= sumCod($pago->consolidado, 5) ?></td>
                <td><?= $pago->vendedor_nombre ?></td>
                <td><?= $pago->pago_nombre ?></td>
                <td>
                    <?= $pago->pago_id == 4 ? 'BANCO: ' . $pago->banco_nombre : 'CAJA' ?>
                </td>
                <td>
                    <?php if ($pago->pago_id == 4): echo getCajaBanco($pago->banco_id) ?>

                    <?php elseif ($pago->pago_id == 7): echo 'RETENCI&Oacute;N (' . MONEDA . ')' ?>

                    <?php else: ?>
                        <select id="cuenta_id_<?= $pago->id ?>">
                            <?php foreach ($cajas as $caja): ?>
                                <option value="<?= $caja->cuenta_id ?>"
                                    <?= ($caja->principal == 1 && $caja->moneda_id == 1) ? 'selected' : '' ?>>
                                    <?= $caja->descripcion ?>
                                    (<?= $caja->moneda_id == 1 ? MONEDA : DOLAR ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    <?php endif; ?>
                </td>
                <td><?= $pago->num_oper ?></td>
                <td><?= MONEDA . ' ' . number_format($pago->monto, 2) ?></td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary tip confirmar_liquidacion"
                                data-historial_id="<?= $pago->id ?>"
                                data-tipo="2"
                                title="Confirmar Liquidacion">
                            <i class="fa fa-check"></i>
                        </button>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


<script type="text/javascript">


    $(document).ready(function () {

        show_pendiente();

        $(".confirmar_liquidacion").on('click', function () {
            var id = $(this).attr('data-historial_id');
            var tipo = $(this).attr('data-tipo');

            var url = '<?php echo base_url('pago_pendiente/confirmar_liquidar_pago')?>' + '/' + id;

            if (tipo == '2')
                url = '<?php echo base_url('pago_pendiente/confirmar_consolidado_pago')?>' + '/' + id;

            data = {
                cuenta_id: '0'
            };
            if ($("#cuenta_id_" + id).val() != undefined)
                data.cuenta_id = $("#cuenta_id_" + id).val();

            $(this).attr('disabled', 'disabled');
            $.ajax({
                url: url,
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
                        show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Confirmaci&oacute;n ejecutada correctamente.</p>');
                        $("#vendedor_id").trigger('change');
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

        $("#confirmar_liquidacion_select").on('click', function () {

            data = {
                historial_id: prepare_historial_id(1)
            };

            var len = 0;
            $('.select_all').each(function () {
                if ($(this).prop('checked')) {
                    len++;
                }
            });

            if (len == 0) {
                show_msg('warning', '<h4>Error. </h4><p>Seleccione al menos un registro.</p>');
                return false;
            }

            $(this).attr('disabled', 'disabled');
            $.ajax({
                url: '<?php echo base_url('pago_pendiente/confirmar_liquidar_pago_seleccion')?>',
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
                        show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Confirmaci&oacute;n ejecutada correctamente.</p>');
                        $("#vendedor_id").trigger('change');
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

        $("#estado").on('change', function () {
            show_pendiente();
        });

        $("#select_all").on('change', function () {
            if ($(this).prop('checked'))
                $('.select_all').prop('checked', true);
            else
                $('.select_all').prop('checked', false);

        });

        $("#select_all_c").on('change', function () {
            if ($(this).prop('checked'))
                $('.select_all_c').prop('checked', true);
            else
                $('.select_all_c').prop('checked', false);

        });
    });

    function show_pendiente() {
        $("#pen_liquidar").hide();
        $("#pen_confirmar").hide();
        $("#pen_consolidado").hide();

        if ($("#estado").val() == 1) {
            $("#pen_confirmar").show();
        }
        else if ($("#estado").val() == 2) {
            $("#pen_liquidar").show();
        }
        else if ($("#estado").val() == 3) {
            $("#pen_consolidado").show();
        }
    }

    function prepare_historial_id(tipo) {
        var historial_id = [];
        var selector = '.select_all';
        if (tipo == 2)
            selector = '.select_all_c';

        $(selector).each(function () {
            if ($(this).prop('checked')) {
                var cuenta_id = 0;
                if ($("#cuenta_id_" + $(this).val()).val() != undefined)
                    cuenta_id = $("#cuenta_id_" + $(this).val()).val();
                historial_id.push({
                    'id': $(this).val(),
                    'cuenta_id': cuenta_id,
                });
            }
        });
        return JSON.stringify(historial_id);
    }


</script>
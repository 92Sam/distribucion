<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title"><?= isset($header_text) ? $header_text : '' ?></h4>
        </div>
        <div class="modal-body">
            <input type="hidden" name="cuenta_id" id="cuenta_id" required="true"
                   value="<?= isset($cuenta->id) ? $cuenta->id : '' ?>">
            <form name="caja_form" action="<?= base_url() ?>cajas/caja_cuenta_guardar" method="post" id="caja_form">

                <input type="hidden" name="caja_id" id="caja_id" required="true"
                       value="<?= $caja_actual->id ?>" data-moneda_id="<?= $caja_actual->moneda_id ?>">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Cuenta Origen</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="cuenta_descripcion" name="cuenta_descripcion"
                                   class="form-control" readonly
                                   value="<?= isset($cuenta->descripcion) ? $cuenta->descripcion : '' ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Saldo Actual</label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <div
                                    class="input-group-addon"><?= $caja_actual->moneda_id == 1 ? MONEDA : DOLAR ?></div>
                                <input type="text" id="saldo_actual" name="saldo_actual"
                                       class="form-control" readonly
                                       value="<?= isset($cuenta->saldo) ? $cuenta->saldo : '0' ?>">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Fecha del Ajuste</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="fecha_ajuste" name="fecha_ajuste"
                                   class="form-control input-datepicker"
                                   value="<?= date('d-m-Y') ?>" readonly style="cursor: pointer;">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Motivo del Ajuste</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="motivo_ajuste" name="motivo_ajuste"
                                   class="form-control"
                                   value="">
                        </div>
                    </div>
                </div>

                <div class=" row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Tipo de Ajuste</label>
                        </div>
                        <div class="col-md-8">
                            <select name="tipo_ajuste" id="tipo_ajuste" class="form-control">
                                <option value="">Seleccione</option>
                                <option value="TRASPASO">TRASPASO</option>
                                <option value="INGRESO">INGRESO</option>
                                <option value="EGRESO">EGRESO</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row traspaso_caja" style="display: none;">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Caja a Transferir</label>
                        </div>
                        <div class="col-md-8">
                            <select id="caja_select" name="caja_select" class="form-control">
                                <?php foreach ($cajas as $caja): ?>
                                    <option <?= $caja->id == $caja_actual->id ? 'selected' : '' ?>
                                        value="<?= $caja->id ?>"
                                        data-moneda_id="<?= $caja->moneda_id ?>"
                                    ><?= $caja->moneda_id == 1 ? 'CAJA SOLES' : 'CAJA DOLARES' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="moneda_tasa" class="row" style="display: none;">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Tipo de Cambio</label>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-addon"><?= MONEDA ?></div>
                                <input type="number" id="tasa" name="tasa"
                                       class="form-control"
                                       value="">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <div class="input-group-addon tipo_moneda"></div>
                                <input type="number" id="subimporte" name="subimporte" readonly
                                       class="form-control"
                                       value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row traspaso_caja" style="display: none;">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Cuenta Destino</label>
                        </div>
                        <div class="col-md-8">
                            <select id="cuentas_select" name="cuentas_select" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($caja_cuentas as $caja_cuenta): ?>
                                    <?php if ($caja_cuenta->caja_id == $caja_actual->id && $caja_cuenta->id != $cuenta->id): ?>
                                        <option
                                            value="<?= $caja_cuenta->id ?>"><?= $caja_cuenta->descripcion ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div id="ajuste_importe" class="row" style="display: none;">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Importe a Ajustar</label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <div
                                    class="input-group-addon"><?= $caja_actual->moneda_id == 1 ? MONEDA : DOLAR ?></div>
                                <input id="importe" name="importe"
                                       class="form-control"
                                       value="">
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="modal-footer">
            <a id="btn_save_form_confirm" href="#" class="btn btn-primary">Guardar</a>
            <a href="#" class="btn btn-warning" data-dismiss="modal">Cancelar</a>
        </div>
    </div>
</div>

<div class="modal" id="confirm_ajuste" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-header">
            <button type="button" class="close" onclick="$('#confirm_ajuste').modal('hide')"
                    aria-hidden="true">&times;</button>
            <h4 class="modal-title">Confirmar Ajuste</h4>
        </div>
        <div class="modal-body" style="background-color: #FFF;">
            <h4>Estas Seguro de ejecutar este ajuste?</h4>
        </div>
        <div class="modal-footer">
            <a id="btn_save_form" href="#" class="btn btn-primary">Guardar</a>
            <a href="#" class="btn btn-warning" data-dismiss="modal">Cancelar</a>
        </div>
    </div>
</div>


<script>

    var cajas = [];
    var cuentas = [];

    <?php foreach ($cajas as $caja): ?>
    cajas.push({
        'id': '<?=$caja->id?>',
        'moneda_id': '<?=$caja->moneda_id?>'
    });
    <?php endforeach; ?>

    <?php foreach ($caja_cuentas as $caja_cuenta): ?>
    cuentas.push({
        'id': '<?=$caja_cuenta->id?>',
        'caja_id': '<?=$caja_cuenta->caja_id?>',
        'descripcion': '<?=$caja_cuenta->descripcion?>'
    });
    <?php endforeach; ?>

    $(document).ready(function () {

        $('.input-datepicker').datepicker({weekStart: 1, format: 'dd-mm-yyyy'});

        $("#tasa, #importe").on('keyup', function () {
            var moneda = $("#caja_id").attr('data-moneda_id');
            var tasa = isNaN(parseFloat($("#tasa").val())) ? 1 : parseFloat($("#tasa").val());
            var importe = isNaN(parseFloat($("#importe").val())) ? 0 : parseFloat($("#importe").val());

            if (moneda == '1')
                $("#subimporte").val(formatPrice(parseFloat(importe / tasa)));
            else
                $("#subimporte").val(formatPrice(parseFloat(importe * tasa)));

        });

        $("#tipo_ajuste").on('change', function () {
            $("#importe").val('');
            $("#tasa").val('');
            $("#subimporte").val('0.00');

            if ($(this).val() == 'TRASPASO') {
                $(".traspaso_caja").show();
                $("#ajuste_importe").show();
            }
            else if ($(this).val() == 'INGRESO' || $(this).val() == 'EGRESO') {
                $(".traspaso_caja").hide();
                $("#ajuste_importe").show();
                $("#importe").trigger('focus');
            }
            else {
                $(".traspaso_caja").hide();
                $("#ajuste_importe").hide();
                $("#importe").trigger('focus');
            }
        });

        $("#caja_select").on('change', function () {
            var cuenta_id = $("#cuentas_select");

            cuenta_id.html('<option value="">Seleccione</option>');

            for (var i = 0; i < cuentas.length; i++) {
                if (cuentas[i].caja_id == $(this).val() && cuentas[i].id != $("#cuenta_id").val()) {
                    cuenta_id.append('<option value="' + cuentas[i].id + '">' + cuentas[i].descripcion + '</option>');
                }
            }

            if ($("#caja_id").val() != $(this).val()) {
                if ($('#caja_id').attr('data-moneda_id') == 1)
                    $(".tipo_moneda").html('$');
                else
                    $(".tipo_moneda").html('S/.');
                $("#moneda_tasa").show();
            }
            else {
                $("#moneda_tasa").hide();
            }

        });

        $("#btn_save_form_confirm").on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var saldo_actual = isNaN(parseFloat($("#saldo_actual").val())) ? 0 : parseFloat($("#saldo_actual").val());
            var importe = isNaN(parseFloat($("#importe").val())) ? 0 : parseFloat($("#importe").val());

            if ($("#motivo_ajuste").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>El motivo del ajuste es obligatorio.</p>');
                return false;
            }
            if ($("#tipo_ajuste").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar el tipo de ajuste.</p>');
                return false;
            }

            if (importe <= 0) {
                show_msg('warning', '<h4>Error. </h4><p>El importe a ajustar debe ser mayor que cero.</p>');
                return false;
            }

            if (importe > saldo_actual && ($("#tipo_ajuste").val() == 'EGRESO' || $("#tipo_ajuste").val() == 'TRASPASO')) {
                show_msg('warning', '<h4>Error. </h4><p>El importe a ajustar debe ser menor o igual que el saldo actual.</p>');
                return false;
            }

            if ($("#cuentas_select").val() == "" && $("#tipo_ajuste").val() == 'TRASPASO') {
                show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar la cuenta destino.</p>');
                return false;
            }

            var tasa = isNaN(parseFloat($("#tasa").val())) ? 1 : parseFloat($("#tasa").val());
            if (tasa <= 0 && $("#tipo_ajuste").val() == 'TRASPASO') {
                show_msg('warning', '<h4>Error. </h4><p>El tipo de cambio debe ser mayor que cero.</p>');
                return false;
            }

            $("#confirm_ajuste").modal('show');
        });

        $("#btn_save_form").on('click', function (e) {

            $("#confirm_ajuste").modal('hide');

            var tasa = isNaN(parseFloat($("#tasa").val())) ? 1 : parseFloat($("#tasa").val());
            var data = {
                fecha: $("#fecha_ajuste").val(),
                motivo: $("#motivo_ajuste").val(),
                tipo_ajuste: $("#tipo_ajuste").val(),
                cuenta_id: $("#cuentas_select").val(),
                tasa: tasa,
                importe: $("#importe").val(),
                subimporte: $("#subimporte").val()
            };

            var url = '<?php echo base_url('cajas/caja_ajustar_guardar')?>' + '/' + $("#cuenta_id").val();

            $("#btn_save_form").attr('disabled', 'disabled');
            $.ajax({
                url: url,
                data: data,
                headers: {
                    Accept: 'application/json'
                },
                type: 'post',
                success: function (data) {
                    if (data.success != undefined) {
                        show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Cuenta ajustada correctamente.</p>');
                        $.ajax({
                            url: '<?php echo base_url('cajas')?>',
                            success: function (data) {
                                $('#page-content').html(data);
                                $(".modal-backdrop").remove();
                            }
                        });
                    }
                    else if (data.error == '1') {
                        show_msg('warning', '<h4>Error. </h4><p>Ha ocurrido un error interno.</p>');
                    }
                },
                error: function (data) {
                    show_msg('danger', '<h4>Error. </h4><p>Error inesperado.</p>');
                },
                complete: function (data) {
                    $("#btn_save_form").removeAttr('disabled');
                }
            });
        });

    });
</script>

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
<br>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Buscar retenciones</label>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="mes">
                                <?php for($i = 1; $i < 13; $i++):?>
                                    <option value="<?=$i?>" <?= date('n')==$i ? 'selected="selected"' : ''?>>
                                        <?= getMes($i) ?>
                                    </option>
                                <?php endfor;?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" id="year">
                                <?php foreach ($year as $y):?>
                                <option value="<?= $y->year?>" <?=$y->year == date('Y') ? 'selected="selected"' : ''?>>
                                    <?=$y->year?></option>
                                <?php endforeach;?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <label>Selecciones las retenciones</label>
                    </div>
                    <div class="col-md-8">
                        <label class="control-label" style="cursor: pointer;">
                            <input id="ret_all" type="checkbox"> Selecionar Todas:
                        </label><br>
                    <div id="ret_content"
                         style="width: 100%; height: 100px; border: 1px solid #dae8e7; overflow-y: scroll;">
                        <?php foreach ($retenciones as $retencion): ?>
                            <?php if(date('Y-m', strtotime($retencion->fecha_mov)) == date('Y-m')):?>
                            <label style="cursor: pointer;">
                                <input class="ret_check" type="checkbox"
                                       value="<?= $retencion->id ?>"
                                       data-saldo="<?=$retencion->saldo?>">
                                       <?= MONEDA.' '.number_format($retencion->saldo, 2)?> |
                                        #: <?= $retencion->ref_val ?>
                            </label><br>
                        <?php endif;?>
                        <?php endforeach; ?>
                    </div>
                    </div>
                </div>


                <div id="ajuste_importe" class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Importe a Pagar</label>
                        </div>
                        <div class="col-md-8">
                            <div class="input-group">
                                <div
                                    class="input-group-addon"><?= $caja_actual->moneda_id == 1 ? MONEDA : DOLAR ?></div>
                                <input type="number" id="importe" name="importe"
                                       class="form-control"
                                       value="0.00" readonly>
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
            <a href="#" class="btn btn-warning" onclick="$('#confirm_ajuste').modal('hide')">Cancelar</a>
        </div>
    </div>
</div>


<script>

    var cajas = [];
    var cuentas = [];
    var retenciones = [];

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

    <?php foreach ($retenciones as $retencion): ?>
    retenciones.push({
        'id': '<?=$retencion->id?>',
        'operacion': '<?=$retencion->ref_val?>',
        'fecha': '<?=date('Y-n', strtotime($retencion->fecha_mov))?>',
        'saldo': <?= $retencion->saldo ?>
    });
    <?php endforeach; ?>

    $(document).ready(function () {

        $('.input-datepicker').datepicker({weekStart: 1, format: 'dd-mm-yyyy'});

        add_checkbox_events();

        $("#mes, #year").on('change', function(){
            load_retenciones();
        });

        $("#btn_save_form_confirm").on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            var saldo_actual = isNaN(parseFloat($("#saldo_actual").val())) ? 0 : parseFloat($("#saldo_actual").val());
            var importe = isNaN(parseFloat($("#importe").val())) ? 0 : parseFloat($("#importe").val());

            if (importe <= 0) {
                show_msg('warning', '<h4>Error. </h4><p>El importe a ajustar debe ser mayor que cero.</p>');
                return false;
            }

            if (importe > saldo_actual) {
                show_msg('warning', '<h4>Error. </h4><p>El importe a ajustar debe ser menor o igual que el saldo actual.</p>');
                return false;
            }

            $("#confirm_ajuste").modal('show');
        });

        $("#btn_save_form").on('click', function (e) {

            $("#confirm_ajuste").modal('hide');

            var rets = [];
            $('.ret_check').each(function () {
                if ($(this).prop('checked')) {
                    rets.push($(this).val());
                }
            });
            rets = JSON.stringify(rets);

            var tasa = isNaN(parseFloat($("#tasa").val())) ? 1 : parseFloat($("#tasa").val());
            var data = {
                fecha: $("#fecha_ajuste").val(),
                importe: $("#importe").val(),
                retenciones: rets
            };

            var url = '<?php echo base_url('cajas/caja_ajustar_retencion_guardar')?>' + '/' + $("#cuenta_id").val();

            $("#btn_save_form").attr('disabled', 'disabled');
            $("#btn_save_form_confirm").attr('disabled', 'disabled');
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
                    $("#btn_save_form_confirm").removeAttr('disabled');
                }
            });
        });
    });

    function load_retenciones(){
        var ret = $("#ret_content");
        var mes = $("#mes").val();
        var year = $("#year").val();

        ret.html('');

        for(var i = 0; i < retenciones.length; i++){
            var fecha = year + '-' + mes;
            if(retenciones[i].fecha == fecha){
                var template = '<label style="cursor: pointer;">';
                template += '<input class="ret_check" type="checkbox"';
                template += 'value="' + retenciones[i].id + '"';
                template += 'data-saldo="' + retenciones[i].saldo + '">';
                template += ' S/. ' + formatPrice(retenciones[i].saldo) + ' | ';
                template += '#: ' + retenciones[i].operacion;
                template += '</label><br>';

                ret.append(template);
            }
        }

        $("#importe").val('0.00');
        $("#ret_all").prop('checked', false);
        add_checkbox_events();
    }

    function add_checkbox_events(){
        $("#ret_all").on('change', function () {
            if ($("#ret_all").prop('checked') == true) {
                $('.ret_check').prop('checked', 'checked');
            }
            else {
                $('.ret_check').removeAttr('checked');
            }
            $('.ret_check').trigger('change');
        });

        $('.ret_check').on('change', function () {
            var importe = 0;
            var n = 0;
            $('.ret_check').each(function () {
                if ($(this).prop('checked')) {
                    var saldo = parseFloat($(this).attr('data-saldo'));
                    importe = parseFloat(importe + saldo);
                    n++;
                }
            });

            $("#importe").val(formatPrice(importe));


            if (n == $('.ret_check').length)
                $('#ret_all').prop('checked', 'checked');
            else
                $('#ret_all').removeAttr('checked');
        });
    }
</script>

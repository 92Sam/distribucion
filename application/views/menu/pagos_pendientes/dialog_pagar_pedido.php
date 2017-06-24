<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Cobrar por Documento</h4>
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
                                   value="<?= $venta->total_deuda ?>" readonly>
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
                                   value="<?= $venta->saldo ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <h4>Realizar Pago</h4>

            <div class="row">
                <div class="form-group">
                    <div class="col-md-4">
                        <label>Medios de Pago</label>
                    </div>
                    <div class="col-md-8">
                        <select name="pago_id" id="pago_id" class="form-control">
                            <option value="">Seleccione</option>
                            <?php foreach ($metodos_pago as $pago): ?>
                                <?php if ($pago->id_metodo != 7): ?>
                                    <option
                                        value="<?= $pago->id_metodo ?>"><?= $pago->nombre_metodo ?></option>
                                <?php endif; ?>
                                <?php if ($pago->id_metodo == 7 && $retencion->retencion != 0 && $retencion->cobro_estado == 'NO_COBRADO'): ?>
                                    <option
                                        value="<?= $pago->id_metodo ?>"><?= $pago->nombre_metodo ?></option>
                                <?php endif; ?>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row" id="banco_block" style="display: none;">
                <div class="form-group">
                    <div class="col-md-4">
                        <label>Seleccione el Banco</label>
                    </div>
                    <div class="col-md-8">
                        <select name="banco_id" id="banco_id" class="form-control">
                            <option value="">Seleccione</option>
                            <?php foreach ($bancos as $banco): ?>
                                <option
                                    value="<?= $banco->banco_id ?>"><?= $banco->banco_nombre ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row" id="retencion_block" style="display: none;">
                <div class="form-group">
                    <div class="col-md-4">
                        <label id="retencion_label">Retencion %</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" id="retencion" name="retencion"
                               class="form-control" autocomplete="off"
                               value="">
                    </div>
                </div>
            </div>

            <div class="row" id="operacion_block" style="display: block;">
                <div class="form-group">
                    <div class="col-md-4">
                        <label id="num_oper_label">Dato Adicional</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" id="num_oper" name="num_oper"
                               class="form-control" autocomplete="off"
                               value="">
                    </div>
                </div>
            </div>

            <div class="row" id="fechaoperacion_block" style="display: none;">
                <div class="form-group">
                    <div class="col-md-4">
                        <label id="fec_oper_label">Fecha Operación</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" id="fec_oper" name="fec_oper"
                               class="form-control input-datepicker"
                               value="<?= date('d-m-Y') ?>" readonly style="cursor: pointer;">
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="form-group">
                    <div class="col-md-4">
                        <label>Importe a Pagar</label>
                    </div>
                    <div class="col-md-8">
                        <input type="text" id="importe" name="importe"
                               class="form-control" autocomplete="off"
                               value="">
                    </div>
                </div>
            </div>


            <div class="modal-footer">
                <a id="btn_save_form" href="#" class="btn btn-primary">Guardar</a>
                <a href="#" class="btn btn-warning" data-dismiss="modal">Cancelar</a>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var metodos_pago = [];
    var bancos = [];
    var retencion = {};

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

    retencion.pedido_id = '<?=$retencion->pedido_id?>';
    retencion.retencion = '<?=$retencion->retencion?>';
    retencion.cobro_estado = '<?=$retencion->cobro_estado?>';

    $(document).ready(function () {

        $("#btn_save_form").on('click', function(){

            if($("#pago_id").val() == ""){
                show_msg('warning', '<h4>Error. </h4><p>Seleccione un Medio de Pago.</p>');
                return false;
            }

            if($("#pago_id").val() == "4" && $("#banco_id").val() == ""){
                show_msg('warning', '<h4>Error. </h4><p>Seleccione un Banco.</p>');
                return false;
            }

            if($("#pago_id").val() == "4" && $("#num_oper").val() == ""){
                show_msg('warning', '<h4>Error. </h4><p>El numero de operaci&oacute;n es requerido.</p>');
                $("#num_oper").trigger('focus');
                return false;
            }

            if($("#pago_id").val() == "5" && $("#num_oper").val() == ""){
                show_msg('warning', '<h4>Error. </h4><p>El numero de Cheque es requerido.</p>');
                $("#num_oper").trigger('focus');
                return false;
            }

            if($("#pago_id").val() == "6" && $("#num_oper").val() == ""){
                show_msg('warning', '<h4>Error. </h4><p>El numero de Nota de Cr&eacute;dito es requerido.</p>');
                $("#num_oper").trigger('focus');
                return false;
            }

            if($("#pago_id").val() == "7" && $("#num_oper").val() == ""){
                show_msg('warning', '<h4>Error. </h4><p>El numero de retenci&oacute;n es requerido.</p>');
                $("#num_oper").trigger('focus');
                return false;
            }

            if ($("#pago_id").val() == "4") {
                if ($("#importe").val() == 0) {
                    show_msg('warning', '<h4>Error. </h4><p>Debe ingresar el importe del déposito.</p>');
                    $("#importe").trigger('focus');
                    return false;
                }
             }

            var importe = isNaN(parseFloat($("#importe").val())) ? 0 : parseFloat($("#importe").val());
            var saldo = parseFloat($("#saldo").val());

            if(importe <= 0 || importe > saldo){
                show_msg('warning', '<h4>Error. </h4><p>El importe es menor que cero o mayor que el saldo.</p>');
                $("#importe").trigger('focus');
                return false;
            }

            var data = {
                'pago_id': $("#pago_id").val(),
                'banco_id': $("#banco_id").val(),
                'num_oper': $("#num_oper").val(),
                'retencion': $("#retencion").val(),
                'importe': $("#importe").val(),
                'fec_oper':$("#fec_oper").val()
            };

            $("#btn_save_form").attr('disabled', 'disabled');
            $.ajax({
                url: '<?php echo base_url('pago_pendiente/ejecutar_pagar_nota_pedido')?>' + '/' + $("#doc_venta").attr('data-pedido_id'),
                data: data,
                headers: {
                    Accept: 'application/json'
                },
                type: 'post',
                success: function (data) {
                    if (data.success != undefined) {
                        $("#dialog_pagar").modal('hide');
                        show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Pago ejecutado correctamente.</p>');
                        $('.btn_buscar').click();
                    }
                    else if (data.error == '1') {
                        show_msg('warning', '<h4>Error. </h4><p>El numero de operación ingresado ya fue registrado</p>');
                        $("#num_oper").trigger('focus');
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

        $("#pago_id").on('click', function () {

            $("#banco_id").val('');
            $("#num_oper").val('');
            $("#importe").val('');
            $("#retencion_block").hide();
            $("#banco_block").hide();
            $("#importe").removeAttr('readonly');

            if ($(this).val() == '4') {
                $("#banco_block").show();
                $("#num_oper_label").html('N&uacute;mero de Operaci&oacute;n');
                $("#fechaoperacion_block").show();
            }
            else if ($(this).val() != '4') {

                if ($(this).val() == '5'){
                    $("#num_oper_label").html('N&uacute;mero de Cheque');
                    $("#fechaoperacion_block").hide();
                }
                if ($(this).val() == '6'){
                    $("#num_oper_label").html('N&uacute;mero de Nota de Cr&eacute;dito');
                    $("#fechaoperacion_block").hide();
                }
                else{
                    $("#num_oper_label").html('Dato Adicional');
                    $("#fechaoperacion_block").hide();
                }
            }

            if ($(this).val() == '7') {
                $("#num_oper_label").html('N&uacute;mero de Retenci&oacute;n');
                $("#importe").attr('readonly', 'readonly');
                $("#importe").val(calcular_retencion());
                $("#retencion").val(retencion.retencion);
                $("#retencion_block").show();
                $("#fechaoperacion_block").hide();
            }

        });

        $("#retencion").on('keyup', function () {
            var ret_val = parseFloat($(this).val()) > 0 ? parseFloat($(this).val()) : 0;
            retencion.retencion = ret_val;
            if ($(this).val() != "")
                $(this).val(ret_val);
            $("#importe").val(calcular_retencion());
        });

    });

    function calcular_retencion() {
        var ret_val = parseFloat(retencion.retencion) > 0 ? parseFloat(retencion.retencion) : 0;
        var venta_total = parseFloat($("#venta_total").val());

        return formatPrice(parseFloat(venta_total * ret_val / 100));
    }

    function validarNumeroOperacion(){

        return false;
        var operacion = $("#num_oper").val();
        $.ajax({
            url: '<?= base_url()?>banco/validaNumeroOperacion/' + operacion,
            dataType:'json',
            async: false,
            data: {'operacion': operacion},
            type: 'POST',

            success: function(data){
                if (data.error == undefined)
                    result = false;
                else
                    result = true;

            },
            error:function(){
                show_msg('danger','Ha ocurrido un error vuelva a intentar');
            }
        })

        return result;

    }

</script>
<form name="formagregar" action="<?= base_url() ?>banco/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($banco['banco_id'])) echo $banco['banco_id']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Banco</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Nombre</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="nombre" id="nombre" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_nombre'])) echo $banco['banco_nombre']; ?>">
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Asociar Cuenta</label>
                        </div>
                        <div class="col-md-4">
                            <select id="caja_select" name="caja_select" class="form-control">
                                <?php foreach ($cajas as $caja): ?>
                                    <option <?= $caja->id == $caja_actual->id ? 'selected' : '' ?>
                                        value="<?= $caja->id ?>"
                                        data-moneda_id="<?= $caja->moneda_id ?>"
                                    ><?= $caja->moneda_id == 1 ? 'CAJA SOLES' : 'CAJA DOLARES' ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select id="cuentas_select" name="cuentas_select" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($caja_cuentas as $caja_cuenta): ?>
                                    <?php if ($caja_cuenta->caja_id == $caja_actual->id): ?>
                                        <option
                                            value="<?= $caja_cuenta->id ?>"
                                        <?= isset($banco['cuenta_id']) && $banco['cuenta_id'] == $caja_cuenta->id ? 'selected' : ''?>><?= $caja_cuenta->descripcion ?></option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Número de Cuenta</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="nro_cuenta" id="nro_cuenta" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_numero_cuenta'])) echo $banco['banco_numero_cuenta']; ?>">
                        </div>

                    </div>
                </div>

                <!--  <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Saldo</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="saldo" id="saldo" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_saldo'])) echo $banco['banco_saldo']; ?>">
                        </div>

                    </div>
                </div>   -->

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Cuenta Contable</label>
                        </div>
                        <div class="col-md-8">
                            <input type="number" name="cuenta_contable" id="cuenta_contable" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_cuenta_contable'])) echo $banco['banco_cuenta_contable']; ?>">
                        </div>


                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Titular</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="titular" id="titular" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_titular'])) echo $banco['banco_titular']; ?>">
                        </div>
                    </div>
                </div>

            </div>


            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
            <!-- /.modal-content -->
</form>

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

    if($("#nombre").val() != '' ){
        $("#saldo").prop("readonly",true);
    }

    $(document).ready(function(){

        $("#caja_select").on('change', function () {
            var cuenta_id = $("#cuentas_select");

            cuenta_id.html('<option value="">Seleccione</option>');

            for (var i = 0; i < cuentas.length; i++) {
                if (cuentas[i].caja_id == $(this).val()) {
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
    });

</script>

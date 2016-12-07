<input type="hidden" name="caja_id" id="caja_id" required="true"
       value="<?= isset($caja->id) ? $caja->id : '' ?>">
<form name="caja_form" action="<?= base_url() ?>cajas/caja_guardar" method="post" id="caja_form">


    <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <label>Ubicaci&oacute;n</label>
            </div>
            <div class="col-md-9">
                <select name="local_id" id="local_id" class="form-control">
                    <option value="">Seleccione</option>
                    <?php foreach ($locales as $local): ?>
                        <option
                            value="<?= $local['int_local_id'] ?>"
                            <?= isset($caja->local_id) && $caja->local_id == $local['int_local_id'] ? 'selected' : '' ?>><?= $local['local_nombre'] ?></option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <label>Responsable</label>
            </div>
            <div class="col-md-9">
                <select name="responsable_id" id="responsable_id" class="form-control">
                    <option value="">Seleccione</option>
                    <?php foreach ($usuarios as $usuario): ?>
                        <option
                            value="<?php echo $usuario->nUsuCodigo ?>"
                            <?= isset($caja->responsable_id) && $caja->responsable_id == $usuario->nUsuCodigo ? 'selected' : '' ?>>
                            <?= $usuario->nombre ?>

                        </option>
                    <?php endforeach ?>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <label>Moneda</label>
            </div>
            <div class="col-md-9">
                <select name="moneda_id" id="moneda_id" class="form-control">
                    <option value="">Seleccione</option>
                    <option value="1" <?= isset($caja->moneda_id) && $caja->moneda_id == 1 ? 'selected' : '' ?>>SOLES
                    </option>
                    <option value="2" <?= isset($caja->moneda_id) && $caja->moneda_id == 2 ? 'selected' : '' ?>>
                        DOLARES
                    </option>
                </select>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="form-group">
            <div class="col-md-3">
                <label>Estado de la Caja</label>
            </div>
            <div class="col-md-9">
                <select name="estado" id="estado" class="form-control">
                    <option value="1" <?= isset($caja->estado) && $caja->estado == 1 ? 'selected' : '' ?>>ACTIVA
                    </option>
                    <option value="0" <?= isset($caja->estado) && $caja->estado == 0 ? 'selected' : '' ?>>INACTIVA
                    </option>
                </select>
            </div>
        </div>
    </div>
    <!-- /.modal-content -->
</form>

<script>

    $(document).ready(function () {

        $("#btn_save_form").on('click', function () {

            if ($("#local_id").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar un local.</p>');
                return false;
            }
            if ($("#responsable_id").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar un responsable.</p>');
                return false;
            }
            if ($("#moneda_id").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar una moneda.</p>');
                return false;
            }

            var form = $('#caja_form').serialize();
            var url = '<?php echo base_url('cajas/caja_guardar')?>';
            if ($("#caja_id").val() != "")
                url = '<?php echo base_url('cajas/caja_guardar')?>' + '/' + $("#caja_id").val();

            $("#btn_save_form").attr('disabled', 'disabled');
            $.ajax({
                url: url,
                data: form,
                headers: {
                    Accept: 'application/json'
                },
                type: 'post',
                success: function (data) {
                    if (data.success != undefined) {
                        show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Caja guardada correctamente.</p>');
                        $.ajax({
                            url: '<?php echo base_url('cajas')?>',
                            success: function (data) {
                                $('#page-content').html(data);
                                $(".modal-backdrop").remove();
                            }
                        });
                    }
                    else if (data.error == '1') {
                        show_msg('warning', '<h4>Error. </h4><p>Ya este local y esta moneda tienen una caja creada.</p>');
                    }
                },
                error: function (data) {

                },
                complete: function (data) {
                    $("#btn_save_form").removeAttr('disabled');
                }
            });
        });
    });
</script>





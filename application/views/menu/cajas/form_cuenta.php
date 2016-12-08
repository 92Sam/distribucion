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
                       value="<?= $caja_id ?>">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Descripci&oacute;n</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" id="descripcion" name="descripcion"
                                   class="form-control"
                                   value="<?= isset($cuenta->descripcion) ? $cuenta->descripcion : '' ?>">
                        </div>
                    </div>
                </div>

                <div class=" row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Responsable</label>
                        </div>
                        <div class="col-md-8">
                            <select name="responsable_id" id="responsable_id" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($usuarios as $usuario): ?>
                                    <option
                                        value="<?php echo $usuario->nUsuCodigo ?>"
                                        <?= isset($cuenta->responsable_id) && $cuenta->responsable_id == $usuario->nUsuCodigo ? 'selected' : '' ?>>
                                        <?= $usuario->nombre ?>

                                    </option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Cuenta Principal</label>
                        </div>
                        <div class="col-md-8">
                            <select name="principal" id="principal" class="form-control">
                                <option
                                    value="0" <?= isset($cuenta->principal) && $cuenta->principal == 0 ? 'selected' : '' ?>>
                                    NO
                                </option>
                                <option
                                    value="1" <?= isset($cuenta->principal) && $cuenta->principal == 1 ? 'selected' : '' ?>>
                                    SI
                                </option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label>Estado de la Cuenta</label>
                        </div>
                        <div class="col-md-8">
                            <select name="estado" id="estado" class="form-control">
                                <option
                                    value="1" <?= isset($cuenta->estado) && $cuenta->estado == 1 ? 'selected' : '' ?>>
                                    ACTIVA
                                </option>
                                <option
                                    value="0" <?= isset($cuenta->estado) && $cuenta->estado == 0 ? 'selected' : '' ?>>
                                    INACTIVA
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- /.modal-content -->
            </form>
        </div>
        <div class="modal-footer">
            <a id="btn_save_form" href="#" class="btn btn-primary">Guardar</a>
            <a href="#" class="btn btn-warning" data-dismiss="modal">Cancelar</a>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {

        $("#btn_save_form").on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if ($("#descripcion").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>La descripci&oacute;n es obligatoria.</p>');
                return false;
            }
            if ($("#responsable_id").val() == '') {
                show_msg('warning', '<h4>Error. </h4><p>Debe seleccionar un responsable.</p>');
                return false;
            }

            var form = $('#caja_form').serialize();
            var url = '<?php echo base_url('cajas/caja_cuenta_guardar')?>';
            if ($("#cuenta_id").val() != "")
                url = '<?php echo base_url('cajas/caja_cuenta_guardar')?>' + '/' + $("#cuenta_id").val();

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
                        show_msg('success', '<h4>Operaci&oacute;n exitosa. </h4><p>Cuenta guardada correctamente.</p>');
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





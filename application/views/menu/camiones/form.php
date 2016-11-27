<form name="formagregar" action="<?= base_url() ?>camiones/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id=""
           value="<?php if (isset($camiones['camiones_id'])) echo $camiones['camiones_id']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Cami&oacute;n</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Placa Cami&oacuten</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="camiones_placa" id="camiones_placa" required="true"
                                   class="form-control"
                                   value="<?php if (isset($camiones['camiones_placa'])) echo $camiones['camiones_placa']; ?>">

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Metros c&uacute;bicos</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="metros_cubicos" id="metros_cubicos" required="true"
                                   class="form-control" onkeypress="return isNumber(event);"
                                   value="<?php if (isset($camiones['metros_cubicos'])) echo $camiones['metros_cubicos']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Trabajador</label>
                        </div>
                        <div class="col-md-9">
                            <select name="id_trabajadores" id="id_trabajadores" required="true" class="form-control"
                                    onchange="actualizarestados();">
                                <option value="">Seleccione</option>
                                <?php
                                foreach ($trabajadores as $nombreUser): ?>
                                    <option
                                        value="<?= $nombreUser['nUsuCodigo'] ?>" <?php if (isset($camiones['id_trabajadores']) and $camiones['id_trabajadores'] == $nombreUser['nUsuCodigo']) echo 'selected' ?>><?= $nombreUser['nombre'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- /.modal-content -->
            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
        </div>
    </div>
</form>
<script>
    $(document).ready(function () {
        $("select").chosen();
    })
</script>
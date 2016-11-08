<form name="formagregar" action="<?= base_url() ?>cajas/guardar" method="post" id="formagregar">


    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($cajas['caja_id'])) echo $cajas['caja_id']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Caja</h4>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Local</label>
                        </div>
                        <div class="col-md-10">
                            <select name="local" id="local" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($locales as $local): ?>
                                    <option
                                        value="<?php echo $local['int_local_id'] ?>" <?php if (isset($cajas['local']) and $cajas['local'] == $local['int_local_id']) echo 'selected' ?>><?= $local['local_nombre'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>            

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Activo</label>
                        </div>
                        <div class="col-md-10">
                            <input type="checkbox" name="activo" id="activo" required="true"
                                   class="form-checkbox" value="1" <?php if (!isset($cajas['caja_id'])) echo 'checked="checked"'; 
                                                                            elseif (isset($cajas['caja_id'])) {
                                                                        echo ($cajas['activo'] == 1) ? 'checked="checked"' : NULL;  }?>                       # code...
                                                                   >
                        </div>
                    </div>
                </div>   

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Cuenta Contable</label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" name="cuenta_contable" id="cuenta_contable" required="true"
                                   class="form-control"
                                   value="<?php if (isset($cajas['cuenta_contable'])) echo $cajas['cuenta_contable']; ?>">
                        </div>
                    </div>
                </div>   

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Responsable</label>
                        </div>
                        <div class="col-md-10">
                            <select name="responsable" id="responsable" class="form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($usuarios as $responsable): ?>
                                    <option
                                        value="<?php echo $responsable['nUsuCodigo'] ?>" <?php if (isset($cajas['responsable']) and $cajas['responsable'] == $responsable['nUsuCodigo']) echo 'selected' ?>><?= $responsable['nombre'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>   

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Usuarios</label>
                        </div>
                        <div class="col-md-10">
                            <select name="usuarios[]" id="usuarios" class='form-control selectpicker' multiple="true">
     
                                    <?php foreach ($usuarios as $users) { ?>

                                        <?php if ($usuarios != null) {
                                            $cantidad = count($usuarios);
                                            $i = 1;
                                            foreach ($usuarios as $usz) {
                                                if (isset($cajas['caja_id']) and $cajas['caja_id'] == $users['caja']) {
                                                    ?>
                                                    
                                                     <option
                                                        value="<?php echo $users['nUsuCodigo'] ?>"
                                                        selected><?= $users['nombre'] ?></option>

                                                    <?php break;
                                                }
                                                if ($cantidad == $i) {
                                                    ?>
                                                    <option
                                                value="<?php echo $users['nUsuCodigo'] ?>"><?= $users['nombre'] ?></option>
                                                <?php
                                                } else {
                                                    $i++;

                                                }
                                            }
                                        } else { ?>
                                          <option
                                                value="<?php echo $users['nUsuCodigo'] ?>" ><?= $users['nombre'] ?></option>
                                        <?php };

                                    } ?>
                                   
                            </select>
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
    </div>

</form>

<script>
    $('.selectpicker').selectpicker();
</script>





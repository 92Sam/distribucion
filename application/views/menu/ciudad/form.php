
<form name="formagregar" action="<?= base_url() ?>ciudad/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($ciudad['ciudad_id'])) echo $ciudad['ciudad_id']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo distrito</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Pa&iacute;s</label>
                        </div>
                        <div class="col-md-9">
                            <select name="id_pais" id="id_pais" required="true" class="form-control"
                                    onchange="region.actualizarestados();">
                                <option value="">Seleccione</option>
                                <?php foreach ($paises as $pais): ?>
                                    <option
                                        value="<?php echo $pais['id_pais'] ?>" <?php if (isset($ciudad['id_pais']) and $ciudad['id_pais'] == $pais['id_pais']) echo 'selected' ?>><?= $pais['nombre_pais'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Ciudad</label>
                        </div>
                        <div class="col-md-9">

                            <select name="estado_id" ID="estado_id" required="true" class="form-control">
                                <option value="">Seleccione</option>
                                <?php if (isset($ciudad['ciudad_id'])): ?>
                                    <?php foreach ($estados as $estado): ?>
                                        <option
                                            value="<?php echo $estado['estados_id'] ?>" <?php if (isset($ciudad['estados_id']) and $ciudad['estados_id'] == $ciudad['estados_id']) echo 'selected' ?>><?= $estado['estados_nombre'] ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Distrito</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="ciudad_nombre" id="ciudad_nombre" required="true"
                                   class="form-control"
                                   value="<?php if (isset($ciudad['ciudad_nombre'])) echo $ciudad['ciudad_nombre']; ?>">
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

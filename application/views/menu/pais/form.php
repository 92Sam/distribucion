<form name="formagregar" action="<?= base_url() ?>pais/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($pais['id_pais'])) echo $pais['id_pais']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Pa&iacute;s</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Pa&iacute;s</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="nombre_pais" id="nombre_pais" required="true"
                                   class="form-control"
                                   value="<?php if (isset($pais['nombre_pais'])) echo $pais['nombre_pais']; ?>">
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
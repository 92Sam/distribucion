<form name="formagregar" id="formagregar"  action="<?= base_url() ?>linea/guardar" method="post" id="">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva L&iacute;nea</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-md-2">
                        Nombre
                    </div>
                    <div class="col-md-10"><input type="text" name="nombre" id="nombre" required="true" class="form-control"
                                                  value="<?php if (isset($lineas['nombre_linea'])) echo $lineas['nombre_linea']; ?>">
                    </div>
                    <input type="hidden" name="id" id="" required="true"
                           value="<?php if (isset($lineas['id_linea'])) echo $lineas['id_linea']; ?>">
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
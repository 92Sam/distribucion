<form name="formagregar" action="<?= base_url() ?>tiposdegasto/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($tiposgasto['id_tipos_gasto'])) echo $tiposgasto['id_tipos_gasto']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Tipo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Nombre</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="nombre_tipos_gasto" id="nombre_tipos_gasto" required="true"
                                   class="form-control"
                                   value="<?php if (isset($tiposgasto['nombre_tipos_gasto'])) echo $tiposgasto['nombre_tipos_gasto']; ?>">
                        </div>

                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>
</form>
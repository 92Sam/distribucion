<form name="formagregar" id="formagregar" action="<?= base_url() ?>impuesto/guardar" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Impuesto</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-md-2">
                        Nombre:
                    </div>
                    <div class="col-md-10"><input type="text" name="nombre" id="nombre" required="true" class="form-control"
                                                  value="<?php if (isset($impuesto['nombre_impuesto'])) echo $impuesto['nombre_impuesto']; ?>">

                        <input type="hidden" name="id" id="" required="true"
                               value="<?php if (isset($impuesto['id_impuesto'])) echo $impuesto['id_impuesto']; ?>">
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div class="col-md-2">
                        Porcentaje:
                    </div>
                        <div class="col-md-3">
                            <input type="number" name="porcentaje" id="porcentaje" class="form-control" required="true" min="0"
                                  data-toggle="tooltip"  title="Si es decimal por favor coloque punto" data-original-title="Si es DECIMAL por favor coloque punto"
                                   value="<?php if (isset($impuesto['porcentaje_impuesto'])) echo $impuesto['porcentaje_impuesto']; ?>">
                        </div>
                    <div class="col-md-2">
                        <input type="text"  id="" class="form-control"  disabled
                               value="%">
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
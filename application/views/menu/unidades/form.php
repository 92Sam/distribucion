<form name="formagregar" id="formagregar" action="<?= base_url() ?>unidades/guardar" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Unidad</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                <div class="form-group">
                    <div class="col-md-2">
                        Nombre:
                    </div>
                    <div class="col-md-10"><input type="text" name="nombre" id="nombre" required="true" class="form-control"
                                                  value="<?php if (isset($unidad['nombre_unidad'])) echo $unidad['nombre_unidad']; ?>">

                        <input type="hidden" name="id" id="" required="true"
                               value="<?php if (isset($unidad['id_unidad'])) echo $unidad['id_unidad']; ?>">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        Abreviatura:
                    </div>
                    <div class="col-md-10"><input type="text" name="abreviatura" id="abreviatura" required="true" class="form-control"
                                                  value="<?php if (isset($unidad['abreviatura'])) echo $unidad['abreviatura']; ?>">


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
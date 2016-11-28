<form name="formagregar" action="<?= base_url() ?>familia/guardar" method="post" id="formagregar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Sub Línea</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="col-xs-12">
                        <div class="alert alert-danger alert-dismissable" id="error"
                             style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                            <h4><i class="icon fa fa-check"></i> Error</h4>
                            <span id="errorspan"><?php echo isset($error) ? $error : '' ?></div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-2">
                        Nombre
                    </div>

                    <div class="col-md-10">
                        <input type="text" name="nombre" id="nombre" required="true" class="form-control"
                               value="<?php if (isset($familia['nombre_familia'])) echo $familia['nombre_familia']; ?>">
                    </div>
                    <input type="hidden" name="id" id="" required="true"
                           value="<?php if (isset($familia['id_familia'])) echo $familia['id_familia']; ?>">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</form>
<form name="formagregar" id="formagregar" action="<?= base_url() ?>precio/guardar" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Precio</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <div class="col-md-2">
                        Nombre:
                    </div>
                    <div class="col-md-10"><input type="text" name="nombre" id="nombre" required="true" class="form-control"
                                                  value="<?php if (isset($precio['nombre_precio'])) echo $precio['nombre_precio']; ?>">

                        <input type="hidden" name="id" id="" required="true"
                               value="<?php if (isset($precio['id_precio'])) echo $precio['id_precio']; ?>">
                    </div>
                </div>
            </div>

            <div class="modal-body">
                <div class="form-group">
                    <div class="col-md-2">
                        Descuento:
                    </div>
                    <div class="col-md-3">
                        <input type="number" name="descuento" id="descuento" class="form-control" required="true" min="0"
                               data-toggle="tooltip"  title="Si es decimal por favor coloque punto" data-original-title="Si es DECIMAL por favor coloque punto"
                               value="<?php if (isset($precio['descuento_precio'])) echo $precio['descuento_precio']; ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text"  id="" class="form-control"  disabled
                               value="%">
                    </div>

                </div>
            </div>

            <div class="modal-body" style="height: 100px">
                <div class="form-group">
                    <div class="col-md-2">
                        Mostrar:
                    </div>
                    <div class="col-md-2"><input type="checkbox" name="mostrar" id=""
                                                  value=1 <?php if(isset($precio['mostrar_precio']) && $precio['mostrar_precio']==1){

                            echo "checked";
                        } ?> >
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
<form name="formagregar" action="<?= base_url() ?>clientesgrupos/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($clientesgrupos['id_grupos_cliente'])) echo $clientesgrupos['id_grupos_cliente']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Grupo</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Nombre</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="nombre_grupos_cliente" id="nombre_grupos_cliente" required="true"
                                   class="form-control"
                                   value="<?php if (isset($clientesgrupos['nombre_grupos_cliente'])) echo $clientesgrupos['nombre_grupos_cliente']; ?>">
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
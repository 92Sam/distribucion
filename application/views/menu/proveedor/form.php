<form name="formagregar" action="<?= base_url() ?>proveedor/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id=""
           value="<?php if (isset($proveedor['id_proveedor'])) echo $proveedor['id_proveedor']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Proveedor</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">RUC</label>
                        </div>
                        <div class="col-md-8"> <!--proveedor_nrofax -->
                            <input type="text" name="proveedor_ruc" id="proveedor_ruc"  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_ruc'])) echo $proveedor['proveedor_ruc']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Razon Social</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="proveedor_nombre" id="proveedor_nombre" required="true"
                                   class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_nombre'])) echo $proveedor['proveedor_nombre']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Direcci&oacute;n Proveedor</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="proveedor_direccion1" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_direccion1'])) echo $proveedor['proveedor_direccion1']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Tel&eacute;fono de Proveedor </label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="proveedor_telefono1" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_telefono1'])) echo $proveedor['proveedor_telefono1']; ?>">
                        </div>
                    </div>
                </div>

                 <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Correo Proveedor</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="proveedor_email" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_email'])) echo $proveedor['proveedor_email']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Nombre Contacto</label>
                        </div>
                        <div class="col-md-8"><!--proveedor_direccion2 -->
                            <input type="text" name="proveedor_contacto" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_contacto'])) echo $proveedor['proveedor_contacto']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Telefono Contacto</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="proveedor_telefono2" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_telefono2'])) echo $proveedor['proveedor_telefono2']; ?>">
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">P&aacute;gina Web</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="proveedor_paginaweb" id=""  class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_paginaweb'])) echo $proveedor['proveedor_paginaweb']; ?>">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-4">
                            <label class="control-label panel-admin-text">Observaci&oacute;n</label>
                        </div>
                        <div class="col-md-8">
                            <input type="text" name="proveedor_observacion" id="" class="form-control"
                                   value="<?php if (isset($proveedor['proveedor_observacion'])) echo $proveedor['proveedor_observacion']; ?>">
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

    <script type="text/javascript">
    jQuery(document).ready(function() {
        $("#proveedor_ruc").mask('99999999999');

    });

    </script>

</form>
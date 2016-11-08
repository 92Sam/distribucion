<form name="formagregar" action="<?= base_url() ?>banco/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($banco['banco_id'])) echo $banco['banco_id']; ?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nuevo Banco</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Nombre</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="nombre" id="nombre" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_nombre'])) echo $banco['banco_nombre']; ?>">
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Número de Cuenta</label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" name="nro_cuenta" id="nro_cuenta" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_numero_cuenta'])) echo $banco['banco_numero_cuenta']; ?>">
                        </div>

                    </div>
                </div>

                <!--  <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Saldo</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="saldo" id="saldo" required="true"
                                   class="form-control" 
                                   value="<?php if (isset($banco['banco_saldo'])) echo $banco['banco_saldo']; ?>">
                        </div>

                    </div>
                </div>   -->

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Cuenta Contable</label>
                        </div>
                        <div class="col-md-10">
                            <input type="number" name="cuenta_contable" id="cuenta_contable" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_cuenta_contable'])) echo $banco['banco_cuenta_contable']; ?>">
                        </div>
                        

                    </div>
                </div>   

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Titular</label>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="titular" id="titular" required="true"
                                   class="form-control"
                                   value="<?php if (isset($banco['banco_titular'])) echo $banco['banco_titular']; ?>">
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

<script>
    
    if($("#nombre").val() != '' ){
        $("#saldo").prop("readonly",true);
    }

</script>

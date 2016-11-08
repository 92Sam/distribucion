<div class="modal-dialog" style="width: 50%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Pagar Cuota</h4>
        </div>
        <div class="modal-body">
            <form id="form">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">Cantidad Pendiente</div>
                        <div class="col-md-1"> <?php if ($cuentas[0]['suma'] != null) {
                                echo $cuentas[0]['total_ingreso'] - $cuentas[0]['suma'];
                            } else {
                                echo $cuentas[0]['total_ingreso'];
                            } ?>  </div>

                        <div class="col-md-2">Cantidad abonada</div>
                        <div class="col-md-1"> <?php if ($cuentas[0]['suma'] != null) {
                                echo $cuentas[0]['suma'];
                            } else {
                                echo "0";
                            } ?>  </div>

                        <div class="col-md-2">Monto a Pagar:</div>
                        <div class="col-md-3">
                            <input type="number" id="cantidad_a_pagar" value="" onkeydown="return:soloDecimal();"
                                   class="form-control">
                            <input type="hidden" id="id_cronograma">

                            <input name="proveedor" value="<?php if (isset($proveedor)) {
                                echo $proveedor;
                            } ?>" type="hidden">
                            <input name="fecIni" value="<?php if (isset($fecIni)) {
                                echo $fecIni;
                            } ?>" type="hidden">
                            <input name="fecFin" value="<?php if (isset($fecFin)) {
                                echo $fecFin;
                            } ?>" type="hidden">


                        </div>
                    </div>
                </div>
            </form>
            <br>


        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-default" id="guardarPago"
               onclick="guardarPago(<?= $cuentas[0]['total_ingreso']; ?>,<?php if ($cuentas[0]['suma'] != null) {
                   echo $cuentas[0]['suma'];
               } else {
                   echo 0;
               } ?>,<?= $cuentas[0]['id_ingreso']; ?>)">
                <i class=""></i> Pagar</a>
            <a href="#" class="btn btn-default" data-dismiss="modal"
               onclick="javascript:$('#pagar_venta').hide();">Cancelar</a>
        </div>
    </div>
</div>

<script>


    var lst_producto = new Array();
    var producto = {};
    $(function () {


    });
</script>

<script>


</script>


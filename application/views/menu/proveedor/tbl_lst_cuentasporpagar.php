<?php $ruta = base_url(); ?>
<style>
    #tablaresult th {
        font-size: 11px !important;
        padding: 6px 2px;
        text-align: center;
        vertical-align: middle;
    }

    #tablaresult td {
        font-size: 10px !important;
    }
</style>

<!--<script src="<?php echo $ruta; ?>recursos/js/custom.js"></script>-->
<table class='table table-striped dataTable table-bordered' id="tablaresult" name="tablaresult">
    <thead>
    <tr>
        <th>ID</th>
        <th>Tipo</th>
        <th>Documento</th>
        <th>Proveedor</th>
        <th>Fecha Compra</th>
        <th>Monto Venta <?= MONEDA ?></th>
        <th>Monto Pagado <?= MONEDA ?></th>
        <th>Saldo Deuda<?= MONEDA ?></th>
        <th>Días de atraso</th>
        <th>Estado</th>
        <th>Accion</th>

    </tr>
    </thead>
    <tbody>

    </tbody>
</table>

<!-- Seccion Visualizar -->
<div class="modal fade" id="visualizar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>


<!-- Pagar Visualizar -->
<div class="modal fade" id="pagar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<script type="text/javascript">
    $(document).ready(function(){
        TablesDatatablesCuentasPorPagar.init('<?php echo base_url()?>ingresos/lst_cuentas_porpagar_json', 0, false, false);

    });

    var proveedor = '<?php if(isset($proveedor)){ echo $proveedor; }?>';
    var fecha_ini = '<?php if(isset($fecIni)){ echo $fecIni; }  ?>';
    var fecha_fin = '<?php if(isset($fecFin)){ echo $fecFin; }  ?>';


    function pagar_venta(id){

        $.ajax({
            url: '<?= base_url()?>ingresos/ver_deuda',
            type: 'post',
            data: {'id_ingreso': id, 'proveedor': proveedor, 'fecIni': fecha_ini, 'fecFin': fecha_fin},
            success: function (data) {

                $("#pagar_venta").html(data);
                $('#pagar_venta').modal('show');
            }

        })

    }

    function cerrar_visualizar(){

        $('#visualizarPago').modal('hide');
        $('#pagar_venta').modal('hide');
        buscar();
    }
    function visualizar(id){

        $.ajax({
            url: '<?= base_url()?>ingresos/vertodoingreso',
            type: 'post',
            data: {'id_ingreso': id},
            success: function (data) {

                $("#visualizar_venta").html(data);
                $('#visualizar_venta').modal('show');
            }

        })
    }
</script>
<?php $ruta = base_url(); ?>
<!--<script src="<?php echo $ruta; ?>recursos/js/custom.js"></script>-->
<table class='table table-striped dataTable table-bordered  table-condensed' id="lstPagP" name="lstPagP">
    <thead>
    <tr>
        <th title="Tipo Doc">Ingreso ID</th>
        <th title="Tipo Doc">Tipo de Doc.</th>
        <th title="Documento"> Documento</th>
        <th>Proveedor</th>
        <th title="Fecha Registro">Fecha Reg.</th>

        <th title="Total">Monto Ingreso <?php echo MONEDA ?></th>
        <th title="Total">Monto abonado <?php echo MONEDA ?></th>
        <th title="Total">Monto Deudor <?php echo MONEDA ?></th>
        <th>D&iacute;as de atraso</th>
        <th title="Estatus">Estatus</th>
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
<!--- ----------------- -->

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
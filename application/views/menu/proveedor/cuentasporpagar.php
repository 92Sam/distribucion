<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Proveedor</li>
    <li><a href="">Cuentas por Pagar</a></li>
</ul>

<div class="block">
    <form id="frmBuscar">

        <div class="row">
            <div class="col-md-2">
                <label class="control-label panel-admin-text">Proveedores:</label>
            </div>
            <div class="col-md-4">

                <select name="proveedor" id="proveedor" class='cho form-control'>
                    <option value="-1">TODOS</option>
                    <?php if (count($lstproveedor) > 0): ?>
                        <?php foreach ($lstproveedor as $cl): ?>
                            <option
                                value="<?php echo $cl['id_proveedor']; ?>"><?php echo $cl['proveedor_nombre']; ?></option>
                        <?php endforeach; ?>
                    <?php else : ?>
                    <?php endif; ?>
                </select>
            </div>
            <button id="btnBuscar" class="btn btn-default">
            <i class="fa fa-search"></i>
            </button>
        </div>
    </form>

    <br>


    <div class="block-section" id="lstTabla">
        
    </div>
</div>


<div class="modal fade" id="visualizarPago" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<script>


    $(document).ready(function () {

        $('select').chosen();
        $(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});

        $("#proveedor").on('change', function(){
            $("#lstTabla").html('');
        });

        $("#btnBuscar").click(function (e) {
            e.preventDefault()
            buscar();
        });

        buscar();
    });

    function cerrar_detalle_historial() {

        $('#visualizarPago').modal('hide');


    }
    function ver_detalle_pago(id_historial, ingreso_id) {


        $.ajax({
            type: 'POST',
            data: {'id_historial': id_historial, 'ingreso_id': ingreso_id},
            url: '<?php echo base_url();?>' + 'ingresos/imprimir_pago_pendiente',
            success: function (data) {
                $("#visualizarPago").html(data);
                $('#visualizarPago').modal('show');

            }
        });


    }

    function buscar() {

        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'ingresos/lst_cuentas_porpagar',
            success: function (data) {

                $("#lstTabla").html(data);
                $("#abrir_exportar").show();

            }
        });
    }

    function buscartodos() {

        $.ajax({
            type: 'POST',
            data: {'proveedor': -1, 'fecIni': "", 'fecFin': ""},
            url: '<?php echo base_url();?>' + 'ingresos/lst_cuentas_porpagar',
            success: function (data) {
                setTimeout(function () {
                    $("#pdf").attr('href', '<?php echo base_url();?>' + 'ingresos/cuentasporpagarpdf/0/0/-1');
                    $("#excel").attr('href', '<?php echo base_url();?>' + 'ingresos/cuentasporpagarexcel/0/0/-1');
                }, 1);

                $("#lstTabla").html(data);
                $("#abrir_exportar").show();

            }
        });
    }

    
</script>
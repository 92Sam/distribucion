<?php $ruta = base_url(); ?>
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <form id="frmBuscar">
                <div class="block-title">
                    <h3>REPORTE DE LIQUIDACIONES POR CAJERO</h3>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Cajero:</label>
                    </div>
                    <div class="col-md-3">


                        <select name="cajero" id="cajero" class='cho form-control filter-input'>
                            <option value="-1">TODOS</option>
                            <?php if (count($vendedores) > 0): ?>
                                <?php foreach ($vendedores as $vendedor): ?>
                                    <option
                                        value="<?php echo $vendedor->nUsuCodigo; ?>"
                                        id="<?php echo $vendedor->nUsuCodigo; ?>">
                                        <?php echo $vendedor->nombre; ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Desde:</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="fecha_ini" id="fecha_ini" value="<?= date('d-m-Y') ?>"
                               required="true" readonly style="cursor: pointer;"
                               class="form-control fecha input-datepicker filter-input">
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-2">
                        <label class="control-label panel-admin-text">Hasta:</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="fecha_fin" id="fecha_fin" value="<?= date('d-m-Y') ?>"
                               required="true" readonly style="cursor: pointer;"
                               class="form-control fecha input-datepicker filter-input">
                    </div>

                </div>
                <br>
            </form>
        </div>
        <div class="block">

            <div class="row" id="loading" style="display: none;">
                <div class="col-md-12 text-center">
                    <div class="loading-icon"></div>
                </div>
            </div>

            <div id="lstTabla" class=""></div>
        </div>
    </div>
</div>


<div class="modal fade" id="visualizarliquidacion" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>
<script>
    $(document).ready(function () {
        $("#pp_excel").hide();
        $("#pp_pdf").hide();
        $('select').chosen();

        $(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});
        $(".filter-input").on('change', function () {
            buscar();
        });
        buscar();
    });

    function buscar() {

        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'venta/lst_liquidaciones_confirmadas',
            success: function (data) {
                $("#lstTabla").html(data);
            }
        });
    }


    function cerrar() {

        $('#visualizarliquidacion').modal('hide');
        $('#editar').modal('hide');
        buscar();
    }


    function imprimir(id_historial, id_venta, id_liquidacion) {

        $.ajax({
            type: 'POST',
            data: {
                'cajero': $("#cajero").val(),
                'id_historial': id_historial,
                'id_venta': id_venta,
                'id_liquidacion': id_liquidacion
            },
            // dataType: "json",
            url: '<?php echo base_url();?>' + 'venta/imprimir_historial_liquidacion',
            success: function (data) {

                $("#visualizarliquidacion").html(data);
                $('#visualizarliquidacion').modal('show');

            },
            error: function () {

                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Ha ocurrido un error</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
        });

    }
</script>
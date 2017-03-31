<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Ingresos</li>
    <li><a href="">Reporte de Ingreso</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="form-group row">
        <div class="col-md-2">
            Ubicaci&oacute;n
        </div>
        <div class="col-md-4">
            <select id="locales" class="form-control campos" name="locales">
                <option value="seleccione">TODOS</option>
                <?php if (isset($locales)) {
                    foreach ($locales as $local) {
                        ?>
                        <option value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                    <?php }
                } ?>

            </select>

        </div>

        <div class="col-md-2">
            Status
        </div>
        <div class="col-md-4">
            <select id="status" class="form-control campos" name="status">
                <option value="seleccione">TODOS</option>
                <option value="<?= INGRESO_PENDIENTE ?>"><?= INGRESO_PENDIENTE ?></option>
                <option value="<?= INGRESO_COMPLETADO ?>"><?= INGRESO_COMPLETADO ?></option>

            </select>

        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-1">
            <label class="control-label panel-admin-text">Periodo:</label>
        </div>

        <div class="col-md-2">
            <select
                    id="mes"
                    class="form-control campos" name="mes">
                <option value="01" <?= date('m') == '01' ? 'selected' : '' ?>>Enero</option>
                <option value="02" <?= date('m') == '02' ? 'selected' : '' ?>>Febrero</option>
                <option value="03" <?= date('m') == '03' ? 'selected' : '' ?>>Marzo</option>
                <option value="04" <?= date('m') == '04' ? 'selected' : '' ?>>Abril</option>
                <option value="05" <?= date('m') == '05' ? 'selected' : '' ?>>Mayo</option>
                <option value="06" <?= date('m') == '06' ? 'selected' : '' ?>>Junio</option>
                <option value="07" <?= date('m') == '07' ? 'selected' : '' ?>>Julio</option>
                <option value="08" <?= date('m') == '08' ? 'selected' : '' ?>>Agosto</option>
                <option value="09" <?= date('m') == '09' ? 'selected' : '' ?>>Septiembre</option>
                <option value="10" <?= date('m') == '10' ? 'selected' : '' ?>>Octubre</option>
                <option value="11" <?= date('m') == '11' ? 'selected' : '' ?>>Noviembre</option>
                <option value="12" <?= date('m') == '12' ? 'selected' : '' ?>>Diciembre</option>
            </select>
        </div>

        <div class="col-md-2">
            <input type="number" id="year" name="year" value="<?= date('Y') ?>" class="form-control">
        </div>

        <div class="col-md-1">

        </div>


        <div class="col-md-2">
            <label class="control-label panel-admin-text">Rango de Dias</label>
        </div>
        <div class="col-md-1">
            <input type="number" min="1" id="dia_min" name="dia_min" value="1" class="form-control">
        </div>

        <div class="col-md-1">
            <input type="number" min="1" id="dia_max" name="dia_max" value="31" class="form-control">
        </div>

        <div class="col-md-2" style="text-align: right;">
            <button id="btn_buscar" class="btn btn-default" style="padding: 5px 14px !important;">
                <i class="fa fa-search"></i>
            </button>
        </div>

    </div>


    <div class="" id="tabla">


    </div>

    <br>

</div>


<!-- /.modal-dialog -->

<script type="text/javascript">
    $(function () {
        recargarlista();

        $(".fecha").datepicker({
            format: 'dd-mm-yyyy'
        });

        $("#year, #dia_min, #dia_max").bind('keyup change click', function(){
            $("#tabla").html('');
        });

        $(".campos").on('change', function(){
            $("#tabla").html('');
        });


        $("#btn_buscar").on("click", function () {

            recargarlista();

        });

    });

    function recargarlista() {
        var mes = $("#mes").val();
        var year = $("#year").val();
        var dia_min = $("#dia_min").val();
        var dia_max = $("#dia_max").val();
        var locales = $("#locales").val();
        var status = $("#status").val();

        // $("#hidden_consul").remove();

        $.ajax({
            url: '<?= base_url()?>ingresos/get_ingresos',
            data: {
                'id_local': locales,
                'mes': mes,
                'year': year,
                'dia_min': dia_min,
                'dia_max': dia_max,
                'status': status
            },
            type: 'POST',
            success: function (data) {
                // $("#query_consul").html(data.consulta);
                if (data.length > 0)
                    $("#tabla").html(data);
                $("#tablaresult").dataTable();
            },
            error: function () {

                alert('Ocurrio un error por favor intente nuevamente');
            }
        })
    }


</script>

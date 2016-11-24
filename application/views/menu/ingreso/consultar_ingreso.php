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
                <option value="seleccione"> Seleccione</option>
                <?php if(isset($locales)) {
                    foreach($locales as $local){
                        ?>
                        <option selected value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                    <?php }
                } ?>

            </select>

        </div>

        <div class="col-md-2">
            Status
        </div>
        <div class="col-md-4">
            <select id="status" class="form-control campos" name="status">
                <option value="seleccione"> Seleccione</option>
                <option selected value="<?= INGRESO_PENDIENTE ?>"><?= INGRESO_PENDIENTE ?></option>
                <option value="<?= INGRESO_COMPLETADO ?>"><?= INGRESO_COMPLETADO ?></option>

            </select>

        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2">
            Desde
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_desde" id="fecha_desde" required="true" class="form-control fecha campos">
        </div>
        <div class="col-md-2">
            Hasta
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_hasta" id="fecha_hasta" required="true" class="form-control fecha campos">
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
        $(".campos").on("change",function(){

            recargarlista();

        });

    });

    function recargarlista() {
        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        var status = $("#status").val();

        // $("#hidden_consul").remove();

        $.ajax({
            url: '<?= base_url()?>ingresos/get_ingresos',
            data: {
                'id_local': locales,
                'desde': fercha_desde,
                'hasta': fercha_hasta,
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

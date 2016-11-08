<?php $ruta = base_url(); ?>

<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="form-group row">
        <div class="col-md-1">
            Empleado
        </div>

        <div class="col-md-3">

            <select id="trabajadores" class="form-control campos" name="trabajadores">
                <option value="">Seleccione</option>
                <?php
                foreach ($lstUsuario as $empleado) {
                    ?>
                    <option value="<?= $empleado->nUsuCodigo ?>"> <?= $empleado->nombre ?> </option>
                    <?php
                }
                ?>

            </select>

        </div>

        <div class="col-md-1">
            Desde
        </div>
        <div class="col-md-2">
            <input type="text" name="fecha_desde" id="fecha_desde" required="true" value="<?php echo date('d-m-Y') ?>"
                   class="form-control fecha campos">
        </div>
        <div class="col-md-1">
            Hasta
        </div>
        <div class="col-md-2">
            <input type="text" name="fecha_hasta" id="fecha_hasta" required="true" value="<?php echo date('d-m-Y') ?>"
                   class="form-control fecha campos">


        </div>
    </div>
    <div class="box-body" id="tabla">

    </div>
</div>

<script type="text/javascript">
    function VerMapa(id) {

        $("#mapa").load('<?= $ruta ?>puntosReparticion/verMapa/' + id);
        $('#mapa').modal('show');

    }
</script>
<!-- /.modal-dialog -->



<div class="modal fade" id="mapa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<script type="text/javascript">

    $(function () {

        change();
        TablesDatatables.init();
        $(".fecha").datepicker({format: 'dd-mm-yyyy'});
        $(".campos").on("change", function () {

            change();

        });

    });

    function change() {
        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var trabajadores = $("#trabajadores").val();
        // $("#hidden_consul").remove();


        $.ajax({
            url: '<?= base_url()?>puntosReparticion/buscarReparticion',
            data: {
                'trabajador': trabajadores,
                'desde': fercha_desde,
                'hasta': fercha_hasta

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

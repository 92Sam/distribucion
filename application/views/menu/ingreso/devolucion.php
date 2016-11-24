<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Ingresos</li>
    <li><a href="">Reporte de Ingreso</a></li>
</ul>
<div class="block">
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-danger alert-dismissable"
                 style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                <h4><i class="icon fa fa-ban"></i> Error</h4>
                <?php echo isset($error) ? $error : '' ?></div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-success alert-dismissable"
                 style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
                <?php echo isset($success) ? $success : '' ?>
            </div>
        </div>
    </div>
    <?php
    echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
    ?>
    <!-- Progress Bars Wizard Title -->
    <div class="form-group row">
        <div class="col-md-2">
            Ubicaci&oacute;n
        </div>
        <div class="col-md-3">
            <select id="locales" class="form-control campos" name="locales">
                <option value="seleccione"> Seleccione</option>
                <?php if(isset($locales)) {
                    foreach($locales as $local){
                        ?>
                        <option value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                    <?php }
                } ?>

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


    <input type="hidden" name="anular" id="anular" value=1>

    <div  id="tabla">


    </div>

    <br>

</div>



<!-- /.modal-dialog -->

<script type="text/javascript">
    $(function () {
        recargarlista();
        TablesDatatables.init();
        $(".fecha").datepicker({
            format: 'dd-mm-yyyy'
        });
        $(".campos").on("change",function(){

            recargarlista();

        });

    });

    function recargarlista() {
        var anular = 0;
        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        if ($("#anular").length > 0) {

            var anular = 1;
        }
        // $("#hidden_consul").remove();

        $.ajax({
            url: '<?= base_url()?>ingresos/get_ingresos',
            data: {
                'id_local': locales,
                'desde': fercha_desde,
                'hasta': fercha_hasta,
                'anular': anular
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

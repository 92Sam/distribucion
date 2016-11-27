<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Cleintes por atender</li>
</ul>
<div class="block">

    <div class="form-group row">
        <div class="col-md-2">Desde
            <input type="text" name="fecha_desde" id="fecha_desde" required="true" value="<?php echo date('d-m-Y')?>" class="form-control fecha campos">

        </div>

        <div class="col-md-2">
            Hasta<input type="text" name="fecha_hasta" id="fecha_hasta" value="<?php echo date('d-m-Y')?>" required="true"
                        class="form-control fecha campos">
        </div>
    </div>


    <div class="box-body" id="tabla">
        
    </div>
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

    function change(){
        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();

        // $("#hidden_consul").remove();


        $.ajax({
            url: '<?= base_url()?>mapaVentas/puntoReparticion',
            data: {
                'desde': fercha_desde,
                'hasta': fercha_hasta
            },
            type: 'POST',
            success: function (data) {
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

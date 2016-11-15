<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li><a href="">Reporte de Utilidades <?php if(isset($productos)){ echo "Por Productos"; } ?>
            <?php if(isset($cliente)){ echo "Por Clientes"; } ?>
            <?php if(isset($proveedor)){ echo "Por Proveedor"; } ?></a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="form-group row">
        <div class="col-md-2">
            Ubicaci&oacute;n
        </div>
        <div class="col-md-3">
            <select id="locales" class="form-control campos" name="locales">
                <option value=""> Seleccione</option>
                <?php if (isset($locales)) {
                    foreach ($locales as $local) {
                        ?>
                        <option selected value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                    <?php }
                } ?>

            </select>

        </div>
    </div>
    <?php if(isset($todo)){ ?>  <input type="hidden" id="utilidades" value="TODOS">  <?php }?>
    <?php if(isset($productos)){ ?>  <input type="hidden" id="utilidades" value="PRODUCTOS">  <?php }?>
    <?php if(isset($cliente)){ ?>  <input type="hidden" id="utilidades" value="CLIENTE">  <?php }?>
    <?php if(isset($proveedor)){ ?>  <input type="hidden" id="utilidades" value="PROVEEDOR">  <?php }?>
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


    <div class="box-body" id="tabla">
        <div class="table-responsive">
            <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                <thead>
                <?php if(isset($todo)){?>
                <tr>
                    <th > Fecha y Hora</th>
                    <th>C&oacute;digo</th>
                    <th>N&uacute;mero</th>
                    <th>Cantidad</th>
                    <th>Producto</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Costo</th>
                    <th>Precio</th>
                    <th>Utilidad</th>
                    <th>Producto</th>
                    <th>C&oacute;digo</th>
                    <th>N&uacute;mero</th>
                    <th>Cantidad</th>
                </tr>
                <?php }elseif(isset($productos)){?>
                    <th>C&oacute;digo</th>
                    <th>Producto</th>
                    <th>Utilidad</th>
                <?php }elseif(isset($cliente)){?>
                    <th>C&oacute;digo</th>
                    <th>Cliente</th>
                    <th>Utilidad</th>
                <?php }
                elseif(isset($proveedor)){?>
                    <th>C&oacute;digo</th>
                    <th>Proveedor</th>
                    <th>Utilidad</th>
                <?php }
                ?>
                </thead>
                <tbody></tbody>
            </table>

        </div>

        <br>

    </div>

</div>



<!-- /.modal-dialog -->

<script type="text/javascript">
    $(function () {

        TablesDatatables.init();
        traer();

        $(".fecha").datepicker({format: 'dd-mm-yyyy'});
        $(".campos").on("change", function () {

                traer();
        });

    });

    function traer(){
        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        var utilidades = $("#utilidades").val();
        // $("#hidden_consul").remove();


        $.ajax({
            url: '<?= base_url()?>venta/getUtiidadesVentas',
            data: {
                'id_local': locales,
                'desde': fercha_desde,
                'hasta': fercha_hasta,
                'utilidades':utilidades
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

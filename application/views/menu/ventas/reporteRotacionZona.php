<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Reporte</li>
    <li><a href="">Reporte de Rotacion por Zona</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="form-group row">
        <div class="col-md-1">
            Ubicaci&oacute;n
        </div>

        <div class="col-md-3">
            <select id="zonas" class="form-control campos" name="zonas">
                <option value="TODAS"> TODAS</option>
                <?php if (isset($zonas)) {
                    foreach ($zonas as $zona) {
                        ?>
                        <option value="<?= $zona['zona_id']; ?>"> <?= $zona['zona_nombre'] ?> </option>

                    <?php }
                } ?>
            </select>

        </div>

        <div class="col-md-1">
            Desde
        </div>

        <div class="col-md-2">
            <input type="text" name="desde" id="desde" class="form-control input-datepicker campos">

        </div>
        <div class="col-md-1">
            Hasta
        </div>

        <div class="col-md-2">
            <input type="text" name="hasta" id="hasta" class="form-control input-datepicker campos">

        </div>
    </div>

    <div class="box-body" id="tabla">
        <div class="table-responsive">
            <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                <thead>
                <tr>
                    <th>Distrito</th>
                    <th>Urbanización</th>
                    <th>Vendedor</th>
                    <th>Grupo</th>
                    <th>Familia</th>
                    <th>Linea</th>
                    <th>Producto</th>
                    <th>Cantidad Vendida</th>

                    <th>Unidad</th>
                    <th>Clientes</th>
                </tr>
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

        change();

        TablesDatatables.init();

        $(".campos").on("change", function () {

            change();
        });

    });

    function change(){
        var zonas = $("#zonas").val();
        var desde = $("#desde").val();
        var hasta = $("#hasta").val();
        console.log(zonas);

        $.ajax({
            url: '<?= base_url()?>venta/getProductoZona',
            data: {
                'id_zona': zonas,
                'desde': desde,
                'hasta': hasta,
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

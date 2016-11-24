<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Venta</li>
    <li><a href="">Reporte de Venta</a></li>
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
                <?php
                if (isset($locales)) {
                    foreach ($locales as $local) {
                        echo '<option value="' . $local['int_local_id'] . '">' . $local['local_nombre'] . '</option>';
                    }
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2">
            Desde
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_desde" id="fecha_desde" value="<?= date('d-m-Y'); ?>" required="true"
                   class="form-control fecha campos input-datepicker ">
        </div>
        <div class="col-md-2">
            Hasta
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_hasta" id="fecha_hasta" value="<?= date('d-m-Y'); ?>" required="true"
                   class="form-control fecha campos input-datepicker">
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-2">
            Estatus
        </div>
        <div class="col-md-3">
            <select id="estatus" class="form-control campos" name="estatus">
                <option value="">SELECCIONE</option>
                <option value="COMPLETADO">COMPLETADO</option>
                <option value="EN ESPERA">EN ESPERA</option>
                <option value="ANULADO">ANULADO</option>
                <option value="DEVUELTO">DEVUELTO</option>
            </select>
        </div>
    </div>

    <input type="hidden" name="listar" id="listar" value="ventas">

    <div class="box-body" id="tabla">
        <div class="table-responsive">
            <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                <thead>
                <tr>
                    <th>N&uacute;mero de Venta</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>Fecha</th>
                    <th>Tipo de Documento</th>
                    <th>Estatus</th>
                    <th>Local</th>
                    <th>Condici&oacute;n Pago</th>
                    <th> Total</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
        </br>
    </div>
</div>

<script src="<?php echo $ruta; ?>recursos/editable/jquery.jeditable.js"></script>



<script type="text/javascript">
    $(function () {

        TablesDatatables.init();
        get_ventas();

        $(".campos").on("change", function () {

            get_ventas();

        });


    });

    function get_ventas() {
        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        var estatus = $("#estatus").val();
        var listar = $("#listar").val();

        $.ajax({
            url: '<?php echo $ruta ?>venta/get_ventas',
            data: {
                'id_local': locales,
                'desde': fercha_desde,
                'hasta': fercha_hasta,
                'estatus': estatus,
                'listar': listar
            },
            type: 'POST',
            success: function (data) {
                if (data.length > 0) {
                    $("#tabla").html(data);
                }
                $("#tablaresult").dataTable();
            },
            error: function () {
                alert('Ocurrio un error por favor intente nuevamente');
            }
        })
    }
</script>

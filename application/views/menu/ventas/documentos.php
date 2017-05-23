<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Venta</li>
    <li><a href="">Documentos de Venta</a></li>
</ul>

<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="form-group row">
        <div class="col-md-3">
            <label>Ubicaci&oacute;n</label>
            <select id="locales" class="form-control campos" name="locales">
                <?php
                if (isset($locales)) {
                    foreach ($locales as $local) {
                        echo '<option value="' . $local['int_local_id'] . '">' . $local['local_nombre'] . '</option>';
                    }
                }
                ?>
            </select>
        </div>

        <div class="col-md-3">
            <label>Desde</label>
            <input type="text" name="fecha_desde" id="fecha_desde" value="<?= date('d-m-Y'); ?>" required="true"
                   class="form-control fecha campos input-datepicker " readonly style="cursor: pointer;">
        </div>

        <div class="col-md-3">
            <label>Hasta</label>
            <input type="text" name="fecha_hasta" id="fecha_hasta" value="<?= date('d-m-Y'); ?>" required="true"
                   class="form-control fecha campos input-datepicker" readonly style="cursor: pointer;">
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-3">
            <label>Documento</label>
            <select id="tipo_doc" class="form-control campos" name="tipo_doc">
                <option value="">TODOS</option>
                <option value="3">BOLETA DE VENTA</option>
                <option value="1">FACTURA</option>
            </select>
        </div>

        <div class="col-md-3">
            <label>No. Consolidado</label>
            <input type="number" name="consolidado" id="consolidado" value=""
                   class="form-control">

        </div>

        <div class="col-md-3">
            <label>No. Pedido</label>
            <input type="number" name="pedido" id="pedido" value=""
                   class="form-control">
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-1 text-right">
            <button type="button" title="Buscar" id="filter" class="btn btn-default form-control btn_buscar">
                <i class="fa fa-search"></i>
            </button>
        </div>

    </div>



    <input type="hidden" name="listar" id="listar" value="ventas">

    <div class="box-body" id="tabla">

    </div>
</div>

<script type="text/javascript">
    $(function () {


        get_documentos();
        $("#filter").on("click", function () {

            get_documentos();

        });

        $("#locales, #tipo_doc, #fecha_desde, #fecha_hasta").on('change', function(){
            $("#tabla").html('');
        });

        $('#pedido, #consolidado').bind('keyup change click mouseleave', function(){
            $("#tabla").html('');
        });


    });

    function get_documentos() {
        var fercha_desde = $("#fecha_desde").val();
        var fercha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        var pedido = $("#pedido").val();
        var consolidado = $("#consolidado").val();
        var tipo_doc = $("#tipo_doc").val();

        $.ajax({
            url: '<?php echo $ruta ?>venta/get_documentos',
            data: {
                'local_id': locales,
                'fecha_ini': fercha_desde,
                'fecha_fin': fercha_hasta,
                'tipo_doc': tipo_doc,
                'consolidado': consolidado,
                'pedido': pedido,
            },
            type: 'POST',
            success: function (data) {
                    $("#tabla").html(data);
            },
            error: function () {
                alert('Ocurrio un error por favor intente nuevamente');
            }
        });
    }
</script>

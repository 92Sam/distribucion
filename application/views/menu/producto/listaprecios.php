<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Productos</li>
    <li><a href="">Listado de Precios y Productos</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->


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
        <div class="col-md-2">
            <span>Producto con Precios</span>
        </div>
        <div class="col-md-1">
            <input type="radio" id="pago1" CHECKED value=1 name="filtroPrecio"/>
        </div>

        <div class="col-md-2">
            <span>Producto sin Precios</span>
        </div>
        <div class="col-md-1">
            <input type="radio" id="pago2" value=0 name="filtroPrecio"/>

        </div>


    </div>
    <BR>

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

    <div class="table-responsive" id="productostable">

        <table class='table table-striped dataTable table-bordered' id="table">
            <thead>
            <tr>

                <th>ID Producto</th>
                <th>Nombre</th>
                <th>Grupo</th>
                <?php

                $bandera = "LISTA_PRECIOS";
                foreach ($precios as $precio) { ?>
                    <th class=""><?= $precio['nombre_precio'] ?></th>
                <?php } ?>
            </tr>
            </thead>
            <tbody id="tbody">

            </tbody>
        </table>

    </div>

</div>

<br>
<a href="<?= $ruta ?>producto/pdf/1" id="generarpdf" class="btn  btn-default btn-lg" data-toggle="tooltip"
   title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta ?>producto/excel/1" id="generarexcel" class="btn btn-default btn-lg"
   data-toggle="tooltip"
   title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
        class="fa fa-file-excel-o fa-fw"></i></a>



<script>
    $(document).ready(function () {

        $.ajax({	//create an ajax request to load_page.php
            type: "POST",
            url: '<?php echo base_url(); ?>inicio/very_sesion',
            dataType: "json",	//expect html to be returned
            success: function (data) {

                // console.log($("#inicrefresh").length);
                if (data == false)	//if no errors
                {
                    alert('El tiempo de su sessión ha expirado');
                    location.href = '<?php echo base_url() ?>inicio';
                } else {
                    TablesDatatablesListaPrecios.init('<?php echo base_url()?>producto/listaprecios_json', 0, false, false);
                }
            }
        });

        $('input[name=filtroPrecio]').change(function () {


            if ($('#pago1').is(':checked')) {
                var link = $('#generarpdf').attr('href', '<?= $ruta ?>producto/pdf/1');
                var links = $('#generarexcel').attr('href', '<?= $ruta ?>producto/excel/1');


            }
            if ($('#pago2').is(':checked')) {

                var link = $('#generarpdf').attr('href', '<?= $ruta ?>producto/pdf/0');
                var links = $('#generarexcel').attr('href', '<?= $ruta ?>producto/excel/0');
            }

            TablesDatatablesListaPrecios.init('<?php echo base_url()?>producto/listaprecios_json', 0, 'table', false);


        });

    });

</script>


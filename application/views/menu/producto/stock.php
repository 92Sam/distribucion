<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Stock</a></li>
</ul>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>
<!--
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable" id="error"
             style="display:<?php //echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Error</h4>
            <span id="errorspan"><?php //echo isset($error) ? $error : '' ?></div>
    </div>
</div>-->

<div class="block">

    <div class="row">
       <!-- <div class="col-md-1">
            <a class="btn btn-primary" onclick="agregar();">
                <i class="fa fa-plus "> Nuevo</i>
            </a>
        </div>
        <div class="col-md-1">
            <a class="btn btn-default" onclick="duplicar();">
                <i class="fa fa-angle-double-up "> Duplicar</i>
            </a>
        </div>-->

        <div class="col-md-1">
            <a class="btn btn-info" onclick="unidadesycostos();">
                <i class="fa fa-list-ol"> UM y Precios</i>
            </a>
        </div>
        <!--<div class="col-md-1">
            <a class="btn btn-default" onclick="confirmar();">
                <i class="fa fa-remove"> Eliminar</i>
            </a>
        </div>
        <div class="col-md-1 justifyright">
            <a class="btn btn-default" onclick="columnas();">
                <i class="fa fa-columns "> Columnas</i>
            </a>
        </div>-->
    </div>
    <br>


    <div class="table-responsive" id="productostable" >
        <table class='table table-striped dataTable table-bordered'  id="table">
            <thead>
            <tr>
                <?php foreach ($columnas as $col): ?>
                    <?php if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_activo') echo " <th>" . $col->nombre_mostrar . "</th>" ?>
                <?php endforeach; ?>
                <th>UM</th>
                <th>Cantidad</th>
                <th>Fracci&oacute;n</th>
                <th>Activo</th>

            </tr>
            </thead>
            <tbody id="tbody" >



            </tbody>
        </table>

    </div>
    <a href="<?= $ruta ?>producto/pdfStock" class="btn  btn-danger btn-lg" data-toggle="tooltip" title="Exportar a PDF"
       data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

    <a href="<?= $ruta ?>producto/excelStock" class="btn btn-default btn-lg" data-toggle="tooltip"
       title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
            class="fa fa-file-excel-o fa-fw"></i></a>
</div>


<div class="modal fade" id="productomodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="modal fade" id="columnas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


</div>



<script type="text/javascript">

    function unidadesycostos() {
        var id = $("#tbody tr.ui-selected td:first").html();
        if(id!=undefined) {
            $("#productomodal").html($('#loading').html());
            $("#productomodal").load('<?= $ruta ?>producto/verunidades/' + id);
            $('#productomodal').modal('show');
        }
    }



</script>

<!-- Load and execute javascript code used only in this page -->


<script>$(function () {
        $("select").chosen();
        TablesDatatablesJsonProdcutos.init('<?php echo base_url()?>producto/get_by_json',0,false,true);
        $("#tbody").selectable({
            stop: function () {

                var id = $("#tbody tr.ui-selected td:first").html();

            }
        });


    });</script>

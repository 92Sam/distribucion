<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Productos</a></li>
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
        <div class="col-md-1 justifyright">
            <a class="btn btn-primary" onclick="agregar();">
                <i class="fa fa-plus "> Nuevo</i>
            </a>
        </div>
        <div class="col-md-1 justifyright">
            <a class="btn btn-default" onclick="duplicar();">
                <i class="fa fa-angle-double-up "> Duplicar</i>
            </a>
        </div>

        <div class="col-md-1 justifyright">
            <a class="btn btn-default" onclick="editarProducto();">
                <i class="fa fa-edit"> Editar</i>
            </a>
        </div>
        <div class="col-md-1 justifyright">
            <a class="btn btn-default" onclick="confirmar();">
                <i class="fa fa-remove"> Eliminar</i>
            </a>
        </div>
        <div class="col-md-1 justifyright">
            <a class="btn btn-default" onclick="columnas();">
                <i class="fa fa-columns "> Columnas</i>
            </a>
        </div>
    </div>


    <!--
    <div class="row">
        <div class="form-group">
            <div class="col-md-1">
                <label>Ubicaci&oacute;n Inventario</label>
            </div>
            <div class="col-md-5">
                <select class="form-control" id="locales" onchange="getproductosbylocal()">

                    <?php foreach ($locales as $local) { ?>
                        <option value="<?= $local['int_local_id'] ?>"><?= $local['local_nombre'] ?></option>
                    <?php } ?>

                </select>
            </div>
        </div>
    </div>
    -->
    <br>

    <div class="table-responsive" id="productostable">
        <table class='table table-striped dataTable table-bordered' id="table">
            <thead>
            <tr>
                <?php foreach ($columnas as $col): ?>

                <?php if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_activo'){
                        if($col->nombre_mostrar == "Sub Grupo"){
                            echo '<th>Linea</th>';
                        }elseif($col->nombre_mostrar == 'Familia') {
                            echo '<th>Sub Linea</th>';
                        }elseif($col->nombre_mostrar == 'Linea') {
                            echo '<th>Talla</th>';
                        }else{
                            echo " <th>" .$col->nombre_mostrar. "</th>";
                        }
                    }
                ?>
                <?php endforeach; ?>
                <th>Estado</th>


            </tr>
            </thead>
            <tbody id="tbody">


            </tbody>
        </table>
    </div>


</div>


<div class="modal fade" id="productomodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="modal fade" id="columnas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


</div>


<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>producto/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h3 class="modal-title">Eliminar Producto</h3>
                </div>
                <div class="modal-body">
                    <h4>Est&aacute; seguro que desea eliminar el producto seleccionado</h4>
                    <input type="hidden" name="id" id="id_borrar">

                </div>
                <div class="modal-footer">
                    <button type="button" id="botoneliminar" class="btn btn-primary" onclick="eliminar()">
                        <li class="glyphicon glyphicon-thumbs-up"></li> Confirmar
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar
                        <li class="glyphicon glyphicon-thumbs-down"></li>
                    </button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>

<script type="text/javascript">


    function agregar() {
        $("#productomodal").load('<?= $ruta ?>producto/agregar');
        $('#productomodal').modal({show: true, keyboard: false, backdrop: 'static'});
    }


    function editarProducto() {
        var id = $("#tbody tr.ui-selected td:first").html();
        if (id != undefined) {
            $("#productomodal").load('<?= $ruta ?>producto/agregar/' + id);
            $('#productomodal').modal({show: true, keyboard: false, backdrop: 'static'});
        }
    }


    function duplicar() {
        var id = $("#tbody tr.ui-selected td:first").html();
        if (id != undefined) {
            $("#productomodal").load('<?= $ruta ?>producto/agregar/' + id, {'duplicar': 1});
            $('#productomodal').modal({show: true, keyboard: false, backdrop: 'static'});
        }
    }

    function columnas() {
        $("#columnas").load('<?= $ruta ?>producto/editcolumnas');
        $('#columnas').modal({show: true, keyboard: false, backdrop: 'static'});
    }

    function confirmar() {
        var id = $("#tbody tr.ui-selected td:first").html();
        if (id != undefined) {
            $('#borrar').modal('show');
            $("#id_borrar").attr('value', id);
        }

    }

    function eliminar() {

        App.formSubmitAjax($("#formeliminar").attr('action'), getproductosbylocal, 'borrar', 'formeliminar');

    }

    function getproductosbylocal() {
        TablesDatatablesJsonProdcutos.init('<?php echo base_url()?>producto/get_by_json', 0, false, false);
    }

</script>



<script>
    $(function() {
        $("select").chosen();
        //TablesDatatables.init();
        TablesDatatablesJsonProdcutos.init('<?php echo base_url()?>producto/get_by_json', 0, false, false);
        $("#tbody").selectable({
            stop: function () {

                var id = $("#tbody tr.ui-selected td:first").html();
                //  console.log(id);
            }
        });
    });</script>

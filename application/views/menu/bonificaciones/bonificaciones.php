<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Bonificaciones</li>
    <li><a href="">Agregar y editar Bonificaciones</a></li>
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
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
?>
<div class="block">
    <!-- Progress Bars Wizard Title -->


    <a class="btn btn-primary" onclick="agregar();">
        <i class="fa fa-plus "> Nueva</i>
    </a>
    <br>

    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>

                <th>ID</th>
                <th>Vencimiento</th>
                <th>Estado</th>
                <th>Productos</th>
                <th>Marca Condición</th>
                <th>Grupo Condición</th>
                <th>Linea</th>
                <th>Sub Linea</th>
                <th>Sub Familia Condición</th>
                <th>Talla</th>
                <th>Unidad Condición</th>


                <th>Cantidad Condición</th>
                <th>Bono Producto</th>

                <th>Bono Unidad</th>
                <th>Bono Cantidad</th>
                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($bonificacioness) > 0) {

                foreach ($bonificacioness as $bonificaciones) {
                    ?>
                    <tr>

                        <td class="center"><?= $bonificaciones['id_bonificacion'] ?></td>
                        <td><?= $bonificaciones['fecha'] ?></td>
                        <td><?php $days = (strtotime(date('d-m-Y')) - strtotime($bonificaciones['fecha'])) / (60 * 60 * 24);
                            if ($days < 0)
                                $days = 0; ?>
                            <div><label class="label
                            <?php if (floor($days) <= 0) {
                                    echo "label-success";

                                } else {
                                    echo "label-danger";
                                } ?> "> <?php if (floor($days) <= 0) {
                                        echo "Activa";

                                    } else {
                                        echo "Vencida";
                                    } ?></label>
                            </div>
                        </td>
                        <td style="width: 30%;">
                            <?php /*echo '<a class="btn btn-default" data-toggle="tooltip"
                                     title="Ver Productos" data-original-title="fa fa-eye"
                                     onclick="verproductos(' . $bonificaciones['id_bonificacion'] . ');">';
 */ ?>
                          <!--  <i class="fa fa-eye"></i>
                            </a>-->

                            <?php

                                foreach($bonificaciones['bonificaciones_has_producto'] as $produc){
                                    echo sumCod($produc['id_producto']). " ".$produc['producto_nombre'];?>
                                    <br>
                                    <?php
                                }
                            ?>

                        </td>
                        <td><?= $bonificaciones['nombre_marca'] ?></td>
                        <td><?= $bonificaciones['nombre_grupo'] ?></td>
                        <td><?= $bonificaciones['nombre_subgrupo'] ?></td>
                        <td><?= $bonificaciones['nombre_familia'] ?></td>
                        <td><?= $bonificaciones['nombre_subfamilia'] ?></td>
                        <td><?= $bonificaciones['nombre_linea'] ?></td>
                        <td><?= $bonificaciones['nombre_unidad'] ?></td>
                        <td><?= $bonificaciones['cantidad_condicion'] ?></td>
                        <td><?= $bonificaciones['producto_bonificacion'] ?></td>
                        <td><?= $bonificaciones['unidad_bonificacion'] ?></td>
                        <td><?= $bonificaciones['bono_cantidad'] ?></td>

                        <td class="center">
                            <div class="btn-group">

                                <a class="btn btn-default" data-toggle="tooltip" title="Editar"
                                   data-original-title="fa fa-comment-o"
                                   href="#"
                                   onclick="editar('<?php echo $bonificaciones["id_bonificacion"] ?>','<?php echo isset($bonificaciones['producto_id']) ? $bonificaciones['producto_id'] : "false" ?>','<?php echo $bonificaciones['bono_producto'] ?>')">
                                    <i class="fa fa-edit"></i>
                                </a>

                                <?php echo '<a class="btn btn-default" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o"
                                     onclick="borrar(' . $bonificaciones['id_bonificacion'] . ');">'; ?>
                                <i class="fa fa-trash-o"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>

            </tbody>
        </table>
    </div>
</div>


<!-- Modales for Messages -->
<div class="modal hide" id="mOK">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="javascript:window.location.reload();">
        </button>
        <h3>Notificaci&oacute;n</h3>
    </div>
    <div class="modal-body">
        <p>Registro Exitosa</p>
    </div>
    <div class="modal-footer">
        <a href="#" class="btn btn-primary" data-dismiss="modal"
           onclick="javascript:window.location.reload();">Close</a>
    </div>
</div>

</div>



<script type="text/javascript">

    function borrar(id) {

        $('#borrar').modal({show: true, keyboard: false, backdrop: 'static'});
        $("#id_borrar").attr('value', id);
    }

    function editar(id, p1, p2) {

        $("#agregar").load('<?= $ruta ?>bonificaciones/form/' + id + '/' + p1 + '/' + p2);
        $('#agregar').modal({show: true, keyboard: false, backdrop: 'static'});
    }

    function verproductos(id) {

        $("#verproductos").load('<?= $ruta ?>bonificaciones/verproductos/' + id);
        $('#verproductos').modal({show: true, keyboard: false, backdrop: 'static'});
    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>bonificaciones/form');
        $('#agregar').modal({show: true, keyboard: false, backdrop: 'static'});
    }

    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>bonificaciones'

            })
        },
        guardar: function () {
            if ($("#fecha_bonificacion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la fecha</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#cantidad_condicion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar la cantidad</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if (($("#producto_condicion").val() == '') && ($("#familia_condicion").val() == '') &&
                ($("#grupo_condicion").val() == '') && ($("#marca_condicion").val() == '') && ($("#linea_condicion").val() == '')
                && ($("#subgrupos").val() == '') && ($("#subfamilia").val() == '')
            ) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar por lo menos un tipo de bonificación</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;

            }


            if (($("#producto_condicion").val() != '') && ($("#unidad_condicion").val() == ''))
            {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la unidad de la bonificación</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;

            }



            if ($("#bono_cantidad").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar la cantidad</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#bono_producto").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el producto del bono</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#bono_unidad").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la unidad del bono</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            App.formSubmitAjax($("#formagregar").attr('action'), this.ajaxgrupo, 'agregar', 'formagregar');
        }

    }
    function eliminar() {

        App.formSubmitAjax($("#formeliminar").attr('action'), grupo.ajaxgrupo, 'borrar', 'formeliminar');
    }

</script>

<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<div class="modal fade" id="verproductos" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>
<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>bonificaciones/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Bonificación</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar la Bonificación seleccionada?</p>
                    <input type="hidden" name="id" id="id_borrar">
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmar" class="btn btn-primary" onclick="eliminar()">Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>
<!-- /.modal-dialog -->
</div>

<!--<script src="<?php echo $ruta; ?>recursos/js/jquery-ui.js"></script> -->


<script>$(function() {
        TablesDatatablesBonos.init();
    });</script>



<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Gastos</li>
    <li><a href="">Agregar y editar Gastos</a></li>
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
        <i class="fa fa-plus "> Nuevo</i>
    </a>
    <br>

    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>

                <th>ID</th>
                <th>Fecha</th>
                <th>Descripci&oacute;n</th>
                <th>Tipo de Gasto</th>
                <th>Local</th>
                <th>Total</th>

                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($gastoss) > 0) {

                foreach ($gastoss as $gastos) {
                    ?>
                    <tr>

                        <td class="center"><?= $gastos['id_gastos'] ?></td>
                        <td><?= $gastos['fecha'] ?></td>
                        <td><?= $gastos['descripcion'] ?></td>
                        <td><?= $gastos['nombre_tipos_gasto'] ?></td>
                        <td><?= $gastos['local_nombre'] ?></td>
                        <td><?= $gastos['total'] ?></td>

                        <td class="center">
                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Editar" data-original-title="fa fa-comment-o"
                                            href="#" onclick="editar(' . $gastos['id_gastos'] . ');">'; ?>
                                <i class="glyphicon glyphicon-edit"></i>
                                </a>
                                <?php echo '<a class="btn btn-danger" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o"
                                     onclick="borrar(' . $gastos['id_gastos'] . ');">'; ?>
                                <i class="glyphicon glyphicon-trash"></i>
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
</div>



<script type="text/javascript">

    function borrar(id, nom) {

        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
    }


    function editar(id) {

        $("#agregar").load('<?= $ruta ?>gastos/form/' + id);
        $('#agregar').modal('show');
    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>gastos/form');
        $('#agregar').modal('show');
    }


    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>gastos'

            })
        },
        guardar: function () {
            if ($("#fecha").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la fecha</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#descripcion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar la descripción</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#descripcion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar el monto gastado</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#tipo_gasto").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el tipo de gasto</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            if ($("#local_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el local</h4>', {
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

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>gastos/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Gasto</h4>
                </div>
                <div class="modal-body">
                    <h4>Est&aacute; seguro que desea eliminar el Gasto seleccionado?</h4>
                    <input type="hidden" name="id" id="id_borrar">
                </div>
                <div class="modal-footer">
                          <button type="button" id="confirmar" class="btn btn-primary" onclick="eliminar()">
                          <li class="glyphicon glyphicon-thumbs-up"></li> Guardar</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                    <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>
<!-- /.modal-dialog -->
</div>

<script>$(function () {
        TablesDatatables.init();
    });</script>
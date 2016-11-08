<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Cajas</li>
    <li><a href="">Agregar y Editar Cajas</a></li>
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
                <th>Saldo</th>
                <th>Local</th>
                <th>Activo</th>
                <th>Cuenta Contable</th>
                <th>Responsable</th>
                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($cajas) > 0) {

                foreach ($cajas as $caja) {
                    ?>
                    <tr>

                        <td class="center"><?= $caja['caja_id'] ?></td>
                        <td><?= $caja['caja_saldo'] ?></td>
                        <td><?= $caja['local_nombre'] ?></td>
                        <td><?php if($caja['activo']=='1') echo 'Si'; else echo 'No'; ?></td>
                        <td><?= $caja['cuenta_contable'] ?></td>
                        <td><?= $caja['nombre'] ?></td>
                       
                        <td class="center">
                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Editar" data-original-title="fa fa-comment-o"
                                            href="#" onclick="editar(' . $caja['caja_id'] . ');">'; ?>
                                <i class="fa fa-edit"></i>
                                </a>
                                <?php echo '<a class="btn btn-default" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o"
                                     onclick="borrar(' . $caja['caja_id'] . ');">'; ?>
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
</div>


<script type="text/javascript">

    function borrar(id) {

        $('#borrar').modal('show');
        $("#id_borrar").attr('value', id);
    }

    function editar(id) {

        $("#agregar").load('<?= $ruta ?>cajas/form/' + id);
        $('#agregar').modal('show');
    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>cajas/form');
        $('#agregar').modal('show');
    }

    function usuarios(id) {
        console.log(id);
        $("#users").load('<?= $ruta ?>cajas/usuarios/' +id);
        $('#users').modal('show');
    }

    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>cajas'

            })
        },
        guardar: function () {
            if ($("#local").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el local</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#cuenta_contable").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar la cuenta contable</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#responsable").val() == '')  {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el responsable</h4>', {
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

<div class="modal fade bs-example-modal-lg" id="users" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>cajas/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Caja</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar la Caja seleccionada?</p>
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


<script>$(function () {
        TablesDatatables.init();
    });</script>



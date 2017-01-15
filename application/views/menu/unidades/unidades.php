<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Unidades</li>
    <li><a href="">Agregar y editar Unidades</a></li>
</ul>

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
    <!-- Progress Bars Wizard Title -->


    <a class="btn btn-primary" onclick="agregargrupo();">
        <i class="fa fa-plus "> Nueva</i>
    </a>
    <br>


    <?= validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
    ?>
    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>

                <th>ID</th>
                <th>Nombre</th>
                <th>Abreviatura</th>

                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody>
            <?php if (count($unidades) > 0) :?>
                <?php foreach ($unidades as $unidad):?>
                    <tr>
                        <td class="center"><?= $unidad['id_unidad'] ?></td>
                        <td><?= $unidad['nombre_unidad'] ?></td>
                        <td><?= $unidad['abreviatura'] ?></td>

                        <td class="center">
                            <div class="btn-group">
                                <?= '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Editar" data-original-title="Editar"
                                            href="#"
                                            onclick="editargrupo(' . $unidad['id_unidad'] . ');">'; ?>
                                <i class="fa fa-edit"></i>
                                </a>
                                <?= '<a class="btn btn-danger" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="Eliminar"
                                     onclick="borrargrupo(' . $unidad['id_unidad'] . ',\'' . $unidad['nombre_unidad'] . '\');">'; ?>
                                <i class="fa fa-trash-o"></i>
                                </a>

                            </div>
                        </td>
                    </tr>
                <?php endforeach;?>
            <?php endif;?>

            </tbody>
        </table>
    </div>
</div>


</div>
</div>



<script type="text/javascript">

    function borrargrupo(id, nom) {

        $('#borrargrupo').modal('show');
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
    }


    function editargrupo(id) {
        $("#agregargrupo").load('<?= $ruta ?>unidades/form/'+id);
        $('#agregargrupo').modal('show');
    }

    function agregargrupo() {
        $("#agregargrupo").load('<?= $ruta ?>unidades/form');
        $('#agregargrupo').modal('show');
    }


    var grupo = {
        ajaxgrupo : function(){
            return  $.ajax({
                url:'<?= base_url()?>unidades'

            })
        },
        guardar : function () {
            if ($("#nombre").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el nombre</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            App.formSubmitAjax($("#formagregar").attr('action'), this.ajaxgrupo, 'agregargrupo', 'formagregar');
        }
    }

    function eliminar(){

        App.formSubmitAjax($("#formeliminar").attr('action'), grupo.ajaxgrupo, 'borrargrupo', 'formeliminar' );

    }

</script>
<div class="modal fade" id="agregargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<div class="modal fade" id="borrargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>unidades/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Unidad</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar la unidad seleccionada?</p>
                    <input type="hidden" name="id" id="id_borrar">
                    <input type="hidden" name="nombre" id="nom_borrar">
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmar" class="btn btn-primary" onclick="eliminar()">
                    <li class="glyphicon glyphicon-thumbs-up"></li>Confirmar</button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Cancelar
                    <li class="glyphicon glyphicon-thumbs-down"></li></button>

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

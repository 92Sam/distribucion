<?php $ruta = base_url(); ?>

<style>
    #tablaresult th {
        font-size: 11px !important;
        padding: 6px 2px;
        text-align: center;
        vertical-align: middle;
    }

    #tablaresult td {
        font-size: 10px !important;
    }
</style>

<ul class="breadcrumb breadcrumb-top">
    <li>Proveedor</li>
    <li><a href="">Agregar y editar Proveedor</a></li>
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

    <br><br>

    <div class="table-responsive">
        <table class="table table-striped table-bordered " id="tablaresult">
            <tr>
                 <thead>
                    <th>Codigo</th>
                    <th>RUC</th>
                    <th>Razón Social</th>
                    <th>Dirección</th>
                    <th>Telefono</th>
                    <th>Correo</th>
                    <th>Persona de Contacto</th>
                    <th>Telefono Contacto</th>
                    <th>P&aacute;gina Web</th>
                    <th>Observaci&oacute;n</th>
                    <th>Acciones</th>
                </thead>
            </tr>

            <?php if (count($proveedores) > 0):?>

                <?php foreach ($proveedores as $proveedor) :?>

                <tr>
                    <td class="center"><?= $proveedor['id_proveedor'] ?></td>
                    <td><?= $proveedor['proveedor_ruc'] ?></td>
                    <td><?= $proveedor['proveedor_nombre'] ?></td>
                    <td><?= $proveedor['proveedor_direccion1'] ?></td>
                    <td><?= $proveedor['proveedor_telefono1'] ?></td>
                    <td><?= $proveedor['proveedor_email'] ?></td>
                    <td><?= $proveedor['proveedor_contacto'] ?></td>
                    <td><?= $proveedor['proveedor_telefono2'] ?></td>
                    <td><?= $proveedor['proveedor_paginaweb'] ?></td>
                    <td><?= $proveedor['proveedor_observacion'] ?></td>

                    <td class="center">
                        <div class="btn-group">
                            <?='<a class="btn btn-default" data-toggle="tooltip"
                                        title="Editar" data-original-title="fa fa-comment-o"
                                        href="#" onclick=
                                        "editar(' . $proveedor['id_proveedor'] . ');">'; ?>
                            <i class="glyphicon glyphicon-edit"></i>
                            </a>
                            <?= '<a class="btn btn-danger" data-toggle="tooltip"
                                 title="Eliminar" data-original-title="fa fa-comment-o"
                                 onclick="borrar(' . $proveedor['id_proveedor'] . ',\'' . $proveedor['proveedor_nombre'] . '\');">'; ?>
                            <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </div>
                    </td>
                    </tr>
                <?php endforeach;?>
            <?php else:?>
                <h3>No hay información que mostrar</h3>
            <?php endif;?>
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
        $("#nom_borrar").attr('value', nom);
    }


    function editar(id) {

        $("#agregar").load('<?= $ruta ?>proveedor/form/'+id);
        $('#agregar').modal('show');
    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>proveedor/form');
        $('#agregar').modal('show');
    }

    var grupo = {
        ajaxgrupo : function(){
            return  $.ajax({
                url:'<?= base_url()?>proveedor'

            })
        },
        guardar : function () {
            if ($("#proveedor_nombre").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el nombre</h4>', {
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

    function eliminar(){

        App.formSubmitAjax($("#formeliminar").attr('action'), grupo.ajaxgrupo, 'borrar', 'formeliminar' );

    }
</script>

<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>proveedor/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Proveedor</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar el Proveedor seleccionado?</p>
                    <input type="hidden" name="id" id="id_borrar">
                    <input type="hidden" name="nombre" id="nom_borrar">
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
<script src="<?= $ruta?>recursos/js/pages/tablesDatatables.js"></script>
<script>$(function(){ TablesDatatables.init(); });</script>
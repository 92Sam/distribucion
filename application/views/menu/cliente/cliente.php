<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Clientes</li>
    <li><a href="">Agregar y editar Clientes</a></li>
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
                <th>RUC / DNI</th>
                <th>Raz&oacute;n Social</th>
                <th>Tipo</th>
                <th>Grupo</th>
                <th>Direcci&oacute;n</th>
                <th>Distrito</th>
                <th>Zona</th>
                <th>Vendedor</th>
                <th class="desktop">Acciones</th>

            </tr>
            </thead>
            <tbody id="xxxx">


            </tbody>
        </table>

</div>
    <br>
    <a href="<?= $ruta ?>cliente/pdf" class="btn  btn-danger btn-lg" data-toggle="tooltip" title="Exportar a PDF"
       data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
    <a href="<?= $ruta ?>cliente/excel" class="btn btn-default btn-lg" data-toggle="tooltip"
       title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
            class="fa fa-file-excel-o fa-fw"></i></a>
</div>


<!-- Modales for Messages -->
<div class="modal hide" id="mOK">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" onclick="javascript:window.location.reload();">
        <li class="glyphicon glyphicon-thumbs-down"></li>
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
        $("#id_borrar").val(id);
        $("#nom_borrar").val(nom);
    }


    function editar(id) {

        $("#agregar").load('<?= $ruta ?>cliente/form/' + id);
        $('#agregar').modal({show: true, keyboard: false, backdrop: 'static'});
    }

    function agregar() {

        $("#agregar").load('<?= $ruta ?>cliente/form');
        $('#agregar').modal({show: true, keyboard: false, backdrop: 'static'});
    }


    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>cliente'

            })
        },
        guardar: function () {
            if ($("#razon_social").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar la raz&oacute;n social</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#identificacion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar el DNI</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#grupo_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el grupo</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#id_pais").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el pais</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            if ($("#estado_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la ciudad</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            if ($("#ciudad_id").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar el distrito</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#zona").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar la zona</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($("#vendedor").val() == '') {
                var growlType = 'warning';
                $("#vendedor").focus()
                $.bootstrapGrowl('<h4>Debe seleccionar un Vendedor</h4>', {
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
        $('#barloadermodal').modal('show');

         $.ajax({
                url: '<?=base_url()?>cliente/eliminar',
                 type: "post",
                dataType: "json",
                data: { 'id': $('#id_borrar').val()},
                            success: function(data) {
                            if (data != '') {

                                $.bootstrapGrowl('<h4>'+data[Object.keys(data)]+'</h4>', {
                                    type: Object.keys(data),
                                    delay: 2500,
                                    allow_dismiss: true
                                });
                                $('#borrar').modal('toggle')

                                $("#example").dataTable().fnDestroy();

                                TablesDatatablesJson.init('<?php echo base_url()?>api/Clientes', 0, 'example');
                                setTimeout(function () {
                                    $('#barloadermodal').modal('hide');
                                }, 500)

                                if(Object.keys(data) == 'success'){

                                   return true
                                }else{
                                    return false

                                }


                            }
                        }

            });
        //App.formSubmitAjax($("#formeliminar").attr('action'), grupo.ajaxgrupo, 'borrar', 'formeliminar');
    }


</script>

<div class="modal fade" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
                    </button>
                    <h4 class="modal-title">Eliminar Cliente</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <h4>¿Est&aacute; seguro que desea eliminar el Cliente seleccionado?</h4>
                    </div>
                    <input type="hidden" name="id" id="id_borrar">
                    <input type="hidden" name="nombre" id="nom_borrar">
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmar" class="btn btn-primary" onclick="eliminar()">
                        <li class="glyphicon glyphicon-thumbs-up"></li> Confirmar
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal"> Cancelar
                        <li class="glyphicon glyphicon-thumbs-down"></li>
                    </button>

            </div>
        </div>
            <!-- /.modal-content -->
        </div>

</div>
<!-- /.modal-dialog -->


<script>
    $(function () {

       TablesDatatablesJson.init('<?php echo base_url()?>api/Clientes',0, 'example');



    });
</script>
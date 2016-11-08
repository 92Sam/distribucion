<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Ajuste de inventario</a></li>
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

    <a class="btn btn-primary" onclick="agregar();">
        <i class="fa fa-plus "> Nuevo</i>
    </a>

    <!-- Progress Bars Wizard Title -->
    <div class="col-md-1">
        Ubicaci&oacute;n
    </div>
    <div class="col-md-3">
        <select id="locales" class="form-control">
            <option value="seleccione"> Seleccione</option>
            <?php if (isset($locales)) {
                foreach ($locales as $local) {
                    ?>
                    <option value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                <?php }
            } ?>

        </select>
        <br>
    </div>

    <br>


    <?php
    echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
    ?>
    <div class="row">


            <div class="table-responsive" id="tabla">

                <table class="table dataTable table-striped dataTable table-bordered" id="tablaresultado">
                    <thead>
                    <tr>

                        <th>N&uacute;mero</th>
                        <th>Fecha</th>
                        <th>Nombre</th>

                        <th class="desktop">Acciones</th>

                    </tr>
                    </thead>
                    <tbody>
                    <?php if (count($ajustes) > 0) {

                        foreach ($ajustes as $ajuste) {
                            ?>
                            <tr>

                                <td class="center"><?= $ajuste->id_ajusteinventario ?></td>
                                <td class="center"><?= date('d-m-Y H:i:s', strtotime($ajuste->fecha)) ?></td>
                                <td><?= $ajuste->descripcion ?></td>


                                <td class="center">
                                    <div class="btn-group">
                                        <?php

                                        echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Ver Detalle" data-original-title="Ver Detalle"
                                            href="#" onclick="ver(' . $ajuste->id_ajusteinventario . ');">'; ?>
                                        <i class="fa fa-search"></i>
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


    <br>

</div>



<script type="text/javascript">

    function agregar() {

        $("#agregargrupo").load('<?= $ruta ?>inventario/addajuste', function(){
            $('#agregargrupo').modal({show: true});
        });

    }

    function ver(id) {

        $("#verajuste").load('<?= $ruta ?>inventario/verajuste/'+id);
        $('#verajuste').modal({show: true });
    }

    var grupo = {
        ajaxgrupo : function(){
            return  $.ajax({
                url:'<?= base_url()?>inventario/ajuste'

            })
        },
        guardar : function () {

            if ($('#locales_in').val() == 'seleccione') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe Seleccionar el local</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }

            if ($('#fecha').val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe Ingresar la fecha</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            if ($("#columnas tr").length == 0) {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe Seleccionar al menos un producto</h4>', {
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
</script>
<div class="modal fades" id="agregargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<!-- /.modal-dialog -->

<script>$(function () {
        TablesDatatables.init();
        //   TablesDatatables.init();

        $("#locales").on("change", function () {


            // $("#hidden_consul").remove();
            $.ajax({
                url: '<?= base_url()?>inventario/ajusteinventario_by_local',
                data: {'id_local': $("#locales").val()},
                type: 'POST',
                success: function (data) {
                    // $("#query_consul").html(data.consulta);

                    $("#tabla").html(data);
                   TablesDatatables.init();
                }
            })
        });

    });


</script>

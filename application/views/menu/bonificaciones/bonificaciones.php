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
echo validation_errors('<div class="alert alert-danger alert-dismissable">', "</div>");
?>
<div class="span12">
    <div class="block">
        <!-- Progress Bars Wizard Title -->

        <div class="row">
            <div class="col-md-2">
                <a class="btn btn-primary" onclick="agregar();">
                    <i class="fa fa-plus ">Nuevo</i>
                </a>

            </div>

            <form id="frmGrupos">
                <div class="col-md-1">
                    <label class="control-label panel-admin-text">Grupos:</label>
                </div>
                <div class="col-md-3">

                    <select name="grupos" id="grupos" class='cho form-control filter-input'>
                        <?php if (count($grupos) > 0): ?>
                            <?php foreach ($grupos as $grupo): ?>
                                <option
                                    value="<?php echo $grupo['id_grupos_cliente']; ?>"
                                    id="<?php echo $grupo['nombre_grupos_cliente']; ?>">
                                    <?php echo $grupo['nombre_grupos_cliente']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </form>
            <!-- <button id="btnBuscar" class="btn btn-default" >Buscar</button>  -->
        </div>
    </div>
</div>

<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="row" id="loading" style="display: none;">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
    <div id="lstTabla" class="table-responsive"></div>

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

    $(document).ready(function () {

        $("#grupos").on('change', function () {
            loadBonificaciones();
        });

        loadBonificaciones();
    });

    function loadBonificaciones() {
        $("#lstTabla").html($("#loading").html());

        $.ajax({
            type: 'POST',
            data: $('#frmGrupos').serialize(),
            url: '<?php echo base_url();?>' + 'bonificaciones/lst_bonificaciones',
            success: function (data) {
                $("#lstTabla").html(data);

            },
            error: function(){
                $("#lstTabla").html('');
                $.bootstrapGrowl('<h4>Ha ocurrido un error en la opci&oacute;n</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });
            }
        });
    }

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



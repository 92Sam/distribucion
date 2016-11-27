<div class="modal-dialog modal-lg">

    <?= form_open_multipart(base_url() . 'producto/registrar', array('id' => 'formguardar')) ?>
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Datos del producto - <span>    <?php if (isset($producto['producto_nombre'])) echo $producto['producto_nombre']; ?></span></h4>
        </div>

        <div class="modal-body">

            <input type="hidden" name="id" id="id"
                   class='form-control' autofocus="autofocus" maxlength="15"
                   value="<?php if (isset($producto['producto_id']) and empty($duplicar)) echo $producto['producto_id'] ?>"
            >

            <div id="mensaje"></div>


            <ul class="nav nav-tabs" role="tablist">


                <li role="presentation">
                    <a href="#precios" data-toggle="tab"> Unidades y Precios</a>
                </li>


                <li role="presentation">
                    <a href="#promocion" data-toggle="tab">Bonificaciones</a>
                </li>
                <li role="presentation">
                    <a href="#descuento" data-toggle="tab">Descuento</a>
                </li>
                <!--<li role="presentation">
                    <a href="#imagenes" data-toggle="tab">Im&aacute;genes</a>
                </li>-->
            </ul>

            <div class="tab-content row" style="height: auto">


                <div class="tab-pane active" role="tabpanel" id="precios" role="tabpanel">
                   <!-- <div class="panel">


                        <div class="col-md-2">
                            <label>Costo Unitario:</label>
                        </div>

                        <div class="col-md-2">
                            <input type="number" disabled name="costo_unitario" id="costo_unitario" class="form-control" required
                                   value="<?php if (isset($producto['costo_unitario'])) echo $producto['costo_unitario'] ?>"/>
                        </div>
                    </div>
                    <br> -->

                    <div class="table-responsive ">


                        <!-- Block -->

                        <table class="table block table-striped dataTable table-bordered">
                            <thead>
                            <th>Descripci&oacute;n</th>
                            <th>Unidades</th>
                            <th>Metro Cubicos</th>







                            <?php foreach ($precios as $precio):
                                if ($precio['mostrar_precio']):?>
                                    <th><?= $precio['nombre_precio'] ?></th>
                                <?php endif?>
                            <?php endforeach ?>
                            <th></th>
                            </thead>
                            <tbody id="unidadescontainer" class="draggable-tbody">

                            <?php
                            $countunidad = 0;
                            if (isset($unidades_producto) and count($unidades_producto)):



                                foreach ($unidades_producto as $unidad) { ?>
                                    <tr id="trunidad<?= $countunidad ?>" class="trdrag">


                                        <td>

                                            <select disabled name='medida[<?= $countunidad ?>]' id='medida<?= $countunidad ?>'
                                                    class='form-control'>"
                                                <?php foreach ($unidades as $unidad2):
                                                    ?>
                                                    <option
                                                        value='<?= $unidad2['id_unidad'] ?>' <?php if ($unidad2['id_unidad'] == $unidad['id_unidad']) echo 'selected'?>><?= $unidad2['nombre_unidad'] ?></option>"

                                                <?php endforeach ?></select>

                                        </td>

                                        <td>

                                            <input disabled type="number" class="form-control" required

                                                   value='<?= $unidad['unidades'] ?>'
                                                   name="unidad[<?= $countunidad ?>]" id="unidad[<?= $countunidad ?>]">
                                        </td>

                                        <td><input disabled type="number" class="form-control" required
                                                   value='<?= isset($unidad['metros_cubicos']) ? $unidad['metros_cubicos'] : 0 ?>'
                                                   name="metros_cubicos[<?= $countunidad ?>]"
                                                   id="metros_cubicos<?= $countunidad ?>">
                                        </td>
                                        <?php $countproducto = 0;

                                        foreach ($precios as $precioo) {

                                            if ($precio['mostrar_precio']) {
                                                $blanco = true;
                                                foreach ($precios_producto[$countunidad] as $precio) {


                                                    if ($precio['id_precio'] == $precioo['id_precio']) {
                                                        $blanco = false;
                                                        ?>
                                                        <td><input disabled type="hidden" value='<?= $precio['id_precio'] ?>'
                                                                   name='precio_id_<?= $countunidad ?>[<?= $countproducto ?>]'/>
                                                            <input disabled type="number" class="form-control" required
                                                                   value='<?= $precio['precio'] ?>'
                                                                   name="precio_valor_<?= $countunidad ?>[<?= $countproducto ?>]">

                                                        </td>


                                                        <?php


                                                    }
                                                }
                                                if ($blanco) {
                                                    ?>
                                                    <td><input type="hidden" value='<?= $precioo['id_precio'] ?>'
                                                               name='precio_id_<?= $countunidad ?>[<?= $countproducto ?>]'/>
                                                        <input disabled type="number" class="form-control" required
                                                               value='0'
                                                               name="precio_valor_<?= $countunidad ?>[<?= $countproducto ?>]">

                                                    </td>
                                                <?php }
                                                ?>


                                                <?php
                                            }
                                            $countproducto++;


                                        } ?>


                                    </tr>
                                    <?php $countunidad++;
                                } endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="tab-pane table-responsive" role="tabpanel" id="promocion" role="tabpanel">
                    <br>
                    <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                        <thead>
                        <tr>

                            <th>Unidad</th>
                            <th>Familia</th>
                            <th>Grupo</th>
                            <th>Marca</th>
                            <th>Linea</th>
                            <th>Cantidad</th>
                            <th>Bono unidad</th>
                            <th>Bono producto</th>
                            <th>Bono cantidad</th>
                            <th>Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($promociones as $promocion){ ?>
                        <tr>
                            <td>
                                <?php if (isset($promocion['id_unidad'])) echo $promocion['nombre_unidad']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['id_familia'])) echo $promocion['nombre_familia']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['id_grupo'])) echo $promocion['nombre_grupo']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['id_marca'])) echo $promocion['nombre_marca']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['id_linea'])) echo $promocion['nombre_linea']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['cantidad_condicion'])) echo $promocion['cantidad_condicion']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['unidad_bonificacion'])) echo $promocion['unidad_bonificacion']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['producto_bonificacion'])) echo $promocion['producto_bonificacion']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['bono_cantidad'])) echo $promocion['bono_cantidad']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['fecha'])) echo date('d-m-Y', strtotime($promocion['fecha']));
                                } ?>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>

                <div class="tab-pane" role="tabpanel" id="descuento" role="tabpanel">
                    <br>
                    <tr class="table-responsive ">
                        <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                            <thead>
                            <tr>

                                <th>Regla descuento</th>
                                <th>Cantidades</th>
                                <th>Nombre producto</th>
                                <th>Unidad</th>
                                <th>Precio</th>


                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($descuentos as $descuento) { ?>
                            <tr>
                                <td>
                                    <?php if (isset($descuento['nombre'])) echo $descuento['nombre']; ?>
                                </td>
                                <td>
                                    <?php if (isset($descuento['cantidad_minima'])) echo $descuento['cantidad_minima']; ?>
                                    a
                                    <?php if (isset($descuento['cantidad_maxima'])) echo $descuento['cantidad_maxima']; ?>
                                </td>
                                <td>
                                    <?php if (isset($descuento['producto_nombre'])) echo $descuento['producto_nombre']; ?>
                                </td>
                                <td>
                                    <?php if (isset($descuento['nombre_unidad'])) echo $descuento['nombre_unidad']; ?>
                                </td>
                                <td>
                                    <?php if (isset($descuento['precio'])) echo $descuento['precio'];
                                    } ?>
                                </td>


                            </tr>
                            </tbody>
                        </table>
                </div>


            </div>


            <!--<div class="tab-pane" role="tabpanel" id="imagenes" role="tabpanel">

                <div class="form-group">

                    <div class="col-md-2">


                        <img src="<?= base_url() ?>recursos/img/placeholders/avatars/avatar.jpg">
                    </div>
                    <div class="col-md-8">

                        <div class="input-prepend input-append input-group">
                            <span class="input-group-addon"><i class="fa fa-folder"></i> </span>
                            <input type="file" class="form-control">
                        </div>

                    </div>
                </div>

        </div>-->


        </div>
        <div class="modal-footer">
            <div class="text-right">

                <input type="reset" class='btn btn-default' value="Cancelar" data-dismiss="modal">
            </div>
        </div>


    </div>
    <?= form_close() ?>

</div>


<script>
    //$("select").chosen();

    function guardarproducto() {

        var nombre = $("#producto_nombre");
        var producto_impuesto = $("#producto_impuesto");
        if (nombre.val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar el nombre del producto</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if (producto_impuesto.val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar el impuesto</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }

        if ($("#producto_cualidad").val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar la cualidad del producto</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }


        if ($("#unidadescontainer tr").length == 0) {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe Seleccionar al menos una unidad de medida</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }


        var vacios = false;
        var nan = false;
        var negativo = false;
        $("#unidadescontainer input[type='number']").each(function () {
            var txt = $(this).val();

            //console.log(txt);
            if (txt == '') {
                vacios = true;
            }
            /// console.log(isNaN(txt));
            if (!isNaN(txt)) {
                nan = true;
            }

            if (parseInt(txt) < 0) {
                negativo = true;
            }

        });

        if (vacios) {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Los campos precios ,unidades y metros cúbicos  no pueden estar vac&iacute;os y deben contener solo n&uacute;meros</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }
        if (negativo) {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Los campos precios, unidades y metros cúbicos no pueden estar vac&iacute;os y deben contener solo n&uacute;meros positivos</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;
        }


        var repetidas = false;

        var seen = {};
        $("#unidadescontainer select[id^='medida']").each(function () {
            var txt = $(this).val();
            //console.log(txt);
            if (seen[txt]) {
                repetidas = true;
            }
            else {
                seen[txt] = true;
            }
        });

        if (repetidas) {

            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Las unidades de medida no deben repetirse!</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;

        }
        var length = $("#unidadescontainer input[id^='unidad']").length;
        var is_last_item;
        $("#unidadescontainer input[id^='unidad']").each(function (index) {


            if ((index == (length - 1))) {

                is_last_item = $(this).val();

            }


        });


        if (is_last_item != '1') {
            $.bootstrapGrowl('<h4>La unidad minima no puede ser mayor  a uno(1) !</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;

        }

        App.formSubmitAjax($("#formguardar").attr('action'), getproductosbylocal, 'productomodal', 'formguardar');

    }
    var unidadcount = <?= $countunidad ?>;
    function agregarprecio() {


        $("#unidadescontainer").append("<tr id='trunidad" + unidadcount + "'>" +
            "<td><select name='medida[" + unidadcount + "]' id='medida" + unidadcount + "' class='form-control'>" +
            <?php foreach ($unidades as $unidad):
                      ?>
            "<option value='<?= $unidad['id_unidad']?>' ><?= $unidad['nombre_unidad']?></option>" +

            <?php endforeach ?>"</select></td>" +
            "<td><input type='number' class='form-control' required name='unidad[" + unidadcount + "]' id='unidad" + unidadcount + "'></td>" +
            "<td><input type='number' value='0' class='form-control' required name='metros_cubicos[" + unidadcount + "]' id='metros_cubicos" + unidadcount + "'></td>" +


            <?php $preciocount = 0;
             foreach ($precios as $precio):
                      if ($precio['mostrar_precio']):?>
            "<td><input class='form-control' type='hidden' value='<?= $precio['id_precio'] ?>' name='precio_id_" + unidadcount + "[<?= $preciocount ?>]' id='precio_id" + unidadcount + "'>" +
            "<input class='form-control' type='number' required name='precio_valor_" + unidadcount + "[<?= $preciocount ?>]' id='precio_valor" + unidadcount + "'></td>" +
            <?php endif?>

            <?php $preciocount++;
             endforeach ?>
            "<td width='13%'><a class='btn btn-default' href='#' id='eliminar" + unidadcount + "' onclick='eliminarunidad(" + unidadcount + ");'><i class='fa fa-remove'></i> </a> <a style='cursor: move' class='btn btn-default' href='#' data-toggle='tooltip'" +
            " title='Mover' data-original-title='Mover' ><i class='fa fa-arrows-v'></i> </a>  </td>" +
            "</tr>");
        unidadcount++;
    }

    function eliminarunidad(unidadcount) {
        // console.log(unidadcount);
        $("#trunidad" + unidadcount).remove();
        var count = 0;
        $("tr[id^='trunidad']").each(function () {
            $(this).attr('id', 'trunidad' + count);

            $("#trunidad" + count + " select[name^='medida']").attr('name', 'medida[' + count + ']');
            $("#trunidad" + count + " select[name^='medida']").attr('id', 'medida' + count + '');

            $("#trunidad" + count + " input[name^='unidad']").attr('name', 'unidad[' + count + ']');
            $("#trunidad" + count + " input[name^='unidad']").attr('id', 'unidad' + count + '');

            var countprecio=0;
            $("#trunidad"+count+" input[name^='precio_id_']").each(function(){
                $(this).attr('name', 'precio_id_'+count+'['+countprecio+']');
                countprecio++;
            });
            $("#trunidad"+count+" input[name^='precio_id_']").attr('id', 'precio_id'+count);

            var countprecio=0;
            $("#trunidad"+count+" input[name^='precio_valor_']").each(function(){
                $(this).attr('name', 'precio_valor_'+count+'['+countprecio+']');
                countprecio++;
            })

            $("#trunidad" + count + " input[name^='precio_valor_']").attr('id', 'precio_valor' + count);


            $("#trunidad" + count + " a[id^='eliminar']").attr('id', 'eliminar' + count);
            $("#trunidad" + count + " a[id^='eliminar']").attr('onclick', 'eliminarunidad(' + count + ')');

            count++;
        })
    }


</script>

<script src="<?php echo base_url() ?>recursos/js/pages/uiDraggable.js"></script>
<script>
    $(function () {
        UiDraggable.init();
        //$("select[id^='medida']").chosen({ allow_single_deselect: true, disable_search_threshold: 5, width:"100%" });

        $('body').keydown(function (e) {

            if (e.keyCode == 115) {
                agregarprecio();
            }
        });

        $("#producto_marca").chosen();
        $("#producto_linea").chosen();
        $("#producto_familia").chosen();
        $("#produto_grupo").chosen();
        $("#producto_proveedor").chosen();


    });
</script>


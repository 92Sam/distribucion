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

            </ul>

            <div class="tab-content row" style="height: auto">


                <div class="tab-pane active" role="tabpanel" id="precios" role="tabpanel">


                    <div class="table-responsive ">


                        <!-- Block -->

                        <table class="table block table-striped dataTable table-bordered">
                            <thead>
                            <th>UM</th>
                            <th>Cantidad</th>
                            <th>Metros Cubicos</th>



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

                            <th>UM</th>
                            <th>Grupo</th>
                            <th>Marca</th>
                            <th>Cantidad</th>
                            <th>Bono UM</th>
                            <th>Bono Producto</th>
                            <th>Bono Cantidad</th>
                            <th>Fecha Vencimiento</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($promociones as $promocion){ ?>
                        <tr>
                            <td>
                                <?php if (isset($promocion['id_unidad'])) echo $promocion['nombre_unidad']; ?>
                            </td>

                            <td>
                                <?php if (isset($promocion['id_grupo'])) echo $promocion['nombre_grupo']; ?>
                            </td>
                            <td>
                                <?php if (isset($promocion['id_marca'])) echo $promocion['nombre_marca']; ?>
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
                                <th>UM</th>
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



        </div>
        <div class="modal-footer">
            <div class="text-right">

                <input type="reset" class='btn btn-warning' value="Salir" data-dismiss="modal">
            </div>
        </div>


    </div>
    <?= form_close() ?>

</div>


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


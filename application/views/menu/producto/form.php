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
                <li class='active' role="presentation">
                    <a href="#lista" data-toggle="tab">Datos Generales</a>
                </li>

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

                <div class="tab-pane active" role="tabpanel" id="lista" role="tabpanel">
                    <?php foreach ($columnas as $columna): ?>

                        <?php if ($columna->nombre_columna == 'producto_id' && isset($producto['producto_id']) and !isset($duplicar)) { ?>
                            <div class="form-group">
                                <div class="col-md-2"><label class="control-label panel-admin-text">Código:</label></div>
                                <div class="col-md-9">

                                    <input type="text" name="codigo" id="codigo"
                                           class='form-control' autofocus="autofocus" maxlength="15"
                                           value="<?php if (isset($producto['producto_id']) and !isset($duplicar)) echo $producto['producto_id'] ?>"
                                           readonly>

                                </div>
                            </div>
                        <?php } ?>


                        <?php if ($columna->nombre_columna == 'producto_codigo_barra' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2"><label class="control-label panel-admin-text">C&oacute;digo de barra:</label></div>
                                <div class="col-md-9">

                                    <input type="text" name="producto_codigo_barra" id="codigodebarra"
                                           class='form-control' autofocus="autofocus" maxlength="25"
                                           value="<?php if (isset($producto['producto_codigo_barra'])) echo $producto['producto_codigo_barra'] ?>">


                                </div>
                            </div>
                        <?php } ?>

                        <?php if ($columna->nombre_columna == 'producto_nombre') { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label class="control-label panel-admin-text">Nombre:</label>
                                </div>

                                <div class="col-md-9">
                                    <input type="text" name="producto_nombre" required="true" id="producto_nombre"
                                           class='form-control'
                                           maxlength="100"
                                           value="<?php if (isset($producto['producto_nombre'])) echo $producto['producto_nombre'] ?>">
                                </div>
                            </div>

                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_descripcion' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label class="control-label panel-admin-text">Descripcion:</label>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="producto_descripcion" id="producto_descripcion"
                                           class='form-control'
                                           maxlength="500"
                                           value="<?php if (isset($producto['producto_descripcion'])) echo $producto['producto_descripcion'] ?>">
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_marca' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="linea" class="control-label panel-admin-text">Marca:</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="producto_marca" id="producto_marca" class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($marcas) > 0): ?>
                                            <?php foreach ($marcas as $marca): ?>
                                                <option
                                                    value="<?php echo $marca['id_marca']; ?>" <?php if (isset($producto['producto_marca']) && $producto['producto_marca'] == $marca['id_marca']) echo 'selected' ?> ><?php echo $marca['nombre_marca']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_linea' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="producto_linea" class="control-label panel-admin-text">Talla:</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="producto_linea" id="producto_linea" class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($lineas) > 0): ?>
                                            <?php foreach ($lineas as $linea): ?>
                                                <option
                                                    value="<?php echo $linea['id_linea']; ?>" <?php if (isset($producto['producto_linea']) && $producto['producto_linea'] == $linea['id_linea']) echo 'selected' ?>><?php echo $linea['nombre_linea']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_familia' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="producto_familia" class="control-label panel-admin-text">Sub Linea:</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="producto_familia" id="producto_familia" class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($familias) > 0): ?>
                                            <?php foreach ($familias as $familia): ?>
                                                <option
                                                    value="<?php echo $familia['id_familia']; ?>" <?php if (isset($producto['producto_familia']) && $producto['producto_familia'] == $familia['id_familia']) echo 'selected' ?>><?php echo $familia['nombre_familia']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <!--SUB FAMILIA -->
                        <?php if ($columna->nombre_columna == 'producto_subfamilia' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="producto_subfamilia" class="control-label panel-admin-text">Sub Familia:</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="producto_subfamilia" id="producto_subfamilia"
                                            class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($subfamilias) > 0): ?>
                                            <?php foreach ($subfamilias as $familia): ?>
                                                <option
                                                    value="<?php echo $familia['id_subfamilia']; ?>" <?php if (isset($producto['producto_subfamilia']) && $producto['producto_subfamilia'] == $familia['id_subfamilia']) echo 'selected' ?>><?php echo $familia['nombre_subfamilia']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- FIN SUB FAMILIA -->

                        <?php if ($columna->nombre_columna == 'produto_grupo' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="grupo" class="control-label panel-admin-text">Grupo:</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="produto_grupo" id="produto_grupo" class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($grupos) > 0): ?>
                                            <?php foreach ($grupos as $grupo): ?>
                                                <option
                                                    value="<?php echo $grupo['id_grupo']; ?>" <?php if (isset($producto['produto_grupo']) && $producto['produto_grupo'] == $grupo['id_grupo']) echo 'selected' ?>><?php echo $grupo['nombre_grupo']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <!--SUB GRUPO -->
                        <?php if ($columna->nombre_columna == 'producto_subgrupo' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="subgrupo" class="control-label panel-admin-text">Linea:</label>
                                </div>
                                <div class="col-md-9">
                                    <select name="producto_subgrupo" id="producto_subgrupo" class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($subgrupos) > 0): ?>
                                            <?php foreach ($subgrupos as $grupo): ?>
                                                <option
                                                    value="<?php echo $grupo['id_subgrupo']; ?>" <?php if (isset($producto['producto_subgrupo']) && $producto['producto_subgrupo'] == $grupo['id_subgrupo']) echo 'selected' ?>><?php echo $grupo['nombre_subgrupo']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- FIN SUB GRUPO-->
                        <?php if ($columna->nombre_columna == 'producto_proveedor' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2"><label class="control-label panel-admin-text">Proveedor:</label></div>
                                <div class="col-md-9">
                                    <select name="producto_proveedor" id="producto_proveedor" class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($proveedores) > 0): ?>
                                            <?php foreach ($proveedores as $proveedor): ?>
                                                <option
                                                    value="<?php echo $proveedor->id_proveedor; ?>" <?php if (isset($producto['producto_proveedor']) && $producto['producto_proveedor'] == $proveedor->id_proveedor) echo 'selected' ?>><?php echo $proveedor->proveedor_nombre; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>

                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_stockminimo' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="stockmin" class="control-label panel-admin-text">Stock M&iacute;nimo:</label>
                                </div>

                                <div class="col-md-9">


                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">cant.</span>
                                        <input type="text" class='input-small input-square form-control'
                                               name="producto_stockminimo"
                                               id="producto_stockminimo" maxlength="11"
                                               onkeydown="return soloDecimal(this, event);"
                                               value="<?php if (isset($producto['producto_stockminimo'])) echo $producto['producto_stockminimo'] ?>">
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <!--
                        <?php if ($columna->nombre_columna == 'producto_impuesto') { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Impuesto:</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="producto_impuesto" id="producto_impuesto" class='cho form-control'>
                                        <option value="">Seleccione</option>
                                        <?php if (count($impuestos) > 0): ?>
                                            <?php foreach ($impuestos as $impuesto): ?>
                                                <option
                                                    value="<?php echo $impuesto['id_impuesto']; ?>" <?php if (isset($producto['producto_impuesto']) && $producto['producto_impuesto'] == $impuesto['id_impuesto']) echo 'selected'; elseif (strtoupper($impuesto['nombre_impuesto']) == "IGV") echo 'selected' ?>><?php echo $impuesto['nombre_impuesto']; ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        -->
                        <?php if ($columna->nombre_columna == 'producto_largo' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="impuesto" class="control-label panel-admin-text">Largo:</label>
                                </div>
                                <div class="col-md-9">

                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">Cm.</span>
                                        <input type="number" name="producto_largo" id="producto_largo"
                                               class='cho form-control'
                                               value="<?php if (isset($producto['producto_largo'])) echo $producto['producto_largo'] ?>"/>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_ancho' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="impuesto" class="control-label panel-admin-text">Ancho:</label>
                                </div>
                                <div class="col-md-9">

                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">Cm.</span>
                                        <input type="number" name="producto_ancho" id="producto_ancho"
                                               class='cho form-control'
                                               value="<?php if (isset($producto['producto_ancho'])) echo $producto['producto_ancho'] ?>"/>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_alto' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="impuesto" class="control-label panel-admin-text">Alto:</label>
                                </div>
                                <div class="col-md-9">


                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">Cm.</span>
                                        <input type="number" name="producto_alto" id="producto_alto"
                                               class='cho form-control'
                                               value="<?php if (isset($producto['producto_alto'])) echo $producto['producto_alto'] ?>"/>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_activo' and $columna->activo == 1) { ?>

                            <div class="col-md-12">
                                <div class="form-group row">

                                    <div class="col-md-2">
                                        <label for="impuesto" class="control-label panel-admin-text">Estado</label>
                                    </div>

                                    <div class="col-md-9">
                                        <input type="radio" value="1"
                                               name="producto_activo" <?php if (isset($producto['producto_activo'])
                                            and $producto['producto_activo'] == 1
                                        ) {
                                            echo "checked";
                                        } ?>> Activo

                                        <input type="radio" value="0"
                                               name="producto_activo" <?php if (isset($producto['producto_activo'])
                                            and $producto['producto_activo'] == 0
                                        ) echo "checked"; ?>> Inactivo
                                    </div>


                                </div>
                            </div>

                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'presentacion' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="impuesto" class="control-label panel-admin-text">Presentación</label>
                                </div>
                                <div class="col-md-9">

                                    <input type="text" name="presentacion" id="presentacion"
                                           class='cho form-control'
                                           value="<?php if (isset($producto['presentacion'])) echo $producto['presentacion'] ?>"/>
                                </div>

                            </div>

                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_peso' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="impuesto" class="control-label panel-admin-text">Peso:</label>
                                </div>
                                <div class="col-md-9">


                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon">Kg.</span>
                                        <input type="number" name="producto_peso" id="producto_peso"
                                               class='cho form-control'
                                               value="<?php if (isset($producto['producto_peso'])) echo $producto['producto_peso'] ?>"/>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($columna->nombre_columna == 'producto_nota' and $columna->activo == 1) { ?>
                            <div class="form-group">
                                <div class="col-md-2">
                                    <label for="impuesto" class="control-label panel-admin-text">Nota:</label>
                                </div>
                                <div class="col-md-9">
                            <textarea name="producto_nota" id="producto_nota"
                                      class='cho form-control'><?php if (isset($producto['producto_nota'])) echo $producto['producto_nota'] ?></textarea>
                                </div>
                            </div>
                        <?php } ?>
                        <!-- COLOCAMOS DIRECTAMENTE LA UNIDAD MEDIBLE
                        <?php if ($columna->nombre_columna == 'producto_cualidad') { ?>
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="impuesto" class="control-label">Cualidad:</label>
                                </div>
                                <div class="col-md-8">

                                    <select class="form-control" id="producto_cualidad" name="producto_cualidad">
                                        <option value="">Seleccione</option>
                                        <option
                                            value="<?= PESABLE ?>" <?php if (isset($producto['producto_id']) and $producto['producto_cualidad'] == PESABLE) echo 'selected' ?>><?= PESABLE ?></option>
                                        <option
                                            value="<?= MEDIBLE ?>" <?php if (isset($producto['producto_id']) and $producto['producto_cualidad'] == MEDIBLE) echo 'selected' ?>><?= MEDIBLE ?></option>
                                    </select>
                                </div>
                            </div>
                        <?php } ?>
                        -->
                    <?php endforeach ?>
                </div>

                <div class="tab-pane" role="tabpanel" id="precios" role="tabpanel">
                    <div class="panel">
                        <div class="col-md-3">
                            <a class="btn btn-default" onclick="agregarprecio();">
                                <i class="fa fa-plus "> Nuevo Precio (F4)</i>
                            </a>
                        </div>


                        <div class="col-md-2">
                            <label class="control-label panel-admin-text">Costo Unitario:</label>
                        </div>

                        <div class="col-md-2">
                            <input type="number" name="costo_unitario" id="costo_unitario" class="form-control" required
                                   value="<?php if (isset($producto['costo_unitario'])) echo $producto['costo_unitario'] ?>"/>
                        </div>
                    </div>
                    <br>

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

                                            <select name='medida[<?= $countunidad ?>]' id='medida<?= $countunidad ?>'
                                                    class='form-control'>"
                                                <?php foreach ($unidades as $unidad2):
                                                    ?>
                                                    <option
                                                        value='<?= $unidad2['id_unidad'] ?>' <?php if ($unidad2['id_unidad'] == $unidad['id_unidad']) echo 'selected'?>><?= $unidad2['nombre_unidad'] ?></option>"

                                                <?php endforeach ?></select>

                                        </td>

                                        <td>

                                            <input type="number" class="form-control" required

                                                   value='<?= $unidad['unidades'] ?>'
                                                   name="unidad[<?= $countunidad ?>]" id="unidad[<?= $countunidad ?>]">
                                        </td>

                                        <td><input type="number" class="form-control" required
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
                                                        <td><input type="hidden" value='<?= $precio['id_precio'] ?>'
                                                                   name='precio_id_<?= $countunidad ?>[<?= $countproducto ?>]'/>
                                                            <input type="number" class="form-control" required
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
                                                        <input type="number" class="form-control" required
                                                               value='0'
                                                               name="precio_valor_<?= $countunidad ?>[<?= $countproducto ?>]">

                                                    </td>
                                                <?php }
                                                ?>


                                            <?php
                                            }
                                            $countproducto++;


                                        } ?>

                                        <td width='13%'><a href="#" class='btn btn-default'
                                                           id="eliminar<?= $countunidad ?>"
                                                           onclick="eliminarunidad(<?= $countunidad ?>);"><i
                                                    class="fa fa-remove"></i> </a><a class='btn btn-default'
                                                                                     data-toggle='tooltip'
                                                                                     title='Mover'
                                                                                     data-original-title='Mover'
                                                                                     href='#'
                                                                                     style="cursor: move"><i
                                                    class='fa fa-arrows-v'></i> </a></td>
                                    </tr>
                                    <?php $countunidad++;
                                } endif; ?>

                            </tbody>
                        </table>
                    </div>
                </div>


                <div class="tab-pane table-responsive" role="tabpanel" id="promocion" role="tabpanel">
                    <br>

                    <?php foreach($grupos_clie as $grup) {

                        foreach ($promociones as $promocion) {

                            if ($grup['id_grupos_cliente'] == $promocion['id_grupos_cliente']) { ?>

                                <label>Grupo: <?php echo $grup['nombre_grupos_cliente'];?></label>

                                <div row>
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
                                                <?php if (isset($promocion['fecha'])) echo date('d-m-Y', strtotime($promocion['fecha'])); ?>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                    <br>
                                </div>

                            <?php }
                        }

                    }; ?>
                </div>

                <div class="tab-pane" role="tabpanel" id="descuento" role="tabpanel">
                    <br>

                    <?php foreach($grupos_clie as $grup) {
                        $pass = false;

                        foreach ($descuentos as $descuento) {
                            if ($descuento['id_grupos_cliente'] == $grup['id_grupos_cliente']) {
                                $pass = true;
                                break;
                            }
                        }

                        if ($pass) { ?>

                            <label>Grupo: <?php echo $grup['nombre_grupos_cliente'];?></label>

                            <div row>
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
                                        <?php foreach ($descuentos as $descuento) {
                                            if ($descuento['id_grupos_cliente'] == $grup['id_grupos_cliente']) { ?>
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
                                                        <?php if (isset($descuento['precio'])) echo $descuento['precio']; ?>
                                                    </td>
                                                </tr>
                                            <?php }; ?>
                                        <?php };?>
                                        </tbody>
                                    </table>
                                    <br>
                                    <br>
                            </div>
                        <?php }; ?>
                    <?php } ?>

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
                <button class="btn btn-primary" type="button" onclick="guardarproducto()" id="btnGuardar">
                    <li class="glyphicon glyphicon-thumbs-up"></li> Guardar
                </button>


                <button type="reset" class='btn btn-warning' value="Cancelar" data-dismiss="modal">Cancelar
                    <li class="glyphicon glyphicon-thumbs-down"></li>
                </button>

            </div>
        </div>


    </div>
    <?= form_close() ?>

</div>


<script>
    //$("select").chosen();

    function guardarproducto() {

        var nombre = $("#producto_nombre");

        if (nombre.val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar el nombre del producto</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            nombre.focus();
            return false;
        }


        var presentacion = $("#presentacion");
        if (presentacion.val() == '') {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe ingresar la presentacion del producto</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            presentacion.focus();
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
            $("#producto_cualidad").focus();
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
        var sinUnidadMedida = false;

        var seen = {};
        $("#unidadescontainer select[id^='medida']").each(function () {
            var txt = $(this).val();

            //console.log(txt);

            if (seen[txt]) {
                repetidas = true;
            }
            else if(txt == null)
            {
                sinUnidadMedida = true;
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
        if (sinUnidadMedida) {

            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe de crear una unidad de medida antes de crear un producto (Configuraciones => Unidades de Medida)!</h4>', {
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


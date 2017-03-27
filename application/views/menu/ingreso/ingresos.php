<?php $ruta = base_url(); ?>
<style>
    #tbodyproductos td{
        vertical-align: middle;
    }
</style>
<script src="<?php echo $ruta; ?>recursos/js/ingresos.js?<?php echo date('His'); ?>"></script>

<input id="producto_cualidad" type="hidden">


<ul class="breadcrumb breadcrumb-top">
    <li>Compras</li>
    <li>Registro de Existencia</li>
</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-danger alert-dismissable"
             style="display:<?php echo isset($error) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">X
            </button>
            <h4><i class="icon fa fa-ban"></i> Error</h4>
            <?= isset($error) ? $error : '' ?></div>
    </div>
</div>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert"
                    aria-hidden="true">X
            </button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <?php echo isset($success) ? $success : '' ?>
        </div>
    </div>
</div>
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
?>
<div class="block">
    <div class="row-fluid">

        <div class="blok">
            <div class="box-content">


                <form id="frmCompra" class='form-horizontal' style="margin-top: 3%">
                    <input id="costos" name="costos" type="hidden" value="<?= $costos ?>">
                    <input id="ingreso_id" name="id_ingreso" type="hidden"
                           value="<?php if (isset($ingreso->id_ingreso)) echo $ingreso->id_ingreso; ?>">

                    <div class="block-section">
                        <div class="force-margin">

                            <div class="row">

                                <div class="control-group">
                                    <div class="col-md-2">
                                        <label for="fecEnt" class="control-label">Fecha Emision:</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="controls">
                                            <div class="input-append">
                                                <input type="text" placeholder="mes-día-año"
                                                       name="fecEmision"
                                                       value="<? echo isset($ingreso->fecha_registro) ? $ingreso->fecha_registro : date('d-m-Y') ?>"
                                                       id="fecEmision"
                                                       class='input-small datepick required form-control'
                                                       required="true" readonly>
                                                <span class="add-on"><i class="icon-calendar"></i></span>

                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <label for="fecEnt" class="control-label">Motivo del Ingreso</label>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="controls">
                                            <select name="tipo_ingreso" id="tipo_ingreso" class='cho form-control'
                                                    required="true" <?php if (isset($ingreso->id_ingreso)) echo 'disabled' ?>>
                                                <option
                                                    value="<?= COMPRA ?>" <? if ((isset($ingreso->tipo_ingreso) && $ingreso->tipo_ingreso == COMPRA) or !isset($ingreso->tipo_ingreso)) echo 'selected'; ?>><?= COMPRA ?></option>
                                                <option
                                                    value="<?= DONACION ?>"<? if (isset($ingreso->tipo_ingreso) && $ingreso->tipo_ingreso == DONACION) echo 'selected'; ?>
                                                    ?><?= DONACION ?></option>

                                            </select>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <br>

                            <div class="row">

                                <div class="control-group">
                                    <div class="col-md-2">
                                        <label class="control-label">Documento:</label>
                                    </div>

                                    <div class="controls">
                                        <div class="col-md-1">
                                            <input type="text" class='input-mini required form-control'
                                                   name="doc_serie" id="doc_serie"
                                                   autofocus="autofocus" <?php if (isset($ingreso->id_ingreso)) echo 'readonly' ?>
                                                   required="true"
                                                   maxlength="5" size="5"
                                                   value="<? echo isset($ingreso->documento_serie) ? $ingreso->documento_serie : '' ?>">
                                        </div>

                                        <div class="col-md-2">
                                            <input type="text" class='input-medium required form-control'
                                                   name="doc_numero" id="doc_numero"
                                                   required="true" <?php if (isset($ingreso->id_ingreso)) echo 'readonly' ?>
                                                   maxlength="20"
                                                   value="<? echo isset($ingreso->documento_numero) ? $ingreso->documento_numero : '' ?>">
                                        </div>
                                    </div>

                                </div>

                                <div class="col-md-2">
                                    <label for="cboTipDoc" class="control-label">Tipo Documento:</label>
                                </div>
                                <div class="col-md-3">
                                    <div class="controls">
                                        <select name="cboTipDoc" id="cboTipDoc" class='cho form-control'
                                                required="true">

                                            <option
                                                value="<?= BOLETAVENTA ?>" <? if (isset($ingreso->tipo_documento) && $ingreso->tipo_documento == BOLETAVENTA) echo 'selected'; ?>><?= BOLETAVENTA ?></option>
                                            <option
                                                value="<?= FACTURA ?>" <? if ((isset($ingreso->tipo_documento) && $ingreso->tipo_documento == FACTURA) or !isset($ingreso->tipo_documento)) echo 'selected'; ?>><?= FACTURA ?></option>
                                            <option
                                                value="<?= NOTAVENTA ?>" <? if (isset($ingreso->tipo_documento) && $ingreso->tipo_documento == NOTAVENTA) echo 'selected'; ?>><?= NOTAVENTA ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <div class="row">

                                <div class="control-group">
                                    <div class="col-md-2">
                                        <label for="Proveedor" class="control-label">Proveedor:</label>
                                    </div>
                                    <div class="controls">
                                        <div class="col-md-3">
                                            <select name="cboProveedor"
                                                    id="cboProveedor" <?php if (isset($ingreso->id_ingreso)) echo 'disabled' ?>
                                                    class='cho form-control' required="true" required="true">
                                                <?php if (count($lstProveedor) > 0): ?>
                                                    <?php foreach ($lstProveedor as $pv): ?>
                                                        <option
                                                            value="<?php echo $pv->id_proveedor; ?>" <?php if (!isset($ingreso->id_proveedor) && strtoupper($pv->proveedor_nombre) === 'OTROS') echo 'selected' ?>   <?php if (isset($ingreso->id_proveedor) && $ingreso->id_proveedor == $pv->id_proveedor) echo 'selected'; ?>><?php echo $pv->proveedor_nombre; ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                    </div>
                                </div>
                                <div class="control-group" style="display: <?=($costos === 'true') ? 'block' : 'none'?>">
                                    <div class="col-md-2">
                                        <label for="" class="control-label">Impuesto</label>
                                    </div>

                                    <div class="col-md-3">
                                        <select name="impuestos" id="impuestos" class='cho form-control'
                                                required="true" <?php if (isset($ingreso->id_ingreso)) echo 'disabled' ?>>
                                            <option value="0">Seleccione</option>
                                            <?php if (count($impuestos) > 0) { ?>
                                                <?php foreach ($impuestos as $impuesto) { ?>
                                                    <option
                                                        value="<?php echo $impuesto['porcentaje_impuesto']; ?>" <?php if (strtoupper($impuesto['nombre_impuesto']) == "IGV") echo 'selected' ?> ><?php echo $impuesto['nombre_impuesto'] ?></option>
                                                <?php }
                                            } ?>
                                        </select>
                                    </div>
                                </div>

                            </div>
                            <br>

                            <div class="row">

                                <div class="control-group">
                                    <div class="col-md-2">
                                        <label class="control-label">Local:</label>
                                    </div>

                                    <div class="controls">
                                        <div class="col-md-3">
                                            <select name="local" id="local" class='cho form-control'
                                                    required="true" <?php if (isset($ingreso->id_ingreso)) echo 'disabled' ?>>
                                                <option value="">Seleccione</option>
                                                <?php if (count($locales) > 0): ?>
                                                    <?php foreach ($locales as $local): ?>
                                                        <option
                                                            value="<?php echo $local['int_local_id']; ?>"
                                                            <?php if (!isset($ingreso->id_ingreso)) echo 'selected';
                                                            if (isset($ingreso->local_id) && $ingreso->local_id == $local['int_local_id']) echo 'selected'; ?>><?php echo $local['local_nombre']; ?></option>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>

                                    </div>

                                </div>

                                <div class="control-group" style="display: <?=($costos === 'true') ? 'block' : 'none'?>">
                                    <div class="col-md-2">
                                        <label class="control-label">Pago:</label>
                                    </div>

                                    <div class="controls">
                                        <div class="col-md-3">
                                            <select name="pago" id="pago" class='cho form-control'
                                                    required="true">
                                                <option value="">Seleccione</option>
                                                <option
                                                    value="CONTADO" <?php if ((isset($ingreso->pago) && $ingreso->pago == 'CONTADO') or !isset($ingreso->pago)) echo 'selected'; ?>>
                                                    CONTADO
                                                </option>
                                                <option
                                                    value="CREDITO" <?php if (isset($ingreso->pago) && $ingreso->pago == 'CREDITO') echo 'selected'; ?>>
                                                    CREDITO
                                                </option>
                                            </select>
                                        </div>

                                    </div>

                                </div>
                            </div>
                            <br>

                            <div class="row">

                                <div class="control-group">
                                    <div class="col-md-2">
                                        <label class="control-label">Producto:</label>
                                    </div>

                                    <div class="controls">
                                        <div class="col-md-8">
                                            <select name="cboProducto"
                                                    id="cboProducto" <?php if (isset($ingreso->id_ingreso)) echo 'disabled' ?>
                                                    class='cho form-control'
                                                    required="true">
                                                <option value="">Seleccione</option>
                                                <?php if (count($lstProducto) > 0): ?>
                                                    <?php foreach ($lstProducto as $pd): ?>
                                                        <option
                                                            value="<?php echo $pd['producto_id']; ?>"><?php echo sumCod($pd['producto_id']) . " - " . $pd['producto_nombre'] ?></option>
                                                    <?php endforeach; ?>
                                                <?php else : ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <?php if (!isset($ingreso->id_ingreso)): ?>
                                        <div class="col-md-2"><label class="control-label">Precio Unitario: </label> <span id="precio_unitario">0.0</span></div>
                                        <?php endif; ?>


                                    </div>

                                </div>


                            </div>
                            <br>


                        </div>

                        <br>

                        <div class="row">
                            <div class="control-group">
                                <div class="col-md-1">
                                    <label class="control-label">Unidades</label>
                                </div>

                                <div class="controls">
                                    <div class="col-md-2">
                                        <select name="unidades" id="unidades"
                                                class='cho form-control' <?php if (isset($ingreso->id_ingreso)) echo 'disabled' ?>>


                                        </select>
                                    </div>

                                </div>

                            </div>

                            <div class="control-group">
                                <div class="col-md-1">
                                    <label for="" class="control-label">Cantidad</label>
                                </div>

                                <div class="col-md-2">
                                    <input type="number" class='input-square input-mini form-control'
                                           name="cantp"
                                           id="cantp" <?php if (isset($ingreso->id_ingreso)) echo 'readonly' ?>
                                           onkeydown="return soloDecimal(this, event);">
                                </div>
                            </div>

                            <div class="control-group">
                                <div class="col-md-2" style="display: <?= ($costos === 'true') ? 'block' : 'none' ?>;">
                                    <label for="cboTipDoc" class="control-label">Precio Total</label>
                                </div>
                                <div class="col-md-2" style="display: <?= ($costos === 'true') ? 'block' : 'none' ?>;">
                                    <input type="text"
                                           class='form-control' <?php if (isset($ingreso->id_ingreso)) echo 'readonly' ?>
                                           name="precio" id="precio" value="0.0"
                                           onkeydown="return soloDecimal(this, event);"
                                </div>
                            </div>

                            <div class="col-md-1">

                                <a class="btn btn-primary" data-placement="bottom"
                                   style="margin-top:-2.2%;cursor: pointer;"
                                   onclick="listarProductos();" <?php if (isset($ingreso->id_ingreso)) echo 'disabled' ?>>Agregar</a>
                            </div>
                        </div>
                    </div>

                    <br>


                    <div class="row-fluid">
                        <div class="span12">
                            <div class="box">
                                <div class="box-head">
                                    <h3>Detalle Productos</h3>
                                </div>
                                <div class="box-content box-nomargin">
                                    <?php if (isset($ingreso->id_ingreso)): ?>
                                        <input type="hidden" id="editar_ingreso" value="1">
                                    <?php else:?>
                                        <input type="hidden" id="editar_ingreso" value="0">
                                    <?php endif;?>
                                    <div id="lstTabla">

                                        <?php
                                        $countproductos = 0;
                                        //var_dump($venta);
                                        if (isset($detalles)):
                                            foreach ($detalles as $detalle) { ?>

                                                <script type="text/javascript">


                                                    addToArray('<?= $detalle['costo_unitario']?>', '<?= $detalle['id_producto']?>', '<?= $detalle['producto_nombre']?>', '<?= $detalle['cantidad']?>', '<?= $detalle['costo_unitario'] * $detalle['cantidad']?>', '<?= $detalle['id_unidad']?>', '<?= $detalle['nombre_unidad']?>');

                                                </script>
                                                <?php $countproductos++;
                                            }endif;
                                        ?>


                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                    <br/>

                    <div class="row">


                        <div class="control-group">
                            <div class="col-md-3">
                                <label for="subTotal" class="control-label">SubTotal:</label>

                                <div class="controls">
                                    <div class="input-prepend input-append">
                                        <input type="text" class='input-square input-small form-control'
                                               name="subTotal" id="subTotal" value="0" readonly>
                                    </div>
                                </div>
                            </div>

                        </div>


                        <div class="control-group" style=" <?php if ($costos === 'false') echo 'display:none' ?>">
                            <div class="col-md-3">
                                <label for="montoigv" class="control-label">Total Impuesto</label>

                                <div class="controls">
                                    <div class="input-prepend input-append">

                                        <input type="text" class='input-square input-small form-control'
                                               name="montoigv" value="0"
                                               id="montoigv"
                                               readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="control-group" style=" <?php if ($costos === 'false') echo 'display:none' ?>">
                            <div class="col-md-3">
                                <label class="control-label">Total a Pagar:</label>

                                <div class="controls">
                                    <div class="input-prepend input-append">
                                        <input type="text" class='input-square input-small form-control'
                                               name="totApagar" id="totApagar" readonly>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>

                    <br>

                    <div class="block-options">

                        <div class="form-actions">

                            <button class="btn" id="btnGuardar" type="button"><i
                                    class="fa fa-save fa-3x text-info fa-fw"></i> <br>Guardar
                            </button>
                            <!-- <button type="button" class="btn"><i class="fa fa-folder-open-o fa-3x text-info"></i><br>Abrir </button>-->

                            <?php if (!isset($ingreso->id_ingreso)) { ?>
                                <button type="reset" class="btn" id="reiniciar"><i
                                        class="fa fa-refresh fa-3x text-info fa-fw"></i><br>Reiniciar
                                </button><?php } ?>
                            <button class="btn" type="button" id="cancelar"><i
                                    class="fa fa-remove fa-3x text-warning fa-fw"></i><br>Cancelar
                            </button>
                        </div>
                    </div>


            </div>

            </form>

        </div>

    </div>
</div>

<div class="modal fade" id="confirmarmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="$('#confirmarmodal').modal('hide');" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
                <p>Est&aacute; seguro que desea registrar el ingreso de los productos seleccionados?</p>
                <input type="hidden" name="id" id="id_borrar">

            </div>
            <div class="modal-footer">
                <button type="button" id="botonconfirmar" class="btn btn-primary" onclick="guardaringreso();">
                    Confirmar
                </button>
                <button type="button" class="btn btn-default" onclick="$('#confirmarmodal').modal('hide');">Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>

</div>

<div class="modal fade" id="modificarcantidad" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


    <div class="modal-dialog ">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodificarcantidad" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Editar cantidad</h4> <h5 id="nombreproduto2"></h5>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">Unidad:</div>
                        <div class="col-md-10">
                            <select name="unidadedit" id="unidadedit" class='cho form-control'>


                            </select>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">Cantidad:</div>
                        <div class="col-md-10">
                            <input type="number" id="cantidadedit" class="form-control"
                                   onkeydown="return soloDecimal3(this, event);">
                        </div>
                    </div>
                </div>
                <?php if ($costos === 'true'): ?>
                    <div class="row">
                        <div class="form-group">

                            <div class="col-md-2">Total:</div>
                            <div class="col-md-10">
                                <input type="number" id="totaledit" class="form-control"
                                       onkeydown="return soloDecimal3(this, event);">
                            </div>
                        </div>
                    </div>
                <?php endif ?>


            </div>

            <div class="modal-footer">

                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-default" type="button" id="guardarcantidad"><i
                                class="fa fa-save"></i>Guardar
                        </button>

                        <button class="btn btn-default closemodificarcantidad" type="button"><i
                                class="fa fa-close"></i> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script>$(function () {

        $("select").chosen({width: '100%'});
        $("#fecEmision").datepicker({format: 'dd-mm-yyyy'});
        // TablesDatatables.init();

    });</script>


<script>
    var countproducto = <?= $countproductos?>;
    var ruta = '<?php echo $ruta; ?>';
</script>

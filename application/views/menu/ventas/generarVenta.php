<?php $ruta = base_url(); ?>
<?php $disabled = (isset($devolver)) ? 'disabled' : ''; ?>
<script src="<?php echo $ruta; ?>recursos/js/generarventa.js"></script>
<div id="inentariocontainer" style="display: none;"></div>
<input type="hidden" id="producto_cualidad" value="">
<input type="hidden" id="devolver" value="<?php echo isset($devolver) ? 'true' : 'false'; ?>">
<input type="hidden" id="idlocal" value="<?= $this->session->userdata('id_local'); ?>">
<input type="hidden" id="preciosugerido" value="<?php echo isset($preciosugerido) ? 'true' : 'false'; ?>">
<script>
    var countproducto = 0;
</script>
<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Generar ventas</a></li>
</ul>
<!-- END Datatables Header -->
<div class="block">

    <!-- Progress Bars Wizard Title -->

    <form method="post" id="frmVenta" action="#" class=''>
        <input type="hidden" name="url_refresh" id="url_refresh" value="">
        <input type="hidden" id="precio_sugerido" value="0">
        <input type="hidden" name="diascondicionpagoinput" id="diascondicionpagoinput"
               value="<?php if (isset($venta[0]['id_condiciones'])) echo $venta[0]['id_condiciones'] ?>">
        <input type="hidden" name="idventa" id="idventa"
               value="<?php if (isset($venta[0]['venta_id'])) echo $venta[0]['venta_id'] ?>">
        <input type="hidden" name="venta_tipo" id="venta_tipo"
               value="<?= VENTA_CAJA ?>">
        <input type="hidden" name="vendedor" id="vendedor"
               value="<?php echo $this->session->userdata("nUsuCodigo"); ?>">
        <input type="hidden" name="isadmin" id="isadmin"
               value="<?php echo $this->session->userdata("admin"); ?>">
        <div class="row">
            <div class="form-group">
                <div class="col-md-1">
                    <label for="cliente" class="control-label">Cliente</label>
                </div>
                <div class="col-md-5">
                    <select name="id_cliente" id="id_cliente" class='form-control' required="true" <?= $disabled; ?>>
                        <option value="">Seleccione</option>
                        <?php if (count($clientes) > 0): ?>
                            <?php foreach ($clientes as $cl): ?>
                                <option
                                    value="<?php echo $cl['id_cliente']; ?>" <?php if ((isset($venta[0]['cliente_id']) and $venta[0]['cliente_id'] == $cl['id_cliente']) or (!isset($venta[0]['cliente_id']) && $cl['razon_social'] == 'Cliente Frecuente'))
                                    echo 'selected' ?>><?php echo $cl['razon_social']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>

        <div class="row panel">
            <div class="form-group">
                <div class="col-md-1">
                    <label for="cboTipDoc" class="control-label">Buscar Producto:</label>
                </div>
                <div class="col-md-5">
                    <select class="form-control" style="width: 100%" id="selectproductos"
                            onchange="buscarProducto()" <?= $disabled; ?>>
                        <option value="">Seleccione</option>
                        <?php foreach ($productos as $producto) { ?>
                            <option
                                value="<?= $producto['producto_id'] ?>"><?= sumCod($producto['producto_id']) . " - " . $producto['producto_nombre'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-1">
                    <?php if (!isset($devolver)): ?>
                        <button type="button" id="refrescarstock" class="btn btn-primary"><i class="fa fa-refresh"></i>
                            Refrescar
                        </button>
                    <?php endif ?>
                </div>
            </div>
        </div>

        <div class="row ">
            <div class="col-md-8">
                <div class="table-responsive block">
                    <div id="" class="table-responsive" style="height: 400px;    overflow-y: auto;">
                        <table class="table dataTable dataTables_filter table-bordered">
                            <thead>
                            <tr style="background-color: #B1AEAE; color:white;">
                                <th style="padding-top: 0px; padding-bottom: 0px; ">#</th>
                                <th style="padding-top: 0px; padding-bottom: 0px">ID</th>
                                <th style="padding-top: 0px; padding-bottom: 0px">Nombre</th>
                                <th style="padding-top: 0px; padding-bottom: 0px">UM</th>
                                <th style="padding-top: 0px; padding-bottom: 0px">Cantidad</th>
                                <th style="padding-top: 0px; padding-bottom: 0px">Precio</th>
                                <!--<th>Descuento %</th>-->
                                <th style="padding-top: 0px; padding-bottom: 0px">Subtotal</th>
                                <th style="padding-top: 0px; padding-bottom: 0px"></th>
                                <th style="padding-top: 0px; padding-bottom: 0px"></th>
                                <th style="padding-top: 0px; padding-bottom: 0px"></th>
                            </tr>
                            </thead>
                            <tbody id="tbodyproductos">
                            <?php
                            $countproductos = 0;
                            foreach ($venta as $ven) {

                                if ($ven['preciounitario'] > 0) {
                                    $bono = $countproductos;
                                }

                                ?>
                                <script type="text/javascript">

                                    var nombre = "<?php echo $ven["nombre"]; ?>";
                                    calculatotales(<?php echo $ven['producto_id']; ?>, encodeURIComponent(nombre), '<?php echo $ven["nombre_unidad"]; ?>', <?php echo $ven['cantidad']; ?>, <?php echo $ven['preciounitario']; ?>, <?php echo $ven['importe']; ?>, <?php echo $ven['porcentaje_impuesto']; ?>, <?php echo $countproductos; ?>, <?php echo $ven['unidades']; ?>, '<?php echo $ven["producto_cualidad"]; ?>', <?php echo $ven['id_unidad']; ?>, <?php echo $ven['precio_sugerido'] ?>, buscar_bono(<?= $ven['bono'] ?>));
                                    addProductoToArray(<?php echo $ven['producto_id']; ?>, encodeURIComponent(nombre), <?php echo $ven['id_unidad']; ?>, '<?php echo $ven["nombre_unidad"]; ?>', <?php echo $ven['cantidad']; ?>, <?php echo $ven['preciounitario']; ?>, <?php echo isset($ven['precio_sugerido'])?$ven['precio_sugerido']:0;  ?>, <?php echo $ven['importe']; ?>, <?php echo $ven['unidades']; ?>, '<?php echo $ven["producto_cualidad"]; ?>', <?php echo $ven['porcentaje_impuesto']; ?>, buscar_bono(<?= $ven['bono'] ?>), <?php echo $ven['venta_sin_stock']; ?>);
                                </script>
                                <?php $countproductos++; ?>
                                <?php
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-4 block">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="subTotal" class="control-label">Sub-Total:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="input-prepend input-append input-group">
                                <span class="input-group-addon"><?= MONEDA ?></span>
                                <input type="text"
                                       class='input-square input-small form-control'
                                       name="subTotal" id="subTotal" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="montoigv" class="control-label">Impuesto:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="input-prepend input-append input-group">
                                <span class="input-group-addon"><?= MONEDA ?></span>
                                <input type="text" class='input-square input-small form-control' name="montoigv"
                                       id="montoigv" readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label">Total:</label>
                        </div>
                        <div class="col-md-9">
                            <div class="input-prepend input-append input-group">
                                <span class="input-group-addon"><?= MONEDA ?></span><input style="font-size: 14px;
																							font-weight: bolder;"
                                                                                           type="text"
                                                                                           class='input-square input-small form-control'
                                                                                           name="totApagar"
                                                                                           id="totApagar"
                                                                                           readonly value="0.00">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label">Pago</label>
                        </div>
                        <div class="col-md-9">
                            <select name="condicion_pago" id="cboModPag" onchange="activarText_ModoPago()"
                                    class="form-control" <?= $disabled; ?>>
                                <option value="">Seleccione</option>
                                <?php if (count($condiciones_pago) > 0): ?>
                                    <?php foreach ($condiciones_pago as $lc): ?>
                                        <option value="<?php echo $lc['id_condiciones']; ?>"
                                            <?php if ((isset($venta[0]['id_condiciones']) and $venta[0]['id_condiciones'] == $lc['id_condiciones']) OR (!isset($venta[0]['id_condiciones']) and $lc['dias'] == 0)) echo 'selected' ?>>
                                            <?php echo $lc['nombre_condiciones'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <?php if (count($condiciones_pago) > 0): ?>
                                <?php foreach ($condiciones_pago as $lc): ?>
                                    <input type="hidden" id="diascondicionpago<?= $lc['id_condiciones']; ?>"
                                           value="<?= $lc['dias']; ?>"/>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="tipo_documento" class="control-label">Tipo Documento:</label>
                        </div>
                        <div class="col-md-9">
                            <select name="tipo_documento" id="tipo_documento" class="form-control" <?= $disabled; ?>>
                                <option value="">Seleccione</option>
                                <option
                                    value="<?= BOLETAVENTA ?>" <?php if ((isset($venta[0]['documento_tipo']) and $venta[0]['documento_tipo'] == BOLETAVENTA) or !isset($venta[0])) echo 'selected' ?>><?= BOLETAVENTA ?></option>
                                <option
                                    value="<?= FACTURA ?>" <?php if (isset($venta[0]['documento_tipo']) and $venta[0]['documento_tipo'] == FACTURA) echo 'selected' ?>><?= FACTURA ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="cboTipDoc" class="control-label">Fecha:</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" class="form-control" readonly id="fecha" name="fecha"
                                   value="<?= isset($venta[0]['fechaemision']) ? $venta[0]['fechaemision'] : date('d/m/Y') ?>">

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label">Estado</label>
                        </div>
                        <div class="col-md-9">
                            <select name="venta_status" id="venta_status" class="form-control" <?= $disabled; ?>>
                                <option value="">Seleccione</option>
                                <option
                                    value="<?= COMPLETADO ?>" <?php if ((isset($venta[0]['venta_status']) && $venta[0]['venta_status'] == COMPLETADO) or !isset($venta[0]['venta_status'])) echo 'selected' ?>><?= COMPLETADO ?></option>
                                <option
                                    value="<?= ESPERA ?>" <?php if (isset($venta[0]['venta_status']) && $venta[0]['venta_status'] == ESPERA) echo 'selected' ?>><?= ESPERA ?></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label class="control-label">Total Productos</label>
                        </div>
                        <div class="col-md-9">
                            <span id="totalproductos"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="generarventa" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog" style="width: 40%">
                    <div class="modal-content">
                        <div class="modal-body">
                            <h4>Terminar Venta</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label for="totApagar2" class="control-label">Total a Pagar:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-prepend input-append input-group">
                                            <span class="input-group-addon"><?= MONEDA ?></span><input type="number"
                                                                                                       class='input-square input-small form-control'
                                                                                                       min="0.0"
                                                                                                       step="0.1"
                                                                                                       value="0.0"
                                                                                                       id="totApagar2"
                                                                                                       readonly
                                                                                                       onkeydown="return soloDecimal(this, event);">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <?php if (isset($venta[0])) { ?>
                                <div class="row" id="pagadodiv">
                                    <div class="form-group">
                                        <div class="col-md-3">
                                            <label for="importe" class="control-label">A cuenta:</label>
                                        </div>
                                        <div class="col-md-9">
                                            <div class="input-prepend input-append input-group">
                                                <span class="input-group-addon"><?= MONEDA ?></span>
                                                <input type="number" tabindex="0"
                                                       class='input-square input-small form-control'
                                                       min="0.0" step="0.1"
                                                       value="<?php echo isset($venta[0]['pagado']) ? $venta[0]['pagado'] : '0.00' ?>"
                                                       readonly name="pagado" id="pagado"
                                                       onkeydown="return soloDecimal(this, event);"
                                                       onkeyup="calcular_importe();">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="row" id="importediv">
                                <div class="form-group">
                                    <div class="col-md-3">
                                        <label for="importe" class="control-label">Importe:</label>
                                    </div>
                                    <div class="col-md-9">
                                        <div class="input-prepend input-append input-group">
                                            <span class="input-group-addon"><?= MONEDA ?></span><input type="number"
                                                                                                       tabindex="0"
                                                                                                       class='input-square input-small form-control'
                                                                                                       min="0.0"
                                                                                                       step="0.1"
                                                                                                       value="0.0"
                                                                                                       name="importe"
                                                                                                       id="importe"
                                                <?php if(isset($devolver)) echo 'readonly';?>
                                                                                                       onkeydown="return soloDecimal(this, event);"
                                                                                                       onkeyup="calcular_importe();">
                                        </div>
                                    </div>
                                </div>
                            </div>



                        </div>
                        <div class="modal-footer">

                            <div class="row">
                                <div class="col-md-12">
                                    <button class="btn btn-primary" type="button" id="realizarventa"
                                            onclick="javascript:hacerventa(0);"><li class="glyphicon glyphicon-thumbs-up"></li> Guardar
                                    </button>
                                    <a href="#" class="btn btn-primary" id="btnRealizarVentaAndView"
                                       onclick="javascript:hacerventa(1);" type="button"><li class="glyphicon glyphicon-thumbs-up"></li> (F6)Guardar e imprimir
                                    </a>
                                    <button class="btn btn-warning closegenerarventa" type="button">
                                    <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal fade bs-example-modal-lg" id="seleccionunidades" tabindex="-1" role="dialog"
                 aria-labelledby="myModalLabel"
                 aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close closeseleccionunidades"
                                    aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Existencia del producto</h4> <h5 id="nombreproduto"></h5>
                        </div>
                        <div class="modal-body" id="modalbodyproducto">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-1">Precio:</div>
                                    <div class="col-md-5">
                                        <select class="form-control" name="precio" id="precios" tabindex="0"
                                                onchange="cambiarnombreprecio()" style="width:250px">
                                            <option value="">Seleccione</option>
                                            <?php foreach ($precios as $precio) { ?>
                                                <option
                                                    value="<?= $precio['id_precio'] ?>" <?php if ($precio['nombre_precio'] == 'Precio Venta') echo 'selected' ?>><?= $precio['nombre_precio'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">Cantidad en Stock:</div>
                                    <div class="col-md-3">
                                        <span id="stock"></span>
                                    </div>
                                </div>
                            </div>
                            </br>

                            <div class="row">
                                <table class="table datatable table-bordered">
                                    <thead>
                                    <th>Presentacion</th>
                                    <th>Unidades</th>
                                    <th id="tituloprecio"></th>
                                    </thead>
                                    <tbody id="preciostbody"></tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-2">Cantidad:</div>
                                    <div class="col-md-3">
                                        <input type="number" readonly id="cantidad" class="form-control"
                                               onkeydown="return soloDecimal3(this, event);">
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <a href="#" class="btn btn-primary" id="agregarproducto">Agregar Producto</a>
                            <a href="#" class="btn btn-warning closeseleccionunidades">Salir</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="block-options">
        <div class="form-actions">
            <button class="btn" id="terminarventa" type="button"><i class="fa fa-save fa-3x text-info fa-fw"></i> </br>
                F6 Guardar
            </button>
            <button type="button" class="btn" id="abrirventas"><i
                    class="fa fa-folder-open-o fa-3x text-info fa-fw"></i> </br>Abrir
            </button>
            <button type="button" class="btn" id="reiniciar"><i class="fa fa-refresh fa-3x text-info fa-fw"></i> </br>
                Reiniciar
            </button>
            <button class="btn" type="button" id="cancelar"><i class="fa fa-remove fa-3x text-warning fa-fw"></i> </br>
                Cancelar
            </button>
        </div>
    </div>
</div>

<div class="modal fade" id="mvisualizarVenta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>

<div class="modal fade" id="ventasabiertas" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>
<div class="modal fade" id="modificarcantidad" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close closemodificarcantidad" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Editar cantidad</h4> <h5 id="nombreproduto2"></h5>
            </div>
            <div class="modal-body" id="modalbodycantidad">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">Cantidad:</div>
                        <div class="col-md-3">
                            <input type="number" id="cantidadedit" class="form-control"
                                   onkeydown="return soloDecimal3(this, event);">
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">Precio:</div>
                        <div class="col-md-3">
                            <input type="number" id="precioedit" class="form-control"
                                   onkeydown="return soloDecimal3(this, event);">
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <div class="row">
                    <div class="col-md-12">
                        <button class="btn btn-primary" type="button" id="guardarcantidad">
                            <li class="glyphicon glyphicon-thumbs-up"></li> Guardar
                        </button>
                        <button class="btn btn-warning closemodificarcantidad" type="button">
                            <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="confirmar_delete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmaci&oacute;n</h4>
            </div>

            <div class="modal-body">

                <h4>¿Estas seguro que deseas eliminar este producto?</h4>

            </div>

            <div class="modal-footer">
                <button type="button" id="eliminar_item" class="btn btn-primary">
                    <li class="glyphicon glyphicon-thumbs-up"></li> Confirmar
                </button>
                <button type="button" class="btn btn-warning" onclick="$('#confirmar_delete').modal('hide');">
                    <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar
                </button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>

</div>


<script>
    var countproducto = <?= $countproductos?>;
    var ruta = '<?php echo $ruta; ?>';
    function buscar_bono(bono) {
        console.log('buscando');
        console.log(bono);

        if (bono == 0) {
            return 'false'
        } else {
            return 'true'
        }
    }
</script>

<?php $ruta = base_url(); ?>
<?php $disabled = (isset($devolver) or isset($preciosugerido)) ? 'disabled' : ''; ?>
<script src="<?php echo $ruta; ?>recursos/js/generarventa.js"></script>
<div id="inentariocontainer" style="display: none;"></div>
<input type="hidden" id="producto_cualidad" value="">
<input type="hidden" id="devolver" value="<?php echo isset($devolver) ? 'true' : 'false'; ?>">
<input type="hidden" id="coso_id" value="<?php echo isset($coso_id) ? $coso_id : 'false'; ?>">
<input type="hidden" id="preciosugerido" value="<?php echo isset($preciosugerido) ? 'true' : 'false'; ?>">
<input type="hidden" id="idlocal" value="<?= $this->session->userdata('id_local'); ?>">
    <style>
legend {
    display: block;
    width: auto;
    padding: 0px;
    margin-bottom: 0;
    font-size: inherit;
    line-height: inherit;
    border: auto;
    border-bottom: none;
}

fieldset {
    border: 3px groove threedface;
    padding: 5px;
}
    </style>
<script>
    var countproducto = 0;
</script>
<ul class="breadcrumb breadcrumb-top">
    <li>Flujo de Trabajo</li>
    <li><a href="">Generar pedidos</a></li>
</ul>

<!-- END Datatables Header -->
<div class="block">

    <!-- Progress Bars Wizard Title -->

    <form method="post" id="frmVenta" action="#" class="">
        <input type="hidden" name="url_refresh" id="url_refresh" value="<?php echo isset($preciosugerido) ? '//consultar?buscar=pedidos' : '/pedidos'; ?>">
        <input type="hidden" name="venta_status" id="venta_status"
               value="<?php echo isset($devolver) ? PEDIDO_DEVUELTO : PEDIDO_GENERADO ?>">
        <input type="hidden" name="venta_tipo" id="venta_tipo"
               value="<?= VENTA_ENTREGA ?>">
        <input type="hidden" name="diascondicionpagoinput" id="diascondicionpagoinput"
               value="<?php if (isset($venta[0]['id_condiciones'])) echo $venta[0]['id_condiciones']; ?>">
        <input type="hidden" name="idventa" id="idventa"
               value="<?php if (isset($venta[0]['venta_id'])) echo $venta[0]['venta_id']; ?>">

        <input type="hidden" name="estatus_consolidado" id="estatus_consolidado"
               value="<?php if (isset($estatus_consolidado)) echo $estatus_consolidado; ?>">

        <input type="hidden" name="vendedor" id="vendedor"
               value="<?php echo $this->session->userdata("nUsuCodigo"); ?>">
        <input type="hidden" name="isadmin" id="isadmin"
               value="<?php echo $this->session->userdata("admin"); ?>">

        <div id="credito_value"></div>

        <div class="row form-group">
            <div class="col-xs-12">
                <ul class="nav nav-pills nav-justified thumbnail setup-panel">
                    <li class="active"><a href="#step-1">
                            <h4 class="list-group-item-heading">Paso 1</h4>

                            <p class="list-group-item-text">Clientes y tipo de pagos</p>
                        </a></li>
                    <li class="disabled"><a href="#step-2">
                            <h4 class="list-group-item-heading">Paso 2</h4>

                            <p class="list-group-item-text">Seleccion de Productos y envio de Pedido</p>
                        </a></li>
                    <!-- <li class="disabled"><a href="#step-3" style="display: none;">
                            <h4 class="list-group-item-heading">Paso 3</h4>

                            <p class="list-group-item-text">Enviar Pedidos</p>
                        </a></li> -->
                </ul>
            </div>
        </div>
        <div class="row setup-content" id="step-1">
            <div class="col-xs-12">
                <fieldset class="col-md-12" style="margin: -2% 1% 1% 0%;">
                    <legend>Datos Claves</legend>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="zona" class="control-label">Zona</label>
                                <select name="zona" id="zona" class='form-control' required="true" >
                                    <option value="">Seleccione</option>

                                </select>
                            </div>
                                <input type="checkbox" name="todasZona" id="todasZonas"> Todas las zonas
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Tipo de Pago</label>
                                <select name="condicion_pago" id="cboModPag" onchange="activarText_ModoPago()"
                                        class="form-control" <?= $disabled; ?>></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="control-label">Documento</label>
                                <select name="tipo_documento" id="tipo_documento" onchange="" class="form-control" <?= $disabled; ?>>
                                    <option value="">Seleccione</option>
                                    <option value="<?= BOLETAVENTA ?>" selected><?= BOLETAVENTA ?>
                                    </option>
                                    <option value="<?= FACTURA ?>"><?= FACTURA ?>
                                    </option>
                                </select>

                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <button id="activate-step-2" class="btn btn-primary">Continuar</button>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <div class="sidebar-user-name"><?=

                                    date('D d - m - Y').'<br> '.$this->session->userdata('nombre')

                                ?></div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <?php
                ?>
                <div id='content_opcion' style="display: none">
                    <fieldset class="col-md-6" style="margin: 0% 1% 1% 0%; width: 49%">
                        <legend>Cliente</legend>
                        <div class="col-md-12">
                            <div class="col-md-12 form-group">
                                <label for="zona" class="control-label">Cliente</label>
                                <select name="id_cliente" id="id_cliente" class='form-control'
                                        required="true" <?= $disabled; ?>>
                                    <option value="">Seleccione</option>
                                    <?php if (count($clientes) > 0) { ?>
                                        <?php foreach ($clientes as $cl): ?>
                                            <option
                                                value="<?php echo $cl['id_cliente']; ?>" <?php if ((isset($venta[0]['cliente_id']) and $venta[0]['cliente_id'] == $cl['id_cliente']) or (!isset($venta[0]['cliente_id']) && $cl['razon_social'] == 'Cliente Frecuente'))
                                                echo 'selected' ?>><?php echo $cl['razon_social']?></option>
                                        <?php endforeach; ?>
                                    <?php } else {
                                        if (isset($venta[0])) {
                                            ?>
                                            <option
                                                value="<?php echo isset($venta[0]['cliente_id']) ? $venta[0]['cliente_id'] : '' ?>" <?php if ((isset($venta[0]['cliente_id'])))
                                                echo 'selected' ?>><?php echo $venta[0]['cliente']; ?></option>

                                        <?php }
                                    } ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-2">
                                <label for="zona" class="control-label panel-admin-text">Retenci&oacute;n</label>
                            </div>
                            <div class="col-md-4">
                                <input type="number" name="retencion" readonly="readonly" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <input id="cambiar_retencion" type="checkbox" name="cambiar_retencion">
                                <label for="cambiar_retencion" class="control-label panel-admin-text"> Cambiar retenci&oacute;n</label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-6">
                                <label for="" class="control-label panel-admin-text"><u>Estado de cuenta actual</u></label>
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-4">
                                <label for="" class="control-label panel-admin-text">Deuda Actual</label>
                            </div>
                            <div class="col-md-6 col-md-12">
                                <input type="number" name="retencion" readonly="readonly" class="form-control">
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-4">
                                <label for="" class="control-label panel-admin-text">Documentos Pendientes</label>
                            </div>
                            <div class="col-md-6">
                                <input type="number" name="retencion" readonly="readonly" class="form-control">
                            </div>

                        </div>
                    </fieldset>

                    <fieldset id='div_nota_pedido' class="col-md-6">
                        <legend>Nota Pedido</legend>
                            <div class="form-group col-md-12">
                                <div class="col-md-3">
                                    <label for="zona" class="control-label panel-admin-text">Cliente</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" class="form-control" name="cliente_nt" id="cliente_nt" style="">

                                </div>

                            </div>
                            <div class="form-group col-md-12">
                                <div class="col-md-3">
                                    <label for="direccion_entrega_np" class="control-label panel-admin-text">Direccion Entrega</label>
                                </div>
                                <div class="col-md-8">
                                    <select name="direccion_entrega_np" id="direccion_entrega_np" class='form-control' required="true" style="">
                                        <option value="">Seleccione</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group col-md-12">
                                <div class="col-md-3">
                                    <label for="contacto_nt" class="control-label panel-admin-text">Contacto</label>
                                </div>
                                <div class="col-md-8">
                                    <input type="text" name="contacto_nt" class="form-control" id="contacto_nt" style="">
                                </div>
                            </div>
                    </fieldset>

                    <fieldset id='div_documento' class="col-md-6" style="margin: 0% 0% 1% 0%;">
                        <legend>Documento</legend>

                        <div class="form-group col-md-12">
                            <div class="col-md-3">
                                <label for="ruc_dc" class="control-label panel-admin-text">RUC</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="ruc_dc" class="form-control" id="ruc_dc" style="">
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-3">
                                <label for="razon_social" class="control-label panel-admin-text">Razon Social</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="razon_social" class="form-control" id="razon_social" style="">
                            </div>
                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-3">
                                <label for="direccion_principal" class="control-label panel-admin-text">Direcci&oacute;n Principal</label>
                            </div>
                            <div class="col-md-8">
                                <select name="direccion_principal" readonly id="direccion_principal" class='form-control' required="true" >
                                    <option value="">Seleccione</option>
                                </select>
                            </div>

                        </div>
                        <div class="form-group col-md-12">
                            <div class="col-md-3">
                                <label for="direccion_entrega_doc" class="control-label panel-admin-text">Direcci&oacute;n Entrega</label>
                            </div>
                            <div class="col-md-8">
                                <select name="direccion_entrega_doc" id="direccion_entrega_doc" class='form-control' required="true" >
                                    <option value="">Seleccione</option>
                                </select>
                            </div>

                        </div>
                    </fieldset>
                </div>
            </div>
        </div>
        <div class="row setup-content" id="step-2">
            <div class="col-xs-12">
                <div class="col-md-12 well">
                    <div class="row panel">
                        <div class="form-group">
                            <div class="col-md-1">
                                <label for="cboTipDoc" class="control-label panel-admin-text">Cliente</label>
                            </div>
                            <div class="col-md-5">
                                <span
                                    id="clienteinformativo"><?php if (isset($venta[0]['cliente'])) echo $venta[0]['cliente'] ?></span>
                            </div>

                        </div>
                    </div>

                    <div class="row panel">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label for="cboTipDoc" class="control-label panel-admin-text">Buscar Producto:</label>
                            </div>
                            <div class="col-md-6">
                                <select class="form-control" style="width: 100%" id="selectproductos"
                                        onchange="buscarProducto()" <?= $disabled; ?>></select>
                            </div>
                            <div class="col-md-4">
                                <div class="btn-group">
                                    <?php if (!isset($devolver) && !isset($preciosugerido)) { ?>
                                        <button type="button" id="refrescarstock" class="btn btn-default">
                                            <i class="fa fa-refresh"></i> Refrescar
                                        </button>

                                    <?php } ?>
                                    <!-- <button id="activate-step-3" class="btn btn-primary">Continuar</button> -->
                                </div>
                            </div>
                            <?php if ($estatus_actual == PEDIDO_DEVUELTO) { ?>
                                <div class="col-md-2">
                                    <div class="modal-footer">
                                        <button type="button" id="estado_original" class="btn btn-primary"
                                                onclick="estado_oiginal()">Resetear
                                        </button>

                                    </div>
                                </div>  <?php } ?>
                        </div>
                    </div>

                    <div class="row ">

                        <div class=" col-md-9 block">
                            <div id="" class="table-responsive" style="height: 400px; overflow-y: auto;">
                                <table class="table dataTable dataTables_filter table-bordered">
                                    <thead>
                                    <tr style="background-color: #B1AEAE; color:white;">
                                        <th style="padding-top: 0px; padding-bottom: 0px; ">#</th>
                                        <th style="padding-top: 0px; padding-bottom: 0px">ID</th>
                                        <th style="padding-top: 0px; padding-bottom: 0px">Nombre</th>
                                        <th style="padding-top: 0px; padding-bottom: 0px">UM</th>
                                        <th style="padding-top: 0px; padding-bottom: 0px">Cant.</th>
                                        <th style="padding-top: 0px; padding-bottom: 0px">Precio</th>
                                        <?php if (isset($preciosugerido)) { ?>

                                            <th style="padding-top: 0px; padding-bottom: 0px">Sugerido</th>
                                        <?php } ?>
                                        <th style="padding-top: 0px; padding-bottom: 0px">Subtotal</th>
                                        <th style="padding-top: 0px; padding-bottom: 0px"></th>
                                        <th style="padding-top: 0px; padding-bottom: 0px"></th>


                                        <th style="padding-top: 0px; padding-bottom: 0px"></th>

                                    </tr>
                                    </thead>
                                    <tbody id="tbodyproductos">
                                    <?php $countproductos = 0;
                                    $bono = $countproductos;
                                    ?>
                                    <?php foreach ($venta as $ven) {


                                        if ($ven['preciounitario'] > 0) {
                                            $bono = $countproductos;
                                        }

                                        ?>
                                        <script type="text/javascript">
                                            var nombre = "<?php echo $ven["nombre"]; ?>";

                                            calculatotales(<?php echo $ven['producto_id']; ?>, encodeURIComponent(nombre), '<?php echo $ven["nombre_unidad"]; ?>', <?php echo $ven['cantidad']; ?>, <?php echo $ven['preciounitario']; ?>, <?php echo $ven['importe']; ?>, <?php echo $ven['porcentaje_impuesto']; ?>, <?php echo $countproductos; ?>, <?php echo $ven['unidades']; ?>, '<?php echo $ven["producto_cualidad"]; ?>', <?php echo $ven['id_unidad']; ?>, <?php echo $ven['precio_sugerido'] ?>, <?php echo ($ven['bono'] == 0) ? '\'false\'' : '\'true\'';?>);
                                            addProductoToArray(<?php echo $ven['producto_id']; ?>, encodeURIComponent(nombre), <?php echo $ven['id_unidad']; ?>, '<?php echo $ven["nombre_unidad"]; ?>', <?php echo $ven['cantidad']; ?>, <?php echo $ven['preciounitario']; ?>, <?php echo isset($ven['precio_sugerido']) ? $ven['precio_sugerido'] : 0;  ?>, <?php echo $ven['importe']; ?>, <?php echo $ven['unidades']; ?>, '<?php echo $ven["producto_cualidad"]; ?>', <?php echo $ven['porcentaje_impuesto']; ?>, <?php echo ($ven['bono'] == 0) ? '\'false\'' : '\'true\'';?>, <?php echo $ven['venta_sin_stock']; ?>);
                                        </script>
                                        <?php $countproductos++; ?>
                                        <?php

                                    } ?>
                                    </tbody>
                                </table>
                            </div>

                        </div>
                        <input type="hidden" id="accion_resetear" name="accion_resetear">

                        <div class="col-md-3 block">

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="cboTipDoc" class="control-label panel-admin-text">Fecha:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" readonly id="fecha" name="fecha" style="text-align: right;"
                                               value="<?= isset($venta[0]['fechaemision']) ? $venta[0]['fechaemision'] : date('d/m/Y'); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="subTotal" class="control-label panel-admin-text">Sub-Total:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-prepend input-append input-group">
                                            <span class="input-group-addon"><?= MONEDA; ?></span>
                                            <input type="text"
                                                   class='input-square input-small form-control'
                                                   name="subTotal" id="subTotal" readonly value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="montoigv" class="control-label panel-admin-text">Impuesto:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-prepend input-append input-group">
                                            <span class="input-group-addon"><?= MONEDA; ?></span>
                                            <input type="text" class='input-square input-small form-control'
                                                   name="montoigv"
                                                   id="montoigv" readonly value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label class="control-label panel-admin-text">Total:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="input-prepend input-append input-group">
                                            <span class="input-group-addon"><?= MONEDA; ?></span>
                                            <input style="font-size: 14px; font-weight: bolder; background: #FFEB9C;" type="text"
                                                   class='input-square input-small form-control'
                                                   name="totApagar"
                                                   id="totApagar"
                                                   readonly value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <br>

                            <div class="row">
                                <div class="form-group">
                                        <div class="col-md-6" style="text-align: center">

                                            <button class="btn btn-danger closegenerarventa" type="button">
                                                <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar
                                            </button>

                                        </div>
                                        <div class="col-md-6">
                                             <button class="btn btn-primary" type="button" id="realizarventa" onclick="javascript:hacerventa(0);">
                                                <li class="glyphicon glyphicon-thumbs-up"></li> Guardar
                                            </button>

                                        </div>
                                </div>
                            </div>

                            <br>

                        </div>

                        <div class="col-md-3">
                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-5">
                                        <label class="control-label">Total Productos</label>
                                    </div>
                                    <div class="col-md-7">
                                        <span id="totalproductos"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="row">
                                <div class="form-group">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row setup-content" id="step-3" style="display: none;">
            <div class="col-xs-12">
                <div class="col-md-12 well">
                    <div class="modal-body">
                        <h4>Terminar Venta</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="totApagar2" class="control-label">Total Pagar:</label>
                                </div>
                                <div class="col-md-9">
                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon"><?= MONEDA ?></span>
                                        <input type="number"
                                               class='input-square input-small form-control'
                                               min="0.0" step="0.1"
                                               value="0.0"
                                               id="totApagar2" readonly
                                               onkeydown="return soloDecimal(this, event);">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($venta[0]) && (!isset($preciosugerido) || $preciosugerido != 1)) { ?>
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

                        <div class="row" id="importediv" style="display: none;">
                            <div class="form-group">
                                <div class="col-md-3">
                                    <label for="importe" class="control-label">Importe:</label>
                                </div>
                                <div class="col-md-9">
                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon"><?= MONEDA ?></span>
                                        <input type="number" tabindex="0"
                                               class='input-square input-small form-control'
                                               min="0.0" step="0.1"
                                               value="0.00"
                                               name="importe" id="importe"
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
                                        onclick="javascript:hacerventa(0);">
                                    <li class="glyphicon glyphicon-thumbs-up"></li> Guardar
                                </button>
                                <button class="btn btn-warning closegenerarventa" type="button">
                                    <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div class="modal fade" id="seleccionunidades" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close closeseleccionunidades" aria-hidden="true">&times;</button>
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
                                            value="<?= $precio['id_precio']; ?>" <?php if ($precio['nombre_precio'] == 'Precio Venta') echo 'selected'; ?>><?= $precio['nombre_precio']; ?></option>
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
                            <div class="col-md-2">Precio Sugerido: <input type="checkbox" id="check_precio"></div>
                            <div class="col-md-3">
                                <input style="display:none;" type="number" id="precio_sugerido" class="form-control"
                                       value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div style="display:none;" class="form-group" id="precio_detalle">
                            <div class="col-md-2">Precio Minimo:</div>
                            <div class="col-md-3" id="min_precio">
                                0,00
                            </div>
                            <div class="col-md-2">Precio Maximo:</div>
                            <div class="col-md-3" id="max_precio">
                                0,00
                            </div>
                        </div>
                        </br>
                    </div>

                </div>
                <div class="modal-footer">
                    <a href="#" class="btn btn-primary" id="agregarproducto">Agregar Producto</a>
                    <a href="#" class="btn btn-warning closeseleccionunidades">Salir</a>
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
                    <li class="glyphicon glyphicon-thumbs-up"></li> Confirmar</button>
                    <button type="button" class="btn btn-warning" onclick="$('#confirmar_delete').modal('hide');">
                    <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar</button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>

    </div>

    <script type="text/javascript">
        function estado_oiginal() {

            $('#confirmacion2').modal('show')

        }

        function ocultar_confirmacion() {

            $('#confirmacion2').modal('hide');
        }


        function buscar_backup(id_venta) {

            $("#montoigv").val(0)
            $("#totApagar").val(0)
            document.getElementById('totApagar').value = 0;
            document.getElementById('totApagar2').value = 0;
            document.getElementById('montoigv').value = 0;
            document.getElementById('subTotal').value = 0;
            resetear()
            $("#accion_resetear").val('resetear')
            $.ajax({

                url: '<?=$ruta?>venta/venta_backup',
                type: 'post',
                data: {'idventa': id_venta},
                dataType: 'json',
                success: function (data) {

                    $('#confirmacion2').modal('hide')
                    $("#tbodyproductos").empty();
                    var venta = data.venta
                    countproducto = 0
                    var bono = countproducto;

                    for (var i = 0; i < venta.length; i++) {
                        var nombre = venta[i]['nombre'];

                        calculatotales(venta[i]['producto_id'], encodeURIComponent(nombre), venta[i]['nombre_unidad'], venta[i]['cantidad'], venta[i]['preciounitario'], venta[i]['importe'], venta[i]['porcentaje_impuesto'], countproducto, venta[i]['unidades'], venta[i]['producto_cualidad'], venta[i]['id_unidad'], venta[i]['precio_sugerido'], buscar_bono(venta[i]['bono']));
                        addProductoToArray(venta[i]['producto_id'], encodeURIComponent(nombre), venta[i]['id_unidad'], venta[i]['nombre_unidad'], venta[i]['cantidad'], venta[i]['preciounitario'], buscar_preciosugerido(venta[i]['precio_sugerido']), venta[i]['importe'], venta[i]['unidades'], venta[i]['producto_cualidad'], venta[i]['porcentaje_impuesto'], buscar_bono(venta[i]['bono']), venta[i]['venta_sin_stock']);

                    }


                }


            })

        }

        function buscar_bono(bono) {
            if (bono == 0) {
                return 'false'
            } else {
                return 'true'
            }
        }

        function buscar_preciosugerido(buscar_preciosugerido) {
            if (buscar_preciosugerido) {
                return buscar_preciosugerido
            } else {
                return 0
            }
        }

        function selectApi(endpoint, index, inner, id, value, add, data) {
            $.ajax({
                url: '<?= $ruta; ?>api/' + endpoint,
                type: 'GET',
                dataType: 'JSON',
                data: {'vendedor': data.vendedor},
                success: function (data) {
                    $(inner).html('');
                    $(inner).html('<option value="">Seleccione<option>');
                    if (index) {
                        data = data[index];
                    }
                    var addHtml = '';
                    for (var i = 0; i < data.length; i++) {
                        var selected = '';
                        if (data[i].selected == true || (add && data[i][add] == 0)) {

                            selected = ' selected';
                        }
                        var mostrar = data[i][value];
                        if (endpoint === "Productos") {

                            mostrar = pad_with_zeroes(data[i]['producto_id'], 4) + " - " + mostrar;
                        }

                        var option = '<option value="' + data[i][id] + '"' + selected + '>' + mostrar + '</option>';
                        if (add) {

                            addHtml += '<input type="hidden" id="diascondicionpago' + data[i][id] + '"  value="' + data[i][add] + '">';

                        }
                        $(inner).append(option);
                    }
                    $(inner).trigger("chosen:updated");
                    $('#credito_value').append(addHtml);


                    /*******Agregado por jhainey***/
                    if ($("#idventa").val() != '' && endpoint === "Clientes") {
                        var cutomer = '<?= isset($ven["cliente_id"]) ? $ven["cliente_id"] : ''?>';

                        $("#id_cliente").val(cutomer).trigger("chosen:updated");


                    }

                    if ($("#idventa").val() != '' && endpoint === "Pagos") {
                        var cboModPag = '<?= isset($venta[0]['id_condiciones']) ? $venta[0]['id_condiciones'] : ''?>';

                        $("#cboModPag").val(cboModPag).trigger("chosen:updated");
                        activarText_ModoPago();

                    }
                },
                error: function (xhr, textStatus, error) {
                    console.log('[' + endpoint + ' Error] ' + textStatus);
                }
            });
        }


        function zonaVendedor(){
            if($('#todasZonas').is(':checked')){
                var n = ''
            }else{
                var d = new Date();
                var n = d.getDay();

                if(n==0){
                    n = 7
                }
            }
            $('#zona option').remove();

            $.ajax({
                url: '<?=base_url()?>venta/zonaVendedor',
                 type: "post",
                dataType: "json",
                data: {'vendedor_id': $('#vendedor').val(), 'dia': n},
                success: function(data) {
                    if (data != '') {
                        if (data != '') {
                             for (i = 0; i < data.length; i++) {
                                 $('#zona').append('<option value=' + data[i].zona_id + '>' + data[i].zona_nombre + '</option>')
                             }
                        }
                        $("#zona").trigger('chosen:updated');
                    }
                }
            });
        }


        function clienteDireccion(){
            if($("#id_cliente").val()!=''){
                $('#direccion_entrega_np option').remove();
                $('#direccion_principal option').remove();
                $('#direccion_entrega_doc option').remove();

                $.ajax({
                    url: '<?=base_url()?>venta/clienteDireccion',
                     type: "post",
                    dataType: "json",
                    data: {'cliente_id': $('#id_cliente').val()},
                    success: function(data) {
                        if (data != '') {
                                 for (i = 0; i < data.length; i++) {
                                     $('#direccion_entrega_np').append('<option value=' + data[i].id + '>' + data[i].valor + '</option>')
                                     if(data[i].principal == 1){

                                        $('#direccion_principal').append('<option value=' + data[i].id + '>' + data[i].valor + '</option>')
                                     }

                                     $('#direccion_entrega_doc').append('<option value=' + data[i].id + '>' + data[i].valor + '</option>')
                                 }
                            $("#direccion_entrega_np").trigger('chosen:updated');
                            $("#direccion_principal").trigger('chosen:updated');
                            $("#direccion_entrega_doc").trigger('chosen:updated');
                        }
                    }
                });
            }
        }

    function representanteCliente(){
        if($("#id_cliente").val()!=''){

            $.ajax({
                url: '<?=base_url()?>venta/representanteCliente',
                 type: "post",
                dataType: "json",
                data: {'cliente_id': $('#id_cliente').val()},
                success: function(data) {
                    if (data != '') {
                        $('#contacto_nt').val(data[0].representante)
                    }
                }
            });
        }
    }



        $(document).ready(function () {



            tipoDoc()
            $('#tipo_documento').change(function(){
                tipoDoc()

            })

            $('#todasZonas').prop('checked', false)
            zonaVendedor()
            $('#todasZonas').click(function(){
                zonaVendedor()

            })


        $("#id_cliente").change(function(){
            clienteDireccion()
            representanteCliente()
        })

        $("#id_cliente").change(function () {
        $('#cliente_nt').val($('#id_cliente :selected').html())
        $('#contacto_nt').val($('#id_cliente :selected').html())

        $("#clienteinformativo").html($("#id_cliente option:selected").html());
        });

            function tipoDoc(){
                if($('#tipo_documento').val() != ''){
                    $('#content_opcion').show()
                    if($('#tipo_documento').val() == 'FACTURA'){
                        $('#div_documento').show()
                    }else{
                        $('#div_documento').hide()
                    }
                }else{
                    $('#content_opcion').hide()
                }
            }

            var data = {};
            data.vendedor = null;
            var useradmin = '<?=  $this->session->userdata("admin"); ?>';

            if (useradmin == 0) {
                data.vendedor = '<?php echo $this->session->userdata("nUsuCodigo"); ?>';
            }

            // console.log(data);
            // Clientes
            // selectApi('Clientes', 'clientes', '#id_cliente', 'id_cliente', 'nombre', null, data);

            // Pagos
            selectApi('Pagos', 'pagos', '#cboModPag', 'id_condiciones', 'nombre_condiciones', 'dias', data);

            // Productos
            selectApi('Productos', 'productos', '#selectproductos', 'producto_id', 'producto_nombre', null, data);

            // Step
            var navListItems = $('ul.setup-panel li a'),
                allWells = $('.setup-content');

            allWells.hide();

            navListItems.click(function (e) {
                e.preventDefault();
                var $target = $($(this).attr('href')),
                    $item = $(this).closest('li');

                if (!$item.hasClass('disabled')) {
                    navListItems.closest('li').removeClass('active');
                    $item.addClass('active');
                    allWells.hide();
                    $target.show();
                    $target.find('input:eq(0)').focus();
                }
            });

            $('ul.setup-panel li.active a').trigger('click');

            $('#activate-step-2').on('click', function (e) {
                $('ul.setup-panel li:eq(1)').removeClass('disabled');
                $('ul.setup-panel li a[href="#step-2"]').trigger('click');
                $(this).remove();
            });

            $('#activate-step-3').on('click', function (e) {
                $('ul.setup-panel li:eq(2)').removeClass('disabled');
                $('ul.setup-panel li a[href="#step-3"]').trigger('click');
                $(this).remove();
            })


        });
    </script>
</div>

<div class="modal fade" id="confirmacion2" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmaci&oacute;n</h4>
            </div>

            <div class="modal-body">Al cambiar el estado el pedido regresara a su estado original,
                ¿esta seguro que desea realizar esta acción?
            </div>
            <div class="modal-footer">
                <button type="button" id="buscar_backup" class="btn btn-primary"
                        onclick="buscar_backup(<?= $venta_id ?>)">Si
                </button>
                <button type="button" class="btn btn-warning" onclick="ocultar_confirmacion()">No</button>

            </div>
        </div>
        <!-- /.modal-content -->
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


<script>
    var countproducto = <?= $countproductos; ?>;
    var ruta = '<?php echo $ruta; ?>';
</script>

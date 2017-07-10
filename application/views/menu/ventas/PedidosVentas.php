<?php $ruta = base_url(); ?>
<?php $disabled = (isset($devolver) or isset($preciosugerido)) ? 'disabled' : ''; ?>
<script src="<?php echo $ruta; ?>recursos/js/generarventa.js"></script>
<div id="inentariocontainer" style="display: none;"></div>
<input type="hidden" id="producto_cualidad" value="">
<input type="hidden" id="devolver" value="<?php echo isset($devolver) ? 1 : 0; ?>">
<input type="hidden" id="coso_id" value="<?php echo isset($coso_id) ? $coso_id : 'false'; ?>">
<input type="hidden" id="preciosugerido" value="<?php echo isset($preciosugerido) ? 'true' : 'false'; ?>">
<input type="hidden" id="idlocal" value="<?= $this->session->userdata('id_local'); ?>">
<input type="hidden" id="pedidos_maximo" value="<?= valueOption('REFRESCAR_PEDIDOS', '20') ?>">
<style>
    .legend {
        width: auto;
        font-size: 18px;
        position: relative;
        top: -15px;
        background-color: #fff;
        padding-right: 5px;
        padding-left: 5px;
    }

    .legend-right {
        width: auto;
        font-size: 18px;
        position: relative;
        float: right;
        top: -15px;
        background-color: #fff;
        padding-right: 5px;
        padding-left: 5px;
    }

    .fieldset {
        border: 1px solid #ddd;
        border-radius: 4px;
        margin-bottom: 25px;
    }

    .setup-content {
        padding-left: 10px;
        padding-right: 10px;
    }

    .tr_head {
        background-color: #B1AEAE;
        color: white;
    }

    .tr_head th {
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        text-align: center;
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
        <input type="hidden" name="url_refresh" id="url_refresh"
               value="<?php echo isset($preciosugerido) ? '//consultar?buscar=pedidos' : '/pedidos'; ?>">
        <input type="hidden" name="venta_status" id="venta_status"
               value="<?php echo isset($devolver) ? PEDIDO_DEVUELTO : PEDIDO_GENERADO ?>">
        <input type="hidden" name="venta_tipo" id="venta_tipo"
               value="<?= VENTA_ENTREGA ?>">
        <input type="hidden" name="diascondicionpagoinput" id="diascondicionpagoinput"
               value="<?php if (isset($venta[0]['id_condiciones'])) echo $venta[0]['id_condiciones']; ?>">
        <input type="hidden" name="idventa" id="idventa"
               value="<?php if (isset($venta[0]['venta_id'])) echo $venta[0]['venta_id']; ?>">

        <input type="hidden" name="edit_pedido" id="edit_pedido"
               value="0">

        <input type="hidden" name="estatus_consolidado" id="estatus_consolidado"
               value="<?php if (isset($estatus_consolidado)) echo $estatus_consolidado; ?>">

        <input type="hidden" name="vendedor" id="vendedor"
               value="<?php echo $this->session->userdata("nUsuCodigo"); ?>">
        <input type="hidden" name="isadmin" id="isadmin"
               value="<?php echo $this->session->userdata("admin"); ?>">

        <div id="credito_value"></div>

        <div class="row form-group">
            <div class="col-md-12">
                <ul class="nav nav-pills nav-justified thumbnail setup-panel">
                    <li class="active"><a href="#step-1">
                            <h4 class="list-group-item-heading">Paso 1</h4>

                            <p class="list-group-item-text">Clientes y tipo de pagos</p>
                        </a></li>
                    <li class="disabled"><a href="#step-2">
                            <h4 class="list-group-item-heading">Paso 2</h4>

                            <p class="list-group-item-text">Seleccion y Envio de Productos</p>
                        </a></li>
                </ul>
            </div>
        </div>
        <div class="row" style="display: <?= count($vendedores) == 1 ? 'none' : 'display' ?>;">
            <div class="col-md-2"></div>
            <div class="col-md-2 text-right">
                <label for="zona" class="control-label panel-admin-text">Vendedor</label>
            </div>
            <div class="col-md-4">
                <select name="id_vendedor" id="id_vendedor" class='form-control' required="true">
                    <?php foreach ($vendedores as $vendedor): ?>
                        <option value="<?= $vendedor->nUsuCodigo ?>"><?= $vendedor->nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <br><br>
        </div>
        <div class="row setup-content" id="step-1">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12 fieldset">
                        <span class="legend">Datos Claves</span>
                        <span
                                class="legend-right"><?= get_day_week(date('N')) . ' - ' . date('d/m/Y') . ' ' . $this->session->userdata('nombre') ?></span>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="zona" class="control-label panel-admin-text">Zona</label>
                                    <select name="zona" id="zona" class='form-control' required="true">
                                        <option value="">Seleccione</option>
                                        <?php foreach ($zonas as $zona): ?>
                                            <option value="<?= $zona['zona_id'] ?>"><?= $zona['zona_nombre'] ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                    <input type="hidden" id="current_zona_id" value="">
                                    <div id=check_zonas style="display:block; margin-top: 5px;">
                                        <input type="checkbox" name="todasZona" id="todasZonas">
                                        <label for="todasZonas" style="cursor: pointer;">Todas las zonas</label>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label panel-admin-text">Tipo de Pago</label>
                                    <select name="condicion_pago" id="cboModPag" onchange="activarText_ModoPago()"
                                            class="form-control" <?= $disabled; ?>></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="control-label panel-admin-text">Documento</label>
                                    <select name="tipo_documento" id="tipo_documento"
                                            class="form-control" <?= $disabled; ?>>
                                        <option value="<?= BOLETAVENTA ?>" selected><?= BOLETAVENTA ?>
                                        </option>
                                        <option value="<?= FACTURA ?>"><?= FACTURA ?>
                                        </option>
                                    </select>

                                </div>
                            </div>
                            <div class="col-md-2 text-right">
                                <label class="control-label panel-admin-text"></label><br>
                                <button id="activate-step-2" class="btn btn-primary">Continuar</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div id='content_opcion' style="display: none" class="col-md-12 fieldset">
                        <span class="legend">Cliente</span>
                        <div class="row">
                            <div class="col-md-4">
                                <label for="zona" class="control-label panel-admin-text">Selecciona el Cliente</label>
                                <select name="id_cliente" id="id_cliente" class='form-control'
                                        required="true">
                                    <option value="">Seleccione</option>
                                    <?php foreach ($clientes as $cl): ?>
                                        <option
                                                data-iden="<?= $cl['identificacion'] ?>"
                                                value="<?= $cl['id_cliente']; ?>"><?= $cl['razon_social'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                                <input type="hidden" id="current_cliente_id" value="">
                            </div>

                            <div class="col-md-3">
                                <label for="zona" class="control-label panel-admin-text">Grupo de Cliente</label>
                                <input type="text" id="grupo_cliente_1" data-id="" value="" name="grupo_cliente_1"
                                       readonly
                                       class="form-control">
                            </div>

                            <div class="col-md-3">
                                <label for="zona" class="control-label panel-admin-text">Retenci&oacute;n</label>
                                <input type="number" id="retencion" value="0" name="retencion" readonly="readonly"
                                       class="form-control">

                                <div id='cont_retencion' style="margin-top: 5px;">
                                    <input id="cambiar_retencion" type="checkbox" name="cambiar_retencion">
                                    <label for="cambiar_retencion" class="control-label panel-admin-text"
                                           style="cursor: pointer;"> Cambiar
                                        retenci&oacute;n</label>
                                </div>
                            </div>

                            <div class="form-group col-md-2">
                                <label for="deuda_actual" class="control-label panel-admin-text">Deuda Actual</label>
                                <div class="input-group">
                                    <div
                                            class="input-group-addon"><?= MONEDA ?></div>
                                    <input type="number" id="deuda_actual"
                                           name="deuda_actual" readonly="readonly"
                                           class="form-control" value="0.00">
                                </div>
                            </div>
                        </div>


                    </div>
                </div>
                <div class="row">
                    <div id='div_nota_pedido' class="col-md-12 fieldset">
                        <span class="legend">Datos de Entrega</span>
                        <div class="row div_documento" style="display: none;">
                            <div class="col-md-4">
                                <label
                                        for="direccion_entrega_np"
                                        class="control-label panel-admin-text">
                                    Direccion Principal</label>
                            </div>
                            <div class="col-md-8">
                                <input type="text" name="direccion_principal" readonly id="direccion_principal"
                                       class='form-control'>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-4">
                                <label
                                        for="direccion_entrega_np"
                                        class="control-label panel-admin-text">
                                    Direccion Entrega</label>
                            </div>
                            <div class="col-md-8">
                                <select name="direccion_entrega_np" id="direccion_entrega_np" class='form-control'
                                        required="true" style="">
                                    <option value="">Seleccione</option>
                                </select>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div style="padding: 5px 15px; float: left; width: 48%;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="razon_social" class="control-label panel-admin-text">Razon
                                            Social</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="razon_social" class="form-control" id="razon_social"
                                               readonly>
                                    </div>
                                </div>

                                <div class="row div_documento" style="display: none;">
                                    <br>
                                    <div class="col-md-4">
                                        <label for="contacto_nt" class="control-label panel-admin-text">Gerente</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="gerente_nombre" class="form-control"
                                               id="gerente_nombre"
                                               readonly>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="contacto_nt"
                                               class="control-label panel-admin-text">Representante</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="representante" class="form-control" id="representante"
                                               readonly>
                                    </div>
                                </div>
                            </div>

                            <div style="padding: 5px 15px; float: right; width: 48%;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="contacto_nt" class="control-label panel-admin-text">RUC /
                                            DNI</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="ruc_dc" class="form-control" id="ruc_dc" readonly>
                                    </div>
                                </div>

                                <div class="row div_documento" style="display: none;">
                                    <br>
                                    <div class="col-md-4">
                                        <label for="contacto_nt" class="control-label panel-admin-text">Gerente
                                            DNI</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="gerente_dni" class="form-control" id="gerente_dni"
                                               readonly>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-4">
                                        <label for="contacto_nt" class="control-label panel-admin-text">Representante
                                            DNI</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="representante_dni" class="form-control"
                                               id="representante_dni"
                                               readonly>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <br>
                    </div>
                </div>
            </div>
        </div>


        <div class="row setup-content" id="step-2">
            <div class="col-xs-12">
                <div class="col-md-12 well">
                    <div class="row panel">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label for="cboTipDoc" class="control-label panel-admin-text">Cliente:</label>
                            </div>
                            <div class="col-md-4">
                                <input type="text" id="clienteinformativo" name="clienteinformativo" readonly="readonly"
                                       class="form-control">
                            </div>
                            <div class="col-md-2">
                                <label for="gruclie" class="control-label panel-admin-text">Grupo del Cliente:</label>
                            </div>
                            <div class="col-md-2">
                                <input type="text" id="gruclie" name="grupo_cliente" readonly="readonly"
                                       class="form-control">
                            </div>

                            <div class="col-md-2 text-right"
                                 style="font-size: 20px; color: #55c862; font-weight: bold;">
                                <?= MONEDA ?> <span id="totApagar2">0.00</span>
                            </div>
                        </div>
                    </div>

                    <div class="row panel">
                        <div class="form-group">
                            <div class="col-md-2">
                                <label for="cboTipDoc" class="control-label panel-admin-text">Buscar
                                    Producto:</label>
                            </div>
                            <div class="col-md-5">
                                <select class="form-control" style="width: 100%" id="selectproductos"
                                        onchange="buscarProducto()"></select>
                            </div>
                            <div class="col-md-2">
                                <div class="btn-group">
                                    <button type="button" id="refrescarstock" class="btn btn-default">
                                        <i class="fa fa-refresh"></i> Refrescar
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-3 text-right">
                                <button class="btn btn-primary" type="button" id="realizarventa"
                                        onclick="javascript:hacerventa(0);">
                                    <li class="glyphicon glyphicon-thumbs-up"></li>
                                    Enviar Pedido
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="row ">

                        <div class=" col-md-9 block">
                            <div id="" class="table-responsive" style="height: 400px; overflow-y: auto;">
                                <table class="table dataTable dataTables_filter table-bordered">
                                    <thead>
                                    <tr class="tr_head">
                                        <th>#</th>
                                        <th>ID</th>
                                        <th>Nombre</th>
                                        <th>UM</th>
                                        <th>Cant.</th>
                                        <th>Precio</th>
                                        <th>Sugerido</th>
                                        <th>Subtotal</th>
                                        <th colspan="2"></th>

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
                                        <label for="cboTipDoc" class="control-label panel-admin-text">Documento:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" readonly id="tipoDocumento"
                                               name="tipoDocumento" style="text-align: right;">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="cboTipDoc" class="control-label panel-admin-text">Fecha:</label>
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" class="form-control" readonly id="fecha" name="fecha"
                                               style="text-align: right;"
                                               value="<?= isset($venta[0]['fechaemision']) ? $venta[0]['fechaemision'] : date('d/m/Y'); ?>">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group">
                                    <div class="col-md-4">
                                        <label for="subTotal"
                                               class="control-label panel-admin-text">SubTotal:</label>
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
                                        <label for="montoigv"
                                               class="control-label panel-admin-text">Impuesto:</label>
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
                                            <input
                                                    style="font-size: 14px; font-weight: bolder; background: #FFEB9C;"
                                                    type="text"
                                                    class='input-square input-small form-control'
                                                    name="totApagar"
                                                    id="totApagar"
                                                    readonly value="0.00">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row" style="display: none;">
                                <br>
                                <br>
                                <div class="form-group">
                                    <div class="col-md-8">
                                        <label class="control-label panel-admin-text">Total Productos</label>
                                    </div>
                                    <div class="col-md-4">
                                        <span id="totalproductos"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 text-right">
                                    <br>
                                    <br>
                                    <button class="btn btn-warning closegenerarventa" type="button">
                                        <li class="glyphicon glyphicon-thumbs-down"></li>
                                        Reiniciar Venta
                                    </button>
                                </div>
                            </div>
                            <br>

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
                                <div class="col-md-2">
                                    <label for="totApagar3" class="control-label panel-admin-text">Total
                                        Pagar:</label>
                                </div>
                                <div class="col-md-6">
                                    <div class="input-prepend input-append input-group">
                                        <span class="input-group-addon"><?= MONEDA ?></span>
                                        <input type="number"
                                               class='input-square input-small form-control'
                                               min="0.0" step="0.1"
                                               value="0.0"
                                               id="totApagar3" readonly
                                               onkeydown="return soloDecimal(this, event);">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php if (isset($venta[0]) && (!isset($preciosugerido) || $preciosugerido != 1)) { ?>
                            <div class="row" id="pagadodiv" style="display: none;">
                                <div class="form-group">
                                    <div class="col-md-2">
                                        <label for="importe" class="control-label panel-admin-text">A
                                            cuenta:</label>
                                    </div>
                                    <div class="col-md-6">
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
                                <div class="col-md-2">
                                    <label for="importe" class="control-label panel-admin-text">Importe:</label>
                                </div>
                                <div class="col-md-6">
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
                                    <li class="glyphicon glyphicon-thumbs-up"></li>
                                    Guardar
                                </button>
                                <button class="btn btn-warning closegenerarventa" type="button">
                                    <li class="glyphicon glyphicon-thumbs-down"></li>
                                    Cancelar
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
                            <div class="col-md-1" style="display: none">Precio:</div>
                            <div class="col-md-5" style="display: none">
                                <select class="form-control" name="precio" id="precios" tabindex="0"
                                        onchange="cambiarnombreprecio()" style="width:250px">
                                    <?php foreach ($precios as $precio) { ?>
                                        <option
                                                value="<?= $precio['id_precio']; ?>" <?php if ($precio['nombre_precio'] == 'Precio Venta') echo 'selected'; ?>><?= $precio['nombre_precio']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-1" align="rigth"><h4>Stock:</h4></div>
                            <div class="col-md-4">
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
                            <div class="col-md-2"><h4>Cantidad:</h4></div>
                            <div class="col-md-2">
                                <input readonly id="cantidad" class="form-control"
                                       Onkeypress='return justNumbers(event);'>
                            </div>
                            <div class="col-md-3">
                                <h4>Precio Sugerido:</h4>
                                <input type="checkbox" id="check_precio">
                            </div>
                            <div class="col-md-2">
                                <input style="display:none;" type="number" id="precio_sugerido" class="form-control"
                                       value="0">
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-top:10px;">
                        <div style="display:none;" class="form-group" id="precio_detalle">
                            <div class="col-md-2">Precio Minimo:</div>
                            <div class="col-md-3" id="min_precio">
                            </div>
                            <div class="col-md-2">Precio Maximo:</div>
                            <div class="col-md-3" id="max_precio">

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
                        <li class="glyphicon glyphicon-thumbs-up"></li>
                        Confirmar
                    </button>
                    <button type="button" class="btn btn-warning" onclick="$('#confirmar_delete').modal('hide');">
                        <li class="glyphicon glyphicon-thumbs-down"></li>
                        Cancelar
                    </button>
                </div>
            </div>
            <!-- /.modal-content -->
        </div>

    </div>

    <script type="text/javascript">

        var clientes = [];
        var zonas = [];
        var vendedores = [];
        var grupo_id = "";
        var grupo_name = "";

        function justNumbers(e) {
            var keynum = window.event ? window.event.keyCode : e.which;
            if ((keynum == 8) || (keynum == 46))
                return true;
            return /\d/.test(String.fromCharCode(keynum));
        }

        <?php foreach ($clientes as $clie): ?>
        clientes.push({
            'id_cliente': '<?=$clie['id_cliente']?>',
            'grupo_id': '<?=$clie['grupo_id']?>',
            'grupo_name': '<?=$clie['nombre_grupos_cliente']?>',
            'vendedor_id': '<?=$clie['vendedor_a']?>',
            'zona_id': '<?=$clie['id_zona']?>',
            'identificacion': '<?=$clie['identificacion']?>'
        });
        <?php endforeach; ?>

        <?php foreach ($zonas as $zona): ?>
        zonas.push({
            'zona_id': '<?=$zona['zona_id'] ?>',
            'zona_nombre': '<?=$zona['zona_nombre']?>',
        });
        <?php endforeach; ?>

        <?php foreach ($vendedores as $vendedor): ?>
        vendedores.push({
            'vendedor_id': '<?=$vendedor->nUsuCodigo ?>',
            'vendedor_nombre': '<?=$vendedor->nombre?>',
        });
        <?php endforeach; ?>


        function get_gruclie() {

            var sele = $("#id_cliente option:selected").val();

            var gn = $('#gruclie').val();

            $('#gruclie').val('');
            $('#grupo_cliente_1').val('');

            var cid = $("#current_cliente_id").val();

            for (var i = 0; i < clientes.length; i++) {
                if (clientes[i].id_cliente == sele) {
                    if ($("#tbodyproductos tr").size() > 0) {
                        if (clientes[i].grupo_id != $("#grupo_cliente_1").attr('data-id')) {
                            show_msg('warning', '<h4>Error.</h4> <p>Este Cliente pertenece a otro Grupo, para cambiarlo reinicie el pedido o borre sus respectivos productos.</p>');
                            $("#id_cliente").val(cid).change().trigger("chosen:updated");
                            $('#gruclie').val(gn);
                            $('#grupo_cliente_1').val(gn);
                        }
                        else {
                            grupo_id = clientes[i].grupo_id;
                            grupo_name = clientes[i].grupo_name;
                            $('#gruclie').val(grupo_name);
                            $('#grupo_cliente_1').val(grupo_name);
                            $('#grupo_cliente_1').attr('data-id', grupo_id);
                        }
                    }
                    else {
                        grupo_id = clientes[i].grupo_id;
                        grupo_name = clientes[i].grupo_name;
                        $('#gruclie').val(grupo_name);
                        $('#grupo_cliente_1').val(grupo_name);
                        $('#grupo_cliente_1').attr('data-id', grupo_id);
                    }
                    break;
                }
            }
        }


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
            $('#totApagar2').html(0);
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
                        get_gruclie();
                        activarText_ModoPago();

                    }
                },
                error: function (xhr, textStatus, error) {
                    console.log('[' + endpoint + ' Error] ' + textStatus);
                }
            });
        }


        function zonaVendedor() {
            if ($('#todasZonas').is(':checked')) {
                var n = ''
            } else {
                var d = new Date();
                var n = d.getDay();

                if (n == 0) {
                    n = 7
                }
            }


            $.ajax({
                url: '<?=base_url()?>venta/zonaVendedor',
                type: "post",
                dataType: "json",
                data: {'vendedor_id': $('#id_vendedor').val(), 'dia': n},
                success: function (data) {
                    if (data != '') {
                        $('#zona').html('<option value="">Seleccione</option>')
                        var arrayRand = [];

                        for (i = 0; i < data.length; i++) {
                            arrayRand.push(data[i].zona_id);
                            $('#zona').append('<option value=' + data[i].zona_id + '>' + data[i].zona_nombre + '</option>')
                        }

                        // Selecci´on aleatorea de valores
                        obtenerClientesZona('');
                        $('#zona').val('');
                        $("#zona").trigger('chosen:updated');

                    } else {
                        obtenerClientes();
                    }
                    // zonaclientes()

                }
            });
        }


        function clienteDireccion() {

            $('#direccion_entrega_np').html('<option value="">Seleccione</option>');
            $('#direccion_principal').html('<option value="">Seleccione</option>');

            if ($("#id_cliente").val() != '') {

                //$('#direccion_entrega_doc option').remove();

                $.ajax({
                    url: '<?=base_url()?>venta/clienteDireccion',
                    type: "post",
                    dataType: "json",
                    data: {'cliente_id': $('#id_cliente').val()},
                    success: function (data) {
                        if (data != '') {
                            for (var i = 0; i < data.length; i++) {
                                if (data[i].principal == 1) {
                                    $('#direccion_entrega_np').append('<option selected value=' + data[i].id + '>' + data[i].valor + '</option>');
                                    $('#direccion_principal').val(data[i].valor);
                                }
                                else
                                    $('#direccion_entrega_np').append('<option value=' + data[i].id + '>' + data[i].valor + '</option>');

                            }
                            $("#direccion_entrega_np").trigger('chosen:updated');
                        }
                    }
                });
            }

            $("#direccion_entrega_np").trigger('chosen:updated');
        }

        function dataCliente() {

            $('#clienteinformativo').val('');

            $('#ruc_dc').val('');
            $('#razon_social').val('');

            $('#gerente_dni').val('');
            $('#gerente_nombre').val('');

            $('#representante_dni').val('');
            $('#representante').val('');

            $('#retencion').val(0);
            if ($("#id_cliente").val() != '') {
                $.ajax({
                    url: '<?=base_url()?>venta/dataCliente',
                    type: "post",
                    dataType: "json",
                    data: {'cliente_id': $('#id_cliente').val()},
                    success: function (data) {
                        if (data != '') {
                            $('#clienteinformativo').val(data.razon_social);

                            $('#ruc_dc').val(data.ruc_cliente);
                            $('#razon_social').val(data.razon_social);

                            $('#gerente_dni').val(data.gerente_dni);
                            $('#gerente_nombre').val(data.representante);

                            $('#representante_dni').val(data.contacto_dni);
                            $('#representante').val(data.contacto_nombre);

                            $('#retencion').val(data.linea_credito_valor);

                        }
                    }
                });
            }
        }

        function getDeudaCliente() {
            $('#deuda_actual').val(formatPrice(0));
            if ($("#id_cliente").val() != '') {
                $.ajax({
                    url: '<?=base_url()?>venta/dataClienteDeuda',
                    type: "post",
                    dataType: "json",
                    data: {'cliente_id': $('#id_cliente').val()},
                    success: function (data) {
                        if (data != '') {
                            $('#deuda_actual').val(formatPrice(data.deuda));
                        }
                    }
                });
            }
        }

        $("#id_vendedor").on('change', function () {

            if ($("#tbodyproductos tr").size() > 0) {
                show_msg('warning', '<h4>Error.</h4> <p>Ya tienes productos agregados a determinado cliente.</p>');
                $('#zona').val(zid).trigger("chosen:updated");
                return false;
            }

            zonaVendedor();
        });

        // Evento de Zonas
        $('#zona').change(function () {
            var zid = $("#current_zona_id").val();
            if ($("#tbodyproductos tr").size() > 0) {
                show_msg('warning', '<h4>Error.</h4> <p>Ya tienes productos agregados a determinado cliente.</p>');
                $('#zona').val(zid).trigger("chosen:updated");
                return false;
            }
            // zonaclientes()
            obtenerClientesZona($(this).val());
            resetCampos('id_cliente');

            $("#current_zona_id").val($('#zona').val());
        });


        function resetCampos(campo_id) {
            if ($('#' + campo_id).is('select')) {
                $('#' + campo_id + ' option').remove();
                $('#' + campo_id).append('<option value="">Seleccione</option>');
                $('#' + campo_id).trigger('chosen:updated');
            }
        }

        function obtenerClientesZona(zona_id) {
// Metodo Ajax
            if (zona_id != '') {
                $.ajax({
                    url: '<?=base_url()?>venta/clientesIdZona' + '/' + $("#id_vendedor").val(),
                    type: "post",
                    dataType: "json",
                    data: {'zona_id': zona_id},
                    success: function (data) {
                        if (data != '') {
                            $('#id_cliente option').remove();
                            $('#id_cliente').append('<option value="">Seleccione</option>');
                            for (var i = 0; i < data.length; i++) {
                                $('#id_cliente').append('<option data-iden="' + data[i].identificacion + '" value=' + data[i].id_cliente + '>' + data[i].razon_social + '</option>')
                            }
                            $("#id_cliente").trigger('change');
                            $("#id_cliente").trigger('chosen:updated');
                        }
                    }
                });
            } else {
                obtenerClientes();
            }
        }

        function obtenerClientes() {
            $("#grupo_cliente_1").val('');
            $("#clienteinformativo").val('');
            $("#gruclie").val('');
            $("#grupo_cliente_1").attr('data-id', '');
// Metodo Ajax
            $.ajax({
                url: '<?=base_url()?>venta/listaClientes' + '/' + $("#id_vendedor").val(),
                type: "post",
                dataType: "json",
                success: function (data) {
                    if (data != '') {
                        $('#id_cliente option').remove();
                        $('#id_cliente').append('<option value="">Seleccione</option>');
                        clientes = [];
                        for (var i = 0; i < data.length; i++) {

                            clientes.push({
                                'id_cliente': data[i].id_cliente,
                                'grupo_id': data[i].grupo_id,
                                'grupo_name': data[i].nombre_grupos_cliente,
                                'vendedor_id': data[i].vendedor_a,
                                'zona_id': data[i].id_zona,
                            });


                            $('#id_cliente').append('<option data-iden="' + data[i].identificacion + '" value=' + data[i].id_cliente + '>' + data[i].razon_social + '</option>')
                        }
                    }
                    else {
                        $('#id_cliente').html('<option value="">Seleccione</option>');
                    }
                    $("#id_cliente").trigger('chosen:updated');
                }
            });
        }

        function getElementOptionRand(id_input) {
            var arrayRand = [];
            $(id_input).children().each(function (index, value) {
                console.log(value);
                arrayRand.push($(value).val())
            });
            return arrayRand[Math.floor(Math.random() * arrayRand.length)];
        }

        function contruirSelect(data, element_id) {
            for (i = 0; i < data.length; i++) {
                $('#' + element_id).append('<option value=' + data[i].zona_id + '>' + data[i].zona_nombre + '</option>')
            }
            $("#" + element_id).trigger('chosen:updated');
        }
        //////////////////////////

        $(document).ready(function () {

            $("#tipoDocumento").val($("#tipo_documento").val());

            tipoDoc();

            $('#cont_retencion').click(function () {
                if ($('#cambiar_retencion').is(':checked')) {
                    $('#retencion').prop("readonly", false)

                } else {
                    $('#retencion').prop("readonly", true)
                }
            })


            $('#tipo_documento').change(function () {
                tipoDoc();
                $("#tipoDocumento").val($("#tipo_documento").val());
            });

            // Evento Zonas N
            $('#todasZonas').prop('checked', false);


            // console.log(data);
            // var zonaRand = getElementOptionRand("#zona");
            // $("#zona").trigger('chosen:updated');

            // Evento Click Checkbox
            $('#todasZonas').click(function () {

                if ($('#todasZonas').is(':checked')) {
                    obtenerClientes();
                } else {
                    $("#zona").val('');
                    $("#zona").change().trigger('chosen:updated');
                }
                zonaVendedor();
            });

            $("#direccion_entrega_np").change(function () {
                if ($('#tipo_documento').val() == 'FACTURA') {
                    $('#direccion_entrega_doc').val($("#direccion_entrega_np").val())
                } else {
                    $('#direccion_entrega_doc').val('')
                }
                $("#direccion_entrega_doc").trigger('chosen:updated');
            })

            $("#direccion_entrega_doc").change(function () {
                $('#direccion_entrega_np').val($("#direccion_entrega_doc").val())
                $("#direccion_entrega_np").trigger('chosen:updated');

            })


            $("#id_cliente").change(function () {
                    var cid = $("#current_cliente_id").val();
                    if ($("#id_cliente").val() == '' && $("#tbodyproductos tr").size() > 0) {
                        show_msg('warning', '<h4>Error.</h4> <p>Ya tienes productos agregados a determinado cliente.</p>');
                        $("#id_cliente").val(cid).trigger("chosen:updated");
                        return false;
                    }

                    get_gruclie();
                    $("#current_cliente_id").val($("#id_cliente").val());
                    clienteDireccion();
                    dataCliente();
                    getDeudaCliente();
                }
            );

            function tipoDoc() {
                $('#content_opcion').show()
                if ($('#tipo_documento').val() == 'FACTURA') {
                    $('.div_documento').show()
                } else {
                    $('.div_documento').hide()
                }
            }

            var data = {};
            data.vendedor = $("#id_vendedor").val();
            var useradmin = '<?=  $this->session->userdata("admin"); ?>';

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
                            <li class="glyphicon glyphicon-thumbs-up"></li>
                            Guardar
                        </button>
                        <button class="btn btn-warning closemodificarcantidad" type="button">
                            <li class="glyphicon glyphicon-thumbs-down"></li>
                            Cancelar
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

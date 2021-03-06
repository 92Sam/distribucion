<?php $ruta = base_url(); ?>
<script src="<?php echo $ruta; ?>recursos/js/generarventa.js"></script>
<div id="inentariocontainer" style="display: none;"></div>
<input type="hidden" id="producto_cualidad" value="">
<input type="hidden" id="idlocal" value="<?= $this->session->userdata('id_local'); ?>">
<input type="hidden" id="pedidos_maximo" value="<?= valueOption('REFRESCAR_PEDIDOS', '20') ?>">
<style>
    .tr_head {
        background-color: #B1AEAE;
        color: white;
    }

    .tr_head th {
        padding-top: 0px !important;
        padding-bottom: 0px !important;
        text-align: center;
    }

    #tbodyproductos td {
        font-size: 10px;
    }
</style>
<script>
    var countproducto = 0;
</script>

<!-- END Datatables Header -->

<!-- Progress Bars Wizard Title -->

<form method="post" id="frmVenta" action="#" class="">
    <input type="hidden" name="url_refresh" id="url_refresh"
           value="<?php echo isset($preciosugerido) ? '//consultar?buscar=pedidos' : '/pedidos'; ?>">
    <input type="hidden" name="venta_status" id="venta_status"
           value="<?php echo $devolver == 1 ? PEDIDO_DEVUELTO : PEDIDO_GENERADO ?>">
    <input type="hidden" name="venta_tipo" id="venta_tipo"
           value="<?= VENTA_ENTREGA ?>">
    <input type="hidden" name="diascondicionpagoinput" id="diascondicionpagoinput"
           value="<?php if (isset($venta[0]['id_condiciones'])) echo $venta[0]['id_condiciones']; ?>">
    <input type="hidden" name="idventa" id="idventa"
           value="<?= $venta_id ?>">

    <input type="hidden" name="edit_pedido" id="edit_pedido"
           value="1">

    <input type="hidden" name="devolver_pedido" id="devolver_pedido"
           value="<?= $devolver ?>">

    <input type="hidden" name="estatus_consolidado" id="estatus_consolidado"
           value="<?php if (isset($estatus_consolidado)) echo $estatus_consolidado; ?>">

    <input type="hidden" name="vendedor" id="vendedor"
           value="<?php echo $this->session->userdata("nUsuCodigo"); ?>">
    <input type="hidden" name="isadmin" id="isadmin"
           value="<?php echo $this->session->userdata("admin"); ?>">

    <input type="hidden" name="importe" id="importe"
           value="0">

    <input type="hidden" name="pagado" id="pagado"
           value="0">
    <input type="hidden" name="id_cliente" id="id_cliente"
           value="<?= $cliente->cliente_id ?>">
    <input type="hidden" id="grupo_cliente_1" data-id="<?= $cliente->grupo_id ?>" value="<?= $cliente->grupo_nombre ?>"
           name="grupo_cliente_1"
           class="form-control">


    <div class="row">
        <div class="col-md-12">
            <div class="col-md-12 well">
                <div class="row panel">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="cboTipDoc" class="control-label panel-admin-text">Cliente:</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" id="clienteinformativo" name="clienteinformativo"
                                   class="form-control" value="<?= $cliente->cliente_nombre ?>" readonly>
                        </div>
                        <div class="col-md-2">
                            <label for="gruclie" class="control-label panel-admin-text">Grupo del Cliente:</label>
                        </div>
                        <div class="col-md-2">
                            <input type="text" id="gruclie" name="grupo_cliente" readonly="readonly"
                                   class="form-control" value="<?= $cliente->grupo_nombre ?>">
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
                                    onchange="buscarProductoEditar()"></select>
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
                                    onclick="javascript:hacerventa(0, '<?= $devolver == 1 ? 2 : 1 ?>');">
                                <li class="glyphicon glyphicon-thumbs-up"></li>
                                Editar Pedido
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row ">

                    <div class=" col-md-9">
                        <div id="" class="table-responsive" style="height: 325px; overflow-y: auto;">
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
                                        add_cantidad_temp(<?php echo $ven['producto_id']; ?>, <?php echo $ven['cantidad']; ?>);
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
                                    <label for="tipoDocumento"
                                           class="control-label panel-admin-text">Documento:</label>
                                </div>
                                <div class="col-md-8">
                                    <div class="input-prepend input-append input-group">
                                        <input type="text" class='form-control'
                                               name="tipoDocumento"
                                               id="tipoDocumento" readonly
                                               value="<?= $venta[0]['tipo_doc_fiscal']  ?>">
                                    </div>
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
                                           value="<?= isset($venta[0]['fechaemision']) ? date('d/m/Y', strtotime($venta[0]['fechaemision'])) : date('d/m/Y'); ?>">
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

                        <div class="row">
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
                                <button class="btn btn-warning" onclick="$('#ventamodal').modal('hide');" type="button">
                                    <li class="glyphicon glyphicon-thumbs-down"></li>
                                    Cerrar Venta
                                </button>
                            </div>
                        </div>
                        <br>

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
    var grupo_id = '<?=$cliente->grupo_id?>';
    var grupo_name = '<?=$cliente->grupo_nombre?>';

    <?php foreach ($clientes as $clie): ?>

    clientes.push({
        'id_cliente': '<?=$clie['id_cliente']?>',
        'grupo_id': '<?=$clie['grupo_id']?>',
        'grupo_name': '<?=$clie['nombre_grupos_cliente']?>',
    });

    <?php endforeach; ?>


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
            data: {'vendedor_id': $('#vendedor').val(), 'dia': n},
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
                    resetCampos('zona');
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
                                $('#direccion_principal').append('<option selected value=' + data[i].id + '>' + data[i].valor + '</option>');
                            }
                            else
                                $('#direccion_entrega_np').append('<option value=' + data[i].id + '>' + data[i].valor + '</option>');

                            //$('#direccion_entrega_doc').append('<option value=' + data[i].id + '>' + data[i].valor + '</option>')
                        }
                        $("#direccion_entrega_np").trigger('chosen:updated');
                        $("#direccion_principal").trigger('chosen:updated');
                        //$("#direccion_entrega_doc").trigger('chosen:updated');
                    }
                }
            });
        }

        $("#direccion_entrega_np").trigger('chosen:updated');
        $("#direccion_principal").trigger('chosen:updated');
    }

    function dataCliente() {
        $('#contacto_nt').val('');
        $('#retencion').val(0);

        $('#clienteinformativo').val('');

        $('#ruc_dc').val('');
        $('#dni_nt').val('');
        $('#razon_social').val('');

        if ($("#id_cliente").val() != '') {
            $.ajax({
                url: '<?=base_url()?>venta/dataCliente',
                type: "post",
                dataType: "json",
                data: {'cliente_id': $('#id_cliente').val()},
                success: function (data) {
                    if (data != '') {
                        $('#clienteinformativo').val(data[0].razon_social);

                        $('#contacto_nt').val(data[0].representante);
                        $('#retencion').val(data[0].linea_credito_valor);
                        $('#dni_nt').val(data[0].identificacion);

                        $('#ruc_dc').val(data[0].ruc_cliente)
                        $('#razon_social').val(data[0].razon_social)
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
                url: '<?=base_url()?>venta/clientesIdZona',
                type: "post",
                dataType: "json",
                data: {'zona_id': zona_id},
                success: function (data) {
                    if (data != '') {
                        $('#id_cliente option').remove();
                        $('#id_cliente').append('<option value="">Seleccione</option>');
                        for (i = 0; i < data.length; i++) {
                            $('#id_cliente').append('<option value=' + data[i].id_cliente + '>' + data[i].representante + '</option>')
                        }
                        $("#id_cliente").trigger('change');
                        $("#id_cliente").trigger('chosen:updated');
                    }
                }
            });
        } else {
            resetCampos('id_cliente');
        }
    }

    function obtenerClientes() {
// Metodo Ajax
        $.ajax({
            url: '<?=base_url()?>venta/listaClientes',
            type: "post",
            dataType: "json",
            success: function (data) {
                if (data != '') {
                    $('#id_cliente option').remove();
                    $('#id_cliente').append('<option value="">Seleccione</option>');
                    for (i = 0; i < data.length; i++) {
                        $('#id_cliente').append('<option value=' + data[i].id_cliente + '>' + data[i].representante + '</option>')
                    }
                    $("#id_cliente").trigger('chosen:updated');
                }
            }
        });
    }

    function getElementOptionRand(id_input) {
        alert(id_input)
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
                $('#id_cliente option').remove();
                $("#id_cliente").trigger('chosen:updated');
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


        function tipoDoc() {
            if ($('#tipo_documento').val() != '') {
                $('#content_opcion').show()
                if ($('#tipo_documento').val() == 'FACTURA') {
                    $('#div_documento').show()
                } else {
                    $('#div_documento').hide()
                }
            } else {
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

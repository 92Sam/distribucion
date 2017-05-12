<style>
    #tabla_resumen_productos thead tr {

    }

    #tabla_resumen_productos thead tr th {

    }

    #tabla_resumen_productos tbody tr td {

        font-size: 11px;
    }

    .nota_entrega_seccion {
        font-size: 12px;
        width: size: 22.50cm;
        margin: auto;
        border-color: #000;
        border-style: dashed;
        margin-bottom: 10px;
    }

    #tabla_resumen_productos thead tr th {
        font-size: 85%;
    }

    #msjF {
        display: block;
        text-align: center;
        height: 200px;
        width: 100%;
    }

</style>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" onclick="$('#noteDeEntrega').modal('hide')" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Vista Previa</h4>
        </div>
        <div class="modal-body" id="notaiprimir">
            <!------AQUI COMIENZA CADA NOTA DE ENTREGA ---->
            <?php
            if (empty($facturas)){
                ?>
                <div id="msjF"><h3>No hay pedidos con facturas en este consolidado</h3></div> <?php
            }else{

            foreach ($facturas as $factura) {

            foreach ($factura['ventas'] as $venta) { ?>
            <div class="panel row nota_entrega_seccion">

                <div id="resumen_venta">
                <div>
                    <div class="block-content-mini-padding">
                        <div class="col-xs-12">
                            Fecha: <span><?= date('Y-m-d') ?></span>
                        </div>
                    </div>
                    <div class="block-content-mini-padding">
                        <div class="col-xs-5">
                            SE&Ntilde;OR(ES):
                        </div>
                        <div class="col-xs-5">
                            R.U.C.: <?= $venta['descripcion'] ?>
                        </div>
                        <div class="col-xs-5">
                            DIRECCION:<span> <?= $venta['direccion_cliente']; ?></span>
                        </div>
                    </div>

                    <div class="col-xs-12 table-responsive" style="width: 100%">
                        <table id="tabla_resumen_productos" class="table">
                            <thead>
                            <tr>
                                <th>N. PEDIDO</th>
                                <th>TIPO DE CLIENTE</th>
                                <th>DISTRITO</th>
                                <th>FECHA DE EMISION</th>
                                <th>VENDEDOR</th>
                                <th>COD. CLIENTE</th>
                                <th>COND. DE VENTA</th>
                            </tr>
                            </thead>
                            <tbody id="detalle_contenido_producto">
                            <tr>
                                <td> <?= $venta['serie'] . "-" . $venta['numero']; ?></td>
                                <td></td>
                                <td></td>
                                <td> <?= date('Y-m-d', strtotime($venta['fechaemision'])); ?></td>
                                <td></td>
                                <td><?= $venta['vendedor']; ?></td>
                                <td><?= $venta['cliente']; ?></td>
                            </tr>
                            </tbody>
                        </table>


                        <!-- info row -->
                        <!-- /.col -->
                    </div>
                    <!-- /.row -->
                    <!-- Table row -->
                    <div>
                        <div class="col-xs-12 table-responsive" style="width: 100%">
                            <table id="tabla_resumen_productos" class="table">
                                <thead>
                                <tr>
                                    <th style=" width: 20%">CODIGO</th>
                                    <th style=" width: 40%">DESCRIPCCI&Oacute;N</th>
                                    <th style=" width: 20%">UNID. MEDIDA</th>
                                    <th style=" width: 20%">CANTIDAD</th>
                                    <th style=" width: 20%">PRECIO</th>
                                    <th style=" width: 20%">DSCTO.</th>
                                    <th style="width: 20%">IMPORTE TOTAL</th>
                                </tr>
                                </thead>
                                <tbody id="detalle_contenido_producto">
                                <?php
                                foreach ($factura['productos']  as $producto) {
                                    if ($venta['documento_id'] == $producto['documento_id']) {
                                        $um = isset($producto['abreviatura']) ? $producto['abreviatura'] : $producto['nombre_unidad'];
                                        $cantidad_entero = intval($producto['cantidad'] / 1) > 0 ? intval($producto['cantidad'] / 1) : '';
                                        $cantidad_decimal = fmod($producto['cantidad'], 1);

                                        $cantidad = $cantidad_entero;

                                        if ($cantidad_decimal > 0) {
                                            if (!empty($cantidad_entero)) {
                                                $cantidad = $cantidad_entero . "." . $cantidad_decimal;
                                            } else
                                                $cantidad = $cantidad_decimal;

                                            if ($cantidad_decimal == 0.25 or $cantidad_decimal == 0.250)
                                                $cantidad = $cantidad_entero . " " . '1/4';
                                            if ($cantidad_decimal == 0.5 or $cantidad_decimal == 0.50 or $cantidad_decimal == 0.500)
                                                $cantidad = $cantidad_entero . " " . '1/2';
                                            if ($cantidad_decimal == 0.75 or $cantidad_decimal == 0.750)
                                                $cantidad = $cantidad_entero . " " . '3/4';
                                        }

                                        if ($producto['producto_cualidad'] == 'MEDIBLE') {
                                            if ($producto['unidades'] == 12 || $producto['orden'] == 1) {
                                                $cantidad = floatval($producto['cantidad']);
                                            } else {
                                                $cantidad = floatval($producto['cantidad'] * $producto['unidades']);
                                                $um = $producto['unidad_minima'];
                                            }
                                        }
                                        ?>
                                        <TR>
                                            <td><?php echo $producto['ddproductoID']; ?></td>
                                            <td><? echo strtoupper($producto['nombre']);

                                                echo ($producto['importe'] == 0) ? ' --- BONIFICACION' : '' ?></td>
                                            <td><?php echo $um; ?></td>
                                            <td><?php echo $cantidad; ?></td>

                                            <td><?= $producto['precioV']; ?></td>
                                            <td></td>
                                            <td><?php echo ceil($producto['importe'] * 10) / 10; ?></td>
                                        </TR>
                                    <?php } ?>
                                <?php } ?>
                                <tr>
                                    <td></td>
                                    <td width="50%"> COPIA SIN DERECHO A CREDITO FISCAL DEL I.G.V.</td>
                                </tr>
                                <tr>
                                    <td colspan="6">OBS.</td>
                                    <td colspan="6"></td>
                                </tr>
                                <tr>
                                    <td colspan="4">SON: <?=  numtoletras($venta['montoTotal'] * 10 / 10); ?></td>
                                    <td colspan="2"> Sub-Total:</td>
                                    <td> <?php echo MONEDA ?>  <?= ceil($venta['subTotal'] * 10) / 10; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                    <td colspan="2">I.V.G....%</td>
                                    <td><?php echo MONEDA ?>  <?= ceil($venta['impuesto'] * 10) / 10; ?></td>
                                </tr>
                                <tr>
                                    <td colspan="4"></td>
                                    <td colspan="2">TOTAL</td>
                                    <td> <?php echo MONEDA ?> <span
                                            id="totalR"><?= ceil($venta['montoTotal'] * 10) / 10; ?></span></td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <br>
                    <!-- END TABLA DE PRODUCTOS -->
                    <div>


                    </div>
                </div>
            </div>
            </br>


        </div>
        <?php
        }
        }
        }
        ?>
        <div class="modal-footer">

            <a href="#" class="btn btn-default" onclick="$('#noteDeEntrega').modal('hide')">Cerrar</a>
            <a href="#" tabindex="0" type="button" id="imprimir" class="btn btn-primary"> <i
                    class="fa fa-print"></i>
                Imprimir</a>
        </div>
    </div>

</div>


<script src="<?php echo base_url(); ?>recursos/js/printThis.js"></script>
    <?php $ruta = base_url();

    ?>
<script>

    function imprimirBoletaFactura(id,tipo){
       // $("#mvisualizarVenta").modal('hide');
        var win = window.open('<?= $ruta ?>venta/rtfFactura/' + id+ '/' + tipo);
        win.focus();

    }

    $(function () {
        var id_venta='<?php echo $id_venta; ?>';
        var consolidado_id = '<?= isset($consolidado_id)?$consolidado_id:"" ?>';
        console.log(consolidado_id);
        $("#imprimir").click(function () {

            if (consolidado_id == '') {
                var id = id_venta;
                var tipo = 'VENTA';
            } else {
                var id = consolidado_id
                var tipo = 'CONSOLIDADO';
            }

            imprimirBoletaFactura(id, tipo);


        });
        setTimeout(function () {
            $("#imprimir").focus();
        }, 500);
    });
</script>
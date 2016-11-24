<style type=text/css>
    @page{
        /*  size: A4 portrait;*/
        margin-top: 0.3cm;
        margin-left:0.5cm;
        margin-bottom: 1.27cm;
        margin-right: 0.3cm;
        size: 29.7cm 21cm;
    }

    body, p, div {
        font-size: 15px;
        font-family: "Courier New";
        line-height: normal;
    }

    h1, h2, h3 {
        font-family: "Courier New";
        font-weight: 100;
    }

    h1 {
        font-weight: bold;
    }

    th {
        color: #000;
        font-weight: 600;
        font-size: 12pt;
        border-bottom: 1px solid white;
        border-top: 1px solid white;
        text-transform: uppercase;
    }

    td {
        font-weight: 600;
        color: #000;
        text-transform: uppercase;
        background-color: #fff;
        font-size: 12pt;
    }

    b {
        font-size: 29px;
    }
</style>


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
                                                    <th style=" width: 20%">CANTIDAD</th>
                                                    <th style=" width: 20%">MEDIDA</th>
                                                    <th style=" width: 20%">PRECIO</th>
                                                    <th style=" width: 20%">DSCTO.</th>
                                                    <th style="width: 20%">IMPORTE</th>
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
                                                            <td><?php echo $cantidad; ?></td>
                                                            <td><?php echo $um; ?></td>
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
                                                    <td colspan="4">SON:</td>
                                                    <td colspan="2"> Sub-Total:</td>
                                                    <td></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="4"></td>
                                                    <td colspan="2">I.V.G....%</td>
                                                    <td></td>
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






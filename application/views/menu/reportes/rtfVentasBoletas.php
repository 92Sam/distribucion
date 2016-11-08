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


<?php
            if (empty($boletas)){
                ?>
                <div id="msjB"><h2>No posee Boleta</h2></div><?php
            }else{
                foreach ($boletas as $boleta) {

                    foreach ($boleta['ventas'] as $venta) { ?>
                        <div class="panel row nota_entrega_seccion" ><br/>

                            <div id="resumen_venta">
                                <div>
                                    <div class="block-content-mini-padding">
                                        <div class="col-xs-12">
                                            Cliente:<span> <?= $venta['cliente']; ?>
                                        </div>
                                    </div>
                                    <div class="block-content-mini-padding">
                                        <div class="col-xs-8">
                                            Direcci&oacute;n:<span> <?= $venta['direccion_cliente']; ?></span>
                                        </div>
                                        <div class="col-xs-5">
                                            Entregar en:
                                        </div>
                                    </div>

                                    <div class="col-xs-12 table-responsive" style="width: 100%">
                                        <table id="tabla_resumen_productos" class="table">
                                            <thead>
                                            <tr>
                                                <th>N. Pedido</th>
                                                <th>Tipo de Cliente</th>
                                                <th>Cond. de Venta</th>
                                                <th>Distrito</th>
                                                <th>Fecha Emision</th>
                                                <th>Vendedor</th>
                                                <th>Codigo Cliente</th>
                                            </tr>
                                            </thead>
                                            <tbody id="detalle_contenido_producto">
                                            <tr>
                                                <td> <?= $venta['serie'] . "-" . $venta['numero']; ?></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td> <?= date('Y-m-d', strtotime($venta['fechaemision'])); ?></td>
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
                                                    <th style=" width: 10%">Codigo</th>
                                                    <th style=" width: 50%">Descripci&oacute;n</th>
                                                    <th style=" width: 10%">Unid. Med.</th>
                                                    <th style=" width: 10%">Cantidad</th>
                                                    <th style=" width: 12%">Val. Vent. Unit.</th>
                                                    <th style=" width: 20%" colspan="2">Importe Total</th>


                                                </tr>
                                                </thead>
                                                <tbody id="detalle_contenido_producto">
                                                <?php
                                                foreach ($boleta['productos']  as $producto) {
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
                                                            <td align="center"><?php echo ceil($producto['importe'] * 10) / 10; ?></td>
                                                        </TR>
                                                    <?php } ?>
                                                <?php } ?>
                                                <tr>
                                                    <td colspan="5"></td>
                                                    <td colspan="2"><span style="font-size: 80%;">TOTAL A PAGAR</span></td>
                                                </tr>
                                                <tr>
                                                    <td colspan="5">SON:</td>
                                                    <td align="center"><?php echo MONEDA ?> <span
                                                            id="totalR"><?= ceil($venta['montoTotal'] * 10) / 10; ?></span></td>
                                                    <td></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <table class="table">
                                                <tr>
                                                    <td align="center" COLSPAN="5">Transportista</td>
                                                    <td rowspan="3" style="font-size:0.8em; ">
                                                        *TODOS LOS PAGOS QUE REALICE EL CLIENTE DEBEN EFECTUARSE A NUESTRO COBRADOR
                                                        AUTORIZADOR, EXIJA SU IDENTIFICACION<br/>
                                                        *ACENPTANDO ESTE COMPROBANTE, Y EN CASO NO LO CANCELE, AUTORIZO EXPRESANTE A
                                                        DIVULGAR ESTA INFORMACION A ----- U OTRA CENTRAL DE RIRSGO<br/>
                                                        *EL CLIENTE DECLARARA HABER RECIBIDO LA MERCADERIA EN CANTIDAD Y PESO
                                                        CORRECTO ASI COMO EN BUEN ESTADO DE CONSERVVACION<br/><br/><br/>
                                            <span
                                                style="border-top:solid 1px black; padding-left:80px; padding-right:80px;  ">Nombre</span>
                                            <span
                                                style="border-top:solid 1px black; padding-left:80px; padding-right:80px;  ">Doc. D.N.I.</span>
                                                        <br>
                                                        <br>
                                                        <br>
                                            <span
                                                style="border-top:solid 1px black; padding-left:80px; padding-right:80px;  ">Firma</span>
                                                    </td>
                                                </tr>

                                                <tr>
                                                    <td>R.U.C.:</td>
                                                    <td> . . .</td>
                                                    <td>Placa:</td>
                                                    <td><?= $venta['placa']; ?></td>
                                                </tr>
                                                <tr>
                                                    <td>Chofer:</td>
                                                    <td><?= $venta['vendedor']; ?></td>
                                                    <td>Codigo:</td>
                                                    <td> . . .</td>
                                                </tr>

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
                    <?php }
                }
            } ?>



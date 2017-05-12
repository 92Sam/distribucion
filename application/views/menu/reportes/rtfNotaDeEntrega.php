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
<?php $ruta = base_url(); ?>

            <!------AQUI COMIENZA CADA NOTA DE ENTREGA ---->
            <?php if (isset($notasdentrega[0])) {
                $i=0;
                foreach ($notasdentrega as $nota) {

                    ?>


                    <div class="container">
                        <?php if (isset($nota['ventas'][0])) {

                            $ventas[0] = $nota['ventas'][0];

                            ?>
                            <div class="col-md-12" class="resumen_venta">

                                <div class="row">
                                    <div class="col-md-12"><br></div>

                                    <div class="col-xs-8">
                                        <h4 class="text-center">
                                            NOTA DE ENTREGA

                                        </h4>
                                    </div>
                                    <div class="col-xs-4" id="notaEntrega">
                                        <h5 class="text-center">
                                            NOTA DE ENTREGA N&deg;
                                            <?php if (isset($ventas[0]['serie']) AND isset($ventas[0]['numero'])) echo $ventas[0]['serie'] . $ventas[0]['numero'];; ?>

                                        </h5>
                                    </div>
                                    <div class="col-md-12"><br></div>

                                    <div class="col-xs-6">CLIENTE: <span><?= strtoupper($ventas[0]['cliente']) ?></span>
                                    </div>
                                    <div class="col-xs-2" style="text-align: right;">COD:CLIE.:
                                        <span><?= $ventas[0]['cliente_id']; ?></span>
                                    </div>
                                    <div class="col-xs-2">F. EMISION:
                                        <span><?= date('Y-m-d', strtotime($ventas[0]['fechaemision'])) ?></span>
                                    </div>
                                    <div class="col-xs-2">USUA: <span><?= strtoupper($ventas[0]['vendedor']) ?></span></div>


                                    <div class="col-xs-8">DIRECION:
                                        <span><?php if (isset($ventas[0]['clienteDireccion'])) echo strtoupper($ventas[0]['clienteDireccion']); ?></span>
                                    </div>
                                    <?php
                                    if (isset($detalleC[0])) {
                                        ?>
                                        <div class="col-xs-2"> F. VENC.:
                                            <span><?= date('Y-m-d', strtotime($detalleC[0]['fecha'])) ?></span>
                                        </div>

                                        <div class="col-xs-2"> HORA:
                                            <span><?= date('H:m:s', strtotime($detalleC[0]['fecha'])) ?></span>
                                        </div>
                                    <?php } ?>
                                    <div class="col-xs-4">CONTACTO:
                                        <span><?php if (isset($ventas[0]['representanteCliente'])) echo strtoupper($ventas[0]['representanteCliente']); ?></span>
                                    </div>
                                    <div class="col-xs-4">TELEFONO:
                                        <span><?php if (isset($ventas[0]['telefonoC1'])) echo $ventas[0]['telefonoC1']; ?></span>
                                    </div>
                                    <div class="col-xs-2">
                                        COND. VENTA:
                                        <span> <?= strtoupper($ventas[0]['nombre_condiciones'])  ?></span>
                                    </div>
                                    <div class="col-xs-2">VEND.:
                                        <span><?php if (isset($ventas[0]['id_vendedor'])) echo $ventas[0]['id_vendedor']; ?></span>
                                    </div>



                                    <div class="col-md-12 table-responsive" style="width: 98%">
                                        <table id="tabla_resumen_productos" class="table">
                                            <thead>
                                            <tr>

                                                <th style="border-bottom: 1px #000 solid; width: 5%"> CODIGO
                                                </th>

                                                <th style="border-bottom: 1px #000 solid; width: 40%">DESCRIPCION
                                                </th>

                                                <th style="border-bottom: 1px #000 solid; width: 20%">PRESENTACION
                                                </th>

                                                <th style="border-bottom: 1px #000 solid; width: 20%">
                                                    CANTIDAD
                                                </th>

                                                <th style="border-bottom: 1px #000 solid; width: 60%">PREC.
                                                    UNIT.
                                                </th>


                                                <th style="border-bottom: 1px #000 solid; width: 20%">TOTAL</th>


                                            </tr>
                                            </thead>
                                            <tbody id="detalle_contenido_producto">
                                            <?php
                                            foreach ($nota['ventas'] as $venta) {
                                                $um = isset($venta['abreviatura']) ? $venta['abreviatura'] : $venta['nombre_unidad'];
                                                $cantidad_entero = intval($venta['cantidad'] / 1) > 0 ? intval($venta['cantidad'] / 1) : '';
                                                $cantidad_decimal = fmod($venta['cantidad'], 1);

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


                                                if ($venta['producto_cualidad'] == 'MEDIBLE') {

                                                    if ($venta['unidades'] == 12 or $venta['orden'] == 1) {
                                                        $cantidad = floatval($venta['cantidad']);

                                                    } else {
                                                        $cantidad = floatval($venta['cantidad'] * $venta['unidades']);
                                                        $um = $venta['unidad_minima'];
                                                    }
                                                }
                                                ?>
                                                <TR>
                                                    <td><?php echo $venta['producto_id'] ?></td>

                                                    <td><?echo strtoupper($venta['nombre']); echo ($venta['bono']==1)?' --- BONIFICACION':'' ?></td>

                                                    <td><?= strtoupper($venta['presentacion']) ?></td>

                                                    <td><?php echo $cantidad . " " . $um ?></td>

                                                    <td><?php echo $venta['preciounitario']; ?></td>

                                                    <td><?php echo $venta['importe'] ; ?></td>
                                                </TR>
                                            <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-12">
                                        <table class="table" id="totales_" >


                                            <tr style="border-bottom:0px #000 dashed">
                                                <td>
                                                    <strong>SON:</strong><br/><br/>
                                                </td>
                                                <td style="border-top: 0px #000 dashed">
                                                    <?php echo MONEDA ?> <span
                                                        id="totalR"><?=  numtoletras($ventas[0]['montoTotal'] * 10 / 10); ?></span>
                                                </td>
                                            </tr>

                                        </table>
                                    </div>
                                    <div class="col-md-12" id="footer">
                                        <div class="col-xs-5">
                                            <h6>*CANJEAR POR BOLETA O FACTURA <br>*GRACIAS POR SU COMPRA. VUELVA
                                                PRONTO</h6>
                                        </div>
                                        <div class="col-xs-3" style="border-top:solid black 1px;">
                                            <h6>RECIBO CONFORME</h6>
                                        </div>
                                        <div class="col-xs-3" style="text-align: right;">
                                            <h6>Total:  <?php echo MONEDA ?>
                                                <span id="totalR"><?= ceil($ventas[0]['montoTotal'] * 10) / 10 ?></span>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        <?php
                        } ?>
                    </div>
                    <?php $i++; }
            } ?>

            <!------AQUI FINALIZA  CADA NOTA DE ENTREGA ---->




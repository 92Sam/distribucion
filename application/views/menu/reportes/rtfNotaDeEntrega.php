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


<?php
function numtoletras($xcifra)
{
    $xarray = array(0 => "Cero",
        1 => "UN", "DOS", "TRES", "CUATRO", "CINCO", "SEIS", "SIETE", "OCHO", "NUEVE",
        "DIEZ", "ONCE", "DOCE", "TRECE", "CATORCE", "QUINCE", "DIECISEIS", "DIECISIETE", "DIECIOCHO", "DIECINUEVE",
        "VEINTI", 30 => "TREINTA", 40 => "CUARENTA", 50 => "CINCUENTA", 60 => "SESENTA", 70 => "SETENTA", 80 => "OCHENTA", 90 => "NOVENTA",
        100 => "CIENTO", 200 => "DOSCIENTOS", 300 => "TRESCIENTOS", 400 => "CUATROCIENTOS", 500 => "QUINIENTOS", 600 => "SEISCIENTOS", 700 => "SETECIENTOS", 800 => "OCHOCIENTOS", 900 => "NOVECIENTOS"
    );
//
    $xcifra = trim($xcifra);
    $xlength = strlen($xcifra);
    $xpos_punto = strpos($xcifra, ".");
    $xaux_int = $xcifra;
    $xdecimales = "00";
    if (!($xpos_punto === false)) {
        if ($xpos_punto == 0) {
            $xcifra = "0" . $xcifra;
            $xpos_punto = strpos($xcifra, ".");
        }
        $xaux_int = substr($xcifra, 0, $xpos_punto); // obtengo el entero de la cifra a covertir
        $xdecimales = substr($xcifra . "00", $xpos_punto + 1, 2); // obtengo los valores decimales
    }

    $XAUX = str_pad($xaux_int, 18, " ", STR_PAD_LEFT); // ajusto la longitud de la cifra, para que sea divisible por centenas de miles (grupos de 6)
    $xcadena = "";
    for ($xz = 0; $xz < 3; $xz++) {
        $xaux = substr($XAUX, $xz * 6, 6);
        $xi = 0;
        $xlimite = 6; // inicializo el contador de centenas xi y establezco el l�mite a 6 d�gitos en la parte entera
        $xexit = true; // bandera para controlar el ciclo del While
        while ($xexit) {
            if ($xi == $xlimite) { // si ya lleg� al l�mite m�ximo de enteros
                break; // termina el ciclo
            }

            $x3digitos = ($xlimite - $xi) * -1; // comienzo con los tres primeros digitos de la cifra, comenzando por la izquierda
            $xaux = substr($xaux, $x3digitos, abs($x3digitos)); // obtengo la centena (los tres d�gitos)
            for ($xy = 1; $xy < 4; $xy++) { // ciclo para revisar centenas, decenas y unidades, en ese orden
                switch ($xy) {
                    case 1: // checa las centenas
                        if (substr($xaux, 0, 3) < 100) { // si el grupo de tres d�gitos es menor a una centena ( < 99) no hace nada y pasa a revisar las decenas

                        } else {
                            $key = (int) substr($xaux, 0, 3);
                            if (TRUE === array_key_exists($key, $xarray)){  // busco si la centena es n�mero redondo (100, 200, 300, 400, etc..)
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux); // devuelve el subfijo correspondiente (Mill�n, Millones, Mil o nada)
                                if (substr($xaux, 0, 3) == 100)
                                    $xcadena = " " . $xcadena . " CIEN " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3; // la centena fue redonda, entonces termino el ciclo del for y ya no reviso decenas ni unidades
                            }
                            else { // entra aqu� si la centena no fue numero redondo (101, 253, 120, 980, etc.)
                                $key = (int) substr($xaux, 0, 1) * 100;
                                $xseek = $xarray[$key]; // toma el primer caracter de la centena y lo multiplica por cien y lo busca en el arreglo (para que busque 100,200,300, etc)
                                $xcadena = " " . $xcadena . " " . $xseek;
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 0, 3) < 100)
                        break;
                    case 2: // checa las decenas (con la misma l�gica que las centenas)
                        if (substr($xaux, 1, 2) < 10) {

                        } else {
                            $key = (int) substr($xaux, 1, 2);
                            if (TRUE === array_key_exists($key, $xarray)) {
                                $xseek = $xarray[$key];
                                $xsub = subfijo($xaux);
                                if (substr($xaux, 1, 2) == 20)
                                    $xcadena = " " . $xcadena . " VEINTE " . $xsub;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                                $xy = 3;
                            }
                            else {
                                $key = (int) substr($xaux, 1, 1) * 10;
                                $xseek = $xarray[$key];
                                if (20 == substr($xaux, 1, 1) * 10)
                                    $xcadena = " " . $xcadena . " " . $xseek;
                                else
                                    $xcadena = " " . $xcadena . " " . $xseek . " Y ";
                            } // ENDIF ($xseek)
                        } // ENDIF (substr($xaux, 1, 2) < 10)
                        break;
                    case 3: // checa las unidades
                        if (substr($xaux, 2, 1) < 1) { // si la unidad es cero, ya no hace nada

                        } else {
                            $key = (int) substr($xaux, 2, 1);
                            $xseek = $xarray[$key]; // obtengo directamente el valor de la unidad (del uno al nueve)
                            $xsub = subfijo($xaux);
                            $xcadena = " " . $xcadena . " " . $xseek . " " . $xsub;
                        } // ENDIF (substr($xaux, 2, 1) < 1)
                        break;
                } // END SWITCH
            } // END FOR
            $xi = $xi + 3;
        } // ENDDO

        if (substr(trim($xcadena), -5, 5) == "ILLON") // si la cadena obtenida termina en MILLON o BILLON, entonces le agrega al final la conjuncion DE
            $xcadena.= " DE";

        if (substr(trim($xcadena), -7, 7) == "ILLONES") // si la cadena obtenida en MILLONES o BILLONES, entoncea le agrega al final la conjuncion DE
            $xcadena.= " DE";

        // ----------- esta l�nea la puedes cambiar de acuerdo a tus necesidades o a tu pa�s -------
        if (trim($xaux) != "") {
            switch ($xz) {
                case 0:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena.= "UN BILLON ";
                    else
                        $xcadena.= " BILLONES ";
                    break;
                case 1:
                    if (trim(substr($XAUX, $xz * 6, 6)) == "1")
                        $xcadena.= "UN MILLON ";
                    else
                        $xcadena.= " MILLONES ";
                    break;
                case 2:
                    if ($xcifra < 1) {
                        $xcadena = "CERO SOLES $xdecimales/100 ";
                    }
                    if ($xcifra >= 1 && $xcifra < 2) {
                        $xcadena = "UN SOLES $xdecimales/100  ";
                    }
                    if ($xcifra >= 2) {
                        $xcadena.= " SOLES $xdecimales/100  "; //
                    }
                    break;
            } // endswitch ($xz)
        } // ENDIF (trim($xaux) != "")
        // ------------------      en este caso, para M�xico se usa esta leyenda     ----------------
        $xcadena = str_replace("VEINTI ", "VEINTI", $xcadena); // quito el espacio para el VEINTI, para que quede: VEINTICUATRO, VEINTIUN, VEINTIDOS, etc
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("UN UN", "UN", $xcadena); // quito la duplicidad
        $xcadena = str_replace("  ", " ", $xcadena); // quito espacios dobles
        $xcadena = str_replace("BILLON DE MILLONES", "BILLON DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("BILLONES DE MILLONES", "BILLONES DE", $xcadena); // corrigo la leyenda
        $xcadena = str_replace("DE UN", "UN", $xcadena); // corrigo la leyenda
    } // ENDFOR ($xz)
    return trim($xcadena);
}

// END FUNCTION

function subfijo($xx)
{ // esta funci�n regresa un subfijo para la cifra
    $xx = trim($xx);
    $xstrlen = strlen($xx);
    if ($xstrlen == 1 || $xstrlen == 2 || $xstrlen == 3)
        $xsub = "";
    //
    if ($xstrlen == 4 || $xstrlen == 5 || $xstrlen == 6)
        $xsub = "MIL";
    //
    return $xsub;
}

// END FUNCTION
?>

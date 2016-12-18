<?php $ruta = base_url();

?>
<style>
    #tabla_resumen_productos thead tr {

    }

    #tabla_resumen_productos thead tr th {

    }

    #tabla_resumen_productos tbody tr td {

        font-size: 12px;
    }

    .nota_entrega_seccion {
        font-size: 11px;
        width: size: 22.50cm;
        margin: auto;
        border-color: #000;
        border-style: dashed;
        margin-bottom: 10px;

    }

    #tabla_resumen_productos thead tr th {
        font-size: 11px;
    }

    #msjB {
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
            <?php
            if (empty($boletas)){
                ?>
                <div id="msjB"><h2>No posee Boleta</h2></div><?php
            }else{

            foreach ($boletas as $boleta) {


            foreach ($boleta['ventas'] as $venta) {

                $totalboleta=0;
                ?>
                
                
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
                                    //var_dump($venta['productos']);

                                    foreach($venta['productos'] as $producto) {

                                        $totalboleta=$totalboleta+ $producto['importe'];
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
                                                <td><?= $producto['preciounitario']; ?></td>
                                                <td align="center"><?php echo ceil($producto['importe'] * 10) / 10; ?></td>
                                            </TR>
                                        <?php } ?>
                                    <?php } ?>
                                    <tr>
                                        <td colspan="5"></td>
                                        <td colspan="2"><span style="font-size: 80%;">TOTAL A PAGAR</span></td>
                                    </tr>
                                    <tr>
                                        <td colspan="5">SON: <?=  numtoletras($totalboleta * 10 / 10); ?></td>
                                        <td align="center"><?php echo MONEDA ?> <span
                                                id="totalR"><?= ceil($totalboleta * 10) / 10; ?></span></td>
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
        </div>
        <div class="modal-footer">

            <a href="#" class="btn btn-default" onclick="$('#noteDeEntrega').modal('hide')">Cerrar</a>
            <a href="#" tabindex="0" type="button" id="imprimir" class="btn btn-primary"> <i class="fa fa-print"></i>Imprimir</a>
        </div>
    </div>
</div>

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

?>
<script src="<?php echo base_url(); ?>recursos/js/printThis.js"></script>
<script>


    function imprimirBoletaFactura(id,tipo){

        //  $("#mvisualizarVenta").modal('hide');
        var win = window.open('<?= $ruta ?>venta/rtfBoleta/' + id+ '/' + tipo);
        win.focus();

    };


    $(function () {
        var id_venta='<?php echo $id_venta; ?>';
        var consolidado_id = '<?= isset($consolidado_id)?$consolidado_id:"" ?>';

        $("#imprimir").click(function () {
            if (consolidado_id == '') {
                var id = id_venta;
                var tipo = 'VENTA';
            } else {
                var id = consolidado_id
                var tipo = 'CONSOLIDADO';
            }

            imprimirBoletaFactura(id,tipo);

        });

        setTimeout(function () {
            $("#imprimir").focus();
        }, 500);
    });
</script>
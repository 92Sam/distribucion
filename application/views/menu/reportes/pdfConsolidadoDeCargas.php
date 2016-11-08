
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
        font-size: 10pt;
        border-bottom: 1px solid white;
        border-top: 1px solid white;
        text-transform: uppercase;
    }

    td {
        font-weight: 600;
        color: #000;
        text-transform: uppercase;
        background-color: #fff;
        font-size: 10pt;
    }

    b {
        font-size: 29px;
    }
</style>
<div class="container">

    <center><h1 class="text-center"><?= strtoupper($this->session->userdata('EMPRESA_NOMBRE')) ?></h1></center>

    <?php
    foreach ($consolidado as $campo) { ?>
    <p><?= date('d/m/Y', strtotime($campo['fecha'])) ?> </p>

    <h2>Consolidado de Guia de Carga</h2>

    <p>Liquidacion: <?= $campo['consolidado_id'] ?>
        <span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Responsable : <?= $campoC['userCarga'] ?></p>

    <p>Almacen:<?= $campoC['local_nombre'] ?></p>
<?php
} ?>
    <br/>
    <table>
        <tr>
            <th width="15%">LINEA</th>
            <th width="10%">CODIGO</th>
            <th width="35%">PRODUCTO</th>
            <th width="35%">UNIDAD</th>

            <th width="15%">MEDIDA</th>
            <th width="10%">CANT</th>
        </tr>

        <?php
        $pdid = $detalleProducto[0]['id_grupo'];
        $gruponombre = $detalleProducto[0]['nombre_grupo'];
        $cantidadtotalgrupo = 0;
        $count = 1;
        ?>

        <?php foreach ($detalleProducto as $campoProducto) {

            if ($campoProducto['id_grupo'] != $pdid) {


                if (empty($campoProducto['produto_grupo'])) {
                    ?>
                    <tr>

                        <td></td>
                        <td></td>
                        <td></td>


                        <td colspan="2">TOTAL SIN GRUPO</td>
                        <td style="border-top: 1px"> <?= number_format($cantidadtotalgrupo, 2) ?></td>

                    </tr>
                    <?php
                } else {

                    ?>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>


                        <td colspan="2">TOTAL <?= strtoupper($gruponombre) ?></td>
                        <td style="border-top: 1px"> <?= number_format($cantidadtotalgrupo, 2) ?></td>

                    </tr>
                    <?php

                }

                $pdid = $campoProducto['id_grupo'];
                $gruponombre = $campoProducto['nombre_grupo'];

                $cantidadtotalgrupo = 0;
            }

            $cantidadtotalgrupo = $cantidadtotalgrupo + $campoProducto['cantidadTotal'];
            if (empty($campoProducto['produto_grupo'])) { ?>
                <tr>
                <td>SIN GRUPO</td>
            <?php } else { ?>
                <tr>
                <td> <?= strtoupper($campoProducto['nombre_grupo']) ?></td>
            <?php } ?>
            <td> <?= strtoupper($campoProducto['producto_id']) ?></td>
            <td> <?= strtoupper($campoProducto['producto_nombre']) ?></td>
            <td> <?= strtoupper($campoProducto['nombre_unidad']) ?></td>

            <td> <?= strtoupper($campoProducto['presentacion']) ?></td>
            <td> <?= $campoProducto['cantidadTotal'] ?></td>
            </tr>



            <?php

            if ($count === sizeof($detalleProducto)) {


                if (empty($campoProducto['produto_grupo'])) {
                    ?>
                    <tr>

                        <td></td>
                        <td></td>
                        <td></td>


                        <td colspan="2">TOTAL SIN GRUPO</td>
                        <td style="border-top: 1px"> <?= number_format($cantidadtotalgrupo, 2) ?></td>

                    </tr>
                    <?php
                } else {

                    ?>
                    <tr>
                        <td></td>
                        <td></td>


                        <td></td>
                        <td colspan="2">TOTAL <?= strtoupper($campoProducto['nombre_grupo']) ?></td>
                        <td style="border-top: 1px"> <?= number_format($cantidadtotalgrupo, 2) ?></td>

                    </tr>
                    <?php

                }

                $pdid = $campoProducto['id_grupo'];
                $gruponombre = $campoProducto['nombre_grupo'];

                $cantidadtotalgrupo = 0;
            }
            $count++;


        }


        ?>
    </table>
</div>
<?php $ruta = base_url(); ?>
<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
    }
</style>

<h2 style="text-align: center;">Consulta de Compras</h2>
<table border="0">
    <tr>
        <td style="border: 0px; width: 80% !important;"></td>
        <td style="border: 0px; text-align: right;">Subtotal: <?= MONEDA ?> <?= number_format($ingreso_totales->subtotal, 2) ?></td>
        <td style="border: 0px; text-align: right;">IGV: <?= MONEDA ?> <?= number_format($ingreso_totales->impuesto, 2) ?></td>
        <td style="border: 0px; text-align: right;">Total: <?= MONEDA ?> <?= number_format($ingreso_totales->total, 2) ?></td>
    </tr>
</table>
<br>
    <table cellpadding="3" cellspacing="0">
        <thead>
        <tr>
            <th>ID</th>
            <th>Fecha Doc.</th>
            <th>Documento</th>
            <th>RUC Proveedor</th>
            <th>Proveedor</th>
            <th>Condicion</th>
            <th>Subtotal</th>
            <th>IGV</th>
            <th>Total</th>
            <th>Estado</th>
            <th>Usuario</th>
            <th>Fecha Registro</th>
            <th>Local</th>


        </tr>
        </thead>
        <tbody>
        <?php if (count($ingresos) > 0) {

            foreach ($ingresos as $ingreso) {
                ?>
                <tr>
                    <td><?php echo $ingreso->id_ingreso ?></td>
                    <td>
                        <span style="display: none;"><?= date('YmdHis', strtotime($ingreso->fecha_emision)) ?></span><?= date('d/m/Y', strtotime($ingreso->fecha_emision)) ?>
                    </td>
                    <td>
                        <?php
                        if ($ingreso->tipo_documento == 'FACTURA') echo 'FA ';
                        if ($ingreso->tipo_documento == 'BOLETA DE VENTA') echo 'BO ';
                        if ($ingreso->tipo_documento == "NOTA DE PEDIDO") echo "NP ";
                        ?>
                        <?php echo $ingreso->documento_serie . "-" . $ingreso->documento_numero ?>
                    </td>
                    <td><?= $ingreso->proveedor_ruc ?></td>
                    <td><?= $ingreso->proveedor_nombre ?></td>
                    <td><?= $ingreso->pago ?></td>
                    <td><?= $ingreso->sub_total_ingreso ?></td>
                    <td><?= $ingreso->impuesto_ingreso ?></td>
                    <td><?= $ingreso->total_ingreso ?></td>
                    <td><label
                                class="label <?php if ($ingreso->ingreso_status == INGRESO_COMPLETADO) {
                                    echo 'label-success';
                                } elseif ($ingreso->ingreso_status == INGRESO_PENDIENTE) {
                                    echo 'label-danger';
                                } else {
                                    echo 'label-warning';
                                } ?>">
                            <?= $ingreso->ingreso_status ?></label>

                    </td>
                    <td><?= $ingreso->nombre ?></td>
                    <td><?= date('d/m/Y', strtotime($ingreso->fecha_registro)) ?></td>
                    <td><?= $ingreso->local_nombre ?></td>


                </tr>
            <?php }
        } ?>

        </tbody>
    </table>


<style type="text/css">
    table td {
        width: 100%;
        border: #e1e1e1 1px solid;
        font-size: 10px;
    }

    thead, th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
        font-size: 11px;
    }

    h3 {
        margin: 2px;
    }
</style>

<?php
$total_venta = 0;
$total_pago = 0;
$total_saldo = 0; ?>
<?php foreach ($cobranzas as $cobranza): ?>
    <?php $actual_desglose = 0 ?>
    <?php $total_venta += $cobranza->total_deuda; ?>
    <?php $total_pago += $cobranza->actual; ?>
    <?php $total_saldo += $cobranza->saldo; ?>
<?php endforeach; ?>

<h3>NOMBRE DE LA EMPRESA: <?= valueOption('EMPRESA_NOMBRE', '') ?></h3>
<h3>
    NOMBRE DEL REPORTE: Cobranzas
    <?php if ($fecha_flag != 1): ?>
        (TODAS)
    <?php else: ?>
        (<?= date('d/m/Y', strtotime($fecha_ini)) ?> a <?= date('d/m/Y', strtotime($fecha_fin)) ?>)
    <?php endif; ?>
</h3>

<h3>FECHA DE CREADO: <?= date('d/m/Y H:i:s') ?></h3>
<br>
<table border="0">
    <tr>
        <td style="font-size: 12px; border: 0px; width: 80% !important;"></td>
        <td style="font-size: 12px; border: 0px; text-align: right;">Total
            Vendido: <?= MONEDA ?> <?= number_format($total_venta, 2) ?></td>
        <td style="font-size: 12px; border: 0px; text-align: right;">Total
            Pagado: <?= MONEDA ?> <?= number_format($total_pago, 2) ?></td>
        <td style="font-size: 12px; border: 0px; text-align: right;">Total
            Saldo: <?= MONEDA ?> <?= number_format($total_saldo, 2) ?></td>
    </tr>
</table>
<table cellpadding="3" cellspacing="0">
    <tr>
        <th>DOC</th>
        <th># Documento</th>
        <th>Cliente</th>
        <th>Fecha de Venta</th>
        <th>Venta</th>
        <th>Pago</th>
        <th>Saldo</th>
        <th>Zona</th>
        <th>Vendedor</th>
        <th>Atraso</th>
    </tr>
    <?php foreach ($cobranzas as $cobranza): ?>
        <?php $actual_desglose = 0 ?>
        <tr>
            <td><?= $cobranza->documento_nombre == 'NOTA DE ENTREGA' ? 'NE' : $cobranza->documento_nombre ?></td>
            <td><?= $cobranza->documento_serie . '-' . $cobranza->documento_numero ?></td>
            <td><?= $cobranza->cliente_nombre ?></td>
            <td><?= date('d/m/Y', strtotime($cobranza->fecha_venta)) ?></td>
            <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda, 2) ?></td>
            <td><?= MONEDA . ' ' . number_format($cobranza->actual, 2) ?></td>
            <td><?= MONEDA . ' ' . number_format($cobranza->saldo, 2) ?></td>
            <td><?= $cobranza->cliente_zona_nombre ?></td>
            <td><?= $cobranza->vendedor_nombre ?></td>
            <td><?= $cobranza->atraso ?></td>
        </tr>

        <?php if ($mostrar_detalles == 1): ?>
            <?php foreach ($cobranza->detalles as $detalle): ?>
                <tr>
                    <td colspan="3"><?= $detalle->tipo_pago_nombre ?></td>
                    <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                    <td></td>
                    <td><?= MONEDA . ' ' . number_format($detalle->monto, 2) ?></td>
                    <?php $actual_desglose += $detalle->monto; ?>
                    <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda - $actual_desglose, 2) ?></td>
                    <td colspan="3"></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    <?php endforeach; ?>
</table>
<table border="0">
    <tr>
        <td style="font-size: 12px; border: 0px; width: 80% !important;"></td>
        <td style="font-size: 12px; border: 0px; text-align: right;">Total
            Vendido: <?= MONEDA ?> <?= number_format($total_venta, 2) ?></td>
        <td style="font-size: 12px; border: 0px; text-align: right;">Total
            Pagado: <?= MONEDA ?> <?= number_format($total_pago, 2) ?></td>
        <td style="font-size: 12px; border: 0px; text-align: right;">Total
            Saldo: <?= MONEDA ?> <?= number_format($total_saldo, 2) ?></td>
    </tr>
</table>

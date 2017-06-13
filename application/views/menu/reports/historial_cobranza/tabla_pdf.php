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

<?php $total = 0; ?>
<?php foreach ($ventas as $v): ?>
    <?php $total += $v->monto; ?>
<?php endforeach; ?>

<h3>NOMBRE DE LA EMPRESA: <?= valueOption('EMPRESA_NOMBRE', '') ?></h3>
<h3>
    NOMBRE DEL REPORTE: Historial de Cobranzas
    <?php if ($fecha_flag != 1): ?>
        (TODAS)
    <?php else: ?>
        (<?= date('d/m/Y', strtotime($fecha_ini)) ?> a <?= date('d/m/Y', strtotime($fecha_fin)) ?>)
    <?php endif; ?>
</h3>

<h3>FECHA DE CREADO: <?= date('d/m/Y H:i:s') ?></h3>
<br>
<table border="0" style="width: 100%;">
    <tr>
        <td style="font-size: 12px; border: 0px; text-align: right; width: 100% !important;">
            Total: <?= MONEDA ?> <?= number_format($total, 2) ?>
        </td>
    </tr>
</table>
<table cellpadding="3" cellspacing="0">
    <tr>
        <th>Documento (Fecha)</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Tipo de Pago</th>
        <th>Monto</th>
        <th>Zona</th>
        <th>Vendedor</th>
    </tr>
    <?php foreach ($ventas as $v): ?>
        <tr>
            <td><?= $v->venta . ' (' . date('d/m/Y', strtotime($v->fecha_venta)) . ')' ?></td>
            <td><?= $v->cliente ?></td>
            <td><?= date('d/m/Y', strtotime($v->fecha)) ?></td>
            <td><?= $v->tipo_pago ?></td>
            <td><?= MONEDA . ' ' . number_format($v->monto, 2) ?></td>
            <td><?= $v->zona ?></td>
            <td><?= $v->vendedor ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<table border="0" style="width: 100%;">
    <tr>
        <td style="font-size: 12px; border: 0px; text-align: right; width: 100% !important;">
            Total: <?= MONEDA ?> <?= number_format($total, 2) ?>
        </td>
    </tr>
</table>
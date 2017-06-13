<style type="text/css">
    table {
        width: 100%;
    }

    table td {
        border: #e1e1e1 1px solid;
        font-size: 10px;
    }

    table th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
        font-size: 11px;
    }

    h3 {
        margin: 2px;
    }

</style>

<?php $total_egresos = 0; ?>
<?php $total_ingresos = 0; ?>
<?php foreach ($cuenta_movimientos as $mov): ?>
    <?php if ($mov->movimiento == 'INGRESO') {
        $total_ingresos += $mov->saldo;
    } else {
        $total_egresos += $mov->saldo;
    }
    ?>
<?php endforeach;?>

<h3>NOMBRE DE LA EMPRESA: <?= valueOption('EMPRESA_NOMBRE', '') ?></h3>
<h3>
    NOMBRE DEL REPORTE: Historial de Movimientos de Caja.

    <br>CAJA: <?= $cuenta->descripcion ?>
    (<?= date('d/m/Y', strtotime($fecha_ini)) ?> a <?= date('d/m/Y', strtotime($fecha_fin)) ?>)
</h3>

<h3>FECHA DE CREADO: <?= date('d/m/Y H:i:s') ?></h3>
<br>

<table>
    <tr>
        <td style="font-size: 12px; border: 0px; text-align: right; width: 75% !important;">
            <label>Ingresos: </label> <?= $caja->moneda_id == 1 ? MONEDA : DOLAR ?> <?= number_format($total_ingresos, 2) ?>
        </td>
        <td style="font-size: 12px; border: 0px; text-align: right; width: 25% !important;">
            <label>Egresos: </label> <?= $caja->moneda_id == 1 ? MONEDA : DOLAR ?> <?= number_format($total_egresos, 2) ?>
        </td>
    </tr>
</table>

<table cellpadding="3" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Fecha</th>
        <th>Responsable</th>
        <th>Movimiento</th>
        <th>Operacion</th>
        <th>Pago</th>
        <th>Saldo</th>
    </tr>
    <?php foreach ($cuenta_movimientos as $mov): ?>
        <tr>
            <td><?= $mov->id ?></td>
            <td><?= $mov->created_at ?></td>
            <td><?= $mov->usuario_nombre ?></td>
            <td><?= $mov->movimiento ?></td>
            <td><?= $mov->operacion ?></td>
            <td><?= $mov->medio_pago ?></td>
            <td><?= $mov->moneda_id == 1 ? MONEDA : DOLAR ?> <?= number_format($mov->saldo, 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>

<table>
    <tr>
        <td style="font-size: 12px; border: 0px; text-align: right; width: 75% !important;">
            <label>Ingresos: </label> <?= $caja->moneda_id == 1 ? MONEDA : DOLAR ?> <?= number_format($total_ingresos, 2) ?>
        </td>
        <td style="font-size: 12px; border: 0px; text-align: right; width: 25% !important;">
            <label>Egresos: </label> <?= $caja->moneda_id == 1 ? MONEDA : DOLAR ?> <?= number_format($total_egresos, 2) ?>
        </td>
    </tr>
</table>


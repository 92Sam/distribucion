<style>
    .tabla_detalles {
        display: <?=$mostrar_detalles == 1? 'block-inline;': 'none;'?>
    }

    table th {
        background-color: #f4f4f4;
    }

    .b-default {
        background-color: #55c862;
        color: #fff;
    }

    .b-warning {
        background-color: #f7be64;
        color: #fff;
    }
</style>

<table class="table table-bordered">
    <tr>
        <th rowspan="2" style="vertical-align: middle;">Mes</th>
        <th colspan="4" style="text-align: center;">Resumen de Ventas</th>
        <th colspan="4" style="text-align: center;">Resumen de Compras</th>
        <th rowspan="2" style="vertical-align: middle;">Ventas - Compras</th>
    </tr>
    <tr>
        <th>Cantidad</th>
        <th>Importe</th>
        <th>Actual</th>
        <th>Saldo</th>
        <th>Cantidad</th>
        <th>Importe</th>
        <th>Pagado</th>
        <th>Por Pagar</th>
    </tr>
    <?php foreach ($ventas_compras as $vc): ?>
        <tr <?= $vc->mes == date('n') ? 'style="background-color: #c6efce; font-weight: bold;"' : '' ?>>
            <td><?= getMes($vc->mes) ?></td>
            <td><?= $vc->cantidad_venta ?></td>
            <td><?= MONEDA . '' . number_format($vc->importe_venta, 2) ?></td>
            <td><?= MONEDA . '' . number_format($vc->saldo_venta, 2) ?></td>
            <td><?= MONEDA . '' . number_format($vc->pagado_venta, 2) ?></td>
            <td><?= $vc->cantidad_compra ?></td>
            <td><?= MONEDA . '' . number_format($vc->importe_compra, 2) ?></td>
            <td><?= MONEDA . '' . number_format($vc->pagado_compra, 2) ?></td>
            <td><?= MONEDA . '' . number_format($vc->saldo_compra, 2) ?></td>
            <td>
                <label style="margin-bottom: 0px; font-size: 12px;"
                       class="control-label badge <?= $vc->importe_venta - $vc->importe_compra < 0 ? 'b-warning' : 'b-default' ?>">
                <?= MONEDA . '' . number_format($vc->importe_venta - $vc->importe_compra, 2) ?></td>
            </label>
        </tr>
    <?php endforeach; ?>
</table>
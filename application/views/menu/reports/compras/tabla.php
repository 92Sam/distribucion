<style>
    table th {
        background-color: #f4f4f4;
    }
</style>

<table class="table table-bordered">
    <tr>
        <th>Desglose</th>
        <th colspan="4" style="text-align: center;">Resumen de Compras</th>
        <th colspan="3" style="text-align: center;">Importe de las Compras Completadas</th>
    </tr>
    <tr>
        <th><?= $desglose ?></th>
        <th>Total de Compras</th>
        <th>Completados</th>
        <th>Pendientes</th>
        <th>Anulados</th>
        <th>Total Importe</th>
        <th>Pagado a Proveedores</th>
        <th>Cuentas por Pagar</th>
    </tr>
    <tr style="background-color: #c6efce; font-weight: bold;">
        <td>TOTALES</td>
        <td><?= $compras->total_completado + $compras->total_pendiente + $compras->total_anulado ?></td>
        <td><?= $compras->total_completado ?></td>
        <td><?= $compras->total_pendiente ?></td>
        <td><?= $compras->total_anulado ?></td>
        <td><?= MONEDA . '' . number_format($compras->importe_completado, 2) ?></td>
        <td><?= MONEDA . '' . number_format($compras->importe_pagado, 2) ?></td>
        <td><?= MONEDA . '' . number_format($compras->importe_pendiente, 2) ?></td>
    </tr>

    <?php foreach ($compras->desgloses as $desglose): ?>
        <tr>
            <td><?= $desglose->desglose ?></td>
            <td><?= $desglose->total_completado + $desglose->total_pendiente + $desglose->total_anulado ?></td>
            <td><?= $desglose->total_completado ?></td>
            <td><?= $desglose->total_pendiente ?></td>
            <td><?= $desglose->total_anulado ?></td>
            <td><?= MONEDA . '' . number_format($desglose->importe_completado, 2) ?></td>
            <td><?= MONEDA . '' . number_format($desglose->importe_pagado, 2) ?></td>
            <td><?= MONEDA . '' . number_format($desglose->importe_pendiente, 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
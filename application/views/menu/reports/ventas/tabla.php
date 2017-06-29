<style>
    .tabla_detalles {
        display: <?=$mostrar_detalles == 1? 'block-inline;': 'none;'?>
    }

    table th {
        background-color: #f4f4f4;
    }
</style>
<?php
$ventas->desgloses = sort_object($ventas->desgloses, function ($a, $b) {
    return ($a->importe_completado < $b->importe_completado);
});
?>
<table class="table table-bordered">
    <tr>
        <th>Desglose</th>
        <th colspan="5" style="text-align: center;">Resumen de Ventas</th>
        <th colspan="3" style="text-align: center;">Importe de las Ventas</th>
    </tr>
    <tr>
        <th><?= $desglose ?></th>
        <th>Total de Ventas</th>
        <th>Cancelada</th>
        <th>Pendiente</th>
        <th>Rechazadas</th>
        <th>En Proceso</th>
        <th>Total</th>
        <th>Pagado</th>
        <th>Saldo</th>
    </tr>
    <tr style="background-color: #c6efce; font-weight: bold;">
        <td>TOTALES</td>
        <td><?= $ventas->total_completado ?></td>
        <td><?= $ventas->total_cancelada ?></td>
        <td><?= $ventas->total_cobranzas ?></td>
        <td><?= $ventas->total_rechazado ?></td>
        <td><?= $ventas->total_proceso ?></td>
        <td><?= MONEDA . '' . number_format($ventas->importe_completado, 2) ?></td>
        <td><?= MONEDA . '' . number_format($ventas->importe_cobranza, 2) ?></td>
        <td><?= MONEDA . '' . number_format($ventas->importe_completado - $ventas->importe_cobranza, 2) ?></td>
    </tr>

    <?php foreach ($ventas->desgloses as $desglose): ?>
        <tr>
            <td><?= $desglose->desglose ?></td>
            <td><?= $desglose->total_completado ?></td>
            <td><?= $desglose->total_cancelada ?></td>
            <td><?= $desglose->total_cobranzas ?></td>
            <td><?= $desglose->total_rechazado ?></td>
            <td><?= $desglose->total_proceso ?></td>
            <td><?= MONEDA . '' . number_format($desglose->importe_completado, 2) ?></td>
            <td><?= MONEDA . '' . number_format($desglose->importe_cobranza, 2) ?></td>
            <td><?= MONEDA . '' . number_format($desglose->importe_completado - $desglose->importe_cobranza, 2) ?></td>
        </tr>
    <?php endforeach; ?>
</table>
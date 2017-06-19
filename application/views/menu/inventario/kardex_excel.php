<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=kardex.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>

<table>
    <thead>
    <tr>
        <th colspan="4">Descripcion: <?= $producto->nombre ?></th>
        <th colspan="3">Unidad de Medida: <?= $producto->um_nombre ?></th>
        <th colspan="3">Periodo: <?= $periodo ?></th>
    </tr>
    <tr>
        <th rowspan="2">Fecha</th>
        <th rowspan="2">Tipo</th>
        <th rowspan="2">Serie</th>
        <th rowspan="2">N&uacute;mero</th>
        <th rowspan="2">Referencia</th>
        <th rowspan="2">UM</th>
        <th rowspan="2">Tipo de Operación</th>
        <th colspan="3" style="text-align: center;">Entradas</th>
        <th colspan="3" style="text-align: center;">Salidas</th>
        <th colspan="3" style="text-align: center;">Saldo Final</th>
    </tr>
    <tr>
        <th>Cantidad</th>
        <th>Costo Unit.</th>
        <th>Costo Total</th>
        <th>Cantidad</th>
        <th>Costo Unit.</th>
        <th>Costo Total</th>
        <th>Cantidad</th>
        <th>Costo Unit.</th>
        <th>Costo Final</th>
    </tr>
    </thead>
    <tbody id="columnas">
    <tr>
        <td></td>
        <td>OTROS</td>
        <td></td>
        <td></td>
        <td></td>
        <td><?= $kardex['inicial'] != NULL ? $kardex['inicial']->nombre_unidad : $kardex['fiscal'][0]->nombre_unidad ?></td>
        <td>SALDO ANTERIOR</td>

        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>
        <td></td>

        <td><?= $kardex['inicial'] != NULL ? $kardex['inicial']->cantidad_final : 0 ?></td>
        <td style="white-space: nowrap;"><?= $kardex['inicial'] != NULL ? MONEDA . ' ' . number_format($kardex['inicial']->costo_unitario_final, 2) : MONEDA . ' 0.00' ?></td>
        <td style="white-space: nowrap;"><?= $kardex['inicial'] != NULL ? MONEDA . ' ' . number_format($kardex['inicial']->total_final, 2) : MONEDA . ' 0.00' ?></td>
    </tr>
    <?php if (count($kardex['fiscal']) > 0): ?>
        <?php foreach ($kardex['fiscal'] as $detalle): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                <?php $tipo_doc = get_tipo_doc($detalle->tipo_doc) ?>
                <td><?= $tipo_doc['value'] ?></td>
                <td><?= $detalle->serie ?></td>
                <td><?= $detalle->numero ?></td>
                <td><?= $detalle->referencia ?></td>
                <td><?= $detalle->nombre_unidad ?></td>
                <?php $tipo_operacion = get_tipo_operacion($detalle->tipo_operacion) ?>
                <td><?= $tipo_operacion['value'] ?></td>
                <?php if ($detalle->IO == 1): ?>
                    <td><?= $detalle->cantidad ?></td>
                    <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario, 2) ?></td>
                    <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total, 2) ?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                <?php endif; ?>
                <?php if ($detalle->IO == 2): ?>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td><?= $detalle->cantidad ?></td>
                    <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario, 2) ?></td>
                    <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total, 2) ?></td>
                <?php endif; ?>
                <td><?= $detalle->cantidad_final ?></td>
                <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario_final, 2) ?></td>
                <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total_final, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
    </tbody>
</table>
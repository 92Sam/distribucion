<style>
    .tabla_detalles {
        display: <?=$mostrar_detalles == 1? 'block-inline;': 'none;'?>
    }
</style>

<table class="table table-striped dataTable table-bordered">
    <thead>
    <tr>
        <th>DOC</th>
        <th># Documento</th>
        <th>Cliente</th>
        <th>Fecha de Venta</th>
        <th>Total deuda</th>
        <th>Actual</th>
        <th>Saldo</th>
        <th>Zona</th>
        <th>Vendedor</th>
        <th>Atraso</th>
    </tr>
    </thead>
    <tbody>
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

        <tr class="tabla_detalles">
            <td colspan="3"><?= $cobranza->generado->tipo_pago_nombre ?></td>
            <td><?= date('d/m/Y', strtotime($cobranza->generado->fecha)) ?></td>
            <td></td>
            <td><?= MONEDA . ' ' . number_format($cobranza->generado->monto, 2) ?></td>
            <?php $actual_desglose += $cobranza->generado->monto; ?>
            <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda - $actual_desglose, 2) ?></td>
        </tr>

        <tr class="tabla_detalles">
            <td colspan="3"><?= $cobranza->liquidacion->tipo_pago_nombre ?></td>
            <td><?= date('d/m/Y', strtotime($cobranza->liquidacion->fecha)) ?></td>
            <td></td>
            <td><?= MONEDA . ' ' . number_format($cobranza->liquidacion->monto, 2) ?></td>
            <?php $actual_desglose += $cobranza->liquidacion->monto; ?>
            <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda - $actual_desglose, 2) ?></td>
        </tr>

        <? foreach ($cobranza->detalles as $detalle): ?>
            <tr class="tabla_detalles">
                <td colspan="3"><?= $detalle->tipo_pago_nombre ?></td>
                <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                <td></td>
                <td><?= MONEDA . ' ' . number_format($detalle->monto, 2) ?></td>
                <?php $actual_desglose += $detalle->monto; ?>
                <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda - $actual_desglose, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
    </tbody>
</table>
<style>
    .tabla_detalles {
        display: <?=$mostrar_detalles == 1? 'block-inline;': 'none;'?>
    }
</style>

<?php foreach ($clientes as $cliente): ?>
    <table class="table table-striped dataTable table-bordered">
        <tbody>
        <tr>
            <th>Cliente</th>
            <td colspan="6"><?= $cliente->cliente_nombre ?></td>
        </tr>
        <tr>
            <th>Zona</th>
            <td colspan="2"><?= $cliente->cliente_zona_nombre ?></td>
            <th>Vendedor</th>
            <td colspan="4"><?= $cliente->vendedor_nombre ?></td>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Documento</th>
            <th>Descripci&oacute;n</th>
            <th>Venta</th>
            <th>Pago</th>
            <th>Saldo</th>
            <th>Estado</th>
        </tr>
        <?php foreach ($cliente->cobranzas as $cobranza): ?>
            <?php $actual_desglose = 0 ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($cobranza->fecha_venta)) ?></td>
                <td>
                    <?= $cobranza->documento_nombre == 'NOTA DE ENTREGA' ? 'NE' : $cobranza->documento_nombre ?>
                    -
                    <?= $cobranza->documento_numero ?>
                </td>
                <td>NOTA DE PEDIDO</td>
                <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda, 2) ?></td>
                <td></td>
                <?php $actual_desglose += $cobranza->total_deuda; ?>
                <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda - $actual_desglose, 2) ?></td>
                <td><?= $cobranza->cliente_zona_nombre ?></td>
                <td><?= $cobranza->vendedor_nombre ?></td>
                <td><?= $cobranza->atraso ?></td>
            </tr>

            <tr class="tabla_detalles">
                <td><?= $cobranza->generado->tipo_pago_nombre ?></td>
                <td><?= date('d/m/Y', strtotime($cobranza->generado->fecha)) ?></td>
                <td></td>
                <td><?= MONEDA . ' ' . number_format($cobranza->generado->monto, 2) ?></td>
                <?php $actual_desglose += $cobranza->generado->monto; ?>
                <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda - $actual_desglose, 2) ?></td>
            </tr>

            <tr>
                <td colspan="3"><?= $cobranza->liquidacion->tipo_pago_nombre ?></td>
                <td><?= date('d/m/Y', strtotime($cobranza->liquidacion->fecha)) ?></td>
                <td></td>
                <td><?= MONEDA . ' ' . number_format($cobranza->liquidacion->monto, 2) ?></td>
                <?php $actual_desglose += $cobranza->liquidacion->monto; ?>
                <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda - $actual_desglose, 2) ?></td>
            </tr>

            <? foreach ($cobranza->detalles as $detalle): ?>
                <tr class="tabla_detalles">
                    <td><?= $detalle->tipo_pago_nombre ?></td>
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
<?php endforeach; ?>

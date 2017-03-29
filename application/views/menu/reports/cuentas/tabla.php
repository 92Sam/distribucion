<style>
    .tabla_detalles {
        display: <?=$mostrar_detalles == 1? 'block-inline;': 'none;'?>
    }
</style>
<div class="col-md-3"><label>Total Comprado: </label> <?= MONEDA ?> <span id="total_compra"></span></div>
<div class="col-md-3"><label>Total Pagado: </label> <?= MONEDA ?> <span id="total_pago"></span></div>
<div class="col-md-3"><label>Total Cuenta: </label> <?= MONEDA ?> <span id="total_cuenta"></span></div>

<table class="table table-striped table-bordered">
    <tr>
        <th>DOC</th>
        <th># Documento</th>
        <th>Proveedor</th>
        <th>Fecha de Emisi&oacute;n</th>
        <th>Compra</th>
        <th>Pagado a Proveedor</th>
        <th>Cuenta por Pagar</th>
        <th>Atraso</th>
    </tr>
    <?php
    $total_compra = 0;
    $total_pago = 0;
    $total_cuenta = 0; ?>
    <?php foreach ($cuentas as $cuenta): ?>
        <?php $actual_desglose = 0 ?>
        <?php $total_compra += $cuenta->total; ?>
        <?php $total_pago += $cuenta->pagado; ?>
        <?php $total_cuenta += $cuenta->cuenta_pagar; ?>
        <tr>
            <td><?= $cuenta->documento_nombre ?></td>
            <td><?= $cuenta->documento_serie . '-' . $cuenta->documento_numero ?></td>
            <td><?= $cuenta->proveedor_nombre ?></td>
            <td><?= date('d/m/Y', strtotime($cuenta->fecha_emision)) ?></td>
            <td><?= MONEDA . ' ' . number_format($cuenta->total, 2) ?></td>
            <td><?= MONEDA . ' ' . number_format($cuenta->pagado, 2) ?></td>
            <td><?= MONEDA . ' ' . number_format($cuenta->cuenta_pagar, 2) ?></td>
            <td><?= $cuenta->atraso ?></td>
        </tr>

        <? foreach ($cuenta->detalles as $detalle): ?>
            <tr class="tabla_detalles">
                <td colspan="3">
                    <?= $detalle->pago_nombre?>
                    <?= $detalle->pago_id == 4 ? '| '. $detalle->banco_nombre . '| '. $detalle->operacion : '' ?>
                    <?= $detalle->pago_id != 3 && $detalle->pago_id != 4 ? '| '. $detalle->operacion : '' ?>
                </td>
                <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                <td></td>
                <td><?= MONEDA . ' ' . number_format($detalle->monto, 2) ?></td>
                <?php $actual_desglose += $detalle->monto; ?>
                <td><?= MONEDA . ' ' . number_format($cuenta->total - $actual_desglose, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>
<input type="hidden" id="input_compra" value="<?= number_format($total_compra, 2) ?>">
<input type="hidden" id="input_pago" value="<?= number_format($total_pago, 2) ?>">
<input type="hidden" id="input_cuenta" value="<?= number_format($total_cuenta, 2) ?>">

<script>
    $(document).ready(function () {
        $("#total_compra").html($("#input_compra").val());
        $("#total_pago").html($("#input_pago").val());
        $("#total_cuenta").html($("#input_cuenta").val());
    });
</script>
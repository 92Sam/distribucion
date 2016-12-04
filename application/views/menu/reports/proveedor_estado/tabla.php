<style>
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
<div class="col-md-3"><label>Total Comprado: </label> <?= MONEDA ?> <span id="total_compra"></span></div>
<div class="col-md-3"><label>Total Pagado: </label> <?= MONEDA ?> <span id="total_pago"></span></div>
<div class="col-md-3"><label>Total de Cuenta: </label> <?= MONEDA ?> <span id="total_cuenta"></span></div>
<?php
$total_compra = 0;
$total_pago = 0;
$total_cuenta = 0; ?>
<?php foreach ($proveedores as $proveedor): ?>
    <table class="table table-condensed table-bordered">
        <tbody>
        <tr>
            <th colspan="2">Proveedor</th>
            <th colspan="2">Total Comprado</th>
            <th colspan="2">Total Pagado</th>
            <th colspan="2">Total de Cuentas</th>
        </tr>
        <tr>
            <td colspan="2"><?= $proveedor->proveedor_nombre ?></td>
            <td colspan="2">
                <?= MONEDA ?> <?= number_format($proveedor->subtotal_compra, 2) ?>
            </td>
            <td colspan="2">
                <?= MONEDA ?> <?= number_format($proveedor->subtotal_pagado, 2) ?>
            </td>
            <td colspan="2">
                <label style="margin-bottom: 0px;"
                       class="control-label badge <?= $proveedor->subtotal_compra - $proveedor->subtotal_pagado > 0 ? 'b-warning' : 'b-default' ?>">
                    <?= MONEDA ?> <?= number_format($proveedor->subtotal_compra - $proveedor->subtotal_pagado, 2) ?>
                </label>
            </td>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>DOC</th>
            <th># Documento</th>
            <th>Comprado</th>
            <th>Pagado a Proveedores</th>
            <th>Cuenta Por Pagar</th>
            <th>Estado</th>
        </tr>
        <?php $actual_desglose = 0 ?>
        <?php foreach ($proveedor->pagos as $pago): ?>
            <?php $total_compra += $pago->total; ?>
            <?php $total_pago += $pago->pagado; ?>
            <?php $total_cuenta += $pago->cuenta_pagar; ?>
            <tr style="background-color: #dae8e7;">
                <td><?= date('d/m/Y', strtotime($pago->fecha_emision)) ?></td>
                <td>
                    <?= $pago->documento_nombre ?>
                </td>
                <td>
                    <?= $pago->documento_serie ?>
                    -
                    <?= $pago->documento_numero ?>
                </td>
                <td><?= MONEDA . ' ' . number_format($pago->total, 2) ?></td>
                <td></td>
                <?php $actual_desglose += $pago->total; ?>
                <td><?= MONEDA . ' ' . number_format($actual_desglose, 2) ?></td>
                <td><?= $pago->cuenta_pagar > 0 ? 'POR PAGAR' : 'PAGADO' ?></td>
            </tr>

            <?php if ($pago->pago == 'CONTADO'): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($pago->fecha_emision)) ?></td>
                    <td></td>
                    <td>CONTADO</td>
                    <td></td>
                    <td><?= MONEDA . ' ' . number_format($pago->total, 2) ?></td>
                    <?php $actual_desglose -= $pago->total; ?>
                    <td><?= MONEDA . ' ' . number_format($actual_desglose, 2) ?></td>
                    <td></td>
                </tr>
            <?php endif; ?>

            <? foreach ($pago->detalles as $detalle): ?>

                <tr>
                    <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                    <td></td>
                    <td>CR&Eacute;DITO</td>
                    <td></td>
                    <td><?= MONEDA . ' ' . number_format($detalle->monto, 2) ?></td>
                    <?php $actual_desglose -= $detalle->monto; ?>
                    <td><?= MONEDA . ' ' . number_format($actual_desglose, 2) ?></td>
                    <td></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endforeach; ?>
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
<style>
    .tabla_detalles {
        display: <?=$mostrar_detalles == 1? 'block-inline;': 'none;'?>
    }
</style>
<div class="col-md-3"><label>Total Vendido: </label> <?= MONEDA ?> <span id="total_venta"></span></div>
<div class="col-md-3"><label>Total Pagado: </label> <?= MONEDA ?> <span id="total_pago"></span></div>
<div class="col-md-3"><label>Total Saldo: </label> <?= MONEDA ?> <span id="total_saldo"></span></div>

<table class="table table-striped table-bordered">
    <tr>
        <th>DOC</th>
        <th># Documento</th>
        <th>Cliente</th>
        <th>Fecha de Venta</th>
        <th>Venta</th>
        <th>Pago</th>
        <th>Saldo</th>
        <th>Zona</th>
        <th>Vendedor</th>
        <th>Atraso</th>
    </tr>
    <?php
    $total_venta = 0;
    $total_pago = 0;
    $total_saldo = 0; ?>
    <?php foreach ($cobranzas as $cobranza): ?>
        <?php $actual_desglose = 0 ?>
        <?php $total_venta += $cobranza->total_deuda; ?>
        <?php $total_pago += $cobranza->actual; ?>
        <?php $total_saldo += $cobranza->saldo; ?>
        <tr>
            <td><?= $cobranza->documento_nombre == 'NOTA DE ENTREGA' ? 'NE' : $cobranza->documento_nombre ?></td>
            <td><?= $cobranza->documento_serie . '-' . $cobranza->documento_numero ?></td>
            <td><?= $cobranza->cliente_nombre ?></td>
            <td><?= date('d/m/Y', strtotime($cobranza->fecha_venta)) ?></td>
            <td><?= MONEDA . ' ' . formatPrice($cobranza->total_deuda) ?></td>
            <td><?= MONEDA . ' ' . formatPrice($cobranza->actual) ?></td>
            <td><?= MONEDA . ' ' . formatPrice($cobranza->saldo) ?></td>
            <td><?= $cobranza->cliente_zona_nombre ?></td>
            <td><?= $cobranza->vendedor_nombre ?></td>
            <td><?= $cobranza->atraso ?></td>
        </tr>

        <?php foreach ($cobranza->detalles as $detalle): ?>
            <tr class="tabla_detalles">
                <td colspan="3"><?= $detalle->tipo_pago_nombre ?></td>
                <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                <td></td>
                <td><?= MONEDA . ' ' . formatPrice($detalle->monto) ?></td>
                <?php $actual_desglose += $detalle->monto; ?>
                <td><?= MONEDA . ' ' . formatPrice($cobranza->total_deuda - $actual_desglose) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>
<input type="hidden" id="input_venta" value="<?= formatPrice($total_venta) ?>">
<input type="hidden" id="input_pago" value="<?= formatPrice($total_pago) ?>">
<input type="hidden" id="input_saldo" value="<?= formatPrice($total_saldo) ?>">

<script>
    $(document).ready(function () {
        $("#total_venta").html($("#input_venta").val());
        $("#total_pago").html($("#input_pago").val());
        $("#total_saldo").html($("#input_saldo").val());
    });
</script>
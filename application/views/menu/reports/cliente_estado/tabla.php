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
<div class="col-md-3"><label>Total Vendido: </label> <?= MONEDA ?> <span id="total_venta"></span></div>
<div class="col-md-3"><label>Total Pagado: </label> <?= MONEDA ?> <span id="total_pago"></span></div>
<div class="col-md-3"><label>Total Saldo: </label> <?= MONEDA ?> <span id="total_saldo"></span></div>
<?php
$total_venta = 0;
$total_pago = 0;
$total_saldo = 0; ?>
<?php if (count($clientes) == 0 && isset($form_filter)) echo '<h3>No hay resultados para mostrar.</h3>' ?>
<?php foreach ($clientes as $cliente): ?>
    <table class="table table-condensed table-bordered">
        <tbody>
        <tr>
            <th>Cliente</th>
            <td colspan="4"><?= $cliente->cliente_nombre ?></td>
            <th>Total Vendido</th>
            <td colspan="4"><?= MONEDA ?> <span><?= number_format($cliente->subtotal_venta, 2) ?></span></td>
        </tr>
        <tr>
            <th>Zona</th>
            <td colspan="4"><?= $cliente->cliente_zona_nombre ?></td>
            <th>Total Pagado</th>
            <td colspan="4"><?= MONEDA ?> <span><?= number_format($cliente->subtotal_pago, 2) ?></span></td>
        </tr>
        <tr>
            <th>Vendedor</th>
            <td colspan="4"><?= $cliente->vendedor_nombre ?></td>
            <th>Total Saldo</th>
            <td colspan="4">
                <label style="margin-bottom: 0px;"
                       class="control-label badge <?= $cliente->subtotal_venta - $cliente->subtotal_pago > 0 ? 'b-warning' : 'b-default' ?>">
                    <?= MONEDA ?>
                    <?= number_format($cliente->subtotal_venta - $cliente->subtotal_pago, 2) ?>
                </label>
            </td>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Documento</th>
            <th>Descripci&oacute;n</th>
            <th>Venta</th>
            <th>Pago</th>
            <th>Saldo</th>
            <th>Estado</th>
            <th></th>
        </tr>
        <?php $actual_desglose = 0 ?>
        <?php foreach ($cliente->cobranzas as $cobranza): ?>
            <?php $total_venta += $cobranza->total_deuda; ?>
            <?php $total_pago += $cobranza->actual; ?>
            <?php $total_saldo += $cobranza->credito; ?>
            <tr style="background-color: #dae8e7;">
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
                <td><?= MONEDA . ' ' . number_format($actual_desglose, 2) ?></td>
                <td><?= $cobranza->credito > 0 ? 'Pendiente' : 'Cancelado' ?></td>
                <td>
                    <a href="#" class="btn btn-default show_detalle" data-id="<?= $cobranza->venta_id ?>">
                        <i class="fa fa-search"></i>
                    </a>
                </td>
            </tr>

            <? foreach ($cobranza->detalles as $detalle): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                    <td></td>
                    <td><?= $detalle->tipo_pago_nombre ?></td>
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
<input type="hidden" id="input_venta" value="<?= number_format($total_venta, 2) ?>">
<input type="hidden" id="input_pago" value="<?= number_format($total_pago, 2) ?>">
<input type="hidden" id="input_saldo" value="<?= number_format($total_saldo, 2) ?>">

<script>
    $(document).ready(function () {

        $('.show_detalle').on('click', function(e){
            e.preventDefault();

            $.ajax({
                url: '<?=base_url("reporte_modals/detalle_nota_entrega")?>/' + $(this).attr('data-id'),
                type: 'GET',
                success: function(data){
                    $('#detalle_modal').html(data);
                    $('#detalle_modal').modal('show');
                }
            })
        });

        $("#total_venta").html($("#input_venta").val());
        $("#total_pago").html($("#input_pago").val());
        $("#total_saldo").html($("#input_saldo").val());
    });
</script>
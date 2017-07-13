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

    .b-danger {
        background-color: #e74c3c;
        color: #fff;
    }

    .b-primary {
        background-color: #1493D1 !important;
        color: #fff;
    }

</style>
<?php if (count($clientes) == 0 && isset($form_filter)) echo '<h3>No hay resultados para mostrar.</h3>' ?>
<?php foreach ($clientes as $cliente): ?>
    <div class="table-responsive">
    <table class="table table-condensed table-bordered" id="tabla_<?= $cliente->cliente_id ?>">
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
            <td colspan="2"><?= $cliente->vendedor_nombre ?></td>
            <th>Cobranza por Liquidar</th>
            <td>
                <label style="margin-bottom: 0px;"
                       class="control-label badge <?= $cliente->vendedor_pendiente > 0 ? 'b-danger' : 'b-default' ?>">
                    <?= MONEDA ?>
                    <?= number_format($cliente->vendedor_pendiente, 2) ?>
                </label>
                <button style="float: right" type="button" class="b-primary luiquidar_pago"
                        data-vendedor_id="<?= $cliente->vendedor_id ?>">
                    <i class="fa fa-money"></i>
                </button>
            </td>
            <th>Total Saldo</th>
            <td colspan="2">
                <label style="margin-bottom: 0px;"
                       class="control-label badge <?= $cliente->subtotal_venta - $cliente->subtotal_pago > 0 ? 'b-danger' : 'b-default' ?>">
                    <?= MONEDA ?>
                    <?= number_format($cliente->subtotal_venta - $cliente->subtotal_pago, 2) ?>
                </label>
                <?php if ($cliente->subtotal_venta - $cliente->subtotal_pago > 0): ?>
                    <button style="float: right" type="button" class="b-primary pagar_cliente"
                            data-cliente_id="<?= $cliente->cliente_id ?>">
                        <i class="fa fa-money"></i>
                    </button>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th>Fecha</th>
            <th>Documento</th>
            <th>Venta</th>
            <th>Pago Confirmado</th>
            <th>Pago Pendiente</th>
            <th>Saldo</th>
            <th>Atraso</th>
            <th>Acciones</th>
        </tr>
        <?php foreach ($cliente->cobranzas as $cobranza): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($cobranza->fecha_venta)) ?></td>
                <td id="pedido_<?= $cobranza->venta_id ?>">
                    <?= $cobranza->documento_nombre == 'NOTA DE ENTREGA' ? 'NE' : $cobranza->documento_nombre ?>
                    -
                    <?= $cobranza->documento_numero ?>
                </td>
                <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda, 2) ?></td>
                <td><?= MONEDA . ' ' . number_format($cobranza->actual - $cobranza->pagado_pendientes, 2) ?></td>
                <td><?= MONEDA . ' ' . number_format($cobranza->pagado_pendientes, 2) ?></td>
                <td>
                    <label style="margin-bottom: 0px;"
                           class="control-label badge <?= $cobranza->credito > 0 ? 'b-danger' : 'b-default' ?>">
                        <?= MONEDA . ' ' . number_format($cobranza->credito, 2) ?>
                    </label>
                    <?php if ($cobranza->credito > 0): ?>
                        <button style="float: right" type="button" class="b-primary pagar_pedido"
                                data-pedido_id="<?= $cobranza->venta_id ?>">
                            <i class="fa fa-money"></i>
                        </button>
                    <?php endif; ?>
                </td>
                <td><?= $cobranza->atraso ?></td>
                <td>
                    <button type="button" class="b-primary ver_pagos"
                            data-pedido_id="<?= $cobranza->venta_id ?>">
                        <i class="fa fa-search"></i>
                    </button>

                    <button type="button" class="b-default show_detalle"
                            data-id="<?= $cobranza->venta_id ?>">
                        <i class="fa fa-search"></i>
                    </button>
                </td>
            </tr>

        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
<?php endforeach; ?>

<div class="modal fade" id="detalle_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<script type="text/javascript">

    $(document).ready(function () {
//        var first_table_id = $('.table').attr('id');
//        if (first_table_id != undefined && $('#cliente_id').val() != 0){
//            alert(first_table_id)
//            DT.init(first_table_id);
//        }


        $('.show_detalle').on('click', function (e) {
            e.preventDefault();

            $.ajax({
                url: '<?=base_url("reporte_modals/detalle_nota_entrega")?>/' + $(this).attr('data-id'),
                type: 'GET',
                success: function (data) {
                    $('#detalle_modal').html(data);
                    $('#detalle_modal').modal('show');
                }
            })
        });

        $(".pagar_pedido").on('click', function () {
            var id = $(this).attr('data-pedido_id');

            $.ajax({
                url: '<?php echo base_url('pago_pendiente/pagar_nota_pedido')?>' + '/' + id,
                type: 'post',
                success: function (data) {
                    $("#dialog_pagar").html(data);
                    $("#dialog_pagar").modal('show');
                }
            });

        });

        $(".ver_pagos").on('click', function () {
            var id = $(this).attr('data-pedido_id');

            $.ajax({
                url: '<?php echo base_url('pago_pendiente/ver_pagos')?>' + '/' + id,
                type: 'post',
                success: function (data) {
                    $("#dialog_pagar").html(data);
                    $("#dialog_pagar").modal('show');
                }
            });

        });

        $(".pagar_cliente").on('click', function () {
            var id = $(this).attr('data-cliente_id');

            $.ajax({
                url: '<?php echo base_url('pago_pendiente/pagar_cliente')?>' + '/' + id,
                type: 'post',
                success: function (data) {
                    $("#dialog_pagar").html(data);
                    $("#dialog_pagar").modal('show');
                }
            });

        });

        $(".luiquidar_pago").on('click', function () {
            var id = $(this).attr('data-vendedor_id');

            $.ajax({
                url: '<?php echo base_url('pago_pendiente/liquidar_pago')?>' + '/' + id,
                type: 'post',
                success: function (data) {
                    $("#dialog_pagar").html(data);
                    $("#dialog_pagar").modal('show');
                }
            });

        });
    });
</script>

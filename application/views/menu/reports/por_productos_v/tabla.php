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

<?php if (count($productos_list) == 0) echo '<h3>No hay resultados para mostrar.</h3>'; else { ?>

    <table class="table table-condensed table-bordered">
        <tbody>
        <tr>
            <th>Fecha Venta</th>
            <th>Doc. Fiscal</th>
            <th>NE - Numero</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Unit.</th>
            <th>Costo Unit.</th>
            <th>Importe</th>
            <th>Bono</th>
            <th>Estado</th>
        </tr>
        <?php $total_cantidad = 0; ?>
        <?php $total_importe = 0; ?>
        <?php $p_precio = 0; ?>
        <?php $p_precio_valor = 0; ?>
        <?php $count = 0; ?>
        <?php foreach ($productos_list as $p): ?>
            <tr>
                <td><?= date('d/m/Y H:i:s', strtotime($p->fecha)) ?></td>
                <?php
                $doc = 'BO';
                if ($p->doc_fiscal == 'FACTURA')
                    $doc = 'FA'; ?>
                <td><?= $doc ?></td>
                <td><?= 'NE ' . $p->serie . ' - ' . $p->numero ?></td>
                <td><?= sumCod($p->producto_id, 4) ?></td>
                <?php $total_cantidad += $p->cantidad ?>
                <td><?= $p->cantidad ?></td>
                <td><?= MONEDA ?> <?= $p->precio_unitario ?></td>
                <?php $p_precio_valor += $p->costo_unitario ?>
                <td><?= MONEDA ?> <?= number_format($p->costo_unitario * 1.18, 2) ?></td>
                <?php $total_importe += $p->precio_unitario * $p->cantidad ?>
                <td><?= MONEDA ?> <?= $p->precio_unitario * $p->cantidad ?></td>
                <td><?= $p->bono == '1' ? 'SI' : 'NO' ?></td>
                <td><?= $p->estado ?></td>
            </tr>
        <?php endforeach; ?>
        <tr style="font-weight: bold;">
            <td colspan="2"></td>
            <td><?= count($productos_list) ?></td>
            <td colspan=""></td>
            <td><?= $total_cantidad ?></td>
            <td><?= MONEDA ?> <?= number_format($total_importe / $total_cantidad, 2) ?></td>
            <td><?= MONEDA ?> <?= number_format($productos_list[count($productos_list) - 1]->costo_unitario * 1.18, 2) ?></td>
            <td><?= MONEDA ?> <?= $total_importe ?></td>
            <td colspan="2" style="text-align: right;">
                <?= MONEDA ?> <?= number_format($total_importe / $total_cantidad, 2) - number_format($productos_list[count($productos_list) - 1]->costo_unitario * 1.18, 2) ?>
            </td>
        </tr>
        <tr style="font-weight: bold;">
            <th colspan="2"></th>
            <th>Total Ventas</th>
            <th colspan=""></th>
            <th>Total Cantidad</th>
            <th>Precio Promedio</th>
            <th>Costo Promedio</th>
            <th>Total Importe</th>
            <th colspan="2" style="text-align: right;">Margen Unitario</th>
        </tr>
        </tbody>
    </table>

    <script>
        $(document).ready(function () {

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

            $("#total_venta").html($("#input_venta").val());
            $("#total_pago").html($("#input_pago").val());
            $("#total_saldo").html($("#input_saldo").val());
        });
    </script>
<?php } ?>
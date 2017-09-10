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
    <div style="text-align: right; color: #2F3C53; font-size: 1.1rem;">Nota: Totales de cantidad (Total | Total vendido
        | Total Bonificado)
    </div>
    <table class="table table-condensed table-bordered">
        <tbody>
        <tr>
            <th>Fecha Venta</th>
            <th>Doc. Fiscal</th>
            <th>NE - Numero</th>
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
        <?php $p_selected = false; ?>
        <?php foreach ($productos_list as $p): ?>

            <?php if ($p_selected != $p->producto_id): ?>
                <tr style="font-weight: bold;">
                    <td colspan="2"
                    ><?= sumCod($p->producto_id, 4) . ' - ' . $p->nombre . ' ' . $p->presentacion ?></td>


                    <?php $total_cantidad = 0; ?>
                    <?php $total_cantidad_ok = 0; ?>
                    <?php $total_cantidad_bono = 0; ?>
                    <?php $total_importe = 0; ?>
                    <?php $p_precio = 0; ?>
                    <?php $p_precio_valor = 0; ?>
                    <?php $costo = 0; ?>
                    <?php $count = 0; ?>

                    <?php foreach ($productos_list as $p_temp): ?>
                        <?php if ($p_temp->producto_id == $p->producto_id): ?>
                            <?php $total_cantidad += $p_temp->cantidad ?>
                            <?php if ($p_temp->bono != '1'): ?>
                                <?php $total_cantidad_ok += $p_temp->cantidad ?>
                            <?php else: ?>
                                <?php $total_cantidad_bono += $p_temp->cantidad ?>
                            <?php endif; ?>
                            <?php $p_precio_valor += $p_temp->costo_unitario ?>
                            <?php $total_importe += $p_temp->precio_unitario * $p_temp->cantidad ?>
                            <?php $costo = $p_temp->costo_unitario; ?>
                            <?php $count++; ?>
                        <?php endif; ?>
                    <? endforeach; ?>
                    <td><?= $count ?> ventas</td>
                    <td><?= $total_cantidad . " | " . $total_cantidad_ok . " | " . $total_cantidad_bono ?></td>
                    <td><?= MONEDA ?> <?= number_format($total_importe / $total_cantidad, 2) ?></td>
                    <td><?= MONEDA ?> <?= number_format($costo * 1.18, 2) ?></td>
                    <td><?= MONEDA ?> <?= number_format($total_importe, 2) ?></td>
                    <td colspan="2" style="text-align: right;">
                        Margen
                        Unitario:
                        <?php $margen = number_format($total_importe / $total_cantidad, 2) - number_format($costo * 1.18, 2); ?>
                        <?= $margen > 0 ? '<span style="color: #1bb52a;">' . MONEDA . ' ' . number_format($margen, 2) . '</span>' : '<span style="color: #CC0000;">' . MONEDA . ' ' . number_format($margen, 2) . '</span>' ?>
                    </td>
                </tr>
            <?php endif; ?>

            <?php $p_selected = $p->producto_id; ?>

            <tr>
                <td><?= date('d/m/Y H:i:s', strtotime($p->fecha)) ?></td>
                <?php
                $doc = 'BO';
                if ($p->doc_fiscal == 'FACTURA')
                    $doc = 'FA'; ?>
                <td><?= $doc ?></td>
                <td><?= 'NE ' . $p->serie . ' - ' . $p->numero ?></td>
                <td><?= $p->cantidad ?></td>
                <td><?= MONEDA ?> <?= $p->precio_unitario ?></td>
                <td><?= MONEDA ?> <?= number_format($p->costo_unitario * 1.18, 2) ?></td>
                <td><?= MONEDA ?> <?= $p->precio_unitario * $p->cantidad ?></td>
                <td><?= $p->bono == '1' ? 'SI' : 'NO' ?></td>
                <td><?= $p->estado ?></td>
            </tr>
        <?php endforeach; ?>

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
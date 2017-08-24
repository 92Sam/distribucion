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
            <th>Registro</th>
            <th>Emision</th>
            <th>Documento</th>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio Compra</th>
            <th>Precio Valor</th>
            <th>Importe</th>
            <th>Estado</th>
        </tr>
        <?php $total_cantidad = 0; ?>
        <?php $total_importe = 0; ?>
        <?php $p_precio = 0; ?>
        <?php $p_precio_valor = 0; ?>
        <?php foreach ($productos_list as $p): ?>
            <tr>
                <td><?= date('d/m/Y H:i:s', strtotime($p->fecha_registro)) ?></td>
                <td><?= date('d/m/Y', strtotime($p->fecha_emision)) ?></td>
                <?php if ($p->tipo_documento == 'FACTURA')
                    $doc = 'FA ';
                elseif ($p->tipo_documento == 'BOLETA DE VENTA')
                    $doc = 'BO ';
                else $doc = 'NP '; ?>
                <td><?= $doc . $p->documento_serie . ' - ' . $p->documento_numero ?></td>
                <td><?= sumCod($p->producto_id, 4) ?></td>
                <?php $total_cantidad += $p->cantidad ?>
                <td><?= $p->cantidad ?></td>
                <?php $p_precio += $p->precio ?>
                <td><?= MONEDA ?> <?= $p->precio ?></td>
                <?php $p_precio_valor += $p->precio_valor ?>
                <td><?= MONEDA ?> <?= $p->precio_valor ?></td>
                <?php $total_importe += $p->total_detalle ?>
                <td><?= MONEDA ?> <?= $p->total_detalle ?></td>
                <td><?= $p->estado ?></td>
            </tr>
        <?php endforeach; ?>
        <tr style="font-weight: bold;">
            <td colspan="2"></td>
            <td><?= count($productos_list) ?></td>
            <td></td>
            <td><?= $total_cantidad ?></td>
            <td><?= MONEDA ?> <?= number_format($p_precio / count($productos_list), 2) ?></td>
            <td><?= MONEDA ?> <?= number_format($p_precio_valor / count($productos_list), 2) ?></td>
            <td><?= MONEDA ?> <?= $total_importe ?></td>
            <td></td>
        </tr>
        <tr style="font-weight: bold;">
            <th colspan="2"></th>
            <th>Total Compras</th>
            <th></th>
            <th>Total Cantidad</th>
            <th>Precio Promedio</th>
            <th>Precio Valor</th>
            <th>Total Importe</th>
            <th></th>
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
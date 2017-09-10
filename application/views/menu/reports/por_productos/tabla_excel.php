<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=compras_por_productos.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
    <tr>
        <th>Registro</th>
        <th>Emision</th>
        <th>Documento</th>
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
    <?php $p_selected = false; ?>
    <?php foreach ($productos_list as $p): ?>
        <?php if ($p_selected != $p->producto_id): ?>
            <tr style="font-weight: bold;">
                <th colspan="2"
                ><?= sumCod($p->producto_id, 4) . ' - ' . $p->nombre . ' ' . $p->presentacion ?></th>


                <?php $total_cantidad = 0; ?>
                <?php $total_importe = 0; ?>
                <?php $p_precio = 0; ?>
                <?php $p_precio_valor = 0; ?>
                <?php $costo = 0; ?>
                <?php $count = 0; ?>

                <?php foreach ($productos_list as $p_temp): ?>
                    <?php if ($p_temp->producto_id == $p->producto_id): ?>
                        <?php $total_cantidad += $p_temp->cantidad ?>
                        <?php $p_precio += $p->precio ?>
                        <?php $p_precio_valor += $p->precio_valor ?>
                        <?php $total_importe += $p->total_detalle ?>
                        <?php $count++; ?>
                    <?php endif; ?>
                <? endforeach; ?>
                <th><?= $count ?> compras</th>
                <th><?= $total_cantidad ?></th>
                <th><?= MONEDA ?> <?= number_format($p_precio / $count, 2) ?></th>
                <th><?= MONEDA ?> <?= number_format($p_precio_valor / $count, 2) ?></th>
                <th colspan="2"><?= MONEDA ?> <?= $total_importe ?></th>
            </tr>
        <?php endif; ?>

        <?php $p_selected = $p->producto_id; ?>

        <tr>
            <td><?= date('d/m/Y H:i:s', strtotime($p->fecha_registro)) ?></td>
            <td><?= date('d/m/Y', strtotime($p->fecha_emision)) ?></td>
            <?php if ($p->tipo_documento == 'FACTURA')
                $doc = 'FA ';
            elseif ($p->tipo_documento == 'BOLETA DE VENTA')
                $doc = 'BO ';
            else $doc = 'NP '; ?>
            <td><?= $doc . $p->documento_serie . ' - ' . $p->documento_numero ?></td>
            <td><?= $p->cantidad ?></td>
            <td><?= MONEDA ?> <?= $p->precio ?></td>
            <td><?= MONEDA ?> <?= $p->precio_valor ?></td>
            <td><?= MONEDA ?> <?= $p->total_detalle ?></td>
            <td><?= $p->estado ?></td>
        </tr>
    <?php endforeach; ?>
</table>


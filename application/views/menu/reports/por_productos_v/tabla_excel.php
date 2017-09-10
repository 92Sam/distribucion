<?php
header("Content-type: application/octet-stream");
header("Content-Disposition: attachment; filename=venta_por_productos.xls");
header("Pragma: no-cache");
header("Expires: 0");
?>
<table>
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
                <th colspan="2"
                ><?= sumCod($p->producto_id, 4) . ' - ' . $p->nombre . ' ' . $p->presentacion ?></th>


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
                <th><?= $count ?> ventas</th>
                <th><?= $total_cantidad ?></th>
                <th><?= MONEDA ?> <?= number_format($total_importe / $total_cantidad, 2) ?></th>
                <th><?= MONEDA ?> <?= number_format($costo * 1.18, 2) ?></th>
                <th><?= MONEDA ?> <?= number_format($total_importe, 2) ?></th>
                <th>Margen Unitario</th>
                <th>
                    <?php $margen = number_format($total_importe / $total_cantidad, 2) - number_format($costo * 1.18, 2); ?>
                    <?= $margen ?>
                </th>
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
</table>


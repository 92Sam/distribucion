<style type="text/css">
    table {
        width: 100%;
    }
    table td {
        border: #e1e1e1 1px solid;
        font-size: 9px;
    }

    table th {
        background: #585858;
        border: #111 1px solid;
        color: #fff;
        font-size: 10px;
    }

</style>

<table cellpadding="3" cellspacing="0">
    <tr style="background: #585858;">
        <?php foreach ($columnas as $col): ?>
            <?php if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_activo'): ?>
                <?php if ($col->nombre_mostrar == "Sub Grupo"): ?>
                    <td style="color: #fff;">Linea</td>
                <?php elseif ($col->nombre_mostrar == 'Familia'): ?>
                    <td style="color: #fff;">Sub Linea</td>
                <?php elseif ($col->nombre_mostrar == 'Linea'): ?>
                    <td style="color: #fff;">Talla</td>
                <?php else: ?>
                    <td style="color: #fff;"><?= $col->nombre_mostrar ?></td>
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
        <td style="color: #fff;">UM</td>
        <td style="color: #fff;">Cantidad</td>
    </tr>
    <?php foreach ($lstProducto as $producto): ?>
        <tr>
            <?php foreach ($columnas as $col): ?>
                <?php if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_activo'): ?>
                    <?php if ($col->nombre_columna == 'producto_id'): ?>
                        <td><?= sumCod($producto['producto_id']) ?></td>
                    <?php else: ?>
                        <td><?= $producto[$col->nombre_join] ?></td>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endforeach; ?>

            <td><?= $producto['nombre_unidad'] ?></td>
            <td><?= $producto['cantidad'] ?></td>
        </tr>
    <?php endforeach; ?>
</table>
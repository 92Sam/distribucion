<style>
    .tabla_detalles {
        display: <?=$mostrar_detalles == 1? 'block-inline;': 'none;'?>
    }

    table th {
        background-color: #f4f4f4;
    }
</style>


<table class="table table-bordered">
    <tr>
        <th rowspan="2" style="vertical-align: middle;">C&oacute;digo</th>
        <th rowspan="2" style="vertical-align: middle;">Producto</th>
        <th rowspan="2" style="vertical-align: middle;">Presentaci&oacute;n</th>
        <th colspan="3" style="text-align: center;">Stock</th>
    </tr>
    <tr>
        <th>Tr&aacute;nsito</th>
        <th>Liquidado</th>
        <th>Comprometido</th>
    </tr>
    <?php foreach ($stocks as $stock): ?>
        <tr>
            <td><?= sumCod($stock->producto_id, 6) ?></td>
            <td><?= $stock->producto_nombre ?></td>
            <td><?= $stock->presentacion != '' ? $stock->presentacion : $stock->unidad_nombre ?></td>
            <td><?= $stock->stock ?></td>
            <td><?= $stock->liquidado ?></td>
            <td><?= $stock->stock - $stock->liquidado ?></td>
        </tr>
    <?php endforeach; ?>
</table>

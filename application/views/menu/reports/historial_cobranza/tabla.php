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
        <th>Documento (Fecha)</th>
        <th>Cliente</th>
        <th>Fecha</th>
        <th>Tipo de Pago</th>
        <th>Monto</th>
        <th>Zona</th>
        <th>Vendedor</th>
    </tr>
    <?php $total = 0; ?>
    <?php foreach ($ventas as $v): ?>
        <tr>
            <td><?= $v->venta . ' (' . date('d/m/Y', strtotime($v->fecha_venta)) . ')' ?></td>
            <td><?= $v->cliente ?></td>
            <td><?= date('d/m/Y', strtotime($v->fecha)) ?></td>
            <td><?= $v->tipo_pago ?></td>
            <?php $total += $v->monto ?>
            <td><?= MONEDA . ' ' . number_format($v->monto, 2) ?></td>
            <td><?= $v->zona ?></td>
            <td><?= $v->vendedor ?></td>
        </tr>
    <?php endforeach; ?>
</table>
<div class="row">
    <div class="col-md-12" style="text-align: right">
        <h4>Total: <?= MONEDA ?> <?= number_format($total, 2) ?></h4>
    </div>
</div>
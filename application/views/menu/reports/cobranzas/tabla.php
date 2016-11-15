<table class="table table-striped dataTable table-bordered">
    <thead>
    <tr>
        <th>Documento</th>
        <th>Numero Documento</th>
        <th>Cliente</th>
        <th>Fecha Documento</th>
        <th>Fecha de Venta</th>
        <th>Total deuda</th>
        <th>Actual</th>
        <th>Saldo</th>
        <th>Zona</th>
        <th>Vendedor</th>
        <th>Días mora</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($cobranzas as $cobranza): ?>
        <tr>
            <td><?= $cobranza->documento_nombre == 'NOTA DE ENTREGA' ? 'NE' : $cobranza->documento_nombre ?></td>
            <td><?= $cobranza->documento_serie . '-' . $cobranza->documento_numero ?></td>
            <td><?= $cobranza->cliente_nombre ?></td>
            <td><?= $cobranza->fecha_documento ?></td>
            <td><?= $cobranza->fecha_venta ?></td>
            <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda, 2) ?></td>
            <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda, 2) ?></td>
            <td><?= MONEDA . ' ' . number_format($cobranza->total_deuda, 2) ?></td>
            <td><?= $cobranza->cliente_zona_nombre ?></td>
            <td><?= $cobranza->vendedor_nombre ?></td>
            <td><?= 0 ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
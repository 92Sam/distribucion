<style>

    table th {
        background-color: #f4f4f4;
    }
</style>
<div class="table-responsive">
    <table class="table table-bordered">
        <tr>
            <th>Fecha</th>
            <th>Documento</th>
            <th>RUC/DNI</th>
            <th>Cliente</th>
            <th>Vendedor</th>
            <th>Zona</th>
            <th>Condicion</th>
            <th>Estado</th>
            <th>Criterio</th>
            <th>Subtotal</th>
            <th>IGV</th>
            <th>Total</th>
        </tr>
        <?php $total = 0; ?>
        <?php foreach ($ventas as $venta): ?>
            <tr>
                <td><?= date('d/m/Y', strtotime($venta->fecha)) ?></td>
                <?php
                $doc = "NP";
                if ($venta->documento == "BOLETA DE VENTA")
                    $doc = "BO";
                if ($venta->documento == "FACTURA")
                    $doc = "FA";
                ?>
                <td><?= $doc ?> <?= $venta->documento_numero ?></td>
                <td><?= $venta->ruc_dni ?></td>
                <td><?= $venta->razon_social ?></td>
                <td><?= $venta->vendedor ?></td>
                <td><?= $venta->zona ?></td>
                <td><?= $venta->condicion ?></td>
                <td><?= $venta->estado ?></td>
                <td><?= $venta->criterio ?></td>
                <td><?= MONEDA ?> <?= number_format($venta->subtotal, 2) ?></td>
                <td><?= MONEDA ?> <?= number_format($venta->igv, 2) ?></td>
                <?php $total += $venta->total; ?>
                <td> <?= number_format($venta->total, 2) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<div class="row">
    <div class="col-md-12" style="text-align: right">
        <h4>Total: <?= MONEDA ?> <?= number_format($total, 2) ?></h4>
    </div>
</div>

<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<script>

    function ver(id) {
        $("#ver").load('<?= base_url()?>reporte/nota_entrega_form/' + id);
        $('#ver').modal('show');

    }
</script>
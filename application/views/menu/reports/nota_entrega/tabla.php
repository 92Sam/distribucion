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
            <th>Estado Imp.</th>
            <th>Estado NE</th>
            <th>Total</th>
            <th>Documentos</th>
        </tr>
        <?php $total = 0; ?>
        <?php foreach ($ventas as $venta): ?>
            <?php $text = 'class="label-danger" style="color: #FFFFFF;"';?>
            <tr <?= $venta->estado_ne == 'RECHAZADO' || $venta->estado_ne == 'ANULADO' ? $text : ''?>>
                <td><?= date('d/m/Y', strtotime($venta->fecha)) ?></td>
                <td><?= $venta->documento ?></td>
                <td><?= $venta->ruc_dni ?></td>
                <td><?= $venta->razon_social ?></td>
                <td><?= $venta->vendedor ?></td>
                <td><?= $venta->zona ?></td>
                <td><?= $venta->condicion ?></td>
                <td><?= $venta->estado ?></td>
                <td><?= $venta->estado_ne ?></td>
                <?php $total += $venta->estado_ne == 'RECHAZADO' || $venta->estado_ne == 'ANULADO' ? 0 : $venta->total; ?>
                <td><?= MONEDA ?> <?= $venta->total ?></td>
                <td>
                    <a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                       title="Ver" data-original-title="Ver"
                       href="#" onclick="ver(<?= $venta->venta_id ?>);">
                        <i class="fa fa-search"></i>
                    </a>
                </td>
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
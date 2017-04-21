<style>

    table th {
        background-color: #f4f4f4;
    }
</style>

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
        <th>Total</th>
        <th>Documentos</th>
    </tr>
    <?php foreach ($ventas as $venta): ?>
        <tr>
            <td><?=date('d/m/Y', strtotime($venta->fecha))?></td>
            <td><?=$venta->documento?></td>
            <td><?=$venta->ruc_dni?></td>
            <td><?=$venta->razon_social?></td>
            <td><?=$venta->vendedor?></td>
            <td><?=$venta->zona?></td>
            <td><?=$venta->condicion?></td>
            <td><?=$venta->estado?></td>
            <td><?=MONEDA?> <?=$venta->total?></td>
            <td>
                <a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                   title="Ver" data-original-title="Ver"
                   href="#" onclick="ver(<?=$venta->venta_id?>);">
                    <i class="fa fa-search"></i>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<script>

    function ver(id) {
        $("#ver").load('<?= base_url()?>reporte/nota_entrega_form/' + id);
        $('#ver').modal('show');

    }
</script>
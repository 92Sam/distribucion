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
        <th></th>
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
            <td style="white-space: nowrap">
                <a href="#" class="btn btn-default show_detalle" data-id="<?= $v->venta_id ?>">
                    <i class="fa fa-search"></i>
                </a>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
<div class="row">
    <div class="col-md-12" style="text-align: right">
        <h4>Total: <?= MONEDA ?> <?= number_format($total, 2) ?></h4>
    </div>
</div>

<script>
    $(function(){
        $('.show_detalle').on('click', function(e){
            e.preventDefault();

            $.ajax({
                url: '<?=base_url("reporte_modals/detalle_nota_entrega")?>/' + $(this).attr('data-id'),
                type: 'GET',
                success: function(data){
                    $('#detalle_modal').html(data);
                    $('#detalle_modal').modal('show');
                }
            })
        });
    });
</script>
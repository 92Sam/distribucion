<?php $ruta = base_url(); ?>
<style>
    #tablaresult th {
        font-size: 11px !important;
        padding: 6px 2px;
        text-align: center;
        vertical-align: middle;
    }

    #tablaresult td {
        font-size: 10px !important;
    }
</style>

<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered' id="tablaresult" name="tablaresult">
        <thead>
        <tr>
            <th>ID</th>
            <th>Tipo</th>
            <th>Documento</th>
            <th>Proveedor</th>
            <th>Fecha Compra</th>
            <th>Monto Venta <?= MONEDA ?></th>
            <th>Monto Pagado <?= MONEDA ?></th>
            <th>Saldo Deuda<?= MONEDA ?></th>
            <th>Días de atraso</th>
            <th>Accion</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($lstproveedor as $p): ?>
            <tr>
                <td><?= $p->ingreso_id ?></td>
                <td><?= $p->documento_nombre ?></td>
                <td><?= $p->documento_serie . ' - ' . $p->documento_numero ?></td>
                <td><?= $p->proveedor_nombre ?></td>
                <td><?= date('d/m/Y', strtotime($p->fecha_emision)) ?></td>
                <td><?= number_format($p->monto_venta, 2) ?></td>
                <td><?= number_format($p->monto_pagado, 2) ?></td>
                <td><?= number_format($p->monto_venta - $p->monto_pagado, 2) ?></td>
                <td><?= $p->atraso ?></td>
                <td>
                    <a onclick="ver_detalle_pago('<?= $p->ingreso_id ?>')" class="btn btn-default tip" title="Ver"><i
                                class="fa fa-search"></i> Ver</a>

                    <a onclick="pagar_venta('<?= $p->ingreso_id ?>')" class="btn btn-default tip" title="Pagar"><i
                                class="fa fa-paypal"></i> Pagar</a>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Seccion Visualizar -->
<div class="modal fade" id="visualizar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>


<!-- Pagar Visualizar -->
<div class="modal fade" id="pagar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<script type="text/javascript">
    $(document).ready(function () {
        TablesDatatables.init(4);
    });


    function pagar_venta(id) {

        $.ajax({
            url: '<?= base_url()?>ingresos/ver_deuda',
            type: 'post',
            data: {'id_ingreso': id},
            success: function (data) {

                $("#pagar_venta").html(data);
                $('#pagar_venta').modal('show');
            }

        })

    }

    function ver_detalle_pago(id) {

        $.ajax({
            url: '<?= base_url()?>proveedor/ingreso_pago_detalles',
            type: 'post',
            data: {'id_ingreso': id},
            success: function (data) {

                $("#visualizar_venta").html(data);
                $('#visualizar_venta').modal('show');
            }

        })

    }

    function cerrar_visualizar() {

        $('#visualizarPago').modal('hide');
        $('#pagar_venta').modal('hide');
        buscar();
    }
    function visualizar(id) {

        $.ajax({
            url: '<?= base_url()?>ingresos/vertodoingreso',
            type: 'post',
            data: {'id_ingreso': id},
            success: function (data) {

                $("#visualizar_venta").html(data);
                $('#visualizar_venta').modal('show');
            }

        })
    }
</script>
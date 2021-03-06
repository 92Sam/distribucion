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
            <th>Estado NE</th>
            <th>Total</th>
            <th>Acciones</th>
        </tr>
        <?php $total = 0; ?>
        <?php foreach ($ventas as $venta): ?>
            <?php $text = 'class="label-danger" style="color: #FFFFFF;"'; ?>
            <tr <?= $venta->estado_ne == 'RECHAZADO' || $venta->estado_ne == 'ANULADO' ? $text : '' ?>>
                <td><?= date('d/m/Y', strtotime($venta->fecha)) ?></td>
                <td><?= $venta->documento ?></td>
                <td><?= $venta->ruc_dni ?></td>
                <td><?= $venta->razon_social ?></td>
                <td><?= $venta->vendedor ?></td>
                <td><?= $venta->zona ?></td>
                <td><?= $venta->estado_ne ?></td>
                <?php $total += $venta->estado_ne == 'RECHAZADO' || $venta->estado_ne == 'ANULADO' ? 0 : $venta->total; ?>
                <td><?= MONEDA ?> <?= $venta->total ?></td>
                <td>
                    <a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                       title="Detalles" data-original-title="Ver"
                       href="#" onclick="detalle(<?= $venta->venta_id ?>);">
                        <i class="fa fa-search"></i>
                    </a>

                    <a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                       title="Documentos" data-original-title="Ver"
                       href="#" onclick="ver(<?= $venta->venta_id ?>);">
                        <i class="fa fa-file"></i>
                    </a>

                    <a class="btn btn-danger" data-toggle="tooltip"
                       title="Rechazar" data-original-title="Ver"
                       href="#" onclick="rechazar(<?= $venta->venta_id ?>, '<?= $venta->documento ?>');">
                        <i class="fa fa-remove"></i>
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

<div class="modal fade" id="detalle_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="confirmar_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmaci&oacute;n de <strong id="ne_tittulo"></strong></h4>
            </div>
            <div class="modal-body">
                <input type="hidden" id="ne_id">
                <h3>¿Estas seguro de devolver esta nota de entrega?</h3>

                <h4>Documentos Asociados</h4>
                <div id="docs_asociados" style="padding-left: 25px;padding-right: 25px;">

                </div>
                <br>
                <h4>Numero de la Nota de Credito</h4>
                <div class="row">
                    <div class="col-md-2"></div>
                    <div class="col-md-3">
                        <input type="text" id="nc_serie" class="form-control" disabled>
                    </div>
                    <div class="col-md-5">
                        <input type="text" id="nc_numero" class="form-control" disabled>
                    </div>
                    <div class="col-md-2"></div>
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-default" id="confirmar_b" onclick="rechazar_exec()">Confirmar
                </button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<script>

    function detalle(id) {
        $("#detalle_modal").load('<?= base_url()?>reporte_modals/detalle_nota_entrega/' + id);
        $('#detalle_modal').modal('show');

    }

    function ver(id) {
        $("#ver").load('<?= base_url()?>reporte/nota_entrega_form/' + id);
        $('#ver').modal('show');

    }

    function rechazar(id, ne) {
        $('#ne_id').val(id);
        $('#ne_tittulo').html(ne);
        $("#barloadermodal").modal('show');

        $.ajax({
            url: '<?=base_url("venta/devolver_detalles")?>/' + id,
            type: 'GET',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                $('#docs_asociados').html('');
                for (var i = 0; i < data.docs_fiscales.length; i++) {
                    var doc = "";
                    if (data.docs_fiscales[i].documento_tipo == 'FACTURA')
                        doc += "FA ";
                    else
                        doc += "BO ";

                    doc += data.docs_fiscales[i].documento_serie + "-" + data.docs_fiscales[i].documento_numero;
                    var template = '<input type="text" value="' + doc + '" disabled> ';
                    $('#docs_asociados').append(template);
                }

                $('#nc_serie').val(sumCod(data.serie, 4));
                $('#nc_numero').val(sumCod(data.numero, 5));
                $('#confirmar_modal').modal('show');
            },
            complete: function (data) {
                $("#barloadermodal").modal('hide');
            }
        });

    }

    function rechazar_exec() {
        var id = $('#ne_id').val();

        $("#barloadermodal").modal('show');
        $('#confirmar_b').attr('disabled', 'disabled');

        $.ajax({
            url: '<?=base_url("venta/rechazar_exec")?>/' + id,
            type: 'GET',
            success: function (data) {
                show_msg('success', 'La ' + $('#ne_tittulo').html() + ' ha sido devuelta.');

                $('#confirmar_modal').modal('hide');
                filter_cobranzas(id);
            },
            complete: function (data) {
                $("#barloadermodal").modal('hide');
                $('#confirmar_b').removeAttribute('disabled');
            }
        })
    }
</script>
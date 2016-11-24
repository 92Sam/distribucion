<style>

    #tabla_resumen_productos thead tr {
        border-top: 1px #000 dashed;
        border-bottom: 1px #000 dashed;
    }

    #tabla_resumen_productos thead tr th {
        border-top: 1px #000 dashed;
        border-bottom: 1px #000 dashed;
    }

    #tabla_resumen_productos tbody tr td {
        border-top: 0px #000 dashed;
        border-bottom: 0px #000 dashed;
        font-size: 85%;
    }

    #panel_documento {
        font-size: 90%;
    }

    #tabla_resumen_productos thead tr th {
        font-size: 85%;
    }
</style>

<?php $ruta = base_url(); ?>
<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Hoja de Liquidaci&oacute;n</h4>
        </div>
        <div class="modal-body">
            <? $totalCount = 0; ?>
            <? $num = 1; ?>
            <? $fecha = date('d-m-Y H:m:s'); ?>
            <? $vend = $vendedor['nombre'];?>
            <table class="table table-striped dataTable table-bordered">
                <thead>
                <tr>
                    <th>No.</th>
                    <th>N&uacute;mero Documento</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>M&eacute;todo</th>
                    <th>Fecha</th>
                    <th>Monto</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($resultado as $res): ?>
                    <tr>
                        <td><?= $num++; ?></td>
                        <td><?= $res['documento_Serie'] . "-" . $res['documento_Numero']; ?></td>
                        <td><?= $res['razon_social']; ?></td>
                        <td><?= $vend; ?></td>
                        <td><?= $res['tipo_metodo']; ?></td>
                        <td><?= $fecha; ?></td>
                        <td><?= $res['historial_monto']; ?></td>
                        <? $totalCount += floatval($res['historial_monto']); ?>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><strong><?= 'TOTAL: ' . MONEDA . $totalCount; ?></strong></td>
                </tr>
                <tr>
                    <td><strong><?= 'CHEQUE: '; ?></strong></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                </tr>
                <tr>
                    <td><strong><?= 'DEPOSITO BBVA: '; ?></strong></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                </tr>
                <tr>
                    <td><strong><?= 'DEPOSITO BCP: '; ?></strong></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                </tr>
                <tr>
                    <td><strong><?= 'DEPOSITO V&M: '; ?></strong></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                </tr>
                <tr>
                    <td><strong><?= 'BILLETE: '; ?></strong></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                </tr>
                <tr>
                    <td><strong><?= 'MONEDA: '; ?></strong></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                </tr>
                <tr>
                    <td><strong><?= 'TOTAL: '; ?></strong></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                    <td><?= ''?></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="row">
            <div class="col-md-12"  align="left" style="padding-left:40px;">
                <a target="_blank" href="<?= $ruta?>venta/pdfResumenLiquidacion/<?php echo $id_vend;?>/<?php echo $vend;?>/<?php echo $liquidacion;?>/<?php echo $fecha;?>/<?php echo $totalCount;?>/" class="btn btn-default" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-default" id="cerrar_visualizar" onclick="cerrar()">Cerrar</a>
            <a href="#" tabindex="0" type="button" id="imprimir" class="btn btn-primary"> <i class="fa fa-print"></i>Imprimir</a>
        </div>
    </div>

</div>

<script src="<?php echo base_url() ?>recursos/js/printThis.js"></script>
<script>
    $(function () {


        $("#imprimir").click(function (e) {
            e.preventDefault();
            $("#resumen_venta").printThis({
                importCSS: true,
                loadCSS: "<?= base_url()?>recursos/css/carta.css"
            });
        });

        setTimeout(function () {
            $("#imprimir").focus();
        }, 500);
    })
</script>
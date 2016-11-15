<?php $ruta = base_url(); ?>

<form name="form" method="post" id="form" action="<?= $ruta ?>venta/guardar_liquidar">


    <table class='table table-striped table-condensed table-responsive dataTable table-bordered no-footer' id="lstPagP"
           name="lstPagP">
        <thead>
        <tr>

            <th>Fecha de Liquidaci&oacute;n</th>
            <th>Cajero</th>
            <th>Vendedor</th>
            <th>Total Liquidaci&oacute;n</th>
            <th>Editar</th>

        </tr>
        </thead>
        <tbody>
        <?php if (count($lstVenta) > 0): ?>
            <?php foreach ($lstVenta as $v): ?>
                <tr>

                    <td><span
                            style="display:
                        none"><?= date('YmdHis', strtotime($v['liquidacion_fecha'])) ?></span><?php echo date("d-m-Y H:i:s", strtotime($v['liquidacion_fecha'])) ?>
                    </td>
                    <td><?php echo $v['cajero']; ?></td>
                    <td><?php echo $v['nombre']; ?></td>
                    <td><?php echo MONEDA.' '.$v['suma']; ?></td>

                    <td>
                        <div class="btn-group" align="center">
                            <button type="button"
                                    onclick="imprimir(<?= $v['historial_id'] ?>,<?= $v['credito_id'] ?>,<?= $v['liquidacion_id'] ?>)"
                                    class='btn btn-primary'>Impimir
                            </button>
                        </div>
                    </td>

                </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Seccion Visualizar -->

</form>
<!--- ----------------- -->

<div class="modal fade" id="visualizarliquidacion" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>
<script src="<?php echo $ruta; ?>recursos/js/Validacion.js?<?php echo date('Hms'); ?>"></script>

<script type="text/javascript">


    $(document).ready(function () {


        TablesDatatables.init(0, "lstPagP");

    });


</script>
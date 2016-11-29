<?php $ruta = base_url(); ?>
<div class="modal-dialog modal-lg">
    <form name="formcerrarliquidacion" method="post" id="formcerrarliquidacion"
          action="<?= base_url() ?>consolidadodecargas/cerrarLiquidacion">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Liquidación de guia de carga</h4>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-striped dataTable table-bordered" id="example">
                        <thead>
                        <tr>

                            <th style="text-align: center">ID</th>
                            <th style="text-align: center">Documento</th>
                            <th style="text-align: center">Bultos</th>
                            <th style="text-align: center">Cliente</th>
                            <th style="text-align: center">Importe Pedido</th>
                            <th style="text-align: center">Estado</th>
                            <th style="text-align: center">Acciones</th>
                            <th style="text-align: center">Monto Cobrado</th>

                        </tr>
                        </thead>
                        <tbody>

                        <?php

                        $liquidar = true;
                        $s = 0;
                        $total=0;
                        foreach ($consolidado as $consolidadoDetalles) {

                            if ($consolidadoDetalles['montocobradoliquidacion'] == null) {
                                $consolidadoDetalles['montocobradoliquidacion'] = 0;
                            }
                            $total =$total+ $consolidadoDetalles['montocobradoliquidacion'];
                                $color = 'b-default';

                            if($consolidadoDetalles['venta_status'] == 'ENTREGADO')
                                $color = 'b-primary';
                            elseif($consolidadoDetalles['venta_status'] == 'DEVUELTO PARCIALMENTE')
                                $color = 'b-other';
                            elseif($consolidadoDetalles['venta_status'] == 'RECHAZADO')
                                $color = 'b-warning';
                            ?>
                            <tr>
                                <td style="text-align: center"><?php echo $consolidadoDetalles['venta_id']; ?></td>
                                <td style="text-align: center"><?php echo 'NE - '.$consolidadoDetalles['documento_Numero']; ?></td>
                                <td style="text-align: center"><?php echo number_format($consolidadoDetalles['bulto'],0) ?></td>
                                <td style="text-align: center"><?php echo $consolidadoDetalles['razon_social']; ?></td>
                                <td style="text-align: right"><?php echo MONEDA.' '.number_format($consolidadoDetalles['total'],2) ?></td>

                                <td style="text-align: center"><?php echo $consolidadoDetalles['venta_status']; ?></td>
                                <td style="text-align: center">
                                    <?php
                                    if (($status != 'CERRADO'&& $status != 'CONFIRMADO') && $consolidadoDetalles['venta_status'] == PEDIDO_ENVIADO) {
                                        $liquidar = false;
                                    }
                                    //var_dump($consolidadoDetalles);
                                    if (($status != 'CERRADO'&& $status != 'CONFIRMADO') && $consolidadoDetalles['venta_status'] == PEDIDO_ENVIADO && (($consolidadoDetalles['confirmacion_usuario'] != '' && floatval($consolidadoDetalles['pagado']) > 0) || ($consolidadoDetalles['confirmacion_usuario'] == '' && floatval($consolidadoDetalles['pagado']) <= 0))) {


                                        ?>
                                        <button type="button" id="liquidar"
                                                onclick="liquidarPedido(<?php echo $consolidadoDetalles['pedido_id'] ?>, <?php echo $consolidadoDetalles['pagado'] ?>, <?php echo $consolidadoDetalles['total'] ?>,<?php echo $consolidadoDetalles['consolidado_id'] ?>,'<?= $consolidadoDetalles['venta_status'] ?>',<?= $consolidadoDetalles['montocobradoliquidacion'] ?>,<?= $consolidadoDetalles['totalbackup'] ?>);"
                                                class="btn btn-default"><i class="fa fa-refresh"></i>
                                            Liquidar
                                        </button>
                                    <?php }

                                    if ($consolidadoDetalles['confirmacion_usuario'] == '' && floatval($consolidadoDetalles['pagado']) > 0) {
                                        ?>
                                        <label class="label label-danger">Debe Confirmar Pago Adelantado</label>
                                        <?php
                                    }

                                    if (($status != 'CERRADO'&& $status != 'CONFIRMADO')) {

                                        if ($consolidadoDetalles['venta_status'] == PEDIDO_RECHAZADO ||
                                            $consolidadoDetalles['venta_status'] == PEDIDO_ENTREGADO ||
                                            $consolidadoDetalles['venta_status'] == PEDIDO_DEVUELTO
                                        ) {


                                            ?>
                                            <button type="button" id="liquidar"
                                                    onclick="liquidarPedido(<?php echo $consolidadoDetalles['pedido_id'] ?>, <?php echo $consolidadoDetalles['pagado'] ?>, <?php echo $consolidadoDetalles['total'] ?>,<?php echo $consolidadoDetalles['consolidado_id'] ?>,'<?= $consolidadoDetalles['venta_status'] ?>',<?= $consolidadoDetalles['montocobradoliquidacion'] ?>,<?= $consolidadoDetalles['totalbackup'] ?>);"
                                                    class="btn btn-primary"><i class="fa fa-refresh"></i>
                                                Cambiar
                                            </button>

                                        <?php }
                                    } ?>
                                </td>
                                <td style="text-align: right;"><?php echo MONEDA.' '.number_format($consolidadoDetalles['montocobradoliquidacion'],2) ?></td>
                            </tr>
                            <?php $s++;
                        } ?>

                        </tbody>
                    </table>
                    <br>
                    <h3 style="font-weight:bold;"">
                        <label class="control-label badge b-warning"> Monto total cobrado:
                            <span style="font-weight:bold;"><?php if (isset($total)) echo MONEDA.' '.number_format($total,2); ?></span>
                        </label>
                    </h3>

                <input type="hidden" value="<?php echo $id_consolidado ?>" name="id">


            <div class="modal-footer" id="">
                <?php

                if (isset($liquidar) && $liquidar == true && $status == 'IMPRESO') { ?>
                    <button type="button" id="" class="btn btn-primary" onclick="grupo.cerrarLiquidacion()">
                        <li class="glyphicon glyphicon-thumbs-up"></li> Cerrar Liquidación
                    </button>

                <?php }
                if (($status != 'CERRADO'&& $status != 'CONFIRMADO')) {
                } else {
                    ?>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">
                    <li class="glyphicon glyphicon-thumbs-down"></li> Salir</button>
                    <div style="float:left; margin-right: 10px">
                        <button type="button" class="btn btn-info"
                                onclick="pedidoDevolucion(<?php echo $id_consolidado ?>);">
                            <i class="fa fa-print"></i> Devoluciones
                        </button>
                    </div>
                    <div style="float:left;">
                        <button type="button" class="btn btn-info"
                                onclick="pedidoPreCancelacion(<?php echo $id_consolidado ?>);">
                            <i class="fa fa-print"></i> Pre-Cancelación
                        </button>
                    </div>
                <?php }

                ?>

            </div>


</form>


<script type="text/javascript">
    function pedidoDevolucion(id) {

        var win = window.open('<?= $ruta ?>consolidadodecargas/pedidoDevolucion/' + id, '_blank');
        win.focus();

        grupo.ajaxgrupo().success(function (data) {

        });


    }
    function pedidoPreCancelacion(id) {

        var win = window.open('<?= $ruta ?>consolidadodecargas/pedidoPreCancelacion/' + id, '_blank');
        win.focus();

        grupo.ajaxgrupo().success(function (data) {

        });


    }
</script>
<script type="text/javascript">
    $(document).ready(function () {

        $("#myBotons").on("click", function () {
            bootbox.confirm("Confirmar cierre de liquidación", function (result) {
                if (result == true) {
                    grupo.cerrarLiquidacion();
                }
            });
        });

    });
</script>



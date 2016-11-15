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

                            <th>ID</th>
                            <th>Total</th>
                            <th>Cantidad</th>
                            <th>Tipo Doc.</th>
                            <th>Numero Doc.</th>
                            <th>Cliente</th>
                            <th>Status</th>
                            <th>Acciones</th>
                            <th>Monto Cobrado</th>

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

                            if($consolidadoDetalles['venta_status'] == 'RECHAZADO')
                                $color = 'b-warning';
                            elseif($consolidadoDetalles['venta_status'] == 'ENTREGADO')
                                $color = 'b-primary';
                            elseif($consolidadoDetalles['venta_status'] == 'DEVUELTO PARCIALMENTE')
                                $color = 'b-other';
                            ?>
                            <tr>
                                <td><?php echo $consolidadoDetalles['venta_id']; ?></td>
                                <td><?php echo $consolidadoDetalles['total']; ?></td>
                                <td><?php echo $consolidadoDetalles['cantidad_prductos']; ?></td>
                                <td><?php echo $consolidadoDetalles['nombre_tipo_documento']; ?></td>
                                <td><?php echo $consolidadoDetalles['documento_Serie'] . "-" . $consolidadoDetalles['documento_Numero']; ?></td>
                                <td><?php echo $consolidadoDetalles['razon_social']; ?></td>
                                <td><?php echo $consolidadoDetalles['venta_status']; ?></td>
                                <td>
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
                                <td><?php echo $consolidadoDetalles['montocobradoliquidacion'] ?></td>
                            </tr>
                            <?php $s++;
                        } ?>

                        </tbody>
                    </table>
                    <div>Monto totalizado: <span><?php if (isset($total)) echo $total; ?></span></div>
                </div>
                <input type="hidden" value="<?php echo $id_consolidado ?>" name="id">


            </div>
            <div class="modal-footer" id="">
                <?php

                if (isset($liquidar) && $liquidar == true && $status == 'IMPRESO') { ?>
                    <button type="button" id="" class="btn btn-primary" onclick="grupo.cerrarLiquidacion()">Cerrar
                        Liquidación
                    </button>

                <?php }
                if (($status != 'CERRADO'&& $status != 'CONFIRMADO')) {
                } else {
                    ?>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                    <div style="float:left;">
                        <button type="button" class="btn btn-default"
                                onclick="pedidoDevolucion(<?php echo $id_consolidado ?>);">
                            <i class="fa fa-print"></i> Devoluciones
                        </button>
                    </div>
                    <div style="float:left;">
                        <button type="button" class="btn btn-default"
                                onclick="pedidoPreCancelacion(<?php echo $id_consolidado ?>);">
                            <i class="fa fa-print"></i> Pre-Cancelación
                        </button>
                    </div>
                <?php }

                ?>

            </div>

        </div>
</div>
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



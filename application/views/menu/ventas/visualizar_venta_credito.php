<div class="modal-dialog" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                    onclick="javascript:$('#visualizar_venta').hide();">&times;</button>
            <h3>Visualizar Venta</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <?php if (isset($ventas[0])){ ?>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Fecha Emision:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="input-prepend">
                                <input type="text" class='input-square input-small form-control' name="fec_emision"
                                       value="<?= isset($ventas[0]['fechaemision']) ? $ventas[0]['fechaemision'] : '' ?>"
                                       id="fec_emision" readonly>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="nro_venta" class="control-label">Nro Venta:</label>
                        </div>
                        <div class="col-md-3">

                            <input type="text" class='form-control' name="nro_venta"
                                   id="nro_venta" value="<?= $ventas[0]['serie'] . "-" . $ventas[0]['numero'] ?>"
                                   readonly>

                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Cliente:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class='form-control' name="Cliente"
                                   value="<?= $ventas[0]['cliente'] ?>" id="Cliente"
                                   readonly>
                        </div>

                    </div>

                    <div class="form-group"></div>
                </div>
                <div class="row"></div>
            </div>
            <div class="row-fluid">
                <div class="block">
                    <div class="block-title">
                        <h3>Detalle Productos</h3>
                    </div>
                    <div class="box-content box-nomargin">
                        <div id="lstTabla" class="table-responsive">

                            <table id="table" class="table dataTable dataTables_filter table-striped">
                                <thead>
                                <th>Producto</th>
                                <th>Cantidad</th>
                                <th>Precio</th>
                                <th>Subtotal</th>
                                </thead>

                                <tbody>
                                <?php foreach ($ventas as $venta): ?>
                                    <tr>
                                        <td><?= $venta['nombre'] ?></td>
                                        <td><?= $venta['cantidad'] ?></td>
                                        <td><?= $venta['preciounitario'] ?></td>
                                        <td><?= $venta['importe'] ?></td>
                                    </tr>
                                <?php endforeach ?>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group">
                    <div class="col-md-2">
                        <label for="monto_total" class="control-label">Monto Total:</label>
                    </div>
                    <div class="col-md-3">
                        <div class="input-prepend">
                            <input type="text" class='input-square input-small form-control' name="monto_total"
                                   id="monto_total" value="<?= $ventas[0]['montoTotal'] ?>" readonly>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row-fluid"></div>
            <div class="row-fluid">
                <div class="block">
                    <div class="block-title">
                        <h3>Pagos Adelantados</h3>
                    </div>
                    <div class="box-content box-nomargin">
                        <div id="lstTabla" class="table-responsive">
                            <table id="table" class="table dataTable dataTables_filter table-striped">
                                <thead>


                                <th>Monto Pagado</th>
                                <th>Operacion</th>

                                </thead>

                                <tbody>

                                <?php if ($ventas[0]['pagado']): ?>
                                    <tr>
                                        <td><?php echo $ventas[0]['pagado']; ?></td>
                                        <td>Generacion del pedido</td>
                                    </tr>
                                <?php endif; ?>

                                <?php  if (isset($consolidado_detalle[0]['confirmacion_monto_cobrado_caja']) || isset($consolidado_detalle[0]['confirmacion_monto_cobrado_bancos'])): ?>
                                    <tr>
                                        <td><?php echo number_format(floatval($consolidado_detalle[0]['confirmacion_monto_cobrado_caja']) + floatval($consolidado_detalle[0]['confirmacion_monto_cobrado_bancos']),2); ?></td>
                                        <td>Liquidacion del pedido (Confirmacio de entrega de dinero)</td>
                                    </tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row-fluid">
                <div class="block">
                    <div class="block-title">
                        <h3>Historial de Pago</h3>
                    </div>
                    <div class="box-content box-nomargin">
                        <div id="lstTabla" class="table-responsive">
                            <table id="table" class="table dataTable dataTables_filter table-striped">
                                <thead>
                                <th>Encargado</th>
                                <th>Fecha</th>
                                <th>Monto Pagado</th>
                                <th>Restante</th>
                                <th>Estatus</th>
                                <th>Acci&oacute;n</th>
                                </thead>

                                <tbody>
                                <?php
                                if (count($historial) > 0) {

                                    foreach ($historial as $row):
                                        $restante = $row['monto_restante'];?>
                                        <tr>
                                            <td><?php if ($row['nombre'] != null) {
                                                    echo $row['nombre'];
                                                } ?></td>
                                            <td><?= date("d-m-Y H:i:s", strtotime($row['historial_fecha'])) ?></td>
                                            <td>
                                                <?php $pos = strrpos($row['historial_monto'], '.');
                                                echo " " . MONEDA;
                                                if ($pos === false) {
                                                    echo $row['historial_monto'];
                                                } else {
                                                    echo substr($row['historial_monto'], 0, $pos + 3);
                                                } ?>

                                            </td>

                                            <td><?php
                                                $restante = ($restante);
                                                echo $restante;

                                                ?></td>
                                            <td><?= $row['historial_estatus'] ?></td>
                                            <td class='actions_big'>
                                                <div class="btn-group">
                                                    <a class='btn btn-default tip' title="Ver Venta"
                                                       onclick="visualizar_monto_abonado(<?= $row['historial_id'] ?>,<?= $row['credito_id'] ?>)"><i
                                                            class="fa fa-search"></i> Imprimir </a>

                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach;
                                } ?>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php } else {

            echo "Error de data, esta venta no tiene detalles";
        } ?>
        <div class="modal-footer">
            <a href="#" class="btn btn-danger" data-dismiss="modal"
               onclick="javascript:$('#visualizar_venta').hide();">Salir</a>
        </div>
    </div>
</div>

<div class="modal fade" id="visualizar_cada_historial" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

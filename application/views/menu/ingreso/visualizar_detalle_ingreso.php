<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal"
                    onclick="javascript:$('#visualizar_venta').hide();">&times;</button>
            <h3>Visualizar Cuenta por Pagar</h3>
        </div>
        <div class="modal-body">
            <div class="row-fluid force-margin">

                <div class="row">
                    <div class="form-group">


                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Fecha Emision:</label>
                        </div>
                        <div class="col-md-3">
                            <div class="input-prepend">
                                <input type="text" class='input-square input-small form-control' name="fec_emision"
                                       value="<?= date("d-m-Y", strtotime($detalle[0]['fecha_registro'])) ?>"
                                       id="fec_emision" readonly>
                            </div>
                        </div>


                        <div class="col-md-2">
                            <label for="nro_venta" class="control-label">N&uacute;mero del Ingreso:</label>
                        </div>
                        <div class="col-md-3">

                            <input type="text" class='form-control' name="nro_venta"
                                   id="nro_venta"
                                   value="<?= $detalle[0]['documento_serie'] . "-" . $detalle[0]['documento_numero'] ?>"
                                   readonly>

                        </div>
                    </div>
                </div>


                <div class="row">

                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Proveedor:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class='form-control' name="Cliente"
                                   value="<?= $detalle[0]['proveedor_nombre'] ?>" id="Cliente"
                                   readonly>
                        </div>

                    </div>

                    <!--
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Nro Cuota:</label>
                        </div>

                        <div class="col-md-3">
                            <input type="text" class='input-square input-small form-control'
                                   value=""
                                   name="nro_cuota"
                                   id="nro_cuota" readonly>
                        </div>
                    </div> -->


                </div>
                <div class="row">
                    <!--
                    <div class="form-group">
                        <div class="col-md-2">
                            <label for="fec_primer_pago" class="control-label">Monto Total del Ingreso:</label>
                        </div>
                        <div class="col-md-3">
                            <input type="text" class='input-square input-small form-control'
                                   value=""
                                   name="dec_credito_montocuota"
                                   id="monto_cuota" readonly>
                        </div>
                    </div>

                </div> -->

                </div>
                <div class="row-fluid">
                    <div class="block">
                        <div class="block-title">
                            <h3>Detalle Productos</h3>
                        </div>
                        <div class="box-content box-nomargin">
                            <div id="lstTabla" class="table-responsive">

                                <table id="table" class="table table-striped table-bordered">
                                    <thead>
                                    <th>Producto</th>
                                    <th>Cantidad</th>
                                    <th>Precio</th>
                                    <th>Subtotal</th>
                                    </thead>

                                    <tbody>
                                    <?php if (count($detalle > 0)) {
                                        foreach ($detalle as $row): ?>
                                            <tr>
                                                <td><?= $row['producto_nombre'] ?></td>
                                                <td><?= $row['cantidad'] ?></td>
                                                <td><?= $row['precio'] ?></td>
                                                <td><?= $row['total_detalle'] ?></td>
                                            </tr>
                                        <?php endforeach;

                                    }
                                    ?>


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
                                       id="monto_total" value="<?= number_format($total_ingreso['total_ingreso'], 2) ?>"
                                       readonly>
                            </div>

                        </div>
                    </div>
                </div>


                <div class="row-fluid">
                    <div class="block">
                        <div class="block-title">
                            <h3>Historial de Pago</h3>
                        </div>
                        <div class="box-body">
                            <div id="lstTabla" class="table-responsive">
                                <table id="table_resultado" class="table table-striped table-bordered">
                                    <thead>
                                    <th>Fecha</th>
                                    <th>Monto Pagado</th>
                                    <th>Restante</th>
                                    <th>Acci&oacute;n</th>
                                    </thead>

                                    <tbody>
                                    <?php
                                    if (count($cuentas) > 0) {


                                        $restante = $total_ingreso['total_ingreso'];
                                        foreach ($cuentas as $row): ?>
                                            <tr>
                                                <td><?= date("d-m-Y H:i:s", strtotime($row['pagoingreso_fecha'])) ?></td>
                                                <td>
                                                    <?php echo " " . MONEDA;

                                                    echo number_format($row['pagoingreso_monto'], 2);
                                                    ?>

                                                </td>
                                                <td><?php
                                                    $restante = ($restante - $row['pagoingreso_monto']);
                                                    echo number_format($restante, 2);

                                                    ?></td>
                                                <td>
                                                    <div class="">
                                                        <a href="#" type="button" id="imprimir" class="btn btn-primary"
                                                           onclick="ver_detalle_pago(<?= $row['pagoingreso_id'] ?>,<?= $row['pagoingreso_ingreso_id'] ?>)">
                                                            <i
                                                                class="fa fa-print"></i>Imprimir</a>
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


            <div class="modal-footer">
                <a href="#" class="btn btn-danger" data-dismiss="modal"
                   onclick="javascript:$('#visualizar_venta').hide();">Salir</a>
            </div>
        </div>
    </div>
</div>
<script>

    $(document).ready(function () {
        // TablesDatatables.init();


    });
</script>
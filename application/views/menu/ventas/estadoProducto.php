<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Estado del producto</h4>
        </div>
        <div class="modal-body">

            <?php //var_dump($datos_producto);
           // var_dump($cantidad_comprada);?>

            <div class="row">
                <div class="box-body" id="tabla">
                    <div class="table-responsive">
                        <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                            <thead>
                            <tr>
                                <th>UM</th>
                                <th>Precio de Venta</th>
                                <th>Ultimo costo</th>
                                <th>Costo promedio</th>
                                <th>Cantidad Comprado</th>
                                <th>Cantidad Vendido</th>
                                <th>Importe Comprado</th>
                                <th>Importe Vendido</th>
                                <th>Utilidad</th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($producto_estados as $estado):?>
                                    <tr>
                                        <td><?=$estado->unidad_nombre?></td>
                                        <td><?=MONEDA.' '.number_format($estado->precio_venta,2)?></td>
                                        <td><?=MONEDA.' '.number_format($estado->ultimo_costo,2)?></td>
                                        <td><?=MONEDA.' '.number_format($estado->costo_promedio,3)?></td>
                                        <td><?=$estado->cantidad_comprado?></td>
                                        <td><?=$estado->cantidad_vendido?></td>
                                        <td><?=MONEDA.' '.number_format($estado->importe_comprado,2)?></td>
                                        <td><?=MONEDA.' '.number_format($estado->importe_vendido,2)?></td>
                                        <td><?=MONEDA.' '.number_format($estado->importe_vendido - $estado->importe_comprado,2)?></td>
                                    </tr>
                                <?php endforeach;?>
                            </tbody>
                        </table>

                    </div>

                    <br>

                </div>
            </div>



        </div>
        <div class="modal-footer">
            <input type="reset" class="btn btn-default" value="Cancelar" data-dismiss="modal">
        </div>
    </div>
</div>

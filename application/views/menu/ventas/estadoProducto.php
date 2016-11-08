<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Estado del producto <?= $producto_unidad[0]['producto_nombre'] ?></h4>
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

                                <th>Precio de Venta</th>
                                <th> Ultimo costo de compra</th>
                                <th>Costo promedio actual</th>
                                <th>Utilidad</th>
                                <th>Cantidad de productos vendidos</th>
                                <th>Cantidad de productos bonificados</th>

                            </tr>
                            </thead>
                            <tbody>


                            <tr>

                                <td><?php echo $precio_venta['precio']; ?></td>
                                <td><?php  if($producto_unidad[0]['costo_unitario']==null){
                                        echo "No posee";
                                    }else{
                                        echo $producto_unidad[0]['costo_unitario'];
                                    }
                                    ?></td>

                                <td><?php
                                    if($producto_unidad[0]['costo_unitario']!=null and $producto_unidad[0]['costo_unitario']>0 ) {

                                        if ($cantidad_comprada['cantidad_comprada'] > 0) {
                                            $pos = strrpos($producto_unidad[0]['costo_unitario'] / $cantidad_comprada['cantidad_comprada'], '.');
                                            if ($pos === false) {
                                                echo$producto_unidad[0]['costo_unitario'] /$cantidad_comprada['cantidad_comprada'];
                                            } else {
                                                echo substr($producto_unidad[0]['costo_unitario']/ $cantidad_comprada['cantidad_comprada'], 0, $pos + 3);
                                            }
                                        }else{

                                            $pos = strrpos($producto_unidad[0]['costo_unitario'], '.');
                                            if ($pos === false) {
                                                echo $producto_unidad[0]['costo_unitario'];
                                            } else {
                                                echo substr($producto_unidad[0]['costo_unitario'], 0, $pos + 3);
                                            }
                                        }
                                    }else{
                                        echo "0";
                                    }
                                    ?></td>
                                <td><?php  echo MONEDA." ".$datos_producto[0]['utilidad'];  ?></td>
                                <td><?php  echo $datos_producto[0]['cantidad_vendida']; ?></td>
                                <td><?php  if($producto_bonificado['cantidad_bonificada']!="") { echo
                                    $producto_bonificado['cantidad_bonificada']; } ?></td>

                            </tr>

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

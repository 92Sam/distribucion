<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalles del producto</h4>
        </div>
        <div class="modal-body">

            <div class="row">
                <div class="box-body" id="tabla">
                    <div class="table-responsive">
                        <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                            <thead>
                            <tr>

                                <th>Cantidad</th>
                                <th>Precio</th>

                                <th>Unidad de Medida</th>
                                <th>Total Detalle</th>
                                <th>Fecha de registro</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($productos as $detallesProducto) {
                                ?>
                                <tr>

                                    <td> <?php echo $detallesProducto['cantidad']; ?></td>
                                    <td> <?php echo $detallesProducto['precio']; ?></td>

                                    <td> <?php echo $detallesProducto['nombre_unidad']; ?></td>
                                    <td> <?php echo $detallesProducto['total_detalle']; ?></td>
                                    <td> <?php echo date('d-m-Y', strtotime($detallesProducto['fecha_registro'])); ?></td>

                                </tr>
                            <?php

                            }
                            ?>
                            </tbody>
                        </table>

                    </div>

                    <br>

                </div>
            </div>
        </div>
    </div>
</div>


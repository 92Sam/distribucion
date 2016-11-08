<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Venta</li>
    <li><a href="">Reporte de Venta</a></li>
</ul>
<div class="block">
    <div class="box-body" id="tabla">
        <div class="table-responsive">
            <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                <thead>
                <tr>

                    <th>ID</th>
                    <th>Nombre del producto</th>
                    <th>Detalles</th>

                </tr>
                </thead>
                <tbody>
                <?php foreach ($productos as $producto) { ?>
                    <tr>
                        <td><?= $producto['producto_id'] ?></td>
                        <td><?= $producto['producto_nombre'] ?></td>
                        <td class="center">
                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Costos de compra" data-original-title="Costos de compra"
                                            href="#" onclick="verDetalles(' . $producto['producto_id'] . '); ">'; ?>
                                <span><i class="fa fa-line-chart"></i>Costos</span>
                                </a>
                            </div>

                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Estado" data-original-title="Estado del producto"
                                            href="#" onclick="verEstado(' . $producto['producto_id'] . '); ">'; ?>
                                <span><i class="fa fa-history"></i>Estado Producto</span>
                                </a>
                            </div>
                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Evolucion" data-original-title="Evolucion de costos"
                                            href="#" onclick="evolucionDeCostos(' . $producto['producto_id'] . '); ">'; ?>
                                <span><i class="fa fa-line-chart"></i>Evolucion de costos</span>
                                </a>
                            </div>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

        </div>

        <br>

    </div>
</div>



<!-- /.modal-dialog -->

<script type="text/javascript">

    $(function () {

        TablesDatatables.init();



    });
    function verDetalles(id) {

        $("#detallesProducto").load('<?= $ruta ?>ingresos/detallesProducto/' + id);
        $('#detallesProducto').modal('show');
    }
    function verEstado(id) {

        $("#estadoProducto").load('<?= $ruta ?>producto/estadoProducto/' + id);
        $('#estadoProducto').modal('show');
    }
    function evolucionDeCostos(id) {

        $("#estadoProducto").load('<?= $ruta ?>producto/evolucionCostos/' + id);
        $('#estadoProducto').modal('show');
    }

</script>

<div class="modal fade" id="detallesProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>

<div class="modal fade" id="estadoProducto" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>



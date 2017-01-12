<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped table-bordered" id="tblbonificaciones">
        <thead>
        <tr align="center">
            <th>ID</th>
            <th>Grupo Cliente</th>
            <th>Vencimiento</th>
            <th>Estado</th>
            <th>Productos</th>
            <th>Marca Condición</th>
            <th>Unidad Condición</th>
            <th>Cantidad Condición</th>
            <th>Bono Producto</th>
            <th>Bono Unidad</th>
            <th>Bono Cantidad</th>
            <th class="desktop">Acciones</th>

        </tr>
        </thead>
        <tbody>

        <?php foreach ($bonificacioness as $bonificaciones): ?>
            <tr>
                <td> <?= $bonificaciones['id_bonificacion'] ?> </td>
                <td> <?= $bonificaciones['GrupoCliente'] ?> </td>
                <td> <?= $bonificaciones['fecha'] ?> </td>
                <td> <?php $days = (strtotime(date('d-m-Y')) - strtotime($bonificaciones['fecha'])) / (60 * 60 * 24);
                    if ($days < 0)
                        $days = 0; ?>
                    <div>
                        <label class="label <?php if (floor($days) <= 0) {echo "label-success";
                        } else { echo "label-danger";
                            } ?> "> <?php if (floor($days) <= 0) {
                                echo "Activa";
                            } else {
                                echo "Vencida";
                            } ?></label>
                    </div>
                </td>

                <td> <?php foreach ($bonificaciones['bonificaciones_has_producto'] as $produc) {
                            echo sumCod($produc['id_producto']) . " " . $produc['producto_nombre']; ?>
                        <br><?php } ?>
                </td>

                <td><?= $bonificaciones['nombre_marca'] ?></td>
                <td><?= $bonificaciones['nombre_unidad'] ?></td>
                <td><?= $bonificaciones['cantidad_condicion'] ?></td>
                <td><?= $bonificaciones['producto_bonificacion'] ?></td>
                <td><?= $bonificaciones['unidad_bonificacion'] ?></td>
                <td><?= $bonificaciones['bono_cantidad'] ?></td>
                <td class="center">
                    <div class="btn-group">

                        <a class="btn btn-default" data-toggle="tooltip" title="Editar"
                           data-original-title="fa fa-comment-o"
                           href="#"
                           onclick="editar('<?php echo $bonificaciones["id_bonificacion"] ?>','<?php echo isset($bonificaciones['producto_id']) ? $bonificaciones['producto_id'] : "false" ?>','<?php echo $bonificaciones['bono_producto'] ?>', '<?php echo $id_grupoclie ?>')">
                            <i class="fa fa-edit"></i>
                        </a>

                        <?php echo '<a class="btn btn-danger" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o"
                                     onclick="borrar(' . $bonificaciones['id_bonificacion'] . ');">'; ?>
                        <i class="fa fa-trash-o"></i>
                        </a>

                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    $(function () {
        TablesDatatablesBonos.init();
    });
</script>

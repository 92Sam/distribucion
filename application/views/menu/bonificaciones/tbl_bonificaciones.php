
<table class="table table-striped dataTable table-bordered" id="example">
    <thead>
    <tr>
        <th>ID</th>
        <th>Vencimiento</th>
        <th>Estado</th>
        <th>Productos</th>
        <th>Marca Condici&oacute;n</th>
        <th>Grupo Condici&oacute;n</th>
        <th>Sub Grupo Condici&oacute;n</th>
        <th>Familia Condici&oacute;n</th>
        <th>Sub Familia Condici&oacute;n</th>
        <th>L&iacute;nea Condici&oacute;n</th>
        <th>Unidad Condici&oacute;n</th>
        <th>Cantidad Condici&oacute;n</th>
        <th>Bono Producto</th>
        <th>Bono Unidad</th>
        <th>Bono Cantidad</th>

        <th class="desktop">Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($bonificacioness) > 0) {

        foreach ($bonificacioness as $bonificaciones) {
            ?>
            <tr>

                <td class="center"><?= $bonificaciones['id_bonificacion'] ?></td>
                <td><?= $bonificaciones['fecha'] ?></td>
                <td><?php $days = (strtotime(date('d-m-Y')) - strtotime($bonificaciones['fecha'])) / (60 * 60 * 24);
                    if ($days < 0)
                        $days = 0; ?>
                    <div><label class="label
                            <?php if (floor($days) <= 0) {
                            echo "label-success";

                        } else {
                            echo "label-danger";
                        } ?> "> <?php if (floor($days) <= 0) {
                                echo "Activa";

                            } else {
                                echo "Vencida";
                            } ?></label>
                    </div>
                </td>
                <td>
                    <?php /*echo '<a class="btn btn-default" data-toggle="tooltip"
                                     title="Ver Productos" data-original-title="fa fa-eye"
                                     onclick="verproductos(' . $bonificaciones['id_bonificacion'] . ');">';
 */ ?>
                    <!--  <i class="fa fa-eye"></i>
                      </a>-->

                    <?php

                    foreach($bonificaciones['bonificaciones_has_producto'] as $produc){
                        echo sumCod($produc['id_producto']). " ".$produc['producto_nombre'];?>
                        <br>
                        <?php
                    }
                    ?>

                </td>
                <td><?= $bonificaciones['nombre_marca'] ?></td>
                <td><?= $bonificaciones['nombre_grupo'] ?></td>
                <td><?= $bonificaciones['nombre_subgrupo'] ?></td>
                <td><?= $bonificaciones['nombre_familia'] ?></td>
                <td><?= $bonificaciones['nombre_subfamilia'] ?></td>
                <td><?= $bonificaciones['nombre_linea'] ?></td>
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
                           onclick="editar('<?php echo $bonificaciones["id_bonificacion"] ?>','<?php echo isset($bonificaciones['producto_id']) ? $bonificaciones['producto_id'] : "false" ?>','<?php echo $bonificaciones['bono_producto'] ?>')">
                            <i class="fa fa-edit"></i>
                        </a>

                        <?php echo '<a class="btn btn-default" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="fa fa-comment-o"
                                     onclick="borrar(' . $bonificaciones['id_bonificacion'] . ');">'; ?>
                        <i class="fa fa-trash-o"></i>
                        </a>

                    </div>
                </td>
            </tr>
        <?php }
    } ?>

    </tbody>
</table>

<script>
    TablesDatatables.init(1);
</script>
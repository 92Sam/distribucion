
<?php $ruta = base_url(); ?>

<table class="table table-striped dataTable table-bordered table-condensed" id="tblbonificaciones">
    <thead>
    <tr>
        <th>ID</th>
        <th>Vencimiento</th>
        <th>Estado</th>
        <th style="width: 300px !important;">Productos</th>
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
<a href="<?= $ruta ?>bonificaciones/pdfExport/<?php echo $id_grupoclie;?>/" id="generarpdf" class="btn  btn-default btn-lg" data-toggle="tooltip"
   title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta ?>bonificaciones/excelExport/<?php echo $id_grupoclie;?>/" id="generarexcel" class="btn btn-default btn-lg" data-toggle="tooltip"
   title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>


<script>
    TablesDatatables.init(1);
</script>
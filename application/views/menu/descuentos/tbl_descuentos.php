<table class="table table-striped table-bordered table-condensed dataTable" id="tbldescuentos">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre</th>
        <th class="desktop">Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($descuentos) > 0) {

        foreach ($descuentos as $descuento) {
            ?>
            <tr>

                <td class="center"><?= sumCod($descuento['descuento_id']); ?></td>
                <td><?= $descuento['nombre'] ?></td>

                <td class="center">
                    <div class="btn-group">
                        <?php

                        echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                                        title="Editar" data-original-title="Ver"
                                                        href="#" onclick="verModal(' . $descuento['descuento_id'] . ');">'; ?>
                        ver
                        </a>
                        <?php

                        echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                                        title="Editar" data-original-title="Editar"
                                                        href="#" onclick="editar(' . $descuento['descuento_id'] . ',\'' . $grupo_id . '\');">'; ?>
                        <i class="fa fa-edit"></i>
                        </a>
                        <?php echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                     title="Eliminar" data-original-title="Eliminar" onclick="borrar(' . $descuento['descuento_id'] . ',\'' . $descuento['nombre'] . '\');">'; ?>
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
    $(function () {
        TablesDatatables.init();

    });
</script>
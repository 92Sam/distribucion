
<style>
    #tablaresult th {
        font-size: 12px !important;
        padding: 6px 2px;
        text-align: center;
        vertical-align: middle;
    }

    #tablaresult td {
        text-align: center;
        font-size: 12px !important;
    }
</style>

<table class="table table-striped table-bordered" id="tablaresult">
    <thead>
    <tr>
        <th>ID</th>
        <th>Grupo de Descuento</th>
        <th>Nombre de la escala de Descuentos</th>
        <th>Acciones</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($descuentos) > 0) {

        foreach ($descuentos as $descuento) {
            ?>
            <tr>

                <td><?= $descuento['descuento_id']; ?></td>
                <td><?= $descuento['nombre_grupos_cliente'] ?></td>
                <td><?= $descuento['nombre'] ?></td>

                <td class="center">
                    <div class="btn-group">
                        <?= '<a class="btn btn-primary" data-toggle="tooltip"
                             title="Editar" data-original-title="Ver" href="#"
                             onclick="verModal(' . $descuento['descuento_id'] . ');">'; ?>
                        ver
                        </a>
                        <?='<a class="btn btn-default" data-toggle="tooltip"
                                    title="Editar" data-original-title="Editar"
                                    href="#"
                                    onclick="editar(' . $descuento['descuento_id'] . ',\'' . $grupo_id . '\');">'; ?>
                        <i class="fa fa-edit"></i>
                        </a>
                        <?php echo '<a class="btn btn-danger" data-toggle="tooltip"
                                    title="Eliminar" data-original-title="Eliminar"
                                    onclick="borrar(' . $descuento['descuento_id'] . ',\'' . $descuento['nombre'] . '\');">'; ?>
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
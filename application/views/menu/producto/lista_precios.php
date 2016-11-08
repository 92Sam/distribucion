<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered table-condensed table-hover" id="tablaresultado">
        <thead>
        <tr>

            <th>ID Producto</th>
            <th>Nombre</th>
            <th>Grupo</th>
            <?php
            $bandera = "LISTA_PRECIOS";
            foreach ($precios as $precio) { ?>
                <th class=""><?= $precio['nombre_precio'] ?></th>
            <?php } ?>
        </tr>
        </thead>
        <tbody>
        <?php
            foreach($lista as $lista) {
                ?>
                <tr>
                    <td><?= $lista['producto_id']; ?></td>
                    <td><?= $lista['producto_nombre']; ?></td>
                    <td><?= $lista['nombre_grupo']; ?></td>
                    <td>
                        <?php
                        foreach ($precios as $precio) {


                            foreach ($lista_p as $producto) {
                                if ($lista['producto_id'] == $producto['id_producto']) {
                                    if ($producto['id_precio'] == $precio['id_precio'] and $producto['id_grupo'] == $lista['id_grupo']) {

                                       echo $producto['nombre_unidad'] . ": " . number_format($producto['precio'], 2) . " <br>";

                                    }
                                }
                            }


                        }

                        ?>

                    </td>
                </tr>
                <?php
            }
        ?>
        </tbody>
    </table>

</div>




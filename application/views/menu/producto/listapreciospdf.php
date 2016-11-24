<?php $ruta = base_url(); ?>
<style type=text/css>
    th {
        color: #000;
        font-weight: bold;
        background-color: #CED6DB;
    }

    td {
        color: #222;
        font-weight: bold;
        background-color: #fff;
    }

    table {
        border: 5px
    }

    body {
        font-size: 15px
    }
</style>


<div class="block">

    <div class="row">
        <h1 >LISTA DE PRECIOS</h1>
    </div>
    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
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
            //var_dump($productos);
            if (count($lstProducto) > 0) {
                foreach ($lstProducto as $row) {
                    ?>
                    <tr>
                        <td><?= $row['producto_id'] ?></td>
                        <td><?= $row['producto_nombre'] ?></td>
                        <td><?= $row['nombre_grupo'] ?></td>
                        <?php

                        foreach ($precios as $precio) {
                            echo "<td>";

                            foreach ($productos as $producto) {
                                if ($row['producto_id'] == $producto['id_producto']) {
                                    if ($producto['id_precio'] == $precio['id_precio'] and $producto['id_grupo'] == $row['id_grupo']) {
                                        echo $producto['nombre_unidad'] . ": " . number_format($producto['precio'], 2);
                                        echo "<br>";
                                    }
                                }
                            }

                            echo "</td>";
                        }

                        ?>
                    </tr>
                    <?php


                }
            }
            ?>
            </tbody>
        </table>

    </div>
</div>


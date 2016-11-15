<?php
/**
 * Created by PhpStorm.
 * User: Jhainey
 * Date: 09/07/2015
 * Time: 15:01
 */
?>
<table class='table table-striped dataTable table-bordered table-condensed' id="table">
    <thead>
    <tr>
        <?php foreach ($columnas as $col): ?>
            <?php if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_activo') echo " <th>" . $col->nombre_mostrar . "</th>" ?>
        <?php endforeach; ?>
        <th>UM</th>
        <th>Cantidad</th>
        <th>Fracci&oacute;n</th>
        <th>Activo</th>

    </tr>
    </thead>
    <tbody id="tbody">

    <?php foreach ($lstProducto as $pd): //var_dump($pd);

        ?>


        <tr id="<?= $pd['producto_id']; ?>">
            <?php foreach ($columnas as $col): ?>
                <?php
                if (array_key_exists($col->nombre_columna, $pd) and $col->mostrar == TRUE) {
                    if ($col->nombre_columna != 'producto_activo') {
                        echo '<td> ' . $pd[$col->nombre_join] . ' </td>';
                    }
                }

                ?>
            <?php endforeach; ?>
            <td>
                <?php echo $pd['nombre_unidad']; ?>

            </td>
            <td>
                <?php echo $pd['cantidad']; ?>

            </td>
            <td>
                <?php echo $pd['fraccion']; ?>

            </td>
            <td>
                <?php
                if ($pd['producto_activo'] == 1) echo "Activo"; else  echo "Inactivo";
                ?>
            </td>

        </tr>

    <?php endforeach; ?>


    </tbody>
</table>


<script>$(function () {
        TablesDatatables.init(0, 'table');
        $("#tbody").selectable({
            stop: function () {

                var id = $("#tbody tr.ui-selected").attr('id');


            }
        });
    });</script>

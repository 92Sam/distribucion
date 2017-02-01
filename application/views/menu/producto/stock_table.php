<?php $ruta = base_url(); ?>

        <table class='table table-striped dataTable table-bordered'  id="table">
            <thead>
            <tr>
                <?php foreach ($columnas as $col): ?>

                <?php if ($col->mostrar == TRUE && $col->nombre_columna != 'producto_activo'){
                        if($col->nombre_mostrar == "Sub Grupo"){
                            echo '<th>Linea</th>';
                        }elseif($col->nombre_mostrar == 'Familia') {
                            echo '<th>Sub Linea</th>';
                        }elseif($col->nombre_mostrar == 'Linea') {
                            echo '<th>Talla</th>';
                        }else{
                            echo " <th>" .$col->nombre_mostrar. "</th>";
                        }
                    }
                ?>
                <?php endforeach; ?>
                <th>UM</th>
                <th>Cantidad</th>


            </tr>
            </thead>
            <tbody id="tbody" >
                <?php foreach ($lstProducto as $producto):?>
                    <tr>
                    <?php foreach ($columnas as $col):?>
                        <?php if($col->mostrar == TRUE && $col->nombre_columna != 'producto_activo'):?>
                            <?php if ($col->nombre_columna == 'producto_id'):?>
                                <td><?=sumCod($producto['producto_id'])?></td>
                            <?php else:?>
                                <td><?=$producto[$col->nombre_join]?></td>
                            <?php endif;?>
                        <?php endif;?>
                    <?php endforeach;?>

                    <td><?=$producto['nombre_unidad']?></td>
                    <td><?=$producto['cantidad']?></td>
                </tr>
                <?php endforeach;?>

            </tbody>
        </table>

        <script>
$(document).ready(function(){
    TablesDatatablesBonos.init();

    $("#tbody").selectable({
        stop: function () {
            var id = $("#tbody tr.ui-selected td:first").html();
        }
    });
});
        </script>

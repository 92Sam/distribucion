<?php $ruta = base_url(); ?>
<div class="row">
    <div class="col-sm-12 col-lg-12">
        <div class="table-responsive">
            <table class="table table-striped dataTable table-bordered" id="example">
                <thead>
                <tr>

                    <?php
                    foreach($escalas as $escala) {
                        $p = $escala['producto_id'];
                    }
                    if(empty($p)){
                        echo "<th>Sin resultados</th>";

                    }else {
                        ?>
                        <th>ID</th>
                        <th></th>
                        <?php
                        foreach ($escalas_h as $escala) {

                            ?>

                            <th><?= $escala['cantidad_minima'] ?>--<?= $escala['cantidad_maxima'] ?></th>
                            <?php
                        }
                    }
                    ?>


                </tr>
                </thead>
                <tbody>
                <?php

                $array_destino=array();
                $valor=array();
                foreach($escalas as $escala) {

                    $valor['nombre']=$escala['producto_nombre'];
                    $valor['id']=$escala['producto_id'];
                    if (!in_array($valor,$array_destino)){
                        $array_destino[]=$valor;
                    }
                }
                foreach ($array_destino as $valor) {
                    ?>
                    <tr><td><?= sumCod($valor['id']);  ?></td>
                        <td><?= $valor['nombre']; ?></td>
                        <?php
                        foreach($escalas as $escala) {
                            if ($valor['nombre'] == $escala['producto_nombre']) {
                                echo "<td>" . $escala['precio'] . "</td>";
                            }
                        }
                        ?>
                    </tr>
                    <?php
                }
                ?>

                </tbody>
            </table>
        </div>

    </div>
</div>


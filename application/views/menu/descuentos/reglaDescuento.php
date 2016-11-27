<?php $ruta = base_url(); ?>

<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            Regla de descuento
        </div>
        <?php foreach($escalas_h as $id){
        echo'<input type="hidden" id="desID" value="'.$id['descuento_id'].'" />';
       }
        ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-1">
                    <span>ID</span>
                </div>
                <div class="col-md-2">
                    <input type="text" name="id_des" id="id" class="form-control inputB" />
                </div>
                <div class="col-md-2">
                    <span>Nombre</span>
                </div>
                <div class="col-md-3">
                    <input type="text"  id="nombre" name="nombre_des" class="form-control inputB" />
                </div>
            </div>
            <br />
            <div id="result">
            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <div class="table-responsive">
                        <table class="table table-striped dataTable table-bordered" id="example">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th></th>
                                <?php
                                foreach($escalas_h as $escala){

                                    ?>

                                    <th><?= $escala['cantidad_minima'] ?>--<?= $escala['cantidad_maxima'] ?></th>
                                    <?php
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
            </div>


        </div>
    </div>

    <script src="<?php echo $ruta ?>recursos/js/pages/widgetsStats.js"></script>
    <script src="<?php echo $ruta ?>recursos/js/jquery.flot.categories.js"></script>

    <script>
        $(document).ready(function () {
            $('.inputB').keyup(function(){

                var desID =   $("#desID").val();
                var id =   $("#id").val();
                var nombre = $("#nombre").val();
                $.ajax({
                    url: '<?= base_url()?>descuentos/lista_descuento',
                    data: {
                        'desID': desID,
                        'id_des': id,
                        'nombre_des':nombre
                    },
                    type: 'POST',
                    success: function (data) {
                        if (data.length > 0) {
                            $("#result").html(data);
                        }

                        TablesDatatables.init(0, 'tablaresultado');
                    },
                    error: function () {

                        alert('Ocurrio un error por favor intente nuevamente');
                    }
                });
            });

        });
    </script>

<?php $ruta = base_url(); ?>

<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            Regla de descuento
        </div>
        <?php foreach ($escalas_h as $id) {
            echo '<input type="hidden" id="desID" value="' . $id['descuento_id'] . '" />';
        }
        ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-1">
                    <span>ID</span>
                </div>
                <div class="col-md-2">
                    <input type="text" name="id_des" id="id" class="form-control inputB"/>
                </div>
                <div class="col-md-2">
                    <span>Nombre</span>
                </div>
                <div class="col-md-3">
                    <input type="text" id="nombre" name="nombre_des" class="form-control inputB"/>
                </div>
            </div>
            <br/>
            <div id="result">
                <div class="row">
                    <label class="col-md-12 control-label">
                        <span id="pt"></span> productos de <?=$total_productos->total?>
                    </label>
                </div>
                <br/>
                <div class="row">
                    <div class="col-sm-12 col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped dataTable table-bordered" id="example">
                                <thead>
                                <tr>
                                    <th>C&oacute;digo</th>
                                    <th>Producto</th>
                                    <?php
                                    foreach ($escalas_h as $escala) {

                                        ?>

                                        <th><?= $escala['cantidad_minima'] ?>--<?= $escala['cantidad_maxima'] ?></th>
                                        <?php
                                    }
                                    ?>


                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $array_destino = array();
                                $valor = array();

                                foreach ($escalas as $escala) {

                                    $valor['nombre'] = $escala['producto_nombre'];
                                    $valor['id'] = $escala['producto_id'];
                                    if (!in_array($valor, $array_destino)) {
                                        $array_destino[] = $valor;
                                    }
                                }
                                foreach ($array_destino as $valor) {
                                    ?>
                                    <tr>
                                        <td><?= sumCod($valor['id']); ?></td>

                                        <td><?= $valor['nombre']; ?></td>
                                        <?php
                                        foreach ($escalas as $escala) {
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

                            <br>

                            <a href="<?= $ruta ?>descuentos/pdfExport/<?php echo $id_desc; ?>/" id="generarpdf"
                               class="btn  btn-default btn-lg" data-toggle="tooltip"
                               title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i
                                        class="fa fa-file-pdf-o fa-fw"></i></a>
                            <a href="<?= $ruta ?>descuentos/excelExport/<?php echo $id_desc; ?>/" id="generarexcel"
                               class="btn btn-default btn-lg" data-toggle="tooltip"
                               title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
                                        class="fa fa-file-excel-o fa-fw"></i></a>
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

            $('#pt').html($('#example tbody tr').length);
            $('.inputB').keyup(function () {

                var desID = $("#desID").val();
                var id = $("#id").val();
                var nombre = $("#nombre").val();
                $.ajax({
                    url: '<?= base_url()?>descuentos/lista_descuento',
                    data: {
                        'desID': desID,
                        'id_des': id,
                        'nombre_des': nombre
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

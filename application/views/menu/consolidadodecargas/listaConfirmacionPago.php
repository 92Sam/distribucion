<?php $ruta = base_url(); ?>
<div class="table-responsive" id="tablaresultado">
    <table class="table table-striped dataTable table-bordered">
        <thead>
        <tr>

            <th>N° de Consolidado Guía Carga</th>
            <th>Transportista</th>
            <th>Fecha</th>
            <th>Placa del cami&oacute;n</th>
            <th>Importe a liquidar</th>
            <th>Acciones</th>


        </tr>
        </thead>
        <tbody id="tbody_confirmacion">
        <?php if (count($consolidado) > 0) {

            foreach ($consolidado as $consolidadoCamion) {
               // if ($consolidadoCamion['status'] == "CERRADO") {
                    ?>
                    <tr>

                        <td class="center"><?= $consolidadoCamion['consolidado_id'] ?></td>
                        <td><?= $consolidadoCamion['nombre'] ?></td>
                        <td><?= date('d-m-Y', strtotime($consolidadoCamion['fecha'])) ?></td>
                        <td><?= $consolidadoCamion['camiones_placa'] ?></td>
                        <td><?= number_format($consolidadoCamion['totalC'], 2) ?></td>
                        <td class="center">
                            <?php if ($consolidadoCamion['status'] == "CERRADO"){ ?>
                            <div class="btn-group">

                                <a class="btn btn-default" data-toggle="tooltip"
                                   title="Ver" data-original-title="fa fa-comment-o"
                                   href="#"
                                   onclick="infoCobro(<?= $consolidadoCamion['consolidado_id'] ?>,'<?= $consolidadoCamion['status'] ?>','CONFIRMAR'); ">
                                    Confirmar cobro
                                </a>
                                <?php } else {
                                    ?>
                                    <a class="btn btn-default" data-toggle="tooltip"
                                       title="Ver" data-original-title="fa fa-comment-o"
                                       href="#"
                                       onclick="infoCobro(<?= $consolidadoCamion['consolidado_id'] ?>,'<?= $consolidadoCamion['status'] ?>','VER'); ">
                                        Ver
                                    </a>
                                <?php } ?>
                            </div>
                        </td>
                    </tr>
                <?php }
           // }
        }
        ?>

        </tbody>
    </table>

</div>


<script>$(function () {

        TablesDatatables.init();
    });
</script>
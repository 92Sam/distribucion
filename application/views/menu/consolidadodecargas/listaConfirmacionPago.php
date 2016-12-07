<?php $ruta = base_url(); ?>
<div class="table-responsive" id="tablaresultado">
    <table class="table table-striped dataTable table-bordered">
        <thead>
        <tr>

            <th style="text-align: center;">N° de Consolidado Guía Carga</th>
            <th style="text-align: center;">Fecha Entrega</th>
            <th style="text-align: center;">Transportista</th>
            <th style="text-align: center;">Placa del cami&oacute;n</th>
            <th style="text-align: center;">Fecha Cierre Liquidación</th>
            <th style="text-align: center;">Importe a liquidar</th>
            <th style="text-align: center;">Acciones</th>


        </tr>
        </thead>
        <tbody id="tbody_confirmacion">
        <?php if (count($consolidado) > 0) {

            foreach ($consolidado as $consolidadoCamion) {
               // if ($consolidadoCamion['status'] == "CERRADO") {
                    ?>
                    <tr>

                        <td style="text-align: center;"><?= $consolidadoCamion['consolidado_id'] ?></td>
                        <td style="text-align: center;"><?= date('d-m-Y', strtotime($consolidadoCamion['fecha'])) ?></td>
                        <td style="text-align: center;"><?= $consolidadoCamion['nombre'] ?></td>
                        <td style="text-align: center;"><?= $consolidadoCamion['camiones_placa'] ?></td>
                        <td>Antonio</td>
                        <td style="text-align: center;"><?= number_format($consolidadoCamion['totalC'], 2) ?></td>
                        <td style="text-align: center;">
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
<?php $ruta = base_url(); ?>

<table id='example' class="table table-striped dataTable table-bordered">


    <thead>
    <tr>

        <th style="text-align: center;">N° de Consolidado Guía Carga</th>
        <th style="text-align: center;">Fecha Entrega</th>
        <th style="text-align: center;">Camión</th>
        <th style="text-align: center;">Chofer</th>
        <th style="text-align: center;">Estado</th>
        <th style="text-align: center;">Accion</th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($consolidado) > 0) {

        foreach ($consolidado as $campoConsolidado) {
            ?>
            <tr>

                <td style="text-align: center;"><?= $campoConsolidado['consolidado_id'] ?></td>
                <td style="text-align: center;"><span
                        style="display: none;"><?= date('YmdHis', strtotime($campoConsolidado['fecha'])) ?></span><?= date('d-m-Y', strtotime($campoConsolidado['fecha'])) ?>
                </td>
                <td style="text-align: center;"><?= $campoConsolidado['camiones_placa'] ?></td>
                <td style="text-align: center;"><?= $campoConsolidado['nombre'] ?></td>
                <td style="text-align: center;"><?= $campoConsolidado['status'] ?></td>
                <td style="text-align: center;">
                    <?php
                    $color = 'default';
                    if ($campoConsolidado['status'] == 'CONFIRMADO')
                        $color = 'warning';
                    if ($campoConsolidado['status'] == 'CERRADO')
                        $color = 'primary';
                    if ($campoConsolidado['status'] == 'IMPRESO')
                        $color = 'other';

                    if ($campoConsolidado['status'] == "IMPRESO"){
                    $status = TRUE;
                    ?>
                    <div class="btn-group">

                        <a class="btn btn-<?= $color ?>" data-toggle="tooltip"
                           title="Ver" data-original-title="fa fa-comment-o"
                           href="#"
                           onclick="VerConsolidado(<?= $campoConsolidado['consolidado_id'] ?>,'<?= $campoConsolidado['status'] ?>'); ">
                            Liquidar
                        </a>
                        <?php } else {
                            ?>
                            <a class="btn btn-<?= $color ?>" data-toggle="tooltip"
                               title="Ver" data-original-title="fa fa-comment-o"
                               href="#"
                               onclick="VerConsolidado(<?= $campoConsolidado['consolidado_id'] ?>,'<?= $campoConsolidado['status'] ?>'); ">
                                Ver
                            </a>
                        <?php } ?>
                    </div>
                </td>
            </tr>
        <?php }
    }
    ?>

    </tbody>
</table>


<script type="text/javascript">
    TablesDatatables.init(1);
</script>
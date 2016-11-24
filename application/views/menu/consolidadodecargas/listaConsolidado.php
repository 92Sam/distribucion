<?php $ruta = base_url(); ?>

<table class="table table-striped dataTable table-bordered">


    <thead>
    <tr>

        <th>N° de Consolidado Guía Carga</th>
        <th>Fecha</th>
        <th>Camion</th>
        <th>Chofer</th>
        <th>Estado</th>
        <th>Accion</th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($consolidado) > 0) {

        foreach ($consolidado as $campoConsolidado) {
            ?>
            <tr>

                <td class="center"><?= $campoConsolidado['consolidado_id'] ?></td>
                <td><span
                        style="display: none;"><?= date('YmdHis', strtotime($campoConsolidado['fecha'])) ?></span><?= date('d-m-Y', strtotime($campoConsolidado['fecha'])) ?>
                </td>
                <td><?= $campoConsolidado['camiones_placa'] ?></td>
                <td><?= $campoConsolidado['nombre'] ?></td>
                <td><?= $campoConsolidado['status'] ?></td>
                <td class="center">
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
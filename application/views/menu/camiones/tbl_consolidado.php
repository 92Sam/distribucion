<style>
    .btn-other {
        background-color: #3b3b1f;
        color: #fff;
    }

    .b-default {
        background-color: #55c862;
        color: #fff;
    }

    .b-warning {
        background-color: #f7be64;
        color: #fff;
    }

    .b-primary {
        background-color: #2CA8E4;
        color: #fff;
    }

    .table td {
        font-weight: normal;
        font-size: 11px;
        vertical-align: middle !important;
    }

    .btn-group-sm > .btn, .btn-sm {
        font-size: 10px;
    }
</style>

<table class="table table-striped dataTable table-bordered" id="example">
    <thead>
    <tr>

        <th style="text-align: center;">N° de Consolidado Guía Carga</th>
        <th style="text-align: center;">Fecha Entrega</th>
        <th style="text-align: center;">Camión</th>
        <th style="text-align: center;">Chofer</th>
        <th style="text-align: center;">Estado</th>
        <th style="text-align: center;">Acciones</th>


    </tr>
    </thead>
    <tbody>
    <?php if (count($consolidado) > 0) {

        foreach ($consolidado as $campoConsolidado) {
            ?>
            <tr style="text-align: center;">

                <td class="center"><?= $campoConsolidado['consolidado_id'] ?></td>
                <td><span
                        style="display: none;"><?= date('YmdHis', strtotime($campoConsolidado['fecha'])) ?></span>
                    <?php if ($campoConsolidado['status'] == 'ABIERTO') { ?>
                        <input type="text" class="form-control cambiar_fecha" readonly
                               style="width: 100px; padding: 2px 2px; cursor: pointer; color: #2CA8E4; text-align: center; border: 1px solid #2CA8E4;"
                               value="<?= date('d-m-Y', strtotime($campoConsolidado['fecha'])) ?>"
                               data-id="<?= $campoConsolidado['consolidado_id'] ?>">
                    <?php } else { ?>
                        <?= date('d-m-Y', strtotime($campoConsolidado['fecha'])) ?>
                    <?php } ?>

                </td>
                <td><?= $campoConsolidado['camiones_placa'] ?></td>
                <td><?= $campoConsolidado['nombre'] ?></td>
                <td><?= $campoConsolidado['status'] ?></td>

                <td class="center">
                    <div class="btn-group">
                        <?php
                        $color = 'default';
                        if ($campoConsolidado['status'] == 'CONFIRMADO')
                            $color = 'warning';
                        if ($campoConsolidado['status'] == 'CERRADO')
                            $color = 'primary';
                        if ($campoConsolidado['status'] == 'IMPRESO')
                            $color = 'other';

                        echo '<a class="btn btn-sm btn-' . $color . '" data-toggle="tooltip"
                                            title="Consolidado Documentos" data-original-title="Consolidado Documentos"
                                            href="#" onclick="VerConsolidado(' . $campoConsolidado['consolidado_id'] . '); ">'; ?>
                        Consolidado
                        </a>
                        <?php

                        echo '<a class="btn btn-sm btn-warning" data-toggle="tooltip"
                                            title="Imprimir" data-original-title="Imprimir"
                                            href="#" onclick="alertImprimir(' . $campoConsolidado['consolidado_id'] . '); ">'; ?>
                        <i class="fa fa-print fa-fw" id="ic"></i><span
                            style="display:none;">Imprimir</span></a>
                        </a>


                    </div>

                    <input type="hidden" id="metrosc" name="metrosc"
                           value="<?php echo $campoConsolidado['metrosc'] ?>">
                </td>
            </tr>
        <?php }
    }
    ?>

    </tbody>
</table>

<script>

    var fecha_flag = true;

    $(document).ready(function () {
        TablesDatatables.init(1);

        $('.cambiar_fecha').datepicker({
            weekStart: 1,
            format: 'dd-mm-yyyy'
        });

        $('.cambiar_fecha').on('change', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (fecha_flag)
                cambiar_fecha($(this).attr('data-id'), $(this).val());
            $(this).datepicker('hide');
        });
    });


    function cambiar_fecha(id, fecha) {

        fecha_flag = false;

        $.ajax({
            url: '<?php echo base_url('consolidadodecargas/cambiar_fecha'); ?>',
            type: 'POST',
            data: {"id": id, 'fecha': fecha},
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                $("#btn_buscar").click();
            },
            complete: function () {
                fecha_flag = true;
            }
        });


    }
</script>
<style>
    .btn-other{
        background-color: #3b3b1f;
        color: #fff;
    }

    .b-default{
        background-color: #55c862;
        color: #fff;
    }
    .b-warning{
        background-color: #f7be64;
        color: #fff;
    }
    .b-primary{
        background-color: #2CA8E4;
        color: #fff;
    }
</style>

    <div class="col-md-12 text-right">
        <label class="control-label badge b-default">ABIERTO</label>
        <label class="control-label badge btn-other">IMPRESO</label>
        <label class="control-label badge b-primary">CERRADO</label>
    </div>

<table class="table table-striped dataTable table-bordered" id="example">
    <thead>
    <tr>

        <th>N° de Consolidado Guía Carga</th>
        <th>Fecha</th>
        <th>Camion</th>
        <th>Chofer</th>
        <th>Estado</th>
        <th>Acciones</th>


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
                    <div class="btn-group">
                        <?php
                        $color = 'default';
                        if ($campoConsolidado['status'] == 'CONFIRMADO')
                            $color = 'warning';
                        if ($campoConsolidado['status'] == 'CERRADO')
                            $color = 'primary';
                        if ($campoConsolidado['status'] == 'IMPRESO')
                            $color = 'other';

                        echo '<a class="btn btn-' . $color . '" data-toggle="tooltip"
                                            title="Consolidado Documentos" data-original-title="Consolidado Documentos"
                                            href="#" onclick="VerConsolidado(' . $campoConsolidado['consolidado_id'] . '); ">'; ?>
                        Consolidado
                        </a>
                        <?php

                        echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Imprimir" data-original-title="Imprimir"
                                            href="#" onclick="alertImprimir(' . $campoConsolidado['consolidado_id'] . '); ">'; ?>
                        <i class="fa fa-print fa-fw" id="ic"></i><span
                            style="display:none;">Imprimir</span></a>
                        </a>


                        <?php //if ($campoConsolidado['status'] == 'ABIERTO' || $this->session->userdata('admin') == true) { ?>
                        <?php if ($campoConsolidado['status'] == 'ABIERTO') { ?>
                            <button class="btn btn-default" data-toggle="tooltip"
                                    title="Editar" data-original-title="fa fa-comment-o"
                                    onclick="editarconsolidado(<?php echo $campoConsolidado['consolidado_id'] ?>,<?php echo $campoConsolidado['metrosc'] ?>);">
                                <i class="fa fa-edit"></i></button>
                        <?php } ?>
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
    TablesDatatables.init(1);
</script>
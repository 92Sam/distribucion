<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Cajas</li>
    <li><a href="">Agregar y Editar Cajas</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->


    <br>

    <div class="row">
        <div class="col-md-1">
            <a class="btn btn-default" id="btn_new_caja">
                <i class="fa fa-plus"> Nueva Caja</i>
            </a>
        </div>

        <div class="col-md-7"></div>

        <div class="col-md-2">
            <label>Fecha Inicial</label>
            <input type="text" id="fecha_ini" name="fecha_ini"
                   class="form-control input-datepicker"
                   value="<?= date('d-m-Y') ?>" readonly style="cursor: pointer;">
        </div>
        <div class="col-md-2">
            <label>Fecha Final</label>
            <input type="text" id="fecha_fin" name="fecha_fin"
                   class="form-control input-datepicker"
                   value="<?= date('d-m-Y') ?>" readonly style="cursor: pointer;">
        </div>
    </div>

    <ul class="nav nav-tabs">
        <?php foreach ($cajas as $caja): ?>
            <li <?= $caja->moneda_id == 1 ? 'class="active"' : '' ?>>
                <a data-toggle="tab"
                   href="#caja<?= $caja->id ?>"><?= $caja->moneda_id == 1 ? 'SOLES' : 'DOLARES' ?>
                    - <?= $caja->estado == '1' ? 'Activa' : 'Inactiva' ?></a></li>
        <?php endforeach; ?>
    </ul>

    <div class="tab-content">
        <?php foreach ($cajas as $caja): ?>
            <div
                id="caja<?= $caja->id ?>" <?= $caja->moneda_id == 1 ? 'class="tab-pane fade in active"' : 'class="tab-pane"' ?>>
                <h4>Caja de <?= $caja->moneda_id == 1 ? 'SOLES' : 'DOLARES' ?></h4>
                <h4 class="col-md-4"><label>Responsable de la caja: </label> <?= $caja->nombre ?></h4>
                <h4 class="col-md-4"><label>Importe Total Caja + Bancos: </label>
                        <?= MONEDA ?><span id="totalSaldo"></span>
                </h4>
                <div class="col-md-2">
                    <a data-caja_id="<?= $caja->id ?>" class="btn_new_caja_cuenta btn btn-default">
                        <i class="fa fa-plus"> Nueva Cuenta</i>
                    </a>
                </div>
                <div class="col-md-2">
                    <a data-id="<?= $caja->id ?>" class="btn_edit_caja btn btn-primary">
                        <i class="fa fa-edit"> Editar Caja</i>
                    </a>
                </div>
                <br>
                <br>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="example">
                        <thead>
                        <tr>
                            <th>Descripci&oacute;n</th>
                            <th>Responsable</th>
                            <th>Saldo</th>
                            <th>Cuenta Principal</th>
                            <th>Estado</th>
                            <th>Acciones</th>

                        </tr>
                        </thead>
                        <tbody>

                        <?php $totalSaldo = 0; ?>
                        <?php foreach ($caja->desgloses as $desglose): ?>
                            <?php $totalSaldo += $desglose->saldo; ?>
                            <tr>
                                <td><?= $desglose->descripcion ?></td>
                                <td><?= $desglose->nombre ?></td>
                                <td><?= MONEDA . number_format($desglose->saldo,2) ?></td>
                                <td><?= $desglose->principal == '1' ? 'SI' : 'NO' ?></td>
                                <td><?= $desglose->estado == '1' ? 'Activa' : 'Inactiva' ?></td>
                                <td align="center">
                                    <a class="btn_edit_caja_cuenta btn btn-primary"
                                       data-caja_id="<?= $caja->id ?>"
                                       data-id="<?= $desglose->id ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                <?php if($desglose->retencion == 1):?>
                                    <a class="btn_ajustar_caja_cuenta_retencion btn btn-primary"
                                       data-caja_id="<?= $caja->id ?>"
                                       data-id="<?= $desglose->id ?>">
                                        <i class="fa fa-money"></i>
                                    </a>
                                <?php else:?>
                                    <a class="btn_ajustar_caja_cuenta btn btn-warning"
                                       data-caja_id="<?= $caja->id ?>"
                                       data-id="<?= $desglose->id ?>">
                                        <i class="fa fa-exchange"></i>
                                    </a>
                                <?php endif;?>

                                    <a class="btn_detalle_caja_cuenta btn btn-default"
                                       data-id="<?= $desglose->id ?>">
                                        <i class="fa fa-search"></i>
                                    </a>

                                    <?php if(count($desglose->pendientes) > 0):?>
                                    <a class="btn_pendiente_caja_cuenta btn btn-danger"
                                       data-caja_id="<?= $caja->id ?>"
                                       data-id="<?= $desglose->id ?>">
                                        <i class="fa fa-check"></i> <?=count($desglose->pendientes)?>
                                    </a>
                                <?php endif;?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
        <input type="hidden" id="input_saldo" value="<?= number_format($totalSaldo, 2) ?>">
    </div>


</div>


<div class="modal fade" id="dialog_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="dialog_form_pendiente" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>




<script>

    $(document).ready(function () {

        setTimeout(function(){
            $("#totalSaldo").html($("#input_saldo").val());
        }, 200);


        $("#btn_new_caja").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_form')?>',
                type: 'post',
                success: function (data) {
                    $("#dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_edit_caja").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_form')?>' + '/' + $(this).attr('data-id'),
                type: 'post',
                success: function (data) {
                    $("#dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_new_caja_cuenta").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_cuenta_form')?>' + '/' + $(this).attr('data-caja_id'),
                type: 'post',
                success: function (data) {
                    $("#dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_edit_caja_cuenta").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_cuenta_form')?>' + '/' + $(this).attr('data-caja_id') + '/' + $(this).attr('data-id'),
                type: 'post',
                success: function (data) {
                    $("#dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_ajustar_caja_cuenta").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_ajustar_form')?>' + '/' + $(this).attr('data-caja_id') + '/' + $(this).attr('data-id'),
                type: 'post',
                success: function (data) {
                    $("#dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_ajustar_caja_cuenta_retencion").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_ajustar_retencion_form')?>' + '/' + $(this).attr('data-caja_id') + '/' + $(this).attr('data-id'),
                type: 'post',
                success: function (data) {
                    $("#dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });


        $(".btn_detalle_caja_cuenta").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_detalle_form')?>' + '/' + $(this).attr('data-id'),
                type: 'post',
                data: {fecha_ini: $("#fecha_ini").val(), fecha_fin: $("#fecha_fin").val()},
                success: function (data) {
                    $("#dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_pendiente_caja_cuenta").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_pendiente_form')?>' + '/' + $(this).attr('data-id'),
                type: 'post',
                success: function (data) {
                    $("#dialog_form_pendiente").html(data);
                    $("#dialog_form_pendiente").modal('show');
                }
            });
        });

        $('#dialog_form_pendiente').on('hidden.bs.modal', function (e) {
            $.ajax({
                url: '<?php echo base_url('cajas')?>',
                success: function (data) {
                    $('#page-content').html(data);
                    $(".modal-backdrop").remove();
                }
            });
        });

    });

</script>

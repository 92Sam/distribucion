<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Cajas</li>
    <li><a href="">Agregar y Editar Cajas</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->

    <a class="btn btn-default" id="btn_new_caja">
        <i class="fa fa-plus"> Nueva Caja</i>
    </a>
    <br>

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
                <h4 class="col-md-8"><label>Responsable de la caja: </label> <?= $caja->nombre ?></h4>
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

                        <?php foreach ($caja->desgloses as $desglose): ?>
                            <tr>
                                <td><?= $desglose->descripcion ?></td>
                                <td><?= $desglose->nombre ?></td>
                                <td><?= $desglose->saldo ?></td>
                                <td><?= $desglose->principal == '1' ? 'SI' : 'NO' ?></td>
                                <td><?= $desglose->estado == '1' ? 'Activa' : 'Inactiva' ?></td>
                                <td>
                                    <a class="btn_new_caja_cuenta btn btn-primary"
                                       data-caja_id="<?= $caja->id ?>"
                                       data-id="<?= $desglose->id ?>">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endforeach; ?>
    </div>


</div>


<div class="modal fade" id="dialog_form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 id="header_dialog_form" class="modal-title">Atención:</h4>
            </div>
            <div id="body_dialog_form" class="modal-body">
                <p>Debe elegir algún pedido.</p>
            </div>
            <div class="modal-footer">
                <a id="btn_save_form" href="#" class="btn btn-primary">Guardar</a>
                <a href="#" class="btn btn-warning" data-dismiss="modal">Cancelar</a>
            </div>
        </div>
    </div>
</div>

<script>

    $(document).ready(function () {

        $("#btn_new_caja").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_form')?>',
                type: 'post',
                success: function (data) {
                    $("#header_dialog_form").html('Nueva Caja');
                    $("#body_dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_edit_caja").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_form')?>' + '/' + $(this).attr('data-id'),
                type: 'post',
                success: function (data) {
                    $("#header_dialog_form").html('Editar Caja');
                    $("#body_dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_new_caja_cuenta").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_cuenta_form')?>' + '/' + $(this).attr('data-caja_id'),
                type: 'post',
                success: function (data) {
                    $("#header_dialog_form").html('Nueva Cuenta de Caja');
                    $("#body_dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

        $(".btn_new_caja_cuenta").on('click', function () {
            $.ajax({
                url: '<?php echo base_url('cajas/caja_cuenta_form')?>' + '/' + $(this).attr('data-caja_id') + '/' + $(this).attr('data-id'),
                type: 'post',
                success: function (data) {
                    $("#header_dialog_form").html('Editar Cuenta de Caja');
                    $("#body_dialog_form").html(data);
                    $("#dialog_form").modal('show');
                }
            });
        });

    });

</script>



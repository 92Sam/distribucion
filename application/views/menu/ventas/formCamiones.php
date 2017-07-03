<form name="formcamion" method="post" id="formcamion" action="<?= base_url() ?>consolidadodecargas/guardar">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Agregar pedidos a camiones</h4>
            </div>

            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-md-3">
                        Camiones
                    </div>
                    <div class="col-md-6">
                        <select id="camion" class="form-control campos" name="camion">
                            <option value="" data-nombre="" data-disponible="" data-metro_camion="0"> SELECCIONE
                            </option>
                            <?php foreach ($camiones as $camion): ?>
                                <option value="<?= $camion['camiones_id']; ?>"
                                        data-nombre="<?= $camion['nombre'] ?>"
                                        data-disponible="<?= $camion['consolidado_estado'] == 'ABIERTO' ? 0 : 1 ?>"
                                        data-metro_camion="<?= $camion['metros_cubicos'] ?>"
                                >

                                    <?= $camion['camiones_placa'] ?> ------ <?= $camion['metros_cubicos'] ?> Metros
                                    cúbicos
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <?php

                for ($i = 0; $i < count($pedidos); $i++) {
                    ?>
                    <input type="hidden" name="pedidos[]" value="<?= $pedidos[$i]; ?>">
                <?php } ?>
                <input type="hidden" name="metroscamion" id="metroscamion"
                       class="form-control" readonly="readonly" <?php if (isset($consolidado)) { ?>
                    value="<?= $consolidado[0]['metros_cubicos'] ?>"
                <?php } ?>>

                <div class="form-group row">
                    <div class="col-md-3">
                        Datos del chofer:
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="datos_chofer" id="datos_chofer"
                               class="form-control" readonly="readonly" value="">
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3">
                        Total Metros Cúbicos:
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="metros" id="metros" required="true"
                               class="form-control" readonly="readonly" value="<?php echo $metros ?>"
                               style="width:50px;">
                    </div>

                    <div class="col-md-2">
                        Bultos: <span id="total_bultos"><?= $total_bultos ?></span>
                    </div>

                    <div class="col-md-2">
                        Pedidos: <span id="total_pedidos"><?= count($pedidos) ?></span>
                    </div>
                </div>
                <div class="form-group row">
                    <div class="col-md-3">
                        Fecha de entrega
                    </div>
                    <div class="col-md-4">
                        <input type="text" name="fecha_consolidado" id="fecha_consolidado"
                               value="<?php if (isset($consolidado)) {
                                   echo date('d-m-Y', strtotime($consolidado[0]['fecha']));
                               } else {
                                   date('d-m-Y');
                               } ?>" required="true"
                               class="form-control fecha campos input-datepicker " readonly="readonly">
                    </div>

                </div>
            </div>
            <?php
            if (isset($consolidado)) {
                ?>
                <input type="hidden" name="id_consolidado" id="id_consolidado"
                       value="<?= $consolidado[0]['consolidado_id'] ?>">

            <?php }

            ?>
            <div class="modal-footer">
                <button type="button" id="btnconfirmar" class="btn btn-primary" onclick="grupo.guardar()">
                    <li class="glyphicon glyphicon-thumbs-up"></li>
                    Confirmar
                </button>
                <button type="button" class="btn btn-warning" data-dismiss="modal">
                    <li class="glyphicon glyphicon-thumbs-down"></li>
                    Cancelar
                </button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</form>
<script type="text/javascript">
    $("#fecha_consolidado").datepicker({todayHighlight: true});
    $(function () {

        $("#camion").on("change", function () {
            var id_camion = $("#camion").val();
            var nombre = $("#camion option:selected").attr('data-nombre');
            var disponible = $("#camion option:selected").attr('data-disponible') == 1 ? 'Disponible' : 'No disponible';
            var metros_camion = $("#camion option:selected").attr('data-metro_camion');

            $('#metroscamion').val(metros_camion);
            if (id_camion != '')
                $("#datos_chofer").val(nombre + ' - ' + disponible);
            else
                $("#datos_chofer").val('');

        });
    });
</script>


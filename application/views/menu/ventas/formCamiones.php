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
                                <div class="col-md-9">
                                    <select id="camion" class="form-control campos" name="camion">
                                    <option value=""> SELECCIONE</option>
                                   <?php
                                        foreach ($camiones as $camion) {
                                            ?>
                                            <option
                                                value="<?= $camion['camiones_id']; ?>" <?php if (isset($consolidado) and $camion['camiones_id'] == $consolidado[0]['camion']) {
                                                echo "selected";
                                            } ?>> <?= $camion['camiones_placa'] ?>------
                                            <?= $camion['metros_cubicos'] ?> Metros cúbicos
                                        </option>
                                        <?php } ?>
                                </select>
                                </div>
                            </div>
                                   <?php

                                   for ($i = 0; $i < count($pedidos); $i++) {
                                            ?>
                                       <input type="hidden" name="pedidos[]" id="pedidos" value="<?= $pedidos[$i]; ?>">

                                   <?php } ?>
                                   <input type="hidden" name="metroscamion" id="metroscamion"
                                    class="form-control" readonly="readonly"
                                    style="width:50px;" <?php if (isset($consolidado)) { ?>
                                       value="<?= $consolidado[0]['metros_cubicos'] ?>"
                                   <?php } ?>>
                             <div class="form-group row">
                                 <div class="col-md-3">
                                     Total Metros Cúbicos:
                                </div>
                                 <div class="col-md-9">
                                   <input type="text" name="metros" id="metros" required="true"
                                    class="form-control" readonly="readonly" value="<?php echo $metros?>"
                                    style="width:50px;">
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
                                           class="form-control fecha campos input-datepicker ">
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
                            <button type="button" id="btnconfirmar" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                </form>
                <script type="text/javascript">
                    $("#fecha_consolidado").datepicker({todayHighlight: true});
                $(function() {
                    $("#camion").on("change",function() {
                       var id_camion = $("#camion").val();
                        $.ajax({
                        url:  '<?php echo base_url()?>venta/obtenerMetros',
                        type: 'POST',
                        data: "id_camion="+id_camion,

                        success:function(data){

                            $('#metroscamion').val(data);
                        }
                        });
                       // var capacidad = $("#capacidadcamion").val();
                        //$("#capacidad").val(capacidad);
                    });
                 });
                </script>
       

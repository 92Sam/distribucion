<form name="formagregar" action="<?= base_url() ?>zona/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($zona->zona_id)) echo $zona->zona_id ;?>">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Zona</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Pa&iacute;s</label>
                        </div>
                        <div class="col-md-9">
                            <select name="id_pais" id="id_pais" required="true" class="form-control"
                                    onchange="region.actualizarestados();">
                                <option value="">Seleccione</option>
                                <?php foreach ($paises as $pais): ?>
                                    <option
                                        value="<?php echo $pais['id_pais'] ?>" <?php if (isset($zona->id_pais) and $pais['id_pais'] == $zona->id_pais) echo 'selected' ?>><?= $pais['nombre_pais'] ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Ciudad</label>
                        </div>
                        <div class="col-md-9">

                            <select name="estado_id" ID="estado_id" required="true" class="form-control"
                                    onchange="region.actualizardistritos();">
                                <option value="">Seleccione</option>
                                <?php if (isset($zona->zona_id)): ?>
                                    <?php foreach ($estados as $estado): ?>
                                        <option
                                            value="<?php echo $estado['estados_id'] ?>" <?php if (isset($zona->estados_id) and $estado['estados_id'] == $zona->estados_id) echo 'selected' ?>><?= $estado['estados_nombre'] ?></option>
                                    <?php endforeach ?>
                                <?php endif ?>
                            </select>

                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Distrito</label>
                        </div>
                        <div class="col-md-9">

                            <select name="ciudad_id" ID="ciudad_id" required="true" class="form-control">
                                <option value="">Seleccione</option>
                                <?php if (isset($zona->zona_id)){ ?>
                                    <?php foreach ($ciudades as $ciudad): ?>
                                        <option
                                            value="<?php echo $ciudad['ciudad_id'] ?>" <?php if (isset($zona->ciudad_id) and $ciudad['ciudad_id'] == $zona->ciudad_id) echo 'selected' ?>><?= $ciudad['ciudad_nombre'] ?></option>
                                    <?php endforeach ?>
                                <?php } ?>
                            </select>

                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Zona</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" name="zona_nombre" id="zona_nombre" required="true"
                                   class="form-control"
                                   value="<?php if (isset($zona->zona_nombre)) echo $zona->zona_nombre; ?>">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Zona D&iacute;as Semana</label>
                        </div>
                        <div class="col-md-9">
                            <table class="table table-bordered table-condensed">
                                <tr style="background-color:#55c862;">
                                    <th><label class="label remove-padding ">Lunes</th>
                                    <th><label class="label remove-padding">Martes</th>
                                    <th><label class="label remove-padding">Miercoles</th>
                                    <th><label class="label remove-padding">Jueves</th>
                                    <th><label class="label remove-padding">Viernes</th>
                                    <th><label class="label remove-padding">S&aacute;bado</th>
                                    <th><label class="label remove-padding">Domingo</th>
                                </tr>
                                <tr>
                                    <td id=clunes align="center"><input type="checkbox" name="zonadias[]" value="1" id="zlunes" onclick="paintcell('lunes')"></td>
                                    <td id=cmartes align="center"><input type="checkbox" name="zonadias[]" value="2" id="zmartes" onclick="paintcell('martes')"></td>
                                    <td id=cmiercoles align="center"><input type="checkbox" name="zonadias[]" value="3" id="zmiercoles" onclick="paintcell('miercoles')"></td>
                                    <td id=cjueves align="center"><input type="checkbox" name="zonadias[]" value="4" id="zjueves" onclick="paintcell('jueves')"></td>
                                    <td id=cviernes align="center"><input type="checkbox" name="zonadias[]" value="5" id="zviernes" onclick="paintcell('viernes')"></td>
                                    <td id=csabado align="center"><input type="checkbox" name="zonadias[]" value="6" id="zsabado" onclick="paintcell('sabado')"></td>
                                    <td id=cdomingo align="center"><input type="checkbox" name="zonadias[]" value="7" id="zdomingo" onclick="paintcell('domingo')"></td>

                                    <?php if (isset($zona->zona_id)){ ?>
                                        <?php $count = count($dias);?>
                                        <?php for ($i=0; $i<$count; $i++) {?>
                                            <?php foreach ($dias[$i] as $dia): ?>
                                                <?php
                                                switch($dia)
                                                {
                                                    case 1: ?>
                                                        <script>
                                                            //document.getElementById("zlunes").checked = true;
                                                            $("#zlunes").prop('checked', true);
                                                            $("#clunes").attr('bgcolor', "#adff2f");
                                                        </script>
                                                        <?php break;
                                                    case 2: ?>
                                                        <script>
                                                            $("#zmartes").prop('checked', true);
                                                            $("#cmartes").attr('bgcolor', "#adff2f");
                                                        </script>
                                                        <?php break;
                                                    case 3: ?>
                                                        <script>
                                                            $("#zmiercoles").prop('checked', true);
                                                            $("#cmiercoles").attr('bgcolor', "#adff2f");
                                                        </script>
                                                        <?php break;
                                                    case 4: ?>
                                                        <script>
                                                            $("#zjueves").prop('checked', true);
                                                            $("#cjueves").attr('bgcolor', "#adff2f");
                                                        </script>
                                                        <?php break;
                                                    case 5: ?>
                                                        <script>
                                                            $("#zviernes").prop('checked', true);
                                                            $("#cviernes").attr('bgcolor', "#adff2f");
                                                        </script>
                                                        <?php break;
                                                    case 6: ?>
                                                        <script>
                                                            $("#zsabado").prop('checked', true);
                                                            $("#csabado").attr('bgcolor', "#adff2f");
                                                        </script>
                                                        <?php break;
                                                    case 7: ?>
                                                        <script>
                                                            $("#zdomingo").prop('checked', true);
                                                            $("#cdomingo").attr('bgcolor', "#adff2f");
                                                        </script>
                                                        <?php break;
                                                }
                                                ?>
                                            <?php endforeach ?>
                                        <?php };?>
                                    <?php };?>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label>Urbanizaciones</label>
                        </div>
                        <div class="col-md-9">
                            <input type="textarea" name="urb" id="urb" required="true"
                                   class="form-control"
                                   value="<?php if (isset($zona->urb)) echo $zona->urb; ?>">
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
            </div>
        </div>
        <!-- /.modal-content -->
</form>

<script type="text/javascript">

    function paintcell(cell) {

        var clr_green_yellow = "#adff2f";
        var clr_white = "#ffffff";

        if ($("#z"+cell).prop('checked')) {
            //alert('check' + ' c'+cell);
            $("#c"+cell).attr('bgcolor', clr_green_yellow);
        } else {
            //alert('uncheck' + ' c'+cell);
            $("#c"+cell).attr('bgcolor', clr_white);
        }
    }

</script




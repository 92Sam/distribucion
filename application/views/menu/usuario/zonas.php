<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Zonas </h4>
        </div>
        <div class="modal-body">
            <table class='table table-striped table-media dataTable table-bordered'>
                <thead>
                <tr>
                    <th>Ver</th>
                    <th>Zona</th>
                    <th>Distrito</th>
                    <th>Ciudad</th>
                    <th>Pais</th>
                </tr>
                </thead>
                <tbody>
                <?php if (count($usuario_has_zona) > 0){ ?>
                    <?php foreach ($usuario_has_zona as $zona): ?>
                        <tr style="">
                            <td><input type="radio" name="name<?php echo $zona['id_zona']; ?>"
                                       id="id<?php echo $zona['id_zona']; ?>"
                                       value="<?php echo $zona['id_zona']; ?>"
                                       class="radios"></td>
                            <td><?php echo $zona['zona_nombre']; ?></td>
                            <td><?php echo $zona['ciudad_nombre']; ?></td>
                            <td><?php echo $zona['estados_nombre']; ?></td>
                            <td><?php echo $zona['nombre_pais']; ?></td>

                        </tr>

                    <?php endforeach;
                } ?>
                <tr>
                    <table class="table table-bordered table-condensed">
                        <tr style="background-color:#55c862;">
                            <th><label class="label remove-padding">Lunes</th>
                            <th><label class="label remove-padding">Martes</th>
                            <th><label class="label remove-padding">Miercoles</th>
                            <th><label class="label remove-padding">Jueves</th>
                            <th><label class="label remove-padding">Viernes</th>
                            <th><label class="label remove-padding">S&aacute;bado</th>
                            <th><label class="label remove-padding">Domingo</th>
                        </tr>
                        <tr>
                            <td id=clunes align="center"><input type="checkbox" name="zonadias[]" value="1" id="zlunes" disabled="true"></td>
                            <td id=cmartes align="center"><input type="checkbox" name="zonadias[]" value="2" id="zmartes" disabled="true"></td>
                            <td id=cmiercoles align="center"><input type="checkbox" name="zonadias[]" value="3" id="zmiercoles" disabled="true"></td>
                            <td id=cjueves align="center"><input type="checkbox" name="zonadias[]" value="4" id="zjueves" disabled="true"></td>
                            <td id=cviernes align="center"><input type="checkbox" name="zonadias[]" value="5" id="zviernes" disabled="true"></td>
                            <td id=csabado align="center"><input type="checkbox" name="zonadias[]" value="6" id="zsabado" disabled="true"></td>
                            <td id=cdomingo align="center"><input type="checkbox" name="zonadias[]" value="7" id="zdomingo" disabled="true"></td>
                        </tr>
                    </table>
                </tr>
                </tbody>
            </table>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
        </div>
    </div>
    <!-- /.modal-content -->
</div>

<script type="text/javascript">

    var zona_dias = [];

    <?php foreach ($zona_dias as $zdia): ?>
    zona_dias.push({
        'id_zona': '<?=$zdia['id_zona']?>',
        'dia_semana': '<?=$zdia['dia_semana']?>',
    });
    <?php endforeach; ?>

    $(document).ready(function () {

        $(".radios").on('change', function () {
            var id_zona = $(this).attr('value');
            //alert(id_zona);

            $(".radios").each(function () {
                if ($(this).attr('value') != id_zona) {
                    $(this).attr('checked', false);
                }
            });

            var clr_green_yellow = "#adff2f";

            uncheckAll();

            for (var i= 0; i < zona_dias.length; i++) {
                if (zona_dias[i].id_zona == id_zona) {
                    var dia = parseInt(zona_dias[i].dia_semana);
                    switch (dia) {
                        case 1:
                            $("#zlunes").prop('checked', true);
                            $("#clunes").attr('bgcolor', clr_green_yellow);
                            break;

                        case 2:
                            $("#zmartes").prop('checked', true);
                            $("#cmartes").attr('bgcolor', clr_green_yellow);
                            break;

                        case 3:
                            $("#zmiercoles").prop('checked', true);
                            $("#cmiercoles").attr('bgcolor', clr_green_yellow);
                            break;

                        case 4:
                            $("#zjueves").prop('checked', true);
                            $("#cjueves").attr('bgcolor', clr_green_yellow);
                            break;

                        case 5:
                            $("#zviernes").prop('checked', true);
                            $("#cviernes").attr('bgcolor', clr_green_yellow);
                            break;

                        case 6:
                            $("#zsabado").prop('checked', true);
                            $("#csabado").attr('bgcolor', clr_green_yellow);
                            break;

                        case 7:
                            $("#zdomingo").prop('checked', true);
                            $("#cdomingo").attr('bgcolor', clr_green_yellow);
                            break;
                    }
                }
            }
        });


        function uncheckAll()
        {
            var clr_white = "#ffffff";

            $("#zlunes").prop('checked', false);
            $("#clunes").attr('bgcolor', clr_white);

            $("#zmartes").prop('checked', false);
            $("#cmartes").attr('bgcolor', clr_white);

            $("#zmiercoles").prop('checked', false);
            $("#cmiercoles").attr('bgcolor', clr_white);

            $("#zjueves").prop('checked', false);
            $("#cjueves").attr('bgcolor', clr_white);

            $("#zviernes").prop('checked', false);
            $("#cviernes").attr('bgcolor', clr_white);

            $("#zsabado").prop('checked', false);
            $("#csabado").attr('bgcolor', clr_white);

            $("#zdomingo").prop('checked', false);
            $("#cdomingo").attr('bgcolor', clr_white);
        }

    });

</script>
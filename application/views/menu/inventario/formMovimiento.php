<?php $ruta = base_url(); ?>
<style>
    #tblresult th {
        font-size: 11px !important;
        padding: 2px 2px;
        text-align: center;
        vertical-align: middle;
    }

    #tblresult td {
        font-size: 10px !important;
    }
</style>
<form name="formagregar" action="<?php echo $ruta; ?>inventario/guardar" method="post">
    <input id="maximahidden" type="hidden">

    <div class="modal-dialog" style="width: 95%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">
                    Movimiento de Inventario
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4"><label class="control-label">Descripcion:</label> <?=$producto->nombre?></div>
                    <div class="col-md-4"><label class="control-label">Unidad de Medida:</label> <?=$producto->um_nombre?></div>
                    <div class="col-md-4"><label class="control-label">Periodo:</label> <?=$periodo?></div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered table-condensed" id="tblresult">
                        <thead>
                        <tr>
                            <th rowspan="2">Fecha</th>
                            <th rowspan="2">Tipo</th>
                            <th rowspan="2">Serie</th>
                            <th rowspan="2">N&uacute;mero</th>
                            <th rowspan="2">Referencia</th>
                            <th rowspan="2">UM</th>
                            <th rowspan="2">Tipo de Operación</th>
                            <th colspan="3" style="text-align: center;">Entradas</th>
                            <th colspan="3" style="text-align: center;">Salidas</th>
                            <th colspan="3" style="text-align: center;">Saldo Final</th>
                        </tr>
                        <tr>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Costo Total</th>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Costo Total</th>
                            <th>Cantidad</th>
                            <th>Costo Unit.</th>
                            <th>Costo Final</th>
                        </tr>
                        </thead>
                        <tbody id="columnas">
                        <tr>
                            <td></td>
                            <td>OTROS</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td><?= $kardex['inicial'] != NULL ? $kardex['inicial']->nombre_unidad : $kardex['fiscal'][0]->nombre_unidad ?></td>
                            <td>SALDO ANTERIOR</td>

                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>

                            <td><?= $kardex['inicial'] != NULL ? $kardex['inicial']->cantidad_final : 0 ?></td>
                            <td style="white-space: nowrap;"><?= $kardex['inicial'] != NULL ? MONEDA . ' ' . number_format($kardex['inicial']->costo_unitario_final, 2) : MONEDA . ' 0.00' ?></td>
                            <td style="white-space: nowrap;"><?= $kardex['inicial'] != NULL ? MONEDA . ' ' . number_format($kardex['inicial']->total_final, 2) : MONEDA . ' 0.00' ?></td>
                        </tr>
                        <?php if (count($kardex['fiscal']) > 0): ?>
                            <?php foreach ($kardex['fiscal'] as $detalle): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($detalle->fecha)) ?></td>
                                    <?php $tipo_doc = get_tipo_doc($detalle->tipo_doc) ?>
                                    <td><?= $tipo_doc['value'] ?></td>
                                    <td><?= $detalle->serie ?></td>
                                    <td><?= $detalle->numero ?></td>
                                    <td><?= $detalle->referencia ?></td>
                                    <td><?= $detalle->nombre_unidad ?></td>
                                    <?php $tipo_operacion = get_tipo_operacion($detalle->tipo_operacion) ?>
                                    <td><?= $tipo_operacion['value'] ?></td>
                                    <?php if ($detalle->IO == 1): ?>
                                        <td><?= $detalle->cantidad ?></td>
                                        <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario, 2) ?></td>
                                        <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total, 2) ?></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    <?php endif; ?>
                                    <?php if ($detalle->IO == 2): ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><?= $detalle->cantidad ?></td>
                                        <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario, 2) ?></td>
                                        <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total, 2) ?></td>
                                    <?php endif; ?>
                                    <td><?= $detalle->cantidad_final ?></td>
                                    <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->costo_unitario_final, 2) ?></td>
                                    <td style="white-space: nowrap;"><?= MONEDA . ' ' . number_format($detalle->total_final, 2) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <a href="<?= $ruta ?>inventario/pdfKardex" id="generarpdf" class="btn  btn-danger btn-lg"
                   data-toggle="tooltip" title="Exportar a PDF"
                   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
                <a href="<?= $ruta ?>inventario/excelKardex/" class="btn btn-default btn-lg" data-toggle="tooltip"
                   title="Exportar a Excel"
                   data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

                <div class="modal-footer">
                    <button type="button" class="btn btn-warning" data-dismiss="modal">Salir</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>


</form>

<script type="text/javascript">

    $(function () {

        $('#tablaresult').dataTable({
            "order": [[0, "desc"]]
        });
        $("#fecha").datepicker({format: 'dd-mm-yyyy'});


        $("#select").chosen({
            placeholder: "Seleccione el producto",
            allowClear: true
        });
        $("#locales_in").chosen({
            placeholder: "Seleccione el producto",
            allowClear: true
        });
        $('#select').on("change", function () {
            if ($(this).val() != "seleccione") {
                $("#maxima").remove();
                $("#minima").remove();
                $.ajax({
                    url: '<?= base_url()?>inventario/get_unidades_has_producto',
                    type: 'POST',
                    headers: {
                        Accept: 'application/json'
                    },
                    data: {'id_producto': $(this).val()},
                    success: function (data) {

                        $("#fraccion").attr('max', data.unidades[0].unidades);
                        $("#existencia").css("display", "block");
                        $("#cantidad").val("");
                        $("#fraccion").val("");
//data.unidades[data.unidades.length -1].unidades
                        $("#unidad_maxima").append("<div id='maxima'><div class='col-md-5'> Unidad Maxima " + data.unidades[0].nombre_unidad + "</div></div> ");
                        $("#unidad_minima").append("<div id='minima'><div class='col-md-5'> Unidad Minima " + data.unidades[data.unidades.length - 1].nombre_unidad + "</div></div> ");

                        $("#maximahidden").val(data.unidades[0].nombre_unidad);


                    }
                })
            }
        });


    });
    function remover(id) {

        $("#" + id).remove();

    }


    function add_productos() {
        $("#tablaresult").css("display", "block");
        ///var table = $('#tablaresult').DataTable();


        var maxima = $("#maximahidden");
        var fraccion = $("#fraccion");
        var cantidad = $("#cantidad");

        var id = $("#select").val();
        var nombre = $("#select option:selected").html();
        if (id != "seleccione" && $("#cantidad").val() != "") {

            $("#columnas").append('<tr id="' + id + '"><td class="center" width="10%">' + id + '<input type="hidden" name="id_producto[]" value="' + id + '"> </td>' +
                '<td class="center" width="40%">' + nombre + '<input type="hidden" name="nombre_producto[]" value="' + nombre + '"></td>' +
                '<td width="20%" id="unidad_medida_td"' + id + '">' + maxima.val() + '</td><td width="10%">' + cantidad.val() + '<input type="hidden" name="cantidad_producto[]" value="' + cantidad.val() + '"></td>' +
                '<td width="10%">' + fraccion.val() + '<input type="hidden" name="fraccion_producto[]" value="' + fraccion.val() + '"></td>' +
                '<td> <div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Remover" data-original-title="Remover" onclick="remover(' + id + ')"> <i class="fa fa-trash-o"></i> </a></div></td>' +
                '</tr>');
            cantidad.val('');
            fraccion.val('');

        }

    }
</script>

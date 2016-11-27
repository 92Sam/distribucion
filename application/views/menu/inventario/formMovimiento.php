<?php $ruta = base_url(); ?>
<form name="formagregar" action="<?php echo $ruta; ?>inventario/guardar" method="post">
    <input id="maximahidden" type="hidden">

    <div class="modal-dialog" style="width: 85%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Movimiento de Inventario</h4>
            </div>
            <div class="modal-body">
                <?php if ($local == "TODOS") { ?>
                    TODOS
                <?php } else {
                    echo $local['local_nombre'];
                    $local = $local['int_local_id'];
                } ?>
                <br>

                <div class="table-responsive">
                    <table class="table table-striped dataTable table-bordered table-condensed" id="tablaresult">
                        <thead>
                        <tr>

                            <th>Fecha</th>
                            <th>Tipo</th>
                            <th>Serie</th>
                            <th>N&uacute;mero</th>
                            <th>Tipo de Operación</th>
                            <th>Estado</th>
                            <th>Unidad de Medida</th>
                            <th>Entrada Cantidad</th>
                            <th>Entrada Costo Unitario</th>
                            <th>Entrada Costo Total</th>
                            <th>Salida Cantidad</th>
                            <th>Salida Costo Unitario</th>
                            <th>Salida Costo Total</th>
                            <th>SF Cantidad</th>
                            <th>SF Costo Unitario</th>
                            <th>SF Costo Final</th>

                            <?php if (!isset($operacion)) { ?>
                                <th>Cliente</th>
                                <th>Proveedor</th>
                                <?php
                            }
                            ?>

                        </tr>
                        </thead>
                        <tbody id="columnas">

                        <?php if (count($kardex) > 0) {

                            foreach ($kardex as $arreglo) {

                                ?>
                                <tr>
                                    <td><span
                                            style="display: none"><?= date('YmdHis', strtotime($arreglo['dkardexFecha'])) ?></span><?= date('d-m-Y H:i', strtotime($arreglo['dkardexFecha'])) ?>
                                    </td>

                                    <?php if ($operacion=='FISCAL' && ($arreglo['cKardexOperacion'] == "VENTA")) { ?>
                                        <td><?= $arreglo['cKardexTipoDocumentoFiscal'] ?></td>
                                        <td><?= $arreglo['cKardexNumeroSerieFiscal'] ?></td>
                                        <td><?= $arreglo['cKardexNumeroDocumentoFiscal'] ?></td>
                                        <?php
                                    } else { ?>
                                        <td><?= $arreglo['cKardexTipoDocumento'] ?></td>
                                        <td><?= $arreglo['cKardexNumeroSerie'] ?></td>
                                        <td><?= $arreglo['cKardexNumeroDocumento'] ?></td>
                                        <?php
                                    }
                                    ?>

                                    <td><?= $arreglo['cKardexTipo'] ?></td>
                                    <td><?= $arreglo['cKardexEstado'] ?></td>
                                    <td><?= $arreglo['nombre_unidad'] ?></td>

                                    <?php if ($arreglo['cKardexTipo'] == "ENTRADA") { ?>
                                        <td><?= $arreglo['nKardexCantidad'] ?></td>
                                        <td><?= $arreglo['nKardexPrecioUnitario'] ?></td>
                                        <td><?= $arreglo['nKardexPrecioTotal'] ?></td>
                                        <?php
                                    } else { ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <?php
                                    }
                                    ?>

                                    <?php if ($arreglo['cKardexTipo'] == "SALIDA") { ?>
                                        <td><?= $arreglo['nKardexCantidad'] ?></td>
                                        <td><?= $arreglo['nKardexPrecioUnitario'] ?></td>
                                        <td><?= $arreglo['nKardexPrecioTotal'] ?></td>
                                        <?php
                                    } else { ?>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <?php
                                    }
                                    ?>

                                    <td><?= $arreglo['stockUMactual'] ?>  </td>
                                    <td><?= $arreglo['nKardexPrecioUnitario'] ?></td>
                                    <td><?= $arreglo['nKardexPrecioTotal'] ?></td>

                                    <?php if (!isset($operacion) && ($arreglo['cKardexOperacion'] == VENTA)) { ?>
                                        <td><?= $arreglo['razon_social'] ?></td>
                                        <td></td>
                                        <?php
                                    } elseif (!isset($operacion) && ($arreglo['cKardexOperacion'] == INGRESO)) { ?>
                                        <td></td>
                                        <td><?= $arreglo['proveedor_nombre'] ?></td>
                                    <?php } elseif (!isset($operacion) && ($arreglo['cKardexOperacion'] == AJUSTE_INVENTARIO)) { ?>
                                        <td></td>
                                        <td><?= $this->session->userdata('EMPRESA_NOMBRE'); ?></td>
                                        <?php
                                    }

                                    ?>
                                </tr>

                                <?php
                            }
                        }
                        ?>

                        </tbody>
                    </table>
                </div>
                <a href="<?= $ruta ?>inventario/pdfKardex/<?= $producto ?>/<?= $local ?><?php if (isset($operacion)) {
                    echo "/" . $documento_fiscal = true;
                } ?>" id="generarpdf" class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
                   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
                <a href="<?= $ruta ?>inventario/excelKardex/<?= $producto ?>/<?= $local ?><?php if (isset($operacion)) {
                    echo "/" . $documento_fiscal = true;
                } ?>" class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
                   data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

                <div class="modal-footer">
                    <input type="button" id="" class="btn btn-primary" value="Confirmar" data-dismiss="modal">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

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
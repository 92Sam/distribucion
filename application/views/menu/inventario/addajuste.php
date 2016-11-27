<?php $ruta = base_url(); ?>
<form name="formagregar" action="<?php echo $ruta; ?>inventario/guardar" method="post" id="formagregar">
<input id="maximahidden" type="hidden">

    <div class="modal-dialog" style="width: 70%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Ajustar Inventario</h4>
            </div>
            <div class="modal-body">
                <div class="form-group row">
                    <div class="col-md-2">
                        Fecha
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="fecha" id="fecha" required="true" class="form-control"
                               value="<?php if (isset($grupo['nombre_grupo'])) echo $grupo['nombre_grupo']; ?>">
                    </div>
                    <div class="col-md-2">
                        Ubicaci&oacute;n
                    </div>
                    <div class="col-md-6">
                        <select id="locales_in"  name="local" style="width:250px">
                            <option value="seleccione"> Seleccione</option>
                            <?php if (count($locales) > 0) {
                                foreach ($locales as $local) {
                                    ?>
                                    <option selected
                                            value="<?= $local['int_local_id']; ?>;"> <?= $local['local_nombre'] ?> </option>

                                <?php }
                            } ?>

                        </select>
                    </div>


                </div>

                <div class="form-group row">
                    <div class="col-md-2">
                        Descripci&oacute;n
                    </div>
                    <div class="col-md-10">
                        <input type="text" name="descripcion" id="" required="true" class="form-control"
                               value="">
                    </div>

                </div>


                <div class="form-group row">
                    <div class="col-md-2">
                        Buscar Producto
                    </div>
                    <div class="col-md-10">
                        <select id="select" style="width: 100%">
                            <option value="seleccione"> Seleccione el Producto</option>
                            <?php if (count($productos) > 0) {
                                $i = 0;
                                foreach ($productos as $producto) {
                                    ?>

                                    <option class="opciones" value="<?= $producto['producto_id'] ?>">
                                      <?= sumCod($producto['producto_id'])?> - <?= $producto['producto_nombre'] ?></option>


                                <?php }
                            } ?>

                        </select>
                    </div>

                </div>


                <div class="" id="existencia" style="display: none">

                    <div id="div_existencia">
                        <div class="form-group row">
                            <div class="col-md-2">
                                Cantidad
                            </div>
                            <div class="col-md-5">
                                <input type="number" min="0" onkeydown="return soloDecimal(this, event);"
                                       name="cantidad" id="cantidad"
                                       class="form-control"
                                       value="0">
                            </div>
                            <div id="unidad_maxima"></div>

                        </div>


                        <div class="form-group row">
                            <div class="col-md-2">
                                Fracci&oacute;n
                            </div>
                            <div class="col-md-5">
                                <input type="number" min="0" onkeydown="return soloDecimal(this, event);"
                                       name="fraccion" id="fraccion"
                                       class="form-control"
                                       value="0">
                            </div>
                            <div id="unidad_minima"></div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-2">
                                Costo de unidad maxima
                            </div>
                            <div class="col-md-5">
                                <input type="number" min="0" onkeydown="return soloDecimal(this, event);"
                                       name="costoUnitario" id="costoUnitario"
                                       class="form-control"
                                       value="<?php if (isset($producto['costo_unitario'])) echo $producto['costo_unitario']; ?>">
                            </div>
                        </div>

                        <div class="form-group right">
                            <input type="button" id="" onclick="add_productos()" class="btn btn-default"
                                   value="Agregar">


                        </div>
                    </div>
                </div>


                <br>

                <div class="table-responsive">
                    <table class="table dataTable" id="tablaresult" style="display: none; width: 100%">
                        <thead>
                        <tr>

                            <th>C&oacute;digo</th>
                            <th>Nombre</th>
                            <th>UM</th>
                            <th>Cantidad</th>
                            <th>Fraccion</th>
                            <th>Costo de unidad maxima</th>

                            <th class="desktop">Acciones</th>

                        </tr>
                        </thead>
                        <tbody id="columnas">


                        </tbody>
                    </table>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">Confirmar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
            <!-- /.modal-content -->
        </div>


</form>


<script type="text/javascript">

    $(function () {
        $("#fecha").datepicker();


        $("#select").chosen({
            width: "100%"
        });
        $("#locales_in").chosen({
            width: "100%"
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
                        $("#cantidad").val("0");
                        $("#fraccion").val("0");
                        $("#costoUnitario").val("");
//data.unidades[data.unidades.length -1].unidades
                        $("#unidad_maxima").append("<div id='maxima'><div class='col-md-5'> Unidad Maxima " + data.unidades[0].nombre_unidad + "</div></div> ");
                        $("#unidad_minima").append("<div id='minima'><div class='col-md-5'> Unidad Minima " + data.unidades[data.unidades.length - 1].nombre_unidad + "</div></div> ");

                        $("#maximahidden").val( data.unidades[0].nombre_unidad);


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


        var maxima=$("#maximahidden");
        var fraccion=$("#fraccion");
        var cantidad=$("#cantidad");
        var costo_unitario = $("#costoUnitario");

        var id = $("#select").val();
        var nombre = $("#select option:selected").html();
        if (id != "seleccione" && $("#cantidad").val() != "") {

            if ($("#cantidad").val() != "" && $("#fraccion").val() != "") {

                $("#columnas").append('<tr id="' + id + '"><td class="center" width="10%">' + id + '<input type="hidden" name="id_producto[]" value="' + id + '"> </td>' +
                    '<td class="center" width="40%">' + nombre + '<input type="hidden" name="nombre_producto[]" value="' + nombre + '"></td>' +
                    '<td width="20%" id="unidad_medida_td"' + id + '">' + maxima.val() + '</td><td width="10%">' + cantidad.val() + '<input type="hidden" name="cantidad_producto[]" value="' + cantidad.val() + '"></td>' +
                    '<td width="10%">' + fraccion.val() + '<input type="hidden" name="fraccion_producto[]" value="' + fraccion.val() + '"></td>' +
                    '<td width="10%">' + costo_unitario.val() + '<input type="hidden" name="costo_unitario[]" value="' + costo_unitario.val() + '"></td>' +
                    '<td> <div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Remover" data-original-title="Remover" onclick="remover(' + id + ')"> <i class="fa fa-trash-o"></i> </a></div></td>' +
                    '</tr>');
                cantidad.val('0');
                fraccion.val('0');
                // costo_unitario.val('');
            }
        }

    }
</script>
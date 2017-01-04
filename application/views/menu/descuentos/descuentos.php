<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Descuentos</li>
    <li><a href="">Agregar y Editar Descuentos</a></li>
</ul>

<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable">', "</div>");
?>

<div class="span12">
    <div class="block">
        <!-- Progress Bars Wizard Title -->

        <div class="row">
            <div class="col-md-2">
                <a class="btn btn-primary" onclick="agregar();">
                    <i class="fa fa-plus ">Nuevo</i>
                </a>

            </div>

            <form id="frmGrupos">
                <div class="col-md-1">
                    <label class="control-label panel-admin-text">Grupos:</label>
                </div>
                <div class="col-md-3">

                    <select name="grupos" id="grupos" class='cho form-control filter-input'>
                        <?php if (count($grupos) > 0): ?>
                            <?php foreach ($grupos as $grupo): ?>
                                <option
                                    value="<?php echo $grupo['id_grupos_cliente']; ?>"
                                    id="<?php echo $grupo['nombre_grupos_cliente']; ?>">
                                    <?php echo $grupo['nombre_grupos_cliente']; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="row" id="loading" style="display: none;">
        <div class="col-md-12 text-center">
            <div class="loading-icon"></div>
        </div>
    </div>
    <div id="lstTabla" class="table-responsive"></div>

</div>


<script type="text/javascript">

    $(document).ready(function () {

        $("#grupos").on('change', function() {
            loadDescuentos();
        });

        loadDescuentos();
    });


    function loadDescuentos() {
        $("#lstTabla").html($("#loading").html());

        $.ajax({
            type: 'POST',
            data: $('#frmGrupos').serialize(),
            url: '<?php echo base_url();?>' + 'descuentos/lst_descuentos',
            success: function (data) {
                $("#lstTabla").html(data);

            },
            error: function(){
                $("#lstTabla").html('');
                $.bootstrapGrowl('<h4>Ha ocurrido un error en la opci&oacute;n</h4>', {
                    type: 'warning',
                    delay: 2500,
                    allow_dismiss: true
                });
            }
        });
    }

</script>


<script type="text/javascript">

    var lst_producto = new Array();
    var lst_producto_con_precio = new Array();
    var count = 0;

    //$("#cboProducto").chosen();

    function listarTodos() {

        var producto = {};
        producto.count = countproducto;

        $("#lstTabla").show();
        var $tabla = $("#lstTabla");
        $tabla.find("table").remove();
        $tabla.append('<table id="tablita" class="table table-striped table-condensed table-bordered data-nosort="0">' +
            '<thead><tr><th>Codigo</th><th>Producto</th><th>Unidad</th><th>Action</th></tr>' +
            '</thead></table>');

        $("#cboProducto option").each(function () {

            countproducto++;
            $("#tablita").append(
                '<tr><td>' + $(this).val() +
                '</td><td>' + $(this).text() +
                '</td><td>' + $(this).val() +
                '</td><td class="actions">' +
                '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="del_listaProducto(countproducto,' + $(this).val() + ');">' +
                '<i class="fa fa-trash-o"></i></a>' +
                '</div></td></tr>');
        });
        $("#listar").attr("disabled", true);
        $("#listarTodos").attr("disabled", true);
    }


    function listarProductos() {

        var producto = {};
        var contador = {};
        producto.Codigo = $("#cboProducto").val();
        producto.Productor = encodeURIComponent($("#cboProducto option:selected").text());
        producto.unidad = $("#unidades").val();
        producto.unidad_nombre = $('#unidades option:selected').html();
        producto.count = countproducto;
        contador.con = $("#unidades").val();
        alert('$("#unidades").val()');

        if ($("#unidades").val() != "" && $("#cboProducto").val() != "") {

            lst_producto.push(producto);
            countproducto++;
            $("#lstTabla").show();
            var $tabla = $("#lstTabla");
            $tabla.find("table").remove();
            $tabla.append('<table class="table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">' +
                '<thead><tr><th>Codigo</th><th>Producto</th><th>Unidad</th><th>Action</th></tr>' +
                '</thead></table>');
            var tbody = $('<tbody id="tbodyproductos"></tbody>');
            jQuery.each(lst_producto, function (i, value) {

                tbody.append(
                    '<tr><td style="text-align: center;">' + value["Codigo"] +
                    '</td><td >' + decodeURIComponent(value["Productor"]) +
                    '</td><td >' + value["unidad_nombre"] +
                    '</td><td class="actions">' +
                    '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="del_listaProducto(' + value["count"] + ','+value["Codigo"]+');">' +
                    '<i class="fa fa-trash-o"></i></a>' +
                    '</div></td></tr>'
                );
            });


            $tabla.find("table").append(tbody);
            $("#cboProducto").val('').trigger("chosen:updated");
            $("#unidades").val('').trigger("chosen:updated");

        } else {

            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe seleccionar todos los campos!</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;

        }

    }



</script>

<script type="text/javascript">

    function borrar(id, nom) {

        $('#borrar').modal({show: true, keyboard: false, backdrop: 'static'});
        $("#id_borrar").attr('value', id);
        $("#nom_borrar").attr('value', nom);
    }

    function verModal(id) {

        $("#verModal").load('<?= $ruta ?>descuentos/verReglaDescuento/' + id, function(){
            $('#verModal').modal('show');
        });

    }

    function editar(id, grupo_id) {

        lst_producto = new Array();
        lst_escalas = new Array();
        lst_precio = new Array();

        arreglo_precios = new Array();
        countescalas = 0;
        countproductos = 0;
        countprecio = 0;
        $("#barloadermodal").modal('show');

        $("#agregar").load('<?= $ruta ?>descuentos/form/' + id + '/' + grupo_id + '/', function () {
            $('#agregar').modal({show: true, keyboard: false, backdrop: 'static'});
            $("#barloadermodal").modal('hide');

        });

    }

    function agregar() {
        var id = false;
        var grupo_id = $("#grupos option:selected").val();

        $("#barloadermodal").modal('show');

        $("#agregar").load('<?= $ruta ?>descuentos/form/' + id + '/' + grupo_id + '/', function(){
            $('#agregar').modal({show: true, keyboard: false, backdrop: 'static'});
            $("#barloadermodal").modal('hide');

        });

        lst_producto = new Array();
        lst_escalas = new Array();
        lst_precio = new Array();

        arreglo_precios = new Array();
        countescalas = 0;
        countproductos = 0;
        countprecio = 0;
    }

    var grupo = {
        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>descuentos'

            })
        },

    }
    function eliminar() {

        App.formSubmitAjax($("#formeliminar").attr('action'), grupo.ajaxgrupo, 'borrar', 'formeliminar');

    }


</script>


<script type="text/javascript">

    var lst_producto = new Array();
    var lst_escalas = new Array();
    var lst_precio = new Array();

    var arreglo_precios = new Array();
    var countescalas = 0;
    var countproductos = 0;
    var countprecio = 0;

    function guardardescuento() {


        var hasta = "";
        var desde = "";
        var i = 0;
        var temp = {};
        var p = 0;
        var lst_rango = new Array();

        if (lst_escalas.length > 1) {
            for (i = 0; i < lst_escalas.length; i++) {

                if (lst_escalas[i + parseInt("1")] != undefined) {

                    if ((parseInt(lst_escalas[i + parseInt("1")]["desde"]) - parseInt(lst_escalas[i]["hasta"])) != 1) {

                        lst_rango[p++] = parseInt(lst_escalas[i]["desde"]);
                        lst_rango[p++] = parseInt(lst_escalas[i]["hasta"]);

                        lst_rango[p++] = parseInt(lst_escalas[i + parseInt("1")]["desde"]);
                        lst_rango[p++] = parseInt(lst_escalas[i + parseInt("1")]["hasta"]);

                    }
                } else {

                    break;
                }
            }
        }

        if (lst_rango.length > 0) {

            for (i = 0; i < lst_rango.length / 4; i++) {

                var growlType = 'warning';
                $.bootstrapGrowl('<h5>Entre las siguientes escalas falta un rango: desde ' + lst_rango[i] + ' hasta ' + lst_rango[i + 1] + ' y desde ' + lst_rango[i + 2] + ' hasta ' + lst_rango[i + 3] + '</h5>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            }
            return false;
        } else {


            $("#barloadermodal").modal('show');

            if ($("#nombre").val() != "" && lst_escalas.length > 0) {

                if (lst_producto.length == 0) {
                    mensaje = "Debe seleccionar al menos un producto"
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>' + mensaje + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $("#barloadermodal").modal('hide');
                    return false;
                }


                var miJSON = JSON.stringify(lst_escalas);
                var miJSONE = JSON.stringify(lst_producto);


                $.ajax({
                    type: 'POST',
                    data: $('#formaagregar').serialize() + '&lst_escalas=' + miJSON + '' + '&lst_producto=' + miJSONE + '',
                    url: '<?=$ruta?>descuentos/registrar_descuento',
                    dataType: 'json',
                    success: function (data) {

                        if (data.success != 'undefined') {

                            $('#agregar').modal('hide');
                            var growlType = 'success';
                            $.bootstrapGrowl('<h4>Se ha registrado el descuento</h4> Numero de descuento: ' + data.id, {
                                type: growlType,
                                delay: 5000,
                                allow_dismiss: true
                            });

                            grupo.ajaxgrupo().success(function (data2) {
                                /*
                                *
                                *  $("#barloadermodal").modal('hide');

                                 $('#agregar').on('hidden.bs.modal', function () {


                                 $('#page-content').html(data2);

                                 $("#successspan").html(data.success);

                                 $("#success").css('display', 'block');
                                 })***/

                                $('#page-content').html(data2).promise().done(function(){
                                    $("#barloadermodal").modal('hide');

                                    $("#successspan").html(data.success);

                                    $("#success").css('display', 'block');
                                });
                            });

                        }
                        else {
                            $("#barloadermodal").modal('hide');
                            $("#success").css('display', 'none');
                            var growlType = 'warning';
                            $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            })

                        }


                    },
                    error: function (data) {
                        $("#barloadermodal").modal('hide');


                        var growlType = 'warning';
                        $.bootstrapGrowl('<h4> Ha ocurrido un error al registrar el descuento</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                    }
                });
            } else {

                if (lst_producto.length == 0) {
                    mensaje = "Debe seleccionar al menos un producto"
                }
                else {
                    mensaje = "Debe seleccionar todos los campos";
                }
                var growlType = 'warning';
                $.bootstrapGrowl('<h4>' + mensaje + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $("#barloadermodal").modal('hide');
                return false;
            }
        }
    }


    function accionEscalas() {

        var escala = {};

        escala.desde = parseInt($("#desde").val());
        escala.hasta = parseInt($("#hasta").val());
        escala.contador = countescalas + 1;

        var hasta = "";
        var desde = "";
        var i = 0;


        if (escala.desde != "" && escala.hasta != "") {


            if (escala.desde < escala.hasta) {


                for (i = 0; i < lst_escalas.length; i++) {


                    if (escala.desde >= parseInt(lst_escalas[i]["desde"]) && escala.hasta <= parseInt(lst_escalas[i]["hasta"])) {
                        var growlType = 'warning';
                        $.bootstrapGrowl('<h4>Ya ha ingresado una escala dentro de este rango!</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });
                        return false;
                    }

                    if (escala.desde > parseInt(lst_escalas[i]["hasta"])) {
                        continue;

                    } else {

                        if (escala.hasta >= parseInt(lst_escalas[i]["hasta"])) {
                            var growlType = 'warning';
                            $.bootstrapGrowl('<h4>Ya ha ingresado una escala dentro de este rango!</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });
                            return false;

                        }

                        if (escala.hasta >= parseInt(lst_escalas[i]["desde"])) {

                            var growlType = 'warning';
                            $.bootstrapGrowl('<h4>Ya ha ingresado una escala dentro de este rango!</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });
                            return false;

                        }


                    }

                }

                document.getElementById('desde').value = '';
                document.getElementById('hasta').value = '';
                lst_escalas.push(escala);


                if (lst_escalas.length > 1) {
                    for (i = 0; i < lst_escalas.length; i++) {

                        if (lst_escalas[i + parseInt("1")] != undefined) {

                            if (parseInt(lst_escalas[i]["desde"]) >= parseInt(lst_escalas[i + 1]["desde"])) {

                                desde = parseInt(lst_escalas[i + 1]["desde"]);
                                hasta = parseInt(lst_escalas[i + 1]["hasta"]);

                                lst_escalas[i + 1]["desde"] = parseInt(lst_escalas[i]["desde"]);
                                lst_escalas[i + 1]["hasta"] = parseInt(lst_escalas[i]["hasta"]);

                                lst_escalas[i]["desde"] = desde;
                                lst_escalas[i]["hasta"] = hasta;

                            }
                        } else {
                            break;

                        }

                    }

                }
                countescalas++;

                var arreglo_precios = new Array();
                var contador_precios = 0;
                jQuery.each(lst_escalas, function (i, value) {


                    if ($("#tbodyproductos" + value.contador) != undefined) {


                        var variable = "#tbodyproductos" + value.contador + " input[type='number']";
                        var contador = 0;
                        $(variable).each(function () {
                            var monto = $(this).val();

                            for (i = contador; i < lst_producto.length; i++) {

                                if ($("#precio" + value.contador + lst_producto[i]["Codigo"]).val() != undefined) {

                                    var precios = {};
                                    precios.codigo = lst_producto[i]["Codigo"];
                                    precios.monto = monto
                                    precios.escala = value.contador;
                                    contador_precios = contador_precios + 1;
                                    arreglo_precios.push(precios)
                                    contador = i + 1;
                                    break;
                                }
                            }
                        });


                    }
                });


                generarListado(false, arreglo_precios);
                var growlType = 'info';
                $.bootstrapGrowl('<h4>Se ha creado la escala, ahora puede agreagr los productos!</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;


            } else {

                var growlType = 'warning';
                $.bootstrapGrowl('<h4>Ingrese un rango válido!</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }

        } else {
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe seleccionar todos los campos!</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

    }

    function accionProductos() {


        if(lst_escalas.length<=0){
            var growlType = 'warning';
            $.bootstrapGrowl('<h4>Debe crear primero una escala!</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }else {

        //    console.log($("#cboProducto option:selected").html());

            var producto = {};
            producto.Codigo=$("#cboProducto").val();
            producto.Productor=$("#cboProducto option:selected").html();
            producto.unidad=$("#unidades").val();
            producto.unidad_nombre=$('#unidades option:selected').html();
            producto.contador=countproductos + 1;

            //console.log(producto);


            var j = 0;


            if ($("#unidades").val() != "" && $("#cboProducto").val() != "") {

                /*      if ( ($("#cboProducto").val() == prod ) && ($("#unidades").val() == 1 ) ){
                 alert("El producto ya está en el listado");

                 } */

                var validar_existencia = false;
                jQuery.each(lst_producto, function (i, value) {

                    if (value["Codigo"] == producto.Codigo) {

                        validar_existencia = true
                    }
                });

                if (validar_existencia == true) {

                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>Ya ha seleccionado este producto!</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    return false;
                }
                lst_producto.push(producto);

                var arreglo_precios = new Array();
                var contador_precios = 0;
                jQuery.each(lst_escalas, function (i, value) {


                    if ($("#tbodyproductos" + value.contador) != undefined) {


                        var variable = "#tbodyproductos" + value.contador + " input[type='number']";
                        var contador = 0;
                        $(variable).each(function () {
                            var monto = $(this).val();

                            for (j = contador; j < lst_producto.length; j++) {

                                if ($("#precio" + value.contador + lst_producto[j]["Codigo"]).val() != undefined) {


                                    var precios = {};
                                    precios.codigo = lst_producto[j]["Codigo"];
                                    precios.monto = monto
                                    precios.escala = value.contador;
                                    contador_precios = contador_precios + 1;
                                    arreglo_precios.push(precios)
                                    contador = j + 1;
                                    break;
                                }
                            }
                        });


                    }
                });



                generarListado(false, arreglo_precios);
                countproductos++;


            } else {

                var growlType = 'warning';
                $.bootstrapGrowl('<h4>Debe seleccionar todos los campos!</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                return false;
            }
        }
    }


    function recorrerProdcutos(escala, escala_id, arreglodeprecios) {




        jQuery.each(lst_producto, function (i, value) {

            if (escala_id != undefined) {




                var paso = false;
                var completo = '';


                completo += '<tr id="tr' + escala + value["Codigo"] + '">' + '<td style="text-align: center;">' + value["Codigo"] +
                    '</td><td >' + decodeURIComponent(value["Productor"]) +
                    '</td><td >' + value["unidad_nombre"] +
                    '</td>';

                jQuery.each(lst_producto_con_precio, function (j, valor) {

                    if (valor["Codigo"] == value["Codigo"] && valor["escala_id"] == escala_id) {
                        paso = true;

                        completo += '<td ><input type="number" name="precio[]" id="precio' + escala + value["Codigo"] + '" value="' + valor["precio"] + '" class="pr form-control"/>' +
                            '</td><td class="actions">' +
                            '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar" ' +
                            'data-original-title="Eliminar" onclick="del_listaProducto(' + value["contador"] + ',' + value["Codigo"] + ');">' +
                            '<i class="fa fa-trash-o"></i></a>' +
                            '</div></td></tr>';

                    }


                });

                if (paso == false) {
                    completo += '<td ><input type="number" name="precio[]" id="precio' + escala + value["Codigo"] + '" value="0.00" class="pr form-control"/>' +
                        '</td><td class="actions">' +
                        '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar"' +
                        ' data-original-title="Eliminar" onclick="del_listaProducto(' + value["contador"] + ',' + value["Codigo"] + ');">' +
                        '<i class="fa fa-trash-o"></i></a>' +
                        '</div></td></tr>';

                }

                $("#tbodyproductos" + escala).append(completo);


                $("#cboProducto").val('').trigger("chosen:updated");
                $("#unidades").val('').trigger("chosen:updated");
            } else {


                var completo = '';


                completo += '<tr id="tr' + escala + value["Codigo"] + '">' + '<td style="text-align: center;">' + value["Codigo"] +
                    '</td><td >' + decodeURIComponent(value["Productor"]) +
                    '</td><td >' + value["unidad_nombre"] +
                    '</td>';
                var p = 0;

                /*console.log(arreglodeprecios)
                 console.log(arreglodeprecios.length)*/
                if (arreglodeprecios.length > 0) {
                    // console.log(escala)
                    var paso = false;
                    for (p = 0; p < arreglodeprecios.length; p++) {

                        if (arreglodeprecios[p]["escala"] == escala && arreglodeprecios[p]["codigo"] == value["Codigo"]) {
                            // console.log("por aqui si")
                            completo += '<td ><input type="number" name="precio[]" id="precio' + escala + value["Codigo"] + '" value="' + arreglodeprecios[p]["monto"] + '" class="pr form-control"/>' +
                                '</td><td class="actions">' +
                                '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar" ' +
                                'data-original-title="Eliminar" onclick="del_listaProducto(' + value["contador"] + ',' + value["Codigo"] + ');">' +
                                '<i class="fa fa-trash-o"></i></a>' +
                                '</div></td></tr>';
                            paso = true;
                        }

                    }

                    if (paso == false) {
                        //  console.log(paso)
                        completo += '<td ><input type="number" name="precio[]" id="precio' + escala + value["Codigo"] + '" value="0.00" class="pr form-control"/>' +
                            '</td><td class="actions">' +
                            '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar" ' +
                            'data-original-title="Eliminar" onclick="del_listaProducto(' + value["contador"] + ',' + value["Codigo"] + ');">' +
                            '<i class="fa fa-trash-o"></i></a>' +
                            '</div></td></tr>';

                    }

                } else {

                    completo += '<td ><input type="number" name="precio[]" id="precio' + escala + value["Codigo"] + '" value="0.00" class="pr form-control"/>' +
                        '</td><td class="actions">' +
                        '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar" ' +
                        'data-original-title="Eliminar" onclick="del_listaProducto(' + value["contador"] + ',' + value["Codigo"] + ');">' +
                        '<i class="fa fa-trash-o"></i></a>' +
                        '</div></td></tr>';

                }

                $("#tbodyproductos" + escala).append(completo);
                $("#cboProducto").val('').trigger("chosen:updated");
                $("#unidades").val('').trigger("chosen:updated");


            }
        });


    }

    function quitarEscala(contador) {
        var arreglo_precios = new Array();
        var contador_precios = 0;

        $("#escalita" + contador).remove();
        // console.log($("#escalita" + contador));


        var $tabla = $(".escalita");

        var j = 1;
        jQuery.each(lst_escalas, function (i, value) {


            if (value["contador"] == contador) {
                eliminar = i;

                /* jQuery.each(lst_producto, function (j, valor) {

                 if(valor[""])
                 if (value["contador"] == contador) {
                 eliminar = i;

                 }
                 });*/

            } else {
                var valor = value["contador"];

                var variable = "#tbodyproductos" + valor + " input[type='number']";
                var precios = {};
                $(variable).each(function () {
                    precios.monto = $(this).val();
                    precios.escala = j;
                    contador_precios = contador_precios + 1;
                    arreglo_precios.push(precios)

                });
                //console.log(precios)

                value["contador"] = j;
                j++;


            }


        });
        countescalas--;

        //console.log(arreglo_precios)
        lst_escalas.splice(eliminar, 1);
        generarListado(true, arreglodeprecios);

    }


    function generarListado(eliminar_escala, arreglodeprecios) {


        $("#lstEscalas").html('');
        jQuery.each(lst_escalas, function (i, value) {


            // console.log(arreglo_precios)

            $("#lstEscalas").append('<div class="escalita table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" id="escalita' + value["contador"] + '">' + 'Escala del ' + value["desde"] + ' hasta ' + value["hasta"] + ' ' +
                '<div class="btn-group">' +
                '<a class="btn btn-default btn-mini btn-default" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="quitarEscala(' + value.contador + ');">' +
                '<i class="fa fa-trash-o"></i></a></div>' +
                '<div class="lstTabla" id="lstTabla' + value.contador + '"></div></div>');

            var $tabla = $("#lstTabla" + value.contador);

            $tabla.html('<table id="productos' + value.contador + '" class="copiar table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">' +
                '<thead><tr><th>Codigo</th><th>Producto</th><th>Unidad</th><th>Precio</th><th>Action</th></tr>' +
                '</thead></table>');

            var tbody = $('<tbody class="agrega" id="tbodyproductos' + value.contador + '"></tbody>');
            $("#productos" + value.contador).append(tbody);


            recorrerProdcutos(value.contador, value.id, arreglodeprecios);

        });


    }

    function del_listaProducto(contador, producto_id) {

       // console.log("elimianndo "+producto_id);

      //  console.log("contador"+contador);
        var eliminar;
       // console.log(lst_escalas);
        jQuery.each(lst_escalas, function (j, valor) {
            jQuery.each(lst_producto, function (i, value) {

               // console.log(value);
                if (value["contador"] == contador) {

                    $("#tr" + valor["contador"] + producto_id).remove();
                    console.log('estes es el que hay que eliminar '+valor["contador"] + producto_id);
                    eliminar = i;

                }

            });
        });

//        console.log(eliminar);
        lst_producto.splice(eliminar, 1);

        var arreglo_precios = new Array();
        var contador_precios = 0;
        var j = 0;
        jQuery.each(lst_escalas, function (i, value) {

            if ($("#tbodyproductos" + value.contador) != undefined) {

                var variable = "#tbodyproductos" + value.contador + " input[type='number']";
                var contador = 0;
                $(variable).each(function () {
                    var monto = $(this).val();

                    for (j = contador; j < lst_producto.length; j++) {

                        if ($("#precio" + value.contador + lst_producto[j]["Codigo"]).val() != undefined) {
                            var precios = {};
                            precios.codigo = lst_producto[j]["Codigo"];
                            precios.monto = monto
                            precios.escala = value.contador;
                            contador_precios = contador_precios + 1;
                            arreglo_precios.push(precios)
                            contador = j + 1;
                            break;
                        }
                    }
                });


            }
        });

        jQuery.each(arreglo_precios, function (k, valor) {

            if (valor["codigo"] == producto_id && valor["escala"] == contador) {

                arreglo_precios.splice(k, 1);
            }

        });

        jQuery.each(lst_producto_con_precio, function (k, valor) {

            if (valor["Codigo"] == producto_id) {

                lst_producto_con_precio.splice(k, 1);
            }


        });


        //generarListado(false,arreglo_precios);

    }



</script>


<div class="modal fade bs-example-modal-lg" id="agregar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade bs-example-modal-lg" id="verModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" id="formeliminar" method="post" action="<?= $ruta ?>descuentos/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Eliminar Grupo</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea eliminar el descuento seleccionado?</p>
                    <input type="hidden" name="id" id="id_borrar">
                    <input type="hidden" name="nombre" id="nom_borrar">
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmar" class="btn btn-primary" onclick="eliminar()">Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>
<!-- /.modal-dialog -->



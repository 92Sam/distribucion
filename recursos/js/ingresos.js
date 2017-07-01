/**
 * Created by Jhainey on 18/09/2015.
 */
var lst_producto = new Array();
var tablaListaCompras;
var countproducto = 0;

$(document).ready(function () {
    /*$('body').keydown(function (e) {
        if (e.keyCode == 117) {
            e.preventDefault();
        }
    });
    $('body').keyup(function (e) {
        if (e.keyCode == 117) {
            e.preventDefault();
            guardaringreso();
        }
    });*/

    var tecla_enter = 13;
    var F6 = 117;

    $(document).keydown(function(e){
        if (e.keyCode == F6) {
            e.preventDefault();
        }
    });

    $(document).keyup(function(e){

        if (e.keyCode == tecla_enter) {
            e.preventDefault();
            e.stopImmediatePropagation();
            listarProductos();
        }

        if (e.keyCode == F6) {
            e.preventDefault();
            e.stopImmediatePropagation();
            $("#btnGuardar").click();
        }

        if (e.keyCode == F6 && $("#confirmarmodal").is(":visible") == true) {
            e.preventDefault();
            e.stopImmediatePropagation();
            guardaringreso();
        }
    });

    $("#cboProveedor").change(function () {
        refreshProductos();
    });
    $(".closemodificarcantidad").on('click', function () {
        $("#modificarcantidad").modal('hide');
    });

    $("#btnGuardar").click(function () {
        $("#confirmarmodal").modal('show');


    });


    $("#cancelar").on('click', function (data) {

        if ($("#ingresomodal").length > 0) {
            $("#ingresomodal").modal('hide');
        } else {
            $.ajax({
                url: ruta + 'principal',
                success: function (data) {
                    $('#page-content').html(data);

                }

            })
        }

    });


    $("#reiniciar").on('click', function (data) {

        $.ajax({
            url: ruta + 'ingresos?costos=' + $("#costos").val(),
            success: function (data) {
                $('#page-content').html(data);
            }

        })

    });


    var f = new Date();


    $("#ec_excel").hide();
    $("#ec_pdf").hide();

    $("#cboProducto").chosen({
        placeholder: "Seleccione el producto",
        allowClear: true, 
        search_contains: true
    });

    $("#local").chosen();

    $('#cboProducto').on("change", function () {

        $.ajax({
            url: ruta + 'ingresos/get_unidades_has_producto',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            data: {'id_producto': $(this).val()},
            success: function (data) {

                var options = '';
                for (var i = 0; i < data.unidades.length; i++) {
                    options += '<option  value="'
                        + data.unidades[i].id_unidad
                        + '">'
                        + data.unidades[i].nombre_unidad
                        + '</option>';

                    // console.info(data.unidades[i]);

                    $("#producto_cualidad").val(data.unidades[i].producto_cualidad);
                    if (data.unidades[i].producto_cualidad == "MEDIBLE") {
                        $("#cantp").attr('min', '1');
                        $("#cantp").attr('step', '1');
                        $("#cantp").attr('value', '1');

                    } else {
                        $("#cantp").attr('min', '0.1');
                        $("#cantp").attr('step', '0.1');
                        $("#cantp").attr('value', '0.0');

                    }
                }

                $("#unidades ")
                    .html(
                        '<option value="">Seleccione</option>');

                $("#unidades")
                    .append(options);

                $("#unidades").trigger("chosen:updated");

                $('#precio_unitario').html(data.producto.costo_unitario);
            }
        })


    });

    $("#cboProveedor").chosen({
        placeholder: "Seleccione el producto",
        allowClear: true
    });


    $("#impuestos").chosen({
        placeholder: "Seleccione el impuesto",
        allowClear: true
    });
    tablaListaCompras = $('#tbLista').dataTable({
        "aoColumns": [
            {"sWidth": "15%", "mDataProp": "nroDocumento"},
            {"sWidth": "15%", "mDataProp": "Documento"},
            {"sWidth": "15%", "mDataProp": "FecRegistro"},
            {"sWidth": "15%", "mDataProp": "FecEmision"},
            {"sWidth": "15%", "mDataProp": "RazonSocial"},
            {"sWidth": "15%", "mDataProp": "Responsable"}
        ],
        "fnCreatedRow": function (nRow, aData, iDisplayIndex) {
        },
        "aaSorting": [[0, 'asc'], [1, 'asc']],
        "sDom": "<'row'<'span6'l><'span6'f>r>t<'row'<'span6'i><'span6'p>>",
        "sPaginationType": "bootstrap",
        "oLanguage": {
            "sLengthMenu": "_MENU_ registros por página"
        }
    });


    $("#btnBuscar").click(function (e) {
        e.preventDefault();
        document.getElementById('fecIni1').value = $("#fecIni").val();
        document.getElementById('fecFin1').value = $("#fecFin").val();
        document.getElementById('fecIni2').value = $("#fecIni").val();
        document.getElementById('fecFin2').value = $("#fecFin").val();
        $.ajax({
            type: 'POST',
            dataType: 'JSON',
            data: $('#frmBuscar').serialize(),
            url: ruta + 'ingresos/lst_reg_ingreso',
            success: function (data) {
                tablaListaCompras.fnAddData(data);
            }
        });
    });


    $("#cboTipDoc").change(function () {
        llenar_tabla();
    });

});


function guardaringreso() {
    $("#botonconfirmar").addClass('disabled');
    $("#barloadermodal").modal('show');

    if ($("#fecEmision").val() != "" && $("#doc_serie").val() != "" && $("#doc_numero").val() != "" && $("#local").val() != ""
        && $("#cboTipDoc").val() != "" && $("#cboProveedor").val() != "" && $("#tbodyproductos tr").length > 0
        && $("#impuestos").val() != "" && $("#pago").val() != "") {

        accionGuardar();
    } else {

        if ($("#ingreso_id").val() != '') {
            accionGuardar();
        } else {
            $("#botonconfirmar").removeClass('disabled');
            if ($("#tbodyproductos tr").length == 0) {
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
            })
            $("#barloadermodal").modal('hide');
            return false;
        }
    }
}
;

function accionGuardar() {

    /*if ($("#costos").val() == 'true') {
     jQuery.each(lst_producto, function (i, value) {
     if (value.precio==0){

     mensaje = "Debe registrar todos los costos";

     var growlType = 'warning';
     $.bootstrapGrowl('<h4>' + mensaje + '</h4>', {
     type: growlType,
     delay: 2500,
     allow_dismiss: true
     })
     $("#barloadermodal").modal('hide');
     return false;

     }
     })
     }*/
    var temp_prod = [];
    for(var i=0;i<lst_producto.length;i++){
        temp_prod.push({
            Codigo: lst_producto[i].Codigo,
            Cantidad: lst_producto[i].Cantidad,
            ValorUnitario: lst_producto[i].ValorUnitario,
            PrecUnt: lst_producto[i].PrecUnt,
            Importe: lst_producto[i].Importe,
            unidad: lst_producto[i].unidad,
            producto_id: lst_producto[i].producto_id
        });
    }

    var miJSON = JSON.stringify(temp_prod);
    console.log(miJSON);
    $.ajax({
        type: 'POST',
        data: $('#frmCompra').serialize() + '&lst_producto=' + miJSON,
        url: ruta + 'ingresos/registrar_ingreso',
        dataType: 'json',
        success: function (data) {


            if (data.success && data.error == undefined) {

                $("#confirmarmodal").modal('hide');
                if ($("#ingresomodal").length > 0) {
                    $("#ingresomodal").modal('hide');
                }
                var growlType = 'success';
                $.bootstrapGrowl('<h4>Se ha registrado el ingreso</h4> Número de ingreso: ' + data.id, {
                    type: growlType,
                    delay: 5000,
                    allow_dismiss: true
                });
                if ($("#ingreso_id").val() == '') {
                    $.ajax({
                        url: ruta + 'ingresos?costos=' + $("#costos").val(),
                        success: function (data2) {


                            $('#page-content').html(data2);


                        }

                    })
                } else {
                    $.ajax({
                        url: ruta + 'ingresos/consultar',
                        success: function (data2) {


                            $('#page-content').html(data2);


                        }

                    })
                }


            }
            else {
                $("#botonconfirmar").removeClass('disabled');
                var growlType = 'warning';
                $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                })

            }

            $("#barloadermodal").modal('hide');
        },
        error: function (data) {
            $("#barloadermodal").modal('hide');


            var growlType = 'warning';
            $.bootstrapGrowl('<h4> Ha ocurrido un error al registrar el ingreso</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

        }
    });
}

function addToArray(precio_unitario, producto_id, nombre, cantidad, Importe, unidad, unidad_nombre) {

    var precio_unitario = precio_unitario;
    var producto = {};
    producto.Codigo = producto_id;
    producto.Productor = encodeURIComponent(nombre);
    producto.Cantidad = cantidad;
    producto.ValorUnitario = precio_unitario;
    producto.PrecUnt = parseFloat(precio_unitario * 1.18).toFixed(2);
    producto.Importe = Importe;
    producto.unidad = unidad;
    producto.unidad_nombre = unidad_nombre;
    producto.count = countproducto;
    producto.producto_id = producto_id;

    lst_producto.push(producto);

    countproducto++;
    llenar_tabla();
}


function listarProductos() {

    //var precio_unitario = (parseFloat($("#precio").val()) / parseFloat($("#cantp").val())).toFixed(2);
    var precio_unitario = ((parseFloat($("#precio").val()).toFixed(2) / parseFloat($("#cantp").val())) * 1.18 ).toFixed(2);
    var valor_unitario = (parseFloat($("#precio").val()).toFixed(2) / parseFloat($("#cantp").val())) ;

    var producto = {};
    producto.Codigo = $("#cboProducto").val();
    producto.Productor = encodeURIComponent($("#cboProducto option:selected").text());
    producto.Cantidad = $("#cantp").val();
    producto.PrecUnt = precio_unitario; // Se multiplica por el 18% IGV
    producto.ValorUnitario = valor_unitario.toFixed(2);
    producto.ValorUnitarioSinRedondeo =valor_unitario;
    producto.Importe = $("#precio").val();
    producto.unidad = $("#unidades").val();
    producto.unidad_nombre = $('#unidades option:selected').html();
    producto.count = countproducto;
    producto.producto_id = $("#cboProducto").val();


    if (parseFloat($("#cantp").val()) > 0.0 && $("#unidades").val() != "" && $("#cboProducto").val() != "") {

        var costos = $("#costos").val();
        if (costos === 'true') {

            if (parseFloat(precio_unitario) < 0.0) {
                var growlType = 'warning';
                $.bootstrapGrowl('<h4>Debe seleccionar todos los campos!</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                })
                return false;
            }
        }

        var producto_cualidad = $("#producto_cualidad").val();


        if (producto_cualidad == "MEDIBLE") {
            cantidadminima = 1;

        } else {

            var length = $("#unidades option").length;
            var is_last_item;
            $("#unidades option").each(function (index) {

                if ((index == (length - 1))) {

                    is_last_item = $(this).val();

                }

            });
            if (is_last_item == producto.unidad) {
                cantidadminima = 0.1;
            } else {
                cantidadminima = 1;
            }


        }

        if (producto.Cantidad == '' || producto.Cantidad < cantidadminima) {
            var growlType = 'danger';

            $.bootstrapGrowl('<h4>Datos incompletos:</h4> <p>Ingrese una cantidad mayor a ' + cantidadminima + ' y menor al stock</p>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false;
        }

        console.log(lst_producto);
        lst_producto.push(producto);
        countproducto++;
        llenar_tabla();
        $("#cboProducto").val('').trigger("chosen:updated");
        $("#unidades").val('').trigger("chosen:updated");
        $("#precio").val('0.0');
        $("#cantp").val('0.0');

        $('#cboProducto').trigger('chosen:open');
        $('#cboProducto_chosen .chosen-search input').trigger('focus');

    } else {

        var growlType = 'warning';
        $.bootstrapGrowl('<h4>Debe seleccionar todos los campos!</h4>', {
            type: growlType,
            delay: 2500,
            allow_dismiss: true
        })
        return false;
    }
}


function llenar_tabla() {

    var costos = $("#costos").val();

    $("#lstTabla").show();
    var montos = 0;
    var $tabla = $("#lstTabla");
    $tabla.find("table").remove();
    var tablahtml = '<table class="table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">' +
        '<thead><tr><th>Codigo</th><th>Producto</th><th>Unidad</th><th>Cantidad</th>';
    if (costos === 'true') {
        tablahtml += '<th>Valor Unit.</th><th>Precio Unit.</th><th>Importe</th>';
    }
    tablahtml += '<th>Acción</th></tr>' +
        '</thead><tbody id="tbodyproductos"></tbody></table>';

    $tabla.append(tablahtml);


    var tbodyhtml = '';

    jQuery.each(lst_producto, function (i, value) {
        tbodyhtml += '<tr><td style="text-align: center;">' + value["Codigo"] +
            '</td><td >' + decodeURIComponent(value["Productor"]) +
            '</td><td >' + value["unidad_nombre"] + '</td>';
        if ($("#editar_ingreso").val() == '0')
            tbodyhtml += '<td style="text-align: center;">' + value["Cantidad"] + '</td>';
        else if ($("#editar_ingreso").val() == '1') {
            tbodyhtml += '<td style="text-align: center; width: 150px;">';
            tbodyhtml += '<input type="number" value="' + value["Cantidad"] + '" ';
            tbodyhtml += 'id="cantidad_' + value["count"] + '" ';
            tbodyhtml += 'data-index="' + value["count"] + '" ';
            tbodyhtml += 'data-target="cantidad_" ';
            tbodyhtml += 'class="form-control text-center cantidad_input" ';
            tbodyhtml += '>';
            tbodyhtml += '</td>';
        }

        if (costos === 'true' && $("#editar_ingreso").val() == '0') {
            tbodyhtml += '<td style="text-align: center;">' + value["ValorUnitario"] + '</td>';
            tbodyhtml += '<td style="text-align: center;">' + value["PrecUnt"] + '</td>';
            tbodyhtml += '<td style="text-align: center;">' + value["Importe"] + '</td>';
        }
        if ($("#editar_ingreso").val() == '1') {
            tbodyhtml += '<td id="valor_unitario_' + value["count"] + '" style="text-align: center;">' + value["ValorUnitario"] + '</td>';
            tbodyhtml += '<td id="precio_unitario_' + value["count"] + '" style="text-align: center;">' + value["PrecUnt"] + '</td>';

            tbodyhtml += '<td style="text-align: center; width: 150px;">';
            tbodyhtml += '<input type="number" value="' + value["Importe"] + '" ';
            tbodyhtml += 'id="importe_' + value["count"] + '" ';
            tbodyhtml += 'data-index="' + value["count"] + '" ';
            tbodyhtml += 'data-target="importe_" ';
            tbodyhtml += 'class="form-control text-center importe_input" ';
            tbodyhtml += '>';
            tbodyhtml += '</td>';
        }

        tbodyhtml += '<td class="actions">' +
            '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="del_listaProducto(' + value["count"] + ');">' +
            '<i class="fa fa-trash-o"></i></a>' +
            '</div>';
        if ($("#editar_ingreso").val() != '1') {
            tbodyhtml += '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Editar" data-original-title="Eliminar" onclick="editCantidad(' + value["count"] + ', ' + value["Cantidad"] + ',' + value["unidad"] + ',' + value['producto_id'] + ' ,' + (value["ValorUnitarioSinRedondeo"] != '' ? value["ValorUnitarioSinRedondeo"] : 0.00) + ');">' +
                '<i class="fa fa-edit"></i></a>' +
                '</div></td></tr>';
        }

        montos = montos + parseFloat(value["Importe"]);

    });


    $("#tbodyproductos").html(tbodyhtml);

    if ($("#editar_ingreso").val() == '1') {
        $('.importe_input, .cantidad_input').bind('keyup change click mouseleave', function (e) {

            var tecla_enter = 13;
            var letra_left = 37, letra_right = 39;
            var index = $(this).attr('data-index');
            var max_index = lst_producto.length - 1;
            var cantidad = isNaN(parseFloat($("#cantidad_" + index).val())) ? 0 : parseFloat($("#cantidad_" + index).val());
            var importe = isNaN(parseFloat($("#importe_" + index).val())) ? 0 : parseFloat($("#importe_" + index).val());;
            var montos = 0;

            var precio_unitario = parseFloat(0).toFixed(2);
            var valor_unitario = parseFloat(0).toFixed(2);
            if (cantidad > 0){
                precio_unitario = parseFloat((importe / cantidad) * 1.18).toFixed(2);
                valor_unitario = parseFloat(importe / cantidad).toFixed(2);
            }


            $("#precio_unitario_" + index).html(precio_unitario);
            $("#valor_unitario_" + index).html(valor_unitario);

            for (var i = 0; i < lst_producto.length; i++) {
                if (lst_producto[i].count == index) {
                    lst_producto[i].Cantidad = cantidad;
                    lst_producto[i].Importe = importe;
                    lst_producto[i].ValorUnitario = valor_unitario;
                    lst_producto[i].PrecUnt = precio_unitario;
                    }

                montos = montos + parseFloat(lst_producto[i].Importe);
            }

            calcular_pago(montos);

            if (e.keyCode == tecla_enter) {
                e.preventDefault();
                var my_index = index;
                if (index == max_index)
                    my_index = 0;
                else
                    my_index++;

                $('#' + $(this).attr('data-target') + my_index).trigger('focus');
            }

            if (e.keyCode == letra_left || e.keyCode == letra_right) {
                e.preventDefault();
                if ($(this).attr('data-target') == 'cantidad_')
                    $('#' + 'importe_' + index).trigger('focus');

                if ($(this).attr('data-target') == 'importe_')
                    $('#' + 'cantidad_' + index).trigger('focus');
            }
        });

        $('.importe_input, .cantidad_input').on('focus', function () {
            $(this).select();
        });

        $('#importe_' + 0).trigger('focus');
    }

    calcular_pago(montos);

}

function editCantidad(count, cantidad, unidad_id, id_producto, valor_unitario) {
    console.log(lst_producto);
    $.ajax({
        url: ruta + 'ingresos/get_unidades_has_producto',
        type: 'POST',
        headers: {
            Accept: 'application/json'
        },
        data: {'id_producto': id_producto},
        success: function (data) {

            var options = '';
            for (var i = 0; i < data.unidades.length; i++) {
                options += '<option  value="'
                    + data.unidades[i].id_unidad
                    + '">'
                    + data.unidades[i].nombre_unidad
                    + '</option>';


                $("#producto_cualidad").val(data.unidades[i].producto_cualidad);
                if (data.unidades[i].producto_cualidad == "MEDIBLE") {
                    $("#cantidadedit").attr('min', '1');
                    $("#cantidadedit").attr('step', '1');
                    $("#cantidadedit").attr('value', '1');
                } else {
                    $("#cantidadedit").attr('min', '0.1');
                    $("#cantidadedit").attr('step', '0.1');
                    $("#cantidadedit").attr('value', '0.0');
                }
            }

            $("#unidadedit")
                .html(options);

            $("#unidadedit").val(unidad_id).trigger("chosen:updated");


        }
    })


    //$("#nombreproduto").text(producto_nombre);
    $("#cantidadedit").val(cantidad);

    $("#modificarcantidad").modal('show');
    var costos = $("#costos").val();

    if (costos === 'true') {

        $("#totaledit").val((valor_unitario * cantidad).toFixed(2));

    }

    $("#guardarcantidad").attr('onclick', 'saveCantidadEdit(' + count + ')');
}


function saveCantidadEdit(count) {

    console.log(lst_producto);
    var lista_vieja = lst_producto;
    var newcantidad = parseFloat($("#cantidadedit").val());
    var newunidad = parseFloat($("#unidadedit").val());
    var newunidadnombre = $("#unidadedit option:selected").html();
    var newtotal = parseFloat($("#totaledit").val());
    var costos = $("#costos").val();
    if (costos === 'false') {
        var newpreciouitario = 0;
    }
    else {
        var newpreciouitario = ((newtotal / newcantidad) * 1.18).toFixed(2); //se aplica la multplicacion del IGV
        var newValorUnitario = (newtotal / newcantidad).toFixed(2);
    }
    $("#modificarcantidad").modal('hide');
    $("#subTotal").val(0.00);
    $("#montoigv").val(0.00);
    $("#totApagar").val(0.00);
    $("#totApagar2").val(0.00)

    countproducto = 0;
    lst_producto = new Array();
    $("#tbodyproductos").html('');

    jQuery.each(lista_vieja, function (i, value) {
        var producto = {};
        producto.Codigo = value.Codigo;
        producto.Productor = value.Productor;
        producto.Cantidad = value.Cantidad;
        producto.ValorUnitario = value.ValorUnitario;
        producto.PrecUnt = value.PrecUnt;
        producto.Importe = value.Importe;
        producto.unidad = value.unidad;
        producto.unidad_nombre = value.unidad_nombre;
        producto.count = value.count;
        producto.producto_id = value.producto_id;
        if (value["count"] == count) {
            producto.Cantidad = newcantidad;
            producto.ValorUnitario = newValorUnitario;
            producto.PrecUnt = newpreciouitario;
            producto.Importe = newtotal;
            producto.unidad = newunidad;
            producto.unidad_nombre = newunidadnombre;
        }

        lst_producto.push(producto);
        countproducto++;
        llenar_tabla();

    });

}


function del_listaProducto(count) {
    var costos = $("#costos").val();


    $("#lstTabla").show();
    var montos = 0;
    var $tabla = $("#lstTabla");
    $tabla.find("table").remove();

    var tablahtml = '<table class="table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">' +
        '<thead><tr><th>Codigo</th><th>Producto</th><th>Cantidad</th>';

    if (costos === 'true') {

        tablahtml += '<th>Precio Unit.</th><th>Importe</th><th>Accion</th>';
    }
    tablahtml += '</tr></thead><tbody id="tbodyproductos"></tbody></table></table>';


    $tabla.append(tablahtml);

    var tbodyhtml = '';
    countproducto = 0;
    jQuery.each(lst_producto, function (i, value) {
        if (value["count"] == count) {
            eliminar = i;

        } else {
            tbodyhtml +=
                '<tr><td style="text-align: center;">' + value["Codigo"] +
                '</td><td >' + decodeURIComponent(value["Productor"]) +
                '</td><td style="text-align: center;">' + value["Cantidad"] + '</td>';

            if (costos === 'true') {
                tbodyhtml += '<td style="text-align: center;">' + value["PrecUnt"] +
                    '</td><td style="text-align: center;">' + value["Importe"] + '</td>';
            }
            tbodyhtml += '<td class="actions">' +
                '<div class="btn-group"><a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="del_listaProducto(' + value["count"] + ');">' +
                '<i class="fa fa-trash-o"></i></a>' +
                '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip" title="Editar" data-original-title="Eliminar" onclick="editCantidad(' + value["count"] + ', ' + value["Cantidad"] + ',' + value["unidad"] + ',' + value['producto_id'] + ' ,' + (value["valor_unitario"] != '' ? value["valor_unitario"] : 0.00) + ');">' +
                '<i class="fa fa-edit"></i></a>' +

                '</div></td></tr>'
            ;
            montos = montos + parseFloat(value["Importe"]);
        }
    });

    tbodyhtml += "</tbody>";

    lst_producto.splice(eliminar, 1);

    $("#tbodyproductos").html(tbodyhtml);
    calcular_pago(montos);
}

function calcular_pago(montoTotal) {

    var tipo_documento = $("#cboTipDoc").val();

    var costos = $("#costos").val();

    if (costos === 'false') {

        document.getElementById('totApagar').value = 0;
        document.getElementById('subTotal').value = 0;
        document.getElementById('montoigv').value = 0;
    }
    else {
        document.getElementById('montoigv').value = (montoTotal * ($("#impuestos").val() / 100)).toFixed(2);
        if (tipo_documento != 'FACTURA') {
            document.getElementById('totApagar').value = parseFloat((montoTotal)).toFixed(2);
            document.getElementById('subTotal').value = parseFloat(montoTotal - document.getElementById('montoigv').value).toFixed(2);
        }
        else {

            document.getElementById('totApagar').value = parseFloat(montoTotal + parseFloat(document.getElementById('montoigv').value)).toFixed(2);
            document.getElementById('subTotal').value = parseFloat(montoTotal).toFixed(2);
        }
    }
}

function generar_reporte_excel() {
    document.getElementById("frmExcel").submit();
}

function generar_reporte_pdf() {
    document.getElementById("frmPDF").submit();
}
function refreshProductos() {
    var proveedor = $("#cboProveedor").val();
    console.log(proveedor);
    $.ajax({
        url: ruta + 'producto/get_by_proveedor',
        type: 'post',
        dataType: 'json',
        headers: {
            Accept: 'application/json'
        },
        data: {id: proveedor},
        success: function (data) {
            var options = '<option value="">Seleccione</option>'
            for (var i = 0; i < data.length; i++) {
                options += '<option  value="'
                    + data[i].producto_id
                    + '">'
                    + data[i].producto_nombre
                    + '</option>';


            }
            console.log(options);
            $("#cboProducto").html(options);

            $("#cboProducto").trigger("chosen:updated");
        }

    })
}

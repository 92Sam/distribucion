/**
 * Created by Jhainey on 18/09/2015.
 */

var lst_producto = new Array();

$(document).ready(function () {

    $("#cboProducto").chosen({
        placeholder: "Seleccione el producto",
        allowClear: true
    });

    $('#cboProducto').on("change", function () {

        $.ajax({
            url: ruta + 'descuentos/get_unidades_has_producto',
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
                }

                $("#unidades")
                    .html(
                    '<option value="">Seleccione</option>');

                $("#unidades")
                    .append(options);

                $("#unidades").trigger("chosen:updated");

            }
        })


    });



});
//$("#cboProducto").chosen();
/*
 function listarTodos() {

 var producto = {};
 producto.count=countproducto;

 $("#lstTabla").show();
 var $tabla = $("#lstTabla");
 $tabla.find("table").remove();
 $tabla.append('<table id="tablita" class="table table-striped table-condensed table-bordered data-nosort="0">' +
 '<thead><tr><th>Codigo</th><th>Producto</th><th>Unidad</th><th>Action</th></tr>' +
 '</thead></table>');

 $("#cboProducto option").each(function(){

 countproducto++;
 $("#tablita").append(
 '<tr><td>' + $(this).val() +
 '</td><td>' + $(this).text() +
 '</td><td>' + $(this).val() +
 '</td><td class="actions">' +
 '<div class="btn-group"><a class="btn btn-default btn-mini btn-default" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="del_listaProducto(' + $(this).val() + ');">' +
 '<i class="fa fa-trash-o"></i></a>' +
 '</div></td></tr>');
 });
 $("#listar").attr("disabled", true);
 $("#listarTodos").attr("disabled", true);
 }
 */
function listarProductos() {

    var producto = {};
    var contador = {};
    producto.Codigo = $("#cboProducto").val();
    producto.Productor =encodeURIComponent($("#cboProducto option:selected").text());
    producto.unidad = $("#unidades").val();
    producto.unidad_nombre = $('#unidades option:selected').html();
    producto.count=countproducto;
    contador.con=$("#unidades").val();

    if ($("#unidades").val() != "" && $("#cboProducto").val()!="") {

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
                '</td><td >' +decodeURIComponent(value["Productor"]) +
                '</td><td >' + value["unidad_nombre"] +
                '</td><td class="actions">' +
                '<div class="btn-group"><a class="btn btn-default btn-mini btn-default" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="del_listaProducto(' + value["count"] + ');">' +
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
        })
        return false;

    }

}

function del_listaProducto(count) {

    $("#listar").attr("disabled", false);
    $("#listarTodos").attr("disabled", false);

    $("#lstTabla").show();
    var montos = 0;
    var $tabla = $("#lstTabla");
    $tabla.find("table").remove();
    $tabla.append('<table class="table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">' +
        '<thead><tr><th>Codigo</th><th>Producto</th><th>Unidad</th><th>Action</th></tr>' +
        '</thead></table>');
    var tbody = $('<tbody id="tbodyproductos"></tbody>');
    countproducto=0;
    jQuery.each(lst_producto, function (i, value) {
        if (value["count"] == count) {
            eliminar =i;
        }else {
            tbody.append(
                '<tr><td style="text-align: center;">' + value["Codigo"] +
                '</td><td >' + decodeURIComponent(value["Productor"]) +
                '</td><td >' + value["unidad_nombre"] +
                '</td><td class="actions">' +
                '<div class="btn-group"><a class="btn btn-default btn-mini btn-default" data-toggle="tooltip" title="Eliminar" data-original-title="Eliminar" onclick="del_listaProducto(' + value["count"] + ');">' +
                '<i class="fa fa-trash-o"></i></a>' +
                '</div></td></tr>'
            );
        }
    });
    lst_producto.splice(eliminar, 1);
    $tabla.find("table").append(tbody);

}

/*
 function del_listaTodo(count) {

 $("#listar").attr("disabled", false);
 $("#listarTodos").attr("disabled", false);

 var $tabla = $("#lstTabla");
 $tabla.find("table").remove();
 $tabla.append('<table class="table table-striped dataTable table-condensed table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">' +
 '<thead><tr><th>Codigo</th><th>Producto</th><th>Unidad</th><th>Action</th></tr>' +
 '</thead></table>');
 var tbody = $('<tbody id="tbodyproductos"></tbody>');
 countproducto=0;

 return !$('#cboProducto option').show();
 $("#lstTabla").splice();

 } */

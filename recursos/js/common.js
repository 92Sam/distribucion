/**
 * Created by Jhainey on 10/10/2015.
 */
function show_msg(type, msg) {
    $.bootstrapGrowl(msg, {
        type: type,
        delay: 5000,
        allow_dismiss: true
    });
}

function sumCod(code, length) {
    var len = length;

    if (len < code.length) len++;

    var temp = code.split("");
    temp = temp.reverse();

    var result = [];
    var n = 0;
    for (var i = len - 1; i >= 0; i--) {
        if (temp[n] != undefined)
            result.push(temp[n++]);
        else
            result.push("0");
    }

    result = result.reverse();
    return result.join("");
}

function formatPrice(price, min) {
    if (min == undefined)
        min = 10;
    var r = +(Math.round(price + "e+4") + "e-4");
    var round = r.toFixed(2).split('.');
    var entero = round[0];
    var fraccion = round[1];

    for (var i = 0; i <= 100; i = i + min) {
        if (i < fraccion && i + min > fraccion) {
            if ((i + min - fraccion) <= (fraccion - i))
                fraccion = i + min;
            else if ((i + min - fraccion) > (fraccion - i))
                fraccion = i;

            if (fraccion == 100) {
                fraccion = 0;
                entero = parseInt(entero) + 1;
            }
        }
    }
    return parseFloat(entero + '.' + fraccion).toFixed(2);
}

var region = {

    actualizarestados: function () {

        $.ajax({
            url: baseurl + 'estados/get_by_pais',
            type: 'POST',
            data: {'pais_id': $("#id_pais").val()},
            dataType: 'json',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data != 'undefined') {
                    var options = '<option value="">Seleccione</option>';
                    for (var i = 0; i < data.length; i++) {

                        options += '<option value="' + data[i].estados_id + '">' + data[i].estados_nombre + '</option>';

                    }

                    $("#estado_id").html(options);
                    $("#estado_id").trigger('chosen:updated');
                }
            }
        })
    },


    actualizardistritos: function () {

        $.ajax({
            url: baseurl + 'ciudad/get_by_estado',
            type: 'POST',
            data: {'estado_id': $("#estado_id").val()},
            dataType: 'json',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data != 'undefined') {
                    var options = '<option value="">Seleccione</option>';
                    for (var i = 0; i < data.length; i++) {

                        options += '<option value="' + data[i].ciudad_id + '">' + data[i].ciudad_nombre + '</option>';
                    }
                    $("#ciudad_id").html(options);
                    $("#ciudad_id").trigger('chosen:updated');
                    $("#zona option").empty();
                    $("#zona").trigger('chosen:updated');
                    $("#vendedor option").empty();
                    $("#vendedor").trigger('chosen:updated');
                }
            }
        })
    },

    actualizarzonas: function () {
        $.ajax({
            url: baseurl + 'zona/get_by_ciudad',
            type: 'POST',
            data: {'ciudad_id': $("#ciudad_id").val()},
            dataType: 'json',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                if (data != 'undefined') {
                    var options = '<option value="">Seleccione</option>';
                    for (var i = 0; i < data.length; i++) {

                        options += '<option value="' + data[i].zona_id + '">' + data[i].zona_nombre + '</option>';
                    }
                    $("#zona").html(options);
                    $("#zona").trigger('chosen:updated');
                    $("#vendedor option").empty();
                    $("#vendedor").trigger('chosen:updated');
                }
            }
        })
    } ,
    actualizarvendedor: function () {
        if($("#zona").val() != ''){
            $.ajax({
                url: baseurl + 'usuario/get_by_usuario',
                type: 'POST',
                data: {'zona_id': $("#zona").val()},
                dataType: 'json',
                headers: {
                    Accept: 'application/json'
                },
                success: function (data) {
                    if (data != 'undefined') {
                        $("#vendedor option").empty();
                        $("#vendedor").trigger('chosen:updated');
                        var options = '<option value="0">Seleccione</option>';
                        for (var i = 0; i < data.length; i++) {

                            options += '<option value="' + data[i].nUsuCodigo + '">' + data[i].nombre + '</option>';
                        }
                        $("#vendedor").html(options);
                        $("#vendedor").trigger('chosen:updated');
                    }
                }
            })
        }else{
            $("#vendedor option").empty();
            $("#vendedor").trigger('chosen:updated');
        }
    },
    actualizarzona: function () {
        if ($("#zona").val() == 0) {
            $.ajax({
                url: baseurl + 'zona/get_by_usuario_zona',
                type: 'POST',
                data: {'vendedor': $("#vendedor").val()},
                dataType: 'json',
                headers: {
                    Accept: 'application/json'
                },
                success: function (data) {
                    if (data != 'undefined') {

                        var options = '<option value="0">Seleccione</option>';
                        for (var i = 0; i < data.length; i++) {

                            options += '<option value="' + data[i].zona_id + '">' + data[i].zona_nombre + '</option>';
                        }
                        $("#zona").html(options);
                        $("#zona").trigger('chosen:updated');
                    }
                }
            })
        }
    }


}

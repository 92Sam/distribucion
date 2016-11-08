/**
 * Created by Jhainey on 10/10/2015.
 */

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
                }
            }
        })
    } ,
    actualizarvendedor: function () {
        if ($("#vendedor").val() == 0){
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

                        var options = '<option value="0">Seleccione</option>';
                        for (var i = 0; i < data.length; i++) {

                            options += '<option value="' + data[i].nUsuCodigo + '">' + data[i].nombre + '</option>';
                        }
                        $("#vendedor").html(options);
                        $("#vendedor").trigger('chosen:updated');
                    }
                }
            })
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

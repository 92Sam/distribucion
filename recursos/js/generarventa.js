var lst_producto = new Array();
var lst_bonos = new Array();

/**CACHE DE ELEMENTOS DEL DOM**/
var cache = {};
var countproducto = 0;
var escalaData = [];
var bonos = new Array();
// Var Min - Max
var min_precio = 0;
var max_precio = 0;
var countbonos = 0;
$(document).ready(function () {

    $(".closeseleccionunidades").on('click', function () {
        $("#seleccionunidades").modal('hide');


    });

    $(".closegenerarventa").on('click', function () {
        if ($("#ventamodal").length > 0 || $("#generarventa").length > 0) {
            $("#generarventa, #ventamodal").modal('hide');
        } else {
            $.ajax({
                url: ruta + 'venta/pedidos',
                success: function (data) {
                    $('#page-content').html(data);
                }
            });
        }
    });

    $(".closemodificarcantidad").on('click', function () {
        $("#modificarcantidad").modal('hide');

    });

    $("#cancelar").on('click', function (data) {
        if ($("#ventamodal").length > 0) {
            $("#ventamodal").modal('hide');
        }
        else {
            $.ajax({
                url: ruta + 'principal',
                success: function (data) {
                    $('#page-content').html(data);
                }
            });
        }
    });

    $("#reiniciar").on('click', function (data) {
        if ($("#ventamodal").length > 0) {
            return false;
        }
        else {
            $.ajax({
                url: ruta + 'venta',
                success: function (data) {
                    $('#page-content').html(data);
                }
            });
        }
    });

    ajaxRefresh = function (url) {
        return $.ajax({
            url: ruta + 'venta' + url
        });
    };

    $('body').off('keydown');
    $('body').on('keydown', function (e) {
        if (e.keyCode == 117) {
            e.preventDefault();
            if ($("#generarventa").is(":visible")) {
                hacerventa(1);
            } else {
                if (!$("#seleccionunidades").is(":visible") && !$("#ventasabiertas").is(":visible")) {
                    $("#generarventa").modal('show');
                }
            }
        }

        if (e.keyCode == 27) {
            if ($("#seleccionunidades").is(":visible")) {
                $("#seleccionunidades").modal('hide');
            }
        }

        if (e.keyCode == 40) {
            if ($("#seleccionunidades").is(":visible")) {
                if ($(".ui-selected").length != 0 && $("#cantidad").not(':focus').length == 1 && $("#agregarproducto").not(':focus').length == 1 && $("#precios_chosen .chosen-search input").not(':focus').length == 1) {

                    var next = parseInt($(".ui-selected").attr('tabindex'));
                    var len = jQuery("#preciostbody tr").length;

                    next = next + 1;
                    if (next == len) {
                        next = 0;
                    }

                    selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(" + next + ")"));

                    return 0;
                }
            }
        }

        if (e.keyCode == 38) {

            if ($("#seleccionunidades").is(":visible")) {
                var next = parseInt($(".ui-selected").attr('tabindex'));
                var len = parseInt(jQuery("#preciostbody tr").length);
                if (next == 0) {
                    next = len - 1;
                } else {
                    next = next - 1;
                }

                if ($(".ui-selected").length != 0 && $("#cantidad").not(':focus').length == 1 && $("#agregarproducto").not(':focus').length == 1 && $("#precios_chosen .chosen-search input").not(':focus').length == 1) {

                    selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(" + next + ")"));

                    return 0;
                }
            }
        }

        if (e.keyCode == 9) {

            if ($("#generarventa").is(":visible")) {
                e.stopPropagation();
                e.preventDefault();
                if ($("#importe").is(':focus')) {
                    $("#importe").blur();
                    $("#btnRealizarVentaAndView").focus();
                    return false;
                }

                if ($("#btnRealizarVentaAndView").is(':focus')) {
                    $("#btnRealizarVentaAndView").blur();
                    $("#importe").focus();
                    return false;
                }
            }

            if ($("#seleccionunidades").is(":visible")) {
                e.stopPropagation();
                e.preventDefault();

                if ($("#precios_chosen .chosen-search input").is(':focus')) {
                    $("#precios_chosen .chosen-search input").blur();
                    if ($(".ui-selected").length == 0) {
                        selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(0)"));
                    }
                    return false;
                }

                if ($(".ui-selected").length != 0 && $("#cantidad").not(':focus').length == 1 && $("#agregarproducto").not(':focus').length == 1 && $("#precios_chosen .chosen-search input").not(':focus').length == 1) {
                    $("#cantidad").removeAttr('readonly');
                    setTimeout(function () {
                        $("#cantidad").focus();
                        return false;
                    }, 5)
                }

                if ($("#cantidad").is(':focus')) {
                    $("#cantidad").blur();
                    $("#agregarproducto").focus();
                    return false;
                }

                if ($("#agregarproducto").is(':focus')) {
                    $("#agregarproducto").blur();
                    $("#precios_chosen .chosen-search input").focus();
                    return false;
                }

            }
        }
        handleF();
    });

    $("#cantidad").focus(function () {
        $("#cantidad").select();
    })

    $("select").chosen({
        width: "100%",
        search_contains: true
    });
    /*$("select[name!='id_cliente']").chosen({
     width: "100%"
     });


     if ($("#isadmin").val() == '1') {
     $("#vendedor").val('');
     }
     /*$("#id_cliente").select2({
     placeholder: 'Buscar Cliente',
     allowClear: true,
     initSelection: function (element, callback) {
     callback({id: 1, text: 'Text'});
     },
     ajax: {

     url: "../api/Clientes",
     dataType: 'json',
     delay: 250,
     data: function (params) {
     return {
     search: params.term, // search term
     select2: true,
     vendedor: $("#vendedor").val(),

     };
     },
     processResults: function (data, params) {
     // parse the results into the format expected by Select2
     // since we are using custom formatting functions we do not need to
     // alter the remote JSON data, except to indicate that infinite
     // scrolling can be used
     params.page = params.page || 1;

     return {
     results: data.clientes,

     };
     },
     cache: true
     },
     escapeMarkup: function (markup) {
     return markup;
     }, // let our custom formatter work
     minimumInputLength: 1,
     templateResult: formatRepo, // omitted for brevity, see the source of this page
     templateSelection: formatRepoSelection // omitted for brevity, see the source of this page

     });*/


    function formatRepo(repo) {
        if (repo.loading) return repo.text;

        var markup = "<div class='select2-result-repository clearfix'>" +
            "<div class='select2-result-repository__title'>" + repo.id_cliente + " - " + repo.representante + "</div>" +
            "</div>";

        return markup;
    }

    function formatRepoSelection(repo) {
        return repo.representante || repo.text;
    }

    setTimeout(function () {
        $("#selectproductos").trigger('chosen:open');
    }, 50);

    activarText_ModoPago();
    $("#lstTabla").hide();

    /***
     * Esta funcion se activa al hacer click en el boton guardar y levanta el modal para ingresar el importe y calcular el vuelto
     */
    $("#terminarventa").on('click', function () {

        $("#generarventa").modal('show');
    });

    $("#refrescarstock").on('click', function () {
        refrescarstock();
    });

    $("#abrirventas").on('click', function () {
        if ($("#ventamodal").length > 0) {
            return false;
        }
        buscarventasabiertas();
    });

    $("#agregarproducto").on('click', function () {

        agregarProducto();
    });

    $('#generarventa').on('shown.bs.modal', function (e) {
        document.getElementById("importe").focus();
        $("#importe").select();
    });

    $('#generarventa').on('hidden.bs.modal', function (e) {
        $("#importe").val(0, 0);

        // $("#vuelto").val(0, 0);
    });


    $('#modificarcantidad').on('hidden.bs.modal', function (e) {
        $("#bono_show").remove();
        bonos = new Array();

    });

    $('#seleccionunidades').on('hidden.bs.modal', function (e) {
        $("#bono_show").remove();

    });


    $('#seleccionunidades').on('hidden.bs.modal', function (e) {
        setTimeout(function () {
            $("#selectproductos").trigger('chosen:open');
        }, 50);

        clearFields();
    });

    $('#mvisualizarVenta').on('hidden.bs.modal', function (e) {


        var urlRefresh = $('#url_refresh').val();

        $("#generarventa").modal('hide');


        alertModal('<h4>Felicidades</h4> <p>La venta se ha guardado</p>', 'success', true);

        // alert('23');
        ajaxRefresh(urlRefresh).success(function (data) {
            $('#barloadermodal').modal('hide');
            $('#page-content').html(data);
        });
    });


// Check Precio
    $("#check_precio").on('change', function () {
        $("#precio_sugerido").val('');
        if ($(this).prop('checked')) {
            $('#precio_sugerido').show();
            $('#precio_detalle').show();
        } else {
            $('#precio_sugerido').hide();
            $('#precio_detalle').hide();
            checkCantidad();
        }
    });

    $("#cantidad").on('keyup', function () {
        checkCantidad();
    });


});


function checkCantidad() {
    // console.log('buscando');
    var cantidad = parseFloat($("#cantidad").val());
    //   console.log('cantidaddd'+cantidad);
    var cumplescala = false;
    if (escalaData.length > 0 && cantidad > 0) {
        for (var i = 0; i < escalaData.length; i++) {
            var data = escalaData[i];

            var unidad_id = data.unidad;

            if (cantidad >= parseFloat(data.cantidad_minima) && cantidad <= parseFloat(data.cantidad_maxima)) {

                var precio = data.precio;
                cumplescala = true;
            }

        }

        if (cumplescala === true) {
            $('#unidaddescuento' + unidad_id).val(precio);
            $('#precio_unidad_' + unidad_id).html(precio);
        } else {
            precio = $('#unidadprecio' + unidad_id).val();
            $('#unidaddescuento' + unidad_id).val(0);
            $('#precio_unidad_' + unidad_id).html(precio);
        }
    }
}


function selectSelectableElement(selectableContainer, elementToSelect) {
    // add unselecting class to all elements in the styleboard canvas except current one
    jQuery("tr", selectableContainer).each(function () {
        if (this != elementToSelect[0]) {
            jQuery(this).removeClass("ui-selected");
        }
    });

    // add ui-selecting class to the element to select
    elementToSelect.addClass("ui-selected");

    $("#cantidad").val(1);

    //console.log('as'+$("#cantidad").val());
    checkCantidad();
    selectableContainer.selectable('refresh');

    // trigger the mouse stop event (this will select all .ui-selecting elements, and deselect all .ui-unselecting elements)
    selectableContainer.data("selectable")._mouseStop(null);


}

function handleF() {
    $(document).on('keydown', function (e) {
        if (e.keyCode == 116) {
            e.preventDefault();
            e.stopPropagation();
            // $(this).next().focus();  //Use whatever selector necessary to focus the 'next' input
            return false;
        }

        if (e.keyCode == 114) {
            e.preventDefault();
            e.stopPropagation();
            if ($(".modal").is(":visible")) {
                return false;
            }

            $('#barloadermodal').modal('show');

            $.ajax({
                url: ruta + 'venta',
                success: function (data) {
                    if (data.error == undefined) {
                        $('#page-content').html(data);
                    } else {
                        alertModal('<h4>' + data.error + '</h4>', 'warning', true);
                    }
                    $('#barloadermodal').modal('hide');
                },
                error: function (response) {
                    $('#barloadermodal').modal('hide');
                    alertModal('<h4>Ha ocurrido un error al realizar la operacion</h4>', 'warning', true);
                }
            });
        }

        if (e.keyCode == 113) {

            e.preventDefault();
            e.stopPropagation();

            if ($(".modal").is(":visible")) {
                return false;
            }

            $('#barloadermodal').modal('show');

            $.ajax({
                url: ruta + 'producto/stock',
                success: function (data) {
                    if (data.error == undefined) {
                        $('#page-content').html(data);
                    } else {
                        alertModal('<h4>' + data.error + '</h4>', 'warning', true);
                    }
                    $('#barloadermodal').modal('hide');
                },
                error: function (response) {
                    $('#barloadermodal').modal('hide');
                    alertModal('<h4>Ha ocurrido un error al realizar la operacion</h4>', 'warning', true);
                }
            });
        }
    });
}

function refrescarstock() {
    $("#barloadermodal").modal({
        show: true,
        backdrop: 'static'
    });
    $.ajax({
        url: ruta + 'inventario/getbylocal',
        data: {local: $("#idlocal").val()},
        type: 'post',
        dataType: 'json',
        success: function (data) {
            var newlist = lst_producto;
            lst_producto = new Array();
            var lst_bonos = new Array();
            $("#selectproductos").val('');
            $("#selectproductos").html('<option value="">Seleccione<option>');

            for (var i = 0; i < data.length; i++) {
                var option = '<option value="' + data[i].producto_id + '">' + data[i].producto_id_cero + ' - ' + data[i].producto_nombre + '</option>';
                $("#selectproductos").append(option);

                var stockhidden = $("#stockhidden" + data[i].producto_id);

                if (stockhidden.length > 0) {
                    stockhidden.val(0);
                    var cantidad_total = (parseFloat(data[i].unidades) * parseFloat(data[i].cantidad));
                    stockhidden.val(cantidad_total);
                }
            }

            $("#selectproductos").trigger("chosen:updated");
            $("#tbodyproductos").html('');

            countproducto = 0;
            $("#subTotal").val(formatPrice(0));
            $("#montoigv").val(formatPrice(0));
            $("#totApagar").val(formatPrice(0));
            $("#totApagar2").html(formatPrice(0));

            jQuery.each(newlist, function (i, value) {

                calculatotales(value.id_producto, value.nombre, value.unidad_nombre, value.cantidad, value.precio, value.detalle_importe, value.porcentaje_impuesto, countproducto, value.unidades, value.producto_cualidad, value.unidad_medida, value.precio_sugerido, value.bono);
                addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, value.cantidad, value.precio, value.precio_sugerido, value.detalle_importe, value.unidades, value.producto_cualidad, value.porcentaje_impuesto, value.bono, value.venta_sin_stock);

                var stockhidden = $("#stockhidden" + value.id_producto);
                var cantidad_total = parseFloat((stockhidden.val() - value.unidades * value.cantidad));
                stockhidden.val(cantidad_total);
            });
            recorrerBonos();
            $('#barloadermodal').modal('hide');
        },
        error: function () {
            $('#barloadermodal').modal('hide');
        }
    })
}

function alertModal(message, type, disabled) {
    $.bootstrapGrowl(message, {type: type, delay: 2500, allow_dismiss: true});
    if (disabled) {
        $(this).prop('disabled', true);
    }
}

function hacerventa(imprimir, flag) {
    if ($('#tipo_documento').val() == 'FACTURA') {
        if ($('#razon_social').val() == '') {
            $.bootstrapGrowl('<h4>Debe ingresar razon social</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false
        }

        if ($('#ruc_dc').val() == '') {
            $.bootstrapGrowl('<h4>Debe ingresar RUC</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);
            return false
        }
    }

    $("#realizarventa").addClass('disabled');
    $("#btnRealizarVentaAndView").addClass('disabled');

    if ($("#id_cliente").val() == '') {
        alertModal('<h4>Datos incompletos</h4> <p>Debe seleccionar el cliente</p>', 'warning', true);
        $("#realizarventa").removeClass('disabled');
        $("#btnRealizarVentaAndView").removeClass('disabled');
        return false;
    }

    if ($("#tipo_documento").val() == '') {
        alertModal('<h4>Datos incompletos</h4> <p>Debe seleccionar el tipo de documento</p>', 'warning', true);
        $("#realizarventa").removeClass('disabled');
        $("#btnRealizarVentaAndView").removeClass('disabled');
        return false;
    }

    if ($("#cboModPag").val() == '') {
        alertModal('<h4>Datos incompletos</h4> <p>Debe seleccionar el modo de pago</p>', 'warning', true);
        $("#realizarventa").removeClass('disabled');
        $("#btnRealizarVentaAndView").removeClass('disabled');
        return false;
    }

    if ($("#venta_status").val() == '') {
        alertModal('<h4>Datos incompletos</h4> <p>Debe seleccionar el status de la venta</p>', 'warning', true);
        $("#realizarventa").removeClass('disabled');
        $("#btnRealizarVentaAndView").removeClass('disabled');
        return false;
    }

    if ($("#tbodyproductos tr[id^='producto']").length == 0) {
        alertModal('<h4>Datos incompletos</h4> <p>Debe seleccionar al menos un producto</p>', 'warning', true);
        $("#realizarventa").removeClass('disabled');
        $("#btnRealizarVentaAndView").removeClass('disabled');
        return false;
    }


    var dias = $('#diascondicionpagoinput').val();
    var importe = parseFloat($('#importe').val());
    var pagado = 0;
    if ($('#pagado').length > 0) {
        pagado = parseFloat($('#pagado').val());
    }
    var totap = parseFloat($('#totApagar').val());

    if (pagado != 0) {
        totap = totap - pagado;
        pagado = 0;

    }
    // var vuelto = parseFloat($('#vuelto').val());
    var devolver = $('#devolver').val();
    var urlRefresh = $('#url_refresh').val();
    var venta_tipo = $("#venta_tipo").val();


    if ((importe - pagado) <= totap) {

        if (importe >= 0) {
            if (dias < 1) {
                $("#barloadermodal").modal({
                    show: true,
                    backdrop: 'static'
                });
                var miJSON = JSON.stringify(lst_producto);
                var losbonos = JSON.stringify(lst_bonos);
                var nom_doc = $("#cboTipDoc option:selected").html();
                $.ajax({
                    type: 'POST',
                    data: $('#frmVenta').serialize() + '&lst_producto=' + miJSON + '&devolver=' + devolver + '&lst_bonos=' + losbonos,
                    dataType: 'json',
                    url: ruta + 'venta/registrar_venta',
                    success: function (data) {
                        $('#barloadermodal').modal('hide');
                        $("#generarventa").modal('hide');

                        if (data.msj == 'guardo') {
                            if ($("#ventamodal").is(":visible")) {
                                $("#generarventa").modal('hide');
                            }

                            if (imprimir == 0 && flag == undefined) {
                                alertModal('<h4>Felicidades</h4> <p>La venta se ha guardado</p>', 'success', false);


                                setTimeout(function () {
                                    //alert(urlRefresh);
                                    ajaxRefresh(urlRefresh).success(function (datat) {


                                        if ($("#ventamodal").length > 0) {


                                            if ($("#cambiarEstatus").hasClass('in')) {
                                                $("#ventamodal").modal('hide');
                                                $('#cambiarEstatus').modal('hide');
                                                if (data.estatus_consolidado != undefined) {
                                                    $("#consolidadoLiquidacion").load(ruta + 'consolidadodecargas/verDetallesLiquidacion/' + $('#coso_id').val() + '/' + data.estatus_consolidado);
                                                } else {
                                                    $("#consolidadoLiquidacion").load(ruta + 'consolidadodecargas/verDetallesLiquidacion/' + $('#coso_id').val());
                                                }

                                            }
                                            else {

                                                $("#ventamodal").on("hidden.bs.modal", function () {
                                                    $('#page-content').html(datat);
                                                });

                                                $("#ventamodal").modal('hide');

                                            }
                                        } else {
                                            $('#page-content').html(datat);
                                        }


                                    })
                                }, 1000)

                            }
                            else if (flag == undefined) {
                                cargaData_Impresion(data.idventa);
                            }
                            else if (flag == 1) {
                                alertModal('<h4>Felicidades</h4> <p>La venta ha sido editada correctamente</p>', 'success', false);
                                $("#ventamodal").modal('hide');
                                $('.btn_buscar').click();
                            }
                            else if (flag == 2) {
                                alertModal('<h4>Felicidades</h4> <p>La venta ha sido modificada correctamente</p>', 'success', false);
                                $("#total").val(formatPrice($("#totApagar").val()));
                                $("#ventamodal").modal('hide');
                            }
                        } else {
                            if(data.sin_stock == undefined){
                                alertModal('<h4>Error</h4> <p> Ha ocurrido un error al guardar la venta</p>', 'warning', true);
                            } else {
                                alertModal('<h4>Error</h4> <p> No hay stock para terminar el pedido</p>', 'warning', true);
                                /*var str = '';
                                for(var i = 0; i < data.sin_stock.length; i++){
                                    str += 'Producto: ' + data.sin_stock[i].producto_id + ', cantidad: ' + data.sin_stock[i].cantidad_actual + '<br>';
                                }
                                alertModal('<h4>Detalles</h4> <p> '+str+'</p>', 'warning', true);*/
                            }
                            $("#realizarventa").removeClass('disabled');
                            $("#btnRealizarVentaAndView").removeClass('disabled');
                            return false;
                        }
                    },
                    error: function (error) {
                        $('#barloadermodal').modal('hide');
                        $("#realizarventa").removeClass('disabled');
                        $("#btnRealizarVentaAndView").removeClass('disabled');
                        alertModal('<h4>Error</h4> <p> Ha ocurrido un error al guardar la venta</p>', 'warning', true);
                    }
                });
                return false;

            } else {
                if (venta_tipo == 'ENTREGA' && devolver == 'true') {

                } else {
                    if (importe >= totap) {
                        $("#realizarventa").removeClass('disabled');
                        $("#btnRealizarVentaAndView").removeClass('disabled');
                        alertModal('<h4>El importe cancelado es igual al total de la venta </h4> <p> La venta se guardará a contado </p>', 'info', true);

                    }
                }

                $("#barloadermodal").modal({
                    show: true,
                    backdrop: 'static'
                });
                var miJSON = JSON.stringify(lst_producto);
                $.ajax({
                    type: 'POST',
                    data: $('#frmVenta').serialize() + '&lst_producto=' + miJSON + '&devolver=' + devolver + '&lst_bonos=' + losbonos,
                    dataType: 'json',
                    url: ruta + 'venta/registrar_venta',
                    success: function (data) {

                        $('#barloadermodal').modal('hide');
                        $("#generarventa").modal('hide');
                        if (data.msj == 'guardo') {

                            if ($("#generarventa").is(":visible")) {
                                $("#generarventa").modal('hide');
                            }

                            if (imprimir == 0) {
                                alertModal('<h4>Felicidades</h4> <p>La venta se ha guardado</p>', 'success', false);

                                //isliquidacion();

                                setTimeout(function () {
                                    ajaxRefresh(urlRefresh).success(function (datat) {

                                        if ($("#ventamodal").length > 0) {

                                            $("#ventamodal").modal('hide');
                                            if ($("#cambiarEstatus").hasClass('in')) {
                                                $('#cambiarEstatus').modal('hide');
                                                if (data.estatus_consolidado != undefined) {
                                                    $("#consolidadoLiquidacion").load(ruta + 'consolidadodecargas/verDetallesLiquidacion/' + $('#coso_id').val() + '/' + data.estatus_consolidado);
                                                } else {
                                                    $("#consolidadoLiquidacion").load(ruta + 'consolidadodecargas/verDetallesLiquidacion/' + $('#coso_id').val());
                                                }

                                            } else {
                                                $('#page-content').html(datat);
                                            }
                                        } else {
                                            $('#page-content').html(datat);
                                        }


                                    })
                                }, 1000)
                            }
                            else {
                                cargaData_Impresion(data.idventa);
                            }
                        } else {
                            $("#realizarventa").removeClass('disabled');
                            $("#btnRealizarVentaAndView").removeClass('disabled');
                            if(data.sin_stock == undefined)
                                alertModal('<h4>Error</h4> <p> Ha ocurrido un error al guardar la venta</p>', 'warning', true);
                            else {
                                alertModal('<h4>Error</h4> <p> No hay stock para terminar el pedido</p>', 'warning', true);
                            }
                            return false;
                        }
                    },
                    error: function (error) {
                        $('#barloadermodal').modal('hide');
                        $("#realizarventa").removeClass('disabled');
                        $("#btnRealizarVentaAndView").removeClass('disabled');
                        alertModal('<h4>Error</h4> <p> Ha ocurrido un error al guardar la venta</p>', 'warning', true);
                    }
                });

                return false;
                $("#realizarventa").removeClass('disabled');
                $("#btnRealizarVentaAndView").removeClass('disabled');

            }
        } else {
            alertModal('<h4>No se admite</h4> <p> El importe debe ser mayor o igual a 0</p>', 'warning', true);
            $("#realizarventa").removeClass('disabled');
            $("#btnRealizarVentaAndView").removeClass('disabled');
            return false;
        }

    } else {
        alertModal('<h4>Error</h4> <p> Por favor ingrese un monto menor o igual al total </p>', 'warning', true);
        $("#realizarventa").removeClass('disabled');
        $("#btnRealizarVentaAndView").removeClass('disabled');
        return false;
    }
}


/**
 * Actualiza la tabla de precios y unidades cuando seleciono un precio del select
 */
function cambiarnombreprecio() {
    $("#cantidad").val(0);
    $("#cantidad").attr('readonly', true);

    var tr = '';
    $("#preciostbody").html(tr);
    $("#tituloprecio").html($("#precios :selected").text());

    $.ajax({
        url: ruta + 'producto/preciosporproducto',
        data: {'producto': $("#selectproductos").val(), 'precio': $("#precios").val()},
        type: 'POST',
        dataType: "json",
        success: function (data) {

            for (var i = 0; i < data.length; i++) {
                tr = "<tr tabindex='" + i + "' id='" + data[i].id_unidad + "'>" +
                    "<td>" +
                    "<input type='hidden' name='unidadnombre' id='unidadnombre" + data[i].id_unidad + "' value='" + data[i].nombre_unidad + "'/>" + data[i].nombre_unidad + "</td>" +
                    "<input type='hidden' name='unidades' id='unidades" + data[i].id_unidad + "' value='" + data[i].unidades + "'/>" +
                    "<td>" + data[i].unidades + "</td>";
                if (data[i].nombre_precio == $("#tituloprecio").text()) {
                    tr += "<td><input type='hidden' name='unidadprecio' id='unidadprecio" + data[i].id_unidad + "' value='" + data[i].precio + "'/>" +
                        "<input type='hidden' name='porcentaje_impuesto' id='porcentaje_impuesto" + data[i].id_unidad + "' value='" + data[i].porcentaje_impuesto + "'/>" +
                        "<input type='hidden' name='unidaddescuento' id='unidaddescuento" + data[i].id_unidad + "' value='" + data[i].descuento_precio + "'/><span id='precio_unidad_" + data[i].id_unidad + "'>" + data[i].precio + "</span></td>";
                }
                tr += "</tr>";
                $("#preciostbody").append(tr);
            }

            $("#preciostbody").selectable({
                stop: function () {
                    checkCantidad();
                    var id_unidad = $("#preciostbody tr.ui-selected").attr('id');
                    var id_producto = $('#selectproductos').val();
                    getUnidadPrecio(id_producto, id_unidad, $('#grupo_cliente_1').attr('data-id'));
                    var id = $("#preciostbody tr.ui-selected").attr('id');
                    $("#cantidad").removeAttr('readonly');
                    $("#cantidad").val(1);
                    $("#cantidad").blur();
                    $("#precios_chosen .chosen-search input").blur();
                    $("#agregarproducto").blur();
                }
            });

            checkCantidad();
            var id_unidad = data[0].id_unidad;
            var id_producto = $('#selectproductos').val();
            getUnidadPrecio(id_producto, id_unidad, $('#grupo_cliente_1').attr('data-id'));
        }
    })
}

//agregar ceros a la izquierda
function pad_with_zeroes(number, length) {

    var my_string = '' + number;
    while (my_string.length < length) {
        my_string = '0' + my_string;
    }

    return my_string;

}
function agregarProducto() {

    if ($("#check_precio").prop('checked')) {
        var precio = $("#precio_sugerido").val() == '' ? 0 : parseFloat($("#precio_sugerido").val());
        var min_precio = parseFloat($("#min_precio").html().trim());
        var max_precio = parseFloat($("#max_precio").html().trim());
        if (precio < min_precio || precio > max_precio) {
            show_msg('warning', '<h4>Error.</h4> El precio sugerido no no esta en el rango permitido.');
            return false;
        }
    }

    var unidad_id = $("#preciostbody tr.ui-selected").attr('id');
    var producto_id = $('#selectproductos').val();

    var existe = false;

    jQuery.each(lst_producto, function (i, value) {

        if (unidad_id == value.unidad_medida && producto_id == value.id_producto) {
            existe = true;
        }
    });

    if (existe == true) {
        alertModal('<h4>Ya existe un registro para este producto con la misma unidad!</h4> <p>Si desea modificar la cantidad seleccione editar</p>', 'warning', true);
        return false;
    } else {

        var producto_id_cero = pad_with_zeroes($('#selectproductos').val());
        var producto_nombre = $('#selectproductos :selected').text();
        var producto_nombre = $('#selectproductos :selected').text();
        producto_nombre = producto_nombre.substring(producto_nombre.indexOf("-") + 1);
        //  producto_nombre=producto_nombre.substring(producto_nombre.indexOf("*")+1);
        var unidad_nombre = $('#unidadnombre' + unidad_id).val();
        var cantidad = parseFloat($('#cantidad').val());

        var precio = parseFloat($('#unidadprecio' + unidad_id).val());
        //if ($("#check_precio").prop('checked'))
        //   precio = parseFloat($('#precio_sugerido').val());

        var precio_sugerido = isNaN(parseFloat($('#precio_sugerido').val())) ? 0 : parseFloat($('#precio_sugerido').val());


        var precio_id = $('#precios').val();
        var porcentaje_impuesto = $('#porcentaje_impuesto' + unidad_id).val();
        var unidaddescuento = $('#unidaddescuento' + unidad_id).val();
        var unidades = parseFloat($('#unidades' + unidad_id).val());
        var stockStatus = $("#stock_status_" + producto_id).val();
        var ventaStatus = $("#venta_status").val();
        var stockhidden = $("#stockhidden" + producto_id);
        // Precio Descuento
        if (unidaddescuento > 0 && $("#check_precio").prop('checked') == false) {
            precio = unidaddescuento;
        }

        var subtotal = precio * cantidad;
        console.log('subtotal' + subtotal);
        var cantidad_total = (parseFloat(stockhidden.val()) - unidades * cantidad);
        // Stock Insuficiente - Producto

        // console.log('stockStatus ' +parseInt(stockStatus));

        //if (ventaStatus != 'GENERADO' && (cantidad_total > stockhidden.val() || cantidad_total < 0))
        if (parseFloat(cantidad_total) > parseFloat(stockhidden.val()) || cantidad_total < 0) {
            alertModal('<h4>Stock Insuficiente!</h4> <p>' + producto_nombre + '</p>', 'warning', true);
            return false;
        }
        // Datos Incompleto - Unidad
        if (unidad_id === undefined) {
            alertModal('<h4>Datos incompletos:</h4> <p>Seleccione la unidad</p>', 'danger', true);
            return false;
        }
        var producto_cualidad = $("#producto_cualidad").val();
        if (producto_cualidad == "MEDIBLE") {
            cantidadminima = 1;
        } else {
            var length = $("#preciostbody tr").length;
            var is_last_item;
            $("#preciostbody tr").each(function (index) {
                if ((index == (length - 1))) {
                    is_last_item = $(this).attr('id');
                }
            });
            if (is_last_item == unidad_id) {
                cantidadminima = 0.1;
            } else {
                cantidadminima = 1;
            }
        }
        // Datos Incompleto - Cantidad
        if (cantidad == '' || isNaN(cantidad) || cantidad < cantidadminima) {
            alertModal('<h4>Datos incompletos:</h4> <p>Ingrese una cantidad mayor a ' + cantidadminima + ' y menor al stock</p>', 'warning', true);
            return false;
        }
        calculatotales(producto_id, producto_nombre, unidad_nombre, cantidad, precio, subtotal, porcentaje_impuesto, countproducto, unidades, producto_cualidad, unidad_id, precio_sugerido, 'false');
        addProductoToArray(producto_id, encodeURIComponent(producto_nombre), unidad_id, unidad_nombre, cantidad, precio, precio_sugerido, subtotal, unidades, producto_cualidad, porcentaje_impuesto, 'false', stockStatus);

        var unidadselecionada = parseInt(unidad_id);

        addBonosToTable(cantidad, unidadselecionada, producto_id);
        clearFields();
        $("#stock_status").val(0);
        var stockhidden = $("#stockhidden" + producto_id);
        var cantidad_total = parseFloat((stockhidden.val() - unidades * cantidad));
        stockhidden.val(cantidad_total);
        $('#seleccionunidades').modal('toggle');
    }
}

// Bonificaciones
function getBonificacion(id, productoeliminar, lista_temporal) {
    bonos = new Array();
    if (id) {
        $("#bono_producto").html('');

        $.ajax({
            type: 'GET',
            data: {'id': id, 'grupo': grupo_id, 'array': true},
            dataType: 'JSON',
            url: ruta + 'api/Bonificaciones/ver_genventa',
            success: function (data) {
                var data = data.bonificaciones;
                if (data.length > 0) {

                    for (var i = 0; i < data.length; i++) {
                        var bono = {};
                        bono.id_bonificacion = data[i].id_bonificacion;
                        bono.id_unidad = data[i].id_unidad;
                        bono.nombre_unidad = data[i].nombre_unidad;
                        bono.condicion_cantidad = data[i].cantidad_condicion;
                        $(".bono_descripcion").html($("#nombreproduto").html());
                        bono.bono_id = data[i].bono_producto;
                        bono.bono_producto = data[i].producto_bonificacion;
                        bono.bono_cantidad = data[i].bono_cantidad;
                        bono.bono_unidad_id = data[i].bono_unidad;
                        bono.bono_unidad = data[i].unidad_bonificacion;
                        bono.bono = "true";
                        bono.id_familia = data[i].id_familia;
                        bono.id_grupo = data[i].id_grupo;
                        bono.id_marca = data[i].id_marca;
                        bono.id_linea = data[i].id_linea;
                        bono.fecha = data[i].fecha;
                        bono.unidades = data[i].unidades;
                        bono.venta_sin_stock_bono = data[i].venta_sin_stock_bono;
                        bono.porcentaje_impuesto = data[i].porcentaje_impuesto;
                        bono.bonochecked = false;
                        bono.bonificaciones_has_producto = data[i].bonificaciones_has_producto;
                        bonos.push(bono);
                        /*Actualizo la existencia */
                        $.ajax({
                            type: 'POST',
                            data: {'producto': bono.bono_id},
                            dataType: "json",
                            url: ruta + 'inventario/get_existencia_producto',
                            success: function (data2) {
                                if ($("#stockhidden" + bono.bono_id).length > 0) {
                                    var val = parseFloat($("#stockhidden" + bono.bono_id).val());
                                    var nueva_cantidad = (parseFloat(data2.existencia_unidad) * parseFloat(data2.maxima_unidades)) + parseFloat(data2.existencia_fraccion);
                                    $("#stockhidden" + bono.bono_id).attr('value', nueva_cantidad);
                                } else {
                                    $("#inentariocontainer").append('<input id="stockhidden' + bono.bono_id + '" type="hiden" value="0"/><input id="stock_status_' + bono.bono_id + '" type="hiden" value="' + bono.venta_sin_stock_bono + '"/>');
                                    var nueva_cantidad = (parseFloat(data2.existencia_unidad) * parseFloat(data2.maxima_unidades)) + parseFloat(data2.existencia_fraccion);
                                    $("#stockhidden" + bono.bono_id).attr('value', nueva_cantidad);
                                }
                            }
                        });
                        var unidad_condicion = bono.nombre_unidad == null ? '' : bono.nombre_unidad;
                        var tr = "<tr tabindex='" + i + "' id='" + bono.bono_id + "'>";
                        tr += "<td>" + bono.condicion_cantidad + " " + unidad_condicion + "</td>";
                        tr += "<td>" + bono.bono_producto + "</td>";
                        tr += "<td>" + data[i].unidad_bonificacion + "</td>";
                        tr += "<td>" + bono.bono_cantidad + "</td>";

                        tr += "</tr>";
                        $("#bono_producto").append(tr);
                    }
                    $("#bono_show").show();
                } else {
                    $("#bono_show").hide();
                }

                if (productoeliminar != undefined) {
                    addBonosToTable(0, productoeliminar.unidad_medida, productoeliminar.id_producto, productoeliminar.cantidad);
                    // deleteBono(productoeliminar.unidad_medida, productoeliminar.cantidad, productoeliminar.id_producto)
                }
            },
            error: function (xhr, textStatus, error) {
                $("#bono_show").hide();
                console.log('[Bonificacion Error] ' + textStatus);
            }
        });
    } else {
        console.log('[Bonificacion Error ID]');
    }

    console.log(bonos);
}

function agregarBono(id_bonificacion, bono_id, bono_producto, bono_cantidad, bono_unidad_id, bono_unidad) {


    // if ($("#check_bono_" + id_bonificacion).is(':checked')) {
    var conteo = countproducto + 1;

    var tr = "<tr id='boni-" + bono_id + "-" + bono_unidad_id + "' class='bono-i'>" +
        "<td>" + conteo + " BONO </td>" +
        "<td>" + pad_with_zeroes(bono_id) + "</td>" +
        "<td>" + decodeURIComponent(bono_producto) + "</td>" +
        "<td>" + bono_unidad + "</td>" +
        "<td>" + bono_cantidad + "</td>" +
        "<td>0</td>" +
        "<td>0</td>" +
        "<td> -- </td>" +
        "<td> -- </td>" +
        "<td> -- </td>" +
        "</tr>";

    // Add Html
    $("#tbodyproductos").append(tr);
    countbonos++;

}


function addBonosToTable(lacantidadcomprada, unidadseleccionada, idprpod, cantidadanterior) {


    jQuery.each(lst_bonos, function (i, v) {
        //Primero devuelvoel stock jiji
        bonoDevolverStock(v);
    });

    /*****************RECORRO TODOS LOS BONOS**/
    var today = new Date();
    //console.log(bonos);
    //console.log('los bono posibls');


    var recorrer = bonos;

    var bonos_arr = new Array();

    var bono_uniq = new Array();


    //console.log(recorrer);
    jQuery.each(recorrer, function (i, valuee) {
        var existe_uniq = false;
        var yaexiste = false;
        var yaexiste2 = false;
        // console.log('el bono');


        if (bonos_arr.length == 0) {
            var bono_uniq = new Array();
            bono_uniq.push(valuee);
            bonos_arr.push(bono_uniq);
        } else {

            jQuery.each(bonos_arr, function (o, valuest) {


                jQuery.each(valuest, function (p, valuej) {

                    // console.log(valuej.bono_id);

                    if (valuej.bono_id === valuee.bono_id) {


                        //  console.log(valuee);
                        jQuery.each(valuest, function (a, valuea) {
                            if (valuea.bono_id === valuee.bono_id && valuea.id_bonificacion === valuee.id_bonificacion) {
                                //   console.log(valuea.bono_id + "es iguaal");
                                yaexiste2 = true;
                            }
                        });

                        if (yaexiste2 === false) {
                            var bono_uniq = new Array();
                            bono_uniq = valuest;
                            //   console.log('existee ' + valuee);
                            bono_uniq.push(valuee);
                            bonos_arr.splice(p, 1);

                            bonos_arr.push(bono_uniq);
                        }
                    }
                    else {


                        existe_uniq = true


                    }
                });

                if (existe_uniq == true) {
                    //   console.log(bonos_arr);
                    jQuery.each(bonos_arr, function (a, valuea) {
                        jQuery.each(valuea, function (b, valueb) {
                            if (valueb.bono_id === valuee.bono_id) {
                                //  console.log(valuee);
                                //  console.log(valuea.bono_id + "es iguaal");
                                yaexiste = true;
                            }
                        });
                    });

                    if (yaexiste === false) {
                        // console.log(' no existeee ' + valuee.bono_id);

                        var bono_uniq = new Array();
                        bono_uniq.push(valuee);
                        bonos_arr.push(bono_uniq);
                    }

                }

            });


        }


        //  console.log(bonos_arr);
    });


    //console.log(recorrer);
    //console.log(bonos_arr);
    //console.log('los bonos separados');
    var cantidad_tomar_en_cuenta = lacantidadcomprada;
    var cantidad_tomar_en_cuenta_AUX = lacantidadcomprada;



    jQuery.each(bonos_arr, function (o, values) {
        var cantidad_p=0;
        jQuery.each(values, function (i, bonito) {
            cantidad_p = 0;
            jQuery.each(lst_producto, function (mm, produtyy) {
                // if (parseInt(produtyy.id_producto) === parseInt(bonito.bono_id)) {
                jQuery.each(bonito.bonificaciones_has_producto, function (oo, bonificicion_has_ppp) {
                    var esigual = parseInt(bonificicion_has_ppp.id_producto) === parseInt(produtyy.id_producto);
                    console.log(esigual);
                    if (esigual === true && produtyy.bono != 'true') {

                        cantidad_p = parseFloat(produtyy.cantidad) + cantidad_p;


                    }
                });

            });
        });

        console.log('cantidad_tomar_en_cuenta1'+cantidad_tomar_en_cuenta);
        cantidad_tomar_en_cuenta = cantidad_p;
        cantidad_tomar_en_cuenta_AUX = cantidad_p;

        console.log('cantidad_tomar_en_cuenta2'+cantidad_tomar_en_cuenta);


        var cantidad_bonificar = 0;
        jQuery.each(values, function (i, bonito) {

            //           console.log(cantidad_tomar_en_cuenta + ' cantidad_bonificar');
            //console.log(bonito.condicion_cantidad+ ' bonito.condicion_cantidad');
            var vecesbono = parseInt(cantidad_tomar_en_cuenta / parseFloat(bonito.condicion_cantidad));

            var newcantidad = vecesbono * parseFloat(bonito.bono_cantidad);
            cantidad_bonificar = cantidad_bonificar + newcantidad;
            //  console.log(cantidad_bonificar);
            console.log(newcantidad+ ' newcantidad');
            //console.log(vecesbono+ ' vecesbono');

            console.log(cantidad_tomar_en_cuenta);
            //console.log(bonito);
            if (((bonito.id_unidad != null && cantidad_tomar_en_cuenta >= parseInt(bonito.condicion_cantidad) && parseInt(unidadseleccionada) == parseInt(bonito.id_unidad))
                || (bonito.id_unidad == null && parseInt(cantidad_tomar_en_cuenta) >= parseInt(bonito.condicion_cantidad) ))) {


                var elbonito = new Array();
                elbonito = bonito;
                elbonito.bono_cantidad = cantidad_bonificar;



                /******busco para ver si el bono ya existe********/

                jQuery.each(lst_bonos, function (j, produt) {

                        // console.log(  produt );
                        //console.log(  bonito );
                        //lo elimino de la lista de bonos para agregarlos con la nuva cantidad
                        if (produt != undefined) {
                            if (parseInt(bonito.bono_id) == parseInt(produt.bono_id) && parseInt(bonito.bono_unidad_id) == parseInt(produt.bono_unidad_id) && produt.bono != 'false') {

                                console.log('redzco cantidad');
                                console.log(newcantidad);
                                lst_bonos.splice(j, 1);
                                $("#boni-" + bonito.bono_id + "-" + bonito.bono_unidad_id).remove();
                            }
                        }
                    }
                );

                // console.log(vecesbono);
                console.log('cantidad_tomar_en_cuentaaaa' + cantidad_tomar_en_cuenta);

                console.log('vecesbono' + vecesbono);
                console.log(' elbonito.bono_cantidad' +  elbonito.bono_cantidad);
                console.log('cantidad_bonificar' + cantidad_bonificar);
                console.log('bonito.condicion_cantidad' + bonito.condicion_cantidad);
                //console.log(bonito.condicion_cantidad);
                cantidad_tomar_en_cuenta = cantidad_tomar_en_cuenta - (vecesbono * parseFloat(bonito.condicion_cantidad));

                console.log('cantidad_tomar_en_cuenta' + cantidad_tomar_en_cuenta);
                alertModal('<h4>Se ha añadido un bono de ' + newcantidad + ' ' + bonito.bono_unidad + ' ' + bonito.bono_producto + ' </h4>', 'info', true);
                lst_bonos.push(elbonito);
                // console.log(elbonito);


            }
            else {


                var eliminar = true;


                console.log(bonito);
                var countarcoinci = 0;


                if (cantidadanterior != undefined) {
                    var cantidad_tomar_en_cuenta_temp = cantidad_tomar_en_cuenta;
                    console.log(lst_producto);
                    jQuery.each(lst_producto, function (mm, produtyy) {
                        // if (parseInt(produtyy.id_producto) === parseInt(bonito.bono_id)) {
                        jQuery.each(bonito.bonificaciones_has_producto, function (oo, bonificicion_has_ppp) {
                            var esigual = parseInt(bonificicion_has_ppp.id_producto) === parseInt(produtyy.id_producto);
                            // console.log(esigual);
                            if (esigual === true && produtyy.bono != 'true') {
                                console.log(bonificicion_has_ppp);
                                console.log(produtyy);
                                countarcoinci++;


                                if (bonificicion_has_ppp.id_producto != idprpod) {
                                    cantidad_tomar_en_cuenta_temp = cantidad_tomar_en_cuenta_temp + cantidadanterior;
                                }

                            }
                        });


                    });


                    console.log('cantidadanterior' + cantidadanterior);
                    console.log('countarcoinci' + countarcoinci);
                    console.log('lacantidadcomprada' + lacantidadcomprada);
                    console.log('cantidad_tomar_en_cuenta_AUX' + cantidad_tomar_en_cuenta_AUX);
                    console.log('cantidad_tomar_en_cuenta_temp' + cantidad_tomar_en_cuenta_temp);
                    console.log('bonito.condicion_cantidad' + bonito.condicion_cantidad);
                    if ((lacantidadcomprada != 0 && countarcoinci >= 1 && parseFloat(cantidad_tomar_en_cuenta_temp) < parseFloat(values[0].condicion_cantidad) ) ||
                        (lacantidadcomprada == 0 && countarcoinci >= 1 && (

                            ( cantidad_tomar_en_cuenta_temp<=cantidad_tomar_en_cuenta_AUX || cantidadanterior==cantidad_tomar_en_cuenta_AUX)
                            //parseFloat(cantidad_tomar_en_cuenta_AUX) < parseFloat(cantidad_tomar_en_cuenta_temp) && cantidadanterior<cantidad_tomar_en_cuenta_temp

                        ))) {
                        eliminar = false;
                    }
                    if(cantidad_tomar_en_cuenta_temp>=bonito.condicion_cantidad && cantidad_tomar_en_cuenta_temp< parseFloat(values[0].condicion_cantidad)){
                        eliminar=true;
                    }

                    console.log(eliminar);
                } else {
                    var cantidad_tomar_en_cuenta_temp = cantidad_tomar_en_cuenta;
                    var cantidad_p = 0;
                    console.log(lst_producto);
                    jQuery.each(lst_producto, function (mm, produtyy) {
                        // if (parseInt(produtyy.id_producto) === parseInt(bonito.bono_id)) {
                        jQuery.each(bonito.bonificaciones_has_producto, function (oo, bonificicion_has_ppp) {
                            var esigual = parseInt(bonificicion_has_ppp.id_producto) === parseInt(produtyy.id_producto);
                            console.log(esigual);
                            if (esigual === true && produtyy.bono != 'true') {
                                console.log(bonificicion_has_ppp);
                                console.log(produtyy);
                                countarcoinci++;


                                // if (bonificicion_has_ppp.id_producto != idprpod) {
                                console.log(produtyy.cantidad);
                                cantidad_p = parseFloat(produtyy.cantidad) + cantidad_p;
                                console.log(lacantidadcomprada);
                                cantidad_tomar_en_cuenta_temp = lacantidadcomprada + produtyy.cantidad;
                                // }

                            }
                        });

                    });

                    cantidad_tomar_en_cuenta_temp = cantidad_tomar_en_cuenta_temp - lacantidadcomprada;

                    console.log('cantidad_p' + cantidad_p);
                    console.log('cantidad_tomar_en_cuenta_AUX' + cantidad_tomar_en_cuenta_AUX);
                    console.log('countarcoinci' + countarcoinci);
                    console.log('cantidad_tomar_en_cuenta_temp' + cantidad_tomar_en_cuenta_temp);
                    console.log('cantidad_tomar_en_cuenta' + cantidad_tomar_en_cuenta);
                    console.log('bonito.condicion_cantidad' + bonito.condicion_cantidad);
                    //parseFloat(cantidad_tomar_en_cuenta_temp) >= parseFloat(bonito.condicion_cantidad)
                    if (countarcoinci >= 1 && parseFloat(cantidad_p) >= parseFloat(bonito.condicion_cantidad)) {
                        eliminar = false;
                    }

                    console.log(eliminar);
                }

                jQuery.each(lst_bonos, function (j, produt) {

                        console.log('lacantidadcomprada' + lacantidadcomprada);
                        if (produt != undefined && lacantidadcomprada != 0) {


                            console.log(produt);
                            console.log(bonito); //parseInt(bonito.id_bonificacion) == parseInt(produt.id_bonificacion)&&
                            if (parseInt(bonito.bono_id) == parseInt(produt.bono_id)
                                && parseInt(bonito.bono_unidad_id) == parseInt(produt.bono_unidad_id) && produt.bono != 'false') {
                                console.log('distinto de cero');

                                jQuery.each(bonito.bonificaciones_has_producto, function (y, bonificicion_has_p) {
                                    if (parseInt(bonificicion_has_p.id_producto) == parseInt(idprpod) && eliminar == true) {
                                        lst_bonos.splice(j, 1);
                                        $("#boni-" + bonito.bono_id + "-" + bonito.bono_unidad_id).remove();
                                        console.log('elimino de una 1');
                                        //        console.log(bonito.bono_id);
                                    }
                                });
                            }
                        }

                        if (produt != undefined && lacantidadcomprada == 0) {
                            // console.log(bonito);
                            //console.log(produt);
                            //bonito.id_bonificacion == produt.id_bonificacion &&  SI ESTO VUELVE A FALLATA HACER LA VALIDACION DE SI EXSTE ALGUN OTRO PRODUCTO QUE GENERE LA BONIFICAION NO SE DEBE ELIMINAR
                            if (bonito.bono_id == produt.bono_id && bonito.bono_unidad_id == produt.bono_unidad_id && produt.bono != 'false') {


                                jQuery.each(bonito.bonificaciones_has_producto, function (y, bonificicion_has_p) {
                                    if (parseInt(bonificicion_has_p.id_producto) == parseInt(idprpod) && eliminar == true) {
                                        lst_bonos.splice(j, 1);
                                        $("#boni-" + bonito.bono_id + "-" + bonito.bono_unidad_id).remove();
                                        console.log('elimino de una');
                                        //        console.log(bonito.bono_id);
                                    }
                                });
                            }
                        }
                    }
                );
            }
        });
        cantidad_tomar_en_cuenta = lacantidadcomprada;
        console.log(lacantidadcomprada);

    });


    recorrerBonos();


}

function bonoDevolverStock(value) {
    var stockStatus = value.venta_sin_stock_bono;
    var ventaStatus = $("#venta_status").val();
    var stockhidden = $("#stockhidden" + value.bono_id);
    var cantidad_total = (parseFloat(stockhidden.val()) + value.unidades * value.bono_cantidad);
    stockhidden.val(cantidad_total);
}

function recorrerBonos() {
    console.log(lst_bonos);
    jQuery.each(lst_bonos, function (i, value) {
        $("#boni-" + value.bono_id + "-" + value.bono_unidad_id).remove();

        var stockStatus = value.venta_sin_stock_bono;
        var ventaStatus = $("#venta_status").val();
        var stockhidden = $("#stockhidden" + value.bono_id);
        var cantidad_total = (parseFloat(stockhidden.val()) - value.unidades * value.bono_cantidad);
        if (stockStatus == 0) {
            if (ventaStatus != 'GENERADO' && (cantidad_total > stockhidden.val() || cantidad_total < 0)) {
                alertModal('<h4>Stock Insuficiente BONO!</h4> <p>' + producto_nombre + '</p>', 'warning', true);
                //return false;
            }
            else {
                agregarBono(value.id_bonificacion, value.bono_id, value.bono_producto, value.bono_cantidad, value.bono_unidad_id, value.bono_unidad);
                /**Actualizo stok**/
                stockhidden.val(cantidad_total);
            }
        } else {
            agregarBono(value.id_bonificacion, value.bono_id, value.bono_producto, value.bono_cantidad, value.bono_unidad_id, value.bono_unidad);
            /**Actualizo stok**/
            stockhidden.val(cantidad_total);
        }
    })
}


function calculatotales(producto_id, producto_nombre, unidad_nombre, cantidad, precio, subtotal, porcentaje_impuesto, count, unidades, cualidad, unidad_id, precio_sugerido, bono) {
    var cont = parseInt(count) + 1;


    //console.log(decodeURIComponent(producto_nombre));
    if (bono === 'false') {
        var tr = "<tr id='producto" + count + "'>" +
            "<td>" + cont;
    } else {
        var tr = "<tr id='boni-" + producto_id + "-" + unidad_id + "' class='bono-i'>" +
            "<td>" + cont;
    }
    //   console.log(producto_nombre);
    if (bono != "false") {
        tr += ' BONO';
    }
    tr += "</td>" +
        "<td>" + pad_with_zeroes(producto_id, 4) + "</td>" +
        "<td>" + decodeURIComponent(producto_nombre) + " </td>" +
        "<td>" + unidad_nombre + "</td>" +
        "<td>" + cantidad + "</td>" +
        "<td>" + precio + "</td>";

    if (precio_sugerido > 0) {
        tr += "<td>" + precio_sugerido + "</td>";
    } else
        tr += "<td> -- </td>";
    tr +=
        "<td>" + parseFloat(Math.ceil(subtotal * 10) / 10) + "</td>" +
        "<td>";
    if (precio_sugerido > 0 && $("#edit_pedido").val() == 1) {
        tr += "<a href='#' data-toggle='tooltip' tittle='Aceptar Precio Sugerido' data-original-title='Aceptar Precio Sugerido' onclick='aplicarPrecioSugerido(" + count + ", " + cantidad + ", " + porcentaje_impuesto + ",  \"" + cualidad + "\", " + precio_sugerido + ", " + producto_id + " )' class='btn btn-default'><i class='fa fa-check-circle'></i> </a>";
    }
    tr += "</td><td>";
    if (bono === 'false') {
        tr += "<a href='#' onclick='confirmar_delete(" + count + ", " + porcentaje_impuesto + ", \"" + cualidad + "\")' class='btn btn-sm btn-danger'><i class='fa fa-trash-o'></i> </a>";
    }
    else {
        tr += "--";
    }
    tr += "</td>";
    if (bono === 'false') {
        //tr += "<a href='#' data-toggle='tooltip' data-original-title='Editar'  onclick='editCantidad(" + count + ", " + cantidad + ", " + porcentaje_impuesto + ", " + unidades + ", \"" + cualidad + "\", " + unidad_id + ", " + producto_id + ", " + precio + " )' class='btn btn-default'><i class='fa fa-edit'></i> </a></td>";
    }
    else {
        tr += "--";
    }
    tr += "</tr>";
    $("#tbodyproductos").append(tr);
    $("#preciostbody").html('');
    $("#cantidad").val('');
    var impuesto = parseFloat(Math.ceil((subtotal * (porcentaje_impuesto / 100)) * 10) / 10);
    var nuevoimpuesto = parseFloat(impuesto) + parseFloat($("#montoigv").val());
    var nuevototal = (parseFloat(subtotal) + parseFloat($("#totApagar").val())).toFixed(2);
    var nuevosubtotal = parseFloat(parseFloat(subtotal) - parseFloat(impuesto)) + parseFloat($("#subTotal").val());
    document.getElementById('totApagar').value = parseFloat(Math.ceil(nuevototal * 10) / 10);
    $('#totApagar2').html(parseFloat(Math.ceil(nuevototal * 10) / 10));
    document.getElementById('montoigv').value = parseFloat(Math.ceil(nuevoimpuesto * 10) / 10);
    document.getElementById('subTotal').value = parseFloat(Math.ceil(nuevosubtotal * 10) / 10);
}

function addProductoToArray(producto_id, producto_nombre, unidad_id, unidad_nombre, cantidad, precio, precio_sugerido, subtotal, unidades, producto_cualidad, porcentaje_impuesto, isbono, venta_sin_stock) {
    console.log(cantidad);
    if (isbono == 'false') {
        var producto = {};
        producto.id_producto = producto_id;
        producto.nombre = producto_nombre;
        producto.precio = precio;
        producto.precio_sugerido = precio_sugerido;
        producto.cantidad = cantidad;
        producto.unidad_medida = unidad_id;
        producto.unidad_nombre = unidad_nombre;
        producto.detalle_importe = subtotal;
        producto.unidades = unidades;
        producto.producto_cualidad = producto_cualidad;
        producto.porcentaje_impuesto = porcentaje_impuesto;
        producto.bono = "false";
        producto.count = countproducto;
        producto.venta_sin_stock = venta_sin_stock;
        lst_producto.push(producto);
    } else {
        var bono = {};
        bono.bono_id = producto_id;
        bono.bono_producto = producto_nombre;
        bono.bono_cantidad = cantidad;
        bono.bono_unidad_id = unidad_id;
        bono.bono_unidad = unidad_nombre;
        bono.bono = "true";
        bono.id_familia = null;
        bono.id_grupo = null;
        bono.id_marca = null;
        bono.id_linea = null;
        bono.fecha = null
        bono.unidades = unidades;
        bono.venta_sin_stock_bono = venta_sin_stock;
        bono.porcentaje_impuesto = porcentaje_impuesto;
        bono.bonochecked = false;
        lst_bonos.push(bono)
    }
    // console.log(lst_bonos);
    countproducto++;
    $("#totalproductos").text(countproducto);
}

function clearFields() {
    $("#selectproductos").val('').trigger("chosen:updated");
    $("#precios").val(1).trigger("chosen:updated");
    bonos = new Array();
}

function clearAllfields() {
    $("#selectproductos").trigger('chosen:open');
    setTimeout(function () {
        $("#selectproductos_chosen .chosen-drop .chosen-search input").focus();
    }, 5);
}

function editCantidad(count, cantidad, porcentaje_impuesto, unidades, cualidad, unidad_id, producto_id, precio) {

    $.ajax({
        type: 'POST',
        data: {'producto': producto_id},
        dataType: "json",
        url: ruta + 'inventario/get_existencia_producto',
        success: function (data) {
            if ($("#stockhidden" + producto_id).length > 0) {
                var val = parseFloat($("#stockhidden" + producto_id).val());
                var maxima = parseInt(val / parseInt(data.maxima_unidades));
                var fraccion = parseFloat(val % parseInt(data.maxima_unidades));
                $("#stock").text(maxima + " " + data.unidad_maxima + " " + fraccion + " " + data.unidad_minima);
                var nueva_cantidad = (parseFloat(data.existencia_unidad) * parseFloat(data.maxima_unidades)) + parseFloat(data.existencia_fraccion);
                //console.log(nueva_cantidad);
                $("#stockhidden" + producto_id).attr('value', nueva_cantidad);
                $("#stock_status_" + producto_id).attr('value', data.stock_status);
            } else {
                $("#inentariocontainer").append('<input id="stockhidden' + producto_id + '" type="hiden" value="0"/><input id="stock_status_' + producto_id + '" type="hiden" value="' + data.stock_status + '"/>');
                $("#stock").text(data.existencia_unidad + " " + data.unidad_maxima + " " + data.existencia_fraccion + " " + data.unidad_minima);

                var nueva_cantidad = (parseFloat(data.existencia_unidad) * parseFloat(data.maxima_unidades)) + parseFloat(data.existencia_fraccion);

                $("#stockhidden" + producto_id).attr('value', nueva_cantidad);
            }
        }
    });

    $("#modalbodycantidad").append(putBonoshow());
    getBonificacion(producto_id); // Bonificacion verificar
    getEscalas(producto_id);      // Escalas Verificar
    setTimeout(function () {

        //  bonosCheck(cantidad, true);
        //console.log(bonos);
    }, 500);
    $("#cantidadedit").val(cantidad);
    precio = precio != undefined ? precio : 0;
    $("#precioedit").val(precio);
    $("#modificarcantidad").modal('show');
    $("#guardarcantidad").attr('onclick', 'saveCantidadEdit(' + count + ', ' + cantidad + ',' + porcentaje_impuesto + ', ' + unidades + ', "' + cualidad + '", ' + unidad_id + ', ' + producto_id + ')')


}

function putBonoshow() {
    return '<div class="row" id="bono_show" style="display:none;">' +
        '<div class="form-group">' +
        '<div class="col-md-12">' +
        '<br>' +
        '<h4>Posibles Bonificaciones para: <b class="bono_descripcion" id=""></b></h4>' +
        '</div> <div class="table-responsive">' +
        '<table class="table datatable table-bordered">' +
        '<thead>' +
        '<th>Por cada</th>' +
        '<th>Producto bono</th>' +
        '<th>Unidad bono</th>' +
        '<th>Cantidad bono</th>' +

        '</thead>' +
        '<tbody class="" id="bono_producto"></tbody>' +
        '</table>' +
        '</div>' +
        '</div>' +
        '</div>';
}

function saveCantidadEdit(count, cantidad, porcentaje_impuesto, unidades, cualidad, unidad_id, producto_id) {


    var cantidad_vieja = cantidad;
    // console.log(bonos);
    var newcantidad = parseFloat($("#cantidadedit").val());
    var precio_edit = parseFloat($("#precioedit").val());

    var producto_cualidad = cualidad;

    var stockStatus = $("#stock_status_" + producto_id).val();
    var stockhidden = $("#stockhidden" + producto_id);


    var ventaStatus = $("#venta_status").val();
    var cantidad_total = (parseFloat(stockhidden.val()) - parseFloat(unidades) * newcantidad);

    // console.log(cantidad_vieja);
    //console.log(unidades * newcantidad);
    if (stockStatus == 0) {
        if (ventaStatus != 'GENERADO' && ((cantidad_total > parseFloat(stockhidden.val()) && cantidad_total >= 0 )
            || ( cantidad_total < 0 && ( parseFloat(unidades * newcantidad) > cantidad_vieja)))) {
            alertModal('<h4>Stock Insuficiente!</h4> ', 'warning', false);
            return false;
        }
    }

    if (producto_cualidad == "MEDIBLE") {
        cantidadminima = 1;
    } else {
        cantidadminima = 0.1;
    }

    if (newcantidad == '' || isNaN(newcantidad) || newcantidad < cantidadminima) {
        alertModal('<h4>Datos incompletos:</h4> <p>Ingrese una cantidad mayor a ' + cantidadminima + ' y menor al stock</p>', 'warning', false);
        return false;
    }

    var lista_vieja = lst_producto;

    $("#modificarcantidad").modal('hide');
    $("#subTotal").val(formatPrice(0));
    $("#montoigv").val(formatPrice(0));
    $("#totApagar").val(formatPrice(0));
    $("#totApagar2").html(formatPrice(0))

    countproducto = 0;

    $("#tbodyproductos").html('');

    lst_producto = new Array();


    getEscalas(producto_id);

    var cantidad = newcantidad;

    var cumplescala = false;
    if (escalaData.length > 0 && cantidad > 0) {
        for (var i = 0; i < escalaData.length; i++) {
            var data = escalaData[i];

            var unidad_id = data.unidad;

            if (cantidad >= parseFloat(data.cantidad_minima) && cantidad <= parseFloat(data.cantidad_maxima)) {

                //var newprecio = data.precio;
                var newprecio = precio_edit;
                cumplescala = true;
            }

        }


    }

    console.log(lista_vieja);

    jQuery.each(lista_vieja, function (i, value) {


        if (value.bono != 'false') {
            // console.log('es istito de false');
            //  lista_vieja.splice(i, 1);


        }
        else {
            // console.log('es igual de false');
            if (value["count"] == count) {

                newcantidad = parseFloat($("#cantidadedit").val());

                //if (cumplescala == true) {
                //   value.precio = newprecio;
                //}
                value.precio = parseFloat(precio_edit);
                var subtotal = parseFloat(value.precio) * newcantidad;

                console.log('newcantidad' + newcantidad);
                console.log('subtotal' + subtotal);
                console.log('parseFloat(value.precio)' + parseFloat(value.precio));
                calculatotales(value.id_producto, value.nombre, value.unidad_nombre, newcantidad, value.precio, subtotal, porcentaje_impuesto, countproducto, value.unidades, cualidad, value.unidad_medida, value.precio_sugerido, value.bono);
                addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, newcantidad, value.precio, value.precio_sugerido, subtotal, value.unidades, cualidad, porcentaje_impuesto, value.bono, value.venta_sin_stock);

            } else {
                newcantidad = value.cantidad;
                calculatotales(value.id_producto, value.nombre, value.unidad_nombre, value.cantidad, value.precio, value.detalle_importe, porcentaje_impuesto, countproducto, value.unidades, cualidad, value.unidad_medida, value.precio_sugerido, value.bono);
                addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, value.cantidad, value.precio, value.precio_sugerido, value.detalle_importe, value.unidades, cualidad, porcentaje_impuesto, value.bono, value.venta_sin_stock);
            }


            var stockhidden = $("#stockhidden" + value.id_producto);

            if (stockhidden.length > 0) {

                var cantidad_total = ((parseFloat(value.unidades) * parseFloat(value.cantidad)) + parseFloat(stockhidden.val()) ) - (parseFloat(value.unidades) * parseFloat(newcantidad));
                stockhidden.val(0);
                // console.log(cantidad_total);
                stockhidden.val(cantidad_total);
            }
        }

    });


    //console.log(lst_bonos);
    //console.log(bonos);


    addBonosToTable(parseFloat($("#cantidadedit").val()), parseInt(unidad_id), producto_id);
}


function aplicarPrecioSugerido(count, cantidad, porcentaje_impuesto, cualidad, preciosugerido, producto_id) {


    var lista_vieja = lst_producto;

    $("#modificarcantidad").modal('hide');
    $("#subTotal").val(formatPrice(0));
    $("#montoigv").val(formatPrice(0));
    $("#totApagar").val(formatPrice(0));
    $("#totApagar2").html(formatPrice(0));

    countproducto = 0;

    $("#tbodyproductos").html('');

    lst_producto = new Array();


    jQuery.each(lista_vieja, function (i, value) {



        //console.log(value["count"]);
        //console.log(count);
        if (value["count"] == count) {


            var subtotal = preciosugerido * cantidad;

            calculatotales(value.id_producto, value.nombre, value.unidad_nombre, cantidad, preciosugerido, subtotal, porcentaje_impuesto, countproducto, value.unidades, cualidad, value.unidad_medida, 0.00, value.bono);
            addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, value.cantidad, preciosugerido, 0.00, subtotal, value.unidades, cualidad, porcentaje_impuesto, value.bono, value.venta_sin_stock);

        } else {

            calculatotales(value.id_producto, value.nombre, value.unidad_nombre, value.cantidad, value.precio, value.detalle_importe, porcentaje_impuesto, countproducto, value.unidades, cualidad, value.unidad_medida, value.precio_sugerido, value.bono);
            addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, value.cantidad, value.precio, value.precio_sugerido, value.detalle_importe, value.unidades, cualidad, porcentaje_impuesto, value.bono, value.venta_sin_stock);
        }


    });
    //return false;

    recorrerBonos();
}

function confirmar_delete(count, porcentaje_impuesto, cualidad) {
    $('#eliminar_item').attr('onclick', "deleteproducto(" + count + ", " + porcentaje_impuesto + ", \"" + cualidad + "\")");
    $('#confirmar_delete').modal('show');
}

function deleteproducto(count, porcentaje_impuesto, cualidad) {
    //console.log('borrando el producto ' + count);
    var lista_vieja = lst_producto;

    var productoeliminar;
    $("#subTotal").val(formatPrice(0));
    $("#montoigv").val(formatPrice(0));
    $("#totApagar").val(formatPrice(0));
    $("#totApagar2").html(formatPrice(0));
    countproducto = 0;
    $("#tbodyproductos").html('');
    lst_producto = new Array();
    lstaeliminar = new Array();
    jQuery.each(lista_vieja, function (i, value) {
        if (value["count"] === count) {
            eliminar = i;
            productoeliminar = value;
            lstaeliminar.push(productoeliminar);
        } else {
            calculatotales(value.id_producto, value.nombre, value.unidad_nombre, value.cantidad, value.precio, value.detalle_importe, porcentaje_impuesto, countproducto, value.unidades, cualidad, value.unidad_medida, value.precio_sugerido, value.bono);
            addProductoToArray(value.id_producto, value.nombre, value.unidad_medida, value.unidad_nombre, value.cantidad, value.precio, value.precio_sugerido, value.detalle_importe, value.unidades, cualidad, porcentaje_impuesto, value.bono, value.venta_sin_stock);
        }
    });
    jQuery.each(lstaeliminar, function (i, value) {
        lista_vieja.splice(i, 1);
    });
    var stockhidden = $("#stockhidden" + productoeliminar.id_producto);
    var cantidad_total = (parseFloat(stockhidden.val()) + parseFloat(productoeliminar.unidades) * parseFloat(productoeliminar.cantidad));
    stockhidden.val(cantidad_total);
    $("#totalproductos").text(countproducto);
    // Elimimino los bonos

    getBonificacion(productoeliminar.id_producto, productoeliminar);

    /*setTimeout(function () {
     deleteBono(productoeliminar.unidad_medida, productoeliminar.cantidad, productoeliminar.id_producto)
     }, 1000);*/

    $('#eliminar_item').attr('onclick', "");
    $('#confirmar_delete').modal('hide');
}

/**
 * Levanta el modal para seleccionar los precios del producto
 */
function buscarProducto() {
    var id = $("#selectproductos").val();

    if (id == '') {
        return false;
    }

    if ($("#id_cliente").val() == '') {
        show_msg('warning', '<h4>Error.</h4> <p>Debe seleccionar un cliente</p>');
        $("#selectproductos").val('').trigger("chosen:updated");
        return false;
    }

    $("#check_precio").prop('checked', false);
    $("#check_precio").trigger('change');

    $.ajax({
        type: 'POST',
        data: {'producto': id},
        dataType: "json",
        url: ruta + 'inventario/get_existencia_producto',
        success: function (data) {
            if (data.precios_normal.length > 0) {

                var precios_normal = data.precios_normal;
                var opciones = '';

                var i = 0;


                // $("#precios").val('');
                //   alert(precios_normal.length);
                $("#precios").html('<option value="">Seleccione<option>');
                for (i = 0; i < precios_normal.length; i++) {

                    if (precios_normal[i]['nombre_precio'] == "Precio Venta") {
                        opciones += '<option value="'
                            + precios_normal[i]['id_precio']
                            + '" selected>'
                            + precios_normal[i]['nombre_precio']
                            + '</option>';
                    } else {

                        opciones += '<option value="'
                            + precios_normal[i]['id_precio']
                            + '" >'
                            + precios_normal[i]['nombre_precio']
                            + '</option>';
                    }


                }
                $("#precios").append(opciones);
                $("#precios").trigger("chosen:updated");
                $("#precios").trigger("chosen:activate");


            }

            if ($("#stockhidden" + id).length > 0) {
                var val = (parseFloat(data.existencia_unidad) * parseFloat(data.maxima_unidades)) + parseFloat(data.existencia_fraccion);
                var maxima = parseInt(val / parseInt(data.maxima_unidades));
                var fraccion = parseFloat(val % parseInt(data.maxima_unidades));
                $("#stock").text(maxima + " " + data.unidad_maxima + " " + fraccion + " " + data.unidad_minima);
                var nueva_cantidad = (parseFloat(data.existencia_unidad) * parseFloat(data.maxima_unidades)) + parseFloat(data.existencia_fraccion);
                //console.log(nueva_cantidad);
                $("#stockhidden" + id).attr('value', nueva_cantidad);
                $("#stock_status_" + id).attr('value', data.stock_status);
            } else {
                $("#inentariocontainer").append('<input id="stockhidden' + id + '" type="hiden" value="0"/><input id="stock_status_' + id + '" type="hiden" value="' + data.stock_status + '"/>');
                $("#stock").text(data.existencia_unidad + " " + data.unidad_maxima + " " + data.existencia_fraccion + " " + data.unidad_minima);

                var nueva_cantidad = (parseFloat(data.existencia_unidad) * parseFloat(data.maxima_unidades)) + parseFloat(data.existencia_fraccion);

                $("#stockhidden" + id).attr('value', nueva_cantidad);
            }

            $("#nombreproduto").text(data.nombre);
            $("#producto_cualidad").attr('value', data.cualidad);

            if (data.producto_cualidad == "MEDIBLE") {
                $("#cantidad").attr('min', '1');
                $("#cantidad").attr('step', '1');
                $("#cantidad").attr('value', '1');
            } else {
                $("#cantidad").attr('min', '0.1');
                $("#cantidad").attr('step', '0.1');
                $("#cantidad").attr('value', '0.0');
            }

            cambiarnombreprecio();

            $("#modalbodyproducto").append(putBonoshow());

            getBonificacion(id); // Bonificacion verificar
            getEscalas(id);      // Escalas Verificar

            $("#seleccionunidades").modal('show');

            setTimeout(function () {
                $("#precios_chosen .chosen-search input").blur();
                selectSelectableElement(jQuery("#preciostbody"), jQuery("#preciostbody").children(":eq(0)"));

            }, 1000);
        }
    });
}

// Escalas
function getEscalas(id) {
    if (id) {
        $.ajax({
            type: 'GET',
            data: {'id': id, 'grupo': grupo_id},
            dataType: 'JSON',
            url: ruta + 'api/escalas/ver_genventa',
            success: function (data) {
                escalaData = data.escalas;
            },
            error: function (xhr, textStatus, error) {
                console.log('[Escala Error] ' + textStatus);
            }
        });
    } else {
        console.log('[Escala Error ID]');
    }
}

// Producto
function getProducto(id) {
    if (id) {
        $.ajax({
            type: 'GET',
            data: {'id': id},
            dataType: 'JSON',
            url: ruta + 'api/productos/id',
            success: function (data) {
                //  console.log(data.productos);
            },
            error: function (xhr, textStatus, error) {
                console.log('[Producto Error] ' + textStatus);
            }
        });
    } else {
        console.log('[Producto Error ID]');
    }
}

// Unidad Precio
function getUnidadPrecio(producto_id, unidad_id, grupo_id) {
    if (producto_id && unidad_id && grupo_id) {
        $.ajax({
            type: 'POST',
            data: {'unidad_id': unidad_id, 'producto_id': producto_id, 'grupo_id': grupo_id},
            dataType: 'JSON',
            url: ruta + 'venta/get_precio_escalas',
            success: function (data) {
                min_precio = data.min;
                max_precio = data.max;
                $('#min_precio').html(min_precio);
                $('#max_precio').html(max_precio);
            },
            error: function (xhr, textStatus, error) {

            }
        });
    } else {
        console.log('[UnidadPrecio Error ID]');
    }
}

function activarText_ModoPago() {
    var modopago = $("#cboModPag").val();
    var dias = $("#diascondicionpago" + modopago).val();
    var venta_tipo = $("#venta_tipo").val();
    var devolver = $("#devolver").val();
    $("#diascondicionpagoinput").val(dias);

    /*if ((dias < 1 && venta_tipo == 'CAJA') || (venta_tipo == 'ENTREGA' && devolver == 'true' )) {
     $("#importediv").show();
     $("#vueltodiv").show();
     } else if (dias > 1 ) {
     $("#importediv").hide();
     $("#vueltodiv").hide();
     }*/
}

function calcular_importe() {
    var totalApagar = $('#totApagar').val();
    var importe = $('#importe').val();
    // document.getElementById('vuelto').value = parseFloat(Math.ceil((parseFloat(importe - totalApagar).toFixed(2)) * 10) / 10);
}

function cargaData_Impresion(id_venta) {
    $.ajax({
        url: ruta + 'venta/verVenta',
        type: 'POST',
        data: "idventa=" + id_venta,
        success: function (data) {
            $("#mvisualizarVenta").html(data);
            $("#mvisualizarVenta").modal('show');
        }
    });
}

function buscarventasabiertas() {
    $.ajax({
        url: ruta + 'venta/get_ventas_por_status',
        type: 'POST',
        data: {'estatus': 'EN ESPERA'},
        success: function (data) {
            $("#ventasabiertas").html(data);
        }
    });
    $("#ventasabiertas").modal('show');
}

function resetear() {

    lst_producto = new Array()
    lst_bonos = new Array()
}

<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Proveedor</li>
    <li><a href="">Cuentas por Pagar</a></li>
</ul>

<div class="block">
    <form id="frmBuscar">

        <div class="row">
            <div class="col-md-2">
                <label class="control-label panel-admin-text">Rango de Fecha</label>
            </div>
            <div class="col-md-2">

                <input type="text" name="fecIni" id="fecIni" readonly  class='form-control input-datepicker'>
            </div>
            <div class="col-md-2">
                <input type="text" name="fecFin" id="fecFin" readonly  class='form-control input-datepicker'>
            </div>
            <div class="col-md-2">

                <select name="proveedor" id="proveedor" class='cho form-control'>
                    <option value="-1">Seleccionar</option>
                    <?php if (count($lstproveedor) > 0): ?>
                        <?php foreach ($lstproveedor as $cl): ?>
                            <option
                                value="<?php echo $cl['id_proveedor']; ?>"><?php echo $cl['proveedor_nombre']; ?></option>
                        <?php endforeach; ?>
                    <?php else : ?>
                    <?php endif; ?>
                </select>
            </div>
            <button id="btnBuscar" class="btn btn-default">Buscar</button>
        </div>
    </form>

    <br>


    <div class="block-section">
        <div id="lstTabla" class="table-responsive">

        </div>
    </div>

    <a class='tip btn btn-default'
               title="Exportar a Excel" id="excel"><i class="fa fa-file-excel-o"></i></a>

    <a class='btn btn-danger'
               title="Exportar a PDF" id="pdf"><i class="fa fa-file-pdf-o"></i> </a>
</div>


<div class="modal fade" id="visualizarPago" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<script>


    $(document).ready(function () {

        $('select').chosen();
        $(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});

        $("#btnBuscar").click(function (e) {
            e.preventDefault()
            buscar();
        });
        buscar();
    });

    function cerrar_detalle_historial() {

        $('#visualizarPago').modal('hide');


    }
    function ver_detalle_pago(id_historial, ingreso_id) {


        $.ajax({
            type: 'POST',
            data: {'id_historial': id_historial, 'ingreso_id': ingreso_id},
            url: '<?php echo base_url();?>' + 'ingresos/imprimir_pago_pendiente',
            success: function (data) {
                $("#visualizarPago").html(data);
                $('#visualizarPago').modal('show');

            }
        });


    }

    function buscar() {

        var fechaini = $('#fecIni').val();
        var fechafin = $('#fecFin').val();

        var urlpdf = '<?php echo base_url();?>' + 'ingresos/cuentasporpagarpdf/';
        if (fechaini != "") {
            urlpdf = urlpdf + fechaini + "/";
        } else {
            urlpdf = urlpdf + "0/";
        }
        if (fechafin != "") {
            urlpdf = urlpdf + fechafin + "/";
        } else {
            urlpdf = urlpdf + "0/";
        }
        urlpdf = urlpdf + $("#proveedor").val();

        var urlexcel = '<?php echo base_url();?>' + 'ingresos/cuentasporpagarexcel/';
        if (fechaini != "") {
            urlexcel = urlexcel + fechaini + "/";
        } else {
            urlexcel = urlexcel + "0/";
        }
        if (fechafin != "") {
            urlexcel = urlexcel + fechafin + "/";
        } else {
            urlexcel = urlexcel + "0/";
        }
        urlexcel = urlexcel + $("#proveedor").val();
        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'ingresos/lst_cuentas_porpagar',
            success: function (data) {

                setTimeout(function () {
                    $("#pdf").attr('href', urlpdf);
                    $("#excel").attr('href', urlexcel);
                }, 1);


                $("#lstTabla").html(data);
                $("#abrir_exportar").show();

            }
        });
    }

    function buscartodos() {

        $.ajax({
            type: 'POST',
            data: {'proveedor': -1, 'fecIni': "", 'fecFin': ""},
            url: '<?php echo base_url();?>' + 'ingresos/lst_cuentas_porpagar',
            success: function (data) {
                setTimeout(function () {
                    $("#pdf").attr('href', '<?php echo base_url();?>' + 'ingresos/cuentasporpagarpdf/0/0/-1');
                    $("#excel").attr('href', '<?php echo base_url();?>' + 'ingresos/cuentasporpagarexcel/0/0/-1');
                }, 1);

                $("#lstTabla").html(data);
                $("#abrir_exportar").show();

            }
        });
    }

    function guardarPago(total_ingreso, suma, id_ingreso) {


        var cantidad_pagar = parseFloat($("#cantidad_a_pagar").val());

        if (cantidad_pagar == "" || isNaN(cantidad_pagar)) {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe ingresar una cantidad</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;

        }

        if (cantidad_pagar > (total_ingreso - suma)) {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Ha ingresado una cantidad mayor a la cantidad a pendiente</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;

        }

        if (cantidad_pagar <= 0) {
            var growlType = 'danger';
            $.bootstrapGrowl('<h4>Debe ingresar un monto mayor a 0</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });
            return false;

        }


        producto.total_ingreso = total_ingreso;
        producto.suma = suma;
        producto.id_ingreso = id_ingreso;
        producto.cantidad_ingresada = cantidad_pagar;

        lst_producto.push(producto);
        var miJSON = JSON.stringify(lst_producto);

        $("#guardarPago").addClass('disabled');
        $.ajax({
            type: 'POST',
            data: $('#form').serialize() + '&lst_producto=' + miJSON,
            dataType: 'json',
            url: '<?= base_url()?>ingresos/guardarPago',
            success: function (data) {
                if (data.success && data.error == undefined) {

                    $.ajax({
                        type: 'POST',
                        data: {'ingreso_id': data.ingreso_id, 'id_historial': data.id_historial},
                        url: '<?= base_url()?>ingresos/imprimir_pago_pendiente',
                        success: function (data2) {

                            $("#visualizarPago").html(data2);
                            $('#visualizarPago').modal('show');
                            $('#pagar_venta').modal('hide');
                        }
                    });

                }
                else {
                    var growlType = 'danger';
                    $("#guardarPago").removeClass('disabled');
                    $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p>Intente nuevamente</p>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                    $('#pago_modal').modal('hide');
                    return false;

                }


            },

            error: function () {
                $("#guardarPago").removeClass('disabled');
                var growlType = 'danger';

                $.bootstrapGrowl('<h4>Ha ocurrido un error </h4> <p>Intente nuevamente</p>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });
                $('#pago_modal').modal('hide');
                return false;

            }
        })
    }
</script>
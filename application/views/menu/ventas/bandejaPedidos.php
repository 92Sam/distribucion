<?php $ruta = base_url();

?>


<ul class="breadcrumb breadcrumb-top">
    <li>Flujo de Trabajo</li>
    <li><a href="">Bandeja de pedidos</a></li>
</ul>
<div class="block">


    <div class="form-group row">
        <div class="col-md-2">
            Desde
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_desde" id="fecha_desde" value="<?= date('d-m-Y') ?>" required="true"
                   class="form-control fecha campos input-datepicker" readonly="readonly">
        </div>
        <div class="col-md-2">
            Hasta
        </div>
        <div class="col-md-4">
            <input type="text" name="fecha_hasta" id="fecha_hasta" value="<?= date('d-m-Y') ?>" required="true"
                   class="form-control fecha campos input-datepicker" readonly="readonly">
        </div>

    </div>
    <div class="form-group row">
        <div class="col-md-2">
            Estado
        </div>
        <div class="col-md-4">
            <select id="estatus" class="campos" name="estatus">
                <option value=""> SELECCIONE</option>
                <option selected value="<?php echo PEDIDO_GENERADO ?>"> <?php echo PEDIDO_GENERADO ?></option>
                <option value="<?php echo PEDIDO_ANULADO ?>"> <?php echo PEDIDO_ANULADO ?></option>
                <option value="<?php echo PEDIDO_ENVIADO ?>"> <?php echo PEDIDO_ENVIADO ?></option>
                <option value="<?php echo PEDIDO_ENTREGADO ?>"> <?php echo PEDIDO_ENTREGADO ?></option>
                <option value="<?php echo PEDIDO_RECHAZADO ?>"> <?php echo PEDIDO_RECHAZADO ?></option>
                <option value="<?php echo PEDIDO_DEVUELTO ?>"> <?php echo PEDIDO_DEVUELTO ?></option>
            </select>

        </div>
        <!--</div>
         <div class="form-group row">-->
        <div class="col-md-2">
            Vendedor
        </div>
        <div class="col-md-4">
            <select id="vendedor" class=" campos" name="vendedor">
                <option value=""> SELECCIONE</option>
                <?php if (isset($vendedores)) {
                    foreach ($vendedores as $vendedor) {
                        ?>
                        <option value="<?= $vendedor->nUsuCodigo; ?>"> <?= $vendedor->nombre ?> </option>

                    <?php }
                } ?>
            </select>

        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-2">
            Cliente
        </div>
        <div class="col-md-4">
            <select id="client" class="campos" name="client">
                <option value=""> SELECCIONE</option>
                <?php if (isset($clientes)) {
                    foreach ($clientes as $client) {
                        ?>
                        <option value="<?= $client['id_cliente']; ?>"> <?= $client['razon_social'] ?> </option>

                    <?php }
                } ?>
            </select>

        </div>


        <!--</div>
         <div class="form-group row">-->
        <div class="col-md-2">
            Zona
        </div>
        <div class="col-md-4">
            <select id="zona" class=" campos" name="zona">
                <option value=""> SELECCIONE</option>
                <?php if (isset($zonas)) {
                    foreach ($zonas as $zona) {
                        ?>
                        <option value="<?= $zona['zona_id']; ?>"> <?= $zona['zona_nombre'] ?> </option>

                    <?php }
                } ?>
            </select>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-2">
            Metros cúbicos
        </div>
        <div class="col-md-3">
            <input type="text" name="suma_metros_cubicos" id="suma_metros_cubicos" value="0" readonly="readonly"
                   class=" form-control">
        </div>


        <div class="col-md-3">
            <button type="button" id="añadircamion" onclick="agregarPedidos();" class="btn btn-info"><i
                    class="fa fa-truck"></i>
                Asignar a camión
            </button>
        </div>
        <div class="col-md-4">
            <button type="button" id="stoprefresh" style="display:none;" onclick="stoprefresh();"
                    class="btn btn-default"><i
                    class="fa fa-stop"></i>
                Detener Auto Refresh
            </button>
            <button type="button" id="inicrefresh" onclick="refreshpedidos();" class="btn btn-default"><i
                    class="fa fa-refresh fa-spin"></i>
                Auto Refresh
            </button>
        </div>


    </div>


    <div class="row">

        <div class="col-md-2">
            <label>Leyenda:</label>
        </div>
        <div class="col-md-2">

            <label class="label label-danger">Cliente Frecuente</label>

            <label class="">Cliente Con Deuda</label>
        </div>
        <div class="col-md-2">
            <a class="btn btn-warning"><i class="fa fa-edit"></i> </a>
            <label class="">Precio Sugerido</label>
        </div>


    </div>
</div>
    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-success alert-dismissable" id="success"
                 style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
                <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
                <span id="successspan"><?php echo isset($success) ? $success : '' ?>

                    </span>
                </div>

        </div>
      </div>

<div class="modal fade" id="seleccionarPedido" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Atención:</h4>
            </div>
            <div class="modal-body">
                <p>Debe elegir algún pedido.</p>
            </div>
            <div class="modal-footer">

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>

<div class="modal fade" id="visualizarCamiones" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
</div>

<input type="hidden" name="listar" id="listar" value="pedidos">

<div class="block" id="tabla">


</div>

<div class="modal fade" id="confirmarCarga" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Desea confirmar</h4>
            </div>

            <div class="modal-body">
                <p>El pedido excede la cantidad de metros cúbicos que soporta el camión.</p>

                <p>Presione "Confirmar" para guardar el pedido de todos modos o "Cancelar" para elegir otro camión.</p>
            </div>
            <br><br><br>

            <div class="modal-footer">
                <button type="button" id="btnguardarconsolidado" class="btn btn-primary" onclick="grupo.guardarconsolidado()">
                   <li class="glyphicon glyphicon-thumbs-up"></li> Guardar
                </button>
                <button type="button" class="btn btn-warning" data-dismiss="modal"> <li class="glyphicon glyphicon-thumbs-down"></li> Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
</div>
<script src="<?php echo $ruta; ?>recursos/editable/jquery.jeditable.js"></script>


<script type="text/javascript">
    var refresh = true;
    $(function() {

        $(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});
        getVentas();
        $(".campos").on("change", function () {
            getVentas();
        });

        refreshpedidos();
        $("select").chosen({width: '100%'});
        $('.w_id').attr('style', 'text-align: center; width:6%')

    });

    function renewSession() {
        $.ajax({
            url: "<?php echo base_url() ?>/inicio/renew_sesion",
            type: "POST"
        });
    }

    function getVentas() {

        if ($('#mvisualizarVenta').hasClass('in') || $("#ventamodal").hasClass('in')) {

        } else {
           // renewSession();

            $("#suma_metros_cubicos").val('0');
            var fercha_desde = $("#fecha_desde").val();
            var fercha_hasta = $("#fecha_hasta").val();
            var locales = $("#locales").val();
            var estatus = $("#estatus").val();
            var listar = $("#listar").val();
            var vendedor = $("#vendedor").val();
            var client = $("#client").val();
            var zona = $("#zona").val();

            // $("#hidden_consul").remove();


            $.ajax({
                url: '<?= base_url()?>venta/get_ventas',
                data: {
                    'id_local': locales,
                    'desde': fercha_desde,
                    'hasta': fercha_hasta,
                    'estatus': estatus,
                    'listar': listar,
                    'client': client,
                    'vendedor': vendedor,
                    'zona': zona

                },
                type: 'POST',
                success: function (data) {
                    // $("#query_consul").html(data.consulta);
                    if (data.length > 0) {
                        $("#tabla").html(data);
                    }

                    TablesDatatables.init(0, 'tablaresultado');
                },
                error: function () {

                    alert('Ocurrio un error por favor intente nuevamente');
                }
            })
        }

    }


    function refreshpedidos() {


        if ($("#inicrefresh").length != 0) {


            if ($('#mvisualizarVenta').hasClass('in') || $("#ventamodal").hasClass('in')) {

                stoprefresh();
            } else {


                //getVentas();


                refresh = setInterval(function() {
                        $.ajax({	//create an ajax request to load_page.php
                            type: "POST",
                            url: '<?php echo base_url(); ?>inicio/very_sesion',
                            dataType: "json",
                            success: function (data) {


                                if (data == "false")	//if no errors
                                {
                                    alert('El tiempo de su sessión ha expirado');
                                    location.href = '<?php echo base_url() ?>inicio';
                                } else {
                                    if ($("#inicrefresh").length == 0) {
                                        stoprefresh();
                                    } else {

                                        getVentas();
                                    }
                                }
                            }
                        });

                    }, 300000); //$this->session->userdata('REFRESCAR_PEDIDOS')


                $('#inicrefresh').fadeOut(0);
                $('#stoprefresh').fadeIn(0);
            }
        }
        else {
            stoprefresh();
        }
    }
    function stoprefresh(refresh) {
        clearInterval(refresh);
        $('#stoprefresh').fadeOut(0);
        $('#inicrefresh').fadeIn(0);
    }
    function agregarPedidos() {
        var metros_c = $("#suma_metros_cubicos").val();

        if ($('input:checkbox').is(':checked')) {
            var pedidos = [];

            $("input:checkbox:checked").each(function () {
                pedidos.push($(this).val());
            });

            $.ajax({
                url: '<?php echo $ruta.'venta/cargarCamion'; ?>',
                type: 'POST',
                data: {
                    'metros_c': metros_c,
                    'pedidos': pedidos
                },

                success: function (data) {

                    $("#visualizarCamiones").html(data);
                    $("#visualizarCamiones").modal('show');


                }
            });
        }
        else {
            $("#seleccionarPedido").modal('show');
        }

    }


    var grupo = {

        ajaxgrupo: function () {
            return $.ajax({
                url: '<?= base_url()?>venta/consultar?buscar=pedidos'

            })
        },
        guardar: function () {
            $("#btnconfirmar").addClass('disabled');
            if ($("#camion").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar un camión</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);
                $("#btnconfirmar").removeClass('disabled');
                return false;
            }


            if ($("#fecha_consolidado").val() == '') {
                $("#btnconfirmar").removeClass('disabled');
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe seleccionar una feha de entrega</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }


            var metroscamion = parseFloat($("#metroscamion").val());
            var metrospedido = parseFloat($("#metros").val());
            if (metroscamion < metrospedido) {
                $("#btnconfirmar").removeClass('disabled');
                $("#confirmarCarga").modal('show');
                return false;
            }
            else {
                App.formSubmitAjax($("#formcamion").attr('action'), this.ajaxgrupo, 'visualizarCamiones', 'formcamion');

            }
        },
        guardarconsolidado: function () {
            $("#btnguardarconsolidado").addClass('disabled');
            $("#confirmarCarga").modal('hide');
            App.formSubmitAjax($("#formcamion").attr('action'), this.ajaxgrupo, 'visualizarCamiones', 'formcamion');

        }
    }
</script>
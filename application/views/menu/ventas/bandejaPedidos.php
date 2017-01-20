<?php $ruta = base_url();

?>
<style>


    .tcharm {
        background-color: #fff;
        border: 1px solid #dae8e7;
        width: 300px;
        padding: 0 20px;
        overflow-y: auto;
    }

    .tcharm-header {
        text-align: center;
    }

    .tcharm-body .row {
        margin: 20px 3px;
    }

    .tcharm-close {
        text-decoration: none !important;
        color: #333333;
        padding: 3px;
        border: 1px solid #fff;
        float: left;
    }

    .tcharm-close:hover {
        background-color: #dae8e7;
        color: #333333;
    }
</style>

<ul class="breadcrumb breadcrumb-top">
    <li>Flujo de Trabajo</li>
    <li><a href="">Bandeja de pedidos</a></li>
</ul>
<div class="block">

    <div id="charm" class="tcharm">
        <div class="tcharm-header">

            <h3><a href="#" class="fa fa-arrow-right tcharm-close"></a> <span>Filtros Avanzados</span></h3>
        </div>

        <div class="tcharm-body">

            <div class="row">
                <div class="col-md-4" style="text-align: center;">
                    <button type="button" class="btn btn-default btn_buscar">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <div class="col-md-4" style="text-align: center;">
                    <button id="btn_filter_reset" type="button" class="btn btn-warning">
                        <i class="fa fa-refresh"></i>
                    </button>
                </div>
                <div class="col-md-4" style="text-align: center;">
                    <button type="button" class="btn btn-danger tcharm-trigger">
                        <i class="fa fa-remove"></i>
                    </button>
                </div>

            </div>

            <div class="row">
                <label class="control-label">Vendedor:</label>
                <select id="vendedor" class=" campos" name="vendedor">
                    <option value="">Todos</option>
                    <?php if (isset($vendedores)) {
                        foreach ($vendedores as $vendedor) {
                            ?>
                            <option value="<?= $vendedor->nUsuCodigo; ?>"> <?= $vendedor->nombre ?> </option>

                        <?php }
                    } ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">
                    Zonas:
                </label>
                <select id="zona" class=" campos" name="zona">
                    <option value="">Todos</option>
                    <?php if (isset($zonas)) {
                        foreach ($zonas as $zona) {
                            ?>
                            <option value="<?= $zona['zona_id']; ?>"> <?= $zona['zona_nombre'] ?> </option>

                        <?php }
                    } ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Cliente:</label>
                <select id="client" class="campos" name="client">
                    <option value="">Todos</option>
                    <?php if (isset($clientes)) {
                        foreach ($clientes as $client) {
                            ?>
                            <option value="<?= $client['id_cliente']; ?>"> <?= $client['razon_social'] ?> </option>

                        <?php }
                    } ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Estado:</label>
                <select id="estatus" class="campos" name="estatus">
                    <option selected value="<?php echo PEDIDO_GENERADO ?>"><?php echo PEDIDO_GENERADO ?></option>
                    <option value="<?php echo PEDIDO_ANULADO ?>"><?php echo PEDIDO_ANULADO ?></option>
                    <option value="<?php echo PEDIDO_ENVIADO ?>"><?php echo PEDIDO_ENVIADO ?></option>
                    <option value="<?php echo PEDIDO_ENTREGADO ?>">COMPLETADO</option>
                </select>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-md-1">
            <label class="label-control">Desde</label>
        </div>
        <div class="col-md-2">
            <input type="text" style="cursor: pointer;" name="fecha_desde" id="fecha_desde" value="<?= date('d-m-Y') ?>"
                   required="true"
                   class="form-control fecha campos input-datepicker" readonly="readonly">
        </div>
        <div class="col-md-1">
            <label class="label-control">Hasta</label>
        </div>
        <div class="col-md-2">
            <input type="text" style="cursor: pointer;" name="fecha_hasta" id="fecha_hasta" value="<?= date('d-m-Y') ?>"
                   required="true"
                   class="form-control fecha campos input-datepicker" readonly="readonly">
        </div>

        <div class="col-md-3">
            <input type="checkbox" id="incluir_fecha" checked>
            <label for="incluir_fecha"
                   class="control-label"
                   style="cursor: pointer;">
                Incluir Filtro de Fecha
            </label>
        </div>

        <div class="col-md-1">
            <button type="button" title="Buscar" class="btn btn-default form-control btn_buscar">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <div class="col-md-1">
            <button type="button" title="Filtros Avanzados" class="btn btn-primary tcharm-trigger form-control">
                <i class="fa fa-plus"></i>
            </button>
        </div>
        <div class="col-md-1">
            <button type="button" title="Parar Consulta Automatica" id="stoprefresh" style="display:none;"
                    onclick="stoprefresh();"
                    class="btn btn-warning"><i
                    class="fa fa-stop"></i>
            </button>
            <button type="button" title="Iniciar Consulta Automatica" id="inicrefresh" onclick="refreshpedidos();"
                    class="btn btn-primary"><i
                    class="fa fa-play"></i>
            </button>
        </div>

    </div>

    <div id="consolidado_block" class="form-group row">
        <h4>Asignaci&oacute;n de Consolidado</h4>
        <div class="col-md-2">
            <label class="">Consolidados</label>
        </div>
        <div class="col-md-4">
            <div class="input-group">
                <select id="consolidado_id" class="form-control">
                    <option value="0">Nuevo Consolidado</option>
                    <?php foreach ($consolidados as $consolidado): ?>
                        <option value="<?= $consolidado['consolidado_id'] ?>">
                            <?= sumCod($consolidado['consolidado_id'], 6) ?> |
                            <?= date('d-m-Y', strtotime($consolidado['fecha'])) ?> |
                            <?= $consolidado['camiones_placa'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <a type="button" id="ver_pedido" href="#"
                   class="input-group-addon">
                    <i class="fa fa-list"></i>
                </a>
            </div>
            <small style="color: #cdcdcd;"># Consolidado | Fecha de Entrega | Cami&oacute;n</small>
        </div>

        <div class="col-md-2">
            <div class="input-group">
                <button type="button" id="añadircamion" onclick="agregarPedidos();" class=".btn btn-primary form-control"
                style="color: white;"><i
                        class="fa fa-truck"></i>
                    Asignar Pedidos
                </button>
                <span type="button" id="ver_pedido"
                      class="input-group-addon">
                    <span id="suma_metros_cubicos"></span> m<sup>3</sup>
                </span>
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

    <div class="modal fade" id="asignar_" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
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

                    <p>Presione "Confirmar" para guardar el pedido de todos modos o "Cancelar" para elegir otro
                        camión.</p>
                </div>
                <br><br><br>

                <div class="modal-footer">
                    <button type="button" id="btnguardarconsolidado" class="btn btn-primary"
                            onclick="grupo.guardarconsolidado()">
                        <li class="glyphicon glyphicon-thumbs-up"></li>
                        Guardar
                    </button>
                    <button type="button" class="btn btn-warning" data-dismiss="modal">
                        <li class="glyphicon glyphicon-thumbs-down"></li>
                        Cancelar
                    </button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>
    </div>
    <script src="<?php echo $ruta; ?>recursos/editable/jquery.jeditable.js"></script>
    <script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>


    <script type="text/javascript">
        var refresh = true;
        $(function () {

            $("#charm").tcharm({
                'position': 'right',
                'display': false,
                'top': '50px'
            });


            $(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});

            getVentas();

            $('.btn_buscar').on('click', function () {
                getVentas();
            });

            $("#incluir_fecha").on('change', function () {
                getVentas();
            });

            $("#btn_filter_reset").on('click', function () {
                $('#vendedor').val("").trigger('chosen:updated');
                $('#zona').val('').trigger('chosen:updated');
                $('#client').val('').trigger('chosen:updated');
                $('#estatus').val("GENERADO").trigger('chosen:updated');
                getVentas();
                //$("#cliente_id").val('0').trigger('chosen:updated');
            });

            $("#ver_pedido").on('click', function () {
                show_msg('success', $('#consolidado_id').val());
            });

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
            $("#charm").tcharm('hide');
            if ($('#mvisualizarVenta').hasClass('in') || $("#ventamodal").hasClass('in')) {

            } else {
                // renewSession();

                $("#suma_metros_cubicos").html('0');
                var fercha_desde = $("#fecha_desde").val();
                var fercha_hasta = $("#fecha_hasta").val();
                var locales = $("#locales").val();
                var estatus = $("#estatus").val();
                var listar = $("#listar").val();
                var vendedor = $("#vendedor").val();
                var client = $("#client").val();
                var zona = $("#zona").val();

                if ($("#incluir_fecha").prop('checked'))
                    var fecha_flag = 1;
                else
                    var fecha_flag = 0;

                // $("#hidden_consul").remove();


                $.ajax({
                    url: '<?= base_url()?>venta/get_ventas',
                    data: {
                        'id_local': locales,
                        'desde': fercha_desde,
                        'hasta': fercha_hasta,
                        'fecha_flag': fecha_flag,
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
                            if ($("#estatus").val() == 'GENERADO') {
                                $("#consolidado_block").show();
                            }
                            else {
                                $("#consolidado_block").hide();
                            }
                            $("#tabla").html(data);
                        }


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
                    refresh = setInterval(function () {
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
        function stoprefresh() {
            clearInterval(refresh);
            $('#stoprefresh').fadeOut(0);
            $('#inicrefresh').fadeIn(0);
        }
        function agregarPedidos() {
            var metros_c = $("#suma_metros_cubicos").html().trim();

            var pedidos = [];

            $(".cargarPedido").each(function () {
                if ($(this).prop('checked'))
                    pedidos.push($(this).val());
            });

            if (pedidos.length == 0) {
                show_msg('warning', '<h4>Error. </h4> Seleccione al menos un pedido.');
                return false;
            }

            if ($("#consolidado_id").val() == 0) {
                $.ajax({
                    url: '<?php echo $ruta . 'venta/cargarCamion'; ?>',
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
                $.ajax({
                    url: '<?= $ruta . 'consolidadodecargas/editar_consolidado' ?>' + '/' + $("#consolidado_id").val(),
                    type: 'POST',
                    data: {
                        'metros_c': metros_c,
                        'pedidos_id': JSON.stringify(pedidos)
                    },

                    success: function (data) {

                        show_msg('success', '<h4>Correcto</h4> Asignaci&oacute;n a consolidado ejecutada');
                        $('.btn_buscar').click();

                    }
                });
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
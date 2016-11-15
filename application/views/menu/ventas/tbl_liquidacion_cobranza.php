<?php $ruta = base_url(); ?>

<form name="form" method="post" id="form" action="<?= $ruta ?>venta/guardar_liquidar">
    <div class="btn-group" align="center">
        <button type="button" onclick="liquidar()"
                class='btn btn-primary'>Liquidar
        </button>
    </div>
    <div class="btn-group" align="center">
        <button type="button" onclick="anular()"
                class='btn btn-primary'>Anular
        </button>
    </div>
    <br>
    <br>
    <br>
    <div class="form-group row">

        <div class="col-md-2">
            <label>TOTAL CAJA</label>
        </div>
        <div class="col-md-2" id="mostrar_caja">

            <input type="text" name="caja" id="borrarcaja" class='form-control' value="0" readonly>
        </div>
        <div class="col-md-2">
            <label>TOTAL BANCO</label>
        </div>
        <div class="col-md-2" id="mostrar_banco">
            <input type="text" name="banco" id="borrarbanco" class='form-control' value="0" readonly>
        </div>
    </div>
    <table class='table table-striped dataTable table-bordered no-footer' id="lstPagP" name="lstPagP">
        <thead>
        <tr>
            <th style="white-space: nowrap;"><input type="checkbox" id="check_all" class=""
                                                    value="1"> Accion
            </th>
            <th>ID</th>
            <th>Nro Venta</th>

            <th>Vendedor</th>
            <th class='tip' title="Fecha Registro">Fecha de pago</th>
            <th class='tip' title="Monto Credito Solicitado">M&eacute;todo de Pago</th>
            <th class='tip' title="Monto Cancelado">Abono <?php echo MONEDA ?></th>
            <th>Estado&nbsp;</th>
            <th>Editar</th>

        </tr>
        </thead>
        <tbody>
        <?php if (count($lstVenta) > 0): ?>
            <?php foreach ($lstVenta as $v): ?>
                <tr>
                    <td class='actions_big' style="width: 5%; text-align: center;">

                        <input type="checkbox" id="id<?php echo $v['historial_id']; ?>" name="historial[]"
                               class="check_all_input"
                               value="<?php echo $v['historial_id']; ?>">
                        <input type="hidden" id="tipo<?php echo $v['historial_id']; ?>"
                               value="<?php echo $v['tipo_metodo']; ?>">
                        <input type="hidden" id="monto<?php echo $v['historial_id']; ?>"
                               value="<?php echo $v['historial_monto']; ?>">
                    </td>
                    <td style="text-align: center;"><?php echo $v['venta_id']; ?></td>
                    <td style="text-align: center;"><?php echo $v['documento_Serie'] . "-" . $v['documento_Numero']; ?></td>

                    <td><?php echo $v['nombre']; ?></td>
                    <td><?php echo date("d-m-Y H:i:s", strtotime($v['historial_fecha'])) ?></td>
                    <td><?php echo $v['nombre_metodo']; ?></td>
                    <td><?php
                        $pos = strrpos($v['historial_monto'], '.');
                        echo " " . MONEDA;
                        if ($pos === false) {
                            echo $v['historial_monto'];
                        } else {
                            echo substr($v['historial_monto'], 0, $pos + 3);
                        }
                        ?></td>
                    <td><?php echo $v['historial_estatus']; ?></td>
                    <td>
                        <div class="btn-group" align="center">
                            <button type="button"
                                    onclick="editar(<?= $v['historial_id'] ?>,<?= $v['historial_monto'] ?>,'<?= $v['documento_Serie'] . "-" . $v['documento_Numero'] ?>',<?= $v['credito_id'] ?>)"
                                    class='btn btn-primary'>Editar
                            </button>
                        </div>
                    </td>

                </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Seccion Visualizar -->
    <div class="modal fade" id="liquidar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Liquidar COBRANZAS</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            Est&aacute; seguro que desea liquidar un total de <p id="mostrar_cantidad"></p>
                        </div>


                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="" class="btn btn-primary" onclick="guardar()">Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>


    </div>


    <div class="modal fade" id="anular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
         aria-hidden="true">

        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Anular Pagos</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-6">
                            Est&aacute; seguro que desea anular un total de <p id="mostrar_cantidad_anular"></p>
                        </div>


                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="" class="btn btn-primary" onclick="guardar_anular()">Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>


    </div>

</form>
<!--- ----------------- -->


<div class="modal fade" id="editar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Editar Pago. Venta n&uacute;mero:
                    <div id="mostrar_venta"></div>
                </h4>
            </div>

            <form id="form_editar" name="form_editar" action="<?= $ruta ?>venta/editar_pago_historial" method="post">
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-md-3 ">
                            <label for="nro_venta" class="control-label">Monto Abonado:</label>
                        </div>

                        <div class="col-md-3" id="">

                            <input type="text" name="montoabonado" id="montoabonado" class='input-square form-control'
                                   readonly>
                        </div>


                        <div class="col-md-3">
                            <label for="nro_venta" class="control-label">Monto Nuevo:</label>
                        </div>

                        <div class="col-md-3" id="">
                            <input type="hidden" name="historial_aeditar" id="historial_aeditar"
                                   class='input-square form-control'>
                            <input type="hidden" name="venta_aeditar" id="venta_aeditar"
                                   class='input-square form-control'>

                            <input type="number" name="montonuevo" id="montonuevo" min="0"
                                   class='input-square form-control' onkeyup="return soloNumeros(event);">
                        </div>


                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" id="" class="btn btn-primary" onclick="guardar_editar()">Confirmar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
        </div>
        <!-- /.modal-content -->
    </div>


</div>

<script type="text/javascript">

    var lst_venta = new Array();
    $(document).ready(function () {

        $("#montonuevo").keyup(function () {
            var value = $(this).val();

        });
        var caja = parseFloat(0.0);
        var banco = parseFloat(0.0);

        $("#check_all").on('change', function (e) {
            if ($("#check_all").prop('checked')) {
                caja = parseFloat(0.0);
                banco = parseFloat(0.0);
            }
            $(".check_all_input").each(function () {
                $(this).prop('checked', $("#check_all").prop('checked'));

                if (!$("#check_all").prop('checked')) {
                    caja = parseFloat(0.0);
                    banco = parseFloat(0.0);
                    $("#borrarcaja").attr('value', caja);
                    $("#borrarbanco").attr('value', banco);
                }
                else
                    $(this).change();
            });
        });

        $(".check_all_input").on('change', function () {
            var $this = $(this);
            var id_historial = $(this).attr('value');
            var monto = parseFloat($("#monto" + id_historial).val());

            if ($this.prop("checked")) {

                if ($("#tipo" + id_historial).val() == "CAJA") {
                    caja = caja + monto;
                    setTimeout(function () {
                        $("#borrarcaja").attr('value', caja)

                    }, 1)
                }
                if ($("#tipo" + id_historial).val() == "BANCO") {
                    banco = banco + monto;
                    setTimeout(function () {
                        $("#borrarbanco").attr('value', banco)

                    }, 1)
                }

            } else {

                if ($("#tipo" + id_historial).val() == "CAJA") {
                    caja = caja - monto;
                    setTimeout(function () {
                        $("#borrarcaja").attr('value', caja)

                    }, 1)
                }
                if ($("#tipo" + id_historial).val() == "BANCO") {
                    banco = banco - monto;
                    setTimeout(function () {
                        $("#borrarbanco").attr('value', banco)

                    }, 1)
                }
            }

        });

        TablesDatatables.init(1);


    });


    function liquidar() {
        var total = $('input[name="historial[]"]:checked').length;

        if (total < 1) {
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar al menos una opci&oacute;n</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            $(this).prop('disabled', true);

            return false;

        }

        $("#borrar_cantidad").remove();


        $("#mostrar_cantidad").append('<p id="borrar_cantidad">' + total + ' Pagos</p>')
        $('#liquidar').modal('show');
        //$("#id").attr('value', id);
    }


</script>
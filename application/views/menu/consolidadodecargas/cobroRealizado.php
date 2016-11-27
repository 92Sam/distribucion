<?php $ruta = base_url(); ?>
<div class="modal-dialog modal-lg" style="width: 85%">

    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Informaci&oacute;n de cobro</h4>
        </div>

        <div class="modal-body">
            <form id="form_confirmar">
                <div class="table-responsive" id="tablaresultados">
                    <table class="table table-striped dataTable table-bordered">
                        <thead>
                        <tr>

                            <th>Nro Nota de Entrega</th>
                            <th>Estado</th>
                            <th>Monto Total</th>
                            <th>Saldo</th>
                            <th>Pago realizado</th>
                            <th>Caja <?php if ($tipo == "CONFIRMAR") {
                                    echo '<input type="checkbox" id="marcar_caja"></th>';
                                } ?>

                            <th>Bancos <?php if ($tipo == "CONFIRMAR") {
                                    echo '<input type="checkbox" id="marcar_banco"></th>';
                                } ?>

                            <th>Confirmar <?php if ($tipo == "CONFIRMAR") {
                                    echo '<input type="checkbox" id="marcar_confirmar">';
                                } ?>
                            </th>
                        </tr>
                        </thead>
                        <tbody id="tbody">
                        <?php
                        $i = 0;
                        $total_caja = 0.00;
                        $total_banco = 0.00;
                        $TOTAL = 0.00;
                        foreach ($consolidado as $row): //var_dump($pd);
                            $TOTAL = $TOTAL+ $row['liquidacion_monto_cobrado'] ;
                            $i++;
                            if ($row['confirmacion_caja_id'] != null) {
                                $total_caja = $total_caja + $row['confirmacion_monto_cobrado_caja'];
                            }

                            if ($row['confirmacion_banco_id'] != null) {
                                $total_banco = $total_banco + $row['confirmacion_monto_cobrado_bancos'];
                            }
                            ?>
                            <tr id="tr<?= $i; ?>">
                                <td>
                                    <?php echo $row['pedido_id']; ?>
                                    <input id="pedido_id<?= $i; ?>" type="hidden" name="pedido_id[<?= $i; ?>]"
                                           class="form-control" value="<?= $row['pedido_id'] ?>" disabled/>
                                    <input type="hidden" name="consolidado_id" class="form-control"
                                           value="<?= $row['consolidado_id'] ?>"/>
                                </td>
                                <td>
                                    <?php echo $row['venta_status']; ?>

                                </td>
                                <td>
                                    <?php echo number_format($row['total'],2); ?>
                                    <input type="hidden" id="total<?= $i ?>" class="form-control"
                                           value="<?= $row['total'] ?>"/>

                                </td>
                                <td>
                                    <div
                                        id="mostrar_suma<?= $i ?>"><?php echo number_format( $row['total'] - $row['liquidacion_monto_cobrado'],2); ?></div>
                                </td>
                                <td>
                                    <?php echo number_format($row['liquidacion_monto_cobrado'],2); ?>
                                    <input type="hidden" name="liquidacion_monto_cobrado[<?= $i ?>]"
                                           class="form-control"
                                        <?php if ($row['liquidacion_monto_cobrado'] == null) { ?> value="0.00"  <?php } else { ?>
                                            value="<?= $row['liquidacion_monto_cobrado'] ?>"
                                        <?php } ?>

                                           id="liquidacion_monto_cobrado<?= $i ?>"/>
                                </td>
                                <td>
                                    <div class="row">
                                        <div class="col-md-10">
                                            <input type="text" onkeypress="return soloNumeros(event);"
                                                   id="inputcaja<?= $i ?>"
                                                <?php if ($tipo == "CONFIRMAR") {
                                                    echo ' value="0" ';
                                                } else { ?>  value="<?= $row["confirmacion_monto_cobrado_caja"] ?>"  <?php } ?>

                                                   name="input_caja[<?= $i; ?>]"
                                                   class="form-control inputcajas col-md-2"
                                                   disabled
                                            />
                                        </div>
                                        <div class="col-md-2">
                                            <?php if ($tipo == "CONFIRMAR") {
                                                echo '<input  type="checkbox" disabled name="check_caja[' . $i . ']" class="cajas" value="' . $i . '" id="caja' . $i . '"/>';
                                            } ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="col-md-10">
                                        <div class="col-md-10">
                                            <input type="text" onkeypress="return soloNumeros(event);"
                                                   id="inputbanco<?= $i ?>" name="input_bancos[<?= $i; ?>]"
                                                <?php if ($tipo == "CONFIRMAR") {
                                                    echo ' value="0" ';
                                                } else { ?>  value="<?= $row["confirmacion_monto_cobrado_bancos"] ?>"  <?php } ?>
                                                   class="form-control inputbancos" disabled/>

                                            <select name="bancos[<?= $i; ?>]" class="form-control"
                                                    id="select_bancos<?= $i ?>"
                                                    disabled>
                                                <option value=""> Seleccione</option>
                                                <?php
                                                foreach ($bancos as $banco) { ?>

                                                    <?php if ($tipo == "VER" and $row["confirmacion_banco_id"] == $banco['banco_id']) {
                                                        ?>
                                                        <option value="<?= $banco['banco_id'] ?>"
                                                                selected> <?= $row['banco_nombre'] ?> </option>
                                                    <?php } else { ?>
                                                        <option
                                                            value="<?= $banco['banco_id'] ?>"> <?= $banco['banco_nombre'] ?> </option>  <?php } ?>

                                                <?php }

                                                ?>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <?php if ($tipo == "CONFIRMAR") {
                                                echo '<input type="checkbox" name="check_banco[' . $i . ']" disabled class="bancos" value="' . $i . '" id="banco' . $i . '"/>';
                                            } ?>
                                            <div>
                                </td>
                                <td>
                                    <?php if ($tipo == "CONFIRMAR") {
                                        echo '<input type="checkbox" id="' . $i . '" value="0" name="confirmar[' . $i . ']" class="confirmar"/>';
                                    } ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>
                        <tr>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>
                                TOTALES <?php echo MONEDA; ?>
                            </td>
                            <td>
                                <input type="text" id="totales_caja" class="form-control"
                                    <?php if ($tipo == "CONFIRMAR") {
                                        echo ' value="0.00" ';
                                    } else { ?>  value="<?= $total_caja ?>"  <?php } ?>
                                       disabled>
                            </td>
                            <td>
                                <input type="text" id="totales_banco"
                                       class="form-control" <?php if ($tipo == "CONFIRMAR") {
                                    echo ' value="0.00" ';
                                } else { ?>  value="<?= $total_banco ?>"  <?php } ?> disabled>
                            </td>
                            <td>

                            </td>
                        </tr>


                        <tr>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>

                            </td>
                            <td>
                                TOTAL GENERAL <?php echo MONEDA; ?>
                            </td>
                            <td>

                            </td>
                            <td>
                                <input type="text" id="TOTAL"
                                       class="form-control"   value="<?= number_format($TOTAL,2) ?>" disabled>
                            </td>
                            <td>

                            </td>
                        </tr>
                        </tbody>
                    </table>

                </div>

            </form>
        </div>
        <div class="modal-footer" id="">


            <?php if ($tipo == "CONFIRMAR") {
                echo '<button type="button" id="cerrar_confirmacion" class="btn btn-primary"  onclick="validar_confirmacion()">Cerrar
                        Confirmaci&oacute;n de Pago
                    </button>';
            } ?>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        </div>
    </div>

</div>

<script>

    $(function () {


        $(".inputcajas").on('keyup', function(){

            var total=0.00;
            $(".inputcajas").each( function(){

                if($(this).val()!=""){
                    var loquetiene=$(this).val()
                    total=parseFloat(total)+parseFloat(loquetiene)

                }

            })
            $('#totales_caja').val(total.toFixed(2));
            var total_caja =  $('#totales_caja').val();
            var total_banco =  $('#totales_banco').val();
            var totall = parseFloat(total_caja)+parseFloat(total_banco);
            $('#TOTAL').val(totall.toFixed(2));


        })


        $(".inputbancos").on('keyup', function(){

            var total=0.00;
            $(".inputbancos").each( function(){

                if($(this).val()!=""){
                    var loquetiene=$(this).val()
                    total=parseFloat(total)+parseFloat(loquetiene)
                }

            })
            $('#totales_banco').val(total.toFixed(2))
            var total_caja =  $('#totales_caja').val();
            var total_banco =  $('#totales_banco').val();
            var totall = parseFloat(total_caja)+parseFloat(total_banco);
            $('#TOTAL').val(totall.toFixed(2));
        })

        $("#marcar_confirmar").on('click', function () {
            var marcar_confirmar = $("#marcar_confirmar")

            $('.confirmar').each(function () {
                var id = $(this).attr('id')

                if (marcar_confirmar.prop('checked')) {

                    $(this).prop('checked', true);
                    $("tr#tr" + id + " input[type=text]").prop('disabled', false)
                    $("#select_bancos" + id).prop('disabled', false)
                    $("#pedido_id" + id).prop('disabled', false)
                    $(this).prop('value', '1')
                    $("#banco" + id).prop('disabled', false)
                    $("#caja" + id).prop('disabled', false)
                } else {
                    $("#banco" + id).prop('checked', false)
                    $("#caja" + id).prop('checked', false)

                    $("#banco" + id).prop('disabled', true)
                    $("#caja" + id).prop('disabled', true)

                    $("#inputcaja" + id).val(0)
                    $("#inputbanco" + id).val(0)

                    $('#totales_caja').val(0.00)
                    $('#totales_banco').val(0.00)
                    $('#TOTAL').val(0.00)


                    $("#marcar_caja").prop('checked', false);
                    $("#marcar_banco").prop('checked', false);

                    $(this).prop('checked', false);
                    $("tr#tr" + id + " input[type=text]").prop('disabled', true)
                    $("#select_bancos" + id).prop('disabled', true)
                    $("#pedido_id" + id).prop('disabled', true)
                    $(this).prop('value', '0')

                }

            })

        })

        $("#marcar_caja").on('click', function () {

            var marcar_caja = $("#marcar_caja")
            var suma = 0.00;
            var verificar_check = false
            if (marcar_caja.prop('checked')) {
                $("#marcar_banco").prop('checked', false)
                $('.cajas').each(function () {
                    var id = $(this).val()
                    if ($("#" + id).prop('checked')) {
                        ///el confirmar de esa fila
                        $("#inputcaja" + id).prop('disabled', false)
                        $("#inputcaja" + id).val(parseFloat($("#liquidacion_monto_cobrado" + id).val()))
                        $("#inputbanco" + id).val(0.00)
                        $("#inputbanco" + id).prop('disabled', 'disabled')
                        $("#select_bancos" + id).prop('disabled', 'disabled')
                        $("#banco" + id).prop('checked', false)
                        suma = parseFloat(suma) + parseFloat($("#inputcaja" + id).val())
                        $(this).prop('checked', true)

                        verificar_check = true  /// esto para saber si hay algun check de cnfirmar marcado
                    } else {


                        suma = parseFloat(suma) + parseFloat(0.00)
                        $(this).prop('checked', false)
                    }

                })


                if (verificar_check == false) {
                    marcar_caja.prop('checked', false)
                } else {
                    marcar_caja.prop('checked', true)
                }


                $('#totales_caja').val(suma.toFixed(2))
                $('#totales_banco').val(0.00)
            } else {

                $('.cajas').prop('checked', false)
                $('.inputcajas').val(0)
                $('#totales_caja').val(0.00)

            }


        })

        $("#marcar_banco").on('click', function () {
            var marcar_banco = $("#marcar_banco")
            var suma = 0.00;
            var habilitar_boton = false;
            var verificar_check = false
            if (marcar_banco.prop('checked')) {
                $("#marcar_caja").prop('checked', false)
                $('.bancos').each(function () {
                    var id = $(this).val()
                    if ($("#" + id).prop('checked')) {
                        ///el confirmar de esa fila
                        $("#inputbanco" + id).prop('disabled', false)
                        $("#inputbanco" + id).val(parseFloat($("#liquidacion_monto_cobrado" + id).val()))
                        $("#inputcaja" + id).val(0.00)
                        $("#inputcaja" + id).prop('disabled', 'disabled')
                        $("#select_bancos" + id).prop('disabled', false)
                        $("#caja" + id).prop('checked', false)
                        suma = parseFloat(suma) + parseFloat($("#inputbanco" + id).val())
                        $(this).prop('checked', true)

                        verificar_check = true  /// esto para saber si hay algun check de cnfirmar marcado

                    } else {
                        suma = parseFloat(suma) + parseFloat(0.00)
                        $(this).prop('checked', false)
                    }

                })

                if (verificar_check == false) {
                    marcar_banco.prop('checked', false)
                } else {
                    marcar_banco.prop('checked', true)
                }

                $('#totales_banco').val(suma.toFixed(2))
                $('#totales_caja').val(0.00)
            } else {

                $('.bancos').prop('checked', false)
                $('.inputbancos').val(0)
                $('#totales_banco').val(0.00)

            }

        })
        $(".confirmar").on('click', function () {

            var id_confirmar = $(this).attr('id')
            var suma = 0.00
            if ($(this).prop('checked')) {
                $("tr#tr" + id_confirmar + " input[type=text]").attr('disabled', false)
                $("#select_bancos" + id_confirmar).attr('disabled', false)
                $("#pedido_id" + id_confirmar).attr('disabled', false)
                $(this).attr('value', '1')
                $("#banco" + id_confirmar).prop('disabled', false)
                $("#caja" + id_confirmar).prop('disabled', false)
            } else {
                $("#marcar_banco").prop('checked', false)
                $("#marcar_banco").prop('caja', false)

                $("#banco" + id_confirmar).prop('checked', false)
                $("#caja" + id_confirmar).prop('checked', false)

                $("#banco" + id_confirmar).prop('disabled', true)
                $("#caja" + id_confirmar).prop('disabled', true)

                $("#select_bancos" + id_confirmar).attr('disabled', true)
                $("#pedido_id" + id_confirmar).attr('disabled', true)
                $(this).attr('value', '0')


                var total_caja = $('#totales_caja').val()
                var total_banco = $('#totales_banco').val()
                if (total_caja > 0) {
                    suma = parseFloat(total_caja) - parseFloat($("#inputcaja" + id_confirmar).val())
                    $('#totales_caja').val(suma.toFixed(2))
                } else {
                    $('#totales_caja').val(0.00)
                }

                if (total_banco > 0) {
                    suma = parseFloat(total_banco) - parseFloat($("#inputbanco" + id_confirmar).val())
                    $('#totales_banco').val(suma.toFixed(2))
                } else {
                    $('#totales_banco').val(0.00)
                }
                $("#inputcaja" + id_confirmar).val(0)
                $("#inputbanco" + id_confirmar).val(0)
                $("tr#tr" + id_confirmar + " input[type=text]").attr('disabled', true)
            }

        })

        $(".cajas").on('click', function () {
            var suma = 0.00
            var id_caja = $(this).val()
            var total_caja = $('#totales_caja').val()
            var total_banco = $('#totales_banco').val()


            if ($("#" + id_caja).prop('checked')) {


                if ($(this).prop('checked')) {


                    $("#inputcaja" + id_caja).prop('disabled', false)
                    var inputcaja= $("#inputcaja" + id_caja).val()  /// este es el valor antes de agregarle el que se coloca por defecto

                    var monto = $("#liquidacion_monto_cobrado" + id_caja).val();
                    $("#inputcaja" + id_caja).val(monto)  //// aqui se le coloca el valor por defecto al marcar el check

                    if ($("#banco" + id_caja).prop('checked')) {
                        if (total_banco > 0) {
                            suma = parseFloat(total_banco) - parseFloat($("#inputbanco" + id_caja).val())
                            $('#totales_banco').val(suma.toFixed(2))
                        } else {
                            $('#totales_banco').val(0.00)
                        }
                    }else{
                        if($("#inputbanco" + id_caja).val()!=""){
                            suma=parseFloat(total_banco)-parseFloat($("#inputbanco" + id_caja).val())
                            $('#totales_banco').val(suma.toFixed(2))

                        }
                    }
                    $("#inputbanco" + id_caja).prop('disabled', 'disabled')
                    $("#select_bancos" + id_caja).prop('disabled', 'disabled')
                    $("#inputbanco" + id_caja).val(0.00)


                    if (total_caja > 0) {
                        suma = (parseFloat(total_caja) + parseFloat($("#inputcaja" + id_caja).val()))-parseFloat(inputcaja)
                        ////aqui sumamos lo que esta en total mas el valor por defecto menos lo que tenia
                        $('#totales_caja').val(suma.toFixed(2))
                    } else {
                        suma = parseFloat($("#inputcaja" + id_caja).val())
                        $('#totales_caja').val(suma.toFixed(2))
                    }
                    // $('#totales_caja').val(parseFloat(total_caja) + parseFloat($("#inputcaja" + id_caja).val()))

                    $("#banco" + id_caja).prop('checked', false)
                } else {


                    if (total_caja > 0) {
                        suma = parseFloat(total_caja) - parseFloat($("#inputcaja" + id_caja).val())
                        $('#totales_caja').val(suma.toFixed(2))
                    } else {
                        $('#totales_caja').val(0.00)
                    }
                    $("#inputcaja" + id_caja).val(0.00)
                    $("#inputbanco" + id_caja).prop('disabled', false)
                    $("#select_bancos" + id_caja).prop('disabled', false)
                    //$('#totales_caja').val(parseFloat(total_caja) - parseFloat($("#inputcaja" + id_caja).val()))
                    $("#inputcaja" + id_caja).prop('disabled', false)


                }
            }


        })

        $(".bancos").on('click', function () {
            var suma = 0.00
            var id_banco = $(this).val()
            var total_banco = $('#totales_banco').val()
            var total_caja = $('#totales_caja').val()

            if ($("#" + id_banco).prop('checked')) {

                if ($(this).prop('checked')) {
                    var inputbanco= $("#inputbanco" + id_banco).val()

                    $("#select_bancos" + id_banco).prop('disabled', false)
                    $("#inputbanco" + id_banco).prop('disabled', false)

                    var monto = $("#liquidacion_monto_cobrado" + id_banco).val();
                    $("#inputbanco" + id_banco).val(monto)

                    if ($("#caja" + id_banco).prop('checked')) {
                        if (total_caja > 0) {
                            suma = parseFloat(total_caja) - parseFloat($("#inputcaja" + id_banco).val())
                            $('#totales_caja').val(suma.toFixed(2))
                        } else {
                            $('#totales_caja').val(0.00)
                        }
                    }else{
                        if($("#inputcaja" + id_banco).val()!=""){
                            suma=parseFloat(total_caja)-parseFloat($("#inputcaja" + id_banco).val())
                            $('#totales_caja').val(suma.toFixed(2))

                        }
                    }
                    $("#inputcaja" + id_banco).prop('disabled', 'disabled')
                    $("#inputcaja" + id_banco).val(0.00)


                    if (total_banco > 0) {
                        suma = (parseFloat(total_banco) + parseFloat($("#inputbanco" + id_banco).val()))-parseFloat(inputbanco)
                        $('#totales_banco').val(suma.toFixed(2))
                    } else {
                        suma = $("#inputbanco" + id_banco).val()

                        $('#totales_banco').val(suma)
                    }

                    $("#caja" + id_banco).prop('checked', false)
                } else {

                    if (total_banco > 0) {
                        suma = parseFloat(total_banco) - parseFloat($("#inputbanco" + id_banco).val())
                        $('#totales_banco').val(suma.toFixed(2))
                    } else {
                        $('#totales_banco').val(0.00);
                    }
                    $("#inputbanco" + id_banco).prop('disabled', false)
                    $("#select_bancos" + id_banco).prop('disabled', false)
                    $("#inputbanco" + id_banco).val(0.00)
                    $("#inputcaja" + id_banco).prop('disabled', false)

                    //$('#totales_banco').val(parseFloat(total_caja) - parseFloat($("#inputcaja" + id_banco).val()))
                }
            }


        })

    });


</script>



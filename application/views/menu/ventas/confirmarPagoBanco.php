<?php $ruta = base_url(); ?>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <center><h3 class="modal-title">DEPOSITO EN BANCOS</h3></center>
        </div>
        <?php
        foreach ($pago as $campo) {
            $cliente = $campo['razon_social'];
            $adelanto = $campo['pagado'];
            $id = $campo['venta_id'];

        }

        ?>
        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    Se va a realizar la confirmacion de pago adelantado
                    hacia la BANCOS del cliente <?= $cliente ?> por <?= MONEDA ?> <?= $adelanto ?></div>
                <div class="col-md-4">


                    <select id="bancos" data-toggle="tooltip" data-placement="right" class="form-control"
                            title="Seleccione un banco">
                        <option value=0>Seleccione el BANCO</option>
                        <?php
                        foreach ($banco as $nombre) {
                            echo '<option value="' . $nombre['banco_id'] . '">' . $nombre['banco_nombre'] . '</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div class="col-md-12">

                <a class="btn btn-success
                  " data-original-title="fa fa-comment-o"
                   href="#"
                   onclick="confirmar(<?= $id ?>); ">
                    SI
                </a>

                <a class="btn btn-default " data-original-title="fa fa-comment-o"
                   href="#"
                   onclick="cerrar(); ">
                    NO
                </a></div>
        </div>
    </div>

</div>

<script>
    $(document).ready(function () {

    });
    function cerrar() {
        $('#pagoCaja').modal('hide');
    }
    function confirmar(id) {
        var idBanco = $("#bancos").val();
        if (idBanco == 0) {
            $('#bancos').tooltip('show')
        } else {
            $.ajax({
                url: '<?= $ruta ?>venta/pagoBancoCobrado',
                data: {'id': id, 'banco': idBanco, monto: '<?= $campo['pagado'] ?>'},
                type: 'POST',
                dataType:'json',
                success: function (data) {
                    if(data.success=='success') {
                        $("#tablaresultado").html(data);
                        $('#pagoCaja').modal('hide');
                        var growlType = 'success';
                        $.bootstrapGrowl('<h4>Pago Realizado!</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        recargarlista();
                    }else{
                        $('#bancos').focus()
                        var growlType = 'warning';
                        $.bootstrapGrowl('<h4>Ha currido un error</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        })
                        return false
                    }
                },
                error: function () {
                    $('#bancos').focus()
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>Ha currido un error</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    })
                    return false
                }
            })


        }

    }
</script>






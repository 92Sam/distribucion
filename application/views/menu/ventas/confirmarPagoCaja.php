<?php $ruta = base_url(); ?>
<div class="modal-dialog ">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <center><h3 class="modal-title">DEPOSITO EN CAJA</h3></center>
        </div>
        <?php
        foreach ($pago as $campo) {
            $cliente = $campo['razon_social'];
            $adelanto = $campo['pagado'];
            $id = $campo['venta_id'];

        }

        ?>
        <div class="modal-body">
            Se va a realizar la confirmacion de pago adelantado<br/>
                hacia CAJA del cliente <?= $cliente ?> por <?= MONEDA ?> <?= $adelanto ?> .<br>
            Está seguro?



        </div>
        <div class="modal-footer">


            <a class="btn btn-success" data-original-title="fa fa-comment-o"
               href="#"
               onclick="confirmar(<?= $id ?>); ">
                SI
            </a>

            <a class="btn btn-default " data-original-title="fa fa-comment-o"
               href="#"
               onclick="cerrar(); ">
                NO
            </a>

        </div>
    </div>
</div>
<script>
    function cerrar() {
        $('#pagoCaja').modal('hide');
    }
    function confirmar(id) {

        $.ajax({
            url: '<?= $ruta ?>venta/pagoCajaCobrado',
            data: {id: id, monto: '<?= $campo['pagado'] ?>'},
            dataType:'json',
            type:'POST',
            success: function (data) {

                if (data.success == 'success') {
                    $('#pagoCaja').modal('hide');
                    var growlType = 'success';
                    $.bootstrapGrowl('<h4>Pago Realizado!</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    })
                    recargarlista();
                } else {
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>Error!</h4> '+data.error, {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    })


                }
            }, error: function () {
                $('#bancos').focus()
                var growlType = 'warning';
                $.bootstrapGrowl('<h4>Ha currido un error</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                })
                return false
            }
        });

    }


</script>






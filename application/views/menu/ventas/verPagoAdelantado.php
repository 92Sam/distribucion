<?php $ruta = base_url(); ?>
<div class="modal-dialog ">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <center><h3 class="modal-title">Detalle de pago</h3></center>
        </div>
        <?php
        foreach ($pago as $campo) {
            // var_dump($campo);
            $cliente = $campo['razon_social'];
            $adelanto = $campo['pagado'];
            $fecha = $campo['confirmacion_fecha'];
            $id = $campo['venta_id'];
            $banco_nombre = $campo['banco_nombre'];

        }

        ?>
        <div class="modal-body">


            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-12">
                            Monto confirmado: <?php echo $adelanto ?>
                        </div>
                        <div class="col-md-12">
                            Feca de confirmacion: <?php echo $fecha ?>
                        </div>
                        <?php if ($banco_nombre != '') { ?>
                            <div class="col-md-12">
                                Banco: <?php echo $banco_nombre ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





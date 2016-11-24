<?php $ruta = base_url(); ?>
<table class="table table-striped dataTable table-bordered" >


    <thead>
    <tr>

        <th>ID del Pedido</th>
        <th>Estado</th>
        <th>Nombre Vendedor</th>
        <th>Nombre Cliente</th>
        <th>Fecha de Pago</th>
        <th>Importe Total de la Venta</th>
        <th>Pago Adelantado</th>
        <th>Confirmar</th>



    </tr>
    </thead>
    <tbody>
    <?php if (count($pagosAdl) > 0) {
        foreach ($pagosAdl as $campoPagos) {

            if(!empty($campoPagos['confirmacion_usuario'])) $estado = "CONFIRMADO";
            else $estado = "PENDIENTE";
            ?>
            <tr>

                <td class="center"><?php  echo $campoPagos['venta_id'] ?></td>
                <td><?= $estado ?></td>
                <td><?= $campoPagos['nombreVendedor'] ?></td>
                <td><?= $campoPagos['razon_social'] ?></td>
                <td><?= date('d-m-Y', strtotime($campoPagos['fecha'])) ?></td>
                <td><?= $campoPagos['total'] ?></td>
                <td><?= $campoPagos['pagado'] ?></td>
                <td class="center">

                    <div class="btn-group">
                        <?php
                            if($estado == "PENDIENTE") { ?>
                                <a class="btn btn-default" data-toggle="tooltip"
                                <a class="btn btn-default" data-toggle="tooltip"
                                   title="Ver" data-original-title="fa fa-comment-o"
                                   href="#"
                                   onclick="pagoCaja(<?= $campoPagos['venta_id'] ?>); ">
                                    Caja
                                </a>
                                <a class="btn btn-default" data-toggle="tooltip"
                                   title="Ver" data-original-title="fa fa-comment-o"
                                   href="#"
                                   onclick="pagoBanco(<?= $campoPagos['venta_id'] ?>); ">
                                    Banco
                                </a>
                                <?php
                            }else{?>
                                <a class="btn btn-default" data-toggle="tooltip"
                                   title="Ver" data-original-title="Ver"
                                   href="#"
                                   onclick="verPago(<?= $campoPagos['venta_id'] ?>); ">
                                    Ver
                                </a>
                            <?php }
                                ?>
                    </div>
                </td>
            </tr>
            <?php
        }
    }
    ?>

    </tbody>
</table>





<script type="text/javascript">
    TablesDatatables.init();
    function pagoCaja(id){
        $("#pagoCaja").load('<?= $ruta ?>venta/pagoCaja/' + id );
        $('#pagoCaja').modal('show');
    }
    function pagoBanco(id){
        $("#pagoCaja").load('<?= $ruta ?>venta/pagoBanco/' + id );
        $('#pagoCaja').modal('show');
    }

    function verPago(id){
        $("#verpago").load('<?= $ruta ?>venta/verPagoAdelantado/' + id );
        $('#verpago').modal('show');
    }
</script>

<style>

    #tabla_resumen_productos thead tr {
        border-top: 1px #000 dashed;
        border-bottom: 1px #000 dashed;
    }

    #tabla_resumen_productos thead tr th {
        border-top: 1px #000 dashed;
        border-bottom: 1px #000 dashed;
    }

    #tabla_resumen_productos tbody tr td {
        border-top: 0px #000 dashed;
        border-bottom: 0px #000 dashed;
        font-size: 85%;
    }

    #panel_documento {
        font-size: 90%;
    }

    #tabla_resumen_productos thead tr th {
        font-size: 85%;
    }

</style>
<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Resumen</h4>
        </div>
        <div class="modal-body">

            <div class="panel" class="text-center" id="panel_documento">
                <?php if (isset($resultado[0])) {
                    ?>
                    <div id="resumen_venta">
                        <div>
                            <div class="row ">
                                <div class="col-xs-12">
                                    <h3 class="text-center">
                                        <span
                                            id="titulo_emp"><?= $resultado[0]['razon_social'] ?></span>
                                    </h3>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-xs-12">
                                    <h4 class="text-center">
                                        <span
                                            id="titulo_emp"><?= $resultado[0]['direccion'] ?></span>
                                    </h4>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xs-12">
                                    <h5 class="text-center">
                                        Telf: <span
                                            id="titulo_emp"><?= $resultado[0]['telefono1'] ?></span>
                                    </h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="block-content-mini-padding">
                                    <div class="col-xs-3">
                                        Venta#:
                                    </div>
                                    <div class="col-xs-9">
                                        <?= $resultado[0]['documento_Serie'] . "-" . $resultado[0]['documento_Numero'] ?>
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="block-content-mini-padding">
                                    <div class="col-xs-1">
                                        Fecha:

                                    </div>
                                    <div class="col-xs-2">

                                        <?php

                                        echo date('d-m-y');
                                        ?>
                                    </div>
                                    <div class="col-xs-1">
                                        Hora:

                                    </div>
                                    <div class="col-xs-2">

                                        <?php

                                        echo date('H:i:s');
                                        ?>
                                    </div>


                                </div>
                            </div>

                            <div class="row">
                                <div class="block-content-mini-padding">
                                    <div class="col-xs-3">
                                        Vendedor:

                                    </div>
                                    <div class="col-xs-4">

                                        <?php echo $vendedor['nombre'];
                                        ?>
                                    </div>


                                </div>
                            </div>
                            <div class="row">
                                <div class="block-content-mini-padding">

                                    <div class="col-xs-3">
                                        Cajero:

                                    </div>
                                    <div class="col-xs-4">

                                        <?php echo $cajero['nombre'];
                                        ?>
                                    </div>


                                </div>
                            </div>
                            <!-- info row -->


                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                        <!-- Table row -->
                        <div>
                        </div>
                        <br>
                        <!-- END TABLA DE PRODUCTOS -->
                        <div>

                            <div class="col-xs-12 col-lg-12">
                                <table class="table" id="totales_">
                                    <!--<tr>
                                    <td>
                                        <strong>Subtotal</strong>
                                    </td>
                                    <td>
                                        $. <span id="subtotalR"><span id="totalR"><?php //echo $ventas[0]['subTotal']?></span>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <strong>IMPUESTO</strong>
                                    </td>
                                    <td>
                                        <!--<label id="igvR">12</label>%-->
                                    <?php //echo $ventas[0]['impuesto']?>
                                    <!--  </td>
                                  </tr>-->
                                    <tr style="border-bottom:0px #000 dashed">
                                        <td style="border-top: 1px #000 dashed">

                                            <strong>Total Caja</strong>
                                        </td>
                                        <td style="border-top: 1px #000 dashed">
                                            <?php echo MONEDA ?> <span
                                                id="totalR"><?php if ($resultado[0]['tipo_metodo'] != null and $resultado[0]['tipo_metodo'] == "CAJA") {
                                                    echo number_format($resultado[0]['suma'],2);
                                                } else {
                                                    if (isset($resultado[1]) and $resultado[1]['tipo_metodo'] != null and $resultado[1]['tipo_metodo'] == "CAJA") {
                                                        echo number_format($resultado[1]['suma'],2);
                                                    } else {
                                                        echo "0";
                                                    }
                                                }


                                                ?></span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom:0px #000 dashed">
                                        <td style="border-top: 0px #000 dashed">
                                            <strong>Total Banco</strong>
                                        </td>
                                        <td style="border-top: 0px #000 dashed">
                                            <?php echo MONEDA ?> <span id="totalR"><?php
                                                if (isset($resultado[1]) and $resultado[1]['tipo_metodo'] != null and $resultado[1]['tipo_metodo'] == "BANCO") {
                                                    echo number_format($resultado[1]['suma'],2);
                                                } else {
                                                    if ($resultado[0]['tipo_metodo'] != null and $resultado[0]['tipo_metodo'] == "BANCO") {
                                                        echo number_format($resultado[0]['suma'],2);
                                                    } else {
                                                        echo "0";
                                                    }

                                                }
                                                ?></span>
                                        </td>
                                    </tr>
                                    <tr style="border-bottom:0px #000 dashed">
                                        <td style="border-top: 0px #000 dashed">
                                            <strong>Total Banco + Total Caja</strong>
                                        </td>
                                        <td style="border-top: 0px #000 dashed">
                                            <?php echo MONEDA ?> <span id="totalR"><?php
                                                if (isset($resultado[1])) {
                                                    echo number_format($resultado[1]['suma'] + $resultado[0]['suma'],2);
                                                } else {
                                                    echo number_format($resultado[0]['suma'],2);
                                                }
                                                ?></span>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                            <div class="row text-center">
                            </div>
                        </div>

                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="modal-footer">
            <a href="#" class="btn btn-default" id="cerrar_visualizar" onclick="cerrar()">Cerrar</a>
            <a href="#" tabindex="0" type="button" id="imprimir" class="btn btn-primary"> <i
                    class="fa fa-print"></i>Imprimir</a>
        </div>
    </div>

</div>

<script src="<?php echo base_url() ?>recursos/js/printThis.js"></script>
<script>
    $(function () {


        $("#imprimir").click(function (e) {
            e.preventDefault();
            $("#resumen_venta").printThis({
                importCSS: true,
                loadCSS: "<?= base_url()?>recursos/css/carta.css"
            });
        });

        setTimeout(function () {
            $("#imprimir").focus();
        }, 500);
    })
</script>
<?php $ruta = base_url(); ?>
<!--<script src="<?php echo $ruta; ?>recursos/js/custom.js"></script>-->
    <div class="col-md-12 text-right">
        <label class="control-label badge label-success">D&iacute;as < 8</label>
        <label class="control-label badge label-warning">D&iacute;as < 31</label>
        <label class="control-label badge label-danger">D&iacute;as >= 31</label>
    </div>

<div class="table-responsive">
    <table class='table table-striped dataTable table-bordered no-footer table-condensed' id="lstPagP" name="lstPagP">
        <thead>
        <tr>
            <th>ID venta</th>
            <th>Documento</th>
            <th>Nro Venta</th>
            <th>Cliente</th>
            <th class='tip' title="Fecha Registro">Fecha de venta.</th>
            <th class='tip' title="Monto Credito Solicitado">Importe Venta <?php echo MONEDA ?></th>
            <th class='tip' title="Monto Cancelado">Monto Canc <?php echo MONEDA ?></th>
            <th class='tip' title="Monto Cancelado">Vendedor</th>

            <th>D&iacute;as de atraso</th>
            <th>Estado&nbsp;</th>
            <th>Accion</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</div>

<!-- Seccion Visualizar -->
<div class="modal fade" id="visualizar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="form_notacredito" id="form_notacredito" method="post" action="<?= $ruta ?>venta/guardar_notacredito">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Nota de Cr&eacute;dito</h4>
                </div>
                <div class="modal-body">
                    <h5><p>Est&aacute; seguro que desea registrar una nota de cr&eacute;dito para la venta numero:

                        <div id="abrir_venta"></div>
                        </p></h5>
                    <input type="hidden" name="id" id="id_venta">
                </div>
                <div class="modal-footer">
                    <button type="button" id="confirmar" class="btn btn-primary" onclick="guardar_notacredito()">
                        Confirmar
                    </button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>
<!--- ----------------- -->

<!-- Pagar Visualizar -->
<div class="modal fade" id="pagar_venta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>
<!--- ----------------- -->

<script type="text/javascript">

    var lst_venta = new Array();
    $(document).ready(function () {

        TablesDatatablesPagosPendientes.init('<?php echo base_url()?>venta/lst_reg_estadocuenta_json', 0, false, false);


    });

    function pagar_venta(id) {


        $.ajax({
            url: '<?= base_url()?>venta/vercredito',
            type: 'post',
            data: {'idventa': id},
            success: function (data) {

                $("#pagar_venta").html(data);
                $('#pagar_venta').modal('show');
            }

        })

    }

    function nota_credito(id, documento_serie, documento_numero) {
        var venta = {};
        venta.id_venta = id
        $("#mostrar_venta_numero").remove();
        $('#borrar').modal('show');


        $("#abrir_venta").append('<div id="mostrar_venta_numero"> ' + documento_serie + '-' + documento_numero + '?</div>');

    }

    function cerrar_visualizar() {

        $('#visualizarPago').modal('hide');
        $('#pagar_venta').modal('hide');
        buscar();
    }

    function visualizar(id) {

        $.ajax({
            url: '<?= base_url()?>venta/verVentaCredito',
            type: 'post',
            data: {'idventa': id},
            success: function (data) {

                $("#visualizar_venta").html(data);
                $('#visualizar_venta').modal('show');
            }

        })
    }

    function guardar_notacredito() {
        lst_venta.push(producto);

        var miJSON = JSON.stringify(lst_venta);

        $.ajax({
            type: 'POST',
            data: $('#form_notacredito').serialize() + '&lst_venta=' + miJSON,
            url: '<?=$ruta?>venta/guardar_notacredito',
            dataType: 'json',
            success: function (data) {


                if (data.success != 'undefined') {

                    $("#confirmarmodal").modal('hide');
                    var growlType = 'success';
                    $.bootstrapGrowl('<h4>' + data.success + '</h4>', {
                        type: growlType,
                        delay: 5000,
                        allow_dismiss: true
                    });

                }
                else {
                    var growlType = 'warning';
                    $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    })

                }

                $('#borrar').modal('hide');
                buscar();
            },
            error: function (data) {
                $('#borrar').modal('hide');


                var growlType = 'warning';
                $.bootstrapGrowl('<h4> Ha ocurrido un error al guardar la nota de credito</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

            }
        });
    }
</script>
<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered" id="tablaresultado">
        <thead>
        <tr>
            <th>Documento Original</th>
            <th>Referencia de Impresion</th>
            <th>Consolidado</th>
            <th>Pedido</th>
            <th>Acciones</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($documentos as $doc): ?>
            <tr>
                <td id="doc_text_<?= $doc->documento_fiscal_id ?>">
                    <?php $doc_letra = 'NE'; ?>
                    <?php if ($doc->documento_tipo == 'BOLETA DE VENTA') $doc_letra = 'BO'; ?>
                    <?php if ($doc->documento_tipo == 'FACTURA') $doc_letra = 'FA'; ?>

                    <?= $doc_letra . " " . $doc->documento_serie . "-" . $doc->documento_numero ?></td>
                <td><?= $doc->referencia ?></td>
                <td><?= $doc->consolidado_id ?></td>
                <td><?= $doc->venta_id ?></td>
                <td>
                    <a class="btn btn-sm btn-primary" data-toggle="tooltip"
                       title="Ver" data-original-title="fa fa-comment-o"
                       href="#"
                       onclick="show_form(<?= $doc->documento_fiscal_id ?>)">
                        Reasignar
                    </a>
                    <?php if ($doc->referencia != '' && $doc_letra == 'BO'): ?>
                        <button type="button" class="btn btn-sm btn-warning"
                                onclick="reimprimir_boleta(<?= $doc->documento_fiscal_id ?>);">
                            <i class="fa fa-print"></i>
                        </button>
                    <?php endif; ?>
                    <?php if ($doc->referencia != '' && $doc_letra == 'FA'): ?>
                        <button type="button" class="btn btn-sm btn-warning"
                                onclick="reimprimir_factura(<?= $doc->documento_fiscal_id ?>);">
                            <i class="fa fa-print"></i>
                        </button>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>


<div class="modal fade" id="reasignar_dialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Anular Ingreso</h4>
            </div>
            <div class="modal-body">
                <p>Reasigne el numero referencial del documento</p>
                <input type="hidden" name="id" id="df_id">

                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-5">
                        <label>Documento Original:</label>
                        <input type="text" id="doc_original" readonly class="form-control">
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-3"></div>
                    <div class="col-md-2">
                        <label>Serie:</label>
                        <input type="number" id="new_serie" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Numero:</label>
                        <input type="number" id="new_numero" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <input type="button" id="" class="btn btn-primary" value="Reasignar Numero" onclick="reasignarNumero();">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>

</div>

<script type="text/javascript">
    $(function () {
        TablesDatatables.init();
    });

    function show_form(df_id) {

        $("#doc_original").val($('#doc_text_' + df_id).html().trim());

        $.ajax({
            url: '<?php echo $ruta ?>venta/get_doc_next/' + df_id,
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            success: function (data) {
                $("#df_id").val(df_id);
                $("#new_serie").val(data.serie);
                $("#new_numero").val(data.numero);

                $("#reasignar_dialog").modal('show');
            },
            error: function () {
                alert('Ocurrio un error por favor intente nuevamente');
            }
        });
    }

    function reasignarNumero(){

        $.ajax({
            url: '<?php echo $ruta ?>venta/reasignar_numero',
            type: 'POST',
            data: {
                'df_id': $('#df_id').val(),
                'serie': $("#new_serie").val(),
                'numero': $('#new_numero').val()
            },
            success: function (data) {
                get_documentos();
                $("#reasignar_dialog").modal('hide');
            },
            error: function () {
                alert('Ocurrio un error por favor intente nuevamente');
            }
        });
    }

    function reimprimir_boleta(df_id){
        var win = window.open('<?= $ruta ?>venta/reimprimir_doc_boleta/' + df_id);
        win.focus();
    }

    function reimprimir_factura(df_id){
        var win = window.open('<?= $ruta ?>venta/reimprimir_doc_factura/' + df_id);
        win.focus();
    }


</script>

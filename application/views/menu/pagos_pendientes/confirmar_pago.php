<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Cobranzas</li>
    <li><a href="">Confirmar Cobranzas</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="box-body">
        <div class="row">
            <div class="col-md-2">
                <label class="control-label">Vendedor:</label>
            </div>
            <div class="col-md-3">
                <select id="vendedor_id" name="vendedor_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($vendedores as $vendedor): ?>
                        <option value="<?= $vendedor->nUsuCodigo ?>"><?= $vendedor->nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1">
            </div>

            <div class="col-md-2">
                <label class="control-label">Pendiente A:</label>
            </div>
            <div class="col-md-2">
                <select id="estado" name="estado" class="form-control">
                    <option value="1">Confirmar Pagos</option>
                    <option value="3">Confirmar Consolidados</option>
                    <option value="2">Liquidar Vendedor</option>
                </select>
            </div>


        </div>
    </div>

    <br>

    <div class="box-body">
        <div id="reporte_tabla" class="table-responsive">
            <?= isset($reporte_tabla) ? $reporte_tabla : '' ?>
        </div>
    </div>

</div>

<div class="modal fade" id="dialog_pagar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<script>

    $(document).ready(function () {
        $("#vendedor_id").on('change', function () {
            var url = '<?php echo base_url('pago_pendiente/confirmar_pago/filter')?>';
            if ($(this).val() != 0)
                url = '<?php echo base_url('pago_pendiente/confirmar_pago/filter')?>' + '/' + $("#vendedor_id").val();
            $.ajax({
                url: url,
                type: 'post',
                success: function (data) {
                    $("#reporte_tabla").html(data);
                }
            });
        });
    });
</script>


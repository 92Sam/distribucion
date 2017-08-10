<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Devoluciones</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="box-body">
        <div class="row">
            <label class="col-md-12 control-label" style="color: #ff9900;">Nota. Por el momento los
                pedidos que permiten
                devoluciones son los entregados y sin ningun monto liquidado.</label>


        </div>
        <div class="row">
            <label class="col-md-1 control-label panel-admin-text">Cliente:</label>
            <div class="col-md-3">

                <select id="cliente_id" name="cliente_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['razon_social'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-1"></div>

            <label class="col-md-2 control-label panel-admin-text">Nota de Entrega:</label>
            <div class="col-md-3">
                <input type="text" id="pedido_text" class="form-control">
            </div>

            <div class="col-md-1"></div>
            <div class="col-md-1">
                <button type="button" class="btn btn-default form-control btn_buscar">
                    <i class="fa fa-search"></i>
                </button>
            </div>
        </div>


    </div>

    <br>

    <div id="reporte_tabla" class="box-body">

        <?= isset($reporte_tabla) ? $reporte_tabla : '' ?>

    </div>

</div>

<div class="modal fade" id="detalle_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<script>

    $(document).ready(function () {

        $("select").chosen();

        $('.btn_buscar').on('click', function () {
            filter_cobranzas();
        });

        $("#cliente_id, #estado, #mes, #estado_ne").on('change', function () {
            $("#reporte_tabla").html('');
        });

        $("#year, #dia_min, #dia_max").bind('keyup change click', function () {
            $("#reporte_tabla").html('');
        });

    });

    function filter_cobranzas(id) {

        if (id != undefined) {
            var win = window.open('<?= $ruta ?>consolidadodecargas/imprimir_notas/' + id + '/VENTA', '_blank');
            win.focus();
        }

        $('#barloadermodal').modal('show');
        var data = {
            'cliente_id': $("#cliente_id").val(),
            'pedido': $("#pedido_text").val(),
        };

        $.ajax({
            url: '<?php echo base_url('venta/devolver/filter')?>',
            data: data,
            type: 'post',
            success: function (data) {
                $("#reporte_tabla").html(data);
            },
            complete: function (data) {
                $('#barloadermodal').modal('hide');
            }
        });
    }

</script>



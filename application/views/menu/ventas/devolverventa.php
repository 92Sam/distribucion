<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Devoluciones</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="box-body">
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

            <div class="col-md-2"></div>
            <div class="col-md-6" style="color: #ff9900;">
                Nota. Por el momento los pedidos que permiten devoluciones son los entregados y sin ningun monto liquidado.
            </div>
        </div>
        <br>
        <div class="row">
            <label class="col-md-1 control-label panel-admin-text">Periodo:</label>

            <div class="col-md-2">
                <input type="number" id="year" name="year" value="<?= date('Y') ?>" class="form-control">
            </div>

            <div class="col-md-2">

                <select
                        id="mes"
                        class="form-control filter-input" name="mes">
                    <option value="01" <?= date('m') == '01' ? 'selected' : '' ?>>Enero</option>
                    <option value="02" <?= date('m') == '02' ? 'selected' : '' ?>>Febrero</option>
                    <option value="03" <?= date('m') == '03' ? 'selected' : '' ?>>Marzo</option>
                    <option value="04" <?= date('m') == '04' ? 'selected' : '' ?>>Abril</option>
                    <option value="05" <?= date('m') == '05' ? 'selected' : '' ?>>Mayo</option>
                    <option value="06" <?= date('m') == '06' ? 'selected' : '' ?>>Junio</option>
                    <option value="07" <?= date('m') == '07' ? 'selected' : '' ?>>Julio</option>
                    <option value="08" <?= date('m') == '08' ? 'selected' : '' ?>>Agosto</option>
                    <option value="09" <?= date('m') == '09' ? 'selected' : '' ?>>Septiembre</option>
                    <option value="10" <?= date('m') == '10' ? 'selected' : '' ?>>Octubre</option>
                    <option value="11" <?= date('m') == '11' ? 'selected' : '' ?>>Noviembre</option>
                    <option value="12" <?= date('m') == '12' ? 'selected' : '' ?>>Diciembre</option>
                </select>
            </div>


            <div class="col-md-2">
                <label class="control-label panel-admin-text">Rango de Dias:</label>
            </div>
            <div class="col-md-1">
                <input type="number" min="1" id="dia_min" name="dia_min" value="<?= date('d') ?>" class="form-control">
            </div>

            <div class="col-md-1">
                <input type="number" min="1" id="dia_max" name="dia_max" value="<?= date('d') ?>" class="form-control">
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

    function filter_cobranzas() {
        $('#barloadermodal').modal('show');
        var data = {
            'cliente_id': $("#cliente_id").val(),
            'year': $("#year").val(),
            'mes': $("#mes").val(),
            'dia_min': $("#dia_min").val(),
            'dia_max': $("#dia_max").val()
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



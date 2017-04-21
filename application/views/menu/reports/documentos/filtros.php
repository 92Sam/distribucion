<div class="row">

    <div class="col-md-2">
        <label class="control-label panel-admin-text">Periodo:</label>
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
        <label class="control-label panel-admin-text" style="color: #FFFFFF;">_</label>
        <input type="number" id="year" name="year" value="<?= date('Y') ?>" class="form-control">
    </div>

    <div class="col-md-4">
        <label class="control-label panel-admin-text">Cliente:</label>
        <select id="cliente_id" name="cliente_id" class="form-control">
            <option value="0">Todos</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['razon_social'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label class="control-label panel-admin-text">Estado:</label>
        <select id="estado" name="estado" class="form-control">
            <option value="0">Todos</option>
            <option value="1">Cancelados</option>
            <option value="2">Pendientes</option>
        </select>

    </div>

    <div class="col-md-1"></div>
    <div class="col-md-1">
        <label class="control-label panel-admin-text" style="color: #FFFFFF;">_</label>
        <button type="button" class="btn btn-default form-control btn_buscar">
            <i class="fa fa-search"></i>
        </button>
    </div>


</div>

<script>

    $(document).ready(function () {

        $("select").chosen();

        $('.btn_buscar').on('click', function () {
            filter_cobranzas();
        });

    });

    function filter_cobranzas() {
        var data = {
            'fecha_ini': $("#fecha_ini").val(),
            'fecha_fin': $("#fecha_fin").val(),
        };

        $.ajax({
            url: '<?php echo base_url('reporte/nota_entrega/filter')?>',
            data: data,
            type: 'post',
            success: function (data) {
                $("#reporte_tabla").html(data);
            }
        });
    }

</script>
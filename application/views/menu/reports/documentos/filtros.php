<div class="row">
    <label class="col-md-2 control-label panel-admin-text">Cliente:</label>
    <div class="col-md-4">

        <select id="cliente_id" name="cliente_id" class="form-control">
            <option value="0">Todos</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['razon_social'] ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <label class="col-md-2 control-label panel-admin-text">Estado:</label>
    <div class="col-md-2">

        <select id="estado" name="estado" class="form-control">
            <option value="0">Todos</option>
            <option value="1">Cancelados</option>
            <option value="2">Pendientes</option>
        </select>

    </div>


</div>
<br>
<div class="row">
    <label class="col-md-2 control-label panel-admin-text">Periodo:</label>
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
        <input type="number" id="year" name="year" value="<?= date('Y') ?>" class="form-control">
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

<script>

    $(document).ready(function () {

        $("select").chosen();

        $('.btn_buscar').on('click', function () {
            filter_cobranzas();
        });

        $("#cliente_id, #estado, #mes").on('change', function(){
            $("#reporte_tabla").html('');
        });

        $("#year, #dia_min, #dia_max").bind('keyup change click', function(){
            $("#reporte_tabla").html('');
        });

    });

    function filter_cobranzas() {
        var data = {
            'cliente_id': $("#cliente_id").val(),
            'estado': $("#estado").val(),
            'year': $("#year").val(),
            'mes': $("#mes").val(),
            'dia_min': $("#dia_min").val(),
            'dia_max': $("#dia_max").val()
        };

        console.log(data)

        $.ajax({
            url: '<?php echo base_url('reporte/documentos/filter')?>',
            data: data,
            type: 'post',
            success: function (data) {
                $("#reporte_tabla").html(data);
            }
        });
    }

</script>
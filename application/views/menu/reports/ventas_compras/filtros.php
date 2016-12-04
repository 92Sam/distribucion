<form id="form_filter">
    <div class="row">
        <div class="col-md-2">
            <label class="control-label" style="padding-top: 8px;">A&ntilde;o del Resumen:</label>
        </div>
        <div class="col-md-2">
            <input id="year" type="number" maxlength="4" class="form-control" value="<?= date('Y') ?>">
        </div>


        <div class="col-md-4">
        </div>
        <div class="col-md-1">
            <button type="button" class="btn btn-default form-control btn_buscar">
                <i class="fa fa-search"></i>
            </button>
        </div>
    </div>
</form>

<script>




    $(document).ready(function () {



        $('.btn_buscar').on('click', function () {
            filter_cobranzas();
        });








    });

    function filter_cobranzas() {
        var data = {
            'year': $("#year").val()
        };
        
        $.ajax({
            url: '<?php echo base_url('reporte_general/ventas_compras/filter')?>',
            data: data,
            type: 'post',
            success: function (data) {
                $("#reporte_tabla").html(data);
            }
        });
    }




</script>
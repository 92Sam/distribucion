<?php $ruta = base_url(); ?>
<style>
    .tcharm {
        background-color: #fff;
        border: 1px solid #dae8e7;
        width: 300px;
        padding: 0 20px;
        overflow-y: auto;
    }

    .tcharm-header {
        text-align: center;
    }

    .tcharm-body .row {
        margin: 20px 3px;
    }

    .tcharm-close {
        text-decoration: none !important;
        color: #333333;
        padding: 3px;
        border: 1px solid #fff;
        float: left;
    }

    .tcharm-close:hover {
        background-color: #dae8e7;
        color: #333333;
    }
</style>
<form id="form_filter">
<div id="charm" class="tcharm">
    <div class="tcharm-header">

        <h3><a href="#" class="fa fa-arrow-right tcharm-close"></a> <span>Filtros Avanzados</span></h3>
    </div>

    <div class="tcharm-body">

        <div class="row">
            <div class="col-md-4" style="text-align: center;">
                <button type="button" class="btn btn-default btn_buscar">
                    <i class="fa fa-search"></i>
                </button>
            </div>
            <div class="col-md-4" style="text-align: center;">
                <button id="btn_filter_reset" type="button" class="btn btn-warning">
                    <i class="fa fa-refresh"></i>
                </button>
            </div>
            <div class="col-md-4" style="text-align: center;">
                <button type="button" class="btn btn-danger tcharm-trigger">
                    <i class="fa fa-remove"></i>
                </button>
            </div>

        </div>

        <div class="row">
            <label class="control-label">Locales:</label>
            <select id="local_id" name="local_id" class="form-control">
                <?php foreach ($locales as $local):?>
                    <option value="<?=$local['int_local_id']?>"><?=$local['local_nombre']?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="row">
            <label class="control-label">Marcas:</label>
            <select id="marca_id" name="marca_id" class="form-control">
            <option value="0">Todos</option>
                <?php foreach ($marcas as $marca):?>
                    <option value="<?=$marca->id_marca?>"><?=$marca->nombre_marca?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="row">
            <label class="control-label">Grupos:</label>
            <select id="grupo_id" name="grupo_id" class="form-control">
            <option value="0">Todos</option>
                <?php foreach ($grupos as $grupo):?>
                    <option value="<?=$grupo->id_grupo?>"><?=$grupo->nombre_grupo?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="row">
            <label class="control-label">Lineas:</label>
            <select id="linea_id" name="linea_id" class="form-control">
            <option value="0">Todos</option>
                <?php foreach ($lineas as $linea):?>
                    <option value="<?=$linea->id_subgrupo?>"><?=$linea->nombre_subgrupo?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="row">
            <label class="control-label">Sub Linea:</label>
            <select id="familia_id" name="familia_id" class="form-control">
            <option value="0">Todos</option>
                <?php foreach ($familias as $familia):?>
                    <option value="<?=$familia->id_familia?>"><?=$familia->nombre_familia?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="row">
            <label class="control-label">Sub Familias:</label>
            <select id="subfamilia_id" name="subfamilia_id" class="form-control">
            <option value="0">Todos</option>
                <?php foreach ($subfamilias as $subfamilia):?>
                    <option value="<?=$subfamilia->id_subfamilia?>"><?=$subfamilia->nombre_subfamilia?></option>
                <?php endforeach;?>
            </select>
        </div>

        <div class="row">
            <label class="control-label">Talla:</label>
            <select id="talla_id" name="talla_id" class="form-control">
            <option value="0">Todos</option>
                <?php foreach ($tallas as $talla):?>
                    <option value="<?=$talla->id_linea?>"><?=$talla->nombre_linea?></option>
                <?php endforeach;?>
            </select>
        </div>

    </div>
</div>
</form>

<ul class="breadcrumb breadcrumb-top">
    <li>Inventario</li>
    <li><a href="">Stock</a></li>
</ul>


<div class="row">
    <div class="col-md-10">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>


</div>

<div class="block">

    <div class="row">


        <div class="col-md-1">
            <a class="btn btn-info" onclick="unidadesycostos();">
                <i class="fa fa-list-ol"> UM y Precios</i>
            </a>
        </div>

        <div class="col-md-9"></div>

        <div class="col-md-1">
            <button type="button" class="btn btn-primary tcharm-trigger form-control">
                <i class="fa fa-plus"></i>
            </button>
        </div>

    </div>
    <br>


    <div class="table-responsive" id="productostable" >

    </div>

    <a href="#" id="exportar_pdf" class="btn  btn-danger btn-lg" data-toggle="tooltip" title="Exportar a PDF"
       data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>

    <a href="<?= $ruta ?>producto/excelStock" class="btn btn-default btn-lg" data-toggle="tooltip"
       title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
            class="fa fa-file-excel-o fa-fw"></i></a>
</div>


<div class="modal fade" id="productomodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">


</div>

<div class="modal fade" id="columnas" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">


</div>



<script type="text/javascript">

    function unidadesycostos() {
        var id = $("#tbody tr.ui-selected td:first").html();
        if(id!=undefined) {
            $("#productomodal").html($('#loading').html());
            $("#productomodal").load('<?= $ruta ?>producto/verunidades/' + id);
            $('#productomodal').modal('show');
        }
    }


    function filter_cobranzas() {
        $("#charm").tcharm('hide');
        var data = {
            'local_id': $("#local_id").val(),
            'marca_id': $("#marca_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val(),
            'subfamilia_id': $("#subfamilia_id").val(),
            'talla_id': $("#talla_id").val(),
        };

        $.ajax({
            url: '<?php echo base_url('producto/stock/filter')?>',
            data: data,
            type: 'post',
            success: function (data) {
                $("#productostable").html(data);
            }
        });
    }

    function exportar_pdf() {
        var data = {
            'local_id': $("#local_id").val(),
            'marca_id': $("#marca_id").val(),
            'grupo_id': $("#grupo_id").val(),
            'linea_id': $("#linea_id").val(),
            'familia_id': $("#familia_id").val(),
            'subfamilia_id': $("#subfamilia_id").val(),
            'talla_id': $("#talla_id").val(),
        };

        var win = window.open('<?= base_url()?>producto/stock/pdf?data=' + JSON.stringify(data), '_blank');
        win.focus();
    }



</script>

<!-- Load and execute javascript code used only in this page -->


<script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>
<script>$(function () {

filter_cobranzas();

    $("#charm").tcharm({
        'position': 'right',
        'display': false,
        'top': '50px'
    });

    $('.btn_buscar').on('click', function () {
        filter_cobranzas();
    });

    $("#btn_filter_reset").on('click', function () {
        $("#marca_id").val('0').trigger('chosen:updated');
        $("#grupo_id").val('0').trigger('chosen:updated');
        $("#linea_id").val('0').trigger('chosen:updated');
        $("#familia_id").val('0').trigger('chosen:updated');
        $("#subfamilia_id").val('0').trigger('chosen:updated');
        $("#talla_id").val('0').trigger('chosen:updated');
        filter_cobranzas();
    });


        $("#exportar_pdf").on('click', function () {
            exportar_pdf();
        });



    });</script>

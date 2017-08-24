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
                <label class="control-label">Marca:</label>
                <select id="marca_id" name="marca_id" class="form-control select-chosen">
                    <option value="0">Todos</option>
                    <?php foreach ($marcas as $marca): ?>
                        <option value="<?= $marca->id_marca ?>"><?= $marca->nombre_marca ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Grupo:</label>
                <select id="grupo_id" name="grupo_id" class="form-control select-chosen">
                    <option value="0">Todos</option>
                    <?php foreach ($grupos as $grupo): ?>
                        <option value="<?= $grupo->id_grupo ?>"><?= $grupo->nombre_grupo ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Linea:</label>
                <select id="linea_id" name="linea_id" class="form-control select-chosen">
                    <option value="0">Todos</option>
                    <?php foreach ($lineas as $linea): ?>
                        <option value="<?= $linea->id_subgrupo ?>"><?= $linea->nombre_subgrupo ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Sublinea:</label>
                <select id="sublinea_id" name="sublinea_id" class="form-control select-chosen">
                    <option value="0">Todos</option>
                    <?php foreach ($sublineas as $sublinea): ?>
                        <option value="<?= $sublinea->id_familia ?>"><?= $sublinea->nombre_familia ?></option>
                    <?php endforeach; ?>
                </select>
            </div>


            <div class="row">
                <label class="control-label">Tipo de Documento:</label>
                <select id="tipo_documento" class="form-control">
                    <option value="0">Todos</option>
                    <option value="FACTURA">FACTURA</option>
                    <option value="BOLETA DE VENTA">BOLETA DE VENTA</option>
                </select>
            </div>


            <div class="row">
                <label class="control-label">Estado:</label>
                <select id="estado" class="form-control">
                    <option value="0">Todos</option>
                    <option value="ENTREGADO" selected>ENTREGADO</option>
                    <option value="DEVUELTO">DEVUELTO</option>
                    <option value="ANULADO">ANULADO</option>
                </select>
            </div>

        </div>
    </div>

    <div class="row">

        <div class="col-md-5">
            <label class="control-label">Productos:</label>
            <select id="producto_id" name="producto_id" class="form-control select-chosen">
                <option value="0">Todos</option>
                <?php foreach ($productos as $producto): ?>
                    <option value="<?= $producto->producto_id ?>"><?= sumCod($producto->producto_id, 4) ?>
                        - <?= $producto->producto_nombre.' - '.$producto->presentacion ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-1">

        </div>
        <div class="col-md-2">
            <label class="control-label">Fecha Inicial:</label>
            <input id="fecha_ini" type="text" class="form-control input-datepicker" value="<?= date('d-m-Y') ?>"
                   style="cursor: pointer;" readonly>
        </div>


        <div class="col-md-2">
            <label class="control-label">Fecha Final:</label>
            <input id="fecha_fin" type="text" class="form-control input-datepicker" value="<?= date('d-m-Y') ?>"
                   style="cursor: pointer;" readonly>
        </div>


        <div class="col-md-1">
            <br>
            <button type="button" class="btn btn-default form-control btn_buscar">
                <i class="fa fa-search"></i>
            </button>
        </div>
        <div class="col-md-1">
            <br>
            <button type="button" class="btn btn-primary tcharm-trigger form-control">
                <i class="fa fa-plus"></i>
            </button>
        </div>
    </div>
</form>
<script src="<?= base_url('recursos/js/tcharm.js') ?>"></script>

<script>


    $(document).ready(function () {

        $(".select-chosen").chosen({
            search_contains: true
        });

        $("#charm").tcharm({
            'position': 'right',
            'display': false,
            'top': '50px'
        });

        add_checkbox_events();

        $('.btn_buscar').on('click', function () {
            filter_cobranzas();
        });

        $("#incluir_fecha").on('change', function () {
            filter_cobranzas();
        });

        $("#mostrar_detalles").on('change', function () {
            if ($(this).prop('checked'))
                $('.tabla_detalles').show();
            else
                $('.tabla_detalles').hide();
        });

        $("#btn_filter_reset").on('click', function () {
            $('#producto_id').val('0').trigger('chosen:updated');
            $('#producto_id').change();
            $('#grupo_id').val('0').trigger('chosen:updated');
            $('#grupo_id').change();
            $('#marca_id').val('0').trigger('chosen:updated');
            $('#marca_id').change();
            $('#linea_id').val('0').trigger('chosen:updated');
            $('#linea_id').change();
            $('#sublinea_id').val('0').trigger('chosen:updated');
            $('#sublinea_id').change();
            $('#tipo_documento').val('0');
            $('#estado').val('ENTREGADO');
            filter_cobranzas();
        });
    });

    function filter_cobranzas() {
        $('#barloadermodal').modal('show');
        $("#charm").tcharm('hide');
        $("#reporte_tabla").html("");
        var data = {
            'fecha_ini': $("#fecha_ini").val(),
            'fecha_fin': $("#fecha_fin").val(),
            'producto_id': $("#producto_id").val(),
            'proveedor_id': $("#proveedor_id").val(),
            'tipo_documento': $("#tipo_documento").val(),
            'estado': $("#estado").val(),
            'grupo_id': $("#grupo_id").val(),
            'marca_id': $("#marca_id").val(),
            'linea_id': $("#linea_id").val(),
            'sublinea_id': $("#sublinea_id").val(),
        };

        $.ajax({
            url: '<?php echo base_url('reporte/por_productos/filter')?>',
            data: data,
            type: 'post',
            success: function (data) {
                $("#reporte_tabla").html(data);
            },
            complete: function () {
                $('#barloadermodal').modal('hide');
            }
        });
    }

    function add_checkbox_events() {
        $("#zonas_all").on('change', function () {
            if ($("#zonas_all").prop('checked') == true) {
                $('.zona_check').prop('checked', 'checked');
            }
            else {
                $('.zona_check').removeAttr('checked');
            }
            $('.zona_check').trigger('change');
        });


        $('.zona_check').on('change', function () {
            $('#cliente_id').html('<option value="0">Todos</option>');
            var n = 0;
            $('.zona_check').each(function () {
                if ($(this).prop('checked')) {
                    n++;
                    select_cliente_by_zona($(this).val());
                }
            });

            $("#cliente_id").val('0').trigger('chosen:updated');


            if (n == $('.zona_check').length)
                $('#zonas_all').prop('checked', 'checked');
            else
                $('#zonas_all').removeAttr('checked');
        });
    }

    function select_cliente_by_zona(zona_id) {

        for (var i = 0; i < clientes.length; i++) {
            if (clientes[i].zona_id == zona_id && (clientes[i].vendedor_id == $("#vendedor_id").val() || $("#vendedor_id").val() == 0)) {
                $('#cliente_id').append(add_cliente_template(clientes[i]));
            }
        }
    }

    function add_zona_template(zona) {
        var template = '<label style="cursor: pointer;">';
        template += '<input class="zona_check" type="checkbox" value="' + zona.id + '" checked> ' + zona.nombre;
        template += '</label><br>';
        return template;
    }

    function add_cliente_template(cliente) {
        return '<option value="' + cliente.id + '">' + cliente.nombre + '</option>';
    }

</script>
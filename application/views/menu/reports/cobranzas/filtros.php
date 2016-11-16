<style>
    .tcharm {
        background-color: #fff;
        border: 1px solid #dae8e7;
        width: 300px;
        padding: 0 20px;
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
                    <button type="button" class="btn btn-default btn_filter_save">
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
                <label class="control-label">Vendedor:</label>
                <select id="vendedor_id" name="vendedor_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($vendedores as $vendedor): ?>
                        <option value="<?= $vendedor->nUsuCodigo ?>"><?= $vendedor->nombre ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label" style="cursor: pointer;">
                    <input id="zonas_all" type="checkbox"> Zonas del Vendedor:
                </label><br>
                <div id="zonas_content"
                     style="width: 100%; height: 100px; border: 1px solid #dae8e7; overflow-y: scroll;">
                    <?php foreach ($zonas as $zona): ?>
                        <label style="cursor: pointer;">
                            <input type="checkbox" value="<?= $zona['zona_id'] ?>"> <?= $zona['zona_nombre'] ?>
                        </label><br>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="row">
                <label class="control-label">Cliente:</label>
                <select id="cliente_id" name="cliente_id" class="form-control">
                    <option value="0">Todos</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?= $cliente['id_cliente'] ?>"><?= $cliente['razon_social'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Dias de Atraso:</label>
                <select class="form-control">
                    <option value="0">Todos</option>
                    <option value="1">Menor que 7 Dias</option>
                    <option value="2">Entre 8 y 5 Dias</option>
                    <option value="3">Entre 16 y 30 Dias</option>
                    <option value="4">Mayor que 30 Dias</option>
                </select>
            </div>

            <div class="row">
                <label class="control-label">Deudas:</label>
                <br>
                <div class="col-md-6">
                    <select class="form-control">
                        <option value="1">Mayor</option>
                        <option value="2">Menor</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <input type="number" class="form-control" value="0">
                </div>

            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <label class="control-label">Tipo de Fecha:</label>
            <select class="form-control">
                <option value="0">Venta</option>
                <option value="0">Documento</option>
            </select>
        </div>

        <div class="col-md-2">
            <label class="control-label">Desde:</label>
            <input type="text" class="form-control" value="<?= date('d/m/Y') ?>" style="cursor: pointer;" readonly>
        </div>

        <div class="col-md-2">
            <label class="control-label">Hasta:</label>
            <input type="text" class="form-control" value="<?= date('d/m/Y') ?>" style="cursor: pointer;" readonly>
        </div>

        <div class="col-md-3"></div>

        <div class="col-md-1">
            <br>
            <button type="button" class="btn btn-default form-control">
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

    //aki hago una cochinada y guardo todos los datos que traigo para ya no hacer consultas adicionales
    //ver como hacer esto con json
    var vendedores = [];
    var vendedor_zonas = [];
    var zonas = [];
    var clientes = [];

    <?php foreach ($vendedores as $vendedor): ?>
    vendedores.push({
        'id': '<?=$vendedor->nUsuCodigo?>',
        'nombre': '<?=$vendedor->nombre?>',
    });
    <?php endforeach; ?>

    <?php foreach ($vendedor_zonas as $vendedor_zona): ?>
    vendedor_zonas.push({
        'vendedor_id': '<?=$vendedor_zona->id_usuario?>',
        'zona_id': '<?=$vendedor_zona->id_zona?>',
    });
    <?php endforeach; ?>

    <?php foreach ($zonas as $zona): ?>
    zonas.push({
        'id': '<?=$zona["zona_id"]?>',
        'nombre': '<?=$zona["zona_nombre"]?>',
    });
    <?php endforeach; ?>

    <?php foreach ($clientes as $cliente): ?>
    clientes.push({
        'id': '<?=$cliente["id_cliente"]?>',
        'nombre': '<?=$cliente["razon_social"]?>',
        'zona_id': '<?=$cliente["id_zona"]?>',
        'vendedor_id': '<?=$cliente["vendedor_a"]?>',
    });
    <?php endforeach; ?>

    console.log(vendedor_zonas);


    $(document).ready(function () {
        $("#charm").tcharm({
            'position': 'right',
            'display': false,
            'top': '50px'
        });

        $("#vendedor_id").on('change', function () {
            var vendedor_id = $(this).val();
            var zonas_content = $('#zonas_content');
            var cliente_id = $('#cliente_id');

            zonas_content.html('');
            cliente_id.html('<option value="0">Todos</option>');

            if (vendedor_id != 0) {
                for (var i = 0; i < vendedor_zonas.length; i++) {
                    if (vendedor_zonas[i].vendedor_id == vendedor_id) {
                        for (var j = 0; j < zonas.length; j++) {
                            if (zonas[j].id == vendedor_zonas[i].zona_id) {
                                zonas_content.append(add_zona_template(zonas[j]));
                            }
                        }
                    }
                }
                for (var i = 0; i < clientes.length; i++) {
                    if (clientes[i].vendedor_id == vendedor_id) {
                        cliente_id.append(add_cliente_template(clientes[i]));
                    }
                }
            }
            else {
                for (var i = 0; i < zonas.length; i++) {
                    zonas_content.append(add_zona_template(zonas[i]));
                }

                for (var i = 0; i < clientes.length; i++) {
                    cliente_id.append(add_cliente_template(clientes[i]));
                }
            }
        });
    });


    function add_zona_template(zona) {
        var template = '<label style="cursor: pointer;">';
        template += '<input type="checkbox" value="' + zona.id + '"> ' + zona.nombre;
        template += '</label><br>';
        return template;
    }

    function add_cliente_template(cliente) {
        return '<option value="' + cliente.id + '">' + cliente.nombre + '</option>';
    }

</script>
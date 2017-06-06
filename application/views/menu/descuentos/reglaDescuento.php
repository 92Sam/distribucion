<?php $ruta = base_url(); ?>
<style>
    .edit-precio {
        width: 50px !important;
        text-align: right;
        border: 1px solid #cecece;
    }

    .btn-file {
        position: relative;
        overflow: hidden;
    }

    .btn-file input[type=file] {
        position: absolute;
        top: 0;
        right: 0;
        min-width: 100%;
        min-height: 100%;
        font-size: 100px;
        text-align: right;
        filter: alpha(opacity=0);
        opacity: 0;
        outline: none;
        background: white;
        cursor: inherit;
        display: block;
    }
</style>
<div class="modal-dialog" style="width: 70%;">
    <div class="modal-content">

        <div class="modal-header">

            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            Regla de descuento: <span id="pt"></span> productos de <?= $total_productos->total ?>
        </div>
        <?php foreach ($escalas_h as $id) {
            echo '<input type="hidden" id="desID" value="' . $id['descuento_id'] . '" />';
        }
        ?>
        <div class="modal-body">
            <!--<div class="row">
                <div class="col-md-1">
                    <span>ID</span>
                </div>
                <div class="col-md-2">
                    <input type="text" name="id_des" id="id" class="form-control inputB"/>
                </div>
                <div class="col-md-2">
                    <span>Nombre</span>
                </div>
                <div class="col-md-3">
                    <input type="text" id="nombre" name="nombre_des" class="form-control inputB"/>
                </div>
            </div>
            <br/>-->
            <div id="result">
                <div class="row">
                    <div class="col-md-3">
                        <label class="btn btn-default">
                            Subir Precios <input type="file" id="file" name="file">
                        </label>
                    </div>
                    <div class="col-md-1">
                        <button id="importar" class="btn btn-default">Importar</button>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-1">
                        <input type="button" class="btn btn-primary" id="exportar" value="Exportar">
                    </div>
                    <div class="col-md-6 text-right">
                        <label class="control-label label-info" style="color: #ffffff;">Nota: Guarda automaticamente los
                            precios.</label>
                    </div>
                </div>
                <div class="row">
                    <div id="msg" class="col-md-12">

                    </div>
                </div>
                <br/>
                <div class="row">
                    <div class="col-sm-12 col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-striped dataTable table-bordered" id="table_detalle">
                                <thead>
                                <tr>
                                    <th>C&oacute;digo</th>
                                    <th>Producto</th>
                                    <?php
                                    foreach ($escalas_h as $escala) {

                                        ?>

                                        <th><?= $escala['cantidad_minima'] ?>--<?= $escala['cantidad_maxima'] ?></th>
                                        <?php
                                    }
                                    ?>


                                </tr>
                                </thead>
                                <tbody>
                                <?php

                                $array_destino = array();
                                $valor = array();

                                foreach ($escalas as $escala) {

                                    $valor['nombre'] = $escala['producto_nombre'];
                                    $valor['id'] = $escala['producto_id'];
                                    if (!in_array($valor, $array_destino)) {
                                        $array_destino[] = $valor;
                                    }
                                }
                                foreach ($array_destino as $valor) : ?>
                                    <tr>
                                        <td><?= sumCod($valor['id']); ?></td>

                                        <td><?= $valor['nombre']; ?></td>
                                        <?php foreach ($escalas as $escala): ?>
                                            <?php if ($valor['nombre'] == $escala['producto_nombre']): ?>
                                                <td style="text-align: center;">
                                                    <input type="text" class="edit-precio"
                                                           value="<?= $escala['precio'] ?>"
                                                           data-unidad="<?= $escala['unidad'] ?>"
                                                           data-escala="<?= $escala['escala'] ?>"
                                                           data-producto="<?= $escala['producto'] ?>">

                                                </td>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>


                            <a href="<?= $ruta ?>descuentos/pdfExport/<?php echo $id_desc; ?>/"
                               id="generarpdf"
                               class="btn btn-default btn-lg" data-toggle="tooltip"
                               title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i
                                        class="fa fa-file-pdf-o fa-fw"></i></a>
                            <a href="<?= $ruta ?>descuentos/excelExport/<?php echo $id_desc; ?>/"
                               id="generarexcel"
                               class="btn btn-default btn-lg" data-toggle="tooltip"
                               title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i
                                        class="fa fa-file-excel-o fa-fw"></i></a>
                        </div>

                    </div>
                </div>
            </div>


        </div>
    </div>
    <input type="hidden" id="total_productos" value="<?= count($array_destino) ?>">
    <script src="<?php echo $ruta ?>recursos/js/pages/widgetsStats.js"></script>
    <script src="<?php echo $ruta ?>recursos/js/jquery.flot.categories.js"></script>

    <script>
        $(document).ready(function () {

            $('#importar').on('click', function () {
                var file_data = $('#file').prop('files')[0];
                var form_data = new FormData();
                form_data.append('file', file_data);
                $('#barloadermodal').modal('show');
                $.ajax({
                    url: '<?=$ruta?>descuentos/importar_precio',
                    dataType: 'text',
                    cache: false,
                    contentType: false,
                    processData: false,
                    data: form_data,
                    type: 'post',
                    success: function (response) {
                        var type = 'warning';
                        if (response == 'OK') {
                            response = 'Importacion realizada con exito.';
                            type = 'success';

                            $("#verModal").load('<?= $ruta ?>descuentos/verReglaDescuento/' + <?=$id_desc?>, function () {

                                $('#barloadermodal').modal('hide');
                                $.bootstrapGrowl(response, {type: type, delay: 2500, allow_dismiss: true});

                            });
                        }
                        else{
                            $('#barloadermodal').modal('hide');
                            $.bootstrapGrowl(response, {type: type, delay: 2500, allow_dismiss: true});
                        }

                    },
                    error: function (response) {
                        $('#barloadermodal').modal('hide');
                        $.bootstrapGrowl('Error inesperado.', {type: 'danger', delay: 2500, allow_dismiss: true});
                    }
                });
            });

            DT_detalles_descuento.init('table_detalle');

            $(".edit-precio").on('focus', function (e) {
                var self = this;
                $('.edit-precio').each(function () {
                    var precio = parseFloat($(this).val());

                    if (isNaN(precio) || precio < 0) {
                        $(this).css('border-color', '#ff0000');
                    }
                    else {
                        $(this).css('border-color', '#cecece');
                    }
                });
                setTimeout(function () {
                    $(self).select();
                }, 100)

            });

            $('.edit-precio').on('keyup', function () {

                var self = this;
                save_precio(self);

            });

            $("#exportar").on('click', function () {

                var precios = [];
                $('.edit-precio').each(function () {
                    var self = $(this);
                    var precio = parseFloat(self.val());

                    if (isNaN(precio) || precio < 0)
                        return false;

                    precios.push({
                        escala: self.attr('data-escala'),
                        producto: self.attr('data-producto'),
                        unidad: self.attr('data-unidad'),
                        precio: precio
                    });
                });

                if (precios.length == 0)
                    return false;

                precios = JSON.stringify(precios);
                download('precios.json', precios);


            });

            $("#pt").html($("#total_productos").val());

        });

        function download(filename, json) {
            var pom = document.createElement('a');
            pom.setAttribute('href', 'data: json/plain; charset=utf-8,' + encodeURIComponent(json));
            pom.setAttribute('download', filename);

            if (document.createEvent) {
                var event = document.createEvent('MouseEvents');
                event.initEvent('click', true, true);
                pom.dispatchEvent(event);
            }
            else {
                pom.click();
            }
        }

        function save_precio(self) {
            $(self).css('border-color', '#eea236');
            var precio = parseFloat($(self).val());

            if (isNaN(precio)) {
                $(self).css('border-color', '#ff0000');
                return;
            }

            if (precio < 0) {
                $(self).css('border-color', '#ff0000');
                return;
            }

            var params = {
                escala: $(self).attr('data-escala'),
                producto: $(self).attr('data-producto'),
                unidad: $(self).attr('data-unidad'),
                precio: precio
            };

            $.ajax({
                url: '<?=$ruta?>descuentos/save_precio',
                type: 'POST',
                headers: {
                    Accept: 'application/json'
                },
                data: params,
                success: function (data) {
                    $('.edit-precio').each(function () {
                        var precio = parseFloat($(this).val());

                        if (isNaN(precio) || precio < 0) {
                            $(this).css('border-color', '#ff0000');
                        }
                        else {
                            $(this).css('border-color', '#cecece');
                        }
                    });
                },
                complete: function (data) {

                },
                error: function (data) {

                }
            });
        }
    </script>

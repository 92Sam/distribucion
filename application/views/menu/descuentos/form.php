<?php $ruta = base_url(); ?>

<form name="formaagregar" style="margin-top: 3%" id="formaagregar">

    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Descuento</h4>
            </div>

            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-1">
                            Nombre
                        </div>
                        <div class="col-md-11">
                            <input type="text" name="nombre" id="nombre" required="true" class="form-control"
                                   value="<?php if (isset($descuentos['nombre'])) echo $descuentos['nombre']; ?>">
                        </div>

                        <input type="hidden" name="id_de_descuento" id="" required="true"
                               value="<?php if (isset($descuentos['descuento_id'])) echo $descuentos['descuento_id']; ?>">
                    </div>
                </div>

                <br>
                <h4 class="text-warning bold">Escalas: Por favor Selecione el rango de unidades</h4>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="col-md-3">
                                Desde
                            </div>
                            <div class="col-md-10">
                                <input type="number" id="desde" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">
                            <div class="col-md-3">
                                Hasta
                            </div>
                            <div class="col-md-10">
                                <input type="number" id="hasta" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <br>

                        <div class="col-md-4">
                            <a id="listar" class="btn btn-primary" data-placement="bottom"
                               style="margin-top:-2%;cursor: pointer;"
                               onclick="accionEscalas();">Agregar</a>
                        </div>
                        <!--div class="col-md-4">
                             <a id="listarTodos" class="btn btn-primary" data-placement="bottom"
                               style="margin-top:-2%;cursor: pointer;"
                               onclick="listarTodos();">Agregar Todos</a>
                        </div>

                        <div class="col-md-4">
                             <a id="quitarTodos" class="btn btn-danger" data-placement="bottom"
                               style="margin-top:-2%;cursor: pointer;"
                               onclick="del_listaTodo();">Quitar Todos</a>
                        </div> -->
                    </div>
                </div>

                <br>
                <h4 class="text-warning bold">Producto: Por favor Selecione los productos</h4>

                <div class="row">
                    <div class="col-md-5">
                        <div class="form-group">

                            <div class="col-md-10">
                                <select name="cboProducto" id="cboProducto" class='cho form-control'
                                        required="true">
                                    <option value="">Seleccione</option>
                                    <?php

                                    if (count($lstProducto) > 0): ?>
                                        <?php foreach ($lstProducto as $pd) {

                                            if (count($productosenreglasdedescuento) > 0) {
                                                $paso = false;
                                                foreach ($productosenreglasdedescuento as $row) {
                                                    if ($row['producto_id'] == $pd['producto_id']) {

                                                        $paso = true;

                                                    }
                                                }
                                                if ($paso == false) { ?>

                                                    <option
                                                        value="<?php echo $pd['producto_id']; ?>">
                                                        <?php echo sumCod($pd['producto_id']) . " - " . $pd['producto_nombre']; ?></option>

                                                    <?php
                                                }

                                            } else { ?>
                                                <option
                                                    value="<?php echo $pd['producto_id']; ?>"><?php echo sumCod($pd['producto_id']) . " - " . $pd['producto_nombre']; ?></option>


                                            <?php }
                                        } ?>
                                    <?php else : ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="form-group">

                            <div class="col-md-10">
                                <select name="unidades" id="unidades" class='cho form-control'>


                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="col-md-4">
                            <a id="listar" class="btn btn-primary" data-placement="bottom"
                               style="margin-top:-2%;cursor: pointer;"
                               onclick="accionProductos();">Agregar</a>
                        </div>

                    </div>
                </div>

                <br>

                <div id="result">
                    <div class="row">
                        <div class="col-sm-12 col-lg-12">
                            <div class="table-responsive">
                                <table class="table table-striped dataTable table-bordered" id="example">
                                    <thead>
                                    <tr>
                                        <th>C&oacute;digo</th>
                                        <th>Producto</th>
                                        <th>Unidad</th>
                                        <th>Precio</th>

                                        <?php
                                        foreach($escalas_h as $escala){
                                            ?>
                                            <th><?= $escala['cantidad_minima'] ?>--<?= $escala['cantidad_maxima'] ?></th>
                                            <?php
                                        }
                                        ?>


                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php

                                    $array_destino=array();
                                    $valor=array();

                                    foreach($escalas as $escala) {

                                        $valor['id'] = $escala['producto_id'];
                                        $valor['nombre'] = $escala['producto_nombre'];
                                        $valor['unidad'] = $escala['nombre_unidad'];
                                        $valor['preciov'] = $escala['preciov'];

                                        if (!in_array($valor,$array_destino)){
                                            $array_destino[] = $valor;
                                        }
                                    }
                                    foreach ($array_destino as $valor) {
                                        ?>
                                        <tr>
                                            <td><?= sumCod($valor['id']); ?></td>

                                            <td><?= $valor['nombre']; ?></td>

                                            <td><?= $valor['unidad']; ?></td>

                                            <td><?= $valor['preciov']; ?></td>

                                            <?php
                                            foreach($escalas as $escala) {
                                                if ($valor['id'] == $escala['producto_id']) { ?>
                                                    <td><input type="number" name="precio[]" min="0"
                                                                           id="precio<?= $escala["precio"] ?>"
                                                            value="<?= $escala["precio"] ?>"
                                                            class="pr form-control"/></td>

                                                <?php }
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                    }
                                    ?>

                                    </tbody>
                                </table>
                            </div>

                        </div>
                    </div>
                </div>

                <a href="<?= $ruta?>venta/pdfReporteDescuentos/"
                   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
                <a href="<?= $ruta?>venta/excelReporteDescuntos/"
                   class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

            </div>
            <div class="modal-footer">
                <div class="form-actions">

                    <button class="btn btn-primary" id="btnGuardar" type="button">Confirmar
                    </button>
                    <!-- <button type="button" class="btn"><i class="fa fa-folder-open-o fa-3x text-info"></i><br>Abrir </button>-->
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
        </div>
</form>

<div class="modal fade" id="confirmarmodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmar</h4>
            </div>
            <div class="modal-body">
                <p>Est&aacute; seguro que desea registrar el ingreso de los productos seleccionados?</p>
                <input type="hidden" name="id" id="id_borrar">

            </div>
            <div class="modal-footer">
                <button type="button" id="botonconfirmar" class="btn btn-primary" onclick="guardardescuento();">
                    Confirmar
                </button>
                <button type="button" id="cerrar" class="btn btn-default">Cancelar</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>

</div>






<script type="text/javascript">

    var countescalas = 0;
    var countproductos = 0;

    $(document).ready(function () {

        $('#cboProducto').chosen({
            placeholder: "Seleccione el producto",
            allowClear: true,
            width: '100%'
        });
        $("#unidades").chosen({
                placeholder: "Seleccione una unidad",
                allowClear: true,
                width: '100%'
            }
        );


        $("#btnGuardar").click(function () {
            guardardescuento();
        });

        $('#cboProducto').on("change", function () {

            $.ajax({
                url: '<?=$ruta?>descuentos/get_unidades_has_producto',
                type: 'POST',
                headers: {
                    Accept: 'application/json'
                },
                data: {'id_producto': $(this).val()},
                success: function (data) {

                    var options = '';
                    for (var i = 0; i < data.unidades.length; i++) {
                        options += '<option  value="'
                            + data.unidades[i].id_unidad
                            + '">'
                            + data.unidades[i].nombre_unidad
                            + '</option>';

                        // console.info(data.unidades[i]);
                    }

                    $("#unidades")
                        .html(
                            '<option value="">Seleccione</option>');

                    $("#unidades")
                        .append(options);

                    $("#unidades").trigger("chosen:updated");


                }
            })
        });


        $("#cancelar").on('click', function (data) {

            $.ajax({
                url: ruta + 'principal',
                success: function (data) {
                    $('#page-content').html(data);
                }

            })

        });


        lst_producto = new Array();
        lst_escalas = new Array();
        lst_precio = new Array();
        lst_producto_con_precio = new Array();


        arreglo_precios = new Array();
        countescalas = 0;
        countproductos = 0;
        countprecio = 0;

        $("#cerrar").on('click', function () {
            $("#confirmarmodal").modal('hide');
        });


        <?php

        if(isset($descuentos) and count($descuentos)>0)
        {

            $contador_precios=0;



            for($i=0;$i<$sizeescalas;$i++)
            {
                ?>


        var escala = {};
        escala.id = '<?php echo  $escalas[$i]['escala_id']; ?>';
        escala.desde = '<?php echo  $escalas[$i]['cantidad_minima']; ?>';
        escala.hasta = '<?php echo  $escalas[$i]['cantidad_maxima']; ?>';
        escala.contador = '<?php echo $i+1; ?>';
        countescalas++;

        <?php



            for($p=0;$p<$sizenoagrupados;$p++)
            {




                if($productosnoagrupados[$p]["escala"]==$escalas[$i]["escala_id"])
                {

                   if($i==0){
                    ?>
        var producto = {};
        producto.Codigo = '<?php echo sumCod($productosnoagrupados[$p]['producto_id']); ?>';
        producto.unidad = '<?php echo $productosnoagrupados[$p]['unidad']; ?>';
        producto.Productor = '<?php echo  $productosnoagrupados[$p]['producto_nombre']; ?>';
        producto.unidad_nombre = '<?php echo  $productosnoagrupados[$p]['nombre_unidad']; ?>';
        producto.contador = '<?php echo $p+1; ?>';
        countproductos++;
        lst_producto.push(producto);
        <?php

        }
        ?>
        var producto_con_precio = {};

        producto_con_precio.Codigo = '<?php echo sumCod($productosnoagrupados[$p]['producto_id']); ?>';
        producto_con_precio.escala_id = '<?php echo $escalas[$i]['escala_id']; ?>';
        producto_con_precio.precio = '<?php echo  $productosnoagrupados[$p]['precio']; ?>';
        producto_con_precio.id_escala_html = '<?php echo $i+1; ?>';
        lst_producto_con_precio.push(producto_con_precio);


        <?php

  }

}

?>
        lst_escalas.push(escala);
        <?php
        }
    }

    ?>


    });

</script>
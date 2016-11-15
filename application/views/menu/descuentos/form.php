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
                        <br>

                        <div class="col-md-4">
                            <a id="listar" class="btn btn-primary" data-placement="bottom"
                               style="margin-top:-2%;cursor: pointer;"
                               onclick="accionProductos();">Agregar</a>
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

                <div class="row-fluid">
                    <div class="span12">
                        <div id="box" class="box">

                            <div id="lstEscalas">
                                <?php

                                if (isset($escalas) and count($escalas) > 0) {
                                    $contador_escala = 1;
                                    $contador_precios = 1;
                                    foreach ($escalas as $escala) {
                                        ?>
                                        <div class="escalita table table-striped dataTable table-condensed table-bordered dataTable-noheader
         table-has-pover dataTable-nosort" id="escalita<?php echo $contador_escala; ?>"> Escala
                                            del <?php echo $escala['cantidad_minima']; ?>
                                            hasta <?php echo $escala['cantidad_maxima']; ?>
                                            <div class="btn-group">
                                                <a class="btn btn-default btn-mini btn-default" data-toggle="tooltip"
                                                   title="Eliminar"
                                                   data-original-title="Eliminar"
                                                   onclick="quitarEscala(<?php echo $contador_escala; ?>)">
                                                    <i class="fa fa-trash-o"></i></a></div>
                                            <div class="lstTabla" id="lstTabla<?php echo $contador_escala; ?>">

                                                <table id="productos<?php echo $contador_escala; ?>" class="copiar table table-striped dataTable table-condensed
            table-bordered dataTable-noheader table-has-pover dataTable-nosort" data-nosort="0">
                                                    <thead>
                                                    <tr>
                                                        <th>Codigo</th>
                                                        <th>Producto</th>
                                                        <th>Unidad</th>
                                                        <th>Precio</th>
                                                        <th>Action</th>
                                                    </tr>
                                                    </thead>
                                                    <tbody class="agrega"
                                                           id="tbodyproductos<?php echo $contador_escala; ?>">
                                                    <?php
                                                    $contador_precios = 1;


                                                    for ($p = 0; $p < count($productosnoagrupados); $p++) {

                                                        if ($productosnoagrupados[$p]["escala"] == $escala["escala_id"]
                                                        ) {

                                                            ?>


                                                            <tr id="tr<?= $contador_escala ?><?= $productosnoagrupados[$p]["producto_id"] ?>">
                                                                <td style="text-align: center;"><?= sumCod($productosnoagrupados[$p]["producto_id"]) ?>
                                                                </td>
                                                                <td><?= $productosnoagrupados[$p]['producto_nombre'] ?>
                                                                </td>
                                                                <td><?= $productosnoagrupados[$p]['nombre_unidad'] ?>
                                                                </td>
                                                                <td><input type="number" name="precio[]" min="0"
                                                                           id="precio<?= $contador_escala ?><?= sumCod($productosnoagrupados[$p]["producto_id"]) ?>"
                                                                           value="<?= $productosnoagrupados[$p]["precio"] ?>"
                                                                           class="pr form-control"/>
                                                                </td>
                                                                <td class="actions">
                                                                    <div class="btn-group"><a
                                                                            class="btn btn-default btn-default btn-default"
                                                                            data-toggle="tooltip"
                                                                            title="Eliminar"
                                                                            data-original-title="Eliminar"
                                                                            onclick="del_listaProducto(<?= $contador_precios ?>,<?= $productosnoagrupados[$p]["producto_id"] ?>)">
                                                                            <i class="fa fa-trash-o"></i></a>
                                                                    </div>
                                                                </td>
                                                            </tr>


                                                            <?php

                                                        }

                                                        $contador_precios++;
                                                    }


                                                    echo " </tbody>";
                                                    echo "</table>";
                                                    $contador_escala++;
                                                    ?>

                                            </div>

                                        </div>


                                    <?php }


                                }

                                ?>

                            </div>


                            <div>

                                <div class="box-content box-nomargin">
                                    <div id="lstTabla">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <br>
                </div>
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
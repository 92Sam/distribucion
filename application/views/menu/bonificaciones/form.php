<?php $ruta = base_url(); ?>


<form name="formagregar" action="<?= base_url() ?>bonificaciones/guardar" method="post" id="formagregar">

    <input type="hidden" name="id" id="" required="true"
           value="<?php if (isset($bonificaciones['id_bonificacion'])) echo $bonificaciones['id_bonificacion']; ?>">


    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Nueva Bonificación</h4>
            </div>
            <div class="modal-body">

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Fecha de Vencimiento</label>
                        </div>
                        <div class="col-md-4">
                            <input type="text" name="fecha_bonificacion" id="fecha_bonificacion" required="true"
                                   class="input-small input-datepicker form-control"
                                   value="<?php if (isset($bonificaciones['fecha'])) echo date('d-m-Y',strtotime($bonificaciones['fecha'])); ?>"/>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">
                            <label>Productos Condición</label>
                        </div>
                        <div class="col-md-4">


                            <select name="producto_condicion[]" id="producto_condicion" class='form-control selectpicker' multiple="true" >


                                <?php foreach ($productos as $producto_condicion) { ?>


                                    <?php if ($bonificaciones_has_producto != null) {
                                        $cantidad = count($bonificaciones_has_producto);
                                        $i = 1;
                                        foreach ($bonificaciones_has_producto as $usz) {
                                            if (isset($usz['id_producto']) and $usz['id_producto'] == $producto_condicion['producto_id']) {
                                                ?>
                                                <option
                                                    value="<?php echo $producto_condicion['producto_id'] ?>"
                                                    selected><?= sumCod($producto_condicion['producto_id'])." - ".$producto_condicion['producto_nombre'] ?></option>
                                                <?php break;
                                            }
                                            if ($cantidad == $i) {
                                                ?>
                                                <option
                                                    value="<?php echo $producto_condicion['producto_id'] ?>"><?= sumCod($producto_condicion['producto_id'])." - ".$producto_condicion['producto_nombre'] ?></option>
                                                <?php
                                            } else {
                                                $i++;

                                            }
                                        }
                                    } else { ?>
                                        <option
                                            value="<?php echo $producto_condicion['producto_id'] ?>"><?= sumCod($producto_condicion['producto_id'])." - ".$producto_condicion['producto_nombre'] ?></option>
                                    <?php };

                                } ?>

                            </select>


                        </div>

                        <div class="col-md-2">
                            <label>Bono Producto</label>
                        </div>
                        <div class="col-md-4">

                            <select name="bono_producto" id="bono_producto" required="true"
                                    class="cho form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($bonoproducto as $bono_producto): ?>
                                    <option
                                        value="<?php echo $bono_producto['producto_id'] ?>" <?php if (isset($bonificaciones['bono_producto']) and $bonificaciones['bono_producto'] == $bono_producto['producto_id']) echo 'selected' ?>><?= sumCod($bono_producto['producto_id'])." - ".$bono_producto['producto_nombre'] ?></option>
                                <?php endforeach ?>
                            </select>

                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Unidad Condición</label>
                        </div>
                        <div class="col-md-4">

                            <select name="unidad_condicion" id="unidad_condicion" class="cho form-control">

                                <?php foreach ($unidades as $unidad): ?>
                                    <option
                                        value="<?php echo $unidad['id_unidad'] ?>" <?php if (isset($bonificaciones['id_unidad']) and $bonificaciones['id_unidad'] == $unidad['id_unidad']) echo 'selected' ?>><?= $unidad['nombre_unidad'] ?></option>
                                <?php endforeach; ?>    

                            </select>

                        </div>

                        <div class="col-md-2">
                            <label>Bono Unidad</label>
                        </div>
                        <div class="col-md-4">
                            <select name="bono_unidad" id="bono_unidad" required="true"
                                    class="cho form-control">
                                <?php foreach ($unidades_bono as $unidad): ?>
                                    <option
                                        value="<?php echo $unidad['id_unidad'] ?>" <?php if (isset($bonificaciones['bono_unidad']) and $bonificaciones['bono_unidad'] == $unidad['id_unidad']) echo 'selected' ?>><?= $unidad['nombre_unidad'] ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Marca Condición</label>
                        </div>
                        <div class="col-md-4">

                            <select name="marca_condicion" id="marca_condicion" class="cho form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($marcas as $marca_condicion): ?>
                                    <option
                                        value="<?php echo $marca_condicion['id_marca'] ?>" <?php if (isset($bonificaciones['id_marca']) and $bonificaciones['id_marca'] == $marca_condicion['id_marca']) echo 'selected' ?>><?= $marca_condicion['nombre_marca'] ?></option>
                                <?php endforeach ?>
                            </select>

                        </div>
                        <div class="col-md-2">
                            <label>Cantidad Bono</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="bono_cantidad" id="bono_cantidad" required="true"
                                   class="form-control"
                                   value="<?php if (isset($bonificaciones['bono_cantidad'])) echo $bonificaciones['bono_cantidad']; ?>">
                        </div>

                    </div>
                </div>
                <div class="row">


                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Grupo Condición</label>
                        </div>
                        <div class="col-md-4">

                            <select name="grupo_condicion" id="grupo_condicion" class="cho form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($grupos as $grupo_condicion): ?>
                                    <option
                                        value="<?php echo $grupo_condicion['id_grupo'] ?>" <?php if (isset($bonificaciones['id_grupo']) and $bonificaciones['id_grupo'] == $grupo_condicion['id_grupo']) echo 'selected' ?>><?= $grupo_condicion['nombre_grupo'] ?></option>
                                <?php endforeach ?>
                            </select>

                        </div>



                    </div>
                </div> <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Sub Grupos</label>
                        </div>
                        <div class="col-md-4">

                            <select name="subgrupos" id="subgrupos" class="cho form-control">
                                <option value="">Seleccione</option>
                                <?php if(count($subgrupos)>0){ foreach ($subgrupos as $subgrupo): ?>
                                    <option
                                        value="<?php echo $subgrupo['id_subgrupo'] ?>" <?php if (isset($bonificaciones['subgrupo_id']) and $bonificaciones['subgrupo_id'] == $subgrupo['id_subgrupo']) echo 'selected' ?>>
                                        <?= $subgrupo['nombre_subgrupo'] ?></option>
                                <?php endforeach; } ?>
                            </select>

                        </div>



                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Familia Condición</label>
                        </div>
                        <div class="col-md-4">

                            <select name="familia_condicion" id="familia_condicion" class="cho form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($familias as $familia_condicion): ?>
                                    <option
                                        value="<?php echo $familia_condicion['id_familia'] ?>" <?php if (isset($bonificaciones['id_familia']) and $bonificaciones['id_familia'] == $familia_condicion['id_familia']) echo 'selected' ?>><?= $familia_condicion['nombre_familia'] ?></option>
                                <?php endforeach ?>
                            </select>

                        </div>

                    </div>
                </div> <div class="row">
                    <div class="form-group">

                        <div class="col-md-2">
                            <label>Sub Familia</label>
                        </div>
                        <div class="col-md-4">

                            <select name="subfamilia" id="subfamilia" class="cho form-control">
                                <option value="">Seleccione</option>
                                <?php if(count($subfamilias)>0){ foreach ($subfamilias as $subfamilia): ?>
                                    <option
                                        value="<?php echo $subfamilia['id_subfamilia'] ?>" <?php if (isset($bonificaciones['subfamilia_id']) and $bonificaciones['subfamilia_id'] == $subfamilia['id_subfamilia']) echo 'selected' ?>>
                                        <?= $subfamilia['nombre_subfamilia'] ?></option>
                                <?php endforeach; } ?>
                            </select>

                        </div>

                    </div>
                </div>



                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Línea Condición</label>
                        </div>
                        <div class="col-md-4">

                            <select name="linea_condicion" id="linea_condicion" class="cho form-control">
                                <option value="">Seleccione</option>
                                <?php foreach ($lineas as $linea_condicion): ?>
                                    <option
                                        value="<?php echo $linea_condicion['id_linea'] ?>" <?php if (isset($bonificaciones['id_linea']) and $bonificaciones['id_linea'] == $linea_condicion['id_linea']) echo 'selected' ?>><?= $linea_condicion['nombre_linea'] ?></option>
                                <?php endforeach ?>
                            </select>

                        </div>


                    </div>
                </div>
                <div class="row">
                    <div class="form-group">
                        <div class="col-md-2">
                            <label>Cantidad Condición</label>
                        </div>
                        <div class="col-md-4">
                            <input type="number" name="cantidad_condicion" id="cantidad_condicion" required="true"
                                   class="form-control"
                                   value="<?php if (isset($bonificaciones['cantidad_condicion'])) echo $bonificaciones['cantidad_condicion']; ?>">
                        </div>

                    </div>
                </div>


            </div>
            <input type="hidden" id="today" name="today"/>

            <div class="modal-footer">
                <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()">
                    Confirmar
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar
                </button>

            </div>
            <!-- /.modal-content -->
        </div>
    </div>
</form>

<script>
    function unidadesencomun(){
        $.ajax({
            url: '<?=$ruta?>bonificaciones/get_unidades_en_comun',
            type: 'POST',
            headers: {
                Accept: 'application/json'
            },
            data: {'id_producto': $("#producto_condicion").val()},
            success: function (data) {

                var options = '';
                for (var i = 0; i < data.unidades.length; i++) {

                    var unidadselected="<?php echo isset($bonificaciones['id_unidad'])?$bonificaciones['id_unidad']:''?>";
                    console.log(unidadselected);
                    options += '<option value="'
                        + data.unidades[i].id_unidad
                        + '"';

                    if(unidadselected!=''){
                        options+=' selected ';
                    }
                    options+= '>'+data.unidades[i].nombre_unidad
                        + '</option>';
                    // console.info(data.unidades[i]);
                }

                $("#unidad_condicion")
                    .html(
                        '<option value="">Seleccione</option>');

                $("#unidad_condicion")
                    .append(options);

                $("#unidad_condicion").trigger("chosen:updated");

            }
        })

    }

    $(document).ready(function () {

        //$("select[id!='producto_condicion']").chosen({width: "100%"});
        $("select[id!='producto_condicion']").chosen({width: "100%"});
        $('#producto_condicion').selectpicker({

            liveSearch: true
        });

        $("#fecha_bonificacion").datepicker("option", "minDate", new Date());

        $("#producto_condicion").on("change", function () {

           unidadesencomun();
        });

        $("#bono_producto").on("change", function () {

            $.ajax({
                url: '<?=$ruta?>bonificaciones/get_unidades_has_producto',
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

                    $("#bono_unidad")
                        .html(
                        '<option value="">Seleccione</option>');

                    $("#bono_unidad")
                        .append(options);

                    $("#bono_unidad").trigger("chosen:updated");

                }
            })

        });

        unidadesencomun();
    });

</script>
 
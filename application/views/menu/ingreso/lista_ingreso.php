<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered" id="tablaresultado">
        <thead>
        <tr>
            <th>ID Ingreso</th>
            <th>Tipo de Documento</th>
            <th>Nro Documento</th>
            <th>Fecha Registro</th>
            <th>Status</th>
            <th>Proveedor</th>
            <th>Responsable</th>
            <th>Local</th>
            <th>Total</th>

            <th>Acciones</th>


        </tr>
        </thead>
        <tbody>
        <?php if (count($ingresos) > 0) {

            foreach ($ingresos as $ingreso) {
                ?>
                <tr>
                    <td><?php echo $ingreso->id_ingreso ?></td>
                    <td><?= $ingreso->tipo_documento ?></td>
                    <td><?php echo $ingreso->documento_serie . "-" . $ingreso->documento_numero ?></td>
                    <td><?= date('d-m-Y H:i:s', strtotime($ingreso->fecha_registro)) ?></td>
                    <td><label
                            class="label <?php if ($ingreso->ingreso_status == INGRESO_COMPLETADO) {
                                echo 'label-success';
                            } elseif ($ingreso->ingreso_status == INGRESO_PENDIENTE) {
                                echo 'label-danger';
                            } else {
                                echo 'label-warning';
                            } ?>">
                            <?= $ingreso->ingreso_status ?></label>

                    </td>
                    <td><?= $ingreso->proveedor_nombre ?></td>
                    <td><?= $ingreso->nombre ?></td>
                    <td><?= $ingreso->local_nombre ?></td>
                    <td><?= $ingreso->total_ingreso ?></td>
                    <td>
                        <div class="btn-group">
                            <?php

                            echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Ver" data-original-title="Ver"
                                            href="#" onclick="ver(' . $ingreso->id_ingreso . ',' . $ingreso->local_id . ');">'; ?>
                            <i class="fa fa-search"></i>
                            </a>

                            <?php
                            if(isset($anular) && $ingreso->ingreso_status !=INGRESO_DEVUELTO ){
                                echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Anular" data-original-title="Anular"
                                            href="#" onclick="mostrar(' . $ingreso->id_ingreso . ','.$ingreso->local_id.');">'; ?>
                            <i class="fa fa-remove"></i>
                            </a>
                            <?php } ?>

                            <?php
                            if ($ingreso->ingreso_status == INGRESO_PENDIENTE) {
                                echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Registrar costos" data-original-title="Registrar costos"
                                            href="#" onclick="editaringreso(' . $ingreso->id_ingreso . ');">'; ?>
                            <i class="fa fa-money"></i>
                            </a>
                            <?php } ?>
                        </div>
                    </td>


                </tr>
            <?php }
        } ?>

        </tbody>
    </table>

</div>

<a href="<?= $ruta ?>ingresos/pdf/<?php if (isset($local_id)) echo $local_id; else echo 0; ?>/<?php if (isset($fecha_desde)) echo $fecha_desde; else echo 0; ?>/<?php if (isset($fecha_hasta)) echo $fecha_hasta; else echo 0; ?>/0"
   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF"
   data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta ?>ingresos/excel/<?php if (isset($local_id)) echo $local_id; else echo 0; ?>/<?php if (isset($fecha_desde)) echo $fecha_desde; else echo 0; ?>/<?php if (isset($fecha_hasta)) echo $fecha_hasta; else echo 0; ?>/0"
   class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel"
   data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

<div class="modal fade" id="ver" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<div class="modal fade" id="borrar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <form name="formeliminar" method="post" action="<?= $ruta ?>grupo/eliminar">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title">Anular Ingreso</h4>
                </div>
                <div class="modal-body">
                    <p>Est&aacute; seguro que desea anular el ingreso seleccionado?</p>
                    <input type="hidden" name="id" id="id_ingreso">
                    <input type="hidden" name="nombre" id="local_id">

                    <div class="row">
                        <div class="col-md-3">
                            <label>Fecha:</label>
                            <input type="text" id="anular_fecha" readonly class="form-control fecha_anular">
                        </div>
                        <div class="col-md-3"></div>
                        <div class="col-md-2">
                            <label>Serie:</label>
                            <input type="text" id="anular_serie" class="form-control">
                        </div>
                        <div class="col-md-3">
                            <label>Numero:</label>
                            <input type="text" id="anular_numero" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="button" id="" class="btn btn-primary" value="Confirmar" onclick="anular()">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                </div>
            </div>
            <!-- /.modal-content -->
        </div>

</div>

<div class="modal fade" id="ingresomodal" style="width: 85%; overflow: auto;
  margin: auto;" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><i class="fa fa-close"></i>
            </button>

            <h3>Editar Ingreso</h3>
        </div>
        <div class="modal-body" id="ingresomodalbody">

        </div>

    </div>

</div>

<script type="text/javascript">
    $(function () {

        TablesDatatables.init();
        $(".fecha_anular").datepicker({
            format: 'dd-mm-yyyy'
        });
    });

    function ver(id, local) {


        $("#ver").load('<?= base_url()?>ingresos/form/' + id + '/' + local);
        $('#ver').modal('show');

    }
    function mostrar(id, local) {

        $('#borrar').modal('show');
        $("#id_ingreso").attr('value', id);
        $("#local_id").attr('value', local);

        $("#anular_serie").val('');
        $("#anular_numero").val('');
        $("#anular_fecha").val('');
    }

    function editaringreso(id) {

        $.ajax({
            url: '<?php echo base_url()?>ingresos',
            data: {'idingreso': id, 'editar': 1, 'costos': 'true'},
            type: 'post',
            success: function (data) {
                $("#ingresomodalbody").html(data);
            }
        })
        $("#ingresomodal").modal('show');

    }


    function anular() {
        var id = $("#id_ingreso").val();
        var local = $("#local_id").val();
        var serie = $("#anular_serie").val();
        var numero = $("#anular_numero").val();
        var fecha = $("#anular_fecha").val();

        if(serie == "" || numero == "" || fecha == ""){
            $.bootstrapGrowl('<h4>Datos Incompletos!</h4>', {
                type: 'warning',
                delay: 2500,
                allow_dismiss: true
            });
            return false;
        }

        $("#barloadermodal").modal({
            show: true,
            backdrop: 'static'
        });


        $.ajax({
            url: '<?= base_url()?>ingresos/anular_ingreso',
            data: {
                'id': id,
                'local': local,
                'serie': serie,
                'numero': numero,
                'fecha': fecha
            },
            type: 'POST',
            'dataType': 'json',
            success: function (data) {
                $('#barloadermodal').modal('hide');

                if (data.error == undefined) {


                    $("#borrar").modal('hide');
                    var growlType = 'success';
                    $.bootstrapGrowl('<h4>Ingreso Anulado!</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    })

                    anularfunction.ajaxgrupo().success(function (data2) {
                        $('#page-content').html(data2);

                    })
                } else {

                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>' + data.error + '</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);
                }
            },
            error: function () {
                $('#barloadermodal').modal('hide');
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Ha ocurrido un error</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
        });


    }
</script>

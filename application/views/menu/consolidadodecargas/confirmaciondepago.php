<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Confirma entrega de dinero</li>
    <li><a href="">Confirma entrega de dinero</a></li>
</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
?>
<div class="block">
    <div class="form-group row">
        <div class="col-xs-1">
            Estatus
        </div>
        <div class="col-md-2">

            <select id="estado" name="estado" class="form-control">
                <option value="">Seleccione</option>

                <option selected  value="CERRADO">CERRADO</option>
                <option  value="CONFIRMADO">CONFIRMADO</option>

            </select>

        </div>
    </div>
<div class="block">
    <!-- Progress Bars Wizard Title -->

    <div class="table-responsive" id="tablaresultado">


    </div>

    </div>
</div>
<script type="text/javascript">

    function infoCobro(consolidado_id, status,tipo) {
        // tipo es para saber si la acccion es ver o confirmar

        $("#consolidadoinfoCobro").load('<?= $ruta ?>consolidadodecargas/infoCobroConslidado/'+consolidado_id+'/'+status+'/'+tipo);
        $('#consolidadoinfoCobro').modal('show');

    }
    function validar_confirmacion(){
        var validar_marcado=false   // esta verifica si hay algun check de confirmar marcado

            $('.confirmar').each( function(e){

            var verificar=true   ///esta verifica si los campos estan vacios los input
            if($(this).prop('checked')){
                validar_marcado=true
                var id=$(this).attr('id')

                if($("#inputcaja" + id).val()==""){

                    $("#inputcaja" + id).focus()
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>No puede dejar el campo vacio</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });
                      verificar=false;
                }

                if($("#inputbanco" + id).val()==""){
                    $("#inputbanco" + id).focus()
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>No puede dejar el campo vacio</h4>', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    verificar=false;
                }else{

                    if($("#inputbanco" + id).val()>0) {
                        if ($("#select_bancos" + id).val() == "") {
                            $("#select_bancos" + id).focus()
                            var growlType = 'warning';

                            $.bootstrapGrowl('<h4>Debe seleccionar un banco</h4>', {
                                type: growlType,
                                delay: 2500,
                                allow_dismiss: true
                            });

                            verificar = false;

                        }
                    }
                }
                if(verificar==false){
                    e.preventDefault();
                }
            }

        })

        if(validar_marcado==false){
            var growlType = 'warning';

            $.bootstrapGrowl('<h4>Debe seleccionar al menos uno</h4>', {
                type: growlType,
                delay: 2500,
                allow_dismiss: true
            });

            return false
            e.preventDefault();

        }


        $("#confirmacion").modal('show')


    }
        function cerrar_confirmacion(){

            $("#boton_aceptar").addClass('disabled');
            $('.confirmar').each( function(){

                $(this).attr('checked', 'checked')
            })

            $.ajax({
                url: '<?= base_url()?>consolidadodecargas/cerrar_confirmacion',
                data: $('#form_confirmar').serialize(),
                type: 'POST',
                dataType: "json",
                success: function (data) {
                    $("#boton_aceptar").removeClass('disabled');
                    if(data.error==undefined) {


                        $('#confirmacion').modal('hide');
                        $('#consolidadoinfoCobro').modal('hide');

                        var growlType = 'success';

                        $.bootstrapGrowl('<h4>la confirmacion se ha procesado exitosamente</h4>', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);
                        refreshajax();
                    }else{
                        var growlType = 'warning';

                        $.bootstrapGrowl('<h4>Error AJ02</h4> Ha ocurrido un error al cerrar la confirmacion Por favor intente nuevamente', {
                            type: growlType,
                            delay: 2500,
                            allow_dismiss: true
                        });

                        $(this).prop('disabled', true);
                    }

                },
                error: function () {
                    $("#boton_aceptar").removeClass('disabled');
                    var growlType = 'warning';

                    $.bootstrapGrowl('<h4>Error AJ01</h4>Ha ocurrido un error al cerrar la confirmacion Por favor intente nuevamente', {
                        type: growlType,
                        delay: 2500,
                        allow_dismiss: true
                    });

                    $(this).prop('disabled', true);
                }
            })
        }

</script>
<div class="modal fade" id="consolidadoinfoCobro" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="width: 100%"
     aria-hidden="true">


</div>

<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Confirmaci&oacute;n</h4>
            </div>

            <div class="modal-body">Est&aacute; seguro que desea continuar?</div>
            <div class="modal-footer">
                <button type="button" id="boton_aceptar" class="btn btn-primary" onclick="cerrar_confirmacion()">Si</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">No</button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>

</div>


<script>$(function () {
        refreshajax();
            //busacodr estado
            $("#estado").on("change", function () {

              refreshajax();

            });


            TablesDatatables.init();
    });

function refreshajax(){
    var estado = $("#estado").val();


    $.ajax({
        url: '<?= base_url()?>consolidadodecargas/buscarConsolidadoEstado',
        data: {
            'estado': estado

        },
        type: 'POST',
        success: function (data) {

            if (data.length > 0)
                $("#tablaresultado").html(data);
            // $("#tablaresultado").dataTable();
        },
        error: function () {

            alert('Ocurrio un error por favor intente nuevamente');
        }
    })
}

</script>
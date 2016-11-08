<?php $ruta = base_url(); ?>
<!-- Load and execute javascript code used only in this page -->


<script type="text/javascript">


</script>


<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Anular Ventas</a></li>
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
<!-- END Datatables Header -->
<div class="block">
    <div class="row-fluid">
        <div class="span12">
            <div class="box">
                <div class="block-title">
                    <h2><strong>Anular</strong> Ventas</h2>
                </div>

                <div class="box-content box-nomargin">

                    <div class="tab-content">
                        <div class="table-responsive">
                            <table class='table table-striped dataTable table-bordered'>
                                <thead>
                                <tr>

                                    <th>Nro. Venta</th>
                                    <th>Cliente</th>
                                    <th>Fecha Reg</th>
                                    <th>Monto Total <?php echo MONEDA ?></th>
                                    <th>Estatus</th>
                                    <th>Accion</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (count($ventas) > 0): ?>
                                    <?php foreach ($ventas as $venta): ?>
                                        <tr>
                                            <td><?php echo $venta->documento_Serie."-".$venta->documento_Numero; ?></td>
                                            <td><?php echo $venta->razon_social; ?></td>
                                            <td style="text-align: center;"><?php echo $venta->fecha; ?></td>
                                            <td style="text-align: center;"><?php echo $venta->total; ?></td>
                                            <td style="text-align: center;"><?php echo $venta->venta_status; ?></td>
                                            <td class='actions_big'>

                                                    <div class="btn-group">
                                                        <a onclick="anular(<?php echo $venta->venta_id; ?>)"
                                                           class='btn btn-default'><i class="fa fa-remove"></i>
                                                            Anular</a>
                                                    </div>

                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="anular" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
             aria-hidden="true">
            <form name="formeliminar" method="post" id="formeliminar" action="<?= $ruta ?>venta/anular_venta">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title">Anular Venta</h4>
                        </div>

                        <div class="modal-body">
                            <div class="form-group row">
                                <div class="col-md-2">
                                    Motivo
                                </div>
                                <div class="col-md-10">
                                    <input type="text" name="motivo" id="motivo" required="true" class="form-control"
                                        >
                                    <input type="hidden" name="id" id="id" required="true" class="form-control"
                                        >
                                </div>

                            </div>

                            </div>

                        <div class="modal-footer">
                            <button type="button" id="" class="btn btn-primary" onclick="grupo.guardar()" >Confirmar</button>
                            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

                        </div>
                    </div>
                    <!-- /.modal-content -->
                </div>
                </form>

        </div>

    </div>
</div>
<script src="<?php echo $ruta?>recursos/js/pages/tablesDatatables.js"></script>
<script>
    $(function(){

        TablesDatatables.init();

        $("#fecha").datepicker({format: 'dd-mm-yyyy'});

    });

    function anular(id) {

        $('#anular').modal('show');
        $("#id").attr('value', id);
    }

    var grupo = {
        ajaxgrupo : function(){
            return  $.ajax({
                url:'<?= base_url()?>venta/cancelar'

            })
        },
        guardar : function () {
            if ($("#motivo").val() == '') {
                var growlType = 'warning';

                $.bootstrapGrowl('<h4>Debe ingresar un motivo</h4>', {
                    type: growlType,
                    delay: 2500,
                    allow_dismiss: true
                });

                $(this).prop('disabled', true);

                return false;
            }
            App.formSubmitAjax($("#formeliminar").attr('action'), this.ajaxgrupo, 'anular', 'formeliminar');
        }
    }


</script>
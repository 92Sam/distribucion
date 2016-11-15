<?php $ruta = base_url(); ?>

<div class="modal-dialog modal-lg" >
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalle Ingreso</h4>
        </div>
        <div class="modal-body">

            <div class="table-responsive">
                <table class="table datatable datatables_filter table-striped" id="tabledetail">

                    <thead>
                    <tr>

                        <th>ID</th>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio</th>
                        <th>Unidad de Medida</th>


                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if (isset($detalles)) {
                        foreach ($detalles as $detalle) {

                            ?>
                            <tr>
                                <td>
                                    <?= $detalle->producto_id ?>
                                </td>
                                <td>
                                    <?= $detalle->producto_nombre ?>
                                </td>
                                <td>
                                    <?= $detalle->cantidad ?>
                                </td>
                                <td>
                                    <?= $detalle->precio ?>
                                </td>
                                <td>
                                    <?= $detalle->nombre_unidad ?>
                                </td>

                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>


            </div>
            <a href="<?= $ruta?>ingresos/pdf/<?php if(isset($local)) echo $local['local_id'];  ?>/0/0/<?php if(isset($id_detalle)) echo $id_detalle;  ?>"
               class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
            <a href="<?= $ruta?>ingresos/excel/<?php if(isset($local)) echo $local['local_id'];  ?>/0/0/<?php if(isset($id_detalle)) echo $id_detalle;  ?>"
               class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

        </div>
    </div>
    <!-- /.modal-content -->
</div>


<script>
    $(function () {

        $("#tabledetail").dataTable();

    });
</script>

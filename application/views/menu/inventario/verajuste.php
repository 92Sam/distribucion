<div class="modal-dialog" >
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Detalle Ajuste</h4>
        </div>
        <div class="modal-body">

            <div class="table-responsive">
                <table class="table datatable datatables_filter table-striped" id="tabledetail">

                    <thead>
                    <tr>

                        <th>C&oacute;digo</th>
                        <th>Nombre</th>
                        <th>UM</th>
                        <th>Cantidad</th>
                        <th>Fraccion</th>


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
                                    <?= $detalle->producto_nombre ?>
                                </td>
                                <td>
                                    <?= $detalle->cantidad_detalle ?>
                                </td>
                                <td>
                                    <?= $detalle->fraccion_detalle ?>
                                </td>

                            </tr>
                        <?php }
                    } ?>
                    </tbody>
                </table>


            </div>

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

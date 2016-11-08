<?php $ruta = base_url(); ?>

<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Productos</h4>
        </div>
        <div class="modal-body">
            <div class="block">

                <div class="table-responsive">
                    <table class="table table-striped dataTable table-bordered" id="example">

                        <tbody>
                        <?php foreach ($bonificaciones_has_producto as $b) {
                            echo "<tr><td>" . $b['producto_id'] . "</td><td>" . $b['producto_nombre'] . "</td></tr>";
                        } ?>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
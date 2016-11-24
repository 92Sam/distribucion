<script type="application/javascript">
    var columna = {
        guardarCol : function () {

             App.formSubmitAjax($("#columnasform").attr('action'), getproductosbylocal, 'columnas', 'columnasform');
        }
    }
</script>
<form name="formagregar" id="columnasform" action="<?= base_url() ?>producto/guardarcolumnas" method="post">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Columnas</h4>
            </div>
            <div class="modal-body">

                <div class="table-responsive">

                    <table class='table dataTable table-bordered'>
                        <thead>
                        <tr>
                            <th>Columna</th>
                            <th>Activo</th>
                            <th>Mostrar</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php foreach ($columnas as $columna) { ?>
                            <tr>
                                <input type="hidden" name="columna_id[]" value="<?php echo $columna->id_columna ?>">
                                <td><?php echo $columna->nombre_mostrar; ?></td>
                                <td>
                                    <input type="checkbox" name="activo_<?= $columna->id_columna ?>"
                                        <?php if ($columna->activo == TRUE or ($columna->nombre_columna == 'producto_id' or $columna->nombre_columna == 'producto_nombre' or
                                                $columna->nombre_columna == 'producto_id' or $columna->nombre_columna == 'producto_impuesto' or $columna->nombre_columna == 'producto_cualidad')
                                        ) echo 'checked' ?>  <?php if ($columna->nombre_columna == 'producto_id' or
                                        $columna->nombre_columna == 'producto_nombre' or $columna->nombre_columna == 'producto_id' or $columna->nombre_columna == 'producto_impuesto' or $columna->nombre_columna == 'producto_cualidad'
                                    ) echo 'disabled' ?>>
                                </td>
                                <td>
                                    <input type="checkbox"
                                           name="mostrar_<?= $columna->id_columna ?>" <?php if ($columna->mostrar == TRUE) echo 'checked' ?>>
                                </td>
                            </tr>
                        <?php } ?>


                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer">
                <button type="button" id="submitcolumnas" class="btn btn-primary" onclick="columna.guardarCol()">
                    Confirmar
                </button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>

            </div>


        </div>
        <!-- /.modal-content -->
    </div>
</form>

<script src="<?php echo base_url() ?>recursos/js/pages/tablesDatatables.js"></script>
<script type="text/javascript">
    $(function() {
     //   TablesDatatables.init();


    });


</script>
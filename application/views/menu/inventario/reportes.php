<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Inventsrio</li>
    <li><a href="">Reportes</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="col-md-1">
        Ubicaci&oacute;n
    </div>
    <div class="col-md-3">
        <select id="locales" class="form-control" name="local">
            <option value="seleccione"> Seleccione</option>
            <?php if(isset($locales)) {
                foreach($locales as $local){
                    ?>
                    <option value="<?= $local['int_local_id']; ?>"> <?= $local['local_nombre'] ?> </option>

                <?php }
            } ?>
        </select>
        <br>
    </div>
    <div class="box-body" id="tabla">


    </div>

<input type="hidden" value="<?= $tipo ?>" id="tipo">
    <div class="table-responsive"></div>
</div>


<!-- /.modal-dialog -->
</div>

<script type="text/javascript">
    $(function () {
        TablesDatatables.init();
        $("#locales").on("change",function(){


            // $("#hidden_consul").remove();
            $.ajax({
                url: '<?= base_url()?>inventario/view_reporte',
                data: {'id_local': $("#locales").val(), tipo:$("#tipo").val() },
                type: 'POST',
                success: function (data) {
                    // $("#query_consul").html(data.consulta);

                    $("#tabla").html(data);
                    $("#tablaresult").dataTable();
                }
            })
        });
        });
    </script>
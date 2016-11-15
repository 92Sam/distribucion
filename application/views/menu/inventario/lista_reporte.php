<?php $ruta = base_url(); ?>
<div class="table-responsive">
<table class="table table-striped dataTable table-bordered" id="tablaresultado">
    <thead>
    <tr>
        <th>ID</th>
        <th>Nombre </th>
        <th>Existencia</th>
        <th>Fracci&oacute;n</th>
    </tr>
    </thead>
    <tbody>
    <?php if (count($inventarios) > 0) {
        foreach ($inventarios as $inventario) {
            ?>
            <tr>
                <td class="center"><?= $inventario->id_inventario ?></td>
                <td class="center"><?= $inventario->producto_nombre ?></td>
                <td><?= $inventario->cantidad ?></td>
                    <td><?= $inventario->fraccion ?></td>

            </tr>
        <?php }
    } ?>
    </tbody>
</table>

    </div>


<div class="btn-group">
    <a href="<?= $ruta?>inventario/pdf/<?= $tipo_reporte ?>/<?= $local ?>" id=""
       value="<?= $ruta?>inventario/pdf/<?= $tipo_reporte?>/<?= $local ?>"
       class="btn  btn-default btn-lg pdf" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
    <a href="<?= $ruta?>inventario/excel/<?= $tipo_reporte?>/<?= $local ?>"
       value="<?= $ruta?>inventario/excel/<?= $tipo_reporte?>/<?= $local ?>" class="btn btn-default btn-lg excel"
       data-toggle="tooltip" title="Exportar a Excel"
       data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
</div>

<div class="modal fades" id="verajuste" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>

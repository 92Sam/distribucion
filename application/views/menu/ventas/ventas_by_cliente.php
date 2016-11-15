<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Ventas</li>
    <li><a href="">Ventas por Cliente</a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <br>

    <div class="table-responsive">
        <table class="table table-striped dataTable table-bordered" id="example">
            <thead>
            <tr>
                <th>N&uacute;mero de Venta</th>
                <th>Fecha</th>
                <th>Vendedor</th>
                <th>Cliente</th>

                <th>Estado</th>

                <th>Sub total</th>
                <th>Impuesto</th>
                <th>Total</th>
                <th class="desktop">Acciones</th>


            </tr>
            </thead>
            <tbody>
            <?php if (count($ventas) > 0) {

                foreach ($ventas as $venta) {
                    ?>
                    <tr>

                        <td class="center"><?= $venta->venta_id ?></td>
                        <td><?= date('d-m-Y H:i:s', strtotime($venta->fecha))?></td>
                        <td><?= $venta->nombre ?></td>
                        <td><?= $venta->razon_social ?></td>

                        <td><?= $venta->venta_status ?></td>

                        <td><?= $venta->sub_total ?></td>
                        <td><?= $venta->impuesto ?></td>
                        <td><?= $venta->totalizado ?></td>



                        <td class="center">
                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Ver" data-original-title="Ver"
                                            href="#" onclick="ver(' . $venta->id_cliente . ')">'; ?>
                                <i class="fa fa-search"></i>
                                </a>


                            </div>
                        </td>
                    </tr>
                <?php }
            } ?>

            </tbody>
        </table>
           </div>

    <br>
    <a href="<?= $ruta?>venta/pdf/0/0/0/0/<?= $ventatodos?>" id="generarpdf" class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
    <a href="<?= $ruta?>venta/excel/0/0/0/0/<?= $ventatodos?>" class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>

</div>



<script type="text/javascript">

    function ver(id) {
        $("#agregargrupo").load('<?= $ruta ?>venta/show_venta_cliente/'+id);
        $('#agregargrupo').modal('show');
    }
</script>
<div class="modal fades" id="agregargrupo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>



<script>$(function () {

        TablesDatatables.init();

    });</script>

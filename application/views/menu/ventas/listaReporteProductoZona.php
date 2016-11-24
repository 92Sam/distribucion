<?php $ruta = base_url(); ?>

<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered" id="tablaresultado">
        <thead>
            <tr>
                <th>Distrito</th>
                <th>Urbanización</th>
                <th>Vendedor</th>
                <th>Grupo</th>
                <th>Familia</th>
                <th>Linea</th>
                <th>Producto</th>

                <th>Cantidad Vendida</th>
                <th>Unidad</th>
                <th>Puntos de Venta</th>
            </tr>
        </thead>
        <tbody>
        <?php if (count($ventas) > 0) {

            foreach ($ventas as $venta) {
                    ?>
                    <tr>
                        <td><?= $venta->ciudad_nombre ?></td>
                        <td><?= $venta->urb ?></td>                        
                        <td><?= $venta->nombre ?></td>
                        <td><?= $venta->nombre_grupo ?></td>                     
                        <td><?= $venta->nombre_familia ?></td>
                        <td><?= $venta->nombre_linea ?></td>
                        <td><?= $venta->producto_nombre ?></td>

                        <td><?= $venta->cantidad_vendida ?></td>
                        <td><?= $venta->nombre_unidad?></td>
                        <td><?= $venta->clientes_atendidos ?></td>
                    </tr>

                <?php } 
            
        }?>

        </tbody>
    </table>

</div>

<a href="<?= $ruta?>venta/pdfReporteZona/<?php if(isset($zona)) echo $zona; else echo 'TODAS';?>/<?php if(isset($fecha_desde)) echo date('Y-m-d', strtotime($fecha_desde)); else echo '';?>/<?php if(isset($fecha_hasta)) echo date('Y-m-d', strtotime($fecha_hasta)); else echo '';?>/"
   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta?>venta/excelReporteZona/<?php if(isset($zona)) echo $zona; else echo 'TODAS';?>/<?php if(isset($fecha_desde)) echo date('Y-m-d', strtotime($fecha_desde)); else echo '';?>/<?php if(isset($fecha_hasta)) echo date('Y-m-d', strtotime($fecha_hasta)); else echo '';?>/"
   class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
<a onclick="agregar();"
   class="btn btn-default btn-lg" data-toggle="tooltip" title="Zonas con Más Pedidos"
   data-original-title="fa fa-file-image-o"><i class="fa fa-pie-chart fa-fw"></i></a>
<script type="text/javascript">

    $(function () {
        TablesDatatables.init();
    });

    function agregar() {

        $("#grafico").load('<?= $ruta ?>venta/pedidosZona');
        $('#grafico').modal('show');
    }

</script>

<div class="modal fade" id="grafico" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>
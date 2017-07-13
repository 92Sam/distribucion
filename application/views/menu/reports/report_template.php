<ul class="breadcrumb breadcrumb-top">
    <li>Reportes</li>
    <li><a href=""><?= isset($reporte_nombre) ? $reporte_nombre : '' ?></a></li>
</ul>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="box-body">
        <?= isset($reporte_filtro) ? $reporte_filtro : '' ?>
    </div>

    <br>

    <div id="reporte_tabla" class="box-body">

            <?= isset($reporte_tabla) ? $reporte_tabla : '' ?>

    </div>

</div>

<div class="modal fade" id="detalle_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>


<script type="text/javascript">
    <?= isset($reporte_js) ? $reporte_js : '' ?>
</script>

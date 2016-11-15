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

    <div class="box-body">
        <div id="reporte_tabla" class="table-responsive">
            <?= isset($reporte_tabla) ? $reporte_tabla : '' ?>
        </div>
    </div>

</div>


<script type="text/javascript">
    <?= isset($reporte_js) ? $reporte_js : '' ?>
</script>

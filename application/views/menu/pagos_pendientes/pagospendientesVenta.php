<?php $ruta = base_url(); ?>


<ul class="breadcrumb breadcrumb-top">
    <li>Cobranzas</li>
    <li><a href="">Pagos Pendientes</a></li>
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

<div class="modal fade" id="dialog_pagar" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
    
</div>



<br>
    <table class="table dataTable table-striped dataTable table-bordered" id="tablaresultado">
        <thead>
        <tr>

            <th>N&uacute;mero</th>
            <th>Fecha</th>
            <th>Nombre</th>

            <th class="desktop">Acciones</th>

        </tr>
        </thead>
        <tbody>
        <?php if (count($ajustes) > 0) {

            foreach ($ajustes as $ajuste) {
                ?>
                <tr>

                    <td class="center"><?= $ajuste->id_ajusteinventario ?></td>
                    <td class="center"><?= date('d-m-Y H:i:s', strtotime($ajuste->fecha)) ?></td>
                    <td><?= $ajuste->descripcion ?></td>


                    <td class="center">
                        <div class="btn-group">
                            <?php

                            echo '<a class="btn btn-default btn-default btn-default" data-toggle="tooltip"
                                            title="Ver Detalle" data-original-title="Ver Detalle"
                                            href="#" onclick="ver(' . $ajuste->id_ajusteinventario . ');">'; ?>
                            <i class="fa fa-search"></i>
                            </a>

                        </div>
                    </td>
                </tr>
            <?php }
        } ?>

        </tbody>
    </table>

<div class="modal fades" id="verajuste" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>

<script type="text/javascript">


</script>
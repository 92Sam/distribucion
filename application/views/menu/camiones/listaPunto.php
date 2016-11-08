<?php $ruta = base_url(); ?>
<div class="table-responsive">
    <table class="table table-striped dataTable table-bordered" id="tablaresultado">
        <thead>
            <tr>
                <th >ID</th>
                <th >Fecha</th>
                <th>Nombre</th>
                <th>Camion</th>
                <th>status</th>
                <th>Mapa</th>

            </tr>
        </thead>
        <tbody>
        <?php if (count($reparticion) > 0) {

            foreach ($reparticion as $campoReparticion) {
                if($campoReparticion['status']=='IMPRESO') {

                    ?>
                    <tr>
                        <td><?= $campoReparticion['consolidado_id'] ?></td>
                        <td><?= date('d-m-Y',strtotime ($campoReparticion['fecha'])) ?></td>
                        <td><?= $campoReparticion['nombre'] ?></td>
                        <td><?= $campoReparticion['camiones_placa'] ?></td>

                        <td><?= $campoReparticion['status'] ?></td>
                        <td class="center">
                            <div class="btn-group">
                                <?php

                                echo '<a class="btn btn-default" data-toggle="tooltip"
                                            title="Ver" data-original-title="fa fa-comment-o"
                                            href="#" onclick="VerMapa(' . $campoReparticion['consolidado_id'] . ')" >'; ?>
                                Ver mapa
                                </a>
                            </div>
                        </td>

                    </tr>
                    <?php
                    }
                }
            }
        ?>

        </tbody>
    </table>

</div>



</div>

<script type="text/javascript">
    function VerMapa(id) {

        $("#mapa").load('<?= $ruta ?>puntosReparticion/verMapa/' + id);
        $('#mapa').modal('show');

    }
</script>
<!-- /.modal-dialog -->





<div class="modal fade" id="mapa" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">

</div>
<script type="text/javascript">
    $(function () {

        TablesDatatables.init();

    });



</script>
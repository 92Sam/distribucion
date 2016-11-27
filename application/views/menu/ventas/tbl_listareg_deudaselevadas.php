<?php $ruta = base_url(); ?>
<!--<script src="<?php echo $ruta; ?>recursos/js/custom.js"></script>-->
<table class='table table-striped dataTable table-bordered no-footer' id="lstPagP" name="lstPagP">
    <thead>
    <tr>
        <th>Nro Venta</th>
        <th>Cliente</th>
        <th class='tip' title="Fecha Registro">Fecha de venta.</th>
        <th class='tip' title="Monto Credito Solicitado">Monto Cred <?php echo MONEDA ?></th>
        <th class='tip' title="Monto Cancelado">Monto Canc <?php echo MONEDA ?></th>
        <th>Documento</th>
        <th>Trabajador&nbsp;</th>
        <th>Zona&nbsp;</th>
        <th>D&iacute;as de atraso </th>
        <th>Estado&nbsp;</th>

    </tr>
    </thead>
    <tbody>
    <?php if (count($lstVenta) > 0):

        ?>
        <?php foreach ($lstVenta as $v): ?>
        <tr>
            <td style="text-align: center;"><?php echo $v['documento_Serie'] . "-" . $v['documento_Numero']; ?></td>
            <td><?php echo $v['razon_social']; ?></td>
            <td style="text-align: center;"><?php echo date("d-m-Y", strtotime($v['fecha'])) ?></td>
            <td style="text-align: center;"><?php echo $v['total']; ?></td>
            <td style="text-align: center;"><?php echo $v['dec_credito_montodebito']; ?></td>
            <td style="text-align: center;"><?php echo $v['nombre_tipo_documento']; ?></td>
            <td style="text-align: center;"><?php echo $v['nombre']; ?></td>
            <td style="text-align: center;"><?php echo $v['zona_nombre']; ?></td>
            <td style="text-align: center;"><?php $days = (strtotime(date('d-m-Y')) - strtotime($v['fecha'])) / (60 * 60 * 24);?>
                <div><label class=" label <?php if (floor($days) < 8) {
                        echo "label-success";
                    } elseif (floor($days) < 16) {
                        echo "label-info";
                    } else {
                        echo "label-warning";
                    } ?> "> <?= floor($days); ?></label></div>
            </td>
            <td style="text-align: center;" ><?php if ($v['var_credito_estado'] == CREDITO_ACUENTA) {
                    echo "A Cuenta";
                } elseif ($v['var_credito_estado'] == CREDITO_CANCELADO) {
                    echo utf8_encode("Cancelado");
                } elseif ($v['var_credito_estado'] == CREDITO_DEBE) {
                    echo "DB";
                } else {
                    echo utf8_encode("Nota de Credito");
                } ?></td>

        </tr>
    <?php endforeach; ?>
    <?php else : ?>
    <?php endif; ?>
    </tbody>
</table>




<script type="text/javascript">

    var lst_venta = new Array();
    $(document).ready(function () {

        TablesDatatables.init();


    });


</script>
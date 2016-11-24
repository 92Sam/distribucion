<style type="text/css">
    table {
        width: 100%;
    }

    thead, th {
        background-color: #CED6DB;
        color: #000;
    }

    tbody tr {
        border-color: #111;
    }

    .title {
        font-weight: bold;
        text-align: center;
        font-size: 1.5em;
        color: #000;
    }
</style>
<table>
    <tr>
        <td style="" class="title" colspan="8">
            CUADRE DE CAJA
        </td>
    </tr>
    <tr>
        <td style="" class="title" colspan="8">
            <?php echo $this->session->userdata('EMPRESA_NOMBRE')." - ". $this->session->userdata('local_nombre'); ?>
        </td>
    </tr>
    <tr>
        <td colspan="8">&nbsp;&nbsp;</td>

    </tr>

    <tr>

        <td style="font-weight: bold;">Cajero:</td>
        <td width="25%">
            <?php echo $this->session->userdata('nombre')

            ?>
        </td>
        <td colspan="2"></td>
        <td style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
        <td width="25%">
            <?php echo date('d-m-Y H:i:s ');

            ?>
        </td>
    </tr>
    <tr>
        <td colspan="7"></td>
    </tr>
    <tr>
        <td width="15%"></td>
        <td width="25%"colspan="2">EFECTIVO SOLES(caja)</td>
        <td width="25%"colspan="2">DEPOSITOS SOLES(bancos)</td>
        <td width="25%" colspan="2">TOTALES (caja + bancos)</td>
    </tr>
    <tr>
        <td width="15%">SALDO INICIAL</td>
        <td colspan="2"></td>
        <td colspan="2"></td>
        <td colspan="2"></td>
    </tr>
    <tr>
        <td width="15%">INGRESOS</td>
        <td colspan="2"><?= number_format($total_ingresos_caja,2) ?></td>
        <td colspan="2"><?= number_format($total_ingresos_banco,2) ?></td>
        <td colspan="2"><?= number_format($total_ingresos_banco+$total_ingresos_caja,2) ?></td>
    </tr>
    <tr>
        <td width="15%">EGRESOS</td>
        <td colspan="2"><?= number_format($total_egreso_caja,2); ?></td>
        <td colspan="2">0.00</td>
        <td colspan="2"><?= number_format($total_egreso_caja,2); ?></td>
    </tr>

    <tr>
        <td width="15%">SALDO FINAL DEL DIA</td>
        <td colspan="2"><?= number_format($total_ingresos_caja-$total_egreso_caja,2) ?></td>
        <td colspan="2"><?= number_format($total_ingresos_banco,2) ?></td>
        <td colspan="2"><?= number_format(($total_ingresos_caja-$total_egreso_caja)+$total_ingresos_banco,2) ?></td>
    </tr>

</table>

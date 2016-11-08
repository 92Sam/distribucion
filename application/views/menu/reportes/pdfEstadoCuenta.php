<style type="text/css">
	table {
		width: 100%;
	}

	thead, th {
		background-color: #CED6DB;
		color: #000;
	}
	tbody tr{
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
		<td style="" class="title"
			colspan="9">ESTADO DE CUENTA
		</td>
	</tr>
	<tr>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
	</tr>

	<tr>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="12%">&nbsp;&nbsp;</td>
		<td width="7%">&nbsp;&nbsp;</td>
		<td width="5%">&nbsp;&nbsp;</td>
		<td width="5%">&nbsp;&nbsp;</td>
		<td width="5%">&nbsp;&nbsp;</td>
		<td width="18%" style="font-weight: bold;">Fecha Emisi&oacute;n:</td>
		<td width="25%"><?php echo date("Y-m-d H:i:s"); ?></td>
	</tr>
	<tr>
		<td colspan="8"></td>
	</tr>
</table>
<table>
	<thead>
	<tr>
		<th>ID venta</th>
		<th>Documento</th>
		<th>Nro Venta</th>

		<th>Cliente</th>
		<th class='tip' title="Fecha Registro">Fecha de venta.</th>
		<th class='tip' title="Monto Credito Solicitado">Monto Cred <?php echo MONEDA ?></th>
		<th class='tip' title="Monto Cancelado">Monto Canc <?php echo MONEDA ?></th>
		<th class='tip' title="Monto Cancelado">Por Liquidar <?php echo MONEDA ?></th>

		<th>D&iacute;as de atraso</th>
		<th>Estado&nbsp;</th>

	</tr>
	</thead>
	<tbody>
	<?php if (count($lstVenta) > 0):

		?>
		<?php foreach ($lstVenta as $v): ?>
		<tr>
			<td style="text-align: center;"><?php echo $v['venta_id']; ?></td>
			<td style="text-align: center;"><?php echo $v['nombre_tipo_documento']; ?></td>
			<td style="text-align: center;"><?php echo $v['documento_Serie'] . "-" . $v['documento_Numero']; ?></td>
			<td><?php echo $v['razon_social']; ?></td>
			<td style="text-align: center;"><?php echo date("d-m-Y H:i:s", strtotime($v['fecha'])) ?></td>
			<td style="text-align: center;"><?php echo $v['total']; ?></td>
			<td style="text-align: center;"><?php echo $v['dec_credito_montodebito']; ?></td>
			<td style="text-align: center;"><?php echo number_format($v['confirmar'], 2); ?></td>


			<td style="text-align: center;"><?php $days = (strtotime(date('d-m-Y')) - strtotime($v['fecha'])) / (60 * 60 * 24); ?>
				<div><label class="label <?php if (floor($days) < 8) {
						echo "label-success";
					} elseif (floor($days) < 16) {
						echo "label-info";
					} else {
						echo "label-warning";
					} ?> "> <?= floor($days); ?></label></div>
			</td>
			<td style="text-align: center;"><?php if ($v['var_credito_estado'] == CREDITO_ACUENTA) {
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
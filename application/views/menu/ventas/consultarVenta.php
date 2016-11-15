<?php $ruta = base_url(); ?>
<div class="row-fluid">
	<div class="span12">
		<div class="box">
			<div class="box-head">
				<h3>BUSCAR</h3>
			</div>
			<div class="box-content box-nomargin" style="text-align: center;padding:1% 0 0.2% 0;">
				<span class="add-on" >Fechas:&nbsp;&nbsp;&nbsp;&nbsp;</span><input type="text" name="fecIni" id="fecIni" class='input-small datepick'>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;del
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="text" name="fecFin" id="fecFin" class='input-small datepick'>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<button class="btn btn-inverse" style="margin-top:-1%;">Buscar</button>
			</div>
		</div>
	</div>
</div>
<div class="row-fluid">
	<div class="span12">
		<div class="box">
			<div class="box-head tabs">
				<h3>LISTA VENTAS</h3>
				<ul class='nav nav-tabs'>
					<li class='active'>
						<a href="#lista" data-toggle="tab">Lista</a>
					</li>
				</ul>
			</div>
			<div class="box-content box-nomargin">
				<div class="tab-content">
						<div class="tab-pane active" id="lista">
							<table class='table table-striped dataTable table-bordered'>
								<thead>
									<tr>
										<th>Nro. Venta</th>
										<th>Cliente</th>
										<th>Fecha Reg</th>
										<th>Monto Total <?php echo MONEDA ?></th>
										<th>Documento</th>
										<th>Forma Pago</th>
										<th>Accion</th>
									</tr>
								</thead>
								<tbody>
									<?php if(count($lstVenta)>0):?>
									<?php foreach ($lstVenta as $v):?>
									 	<tr>
									 		<td><?php echo $v->cTipDocSerie.'-'.$v->cTipDocCodigo;?></td>
									 		<td><?php echo $v->var_cliente_nombre;?></td>
									  		<td><?php echo $v->dat_venta_fecregistro;?></td>
									  		<td><?php echo $v->dec_venta_montoTotal;?></td>
									  		<td><?php echo $v->cTipDocDescripcion;?></td>
									  		<td><?php echo $v->cFormapago;?></td>
									  		<td class='actions_big'>
												<div class="btn-group">
                                                    <a href="#" class='btn btn-default tip' title="Ver Venta"><img
                                                            src="<?php echo $ruta; ?>recursos/img/icons/fugue/magnifier.png"
                                                            alt=""> Ver</a>
                                                    <a href="#" class='btn btn-default tip' title="Imprimir"><img
                                                            src="<?php echo $ruta; ?>recursos/img/icons/fugue/printer.png"
                                                            alt=""> Imprimir</a>
												</div>
											</td>
										</tr>
									<?php endforeach;?>
									<?php endif;?>
								</tbody>
							</table>
						</div>
				</div>
			</div>
		</div>
	</div>
</div>
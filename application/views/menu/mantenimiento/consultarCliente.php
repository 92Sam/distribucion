<?php $ruta = base_url(); ?>
<div class="row-fluid">
	<div class="span12">
	<div class="box">
		<div class="box-head">
			<h3>Lista Cliente</h3>
			<div class="actions">
				<ul>
					<li>
                        <a href="<?php echo $ruta; ?>exportar/toExcel_listaCliente" class='tip btn btn-default'
                           title="Exportar a Excel"><img src="<?php echo $ruta; ?>recursos/img/icons/essen/16/issue.png"
                                                         alt=""></a>
					</li>
					<li>
                        <a href="#" class='btn btn-default tip' title="Exportar a PDF"><img
                                src="<?php echo $ruta; ?>recursos/img/icons/essen/16/attibutes.png" alt=""></a>
					</li>
				</ul>
			</div>
		</div>
		<div class="box-content box-nomargin">
			<table class='table table-striped dataTable table-bordered'>
				<thead>
					<tr>
						<th>Ruc</th>
						<th>Dni &nbsp;</th>
						<th>Cliente </th>
						<th>Telefono &nbsp;&nbsp;</th>
						<th>Direccion &nbsp;</th>
					</tr>
				</thead>
				<tbody>
					<?php if(count($lstCliente)>0):?>
					<?php foreach ($lstCliente as $cl):?>
					 	<tr>
					  		<td><?php echo $cl->RUC;?></td>
					  		<td><?php echo $cl->DNI;?></td>
					  		<td><?php echo $cl->Cliente;?></td>
					  		<td><?php echo $cl->Telefono;?></td>
					  		<td><?php echo $cl->Direccion;?></td>
						</tr>
					  <?php endforeach;?>
					  <?php else :?>
					  <?php endif;?>
				</tbody>
			</table>
		</div>
	</div>
	</div>
</div>
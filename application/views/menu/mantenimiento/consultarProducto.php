<?php $ruta = base_url(); ?>
<div class="row-fluid">
	<div class="span12">
	<div class="box">
		<div class="box-head">
			<h3>Stock de Producto</h3>
			<div class="actions">
				<ul>
					<li>
                        <a href="<?php echo $ruta; ?>exportar/toExcel_stockProducto" class='tip btn btn-default'
                           title="Exportar a Excel"><img src="<?php echo $ruta; ?>recursos/img/icons/essen/16/issue.png"
                                                         alt=""></a>
					</li>
				</ul>
			</div>
		</div>
		<div class="box-content box-nomargin">
				<table class='table table-striped dataTable table-bordered' id="tbLista">
					<thead>
						<tr>
							<th>Producto</th>
							<th>Marca &nbsp;</th>
							<th>Categoria </th>
							<th>Unid. Medida &nbsp;</th>
							<th>Stock &nbsp;&nbsp;</th>
							<th>Precio Venta. <?php echo MONEDA ?>.&nbsp;</th>
						</tr>
					</thead>
					<tbody>
						<?php if(count($lstProducto)>0):?>
						<?php foreach ($lstProducto as $pd):?>
						 	<tr>
						  		<td><?php echo $pd->var_producto_nombre." - ".$pd->var_producto_descripcion;?></td>
						  		<td><?php echo $pd->var_producto_marca;?></td>
						  		<td><?php echo $pd->var_Categoria_Desc;?></td>
						  		<td><?php echo $pd->var_constante_descripcion;?></td>
						  		<td><?php echo $pd->dec_producto_cantidad;?></td>
						  		<td><?php echo $pd->dec_producto_precioventa;?></td>
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
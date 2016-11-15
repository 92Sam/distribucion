<div class="row-fluid">
	<div class="span12">
		<div class="box">
			<form method="post" id="frmVenta" action="#" class='form-horizontal'>
			<div class="box-head tabs">
				<h3>Generar Prestamos</h3>
				<ul class="nav nav-tabs">
					<li class='active'>
						<a href="#ModoPago" data-toggle="tab">Modo Pago</a>
					</li>
					<li >
						<a href="#lista" data-toggle="tab">Lista Producto</a>
					</li>
				</ul>
			</div>
			
			<div class="box-content">
				<div class="tab-content">
					<div class="tab-pane active" id="ModoPago">
						<div class="control-group">
							<label for="cliente" class="control-label">Cliente</label>
							<div class="controls">
								<select name="cboCliente" id="cboCliente" class='cho'>
									<?php if(count($lstCliente)>0):?>
									<?php foreach ($lstCliente as $cl):?>
										<option value="<?php echo $cl->id;?>"><?php echo $cl->Cliente;?></option>
									<?php endforeach;?>
									<?php else :?>
									<?php endif;?>
								</select>
							</div>
						</div>
						<hr/>
						<div class="control-group">
							<label for="cboTipDoc" class="control-label">Tipo Documento:</label>
							<div class="controls">
								<select name="cboTipDoc" id="cboTipDoc">
									<?php if(count($lstTipDoc)>0):?>
										<?php foreach ($lstTipDoc as $lc):?>
											<option value="<?php echo $lc->int_constante_valor;?>"><?php echo $lc->var_constante_descripcion;?></option>
									 <?php endforeach;?>
							  		<?php endif;?>
								</select>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Modo Pago:</label>
							<div class="controls">
								<select name="cboModPag" id="cboModPag" onchange="activarText_ModoPago()">
									<?php if(count($lstFormaPago)>0):?>
										<?php foreach ($lstFormaPago as $lc):?>
											<option value="<?php echo $lc->int_constante_valor;?>"><?php echo $lc->var_constante_descripcion;?></option>
									 <?php endforeach;?>
							  		<?php endif;?>
								</select>
								<div class="input-prepend input-append" id="numero_cuota">
									&nbsp;&nbsp;&nbsp;<span class="add-on" >Nro Cuota</span><input type="number" min="1" max="10" step="1" class='input-square input-mini' name="nrocuota" id="nrocuota" onkeyup="calcular_monto_cuota();">
								</div>
								<div class="input-prepend input-append" id="monto_cuota">
									&nbsp;&nbsp;&nbsp;<span class="add-on" >Monto x Cuota</span><input type="text" class='input-square input-small' name="montxcuota" id="montxcuota" readonly><span class="add-on"><?php echo MONEDA ?></span>
								</div>
							</div>
						</div>
						<hr/>
						<div class="control-group">
							<label for="subTotal" class="control-label">SubTotal:</label>
							<div class="controls">
								<div class="input-prepend input-append">
									<span class="add-on" ><?php echo MONEDA ?></span><input type="text" class='input-square input-small' name="subTotal" id="subTotal" readonly>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label for="montoigv" class="control-label">IGV:</label>
							<div class="controls">
								<div class="input-prepend input-append">
									<input type="number" class='input-square input-mini' min="0.0" max="100.0" step="0.1" value="18.0" name="igv" id="igv" onkeyup="calcular_monto_base_porcentaje();">
									<input type="text" class='input-square input-small' name="montoigv" id="montoigv" readonly><span class="add-on" ><?php echo MONEDA ?></span>
								</div>
							</div>
						</div>
						<div class="control-group">
							<label class="control-label">Total a Pagar:</label>
							<div class="controls">
								<div class="input-prepend input-append">
									<span class="add-on" ><?php echo MONEDA ?></span><input type="text" class='input-square input-small' name="totApagar" id="totApagar" readonly>
								</div>
							</div>
						</div>
						<div class="control-group" id="monto_importe">
							<label for="importe" class="control-label">Importe:</label>
							<div class="controls">
								<div class="input-prepend input-append">
									<span class="add-on" ><?php echo MONEDA ?></span><input type="number" class='input-square input-small' min="0.0" step="0.1" value="0.0" name="importe" id="importe" onkeydown="return soloDecimal(this, event);" onkeyup="calcular_importe();" >
								</div>
							</div>
						</div>
						<div class="control-group" id="monto_vuelto">
							<label for="vuelto" class="control-label">Vuelto:</label>
							<div class="controls">
								<div class="input-prepend input-append">
									<span class="add-on" ><?php echo MONEDA ?></span><input type="text" class='input-square input-small' name="vuelto" id="vuelto" readonly>
								</div>
							</div>
						</div>
						<div class="form-actions">
							<input class="btn btn-danger" value="Cancelar Venta" type="reset" />
							<button class="btn btn-primary" id="btnRealizarVenta" >Realizar Venta</button>
						</div>
					</div>
					<div class="tab-pane" id="lista">
					</div>
				</div>
			</div>
		</form>	
		</div>
	</div>
</div>
<?php $ruta = base_url(); ?>
<script src="<?php echo $ruta;?>recursos/js/Validacion.js"></script>
<script type="text/javascript">

	var lst_listaPrestamo= new Array();
	
	function buscarProducto(){
		var id= $("#cboProducto").val();
		$.ajax({
			type: 'POST',
			data: "id="+id,
			dataType: "json",
			url:'<?php echo $ruta;?>'+'producto/buscar_id',
			success:function(msj){
				jQuery.each( msj, function( key, value ) {
					document.getElementById('precio').value=value["dec_producto_precioventa"];
					document.getElementById('stock').value=value["dec_producto_cantidad"];
				});
			}
		});
		return false;
	}	
	
</script>
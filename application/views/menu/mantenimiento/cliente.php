<?php $ruta = base_url(); ?>

<script type="text/javascript">
	$(document).ready(function(){
		$("#btnGuardar").click(function(e){
			e.preventDefault();
			$.ajax({
				type: 'POST',
				data: $('#frmCliente').serialize(),
				url:'<?php echo $ruta;?>'+'cliente/registrar',
				success:function(msj){
					if(msj == 'guardo'){
						$('#mOK').modal('show');
						jQuery.fn.reset = function(){
							$(this).each(function(){this.reset();});
						};
						$("#frmCliente").reset();
					}else{
						$("#mensaje").html("<div class='alert alert-block alert-danger'><a class='close' data-dismiss='alert' href='#'>�</a><h4 class='alert-heading'>Mensaje!</h4>Registro Fallido...</div>");
					}
				}
			});
		});
	});
	
	function eliminar(id){
		$.ajax({
			type: 'POST',
			data: "id="+id,
			url:'<?php echo $ruta;?>'+'cliente/eliminar',
			success:function(msj){
				if(msj == 'elimino'){
					$('#mOK1').modal('show');
				}else{
					$("#no1").modal('show');
				}
			}
		});
		return false;
	}

	function obtenerCliente(tipb,id){
		if(tipb==1){
			$.ajax({
				type: 'POST',
				data: "id="+id,
				dataType: "json",
				url:'<?php echo $ruta;?>'+'cliente/buscar_id',
				success:function(msj){
					jQuery.each( msj, function( key, value ) {
						document.getElementById('dni_v').value=value["var_cliente_dni"];
						document.getElementById('ruc_v').value=value["var_cliente_ruc"];
						document.getElementById('nombre_v').value=value["var_cliente_nombre"];
						document.getElementById('direccion_v').value=value["var_cliente_direccion"];
						document.getElementById('telefono_v').value=value["var_cliente_telefono"];
						document.getElementById('cboTipoTelf_v').value=value["var_constante_descripcion"];
						document.getElementById('email_v').value=value["var_cliente_email"];
						document.getElementById('otros_v').value=value["var_cliente_otros"];
					});
					$("#view").modal('show');
				}
			});
			return false;
		}
		if(tipb==2){
			$.ajax({
				type: 'POST',
				data: "id="+id,
				dataType: "json",
				url:'<?php echo $ruta;?>'+'cliente/buscar_id',
				success:function(msj){
					jQuery.each( msj, function( key, value ) {
						document.getElementById('cliente_id').value=value["nCliCodigo"];
						document.getElementById('dni_udp').value=value["var_cliente_dni"];
						document.getElementById('ruc_udp').value=value["var_cliente_ruc"];
						document.getElementById('nombre_udp').value=value["var_cliente_nombre"];
						document.getElementById('direccion_udp').value=value["var_cliente_direccion"];
						document.getElementById('telefono_udp').value=value["var_cliente_telefono"];
						selectInCombo('cboTipoTelf_udp',value["int_cliente_tipotelefono"]);
						document.getElementById('email_udp').value=value["var_cliente_email"];
						document.getElementById('otros_udp').value=value["var_cliente_otros"];
					});
					$("#edit").modal('show');
				}
			});
			return false;
		}
		
	}

	function update(){
		$.ajax({
			url:'<?php echo $ruta;?>'+'cliente/update',
			type: 'POST',
			data: $('#frmCliente_udp').serialize(),
			success:function(msj){
				if(msj == 'actualizo'){
					window.location.reload();
				}
			}
		});
		return false;
	}
	
</script>
<div class="row-fluid">
	<div class="span6">
	<div class="box">
		<div class="box-head tabs">
			<h3>Lista Clientes</h3>
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
			<div class="tab-content">
			
					<div class="tab-pane active" id="basic">
						<table class='table table-striped table-media dataTable table-bordered' id="tblistaC">
							<thead>
								<tr>
									<th>Nro. Documento&nbsp;&nbsp; </th>
									<th>Nombre</th>
									<th>Telefono</th>
									<th>Accion</th>
								</tr>
							</thead>
							<tbody>
								<?php if(count($lstCliente)>0):?>
								<?php foreach ($lstCliente as $cl):?>
								 	<tr>
								 		<td style="text-align: center;"><?php echo $cl->Documento;?></td>
								  		<td><?php echo $cl->Cliente;?></td>
								  		<td><?php echo $cl->Telefono;?></td>
								  		<td class='actions_big'>
											<div class="btn-group">
                                                <a style="cursor:pointer;" class='btn btn-default tip' title="Ver"
                                                   onclick="obtenerCliente(1,<?php echo $cl->id; ?>)"><img
                                                        src="<?php echo $ruta; ?>recursos/img/icons/fugue/magnifier.png"
                                                        alt=""></a>
                                                <a style="cursor:pointer;" class='btn btn-default tip' title="Editar"
                                                   onclick="obtenerCliente(2,<?php echo $cl->id; ?>)"><img
                                                        src="<?php echo $ruta; ?>recursos/img/icons/essen/16/pencil.png"
                                                        alt=""></a>
                                                <a style="cursor:pointer;" onclick="eliminar(<?php echo $cl->id; ?>)"
                                                   class='btn btn-default tip' title="Eliminar"><img
                                                        src="<?php echo $ruta; ?>recursos/img/icons/fugue/cross.png"
                                                        alt=""></a>
											</div>
										</td>
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
	</div>
	<div class="span6">
		<div class="box">
			<div class="box-head tabs">
				<h3>Formulario Registro</h3>
				<ul class='nav nav-tabs'>
					<li class='active'>
						<a href="#c_natural" data-toggle="tab">Cliente</a>
					</li>
				</ul>
			</div>
			<div class="box-content">
			    <div class="tab-content">
					<div class="tab-pane active" id="c_natural">
					<div id="mensaje"></div>
						<form id="frmCliente" class='validate form-horizontal' action="#">
							<fieldset>
								<div class="control-group">
									<label class="control-label">Cedula:</label>
									<div class="controls">
										<input type="text" name="dni" id="dni" class='input-small' autofocus="autofocus" placeholder="00000000" maxlength="15" onkeypress="return soloNumeros(event);" required>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Ruc:</label>
									<div class="controls">
										<input type="text" name="ruc" id="ruc" class='input-small' maxlength="15" placeholder="00000000000" onkeypress="return soloNumeros(event);">
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Nombre y Apellidos:</label>
									<div class="controls">
										<input type="text" name="nombre" id="nombre" class='input-xlarge required' value="" onkeypress="return soloTexto(event);" required>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Direccion:</label>
									<div class="controls">
										<input type="text" name="direccion" id="direccion" class='input-xlarge required' value="" required>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label">Telefono</label>
									<div class="controls">
										<select name="cboTipoTelf" id="cboTipoTelf" style="width:36%">
										<?php if(count($lstConstante)>0):?>
										<?php foreach ($lstConstante as $lc):?>
											<option value="<?php echo $lc->int_constante_valor;?>"><?php echo $lc->var_constante_descripcion;?></option>
										 <?php endforeach;?>
								  		<?php endif;?>
										</select>
										<input type="text" name="telefono" id="telefono" class='input-medium required' maxlength="10" onkeypress="return soloNumeros(event);" required>
									</div>
								</div>
								<div class="control-group">
									<label for="email" class="control-label">Email:</label>
									<div class="controls">
										<div class="input-append">
											<input type="text" name="email" id="email" class='input-xlarge' placeholder="ejemplo@gmail.com"><span class="add-on"><i class="icon-envelope"></i></span>
										</div>
									</div>
								</div>
								<div class="control-group">
									<label for="otros" class="control-label">Otros Datos:</label>
									<div class="controls">
											<textarea name="otros" id="otros" class='span10 counter' data-max="200" rows='3'></textarea>
									</div>
								</div>
								
								<div class="form-actions">
									<button class="btn btn-primary" id="btnGuardar">Guardar</button>
									<input type="reset" class='btn btn-danger' value="Cancelar">
								</div>
							</fieldset>
						</form>
				</div>
			</div>
			</div>
		</div>
	</div>
	
	<!-- Modales for Messages -->
	<div class="modal hide" id="mOK">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" onclick="javascript:window.location.reload();">�</button>
	    <h3>Notificaci&oacute;n</h3>
	  </div>
	  <div class="modal-body">
	    <p>Registro Exitosa</p>
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary" data-dismiss="modal" onclick="javascript:window.location.reload();">Close</a>
	  </div>
	</div>
	
	<div class="modal hide" id="mOK1">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" onclick="javascript:window.location.reload();">�</button>
	    <h3>Notificaci&oacute;n</h3>
	  </div>
	  <div class="modal-body">
	    <p>Eliminaci&oacute;n Exitosa</p>
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary" data-dismiss="modal" onclick="javascript:window.location.reload();">Close</a>
	  </div>
	</div>
	
	<div class="modal hide" id="no1">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal" onclick="javascript:window.location.reload();">�</button>
	    <h3>Notificaci&oacute;n</h3>
	  </div>
	  <div class="modal-body">
	    <p>Eliminaci&oacute;n Fallida</p>
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary" data-dismiss="modal" onclick="javascript:window.location.reload();">Close</a>
	  </div>
	</div>
	
	<div class="modal hide" id="edit">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal">�</button>
	    <h3>Editar Cliente</h3>
	  </div>
	  <div class="modal-body">
	    <form id="frmCliente_udp" class='validate form-horizontal' action="#">
			<fieldset>
				<div class="control-group">
					<input type="text" name="cliente_id" id="cliente_id" style="display: none;">
					<label class="control-label">Doc Identidad:</label>
					<div class="controls">
						<input type="text" name="dni_udp" id="dni_udp" class='input-small' autofocus="autofocus" placeholder="00000000" maxlength="8" onkeypress="return soloNumeros(event);" required>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Ruc:</label>
					<div class="controls">
						<input type="text" name="ruc_udp" id="ruc_udp" class='input-small' maxlength="11" placeholder="00000000000" onkeypress="return soloNumeros(event);">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Nombre y Apellidos:</label>
					<div class="controls">
						<input type="text" name="nombre_udp" id="nombre_udp" class='input-xlarge required' value="" onkeypress="return soloTexto(event);" required>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Direccion:</label>
					<div class="controls">
						<input type="text" name="direccion_udp" id="direccion_udp" class='input-xlarge required' value="" required>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Telefono</label>
					<div class="controls">
						<select name="cboTipoTelf_udp" id="cboTipoTelf_udp" style="width:36%">
							<?php if(count($lstConstante)>0):?>
							<?php foreach ($lstConstante as $lc):?>
								<option value="<?php echo $lc->int_constante_valor;?>"><?php echo $lc->var_constante_descripcion;?></option>
							 <?php endforeach;?>
					  		<?php endif;?>
						</select>
						<input type="text" name="telefono_udp" id="telefono_udp" class='input-medium required' maxlength="10" onkeypress="return soloNumeros(event);" required>
					</div>
				</div>
				<div class="control-group">
					<label for="email" class="control-label">Email:</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" name="email_udp" id="email_udp" class='input-xlarge' placeholder="ejemplo@gmail.com"><span class="add-on"><i class="icon-envelope"></i></span>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label for="otros" class="control-label">Otros Datos:</label>
					<div class="controls">
						<textarea name="otros_udp" id="otros_udp" class='span10 counter' data-max="200" rows='3'></textarea>
					</div>
				</div>
			</fieldset>
		</form>
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-primary" data-dismiss="modal" onclick="update()">Actualizar</a>
	    <a href="#" class="btn btn-danger" data-dismiss="modal" >Salir</a>
	  </div>
	</div>
	
	<div class="modal hide" id="view">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal">�</button>
	    <h3>Ver Datos: Cliente</h3>
	  </div>
	  <div class="modal-body">
	    <div class='form-horizontal'>
			<fieldset>
				<div class="control-group">
					<label class="control-label">Doc Identidad:</label>
					<div class="controls">
						<input type="text" name="dni_v" id="dni_v" class='input-small' readonly style="background-color:#fff;">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Ruc:</label>
					<div class="controls">
						<input type="text" name="ruc_v" id="ruc_v" class='input-small' readonly style="background-color:#fff;">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Nombre y Apellidos:</label>
					<div class="controls">
						<input type="text" name="nombre_v" id="nombre_v" class='input-xlarge required' readonly style="background-color:#fff;">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Direccion:</label>
					<div class="controls">
						<input type="text" name="direccion_v" id="direccion_v" class='input-xlarge required' readonly style="background-color:#fff;">
					</div>
				</div>
				<div class="control-group">
					<label class="control-label">Telefono</label>
					<div class="controls">
						<input type="text" name="cboTipoTelf_v" id="cboTipoTelf_v" class='input-mini required' readonly style="background-color:#fff;">
						<input type="text" name="telefono_v" id="telefono_v" class='input-medium required' readonly style="background-color:#fff;">
					</div>
				</div>
				<div class="control-group">
					<label for="email" class="control-label">Email:</label>
					<div class="controls">
						<input type="text" name="email_v" id="email_v" class='input-xlarge' readonly style="background-color:#fff;">
					</div>
				</div>
				<div class="control-group">
					<label for="otros" class="control-label">Otros Datos:</label>
					<div class="controls">
						<textarea name="otros_v" id="otros_v" class='span10' rows='3' readonly style="background-color:#fff;"></textarea>
					</div>
				</div>
			</fieldset>
		</div>
	  </div>
	  <div class="modal-footer">
	    <a href="#" class="btn btn-danger" data-dismiss="modal">Salir</a>
	  </div>
	</div>
	
</div>


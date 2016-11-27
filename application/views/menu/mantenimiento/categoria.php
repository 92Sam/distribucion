<?php $ruta = base_url(); ?>
<script type="text/javascript">
	$(document).ready(function(){
		$("#btnGuardar").click(function(e){
			e.preventDefault();
			$.ajax({
				url:'<?php echo $ruta;?>'+'categoria/registrar',
				type: 'POST',
				data: $('#frmCategoria').serialize(),
				success:function(msj){
					if(msj == 'guardo'){
						jQuery.fn.reset = function(){
							$(this).each(function(){this.reset();});
						};
						$("#frmCategoria").reset();
						$("#mOK").modal('show');
					}else{
						$("#no").html("<div class='alert alert-block alert-danger'><a class='close' data-dismiss='alert' href='#'>�</a><h4 class='alert-heading'>Mensaje!</h4>Registro Fallido...</div>");
					}
				}
			});
		});
	});

	function obtenerCategoria(id){
		$.ajax({
			url:'<?php echo $ruta;?>'+'categoria/buscar_id',
			type: 'POST',
			data: "id="+id,
			success:function(msj){
				document.getElementById('codcat').value=id;
				document.getElementById('desc').value=msj;
				$("#edit").modal('show');
			}
		});
		return false;
	}
	
	function eliminar(id){
		$.ajax({
			url:'<?php echo $ruta;?>'+'categoria/eliminar',
			type: 'POST',
			data: "id="+id,
			success:function(msj){
				if(msj == 'elimino'){
					$("#ok1").click();
				}else{
					$("#no1").click();
				}
			}
		});
		return false;
	}

	function update(){
		$.ajax({
			url:'<?php echo $ruta;?>'+'categoria/update',
			type: 'POST',
			data: $('#frmCategoria_udp').serialize(),
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
			<h3>Lista Categorias</h3>
		</div>
		<div class="box-content box-nomargin">
			<div class="tab-content">
				<div class="tab-pane active" id="basic">
					<button id="ok1" style="display: none;" data-toggle="modal" href="#mOK1"></button>
					<button id="no1" style="display: none;" data-toggle="modal" href="#no1"></button>
					
					<table class='table table-striped table-media dataTable table-bordered table-condensed'>
						<thead>
							<tr>
								<th>Nro&nbsp;&nbsp;</th>
								<th>Descripci&oacute;n</th>
								<th>Accion</th>
							</tr>
						</thead>
						<tbody>
							<?php if(count($lstCategoria)>0):?>
							<?php foreach ($lstCategoria as $ca):?>
							 	<tr>
							 		<td style="text-align: center;"><?php echo $ca->nCatCodigo;?></td>
							 		<td><?php echo $ca->var_Categoria_Desc;?></td>
							  		<td class='actions_big'>
										<div class="btn-group">
                                            <a class="btn btn-default tip"
                                               onclick="obtenerCategoria(<?php echo $ca->nCatCodigo; ?>)"
                                               title="Editar"><img
                                                    src="<?php echo $ruta; ?>recursos/img/icons/essen/16/pencil.png"
                                                    alt=""></a>
                                            <a style="cursor:pointer;"
                                               onclick="eliminar(<?php echo $ca->nCatCodigo; ?>)"
                                               class='btn btn-default tip' title="Eliminar"><img
                                                    src="<?php echo $ruta; ?>recursos/img/icons/fugue/cross.png" alt=""></a>
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
						<a href="#c_natural" data-toggle="tab">Categoria</a>
					</li>
				</ul>
			</div>
			<div class="box-content">
				<div id="no"></div>
				<form id="frmCategoria" class='validate form-horizontal' method="post">
					<div class="control-group">
						<label for="descripcion" class="control-label">Descripci&oacute;n:</label>
						<div class="controls">
							<input type="text" name="descripcion" id="descripcion" class='span11 required'>
						</div>
					</div>
					<div class="form-actions">
						<button class='btn btn-primary' id="btnGuardar">Guardar</button>
						<input type="reset" class='btn btn-danger' value="Cancelar">
					</div>
				</form>
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
	
	<!-- Modal que sirve para editar cada categoria, seg�n la categoria seleccionada -->
	<div class="modal hide" id="edit">
	  <div class="modal-header">
	    <button type="button" class="close" data-dismiss="modal">&times;</button>
	    <h3>Editar Categoria</h3>
	  </div>
	  <div class="modal-body">
	    <form id="frmCategoria_udp" class='validate form-horizontal' method="post">
			<div class="control-group">
				<input type="text" name="codcat" id="codcat" style="display: none;">
				<label for="desc" class="control-label">Descripci&oacute;n:</label>
				<div class="controls">
					<input type="text" name="desc" id="desc" class='span11 required'>
				</div>
			</div>
		</form>
	  </div>
	  <div class="modal-footer">
	  	<!-- Este primer boton tiene el evento update que llama a una funcion javascript para enviar datos
	  	por metodo post, mediante ajax -->
	    <a href="#" class="btn btn-primary" data-dismiss="modal" onclick="update()">Actualizar</a>
	    <a href="#" class="btn" data-dismiss="modal" >Salir</a>
	  </div>
	</div>
	
</div>

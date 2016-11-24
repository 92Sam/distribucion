<?php $ruta = base_url(); ?>
<div class="table-responsive">
	<table class="table table-striped dataTable table-bordered" id="tablaresultado">
		<thead>
		<tr>
			<th>ID</th>
			<th>N&uacute;mero de Venta</th>
			<th>Cliente</th>
			<th>Vendedor</th>
			<th>Fecha</th>
			<th>Tipo de Documento</th>
			<th>Estatus</th>
			<th>Local</th>
			<th>Condici&oacute;n Pago</th>
			<th>Total</th>
			<th>Acciones</th>

		</tr>
		</thead>
		<tbody>
		<?php if (count($ventas) > 0) {
			foreach ($ventas as $venta) {
				$venta_id = $venta->venta_id;
				$venta_status = $venta->venta_status;
				?>
				<tr>
					<td><?= $venta->venta_id ?></td>
					<td><?= $venta->documento_Serie."-".$venta->documento_Numero ?></td>
					<td><?= $venta->razon_social ?></td>
					<td><?= $venta->nombre ?></td>
					<td><?= date('d-m-Y H:i:s', strtotime($venta->fecha)) ?></td>
					<td><?= $venta->nombre_tipo_documento ?></td>
					<?php if ($venta_status == 'EN ESPERA' || $venta_status == 'COMPLETADO') { ?>
						<td><a href="javascript:void(0)" class="edit_estatus_venta" id="<?php echo $venta_id; ?>"><?= $venta_status; ?></a></td>
                    <?php } else { ?>
						<td><?= $venta_status; ?></td>
					<?php } ?>
					<td><?= $venta->local_nombre ?></td>
					<td><?= $venta->nombre_condiciones ?></td>
					<td><?= $venta->total ?></td>
					<td>
                        <a style="cursor:pointer;" onclick="cargaData_Impresion(<?php echo $venta_id; ?>)"
                           class='btn btn-default tip' title="Ver Venta">
                            <i class="fa fa-search"></i> Nota de entrega
						</a>
                        <a style="cursor:pointer;" onclick="cargaData_DocumentoFiscal(<?php echo $venta->venta_id; ?>)"
                           class='btn btn-default tip' title="Ver Venta">
                            <i class="fa fa-search"></i>Boleta/Factura
                        </a>
					</td>
                </tr>
			<?php }
		} ?>
		</tbody>
	</table>
</div>


<a href="<?= $ruta; ?>venta/pdf/<?php if(isset($local)) echo $local; else echo 0;?>/<?php if(isset($fecha_desde)) echo $fecha_desde; else echo 0;?>
 /<?php if(isset($fecha_hasta)) echo $fecha_hasta; else echo 0;?> / <?php if(isset($estatus)) echo $estatus; else echo 0;?>/0"
   class="btn  btn-default btn-lg" data-toggle="tooltip" title="Exportar a PDF" data-original-title="fa fa-file-pdf-o"><i class="fa fa-file-pdf-o fa-fw"></i></a>
<a href="<?= $ruta; ?>venta/excel/<?php if(isset($local)) echo $local; else echo 0;?>/<?php if(isset($fecha_desde)) echo $fecha_desde; else echo 0;?>
 /<?php if(isset($fecha_hasta)) echo $fecha_hasta; else echo 0;?> / <?php if(isset($estatus)) echo $estatus; else echo 0;?>/0"
    class="btn btn-default btn-lg" data-toggle="tooltip" title="Exportar a Excel" data-original-title="fa fa-file-excel-o"><i class="fa fa-file-excel-o fa-fw"></i></a>
<div class="modal fade" id="mvisualizarVenta" tabindex="-1" role="dialog"
     aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>

<script type="text/javascript">
    $(function () {
        TablesDatatables.init(3);

        $('.edit_estatus_venta').editable('<?php echo $ruta; ?>api/pedidos/estatus', {
			indicator : '<img src="<?php echo $ruta; ?>recursos/editable/loading.gif">',
            data: "{'ANULADO':'ANULADO'}",
			type   : 'select',
			submit : 'OK',
			style  : "inherit",
			callback : function(value, settings) {
				console.log(value);
			}
		});


    });

    function generar() 
	{
        var fecha_desde = $("#fecha_desde").val();
        var fecha_hasta = $("#fecha_hasta").val();
        var locales = $("#locales").val();
        var estatus = $("#estatus").val();
        $("#agregargrupo").load('<?= $ruta; ?>venta/pdf/'+locales+'/'+fecha_desde+'/'+fecha_hasta+'/'+estatus);
        TablesDatatables.init();
    }

    function cargaData_Impresion(id_venta) 
	{
        $.ajax({
            url:  '<?php echo $ruta . 'venta/verVenta'; ?>',
            type: 'POST',
            data: "idventa=" + id_venta,
            success: function(data){
                $("#mvisualizarVenta").html(data);
                $("#mvisualizarVenta").modal('show');
            }
        });
    }

    function cargaData_DocumentoFiscal(id_venta) {
        $.ajax({
            url: '<?php echo $ruta . 'venta/verDocumentoFisal'; ?>',
            type: 'POST',
            data: "idventa=" + id_venta,
            success: function (data) {
                $("#mvisualizarVenta").html(data);
                $("#mvisualizarVenta").modal('show');
            }
        });
    }




</script>
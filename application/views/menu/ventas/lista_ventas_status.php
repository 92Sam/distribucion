<?php $ruta = base_url(); ?>

<div class="modal-dialog" style="width: 70%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Ventas en espera</h4>
        </div>

        <div class="modal-body">
            <div class="table-responsive">
                <table class="table table-striped dataTable table-bordered" id="tablaresultado">
                    <thead>
                    <tr>

                        <th>N&uacute;mero de Venta</th>
                        <th>Cliente</th>
                        <th>Vendedor</th>
                        <th>Fecha</th>
                        <th>Total</th>


                    </tr>
                    </thead>
                    <tbody id="selectable">
                    <?php if (count($ventas) > 0){

                        foreach ($ventas as $venta) {
                            ?>
                            <tr id="<?= $venta->venta_id?>">
                                <td><?= $venta->documento_Serie . "-" . $venta->documento_Numero ?></td>
                                <td><?= $venta->razon_social ?></td>
                                <td><?= $venta->nombre ?></td>

                                <td><?= date('d-m-Y H:i:s', strtotime($venta->fecha)) ?></td>

                                <td><?= $venta->total ?></td>



                            </tr>
                        <?php }
                    } ?>

                    </tbody>
                </table>

            </div>
        </div>
        <div class="modal-footer">

            <a href="#" id="abrir"
               class="btn  btn-default " data-toggle="tooltip" title="Abrir"
               data-original-title="fa fa-file-pdf-o"><i class="fa fa-folder-open"></i>Abrir</a>
            <a href="#"
               class="btn  btn-default" data-dismiss="modal" title="Cancelar"
               data-original-title="fa fa-file-pdf-o"><i class="fa fa-remove"></i>Cancelar</a>

        </div>
    </div>
</div>






<script type="text/javascript">
    $(function () {

        TablesDatatables.init();
        $(function() {
            $( "#selectable" ).selectable({
                stop: function() {


                         var id =$("#selectable tr.ui-selected").attr('id');
                        console.log( id );

                }
            });
        });

        $("#abrir").click(function(){
            var id =$("#selectable tr.ui-selected").attr('id');
            $.ajax({
                type: 'POST',
                data:{'idventa':id},
                dataType:'json',
                url: ruta + 'venta/verVentaJson',
                success: function (data) {


                       /// $("#frmVenta").reset();
                   $("#selectproductos").val('').trigger("chosen:updated");
                    $("#idventa").val(data.ventas[0].venta_id);
                    $("#tipo_documento").val(data.ventas[0].descripcion).trigger("chosen:updated");
                    $("#id_cliente").val(data.ventas[0].cliente_id).trigger("chosen:updated");
                    $("#cboModPag").val(data.ventas[0].id_condiciones).trigger("chosen:updated");
                    $("#venta_status").val(data.ventas[0].venta_status).trigger("chosen:updated");
                    $("#fecha").val(data.ventas[0].fechaemision);
                    $("#subTotal").val(data.ventas[0].subTotal);
                    $("#montoigv").val(data.ventas[0].impuesto);
                    $("#totApagar").val(data.ventas[0].montoTotal);
                    lst_producto = new Array();
                    $("#tbodyproductos").html('');
                    for(var i=0;i<data.ventas.length;i++){
                        var producto_id=data.ventas[i].producto_id;
                        var producto_nombre=data.ventas[i].nombre;
                        var unidad_id=data.ventas[i].id_unidad;
                        var unidad_nombre=data.ventas[i].nombre_unidad;
                        var cantidad=data.ventas[i].cantidad;
                        var precio=data.ventas[i].preciounitario;
                        //var unidaddescuento=0;
                        var subtotal=parseFloat(data.ventas[i].importe);
                        var porcentaje_impuesto=parseFloat(data.ventas[i].porcentaje_impuesto);
                        var unidades=parseFloat(data.ventas[i].unidades);
                      //  var precio_id=data.ventas[i].importe;

                        calculatotales(producto_id,producto_nombre,unidad_id,unidad_nombre,cantidad,precio,subtotal,porcentaje_impuesto, unidades)


                    }

                    $("#ventasabiertas").modal('hide');
                }
            });
        })
    });

<?php $ruta = base_url(); ?>

<ul class="breadcrumb breadcrumb-top">
    <li>Pagos Adelantados</li>
</ul>
<div class="row">
    <div class="col-xs-12">
        <div class="alert alert-success alert-dismissable" id="success"
             style="display:<?php echo isset($success) ? 'block' : 'none' ?>">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">X</button>
            <h4><i class="icon fa fa-check"></i> Operaci&oacute;n realizada</h4>
            <span id="successspan"><?php echo isset($success) ? $success : '' ?></div>
        </span>
    </div>
</div>
<?php
echo validation_errors('<div class="alert alert-danger alert-dismissable"">', "</div>");
?>
<div class="block">
    <!-- Progress Bars Wizard Title -->
    <div class="form-group row">

        <div class="col-md-2" >
           Nombre Vendedor
        </div>
        <div class="col-md-2">
            <select id="vendedor" class="form-control  campos" >
                <option value=0 >Seleccionar</option>
                <?php
                    foreach($ltsVendedores as $vendedres){
                        echo '<option value="'.$vendedres['nUsuCodigo'].'">'.$vendedres['nombre'].'</option>';
                    }
                ?>
            </select>
        </div>

        <div class="col-md-2" >
           Nombre Cliente
        </div>
        <div class="col-md-2">
            <select id="cliente" class="form-control  campos" >
                <option value=0 >Seleccionar</option>
                <?php
                foreach($ltsClientes as $clientes){
                    echo '<option value="'.$clientes['id_cliente'].'">'.$clientes['razon_social'].'</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <div class="form-group row">
        <div class="col-md-2" >
            Fecha de operacion
        </div>
        <div class="col-md-2">
            <input type="text" name="fecha" id="fecha" class="form-control fecha campos"  />
        </div>

        <div class="col-md-2" st>
           Estado de pago
        </div>
        <div class="col-md-2" class="form-control campos">
            <select id="estado" class="form-control  campos">
                <option value="">Seleccionar</option>
                <option value="CONFIRMADO" >CONFIRMADO</option>
                <option value="PENDIENTE" selected >PENDIENTE</option>
            </select>
        </div>
    </div>
</div>

<div class="block">
    <div class="table-responsive" id="tablaresultado">



    </div>
</div>



<div class="modal fade" id="consolidadoLiquidacion" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>
<div class="modal fade" id="pagoCaja" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>
<div class="modal fade" id="verpago" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
     aria-hidden="true">
</div>
<script>
    $(function () {
        recargarlista();
        TablesDatatables.init();

        $(".fecha").datepicker({
            format: 'yyyy-mm-dd'
        });
        $(".campos").on("change",function(){

            recargarlista();

        });

    });

    function pagoCaja(id){

        $("#pagoCaja").load('<?= $ruta ?>venta/pagoCaja/' + id );
        $('#pagoCaja').modal('show');
    }
    function pagoBanco(id){
        $("#pagoCaja").load('<?= $ruta ?>venta/pagoBanco/' + id );
        $('#pagoCaja').modal('show');
    }
    function recargarlista() {
        var pedido = $("#idPedido").val();
        var vendedor = $("#vendedor").val();
        var cliente = $("#cliente").val();
        var fecha = $("#fecha").val();
        var estado = $("#estado").val();

        $.ajax({
            url: '<?= base_url()?>venta/filtroPagosAdl',
            data: {
                'pedido': pedido,
                'vendedor': vendedor,
                'cliente': cliente,
                'fecha': fecha,
                'estado': estado
            },
            type: 'POST',
            success: function (data) {

                if (data.length > 0)
                    $("#tablaresultado").html(data);

            },
            error: function () {

                alert('Ocurrio un error por favor intente nuevamente');
            }
        })
    }
</script>
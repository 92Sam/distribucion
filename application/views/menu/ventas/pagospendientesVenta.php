<?php $ruta = base_url(); ?>


<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <form id="frmBuscar">
                <div class="block-title">
                    <h3>PAGOS PENDIENTES</h3>
                </div>


                <div class="row">
                    <div class="col-md-1">
                        <label>Desde</label>
                    </div>
                    <div class="col-md-2">

                        <input type="text" name="fecIni" id="fecIni" class='input-small form-control input-datepicker' value="<?php echo date('d-m-Y')?>">
                    </div>
                    <div class="col-md-1">
                        <label>Hasta</label>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="fecFin" id="fecFin" class='form-control input-datepicker' value="<?php echo date('d-m-Y')?>">
                    </div>
                    <div class="col-md-3">

                        <select name="cboCliente" id="cboCliente" class='cho form-control'>
                            <option value="-1">Seleccionar</option>
                            <?php if (count($lstCliente) > 0): ?>
                                <?php foreach ($lstCliente as $cl): ?>
                                    <option
                                        value="<?php echo $cl['id_cliente']; ?>"><?php echo $cl['razon_social']; ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <button id="btnBuscar" class="btn btn-default">Buscar</button>

                </div>
            </form>
        </div>


        <div class="block">
            <div class="block-section">
                <div id="lstTabla" class="table-responsive"></div>

                <div id="pp_excel">
                    <form action="<?php echo $ruta; ?>venta/toExcel_pagoPendiente" name="frmExcel"
                          id="frmExcel" method="post">
                        <input type="hidden" name="fecIni1" id="fecIni1" class='input-small'>
                        <input type="hidden" name="fecFin1" id="fecFin1" class='input-small'>
                        <input type="hidden" name="cboCliente1" id="cboCliente1" value="-1" class='input-small'>
                    </form>
                </div>


                <div id="pp_pdf">
                    <form name="frmPDF" id="frmPDF"
                          action="<?php echo $ruta; ?>venta/pagospendientepdf" target="_blank"
                          method="post">
                        <input type="hidden" name="fecIni2" id="fecIni2" class='input-small'>
                        <input type="hidden" name="fecFin2" id="fecFin2" class='input-small'>
                        <input type="hidden" name="cboCliente2" id="cboCliente2" value="-1" class='input-small'>
                    </form>
                </div>
                <a href="#" onclick="generar_reporte_excel();" class='tip btn btn-default'
                   title="Exportar a Excel"><i class="fa fa-file-excel-o"></i></a>
                <a href="#" onclick="generar_reporte_pdf();" class='btn btn-default tip btn-default'
                   title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i> </a>

            </div>
        </div>
    </div>


</div>


<script>

    $(document).ready(function() {
        buscar();
        $('select').chosen();
        $(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});

        $("#abrir_exportar").hide();

        $("#btnBuscar").click(function (e) {
            e.preventDefault();
            buscar();

        });



    });

    function cerrar_detalle_historial() {

        $('#visualizar_cada_historial').modal('hide');
    }
    function buscar() {

        document.getElementById('fecIni1').value = $("#fecIni").val();
        document.getElementById('fecFin1').value = $("#fecFin").val();
        document.getElementById('fecIni2').value = $("#fecIni").val();
        document.getElementById('fecFin2').value = $("#fecFin").val();
        document.getElementById('cboCliente2').value = $("#cboCliente").val();
        document.getElementById('cboCliente1').value = $("#cboCliente").val();

        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>'+'venta/lst_reg_pagospendientes',
            success: function(data) {
                $("#lstTabla").html(data);
            }
        });
    }



    function visualizar_monto_abonado(id_historial, id_venta) {

        $.ajax({
            type: 'POST',
            data: {'id_historial': id_historial, 'id_venta': id_venta},
            url: '<?php echo base_url();?>' + 'venta/imprimir_pago_pendiente',
            success: function (data) {
                $("#visualizar_cada_historial").html(data);
                $('#visualizar_cada_historial').modal('show');
            }
        });
    }

    function generar_reporte_excel() {
        document.getElementById("frmExcel").submit();
    }

    function generar_reporte_pdf() {
        document.getElementById("frmPDF").submit();
    }


</script>
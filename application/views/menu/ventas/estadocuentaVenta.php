<?php $ruta = base_url(); ?>


<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <form id="frmBuscar">
                <div class="block-title">
                    <h3>ESTADO DE CUENTAS</h3>
                </div>
                <div class="block-section block-alt-noborder">
                    <div class="row">
                        <div class="col-md-1">
                            <span class="add-on">Fechas:</span>
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="fecIni" id="fecIni"
                                   class='form-control' value="<?php echo date('d-m-Y')?>">
                        </div>
                        <div class="col-md-1">
                           hasta
                        </div>
                        <div class="col-md-2">
                            <input type="text" name="fecFin" id="fecFin" class='form-control' value="<?php echo date('d-m-Y')?>">
                        </div>
                        <div class="col-md-3">
                        <select name="cboCliente" id="cboCliente" class='form-control '>
                            <option value="-1">Seleccionar</option>
                            <?php if (count($lstCliente) > 0): ?>
                                <?php foreach ($lstCliente as $cl): ?>
                                    <option value="<?php echo $cl['id_cliente']; ?>"><?php echo $cl['razon_social']; ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select></div>

                        <button id="btnBuscar" class="btn btn-default" >Buscar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="row-fluid">
    <div class="span12">
        <div class="block">

            <div class="block-section block-alt-noborder">
                <div id="lstTabla" class="table-responsive">
                </div>
            </div>


        </div>
    </div>
</div>
<div class="actions">

    <div id="ec_excel">
        <form action="<?php echo $ruta; ?>venta/toExcel_estadoCuenta" name="frmExcel"
              id="frmExcel" method="post">
            <input type="hidden" name="fecIni1" id="fecIni1" class='input-small'>
            <input type="hidden" name="fecFin1" id="fecFin1" class='input-small'>
            <input type="hidden" name="cboCliente1" id="cboCliente1" value="-1" class='input-small'>
        </form>
    </div>

    <div id="ec_pdf">
        <form name="frmPDF" id="frmPDF" action="<?php echo $ruta; ?>venta/toPDF_estadoCuenta"
              target="_blank" method="post">
            <input type="hidden" name="fecIni2" id="fecIni2" class='input-small'>
            <input type="hidden" name="fecFin2" id="fecFin2" class='input-small'>
            <input type="hidden" name="cboCliente2" id="cboCliente2" value="-1" class='input-small'>
        </form>
    </div>
    <a href="#" onclick="generar_reporte_excel();" class='tip btn btn-default'
       title="Exportar a Excel"><i class="fa fa-file-excel-o"></i> </a>

    <a href="#" onclick="generar_reporte_pdf();" class='btn btn-default tip'
       title="Exportar a PDF"><i class="fa fa-file-pdf-o"></i></a>

</div>

<script>

    $(document).ready(function () {
        buscar();
        $('select').chosen();
        $("#fecIni").datepicker({format: 'dd-mm-yyyy'});
        $("#fecFin").datepicker({format: 'dd-mm-yyyy'});

        $("#btnBuscar").click(function (e) {
            e.preventDefault();
           buscar();
        });
    });

    function buscar(){

        document.getElementById('fecIni1').value = $("#fecIni").val();
        document.getElementById('fecFin1').value = $("#fecFin").val();
        document.getElementById('fecIni2').value = $("#fecIni").val();
        document.getElementById('fecFin2').value = $("#fecFin").val();
        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'venta/lst_reg_estadocuenta',
            success: function (data) {
                $("#lstTabla").html(data);
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
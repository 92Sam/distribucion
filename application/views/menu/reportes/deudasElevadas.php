<?php $ruta = base_url(); ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#abrir_exportar").hide();
        $("#btnBuscar").click(function (e) {
            e.preventDefault()
            buscar();

        });
        $("#todos").click(function (e) {
            e.preventDefault()
            buscartodos();

        });


    });

    function cerrar_detalle_historial() {

        $('#visualizar_cada_historial').modal('hide');
    }
    function buscar() {

        var fechaini = $('#fecIni').val();
        var fechafin = $('#fecFin').val();

        var urlpdf = '<?php echo base_url();?>' + 'venta/deudaselevadaspdf/';
        if (fechaini != "") {
            urlpdf = urlpdf + fechaini + "/";
        } else {
            urlpdf = urlpdf + "0/";
        }
        if (fechafin != "") {
            urlpdf = urlpdf + fechafin + "/";
        } else {
            urlpdf = urlpdf + "0/";
        }
        urlpdf = urlpdf + $("#cboTrabajador").val();
        urlpdf = urlpdf + $("#cboZona").val();

        var urlexcel = '<?php echo base_url();?>' + 'venta/deudasElevadasexcel/';
        if (fechaini != "") {
            urlexcel = urlexcel + fechaini + "/";
        } else {
            urlexcel = urlexcel + "0/";
        }
        if (fechafin != "") {
            urlexcel = urlexcel + fechafin + "/";
        } else {
            urlexcel = urlexcel + "0/";
        }
        urlexcel = urlexcel + $("#cboTrabajador").val();


        $.ajax({
            type: 'POST',
            data: $('#frmBuscar').serialize(),
            url: '<?php echo base_url();?>' + 'venta/lst_reg_deudaselevadas',
            success: function (data) {

                setTimeout(function () {
                    $("#pdf").attr('href', urlpdf);
                    $("#excel").attr('href', urlexcel);
                }, 1);
                $("#abrir_exportar").show();

                $("#lstTabla").html(data);
            }
        });
    }

    function buscartodos() {

        $.ajax({
            type: 'POST',
            data: {'cboTrabajador': -1, 'cboZona': -1, 'fecIni': "", 'fecFin': ""},
            url: '<?php echo base_url();?>' + 'venta/lst_reg_deudaselevadas',
            success: function (data) {

                setTimeout(function () {
                    $("#pdf").attr('href', '<?php echo base_url();?>' + 'venta/deudaselevadaspdf/0/0/-1/-1');
                    $("#excel").attr('href', '<?php echo base_url();?>' + 'venta/deudasElevadasexcel/0/0/-1/-1');
                }, 1);
                $("#abrir_exportar").show();

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
<link rel="stylesheet" href="<?= $ruta ?>recursos/css/plugins.css">
<div class="row-fluid">
    <div class="span12">
        <div class="block">
            <form id="frmBuscar">
                <div class="block-title">
                    <h3>DEUDAS ELEVADAS</h3>
                </div>


                <div class="row">
                    <div class="col-md-3">
                        <label>Desde</label>
                    </div>
                    <div class="col-md-3">

                        <input type="text" name="fecIni" id="fecIni" class='input-small form-control input-datepicker'>
                    </div>
                    <div class="col-md-3">
                        <label>Hasta</label>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="fecFin" id="fecFin" class='form-control input-datepicker'>
                    </div>
                    <div class="col-md-3">
                        <label>Vendedor</label>
                    </div>
                    <div class="col-md-3">

                        <select name="cboTrabajador" id="cboTrabajador" class='cho form-control'>
                            <option value="-1">Vendedor</option>
                            <?php if (count($lstTrabajador) > 0): ?>
                                <?php foreach ($lstTrabajador as $cl): ?>
                                    <option
                                        value="<?php echo $cl['nUsuCodigo']; ?>"><?php echo $cl['nombre']; ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select>

                    </div>
                    <div class="col-md-3">
                        <label>Zona</label>
                    </div>
                    <div class="col-md-3">
                        <select name="cboZona" id="cboZona" class='cho form-control'>
                            <option value="-1">Zona</option>
                            <?php if (count($zonas) > 0): ?>
                                <?php foreach ($zonas as $zona): ?>
                                    <option
                                        value="<?php echo $zona['zona_id']; ?>"><?php echo $zona['zona_nombre']; ?></option>
                                <?php endforeach; ?>
                            <?php else : ?>
                            <?php endif; ?>
                        </select>

                    </div>
                    <div class="col-md-3">
                        <button id="btnBuscar" class="btn btn-default">Buscar</button>

                    </div>
                </div>
            </form>
        </div>

        <div class="span12">
            <div class="block">


                <button id="todos" class="btn btn-default">Todos</button>

                <div id="lstTabla" class="table-responsive"></div>


                <div class="block-section">
                    <div class="row" id="abrir_exportar">
                        <div class="col-md-1">
                            <div id="pp_excel">
                                <a class='btn btn-default btn-lg'
                                   title="Exportar a Excel" id="excel"><i class="fa fa-file-excel-o"></i></a>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <div id="pp_pdf">
                                <a class='btn btn-default btn-lg'
                                   title="Exportar a PDF" id="pdf"><i class="fa fa-file-pdf-o"></i> </a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>


<script>
    $(document).ready(function () {
        $('select').chosen();
        $(".input-datepicker").datepicker({format: 'dd-mm-yyyy'});
    })
</script>
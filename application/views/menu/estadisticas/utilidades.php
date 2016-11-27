<?php $ruta = base_url(); ?>
<ul class="breadcrumb breadcrumb-top">
    <li>Reportes</li>
    <li><a href="">Estadistica de utilidades</a></li>
</ul>

<div class="block">
    <div class="block-title">
        <h3>Utilidades/Estad&iacute;sticas</h3></div>
    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <!-- Widget -->
            <div class="widget">
                <div class="widget-extra themed-background-dark-default text-center">
                    <h3 class="widget-content-light">Utilidad por <strong>Producto</strong></h3>
                </div>
                <div class="widget-extra-full text-center">
                    <div id="productos" style="height:250px"></div>
                </div>
            </div>
            <!-- END Widget -->
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <!-- Widget -->
            <div class="widget">
                <div class="widget-extra themed-background-dark-default text-center">
                    <h3 class="widget-content-light">Utilidad por <strong>Cliente</strong></h3>
                </div>
                <div class="widget-extra-full text-center">
                    <div id="clientes" style="height:250px"></div>
                </div>
            </div>
            <!-- END Widget -->
        </div>
    </div>


    <div class="row">
        <div class="col-sm-12 col-lg-12">
            <!-- Widget -->
            <div class="widget">
                <div class="widget-extra themed-background-dark-default text-center">
                    <h3 class="widget-content-light">Utilidad por <strong>Proveedor</strong></h3>
                </div>
                <div class="widget-extra-full text-center">
                    <div id="proveedores" style="height:250px"></div>
                </div>
            </div>
            <!-- END Widget -->
        </div>
    </div>


    </div>
</div>

<script src="<?php echo $ruta ?>recursos/js/pages/widgetsStats.js"></script>
<script src="<?php echo $ruta ?>recursos/js/jquery.flot.categories.js"></script>


<script>



    // Add the Flot version string to the footer


    $(document).ready(function () {
        $.ajax({
            url: "<?= base_url()?>estadisticas/utilidades",
            type: "POST",
            async: false,
            data: {'utilidades': 'PRODUCTOS'},
            dataType: 'JSON',

            success: function (data) {

                $.plot("#productos", [ data ], {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            align: "center"
                        }
                    },
                    xaxis: {
                        mode: "categories",
                        tickLength: 0
                    },
                    colors: ['#9b59b6'],
                    legend: {show: true, position: 'nw', margin: [15, 10]},
                    grid: {borderWidth: 0}

                });

            }
        })


        $.ajax({
            url: "<?= base_url()?>estadisticas/utilidades",
            type: "POST",
            async: false,
            data: {'utilidades': 'CLIENTE'},
            dataType: 'JSON',

            success: function (data) {

                $.plot("#clientes", [ data ], {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            align: "center"
                        }
                    },
                    xaxis: {
                        mode: "categories",
                        tickLength: 0
                    },
                    colors: ['#9sd9b6'],
                    legend: {show: true, position: 'nw', margin: [15, 10]},
                    grid: {borderWidth: 0}
                });

            }
        })



        $.ajax({
            url: "<?= base_url()?>estadisticas/utilidades",
            type: "POST",
            async: false,
            data: {'utilidades': 'PROVEEDOR'},
            dataType: 'JSON',

            success: function (data) {

                $.plot("#proveedores", [ data ], {
                    series: {
                        bars: {
                            show: true,
                            barWidth: 0.6,
                            align: "center"
                        }
                    },
                    xaxis: {
                        mode: "categories",
                        tickLength: 0
                    },
                    colors: ['#4432ff'],
                    legend: {show: true, position: 'nw', margin: [15, 10]},
                    grid: {borderWidth: 0}
                });

            }
        })


    })


</script>
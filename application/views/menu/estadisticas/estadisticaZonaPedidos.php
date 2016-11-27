<?php $ruta = base_url(); ?>

<div class="modal-dialog">
    <div class="modal-content">

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        </div>

        <div class="modal-body">

            <div class="row">
                <div class="col-sm-12 col-lg-12">
                    <!-- Widget -->
                    <div class="widget">
                        <div class="widget-extra themed-background-dark-default text-center">
                            <h3 class="widget-content-light">Zonas con más <strong>Pedidos</strong></h3>
                        </div>
                        <div class="widget-extra-full text-center">
                            <div id="productos" style="height:250px"></div>
                        </div>
                    </div>
                    <!-- END Widget -->
                </div>
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
            url: "<?= base_url()?>estadisticas/pedidoZona",
            type: "POST",
            async: false,
            data: {'utilidades': 'PRODUCTOS'},
            dataType: 'JSON',

            success: function (data) {

                var zonas = [];

                for (var i = 0; i < data.length;  i++) {
                    zonas[i] = { label: data[i][0],
                             data: data[i]
                    }  
                }

                    $.plot("#productos", zonas
                        ,
                        {
                            legend: {show: true},
                            series: {
                                pie: {
                                    show: true,
                                    radius: 1,
                                        label: {
                                            show: true,
                                            radius: 1,
                                            formatter: function(label, pieSeries) {
                                                return '<div class="chart-pie-label">' + Math.round(pieSeries.percent) + '%</div>';
                                            },
                                            background: {opacity: 0.75, color: '#000000'}
                                         }
                                }
                            }

                        }
                    ); 

            }

        })

    })

</script>
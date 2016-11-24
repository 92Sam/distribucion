<?php $ruta = base_url(); ?>
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Evolucion de costos de compra y Precios de venta</h4>
        </div>
        <div class="modal-body">
            <div id="eti">
                <?php

                foreach($productos as $producto){
                     $id = $producto['productoV'];
                }


                ?>
            </div>
            <div id="chart-classic" class="chart"></div>

        </div>
    </div>
</div>
<script type="text/javascript">

    // Get the elements where we will attach the charts
    var chartClassic = $("#chart-classic");

    var chartMonths = [[1, 'Jan'], [2, 'Feb'], [3, 'Mar'], [4, 'Apr'], [5, 'May'], [6, 'Jun'], [7, 'Jul'], [8, 'Aug'], [9, 'Sep'], [10, 'Oct'], [11, 'Nov'], [12, 'Dic']];


    // Classic Chart
    $(document).ready(function () {

        $.ajax({
            url: "<?php echo base_url()?>producto/costos_json/<?= $id ?>",
            type: "POST",
            async: false,
            data: {'evolucion': 'EARNINGS'},
            dataType: 'JSON',

            success: function (data) {
                console.log(data);
                $.plot(chartClassic,
                    [
                        {
                            label: 'Precios de ventas',
                            data: data.ventas,
                            lines: {show: true, fill: true, fillColor: {colors: [{opacity: 0.25}, {opacity: 0.25}]}},
                            points: {show: true, radius: 6}
                        },
                        {
                            label: 'Costos de compra',
                            data: data.compras,
                            lines: {show: true, fill: true, fillColor: {colors: [{opacity: 0.15}, {opacity: 0.15}]}},
                            points: {show: true, radius: 6},

                        }
                    ],
                    {
                        colors: ['#3498db', '#333333'],
                        legend: {show: true, position: 'nw', margin: [15, 10]},
                        grid: {borderWidth: 0, hoverable: true, clickable: true},
                        yaxis: {ticks: 4, tickColor: '#eeeeee'},
                        xaxis: {ticks: chartMonths, tickColor: '#ffffff'}
                    }
                );


                // Creating and attaching a tooltip to the classic chart
                var previousPoint = null, ttlabel = null;
                chartClassic.bind('plothover', function (event, pos, item) {

                    if (item) {
                        if (previousPoint !== item.dataIndex) {
                            previousPoint = item.dataIndex;

                            $('#chart-tooltip').remove();
                            var x = item.datapoint[0], y = item.datapoint[1];

                            if (item.seriesIndex === 1) {
                                ttlabel = '<strong>' + y + '</strong> sales';
                            } else {
                                ttlabel = '$ <strong>' + y + '</strong>';
                            }

                            $('<div id="chart-tooltip" class="chart-tooltip">' + ttlabel + '</div>')
                                .css({top: item.pageY - 45, left: item.pageX + 5}).appendTo("body").show();
                        }
                    }
                    else {
                        $('#chart-tooltip').remove();
                        previousPoint = null;
                    }
                });

            }
        });
    });


</script>
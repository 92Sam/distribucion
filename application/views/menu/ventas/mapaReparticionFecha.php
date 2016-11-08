<?php $ruta = base_url(); ?>

<div class="block">


    <?php

    if(empty($consolidado)){
        ?>
        <h4 id="msj" class="text-danger">No hay clientes por atender para las fechas seleccionadas</h4>
        <?php
    }
    else{
    ?>
    <div id="gmap-markers" class="gmap" style="width: 100%; height: 400px;">

    </div>
    <?php }?>

    <?php
    foreach ($consolidado as $campos) {


        $la = $campos['latitud'];
        $lo = $campos['longitud'];
    }
    ?>

    <button class="btn btn-success"  id="start_travel" data-toggle="tooltip" data-original-title="fa fa-comment-o" href="#" style="display:none;">Mostrar Como Llegar</button>
    <ul id="instructions"></ul>
</div>
<script type="text/javascript">
    var map;
    $(function () {
        new GMaps({
            div: '#gmap-markers',
            zoom: 10,
            lat: <?=$la ?>,
            lng: <?=$lo ?>,
            scrollwheel: false
        }).addMarkers([
                <?php
             $fechaActual = date('d-m-Y');
             foreach ($consolidado as $campos) {
                $fecha = date('d-m-Y',strtotime ($campos['fechaConsolidado']));
                ?>
                {
                    lat: <?php echo $campos['latitud']; ?>,
                    lng: <?php echo $campos['longitud']; ?>,
                    title: '<?php echo $campos['razon_social']; ?>',
                    animation: google.maps.Animation.DROP,
                    click: function(e){
                        map =  new GMaps({
                            div: '#gmap-markers',
                            zoom: 11,
                            lat: <?=$la ?>,
                            lng: <?=$lo ?>

                        });
                        GMaps.geolocate({
                            success: function(position) {
                                map.drawRoute({
                                    origin: [position.coords.latitude, position.coords.longitude],
                                    destination: [<?php echo $campos['latitud'];?> , <?php echo $campos['longitud'];?>],
                                    travelMode: 'driving',
                                    strokeColor: '#131540',
                                    strokeOpacity: 0.7,
                                    strokeWeight: 6
                                });
                                $('#start_travel').css("display","inline-block");
                                $('#start_travel').click(function(e){
                                    map.travelRoute({
                                        origin: [position.coords.latitude, position.coords.longitude],
                                        destination: [<?php echo $campos['latitud'];?> , <?php echo $campos['longitud'];?>],
                                        travelMode: 'driving',
                                        step: function(e) {
                                            $('#instructions').append('<li>'+e.instructions+'</li>');
                                            $('#instructions li:eq('+e.step_number+')').delay(450*e.step_number).fadeIn(200, function() {
                                                $('#start_travel').css("display","none");
                                                map.drawPolyline({
                                                    path: e.path,
                                                    strokeColor: '#131540',
                                                    strokeOpacity:0.6,
                                                    strokeWeight:6
                                                });
                                            });
                                        }
                                    });
                                });

                            },
                            error: function(error) {
                                alert('geolocalizacion fallo: '+error.message);
                            },
                            not_supported: function() {
                                alert("Su navegador no soporta geolocalizacion");
                            },
                            always: function() {


                            }
                        }); // FIN DEL GEOLOCATE
                    } , //FINAL DEL CLICK
                    infoWindow: {content: '<strong><?php echo $campos['razon_social']; ?></strong>'}
                },
                <?php
                }?>
            ]);
    });
    </script>

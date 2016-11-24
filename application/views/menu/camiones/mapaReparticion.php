<?php $ruta = base_url(); ?>

<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Mapa</h4>
        </div>
        <div class="modal-body">

            <div id="gmap-markers" class="gmap" style="width: 100%; height: 400px;">
                <?php
                foreach ($puntoMapa as $punto) {
                    $la = $punto['latitud'];
                    $lo = $punto['longitud'];
                }
                ?>
            </div>
            <button class="btn btn-success" id="start_travel" data-toggle="tooltip"
                    data-original-title="fa fa-comment-o"
                    href="#" style="display:none;">Mostrar Como llegar
            </button>
            <ul id="instructions"></ul>


        </div>
    </div>


    <script type="text/javascript">

        $(function () {

            var map = new GMaps({
                div: '#gmap-markers',
                zoom: 9,
                lat: <?=$la ?>,
                lng: <?=$lo ?>,
                scrollwheel: false
            }).addMarkers([

                <?php
               foreach ($puntoMapa as $punto) {

              ?>
                {
                    lat: <?php echo $punto['latitud']; ?>,
                    lng: <?php echo $punto['longitud']; ?>,
                    title: '<?php echo $punto['razon_social']; ?>',
                    animation: google.maps.Animation.DROP,
                    click: function (e) {
                        map = new GMaps({
                            div: '#gmap-markers',
                            zoom: 11,
                            lat: <?=$la ?>,
                            lng: <?=$lo ?>

                        });
                        GMaps.geolocate({
                            success: function (position) {
                                map.drawRoute({
                                    origin: [position.coords.latitude, position.coords.longitude],
                                    destination: [<?php echo $punto['latitud'];?>, <?php echo $punto['longitud'];?>],
                                    travelMode: 'driving',
                                    strokeColor: '#131540',
                                    strokeOpacity: 0.7,
                                    strokeWeight: 6
                                });
                                $('#start_travel').css("display", "inline-block");
                                $('#start_travel').click(function (e) {
                                    map.travelRoute({
                                        origin: [position.coords.latitude, position.coords.longitude],
                                        destination: [<?php echo $punto['latitud'];?>, <?php echo $punto['longitud'];?>],
                                        travelMode: 'driving',
                                        step: function (e) {
                                            $('#instructions').append('<li>' + e.instructions + '</li>');
                                            $('#instructions li:eq(' + e.step_number + ')').delay(450 * e.step_number).fadeIn(200, function () {
                                                $('#start_travel').css("display", "none");
                                                map.drawPolyline({
                                                    path: e.path,
                                                    strokeColor: '#131540',
                                                    strokeOpacity: 0.6,
                                                    strokeWeight: 6
                                                });
                                            });
                                        }
                                    });
                                });

                            },
                            error: function (error) {
                                alert('geolocalizacion fallo: ' + error.message);
                            },
                            not_supported: function () {
                                alert("Su navegador no soporta geolocalizacion");
                            },
                            always: function () {

                            }
                        }); // FIN DEL GEOLOCATE
                    }, //FINAL DEL CLICK
                    infoWindow: {content: '<strong><?php echo $punto['razon_social']; ?></strong>'}
                },
                <?php
                 } ?>
            ]);


           /* map.on('marker_added', function (marker) {
                var index = map.markers.indexOf(marker);
                if (index == map.markers.length - 1) {
                    map.fitZoom();
                }
            });*/
        });

    </script>

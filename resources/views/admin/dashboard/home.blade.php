@extends('layouts.app')

@section('title')
    Dashboard | Fletes RGC - Empresa Mexicana especializada en el autotransporte de carga terrestre.
@endsection

@section('page_active')
    Dashboard
@endsection
@section('subpage_active')
    Home
@endsection


@section('css')
@endsection

@section('content')
    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">

            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span class="float-start mt-3">Total de Rutas </span>
                                <h4 class="fw-normal mb-1"> 84 </h2>
                                    <p class="text-muted mb-3">Rutas de hoy</p>
                            </div>
                            <div class="progress progress-bar-alt-info progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="77" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 77%;">
                                    <span class="visually-hidden">77% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span class="float-start mt-3">Total de Cargas </span>
                                <h4 class="fw-normal mb-1"> 150 </h2>
                                    <p class="text-muted mb-3">Cargas de hoy</p>
                            </div>
                            <div class="progress progress-bar-alt-info progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="45" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 45%;">
                                    <span class="visually-hidden">45% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->


            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span class="float-start mt-3">Total de Tickets </span>
                                <h4 class="fw-normal mb-1"> 2 </h2>
                                    <p class="text-muted mb-3">Tickets de hoy</p>
                            </div>
                            <div class="progress progress-bar-alt-info progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="5" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 5%;">
                                    <span class="visually-hidden">5% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->


        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">Grafica de Viajes</h4>
                        <div id="morris-bar-example" dir="ltr" style="height: 280px;" class="morris-chart"></div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">

                        <h4 class="header-title mt-0 mb-3">Ultimos viajes realizados</h4>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Unidad</th>
                                        <th>Origen</th>
                                        <th>Destino</th>
                                        <th>Operador</th>
                                        <th>Destinatario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>U/55</td>
                                        <td>Monterrey NL</td>
                                        <td>Merida</td>
                                        <td>ICECOOL</td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td>01/01/2017</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>U/55</td>
                                        <td>Monterrey NL</td>
                                        <td>Merida</td>
                                        <td>ICECOOL</td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td>01/01/2017</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>U/55</td>
                                        <td>Monterrey NL</td>
                                        <td>Merida</td>
                                        <td>ICECOOL</td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td>01/01/2017</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>U/55</td>
                                        <td>Monterrey NL</td>
                                        <td>Merida</td>
                                        <td>ICECOOL</td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td>01/01/2017</td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->
        </div>
        <!-- end row -->


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mb-3">Control de GPS activos</h4>
                        <div id="map" style="width:100%;height:500px;"></div>
                    </div> <!-- end card-body-->
                </div> <!-- end card-->
            </div> <!-- end col-->
        </div>
        <!-- end row-->
    </div>
@endsection

@section('js')
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        let lat = 31.326015;
        let lng = 75.576180;
        var map;

        function initMap() {
            var marker;
            let h3index;

            var geocoder = new google.maps.Geocoder;
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;
                    map = new google.maps.Map(
                        document.getElementById('map'), {
                            center: {
                                lat: lat,
                                lng: lng
                            },
                            zoom: 12,
                            disableDefaultUI: false,

                        }
                    );
                    map.controls[google.maps.ControlPosition.TOP_LEFT];
                    // Get all devices Init
                    getDevices(function(data) {
                        ShowMaps(data);
                    });
                },
                () => {
                    handleLocationError(true, infoWindow, map.getCenter());
                }
            );

        }

        /**
         * 
         * Pusher data to receive 
         */
        var pusher = new Pusher('8442d369ae2137d24bf4', {
            cluster: 'us3'
        });
        var channel = pusher.subscribe('ruptela-server');
        channel.bind('coords-gps', function(data) {
            console.log("Date: "+ new Date() +" Data received from Pusher: ", data);
            getDevices(function(req) {
                ShowMaps(req);
            });
        });


        /**
         * 
         * Mostramos en el Mapa 
         */

        let markers = [];
        var ready = false; // Carga de markers
        function ShowMaps(data) {
            $.map(data, function(el) {
                console.log(el)

                if (ready == true) { // ya se cargaron los markes por primera vez
                    if (markers.length != el.length) {
                        markers = [];
                    }

                    /**
                     * 
                     * Verificamos si hay algun cambio en las coordenadas de algun marker
                     *  
                     */
                    if (markers.length == 0) {
                        for (let x = 0; x < el.length; x++) {
                            const origins = el[x];
                            var location = new google.maps.LatLng(origins.latitude, origins.longitude);

                            if (origins.latitude != '' && origins.longitude != '') {
                                const marker = new google.maps.Marker({
                                    position: location,
                                    map: map,
                                    title: origins.get_vehicle.name_unit,
                                    icon: "{{ asset('assets/images/marker.png') }}",
                                    lat: origins.latitude,
                                    lng: origins.longitude,
                                    id_gps: origins.id
                                });

                                markers.push(marker);

                                var content =
                                    '<div id="content" style="width: auto; height: auto;">' +
                                    '<h3>Dispositivo GPS <span class="badge bg-info">' + origins.get_g_p_s.uuid_device +
                                    '</span> </h3>' +
                                    '<span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        origins.get_g_p_s.name_device + '</b></span>' +
                                    '<span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        origins.get_vehicle.name_unit + '</b></span><br />' +
                                    '<span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        origins.date_update + '</b></span><br />' +
                                    '<span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        '<a href="https://www.google.com/maps?q='+origins.latitude + ',' + origins.longitude+'" target="_blank">'+ origins.latitude + ',' + origins.longitude + '</a></b></span><br />' +
                                    '<span class="badge bg-success">Velocidad: ' + origins.speed +
                                    ' MPH</span>&nbsp;&nbsp;' +
                                    '<span class="badge bg-warning">HDOP: ' + origins.hdop + ' MPH</span><br />' +
                                    '</div>';

                                var infowindow = new google.maps.InfoWindow({
                                    content: content
                                });

                                google.maps.event.addListener(marker, 'click', function(marker, content,
                                infowindow) {
                                    return function() {
                                        infowindow.setContent(content); //asignar el contenido al globo
                                        infowindow.open(map, marker); //mostrarlo
                                    };
                                }(marker, content, infowindow));
                            }
                        }
                    } else {
                        for (var i = 0; i < markers.length; i++) {
                            let element = markers[i];

                            for (let x = 0; x < el.length; x++) {
                                const origins = el[x];


                                if (element.id_gps == origins.id) {

                                    let init_pos = Math.abs(element.latitude) + Math.abs(element.longitude);
                                    let end_pos = Math.abs(origins.latitude) + Math.abs(origins.longitude);


                                    if (init_pos != end_pos) { // El repa cambio de posicion
                                        var newLocation = new google.maps.LatLng(origins.latitude, origins.longitude);
                                        element.setPosition(newLocation);
                                        element.lat = origins.latitude;
                                        element.lng = origins.longitude;
                                        // Actualiza el contenido del InfoWindow
                                        var newContent =
                                            '<div id="content" style="width: auto; height: auto;">' +
                                            '<h3>Dispositivo GPS <span class="badge bg-info">' + origins.get_g_p_s.uuid_device +
                                            '</span> </h3>' +
                                            '<span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                                origins.get_g_p_s.name_device + '</b></span>' +
                                            '<span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                                origins.get_vehicle.name_unit + '</b></span><br />' +
                                            '<span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                                origins.date_update + '</b></span><br />' +
                                            '<span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                                '<a href="https://www.google.com/maps?q='+origins.latitude + ',' + origins.longitude+'" target="_blank">'+ origins.latitude + ',' + origins.longitude + '</a></b></span><br />' +
                                            '<span class="badge bg-success">Velocidad: ' + origins.speed +
                                            ' MPH</span>&nbsp;&nbsp;' +
                                            '<span class="badge bg-warning">HDOP: ' + origins.hdop + ' MPH</span><br />' +
                                            '</div>';

                                        if (element.infowindow) {
                                            element.infowindow.setContent(newContent);
                                        }
                                        console.log("Marker updated: ", element);
                                        // element.setMap(null);
                                        // markers.splice(i, 1);
                                        // var location = new google.maps.LatLng(origins.latitude, origins.longitude);

                                        // const marker = new google.maps.Marker({
                                        //     position: location,
                                        //     map: map,
                                        //     title: origins.get_vehicle.name_unit,
                                        //     icon: "{{ asset('assets/images/marker.png') }}",
                                        //     lat: origins.latitude,
                                        //     lng: origins.longitude,
                                        //     id_gps: origins.id
                                        // });

                                        // markers.push(marker);

                                        // //contenido de la infowindow
                                        // var content =
                                        //     '<div id="content" style="width: auto; height: auto;">' +
                                        //     '<h3>Dispositivo GPS <span class="badge bg-info">' + origins.get_g_p_s.uuid_device +
                                        //     '</span> </h3>' +
                                        //     '<span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        //         origins.get_g_p_s.name_device + '</b></span>' +
                                        //     '<span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        //         origins.get_vehicle.name_unit + '</b></span><br />' +
                                        //     '<span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        //         origins.date_update + '</b></span><br />' +
                                        //     '<span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        //        '<a href="https://www.google.com/maps?q='+origins.latitude + ',' + origins.longitude+'" target="_blank">'+ origins.latitude + ',' + origins.longitude + '</a></b></span><br />' +
                                        //     '<span class="badge bg-success">Velocidad: ' + origins.speed +
                                        //     ' MPH</span>&nbsp;&nbsp;' +
                                        //     '<span class="badge bg-warning">HDOP: ' + origins.hdop + ' MPH</span><br />' +
                                        //     '</div>';

                                        // var infowindow = new google.maps.InfoWindow({
                                        //     content: content
                                        // });

                                        
                                        // google.maps.event.addListener(marker, 'click', function(marker, content,
                                        //     infowindow) {
                                        //     return function() {
                                        //         infowindow.setContent(
                                        //         content); //asignar el contenido al globo
                                        //         infowindow.open(map, marker); //mostrarlo
                                        //     };
                                        // }(marker, content, infowindow));
                                    }
                                }
                            }
                        }
                    }

                } else {

                    for (let x = 0; x < el.length; x++) {
                        const element = el[x];
                        var location = new google.maps.LatLng(element.latitude, element.longitude);

                        const marker = new google.maps.Marker({
                            position: location,
                            map: map,
                            title: element.get_vehicle.name_unit,
                            icon: "{{ asset('assets/images/marker.png') }}",
                            lat: element.latitude,
                            lng: element.longitude,
                            id_gps: element.id
                        });

                        markers.push(marker);

                        //contenido de la infowindow
                        var content =
                            '<div id="content" style="width: auto; height: auto;">' +
                            '<h3>Dispositivo GPS <span class="badge bg-info">' + element.get_g_p_s.uuid_device +
                            '</span> </h3>' +
                            '<span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                element.get_g_p_s.name_device + '</b></span>' +
                            '<span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                element.get_vehicle.name_unit + '</b></span><br />' +
                            '<span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                element.date_update + '</b></span><br />' +
                            '<span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                               '<a href="https://www.google.com/maps?q='+element.latitude + ',' + element.longitude+'" target="_blank">'+ element.latitude + ',' + element.longitude + '</a></b></span><br />' +
                            '<span class="badge bg-success">Velocidad: ' + element.speed +
                            ' MPH</span>&nbsp;&nbsp;' +
                            '<span class="badge bg-warning">HDOP: ' + element.hdop + ' MPH</span><br />' +
                            '</div>';

                        var infowindow = new google.maps.InfoWindow({
                            content: content
                        });

                        google.maps.event.addListener(marker, 'click', function(marker, content, infowindow) {
                            return function() {
                                infowindow.setContent(content); //asignar el contenido al globo
                                infowindow.open(map, marker); //mostrarlo
                            };
                        }(marker, content, infowindow));

                    }
                }

                ready = true;
            });
        }


        /**
         *
         * Obtenemos listado de dispositivos 
         */
        function getDevices(callback) {
            let url = "{{ route('getAllDispositives') }}";
            $.ajax({
                url: url,
                type: "GET",
                success: callback,
                jsonp: "json",
                dataType: "json"
            });
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $ApiKey_google }}&libraries=places&callback=initMap">
    </script>
    <!--Morris Chart-->
    <script src="{{ asset('assets/libs/morris.js06/morris.min.js') }}"></script>
    <!-- Dashboar init js-->
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
@endsection

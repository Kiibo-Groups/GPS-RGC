@extends('layouts.app')
@section('title')
    Gestor de Rutas
@endsection
@section('page_active')
    Rutas
@endsection
@section('subpage_active')
    Listado
@endsection

@section('content')
    <div class="container-fluid" style="padding: 0 !important;">

        <div class="row" style="background: #fff;height: 100vh;padding-top: 70px;">

            <div class="col-lg-3" style="padding: 0 !important;margin: 0 !important;">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-bordered">
                            <li class="nav-item">
                                <a href="#control_rutas" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                    Control de rutas
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="#rutas_assign" data-bs-toggle="tab" aria-expanded="true" class="nav-link">
                                    Rutas Asignadas
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane  show active" id="control_rutas">
                                <div id="sidebar-menu" style="overflow: hidden;overflow-y: scroll;height: 90vh;">
                                    <ul id="list-group mb-0 user-list">
                                        @foreach ($data as $rutas)
                                            <li class="list-group-item"
                                                style="border-bottom: 1px solid #e1e1e1;padding: 10px 0 !important;">
                                                <a href="#route-inner-{{ $rutas->id }}" data-bs-toggle="collapse"
                                                    class="route-inner" onclick="chkRoutes(this)"
                                                    data-route-container="route-inner-{{ $rutas->id }}"
                                                    data-origin="{{ $rutas->origen }}" data-destin="{{ $rutas->destino }}">
                                                    <div class="user float-start me-3">
                                                        <i class="mdi mdi-account"></i>
                                                    </div>
                                                    <div class="user-desc overflow-hidden">
                                                        <h5 class="name mt-0 mb-1">{{ $rutas->operador }}</h5>
                                                        <span class="desc text-muted font-12 text-truncate d-block">
                                                            {{ $rutas->origen }}, {{ $rutas->destino }}
                                                            <br />
                                                            <small>{{ $rutas->created_at }}</small>
                                                        </span>
                                                    </div>
                                                </a>
                                                <div class="collapse" id="route-inner-{{ $rutas->id }}">

                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                            <div class="tab-pane" id="rutas_assign">
                                <div id="sidebar-menu" style="overflow: hidden;overflow-y: scroll;height: 90vh;">
                                    <ul id="list-group mb-0 user-list">
                                        @foreach ($data as $rutas)
                                            <li class="list-group-item"
                                                style="border-bottom: 1px solid #e1e1e1;padding: 10px 0 !important;">
                                                <a href="#route-inner-{{ $rutas->id }}" data-bs-toggle="collapse"
                                                    class="route-inner" onclick="chkRoutes(this)"
                                                    data-route-container="route-inner-{{ $rutas->id }}"
                                                    data-origin="{{ $rutas->origen }}"
                                                    data-destin="{{ $rutas->destino }}">
                                                    <div class="user float-start me-3">
                                                        <i class="mdi mdi-account"></i>
                                                    </div>
                                                    <div class="user-desc overflow-hidden">
                                                        <h5 class="name mt-0 mb-1">{{ $rutas->operador }}</h5>
                                                        <span class="desc text-muted font-12 text-truncate d-block">
                                                            {{ $rutas->origen }}, {{ $rutas->destino }}
                                                            <br />
                                                            <small>{{ $rutas->created_at }}</small>
                                                        </span>
                                                    </div>
                                                </a>
                                                <div class="collapse" id="route-inner-{{ $rutas->id }}">

                                                </div>
                                            </li>
                                            <li class="list-group-item"
                                                style="border-bottom: 1px solid #e1e1e1;padding: 10px 0 !important;">
                                                <a href="#route-inner-{{ $rutas->id }}" data-bs-toggle="collapse"
                                                    class="route-inner" onclick="chkRoutes(this)"
                                                    data-route-container="route-inner-{{ $rutas->id }}"
                                                    data-origin="{{ $rutas->origen }}"
                                                    data-destin="{{ $rutas->destino }}">
                                                    <div class="user float-start me-3">
                                                        <i class="mdi mdi-account"></i>
                                                    </div>
                                                    <div class="user-desc overflow-hidden">
                                                        <h5 class="name mt-0 mb-1">{{ $rutas->operador }}</h5>
                                                        <span class="desc text-muted font-12 text-truncate d-block">
                                                            {{ $rutas->origen }}, {{ $rutas->destino }}
                                                            <br />
                                                            <small>{{ $rutas->created_at }}</small>
                                                        </span>
                                                    </div>
                                                </a>
                                                <div class="collapse" id="route-inner-{{ $rutas->id }}">

                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-9" style="padding: 0 !important;margin: 0 !important;">
                <div class="card">
                    <div class="card-body" style="padding: 0 !important;">
                        <div id="gmaps-overlay" class="gmaps"
                            style="position: relative; overflow: hidden;height:  90vh !important;"></div>

                            <!-- Hide controls until they are moved into the map. -->
                            <div style="display: none">
                                <div class="controls zoom-control">
                                    <button class="zoom-control-in" title="Zoom In">+</button>
                                    <button class="zoom-control-out" title="Zoom Out">−</button>
                                </div>
                                <div class="controls maptype-control maptype-control-is-map">
                                    <button class="maptype-control-map" title="Show road map">Map</button>
                                    <button class="maptype-control-satellite" title="Show satellite imagery">
                                        Satellite
                                    </button>
                                </div>
                                <div class="controls fullscreen-control">
                                    <button title="Toggle Fullscreen">
                                        <div class="fullscreen-control-icon fullscreen-control-top-left"></div>
                                        <div class="fullscreen-control-icon fullscreen-control-top-right"></div>
                                        <div class="fullscreen-control-icon fullscreen-control-bottom-left"></div>
                                        <div class="fullscreen-control-icon fullscreen-control-bottom-right"></div>
                                    </button>
                                </div>
                            </div>
                    </div>
                </div>
            </div>
        </div>
    @endsection

    @section('js')
        <script>
            let lat = 31.326015;
            let lng = 75.576180;
            window.CSRF_TOKEN = '{{ csrf_token() }}';
            let directionsService,
                directionsRenderer,
                map,
                dirRenderer,
                dr;

            function initMap() {

                var geocoder = new google.maps.Geocoder;
                directionsService = new google.maps.DirectionsService();
                dirRenderer = new google.maps.DirectionsRenderer({
                    draggable: true,
                });
                dr = new google.maps.DirectionsRenderer();

                navigator.geolocation.getCurrentPosition((position) => {
                    lat = position.coords.latitude;
                    lng = position.coords.longitude;

                    map = new google.maps.Map(
                        document.getElementById('gmaps-overlay'), {
                            center: {
                                lat: lat,
                                lng: lng
                            },
                            zoom: 12,
                            disableDefaultUI: true,
                        }
                    );

                    initZoomControl(map);
                    initMapTypeControl(map);
                    initFullscreenControl(map);

                    // map.controls[google.maps.ControlPosition.TOP_LEFT];

                    setTimeout(() => {
                        // Applicamos Condensed al Sidebar
                        $('body').attr('data-sidebar-size', "condensed");
                        $('body').attr('data-sidebar-color', "light");
                        $('.navbar-custom').css('background-color', '#ffffff');
                    }, 800);
                }, () => {
                    handleLocationError(true, infoWindow, map.getCenter());
                });
            }

            function chkRoutes(e) {
                var elems = document.querySelectorAll(".collapse.show");

                [].forEach.call(elems, function(el) {
                    el.classList.remove("show");
                });

                let containerId = e.getAttribute('data-route-container');
                let cont = document.getElementById(containerId)
                cont.innerHTML = '';

                let origin = e.getAttribute('data-origin'); //encodeURIComponent();
                let destin = e.getAttribute('data-destin'); //encodeURIComponent();

                directionsService.route({
                    origin: origin,
                    destination: destin,
                    provideRouteAlternatives: true,
                    travelMode: 'DRIVING',
                    avoidTolls: true
                }, function(response, status) {
                    if (status == google.maps.DirectionsStatus.OK) {
                        console.log(response.routes);

                        let htmlContent = '<ul class="nav-second-level">';
                        for (var i = 0; i < response.routes.length; i++) {

                            dr.setDirections(response);

                            htmlContent += '<li>' +
                                '<a href="javascript:void(0)" onClick="setDirection(' + i + ')" >' +
                                '<div class="user float-start me-3">' +
                                '<i class="mdi mdi-google-maps" style="color:green;"></i>  ' +
                                '</div>' +
                                '<div class="user-desc overflow-hidden" style="display: flex;justify-content: space-between;"> ' +
                                '<span class="desc font-12 d-block"> ' + response.routes[i]['summary'] +
                                '<br />' +
                                '<small class="text-truncate text-muted">' + response.routes[i]['legs'][0]['duration'][
                                    'text'
                                ] + ' - ' + response.routes[i]['legs'][0]['distance']['text'] + '</small>' +
                                '</span>' +
                                '<button type="button" class="btn btn-warning btn-xs waves-effect waves-light" style="height: 30px;border-radius: 20px;">Asignar Ruta</button>' +
                                '</div> ' +
                                '</a>' +
                                '</li>';

                        }

                        htmlContent += '</ul>';

                        cont.insertAdjacentHTML('beforeend', htmlContent);
                    }
                });
                dirRenderer.setMap(map);


                setTimeout(() => {
                    console.log("Cambio de index => ");
                    dr.setRouteIndex(1);
                }, 2500);

                setTimeout(() => {
                    console.log("Cambio de index => ");
                    dr.setRouteIndex(0);
                }, 4500);



                directionsService.route({
                    origin: {
                        query: origin,
                    },
                    destination: {
                        query: destin,
                    },
                    travelMode: google.maps.TravelMode.DRIVING,
                    provideRouteAlternatives: true

                }).then((response) => {


                    for (var i = 0; i < response.routes.length; i++) {
                        directionsRenderer.setDirections(response);
                        // Tell the DirectionsRenderer which route to display
                        directionsRenderer.setRouteIndex(i);
                        directionsRenderer.setMap(map);
                    }

                }).catch((e) => console.log("Directions request failed due to ", status));
            }


            function setDirection(pos) {
                console.log(pos);
                dr.setRouteIndex(pos);
                dr.setMap(map);
            }


            /**
             * 
             * Funciones avanzadas para google Maps
             *  
             */
            function initZoomControl(map) {
                document.querySelector(".zoom-control-in").onclick = function() {
                    map.setZoom(map.getZoom() + 1);
                };

                document.querySelector(".zoom-control-out").onclick = function() {
                    map.setZoom(map.getZoom() - 1);
                };

                map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(
                    document.querySelector(".zoom-control"),
                );
            }

            function initMapTypeControl(map) {
                const mapTypeControlDiv = document.querySelector(".maptype-control");

                document.querySelector(".maptype-control-map").onclick = function() {
                    mapTypeControlDiv.classList.add("maptype-control-is-map");
                    mapTypeControlDiv.classList.remove("maptype-control-is-satellite");
                    map.setMapTypeId("roadmap");
                };

                document.querySelector(".maptype-control-satellite").onclick = function() {
                    mapTypeControlDiv.classList.remove("maptype-control-is-map");
                    mapTypeControlDiv.classList.add("maptype-control-is-satellite");
                    map.setMapTypeId("hybrid");
                };

                map.controls[google.maps.ControlPosition.LEFT_TOP].push(mapTypeControlDiv);
            }

            function initFullscreenControl(map) {
                const elementToSendFullscreen = map.getDiv().firstChild;
                const fullscreenControl = document.querySelector(".fullscreen-control");

                map.controls[google.maps.ControlPosition.RIGHT_TOP].push(fullscreenControl);
                fullscreenControl.onclick = function() {
                    if (isFullscreen(elementToSendFullscreen)) {
                        exitFullscreen();
                    } else {
                        requestFullscreen(elementToSendFullscreen);
                    }
                };

                document.onwebkitfullscreenchange =
                    document.onmsfullscreenchange =
                    document.onmozfullscreenchange =
                    document.onfullscreenchange =
                    function() {
                        if (isFullscreen(elementToSendFullscreen)) {
                            fullscreenControl.classList.add("is-fullscreen");
                        } else {
                            fullscreenControl.classList.remove("is-fullscreen");
                        }
                    };
            }

            function isFullscreen(element) {
                return (
                    (document.fullscreenElement ||
                        document.webkitFullscreenElement ||
                        document.mozFullScreenElement ||
                        document.msFullscreenElement) == element
                );
            }

            function requestFullscreen(element) {
                if (element.requestFullscreen) {
                    element.requestFullscreen();
                } else if (element.webkitRequestFullScreen) {
                    element.webkitRequestFullScreen();
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.msRequestFullScreen) {
                    element.msRequestFullScreen();
                }
            }

            function exitFullscreen() {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                }
            }

            window.initMap = initMap;
        </script>
        <!-- google maps api -->
        <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{ $ApiKey_google }}&callback=initMap&v=weekly" defer>
        </script>
    @endsection

@extends('layouts.app')

@section('title')
    Seguimiento de Dipositivos | Fletes RGC - Empresa Mexicana especializada en el autotransporte de carga terrestre.
@endsection

@section('page_active')
    Seguimiento de Dipositivos
@endsection
@section('subpage_active')
    Home
@endsection
@section('content')
    <div class="container-fluid" style="padding: 0 !important;">

        <div class="row" style="background: #fff;height: 100vh;padding-top: 70px;">

            <div class="tracking-maps-card d-none">
                <div class="card">
                    <div class="card-body">
                        <ul class="nav nav-tabs nav-bordered">
                            <li class="nav-item">
                                <a href="#control_rutas" data-bs-toggle="tab" aria-expanded="false" class="nav-link active">
                                    Dispositivos Activos
                                </a>
                            </li>
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane show active" id="control_rutas">
                                <div id="sidebar-menu">
                                    <ul id="list-group" class="mb-0 user-list">

                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-12 tracking-maps">
                <div class="card">
                    <div class="card-body" style="padding: 0 !important;">
                        <div id="map" class="gmaps"
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
    </div>
@endsection

@section('js')
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/es.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
        integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script>
        ! function($) {
            'use strict';

            dayjs.extend(window.dayjs_plugin_relativeTime);
            dayjs.locale('es'); // <-- Establece español
        }(jQuery);


        let lat;
        let lng;
        var latlng;
        var map;

        var marker;

        navigator.geolocation.getCurrentPosition(
            (position) => {
                lat = position.coords.latitude;
                lng = position.coords.longitude;
                latlng = L.latLng(lat, lng);

                map = L.map('map').setView(latlng, 13);

                L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                }).addTo(map);

                // map = new google.maps.Map(
                //     document.getElementById('map'), {
                //         center: {
                //             lat: lat,
                //             lng: lng
                //         },
                //         zoom: 12,
                //         disableDefaultUI: true,

                //     }
                // );

                // initZoomControl(map);
                // initMapTypeControl(map);
                // initFullscreenControl(map);

                // Get all devices Init
                getDevices(function(data) {
                    ShowMaps(data);
                });
            },
            () => {
                handleLocationError(true, infoWindow, map.getCenter());
            }
        );


        function ViewPositionDevice(latitude, longitude, id) {
            // Elimina la clase de todos los elementos
            document.querySelectorAll('.active-card-maps').forEach(el => {
                el.classList.remove('active-card-maps');
            });

            const li = document.getElementById(`device-${id}`);
            li.classList.add('active-card-maps');

            lat = latitude;
            lng = longitude;
            map.panTo({
                lat: lat,
                lng: lng
            });
            map.setZoom(20);

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
            // Actualizamos la tarjeta del dispositivo
            updateDeviceCard(JSON.parse(data));

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
                            // var location = new google.maps.LatLng(origins.latitude, origins.longitude);
                            var location = L.latLng(origins.latitude, origins.longitude);

                            if (origins.latitude != '' && origins.longitude != '') {
                                const marker = L.marker(location, {
                                    title: origins.get_vehicle.name_unit,
                                    autoPanOnFocus: true,
                                    autoPan: true,
                                    icon: L.icon({
                                        iconUrl: "{{ asset('assets/images/marker.png') }}",
                                        iconSize: [50, 50],
                                        iconAnchor: [16, 32],
                                        popupAnchor: [0, -32]
                                    })
                                }).addTo(map).bindPopup(
                                    `<h3>Dispositivo GPS <span class="badge bg-info">${origins.get_g_p_s.uuid_device}</span></h3>
                                    <span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">${origins.get_g_p_s.name_device}</b></span>
                                    <span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">${origins.get_vehicle.name_unit}</b></span><br />
                                    <span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">${origins.date_update}</b></span><br />
                                    <span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">
                                    <a href="https://www.google.com/maps?q=${origins.latitude},${origins.longitude}" target="_blank">${origins.latitude}, ${origins.longitude}</a></b></span><br />
                                    <span class="badge bg-success">Velocidad: ${origins.speed} MPH</span>`
                                );
                                marker.id_gps = origins.id;
                                markers.push(marker);
                                // const marker = new google.maps.Marker({
                                //     position: location,
                                //     map: map,
                                //     title: origins.get_vehicle.name_unit,
                                //     icon: "{{ asset('assets/images/marker.png') }}",
                                //     lat: origins.latitude,
                                //     lng: origins.longitude,
                                //     id_gps: origins.id
                                // });

                                // var content =
                                //     '<div id="content" style="width: auto; height: auto;">' +
                                //     '<h3>Dispositivo GPS <span class="badge bg-info">' + origins.get_g_p_s
                                //     .uuid_device +
                                //     '</span> </h3>' +
                                //     '<span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                //     origins.get_g_p_s.name_device + '</b></span>' +
                                //     '<span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                //     origins.get_vehicle.name_unit + '</b></span><br />' +
                                //     '<span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                //     origins.date_update + '</b></span><br />' +
                                //     '<span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                //     '<a href="https://www.google.com/maps?q=' + origins.latitude + ',' + origins
                                //     .longitude + '" target="_blank">' + origins.latitude + ',' + origins.longitude +
                                //     '</a></b></span><br />' +
                                //     '<span class="badge bg-success">Velocidad: ' + origins.speed +
                                //     ' MPH</span>&nbsp;&nbsp;' +
                                //     '<span class="badge bg-warning">HDOP: ' + origins.hdop + ' MPH</span><br />' +
                                //     '</div>';

                                // var infowindow = new google.maps.InfoWindow({
                                //     content: content
                                // });

                                // marker.infowindow = infowindow;

                                // markers.push(marker);

                                // google.maps.event.addListener(marker, 'click', function(marker, content,
                                //     infowindow) {
                                //     return function() {
                                //         infowindow.setContent(content); //asignar el contenido al globo
                                //         infowindow.open(map, marker); //mostrarlo
                                //     };
                                // }(marker, content, infowindow));
                            }
                        }
                    } else {
                        for (let x = 0; x < el.length; x++) {
                            const origins = el[x];
                           
                            let marker = markers.find(m => m.id_gps === origins.id);
                            if (marker.id_gps == origins.id) {

                                let currentPos = marker.getLatLng();
                                let newLocation = L.latLng(origins.latitude, origins.longitude);
                                
                                if (currentPos.lat !== origins.latitude || currentPos.lng !== origins.longitude) {
                                    const lat = parseFloat(origins.latitude);
                                    const lng = parseFloat(origins.longitude);
                                    // Mover marker
                                    marker.setLatLng([lat, lng]); 
                                    console.log("¡Marker movido!"); 

                                    // Panear el mapa si el movimiento fue considerable
                                    let distance = map.distance(currentPos, newLocation);
                                    // map.panTo(newLocation);
                                    // var pos = map.latLngToLayerPoint(currentPos);
                                    // pos.y -= 25;
                                    // var fx = new L.PosAnimation();
                                    // fx.once('end',function() {
                                    //     pos.y += 25;
                                    //     fx.run(marker._icon, pos, 0.8);
                                    // });

                                    // fx.run(marker._icon, pos, 0.3);


                                    var newContent =
                                        '<div id="content" style="width: auto; height: auto;">' +
                                        '<h3>Dispositivo GPS <span class="badge bg-info">' + origins.get_g_p_s
                                        .uuid_device + '</span> </h3>' +
                                        '<span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        origins.get_g_p_s.name_device + '</b></span>' +
                                        '<span>Vehículo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        origins.get_vehicle.name_unit + '</b></span><br />' +
                                        '<span>Última actualización: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        origins.date_update + '</b></span><br />' +
                                        '<span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                        '<a href="https://www.google.com/maps?q=' + origins.latitude + ',' + origins
                                        .longitude + '" target="_blank">' +
                                        origins.latitude + ',' + origins.longitude + '</a></b></span><br />' +
                                        '<span class="badge bg-success">Velocidad: ' + origins.speed +
                                        ' MPH</span>&nbsp;&nbsp;' +
                                        '<span class="badge bg-warning">HDOP: ' + origins.hdop + '</span><br />' +
                                        '</div>';

                                    if (marker.getPopup()) {
                                        marker.setPopupContent(newContent);
                                    } else {
                                        marker.bindPopup(newContent);
                                    }
                                }
                                // let init_pos = Math.abs(element.latitude) + Math.abs(element.longitude);
                                // let end_pos = Math.abs(origins.latitude) + Math.abs(origins.longitude);

                                // if (init_pos != end_pos) { // El repa cambio de posicion

                                // var newLocation = new google.maps.LatLng(origins.latitude, origins.longitude);
                                // element.setPosition(newLocation);
                                // element.lat = origins.latitude;
                                // element.lng = origins.longitude;
                                // // Actualiza el contenido del InfoWindow
                                // var newContent =
                                //     '<div id="content" style="width: auto; height: auto;">' +
                                //     '<h3>Dispositivo GPS <span class="badge bg-info">' + origins.get_g_p_s
                                //     .uuid_device +
                                //     '</span> </h3>' +
                                //     '<span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                //     origins.get_g_p_s.name_device + '</b></span>' +
                                //     '<span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                //     origins.get_vehicle.name_unit + '</b></span><br />' +
                                //     '<span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                //     origins.date_update + '</b></span><br />' +
                                //     '<span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                                //     '<a href="https://www.google.com/maps?q=' + origins.latitude + ',' +
                                //     origins.longitude + '" target="_blank">' + origins.latitude + ',' +
                                //     origins.longitude + '</a></b></span><br />' +
                                //     '<span class="badge bg-success">Velocidad: ' + origins.speed +
                                //     ' MPH</span>&nbsp;&nbsp;' +
                                //     '<span class="badge bg-warning">HDOP: ' + origins.hdop +
                                //     ' MPH</span><br />' +
                                //     '</div>';

                                // if (element.infowindow) {
                                //     element.infowindow.setContent(newContent);
                                // }

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
                                // }
                            }
                        }
                    }

                } else {
                    for (let x = 0; x < el.length; x++) {
                        const element = el[x];
                        // var location = new google.maps.LatLng(element.latitude, element.longitude);

                        // const marker = new google.maps.Marker({
                        //     position: location,
                        //     map: map,
                        //     title: element.get_vehicle.name_unit,
                        //     icon: "{{ asset('assets/images/marker.png') }}",
                        //     lat: element.latitude,
                        //     lng: element.longitude,
                        //     id_gps: element.id
                        // });


                        var location = L.latLng(element.latitude, element.longitude);
                        const marker = L.marker(location, {
                            title: element.get_vehicle.name_unit,
                            autoPanOnFocus: true,
                            autoPan: true,
                            icon: L.icon({
                                iconUrl: "{{ asset('assets/images/marker.png') }}",
                                iconSize: [50, 50],
                                iconAnchor: [16, 32],
                                popupAnchor: [0, -32]
                            })
                        }).addTo(map).bindPopup(
                            `<h3>Dispositivo GPS <span class="badge bg-info">${element.get_g_p_s.uuid_device}</span></h3>
                             <span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">${element.get_g_p_s.name_device}</b></span>
                             <span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">${element.get_vehicle.name_unit}</b></span><br />
                             <span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">${element.date_update}</b></span><br />
                             <span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">
                             <a href="https://www.google.com/maps?q=${element.latitude},${element.longitude}" target="_blank">${element.latitude}, ${element.longitude}</a></b></span><br />
                             <span class="badge bg-success">Velocidad: ${element.speed} MPH</span>`
                        );
                        marker.id_gps = element.id;
                        // marker.infowindow = infowindow;
                        markers.push(marker);

                        // google.maps.event.addListener(marker, 'click', function(marker, content, infowindow) {
                        //     return function() {
                        //         infowindow.setContent(content); //asignar el contenido al globo
                        //         infowindow.open(map, marker); //mostrarlo
                        //     };
                        // }(marker, content, infowindow));

                        const dateUpdate = dayjs(element.date_update).fromNow();

                        var CardInnerMaps = `<li id="device-${element.id}" class="list-group-item" style="border-bottom: 1px solid #e1e1e1;padding: 10px 0 !important;">
                                <a href="#route-inner-0" data-bs-toggle="collapse" class="route-inner" style="padding:0 !important;cursor: pointer;"
                                    onclick="ViewPositionDevice(${element.latitude},${element.longitude},${element.id})" data-route-container="route-inner-0">
                                    <div class="user float-start me-2">
                                        <i class="mdi mdi-crosshairs-gps"></i>
                                    </div>
                                    <div class="user-desc overflow-hidden">
                                        <h5 class="name mt-0 mb-1">${element.get_g_p_s.name_device}</h5>
                                        <span class="desc text-muted font-14 text-truncate d-block">
                                            ${element.get_g_p_s.descript_device}
                                            <br />
                                            <span class="device-date">${dateUpdate}</span><br />
                                            <span class="device-speed">
                                            ${parseInt(element.speed, 10) > 0
                                                ? `<span class="badge bg-success">Velocidad ${element.speed} MPH</span>`
                                                : `<span class="badge bg-warning">Detenido</span>`}
                                            </span>
                                    </div>
                                </a>
                            </li>`;

                        $("#list-group").append(CardInnerMaps);
                    }
                    $('.tracking-maps-card').removeClass('d-none');
                    // Applicamos Condensed al Sidebar
                    $('body').attr('data-sidebar-size', "condensed");
                    $('body').attr('data-sidebar-color', "light");
                    $('.navbar-custom').css('background-color', '#ffffff');
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

        /**
         * 
         * Actualizamos la tarjeta del dispositivo en el sidebar
         */
        function updateDeviceCard(element) {
            const li = document.getElementById(`device-${element.id}`);
            if (li) {
                // Actualiza fecha
                li.querySelector('.device-date').textContent = dayjs(element.date_update).fromNow();

                // Actualiza velocidad/estado
                const speedSpan = li.querySelector('.device-speed');
                if (parseInt(element.speed, 10) > 0) {
                    speedSpan.innerHTML = `<span class="badge bg-success">Velocidad ${element.speed} MPH</span>`;
                } else {
                    speedSpan.innerHTML = `<span class="badge bg-warning">Detenido</span>`;
                }

                // Si quieres actualizar coordenadas, puedes agregar un span con clase y actualizarlo igual
                // li.querySelector('.device-coords').textContent = `${element.latitude},${element.longitude}`;
            }
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

        // window.initMap = initMap;
    </script>
@endsection

! function ($) {
    'use strict';

    dayjs.extend(window.dayjs_plugin_relativeTime);
    dayjs.locale('es'); // <-- Establece español
}(jQuery);

// Inicializamos el mapa al cargar el DOM
let lat,
    lng,
    latlng,
    map,
    marker,
    body = document.querySelector('body'),
    navbarCustom = document.querySelector('.navbar-custom'),
    listGroup = document.getElementById('list-group'),
    trackingMapsCard = document.querySelector('.tracking-maps-card');

function initMap() {
    console.log("Inicializamos el mapa...");
    // Verificamos si el navegador soporta Geolocation
    if (!navigator.geolocation) {
        console.error('Geolocation is not supported by this browser.');
        return;
    }

    // Obtenemos la posición actual del usuario
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
                disableDefaultUI: true,
            }
            );

            initZoomControl(map);
            initMapTypeControl(map);
            initFullscreenControl(map);

            // Get all devices Init
            getDevices(function (data) {
                ShowMaps(data);
            });
        },
        () => {
            handleLocationError(true, infoWindow, map.getCenter());
        }
    );
}



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
channel.bind('coords-gps', function (data) {
    // Actualizamos la tarjeta del dispositivo
    updateDeviceCard(JSON.parse(data));
});


/**
 * 
 * Funciones del Mapa 
 */

let markers = [];
var ready = false; // Carga de markers
function ShowMaps(data) {

    $.map(data, function (el) {
        for (let x = 0; x < el.length; x++) {
            const element = el[x];
            var location = new google.maps.LatLng(element.latitude, element.longitude);

            const marker = new google.maps.Marker({
                position: location,
                map: map,
                title: element.get_vehicle.name_unit,
                icon: markerIcon,
                lat: element.latitude,
                lng: element.longitude,
                id_gps: element.id
            });

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
                '<span class="device-coords">Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                '<a href="https://www.google.com/maps?q=' + element.latitude + ',' + element.longitude + '" target="_blank">' + element.latitude + ',' + element.longitude + '</a></b></span><br />' +
                '<span class="badge bg-success">Velocidad: ' + element.speed +
                ' MPH</span>&nbsp;&nbsp;' +
                '<span class="badge bg-warning">HDOP: ' + element.hdop + ' MPH</span><br />' +
                '</div>';

            var infowindow = new google.maps.InfoWindow({
                content: content
            });

            marker.infowindow = infowindow;
            markers.push(marker);

            google.maps.event.addListener(marker, 'click', function (marker, content, infowindow) {
                return function () {
                    infowindow.setContent(content); //asignar el contenido al globo
                    infowindow.open(map, marker); //mostrarlo
                };
            }(marker, content, infowindow));

            const dateUpdate = dayjs(element.date_update).fromNow();

            var CardInnerMaps = `<li id="device-${element.id}" class="list-group-item" style="border-bottom: 1px solid #e1e1e1;padding: 10px 0 !important;">
                                    <label class="device-name" for="check-${element.id}" style="font-weight: 600;">
                                        <div class="form-check float-start me-2">
                                            <input type="checkbox" class="device-check" id="check-${element.id}" style="margin-right:10px;">
                                        </div>
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
                                            </span> 
                                        </div>
                                    </label>
                                </li>`;

            listGroup.innerHTML += CardInnerMaps;
        }

        trackingMapsCard.classList.remove('d-none');
        // Applicamos Condensed al Sidebar
        navbarCustom.setAttribute('style', 'background-color: #ffffff;');
        body.setAttribute('data-sidebar-size', "condensed");
        body.setAttribute('data-sidebar-color', "light");

        ready = true;
    });
}


// Agrega este bloque después de crear los checkboxes (por ejemplo, después de renderizar el listado)
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('device-check')) {
        // Obtén todos los checkboxes marcados
        const checkedBoxes = document.querySelectorAll('.device-check:checked');

        // Elimina la clase de todos los elementos
        document.querySelectorAll('.active-card-maps').forEach(el => {
            el.classList.remove('active-card-maps');
        });

        if (checkedBoxes.length > 0) {
            // Mostrar solo los markers seleccionados
            const ids = Array.from(checkedBoxes).map(cb => cb.id.replace('check-', ''));
            markers.forEach(marker => {
                if (ids.includes(marker.id_gps.toString())) {
                    const li = document.getElementById(`device-${marker.id_gps}`);
                    li.classList.add('active-card-maps');
                    marker.setMap(map);
                    var newLocation = new google.maps.LatLng(marker.lat, marker.lng);
                    map.panTo(newLocation);
                    map.setZoom(20);
                } else {
                    marker.setMap(null);
                }
            });
        } else {
            // Si ninguno está marcado, muestra todos los markers
            markers.forEach(marker => {
                marker.setMap(map);
            });
        }
    }
});

/**
 *
 * Obtenemos listado de dispositivos 
 */
function getDevices(callback) {
    // let url = "{{ route('getAllDispositives') }}";
    let url = URL_BASE + "/api/getAllDispositives";
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
    console.log("Actualizando tarjeta y ubicacion unicamente del dispositivo", element);
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

      
        let marker = markers.find(m => m.id_gps === element.id);
        if (marker.id_gps == element.id) {
            // Verificamos si las coordenadas no estan vacias
            let init_pos = Math.abs(marker.lat) + Math.abs(marker.lng);
            let end_pos = Math.abs(element.latitude) + Math.abs(element.longitude);

            if (init_pos != end_pos) { // El repa cambio de posicion

                var newLocation = new google.maps.LatLng(element.latitude, element.longitude);
                marker.setPosition(newLocation);
                map.panTo(newLocation);
                // Actualiza el contenido del InfoWindow
                // var newContent =
                //     '<div id="content" style="width: auto; height: auto;">' +
                //     '<h3>Dispositivo GPS <span class="badge bg-info">' + origins.get_g_p_s
                //         .uuid_device +
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

                // if (marker.infowindow) {
                //     marker.infowindow.setContent(newContent);
                // }

                // marker.setMap(null);
                // markers.splice(i, 1);
                // var location = new google.maps.LatLng(origins.latitude, origins.longitude);

                // const marker = new google.maps.Marker({
                //     position: location,
                //     map: map,
                //     title: origins.get_vehicle.name_unit,
                //     icon: markerIcon,
                //     lat: origins.latitude,
                //     lng: origins.longitude,
                //     id_gps: origins.id
                // });

                // markers.push(marker);
            } else {
                console.log("No hubo cambio de posicion", init_pos, end_pos);
            }
        }

    }
}

/**
 * 
 * Funciones avanzadas para google Maps
 *  
 */
function handleLocationError(browserHasGeolocation, infoWindow, pos) {
    infoWindow.setPosition(pos);
    infoWindow.setContent(
        browserHasGeolocation ?
            'Error: The Geolocation service failed.' :
            'Error: Your browser doesn\'t support geolocation.'
    );
    infoWindow.open(map);
    console.error(
        browserHasGeolocation ?
            'The Geolocation service failed.' :
            'Your browser doesn\'t support geolocation.'
    );
}

function initZoomControl(map) {
    document.querySelector(".zoom-control-in").onclick = function () {
        map.setZoom(map.getZoom() + 1);
    };

    document.querySelector(".zoom-control-out").onclick = function () {
        map.setZoom(map.getZoom() - 1);
    };

    map.controls[google.maps.ControlPosition.RIGHT_BOTTOM].push(
        document.querySelector(".zoom-control"),
    );
}

function initMapTypeControl(map) {
    const mapTypeControlDiv = document.querySelector(".maptype-control");

    document.querySelector(".maptype-control-map").onclick = function () {
        mapTypeControlDiv.classList.add("maptype-control-is-map");
        mapTypeControlDiv.classList.remove("maptype-control-is-satellite");
        map.setMapTypeId("roadmap");
    };

    document.querySelector(".maptype-control-satellite").onclick = function () {
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
    fullscreenControl.onclick = function () {
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
        function () {
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




// Expose initMap to the global scope
window.initMap = initMap;

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
    ContentDevices = null,
    markerFollowId = null,
    currentInfoWindow = null, // Variable para mantener referencia al InfoWindow actual
    ContentListGroups = 'list-group-all',
    lblViewListDevices = document.getElementById('lblViewListDevices'),
    listDevicesGroup = document.getElementById('list-devices-group'),
    viewTrackingDevice = document.getElementById('view-tracking-device'),
    body = document.querySelector('body'),
    trackingMapsCard = document.querySelector('.tracking-maps-card'),
    floatingCardMaps = document.querySelector('.floating-card-maps'),
    directionsService,
    directionsRenderer,
    trailPath = null;
// Al cargar los dispositivos
window.deviceTracks = {}; // Objeto global

const addUnitForm = document.getElementById('add_new_unit'),
    addBoxForm = document.getElementById('add_new_box'),
    btnchangeViewDisp = document.querySelectorAll('.change_view_disp');

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
                zoom: 13,
                disableDefaultUI: true,
                gestureHandling: 'greedy',
                zoomControl: false,
                mapTypeControl: true,
                streetViewControl: true,
                fullscreenControl: false,
                clickableIcons: false,
                // MapStyle
                styles: MapStyle,

            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map
            });

            // initMapTypeControl(map);
            // initZoomControl(map);
            // initFullscreenControl(map);

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

btnchangeViewDisp.forEach(btn => {
    btn.addEventListener('click', function (e) {
        e.preventDefault();
        let View = e.currentTarget.getAttribute('data-view');
        const target = e.currentTarget.getAttribute('href');
        const allTabs = document.querySelectorAll('.tab-pane');
        allTabs.forEach(tab => {
            tab.classList.remove('active', 'show');
        });
        const targetTab = document.querySelector(target);
        targetTab.classList.add('active', 'show');

        btnchangeViewDisp.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        listDevicesGroup.style.display = 'block';
        viewTrackingDevice.style.display = 'none';

        if (View === 'control_rutas') {
            ContentListGroups = 'list-group-all';
            lblViewListDevices.innerHTML = 'Dispositivos Generales';
            getDevices(function (data) {
                ShowMaps(data);
            });
        }

        if (View === 'gps_tracto') {
            ContentListGroups = 'list-group-trucks';
            lblViewListDevices.innerHTML = 'Dispositivos en Tractos';
            getDevicesTrucks(function (data) {
                ShowMaps(data);
            });
        }

        if (View === 'gps_cajas') {
            ContentListGroups = 'list-group-box';
            lblViewListDevices.innerHTML = 'Dispositivos en Cajas';
            getDevicesBox(function (data) {
                ShowMaps(data);
            });
        }
    });
});


function ViewPositionDevice(latitude, longitude, id) {
    // Cerrar el InfoWindow actual si existe
    if (currentInfoWindow) {
        currentInfoWindow.close();
    }

    markers.forEach(marker => {
        if (marker.id_gps == id) {
            google.maps.event.trigger(marker, 'click');
            currentInfoWindow = marker.infowindow;
        }
    });

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
// channel.bind('coords-gps', function (data) {
//     // Actualizamos la tarjeta del dispositivo
//     updateDeviceCard(data);
// });

/**
 * 
 * Funciones del Mapa 
 */

let markers = [];
var ready = false; // Carga de markers
function ShowMaps(data) {

    $.map(data, function (el) {
        ContentDevices = el;
        listGroup = document.getElementById(ContentListGroups);
        // Limpiamos el listado
        listGroup.innerHTML = '';
        console.log("Cargando dispositivo GPS", ContentDevices);
        for (let x = 0; x < el.length; x++) {
            const element = el[x];
            var location = new google.maps.LatLng(element.latitude, element.longitude);

            const marker = new google.maps.Marker({
                position: location,
                map: map,
                title: element.get_vehicle ? element.get_vehicle.name_unit : 'Sin Vehiculo',
                icon: {
                    path: google.maps.SymbolPath.FORWARD_CLOSED_ARROW,
                    scale: 5,
                    rotation: 0, // después lo actualizamos
                    strokeColor: "#000",
                    fillColor: "#00F",
                    fillOpacity: 1
                },
                lat: element.latitude,
                lng: element.longitude,
                id_gps: element.id
            });

            //contenido de la infowindow
            var content = `<div id="content" style="width: auto; height: auto;">
                <h3>
                    Dispositivo GPS <span class="badge bg-info">${element.get_vehicle.get_g_p_s.uuid_device}</span> 
                </h3>
                <span class="mb-2 d-block">
                    GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">${element.get_vehicle.get_g_p_s.name_device}</b>
                </span>
                <span class="mb-2 d-block" >
                    Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">${element.get_vehicle.name_unit}</b>
                </span>
                ${(element.get_vehicle.get_box != null)
                    ? `<span class="mb-2 d-block">
                            Caja Asignada: <b style="display:block;font-size: 14px;font-weight: 600;">${element.get_vehicle.get_box.name_truck_box}</b>
                        </span>`
                    : ""
                }
                <br />
                <span>
                    Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">${element.date_update}</b>
                </span>
                <br />
                <span class="device-coords">
                    Ultimas Coordenadas: 
                    <b style="display:block;font-size: 14px;font-weight: 600;">
                        <a href="https://www.google.com/maps?q=${element.latitude},${element.longitude}" target="_blank">
                            ${element.latitude},${element.longitude}
                        </a>
                    </b>
                </span>
                <br />
                <span class="badge bg-success device-speed">Velocidad: ${element.speed} MPH</span>&nbsp;&nbsp;
                <span class="badge bg-warning">HDOP: ${element.hdop} MPH</span><br />
            </div>`;

            var infowindow = new google.maps.InfoWindow({
                content: content
            });

            marker.infowindow = infowindow;
            markers.push(marker);

            google.maps.event.addListener(marker, 'click', function (marker, content, infowindow) {
                return function () {
                    // Cerrar el InfoWindow anterior si existe
                    if (currentInfoWindow) {
                        currentInfoWindow.close();
                    }
                    infowindow.setContent(content);
                    infowindow.open(map, marker);
                    currentInfoWindow = infowindow;
                };
            }(marker, content, infowindow));

            const dateUpdate = dayjs(element.date_update).fromNow();

            var CardInnerMaps = `<li id="device-${element.id}" class="list-group-item" style="border-bottom: 1px solid #e1e1e1;padding: 10px 0 !important;border-radius: 10px;">
                <div class="d-flex justify-content-between" for="check-${element.id}" style="font-weight: 600;">
                    <div class="form-check float-start me-2">
                        <input type="checkbox" class="device-check" id="check-${element.id}" style="margin-right:10px;">
                    </div>
                    
                    <div class="w-100 user-desc overflow-hidden">
                        <span class="desc text-muted font-14 d-block" onclick="ViewPositionDevice(${element.latitude}, ${element.longitude}, ${element.id})" style="cursor: pointer;">
                            <h5 class="name mt-0 mb-1">${element.get_vehicle.get_g_p_s.name_device}</h5>
                            <p class="text-truncate">
                                ${element.get_vehicle.get_g_p_s.descript_device}
                                <br />
                                <span class="device-date">${dateUpdate}</span>
                            </p>
                        </span>
                        
                        <div class="d-flex list-options">
                            <a href="javascript:void(0)" onclick="ViewTracksHistory(${element.id})" class="text-center items-center" style="width: 40px;height: 40px;display: flex;align-items: center;justify-content: center;" title="Mostrar historial">
                                <i aria-hidden="true" role="presentation" class="q-icon mdi mdi-transit-connection-variant" style="font-size: 20px;"> </i>
                            </a>
                            <a href="javascript:void(0)" onclick="openCommandsModal(${element.id})" class="text-center items-center" style="width: 40px;height: 40px;display: flex;align-items: center;justify-content: center;" title="Abrir barra de comandos" >
                                <i aria-hidden="true" role="presentation" class="q-icon mdi mdi-animation-play-outline" style="font-size: 20px;"> </i>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex me-2">
                        <img src="${markerIconTruck}" alt="Truck" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="top" title="Vehiculo Asingado" style="border: 0.5px solid #08ff00d4;border-radius: 2003px;padding: 5px;width: 35px;height: 35px;margin: 0 5px;cursor: pointer;">
                        ${(element.get_vehicle.get_box != null)
                    ? `<img src="${markerIconBox}" alt="Caja" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="top" title="Caja Asignada" style="border: 0.5px solid #08ff00d4;border-radius: 2003px;padding: 5px;width: 35px;height: 35px;margin: 0 5px;cursor: pointer;">`
                    : ""
                }
                    </div>
                </div>
            </li>`;

            listGroup.innerHTML += CardInnerMaps;
        }

        trackingMapsCard.classList.remove('d-none');
        ready = true;
    });
}


// Event listener de los checkboxes
document.addEventListener('change', function (e) {
    if (e.target.classList.contains('device-check')) {
        // Obtén todos los checkboxes marcados
        const checkedBoxes = document.querySelectorAll('.device-check:checked');

        // Elimina la clase de todos los elementos
        document.querySelectorAll('.active-card-maps').forEach(el => {
            el.classList.remove('active-card-maps');
        });

        if (checkedBoxes.length > 0) {
            markerFollowId = checkedBoxes[0].id.replace('check-', '');
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
                    // updateDeviceCard(marker.id_gps);
                } else {
                    marker.setMap(null);
                }
            });
        } else {
            markerFollowId = null; // No seguir a nadie
            // Si ninguno está marcado, muestra todos los markers
            markers.forEach(marker => {
                marker.setMap(map);
            });
        }
    }
});


// Buscador de dispositivos
const btnClearSearch = document.getElementById('btnClearSearch');
document.getElementById('searchDevice').addEventListener('input', function (e) {
    const search = e.target.value.toLowerCase();
    if (search.length > 0) {
        btnClearSearch.innerHTML = '<i class="mdi mdi-close"></i>';

        document.querySelectorAll(`#${ContentListGroups} li`).forEach(li => {
            // Puedes buscar por nombre de dispositivo, descripción, etc.
            const name = li.querySelector('.name') ? li.querySelector('.name').textContent.toLowerCase() : '';
            const desc = li.querySelector('.desc') ? li.querySelector('.desc').textContent.toLowerCase() : '';
            if (name.includes(search) || desc.includes(search)) {
                li.style.display = '';
            } else {
                li.style.display = 'none';
            }
        });
    } else {
        btnClearSearch.innerHTML = '<i class="mdi mdi-account-search"></i>';
    }
});

document.getElementById('btnClearSearch').addEventListener('click', function () {
    document.getElementById('searchDevice').value = '';
    btnClearSearch.innerHTML = '<i class="mdi mdi-account-search"></i>';
    document.querySelectorAll(`#${ContentListGroups} li`).forEach(li => {
        li.style.display = '';
    });
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

function getDevicesTrucks(callback) {
    let url = URL_BASE + "/api/getAllTrucks";
    $.ajax({
        url: url,
        type: "GET",
        success: callback,
        jsonp: "json",
        dataType: "json"
    });
}

function getDevicesBox(callback) {
    let url = URL_BASE + "/api/getAllBoxes";
    $.ajax({
        url: url,
        type: "GET",
        success: callback,
        jsonp: "json",
        dataType: "json"
    });
}

function getDispositive(id, callback) {
    let url = URL_BASE + "/api/getDispositive/" + id;
    $.ajax({
        url: url,
        type: "GET",
        success: callback,
        jsonp: "json",
        dataType: "json"
    });
}

function getDispositiveTracks(id, callback) {
    let url = URL_BASE + "/api/getDispositiveTracks/" + id;
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
function updateDeviceCard(IdElement) {
    const li = document.getElementById(`device-${IdElement}`);
    if (li) {

        // Actualiza nombre del dispositivo
        getDispositive(IdElement, function (data) {

            if (data.status === 200) {
                console.log("Actualizando tarjeta del dispositivo:", data);
                const element = data.data;

                // Actualiza fecha
                li.querySelector('.device-date').textContent = dayjs(element.date_update).fromNow();

                // Actualiza velocidad/estado
                // const speedSpan = li.querySelector('.device-speed');
                // if (parseInt(element.speed, 10) > 0) {
                //     speedSpan.innerHTML = `<span class="badge bg-success">Velocidad ${element.speed} Km/h</span>`;
                // } else {
                //     speedSpan.innerHTML = `<span class="badge bg-warning">Detenido</span>`;
                // }


                let marker = markers.find(m => m.id_gps === element.id);
                if (marker.id_gps == element.id) {
                    // Verificamos si las coordenadas no estan vacias
                    let init_pos = Math.abs(marker.lat) + Math.abs(marker.lng);
                    let end_pos = Math.abs(element.latitude) + Math.abs(element.longitude);
                    var newLocation = new google.maps.LatLng(element.latitude, element.longitude);
                    // if (detectChange(element.get_trackings)) {           
                    // if (init_pos != end_pos) { // El repa cambio de posicion
                    marker.setPosition(newLocation);
                    // Solo sigue si este marker es el seleccionado
                    if (markerFollowId && marker.id_gps == markerFollowId) {
                        map.panTo(newLocation);
                        google.maps.event.trigger(marker, 'click');
                    }
                    // Crear polyline para la estela
                    trailPath = new google.maps.Polyline({
                        map,
                        path: [newLocation],
                        strokeColor: "#00F",
                        strokeOpacity: 0.7,
                        strokeWeight: 2
                    });
                    // Actualiza el contenido del InfoWindow

                    // Animar el marcador
                    // En updateDeviceCard, reemplaza la llamada actual por:
                    if (element.get_trackings.positions || element.trackingslast.positions) {

                        let Coordinates = JSON.parse(element.get_trackings.positions);
                        let trackingslast = JSON.parse(element.trackingslast.positions);

                        console.log("Comparando coordenadas actuales vs anteriores");
                        console.log(Coordinates, trackingslast)
                        if (compareCoordinates(Coordinates, trackingslast)) {
                            console.log("Se detectaron cambios en las coordenadas...");
                            console.log("Obteniendo solo las coordenadas nuevas...");
                            // Obtener solo las coordenadas nuevas
                            const newCoordinates = getNewCoordinates(Coordinates, trackingslast);
                            if (newCoordinates.length > 0) {
                                console.log("Animando nuevas coordenadas...");
                                calculateAndDisplayRoute(newCoordinates, marker);
                            } else {
                                console.log("No hay nuevas coordenadas para animar");
                            }
                        } else {
                            console.log("No hay cambios en las coordenadas, no se requiere animación");
                        }
                    } else {
                        console.log("No hay datos de trackings disponibles para animar");
                    }

                }
            }
        });
    }
}

function compareCoordinates(current, last) {
    // Si no hay coordenadas anteriores, considerarlo como cambio
    if (!last || !last.length) return true;

    // Si tienen diferente cantidad de coordenadas, hay cambio
    if (current.length !== last.length) return true;

    // Comparar la última coordenada de cada array
    const currentLast = current[current.length - 1];
    const previousLast = last[last.length - 1];

    return currentLast.Latitude !== previousLast.Latitude ||
        currentLast.Longitude !== previousLast.Longitude ||
        currentLast.Speed !== previousLast.Speed;
}

function getNewCoordinates(current, last) {
    if (!last || !last.length) return current;

    // Encuentra el índice donde empiezan las nuevas coordenadas
    let startIndex = 0;
    for (let i = 0; i < current.length; i++) {
        if (i >= last.length) {
            startIndex = i;
            break;
        }
        if (current[i].Latitude !== last[i].Latitude ||
            current[i].Longitude !== last[i].Longitude) {
            startIndex = i;
            break;
        }
    }

    // Retorna solo las coordenadas nuevas
    return current.slice(startIndex);
}

// Función para actualizar posición y orientación
function updateMarker(lat, lng, angle, marker) {
    // Actualizar marker
    marker.setPosition({ lat, lng });
    const icon = marker.getIcon();
    icon.rotation = angle;
    marker.setIcon(icon);
    var newLocation = new google.maps.LatLng(lat, lng);
    if (!trailPath) {
        trailPath = new google.maps.Polyline({
            map,
            path: [newLocation],
            strokeColor: "#00F",
            strokeOpacity: 0.7,
            strokeWeight: 2
        });
    }

    const path = trailPath.getPath();
    path.push(new google.maps.LatLng(lat, lng));

    // Mantener estela corta (ej: últimos 10 puntos)
    // if (path.getLength() > 10) {
    //     path.removeAt(0);
    // }
}

function drawRoute(coordinates, marker) {
    // Crear el array de LatLng para la polyline
    const path = coordinates.map(point => {
        return {
            angle: point.Angle,
            speed: point.Speed,
            coords: new google.maps.LatLng(point.Latitude, point.Longitude)
        };
    });

    console.log("Dibujando ruta con las siguientes coordenadas:", path);
    // Animar el marker a lo largo de la polyline
    animateMarkerAlongPath(path, marker);
}

function animateMarkerAlongPath(path, marker, duration = 3500) {
    let index = 0;
    if (path.length > 1) {
        map.panTo(path[1].coords);
    }
    function animateSegment(startTime, from, to) {
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);

            // Interpolación lineal entre puntos
            const lat = from.coords.lat() + (to.coords.lat() - from.coords.lat()) * progress;
            const lng = from.coords.lng() + (to.coords.lng() - from.coords.lng()) * progress;
            const angle = from.angle;

            marker.setPosition({ lat, lng });
            updateMarker(lat, lng, angle, marker)
            if (progress < 1) {
                requestAnimationFrame(step);
            } else {
                index++;
                if (index < path.length - 1) {
                    requestAnimationFrame((t) =>
                        animateSegment(t, path[index], path[index + 1])
                    );
                }
            }
        }
        requestAnimationFrame(step);
    }

    if (path.length > 1) {
        animateSegment(null, path[0], path[1]);
    }
}

// Reemplazar la función calculateAndDisplayRoute por:
function calculateAndDisplayRoute(coordinates, marker) {
    if (!coordinates || !coordinates.length) return;

    // Limpiar rutas anteriores si existen
    if (directionsRenderer) {
        directionsRenderer.setMap(null);
    }

    drawRoute(coordinates, marker);
}

function detectChange(coordinates) {
    if (coordinates.length < 2) return false;
    const last = coordinates[coordinates.length - 1];
    const prev = coordinates[coordinates.length - 2];
    // Compara propiedades relevantes
    return last.Latitude !== prev.Latitude ||
        last.Longitude !== prev.Longitude ||
        last.Speed !== prev.Speed;
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

/**
 * Función para abrir el modal de comandos y establecer el ID del dispositivo
 */
function openCommandsModal(deviceId) {
    // Guardamos el ID del dispositivo en el modal
    const modal = document.getElementById('modal-commands');
    modal.setAttribute('data-device-id', deviceId);

    let mlCommIdDevice = document.getElementById('ml-comm-id-device');
    let mlCommTitle = document.getElementById('ml-comm-title');
    let mlCommPhone = document.getElementById('ml-comm-phone');

    // Verificamos que existan todos los elementos necesarios
    if (!mlCommIdDevice || !mlCommTitle || !mlCommPhone) {
        console.error("Faltan elementos del modal");
        return;
    }

    // Buscamos en ContentDevices el dispositivo por su ID
    const device = ContentDevices.find(dev => dev.id === deviceId);
    if (device) {
        console.log("Dispositivo encontrado para el modal:", device);
        mlCommIdDevice.value = device.get_vehicle.get_g_p_s.id ? device.get_vehicle.get_g_p_s.id : null;
        mlCommTitle.innerHTML = device.get_vehicle ? device.get_vehicle.name_unit : 'Sin Vehiculo';
        mlCommPhone.innerHTML = device.get_vehicle.get_g_p_s.phone ? device.get_vehicle.get_g_p_s.phone : 'Sin Teléfono';

        // Abrimos el modal
        const modalInstance = new bootstrap.Modal(modal);
        modalInstance.show();
    } else {
        console.error("No se pudieron obtener los datos del dispositivo para el modal.");
        Swal.fire({
            icon: "error",
            type: 'error',
            title: 'Oops... Hubo un error al abrir el modal de comandos.',
            text: "No se encontraron los datos del dispositivo."
        });
    }
}

document.getElementById('select_command').addEventListener('change', function (e) {
    // Obtenemos el data-descript del elemento seleccionado
    const selectedOption = e.target.options[e.target.selectedIndex];
    const descript = selectedOption.getAttribute('data-descript');
    console.log(descript)
    document.getElementById('ml-comm-descript-command').innerHTML = descript ? descript : 'Selecciona un comando para visualizar la descripción.';
});

/**
 * Envio de Comandos al dispositivo SMS
 */
const sendCommandForm = document.getElementById('send_command_form');
sendCommandForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    console.log("Enviando comando al dispositivo...");
    const formData = new FormData(sendCommandForm);
    SendDataForm(sendCommandForm.action, formData);
});

/**
 * Funcion para visualizar el historial de rutas
 */
function ViewTracksHistory(deviceId) {
    ContentListGroups = 'list-group-history';
    lblViewListDevices.innerHTML = 'Historial de Rutas';
    listDevicesGroup.style.display = 'none';
    viewTrackingDevice.style.display = 'block';
    let firstAddress;
    let lastAddress;
    // Obtenemos el tracking del dispositivo
    getDispositiveTracks(deviceId, async function (data) {
        if (data.status === 200) {
            const element = data.data;
            console.log("Datos del dispositivo para el modal:", element);

            listGroup = document.getElementById(ContentListGroups);
            // Limpiamos el listado
            listGroup.innerHTML = '';
            const dateUpdate = dayjs(element.date_update).fromNow();

            var CardInnerMaps = `<li id="device-${element.id}" class="list-group-item" style="border-bottom: 1px solid #e1e1e1;padding: 10px 0 !important;border-radius: 10px;">
                <div class="d-flex justify-content-between" for="check-${element.id}" style="font-weight: 600;">
                    <div class="form-check float-start me-2" style="cursor:pointer;" onclick="BackToListDevices()">
                        <i aria-hidden="true" role="presentation" class="q-icon mdi mdi-arrow-left" style="font-size: 20px;color: #00ce5a;"> </i>
                    </div>
                    
                    <div class="w-100 user-desc overflow-hidden">
                        <span class="desc text-muted font-14 d-block" style="cursor: pointer;">
                            <h5 class="name mt-0 mb-1">${element.get_vehicle.get_g_p_s.name_device}</h5>
                            <p class="text-truncate">
                                Ultima actualizacion: <span class="device-date">${dateUpdate}</span>
                            </p>
                        </span>
                        
                        <div class="d-flex list-options">
                            <a href="javascript:void(0)" onclick="ViewTracksHistory(${element.id})" class="text-center items-center" style="width: 40px;height: 40px;display: flex;align-items: center;justify-content: center;" title="Mostrar historial">
                                <i aria-hidden="true" role="presentation" class="q-icon mdi mdi-transit-connection-variant" style="font-size: 20px;color: #00ce5a;"> </i>
                            </a>
                            <a href="javascript:void(0)" onclick="openCommandsModal(${element.id})" class="text-center items-center" style="width: 40px;height: 40px;display: flex;align-items: center;justify-content: center;" title="Abrir barra de comandos" >
                                <i aria-hidden="true" role="presentation" class="q-icon mdi mdi-animation-play-outline" style="font-size: 20px;"> </i>
                            </a>
                        </div>
                    </div>
                    <div class="d-flex me-2">
                        <img src="${markerIconTruck}" alt="Truck" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="top" title="Vehiculo Asingado" style="border: 0.5px solid #08ff00d4;border-radius: 2003px;padding: 5px;width: 35px;height: 35px;margin: 0 5px;cursor: pointer;">
                        ${(element.get_vehicle.get_box != null)
                    ? `<img src="${markerIconBox}" alt="Caja" data-bs-container="#tooltip-container" data-bs-toggle="tooltip" data-bs-placement="top" title="Caja Asignada" style="border: 0.5px solid #08ff00d4;border-radius: 2003px;padding: 5px;width: 35px;height: 35px;margin: 0 5px;cursor: pointer;">`
                    : ""
                }
                    </div>
                </div>
            </li>`;

            listGroup.innerHTML += CardInnerMaps;
            // document.querySelectorAll('.history_tracks').forEach(el => el.add('show','active'));
            let ListHistory = `<div class="timeline-history loader-history-timeline">
                        <!-- Sin Historial -->
                        <div class="timeline-history-item">
                            <div class="timeline-history-icon" style="background:green;">S</div>
                            <div class="timeline-history-card">
                                <div class="col-lg-12 text-center pt-8" id="loading">
                                    <div class="spinner-border avatar-lg text-primary m-2" role="status"></div>
                                </div>
                            </div>
                        </div>
                    </div>`;
            listGroup.innerHTML += ListHistory;
            // Mostramos las rutas en el mapa
            if (element.tracking_history && element.tracking_history.length > 0) {
                for (let x = 0; x < element.tracking_history.length; x++) {
                    const device = element.tracking_history[x];
                    // Por cada device:
                    window.deviceTracks[x] = device.tracks;
                    let CountTrascks = device.tracks.length;
                    let FirstDate = device.tracks[0].date_updated;
                    let LastDate = device.tracks[CountTrascks - 1].date_updated;

                    // Obtenemos la Primer y ultima coordenadas
                    let CountCoordsf = device.tracks[CountTrascks - 1].positions.length;
                    let FirstCoords = device.tracks[0].positions[0];
                    let LastCoords = device.tracks[CountTrascks - 1].positions[CountCoordsf - 1];

                    // const Coordinates = element[x].tracks;
                    const SalidaFecha = dayjs(FirstDate).isValid() ? dayjs(FirstDate).format('dddd DD [de] MMMM [del] YYYY [a las] HH:MM') : 'Fecha no disponible';
                    const LlegadaFecha = dayjs(LastDate).isValid() ? dayjs(LastDate).format('dddd DD [de] MMMM [del] YYYY [a las] HH:MM') : 'Fecha no disponible';

                    // Decodificamos las coordenadas para obtener direccion en texto Geocoder
                    geocoder = new google.maps.Geocoder();
                    await geocoder.geocode({ location: { lat: FirstCoords.Latitude, lng: FirstCoords.Longitude } }).then((response) => {
                        if (response.results[0]) {
                            firstAddress = response.results[0].formatted_address;
                        } else {
                            firstAddress = FirstCoords.Latitude + ',' + FirstCoords.Longitude;
                        }
                    }).catch((e) => {
                        console.error("Geocoder failed due to: " + e);
                        firstAddress = FirstCoords.Latitude + ',' + FirstCoords.Longitude;
                    });

                    await geocoder.geocode({ location: { lat: LastCoords.Latitude, lng: LastCoords.Longitude } }).then((response) => {
                        if (response.results[0]) {
                            lastAddress = response.results[0].formatted_address;
                        } else {
                            lastAddress = LastCoords.Latitude + ',' + LastCoords.Longitude;
                        }
                    }).catch((e) => {
                        console.error("Geocoder failed due to: " + e);
                        lastAddress = LastCoords.Latitude + ',' + LastCoords.Longitude;
                    });

                    // let Coordinates = [];
                    // for (let i = 0; i < device.tracks.length; i++) {
                    //     const listCords = device.tracks[i];
                    //     Coordinates = Coordinates.concat(listCords.positions);
                    // }
                    // console.log("Coordenadas totales para la ruta:", Coordinates);

                    // Agregamos las coordenadas al mapa

                    let html = `<div class="timeline-history" style="cursor: pointer;" id="coords_tracks_${x}" onclick="ViewTracksCoords(${deviceId},${x})">
                        <!-- Trip B -->
                        <div class="timeline-history-item">
                            <div class="timeline-history-icon" style="background:#f97316;">B</div>
                            <div class="timeline-history-card">
                                <div>
                                <span class="timeline-history-time">${LlegadaFecha}</span>
                                </div>
                                <div class="timeline-history-meta">
                                ${lastAddress}
                                </div>
                                <div class="timeline-history-detail">
                                    <b>Altitude</b>: ${LastCoords.Altitude} ft <br />
                                    <b>Angle</b>: ${LastCoords.Angle}° <br />
                                    <b>Satellites</b>: ${LastCoords.Satellites} <br />
                                    <b>Speed</b>: ${(LastCoords.Speed / 1000).toFixed(2)} km/h
                                </div>
                            </div>
                        </div>
                        <!-- Trip A -->
                        <div class="timeline-history-item">
                            <div class="timeline-history-icon" style="background:#10b981;">A</div>
                            <div class="timeline-history-card">
                                <div>
                                <span class="timeline-history-time">${SalidaFecha}</span>
                                </div>
                                <div class="timeline-history-meta">
                                    ${firstAddress}
                                </div>
                                <div class="timeline-history-detail">
                                    <b>Altitude</b>: ${FirstCoords.Altitude} ft <br />
                                    <b>Angle</b>: ${FirstCoords.Angle}° <br />
                                    <b>Satellites</b>: ${FirstCoords.Satellites} <br />
                                    <b>Speed</b>: ${(FirstCoords.Speed / 1000).toFixed(2)} km/h
                                </div>
                            </div>
                        </div>
                    </div>`;

                    (x == 1) ? document.querySelectorAll('.loader-history-timeline').forEach(el => el.remove()) : '';
                    ListHistory += html;

                    if (x == 0) {
                        ViewPositionDevice(FirstCoords.Latitude, FirstCoords.Longitude, element.id)
                    }
                }


                listGroup.innerHTML += ListHistory;
                document.querySelectorAll('.loader-history-timeline').forEach(el => el.remove());
            } else {
                console.log("No hay datos de trackings disponibles para mostrar historial");
                ListHistory += `<div class="timeline-history">
                        <!-- Sin Historial -->
                        <div class="timeline-history-item">
                            <div class="timeline-history-icon" style="background:red;">X</div>
                            <div class="timeline-history-card">
                                <div>
                                    <span class="timeline-history-time">Sin historial</span> |
                                    <span class="timeline-history-title">Vacio</span>
                                </div>
                                <div class="timeline-history-meta">
                                    Este dispositivo no tiene historial de rutas disponible.
                                </div>
                            </div>
                        </div>
                    </div>`;

                listGroup.innerHTML += ListHistory;
                document.querySelectorAll('.loader-history-timeline').forEach(el => el.remove());
            }
        } else {
            console.error("No se pudieron obtener los datos del dispositivo para el modal.");
        }
    });
}

let modalMap = null;
let modalMarker = null;
let modalTrailPath = null;
let modalAnimFrameId = null;
// Función para obtener el icono según la velocidad
function getMarkerIcon(speed, angle = 0) {
    return {
        path: speed > 0 ? google.maps.SymbolPath.FORWARD_CLOSED_ARROW : google.maps.SymbolPath.CIRCLE,
        scale: speed > 0 ? 5 : 8,
        rotation: angle,
        strokeColor: "#000",
        fillColor: speed > 0 ? "#00F" : "#f44336", // Azul en movimiento, Rojo detenido
        fillOpacity: 1
    };
}

function initModalMap(coordinates) {
    // Limpiar instancias previas si existen
    if (modalTrailPath) {
        modalTrailPath.setMap(null);
        modalTrailPath = null;
    }
    if (modalMarker) {
        modalMarker.setMap(null);
        modalMarker = null;
    }
    
    // Crear el mapa en el modal
    modalMap = new google.maps.Map(document.getElementById('modal-map'), {
        center: { lat: coordinates[0].Latitude, lng: coordinates[0].Longitude },
        zoom: 10,
        disableDefaultUI: true,
        gestureHandling: 'greedy',
        zoomControl: true,
        mapTypeControl: true,
        streetViewControl: false,
        fullscreenControl: true,
        styles: MapStyle,
    });
    
    // Calcular bounds para todos los puntos
    let bounds = new google.maps.LatLngBounds();
    coordinates.forEach(coord => {
        bounds.extend(new google.maps.LatLng(coord.Latitude, coord.Longitude));
    });
    
    // Ajustar el mapa para mostrar todos los puntos
    modalMap.fitBounds(bounds);

    // Crear el marcador


    modalMarker = new google.maps.Marker({
        position: { lat: coordinates[0].Latitude, lng: coordinates[0].Longitude },
        map: modalMap,
        icon: getMarkerIcon(coordinates[0].Speed, coordinates[0].Angle)
    });

    // Crear el path para la estela
    modalTrailPath = new google.maps.Polyline({
        map: modalMap,
        path: [],
        strokeColor: "#00F",
        strokeOpacity: 0.7,
        strokeWeight: 2.5
    });
}

function ViewTracksCoords(id_device, deviceId) {
    const tracks = window.deviceTracks[deviceId];
    if (!tracks) {
        Swal.fire({
            icon: "error",
            type: 'error',
            title: 'Error',
            text: 'No hay datos de ruta para este dispositivo'
        });
        return;
    }

    let Coordinates = [];
    for (let i = tracks.length - 1; i >= 0; i--) {
        const device = tracks[i];
        const reversedPositions = device.positions.slice().reverse();
        Coordinates = Coordinates.concat(reversedPositions);
    }

    if (!Coordinates || !Coordinates.length) return;

	// Abrimos el modal
	const modalElement = document.getElementById('modal-tracks');
	const modal = new bootstrap.Modal(modalElement);
	
	function onShown() {
		initModalMap(Coordinates);
		const path = Coordinates.map(point => {
			return {
				angle: point.Angle,
				speed: point.Speed,
				coords: new google.maps.LatLng(point.Latitude, point.Longitude)
			};
		});
		console.log("Dibujando ruta con las siguientes coordenadas:", path);
		animateMarkerInModal(path, modalMarker);
	}

	function onHidden() {
		// Cancelar cualquier animación en curso
		if (modalAnimFrameId) {
			cancelAnimationFrame(modalAnimFrameId);
			modalAnimFrameId = null;
		}
		// Limpiar en orden correcto
		if (modalTrailPath) {
			modalTrailPath.setMap(null);
			modalTrailPath = null;
		}
		if (modalMarker) {
			modalMarker.setMap(null);
			modalMarker = null;
		}
		if (modalMap) {
			google.maps.event.clearInstanceListeners(modalMap);
		}
		modalMap = null;
	}

	modalElement.addEventListener('shown.bs.modal', onShown, { once: true });
	modalElement.addEventListener('hidden.bs.modal', onHidden, { once: true });
	modal.show();
}

function animateMarkerInModal(path, marker, duration = 500) {
    let index = 0;

    function animateSegment(startTime, from, to) {
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);

            // Interpolación lineal entre puntos
            const lat = from.coords.lat() + (to.coords.lat() - from.coords.lat()) * progress;
            const lng = from.coords.lng() + (to.coords.lng() - from.coords.lng()) * progress;
            const angle = from.angle;

			// Si se cerró el modal, detener animación
			if (!modalMap || !marker) {
				return;
			}

			// Actualizar posición del marcador
			marker.setPosition({ lat, lng });
			
			// Hacer que el mapa siga al marcador si existe
			if (modalMap) {
				modalMap.panTo({ lat, lng });
			}

            updateModalMarker(lat, lng, angle, marker);

            if (progress < 1) {
				modalAnimFrameId = requestAnimationFrame(step);
            } else {
                index++;
                if (index < path.length - 1) {
					modalAnimFrameId = requestAnimationFrame((t) =>
                        animateSegment(t, path[index], path[index + 1])
                    );
                }
            }
        }
		modalAnimFrameId = requestAnimationFrame(step);
    }

    if (path.length > 1) {
		if (modalMap) {
			modalMap.panTo(path[0].coords);
		}
        animateSegment(null, path[0], path[1]);
    }
}

function updateModalMarker(lat, lng, angle, marker, speed) {
    marker.setPosition({ lat, lng });
    marker.setIcon(getMarkerIcon(speed, angle));

    if (modalTrailPath) {
        const path = modalTrailPath.getPath();
        path.push(new google.maps.LatLng(lat, lng));
    }
}

function ViewPositionOnMap(id_device, tracksJSON) {
    let tracks = JSON.parse(tracksJSON);
    if (tracks && tracks.length > 0) {
        console.log("Mostrando ruta en el mapa con las siguientes coordenadas:", tracks);
        let Coordinates = [];
        for (let i = 0; i < tracks.length; i++) {
            const device = tracks[i];
            Coordinates = Coordinates.concat(device.positions);
        }

        console.log("Coordenadas totales para la ruta:", Coordinates);
        calculateAndDisplayRoute(Coordinates, markers.find(m => m.id_gps == id_device));
    } else {
        console.log("No hay datos de trackings disponibles para mostrar en el mapa");
        Swal.fire({
            icon: "info",
            type: 'info',
            title: 'Sin datos',
            text: "Este dispositivo no tiene historial de rutas disponible."
        });
    }
}


function BackToListDevices() {

    listGroup = document.getElementById('list-group-history');
    // Limpiamos el listado
    listGroup.innerHTML = `<div class="col-lg-12 text-center pt-8" id="loading">
                                <div class="spinner-border avatar-lg text-primary m-2" role="status"></div>
                            </div>`;
    listDevicesGroup.style.display = 'block';
    viewTrackingDevice.style.display = 'none';
    ContentListGroups = 'list-group-all';
    lblViewListDevices.innerHTML = 'Dispositivos Generales';

    getDevices(function (data) { ShowMaps(data); });
}

/**
 * Envio de Formularios para Modal Add Unit/Box
 */

addUnitForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    console.log("Enviando formulario de nueva unidad...");
    const formData = new FormData(addUnitForm);
    SendDataForm(addUnitForm.action, formData);
});

addBoxForm.addEventListener('submit', async function (event) {
    event.preventDefault();
    console.log("Enviando formulario de nueva caja...");
    const formData = new FormData(addBoxForm);
    SendDataForm(addBoxForm.action, formData);
});

const SendDataForm = async (url, formData) => {
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                    .getAttribute('content')
            },
            body: formData
        });

        if (response.ok) {
            const data = await response.json();
            console.log(data);
            if (data.status === 'success') {
                Swal.fire({
                    icon: "success",
                    type: 'success',
                    title: '¡Éxito!',
                    text: data.message
                }).then(() => {
                    if (data.reload) window.location.reload();
                });
            } else {
                Swal.fire({
                    icon: "error",
                    type: 'error',
                    title: 'Oops... Hubo un error al enviar el formulario.',
                    text: data.message
                }).then(() => {
                    // window.location.reload();
                });
            }
        } else {
            const errorData = await response.json();
            console.error('Errores:', errorData);
            Swal.fire({
                icon: "error",
                type: 'error',
                title: 'Oops... Hubo un error al enviar el formulario.',
                text: errorData.message
            }).then(() => {
                window.location.reload();
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: "error",
            type: 'error',
            title: 'Oops...',
            text: "Ocurrió un error inesperado."
        }).then(() => {
            window.location.reload();
        });
    }
}


function MinimizeCardMaps() {
    trackingMapsCard.classList.toggle('minimize-card-maps');
    floatingCardMaps.classList.toggle('active');
}


// Expose functions to the global scope
window.initMap = initMap;
window.openCommandsModal = openCommandsModal;

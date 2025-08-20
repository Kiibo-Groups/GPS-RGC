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
    markerFollowId = null,
    body = document.querySelector('body'),
    navbarCustom = document.querySelector('.navbar-custom'),
    listGroup = document.getElementById('list-group'),
    trackingMapsCard = document.querySelector('.tracking-maps-card');
var directionsService;
var directionsRenderer;
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
            });

            directionsService = new google.maps.DirectionsService();
            directionsRenderer = new google.maps.DirectionsRenderer({
                map: map
            });

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
    updateDeviceCard(data);
});


/**
 * 
 * Funciones del Mapa 
 */

let markers = [];
var ready = false; // Carga de markers
function ShowMaps(data) {

    $.map(data, function (el) {
        console.log("Cargando dispositivo GPS", el);
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
                    infowindow.setContent(content);
                    infowindow.open(map, marker);
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

        // Cuando se hace click en un marker del mapa
        // markers.forEach(marker => {
        //     google.maps.event.addListener(marker, 'click', function () {
        //         markerFollowId = marker.id_gps; // Seguir este marker
        //     });
        // });



        trackingMapsCard.classList.remove('d-none');
        // Applicamos Condensed al Sidebar
        navbarCustom.setAttribute('style', 'background-color: #ffffff;');
        body.setAttribute('data-sidebar-size', "condensed");
        body.setAttribute('data-sidebar-color', "light");

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
document.getElementById('searchDevice').addEventListener('input', function (e) {
    const search = e.target.value.toLowerCase();
    document.querySelectorAll('#list-group li').forEach(li => {
        // Puedes buscar por nombre de dispositivo, descripción, etc.
        const name = li.querySelector('.name') ? li.querySelector('.name').textContent.toLowerCase() : '';
        const desc = li.querySelector('.desc') ? li.querySelector('.desc').textContent.toLowerCase() : '';
        if (name.includes(search) || desc.includes(search)) {
            li.style.display = '';
        } else {
            li.style.display = 'none';
        }
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

function getDispositive(id, callback) {
    // let url = "{{ route('getDispositive', ':id') }}".replace(':id', id);
    let url = URL_BASE + "/api/getDispositive/" + id;
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
                const element = data.data;

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
                    var newLocation = new google.maps.LatLng(element.latitude, element.longitude);
                    // if (detectChange(element.get_trackings)) {           
                    // if (init_pos != end_pos) { // El repa cambio de posicion
                    marker.setPosition(newLocation);
                    map.panTo(newLocation);
                    google.maps.event.trigger(marker, 'click');
                    
                    // Actualiza el contenido del InfoWindow
                    var newContent =
                        '<div id="content" style="width: auto; height: auto;">' +
                        '<h3>Dispositivo GPS <span class="badge bg-info">' + element.get_g_p_s
                            .uuid_device +
                        '</span> </h3>' +
                        '<span>GPS Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                        element.get_g_p_s.name_device + '</b></span>' +
                        '<span>Vehiculo Asignado: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                        element.get_vehicle.name_unit + '</b></span><br />' +
                        '<span>Ultima actualización: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                        element.date_update + '</b></span><br />' +
                        '<span>Coordenadas: <b style="display:block;font-size: 14px;font-weight: 600;">' +
                        '<a href="https://www.google.com/maps?q=' + element.latitude + ',' +
                        element.longitude + '" target="_blank">' + element.latitude + ',' +
                        element.longitude + '</a></b></span><br />' +
                        '<span class="badge bg-success">Velocidad: ' + element.speed +
                        ' MPH</span>&nbsp;&nbsp;' +
                        '<span class="badge bg-warning">HDOP: ' + element.hdop +
                        ' MPH</span><br />' +
                        '</div>';

                    // Animar el marcador
                    // En updateDeviceCard, reemplaza la llamada actual por:
                    let Coordinates = JSON.parse(element.get_trackings.positions);
                    let trackingslast = JSON.parse(element.trackingslast.positions);

                    console.log("Comparando coordenadas actuales vs anteriores");
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
                    // animateMarker(marker, element.get_trackings, 1000, async function (coord, index, coordinates) {
                    //     // Callback para detectar cambios
                    //     await moveMarker(marker,element,newContent, coord, 0);
                    // });
                    // }else {
                    //     console.log("No hay cambio de posicion", element);
                    //     console.log("Posicion actual", init_pos);
                    //     console.log("Posicion nueva", end_pos);
                    // }
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

function drawRoute(coordinates, marker) {
    // Crear el array de LatLng para la polyline
    const path = coordinates.map(point => {
        return new google.maps.LatLng(point.Latitude, point.Longitude);
    });

    // Animar el marker a lo largo de la polyline
    animateMarkerAlongPath(path, marker);
}

function animateMarkerAlongPath(path, marker, duration = 3500) {
    let index = 0;
    map.panTo(path[1]);
    function animateSegment(startTime, from, to) {
        function step(timestamp) {
            if (!startTime) startTime = timestamp;
            const progress = Math.min((timestamp - startTime) / duration, 1);

            // Interpolación lineal entre puntos
            const lat = from.lat() + (to.lat() - from.lat()) * progress;
            const lng = from.lng() + (to.lng() - from.lng()) * progress;
            marker.setPosition({ lat, lng });
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

// function animateMarker(marker, coordinates, interval = 500, onUpdate) {
//     let index = 0;
//     let currentIndex = 0;
//     function move() {
//         if (index < 1) {
//             const coord = JSON.parse(coordinates[index].positions);

//             // Callback para detectar cambios
//             if (typeof onUpdate === 'function') {
//                 onUpdate(coord, index, coordinates);
//             }
//             console.log("Aumento de index", index);
//             index++;
//             setTimeout(move, interval);
//         }
//     }
//     move();
// }

// function moveMarker(marker, element, newContent, coordinates, currentIndex = 0) {

//     if (!coordinates || !coordinates.length) return; // Seguridad 
//     if (currentIndex < coordinates.length) {
//         marker.setPosition(new google.maps.LatLng(coordinates[currentIndex].Latitude, coordinates[currentIndex].Longitude));
//         marker.lat = coordinates[currentIndex].Latitude;
//         marker.lng = coordinates[currentIndex].Longitude;

//         // Solo sigue si este marker es el seleccionado
//         if (markerFollowId && marker.id_gps == markerFollowId) {
//             map.panTo(new google.maps.LatLng(coordinates[currentIndex].Latitude, coordinates[currentIndex].Longitude));
//         }
//         currentIndex++;
//         setTimeout(function () {
//             moveMarker(marker, element, newContent, coordinates, currentIndex);
//         }, 800); // Cambia la posición cada 2 segundos
//     } else {
//         let lxs = markers.findIndex(m => m.id_gps === marker.id);
//         if (lxs !== -1) {
//             markers[lxs].setMap(null); // Quita el marker viejo del mapa
//             markers.splice(lxs, 1);    // Elimina del array


//             const newMarker = new google.maps.Marker({
//                 position: newLocation,
//                 map: map,
//                 title: element.get_vehicle.name_unit,
//                 icon: markerIcon,
//                 lat: element.latitude,
//                 lng: element.longitude,
//                 id_gps: element.id
//             });

//             var infowindow = new google.maps.InfoWindow({
//                 content: newContent
//             });

//             newMarker.infowindow = infowindow;

//             markers.push(newMarker);
//         }
//     }
// }

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




// Expose initMap to the global scope
window.initMap = initMap;

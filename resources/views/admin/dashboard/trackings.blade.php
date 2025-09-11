<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8" />
    <title>Seguimiento de Dispositivos | Startrack360</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Empresa Mexicana especializada en el autotransporte de carga terrestre. " name="description" />
    <meta content="KiiboGroups" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.ico') }}">

    <!-- App css -->
    <link href="{{ asset('assets/css/config/default/bootstrap.min.css') }}" rel="stylesheet" type="text/css"
        id="bs-default-stylesheet" />
    <link href="{{ asset('assets/css/config/default/app.css') }}" rel="stylesheet" type="text/css"
        id="app-default-stylesheet" />

    <link href="{{ asset('assets/css/config/default/bootstrap-dark.min.css') }}" rel="stylesheet" type="text/css"
        id="bs-dark-stylesheet" disabled="disabled" />
    <link href="{{ asset('assets/css/config/default/app-dark.min.css?v=' . now()) }}" rel="stylesheet" type="text/css"
        id="app-dark-stylesheet" disabled="disabled" />


    <!-- icons -->
    <link href="{{ asset('assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/main.css?v=' . time()) }}" rel="stylesheet" type="text/css" />
    <!-- CSS Extras -->
    <script>
        var URL_BASE = "{{ url('/') }}";
        let markerIcon = "{{ asset('assets/images/marker2.png') }}";
        let markerIconTruck = "{{ asset('assets/images/marker.png') }}";
        let markerIconBox = "{{ asset('assets/images/marker-caja.png') }}";
    </script>
</head>
<body>
    <div class="tracking-maps-card d-none">
        <div class="crad">
            <div class="card-header" style="padding: 5px 10px !important;">
                <button class="btn btn-sm d-flex align-items-center"
                    onclick="window.location.href='{{ url('dash') }}'">
                    <i class="mdi mdi-arrow-left" style="font-size: 18px;margin: 0 15px 0 0;"></i>
                    Ir a Dashboard
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body collapse show">
                <ul class="nav nav-tabs nav-bordered align-items-baseline justify-content-between">
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link" data-bs-toggle="dropdown" aria-haspopup="true"
                            aria-expanded="false">
                            <div class="btn-group dropstart" role="group">
                                <i class="mdi mdi-menu" style="font-size: 24px;cursor: pointer;"></i>
                                <div class="dropdown-menu"
                                    style="box-shadow:0 6px 6px -3px rgba(0,0,0,0.2),0 10px 14px 1px rgba(0,0,0,0.14),0 4px 18px 3px rgba(0,0,0,0.12)">
                                    <a class="dropdown-item change_view_disp active" href="#control_rutas" data-bs-toggle="tab"
                                        aria-expanded="false" data-view="control_rutas">Ver Dispositivos Generales</a>
                                    <a class="dropdown-item change_view_disp" href="#gps_tracto" data-bs-toggle="tab"
                                        aria-expanded="false" data-view="gps_tracto">Ver GPS de Tracto</a>
                                    <a class="dropdown-item change_view_disp" href="#gps_cajas" data-bs-toggle="tab"
                                        aria-expanded="false" data-view="gps_cajas">Ver GPS de Cajas</a>
                                    <div class="dropdown-divider"></div>
                                </div>
                            </div>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void(0)" class="nav-link" id="lblViewListDevices">
                            Dispositivos Generales
                        </a>
                    </li>
                    <li class="nav-item">
                        <div class="btn-group" role="group">
                            <a href="javascript:void(0)" class="nav-link" data-bs-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false">
                                <img src="{{ asset('assets/images/marker.png') }}" alt="marker"
                                    style="width: 40px;border: 0.5px solid #0000ff5e;border-radius: 2003px;padding: 5px;">
                            </a>
                            <div class="dropdown-menu"
                                style="box-shadow:0 6px 6px -3px rgba(0,0,0,0.2),0 10px 14px 1px rgba(0,0,0,0.14),0 4px 18px 3px rgba(0,0,0,0.12)">
                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#modal-add-unity" style="display: flex;align-items: center;">
                                    <i class="q-icon text-primary mdi mdi-plus-circle-outline" style="font-size: 20px"></i>&nbsp;&nbsp;
                                    Agregar Nuevo Dispositivo
                                </a>
                                <a class="dropdown-item" href="#" data-bs-toggle="modal"
                                    data-bs-target="#modal-add-boxes" style="display: flex;align-items: center;">
                                    <i class="q-icon text-primary mdi mdi-plus-circle-outline" style="font-size: 20px"></i>&nbsp;
                                    Agregar Nueva Caja
                                </a>
                            </div>
                        </div>
                    </li>
                </ul>
                <div class="tab-content" id="list-devices-group">
                    <div class="btn-group d-flex align-items-center justify-content-between px-3 pb-2">
                        <input type="search" class="form-control" id="searchDevice" clear
                            placeholder="Buscar dispositivo...">
                        <button class="btn btn-primary waves-effect waves-light" id="btnClearSearch">
                            <i class="mdi mdi-account-search"></i>
                        </button>
                    </div>

                    <div class="tab-pane show active" id="control_rutas">
                        <div id="sidebar-menu">
                            <ul id="list-group-all" class="mb-0 user-list">
                                <div class="col-lg-12 text-center pt-8" id="loading">
                                    <div class="spinner-border avatar-lg text-primary m-2" role="status"></div>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-pane" id="gps_tracto">
                        <div id="sidebar-menu">
                            <ul id="list-group-trucks" class="mb-0 user-list">
                                <div class="col-lg-12 text-center pt-8" id="loading">
                                    <div class="spinner-border avatar-lg text-primary m-2" role="status"></div>
                                </div>
                            </ul>
                        </div>
                    </div>

                    <div class="tab-pane" id="gps_cajas">
                        <div id="sidebar-menu">
                            <ul id="list-group-box" class="mb-0 user-list">
                                <div class="col-lg-12 text-center pt-8" id="loading">
                                    <div class="spinner-border avatar-lg text-primary m-2" role="status"></div>
                                </div>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="tab-content" id="view-tracking-device" style="display: none;">
                    <div class="tab-pane show active" id="history_tracks">
                        <div id="sidebar-menu">
                            <ul id="list-group-history" class="mb-0 user-list">
                                <div class="col-lg-12 text-center pt-8" id="loading">
                                    <div class="spinner-border avatar-lg text-primary m-2" role="status"></div>
                                </div>
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
                    style="position: relative; overflow: hidden;height:  100vh !important;"></div>

                <!-- Hide controls until they are moved into the map.
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
                </div> -->
            </div>
        </div>
    </div>


    {{-- Modal para envio de Notificaciones y comandos --}}
    <div id="modal-commands" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content"> 
                <div class="modal-header">
                    <h4 class="modal-title">
                        Barra de comandos
                        <span id="ml-comm-title" class="text-muted" style="font-size: 16px;display:block;"></span>
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('sendCommand') }}" method="POST" id="send_command_form"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" id="ml-comm-id-device" name="device_id" value="">
                        <div class="row mb-4 border-bottom pb-3">
                            <div class="col-md-12">
                                <h4 class="modal-title">
                                    Crear Comando
                                    <span class="text-muted" style="font-size: 16px;display:block;">
                                        Para el dispositivo: <b id="ml-comm-phone"></b>
                                    </span>
                                </h4>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-10">
                                <div class="mb-3">
                                    <input type="text" class="form-control" id="name_command" name="name_command"
                                        placeholder="Nombre del comando">
                                    <div class="form-text text-muted">Escriba un nombre para guardar el comando</div>
                                </div>
                            </div>

                            <div class="col-md-8">
                                <div class="mb-3">
                                    <input type="password" class="form-control" id="password_command" name="password_command"
                                        placeholder="Password (si aplica)">
                                    <div class="form-text text-muted">Ingresa una contraseña si es necesario</div>
                                </div>
                            </div>


                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label for="select_command" class="form-label">Comando a enviar</label>
                                    <select name="select_command" id="select_command" class="form-select">
                                        <option value="null"
                                            data-descript="Selecciona un comando para visualizar la descripción.">
                                            Selecciona un comando</option>
                                        <option value="delrecords"
                                            data-descript="Este comando SMS se utiliza para eliminar todos los registros de la memoria flash interna del dispositivo FM">
                                            Del Records</option>
                                        <option value="reset"
                                            data-descript="Este comando SMS se utiliza para reiniciar el dispositivo FM. El dispositivo se reiniciará y sus parámetros de configuración no se perderán">
                                            Reset</option>
                                        {{-- <option value="setconnection"
                                            data-descript="Este comando SMS se utiliza para cambiar permanentemente la configuración del dispositivo FM: APN">
                                            SetConnection</option> --}}
                                    </select>
                                </div>

                                <div class="form-text text-muted" id="ml-comm-descript-command">
                                    Selecciona un comando para visualizar la descripción.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-info waves-effect waves-light">Enviar SMS</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para Agregado de Unidades --}}
    <div id="modal-add-unity" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Agregar nueva unidad

                        <img src="{{ asset('assets/images/marker.png') }}" alt="Truck" title="Vehiculo Asingado"
                            style="border: 0.5px solid #08ff00d4;border-radius: 2003px;padding: 5px;width: 35px;height: 35px;margin: 0 5px;cursor: pointer;">
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('vehicle_units.store') }}" method="POST" id="add_new_unit"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name_unit" class="form-label">Nombre de la unidad</label>
                                <input type="text" name="name_unit" id="name_unit" class="form-control"
                                    required="required">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="id_unit" class="form-label">Identificador unico</label>
                                <input type="text" name="id_unit" id="id_unit" class="form-control"
                                    required="required">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="registration_unit" class="form-label">Matricula y/o # Folio</label>
                                <input type="text" name="registration_unit" id="registration_unit"
                                    class="form-control" required="required">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="descript" class="form-label">Descripción Corta</label>
                                <input type="tel" name="descript" id="descript" class="form-control"
                                    required="required">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gps_devices_id" class="form-label">Asignación de Dispositivo</label>
                                <select name="gps_devices_id" id="gps_devices_id" class="form-select"
                                    required="required">
                                    <option value="">
                                        Selecciona un dispositivo
                                    </option>
                                    @foreach ($chkAssign as $device)
                                        <option value="{{ $device->id }}">
                                            {{ $device->name_device }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="truck_boxes_id" class="form-label">Asignación de Caja</label>
                                <select name="truck_boxes_id" id="truck_boxes_id" class="form-select">
                                    <option value="">
                                        Selecciona una caja
                                    </option>
                                    @foreach ($chkBoxAssign as $box)
                                        <option value="{{ $box->id }}">
                                            {{ $box->name_truck_box }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="example-select" class="form-select" required="required">
                                    <option value="0">
                                        Activo
                                    </option>
                                    <option value="1">
                                        Inactivo
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-info waves-effect waves-light">Agregar unidad</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Modal para Agregado de Unidades --}}
    <div id="modal-add-boxes" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">
                        Agregar nueva Caja

                        <img src="{{ asset('assets/images/marker-caja.png') }}" alt="Truck"
                            title="Vehiculo Asingado"
                            style="border: 0.5px solid #08ff00d4;border-radius: 2003px;padding: 5px;width: 50px;height: 50px;margin: 0 5px;cursor: pointer;">
                    </h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('truck_boxes.store') }}" method="POST" id="add_new_box"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name_truck_box" class="form-label">Nombre de la Caja</label>
                                <input type="text" name="name_truck_box" id="name_truck_box" class="form-control" required="required">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="id_truck_box" class="form-label">Identificador unico</label>
                                <input type="text" name="id_truck_box" id="id_truck_box" class="form-control" required="required">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="descript_truck_box" class="form-label">Descripción Corta</label>
                                <input type="tel" name="descript_truck_box" id="descript_truck_box" class="form-control" required="required">
                            </div>

                             <div class="col-md-6 mb-3">
                                <label class="form-label">Status</label>
                                <select name="status" id="example-select" class="form-select" required="required">
                                    <option value="0">
                                        Activo
                                    </option>
                                    <option value="1">
                                        Inactivo
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="gps_devices_id" class="form-label">Asignación de Dispositivo</label>
                                <select name="gps_devices_id" id="gps_devices_id" class="form-select"
                                    required="required">
                                    <option value="">
                                        Selecciona un dispositivo
                                    </option>
                                    @foreach ($chkAssign as $device)
                                        <option value="{{ $device->id }}">
                                            {{ $device->name_device }}
                                        </option>
                                    @endforeach
                                </select>
                            </div> 
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary waves-effect"
                            data-bs-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-info waves-effect waves-light">Agregar unidad</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/vendor.min.js') }}"></script>
    <script src="{{ asset('assets/js/app.js') }}"></script>
    <script src="{{ asset('assets/libs/tippy.js/tippy.all.min.js') }}"></script>
    <script src="{{ asset('assets/libs/sweetalert2/sweetalert2.all.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/dayjs.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/plugin/relativeTime.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/dayjs@1/locale/es.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    
    <script>
        var pusher = new Pusher('8442d369ae2137d24bf4', {
            cluster: 'us3'
        });
        var channel = pusher.subscribe('ruptela-server');
    </script>
    <script src="{{ asset('assets/js/styleMaps.js?v=' . time()) }}"></script>
    <script src="{{ asset('assets/js/trackings.js?v=' . time()) }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $ApiKey_google }}&libraries=places&callback=initMap">
    </script> 
</body>
</html>

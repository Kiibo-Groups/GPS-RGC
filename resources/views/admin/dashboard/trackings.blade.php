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
@section('css')
<script>
    let markerIcon = "{{ asset('assets/images/marker.png') }}";
</script>
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
    <script>
        var pusher = new Pusher('8442d369ae2137d24bf4', {
            cluster: 'us3'
        });
        var channel = pusher.subscribe('ruptela-server');
        
    </script>
    <script src="{{ asset('assets/js/trackings.js?v='.time()) }}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $ApiKey_google }}&libraries=places&callback=initMap"></script>
@endsection
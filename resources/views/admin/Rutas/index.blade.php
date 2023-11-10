
@extends('layouts.app')
@section('title') Gestor de Rutas @endsection
@section('page_active') Rutas @endsection 
@section('subpage_active') Listado @endsection 

@section('content') 
<div class="container-fluid">

    <div class="row">
                            
        <!-- sidebar -->
        <div class="col-lg-3">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">Control de rutas</h4>
                    <!--- Sidemenu -->
                    <div id="sidebar-menu">

                        <ul id="side-menu"> 
                            
                            <li style="border-bottom: 1px solid #e1e1e1;">
                                <a href="#">
                                    <i class="mdi mdi-account"></i>
                                    <span> JESUS FLORES ARUAS </span>
                                    <p> <i class="mdi mdi-google-maps" style="color:green;"></i>  
                                        Carretera Panamericana, San Isidro CHIH, 31313
                                        <br />
                                         <small>19 de Sep, de 2023 23:54</small>
                                    </p>
                                   
                                </a>
                            </li>

                            <li style="border-bottom: 1px solid #e1e1e1;">
                                <a href="#">
                                    <i class="mdi mdi-account"></i>
                                    <span> JESUS FLORES ARUAS </span>
                                    <p> <i class="mdi mdi-google-maps" style="color:green;"></i>  
                                        Carretera Panamericana, San Isidro CHIH, 31313
                                        <br />
                                        <small>19 de Sep, de 2023 23:54</small>
                                    </p>
                                    
                                </a>
                            </li>

                            <li style="border-bottom: 1px solid #e1e1e1;">
                                <a href="#">
                                    <i class="mdi mdi-account"></i>
                                    <span> JESUS FLORES ARUAS </span>
                                    <p> <i class="mdi mdi-google-maps" style="color:green;"></i>  
                                        Carretera Panamericana, San Isidro CHIH, 31313
                                        <br />
                                        <small>19 de Sep, de 2023 23:54</small>
                                    </p>
                                    
                                </a>
                            </li>

                            <li style="border-bottom: 1px solid #e1e1e1;">
                                <a href="#">
                                    <i class="mdi mdi-account"></i>
                                    <span> JESUS FLORES ARUAS </span>
                                    <p> <i class="mdi mdi-google-maps" style="color:green;"></i>  
                                        Carretera Panamericana, San Isidro CHIH, 31313
                                        <br />
                                        <small>19 de Sep, de 2023 23:54</small>
                                    </p>
                                    
                                </a>
                            </li>
                        </ul>

                    </div>
                    <!-- End Sidebar -->
                </div>
            </div> <!-- end card-->
        </div> 
        <!-- sidebar -->
        
        <div class="col-lg-9">
            <div class="card">
                <div class="card-body">
                    <h4 class="header-title mb-3">MAPA</h4>
                    <div id="gmaps-overlay" class="gmaps" style="position: relative; overflow: hidden;height: 500px !important;"></div>
                </div>
            </div> <!-- end card-->
        </div> <!-- end col-->

    </div> <!-- end row -->

    {{--
    <div class="row"> 
        

         <div class="col-lg-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <p class="text-muted font-14 mb-3" style="position: relative;height: 50px;">
                        <a href="{{ Asset($link . 'create') }}" type="button" class="btn btn-success waves-effect waves-light" style="float: right;">
                            <span class="btn-label"><i class="mdi mdi-check-all"></i></span>Agregar elemento
                        </a>
                    </p>

                    <table id="responsive-datatable" class="table dt-responsive nowrap">
                        <thead>
                            <tr>
                                <th>unidad</th> 
                                <th>origen</th>
                                <th>destino</th>
                                <th>operador</th> 
                                <th>remitente</th>
                                <th>destinatario</th>
                                <th>Número de Remisión</th>
                                <th>Número de Caja</th>
                                <th style="text-align: right">Opciones</th>
                            </tr> 
                        </thead>
                        <tbody>

                            @foreach ($data as $row)
                                <tr>
                                    <td>
                                        {{ $row->unidad }}    
                                    </td>
                                    <td>
                                        {{ $row->origen }}
                                    </td>
                                    <td>
                                        {{ $row->destino }}
                                    </td>
                                    <td>
                                        {{ $row->operador }}
                                    </td>
                                    <td>
                                        {{ $row->remitente }}
                                    </td>
                                    <td>
                                        {{ $row->destinatario }}
                                    </td>
                                    <td>
                                        {{ $row->remision_num }}
                                    </td>
                                    <td>
                                        {{ $row->caja_num }}
                                    </td> 
                                    <td width="17%" style="text-align: right">

                                        <a href="{{ Asset($link . $row->id . '/edit') }}"
                                            class="btn btn-success waves-effect waves-light btn m-b-15 ml-2 mr-2 btn-md"
                                            data-toggle="tooltip" data-placement="top"
                                            data-original-title="Editar"><i
                                                class="mdi mdi-border-color"></i></a>

                                        <button type="button"
                                            class="btn m-b-15 ml-2 mr-2 btn-md  btn btn-danger waves-effect waves-light"
                                            data-toggle="tooltip" data-placement="top"
                                            data-original-title="Eliminar"
                                            onclick="deleteConfirm('{{ Asset($link . 'delete/' . $row->id) }}')"><i
                                                class="mdi mdi-delete-forever"></i></button> 
                                    </td>
                                </tr>
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div> --}}
</div> 
@endsection

@section('js')
   <!-- google maps api -->
   <script src="https://maps.google.com/maps/api/js?key=AIzaSyCaYDpVSsJR8jI-xigBWtZ5oGr7HmWtsJE"></script>

   <!-- google map plugin -->
   <script src="{{ asset('assets/libs/gmaps/gmaps.min.js') }}"></script> 

   <!-- Init js-->
   <script>
    ! function(t) {
    "use strict";

    function e() {}
    e.prototype.createBasic = function(e) {
        return new GMaps({
            div: e,
            lat: 25.685924, 
            lng: -100.317094,
        })
    }, e.prototype.createMarkers = function(e) {
        var t = new GMaps({
            div: e,
            lat: 25.685924, 
            lng: -100.317094,
        });
        return t.addMarker({
            lat: 25.685924, 
            lng: -100.317094,
            title: "JESUS FLORES ARUAS",
            details: {
                database_id: 42,
                author: "HPNeo"
            },
            infoWindow: {
                content: '<a href="#">'
                            +'<i class="mdi mdi-account"></i>'
                            +'<span> JESUS FLORES ARUAS </span>'
                            +'<p> <i class="mdi mdi-google-maps" style="color:green;"></i>  '
                            +'  Carretera Panamericana, San Isidro CHIH, 31313'
                            +'<br />'
                            +'<small>19 de Sep, de 2023 23:54</small>'
                            +'</p>'
                        +'</a>'
            }
        }), t.addMarker({
            lat: 25.679598, 
            lng: -100.270704,
            title: "JESUS FLORES ARUAS",
            infoWindow: {
                content: '<a href="#">'
                            +'<i class="mdi mdi-account"></i>'
                            +'<span> JESUS FLORES ARUAS </span>'
                            +'<p> <i class="mdi mdi-google-maps" style="color:green;"></i>  '
                            +'  Carretera Panamericana, San Isidro CHIH, 31313'
                            +'<br />'
                            +'<small>19 de Sep, de 2023 23:54</small>'
                            +'</p>'
                        +'</a>'
            }
        }), t
    }, e.prototype.createWithOverlay = function(e) {
        var t = new GMaps({
            div: e,
            lat: 25.685924, 
            lng: -100.317094
        });
        return t.drawOverlay({
            lat: t.getCenter().lat(),
            lng: t.getCenter().lng(),
            content: 'Our Office!',
            verticalAlign: "top",
            horizontalAlign: "center"
        }), t
    }, e.prototype.createWithStreetview = function(e, t, l) {
        return GMaps.createPanorama({
            el: e,
            lat: t,
            lng: l
        })
    }, e.prototype.createMapByType = function(e, t, l) {
        var r = new GMaps({
            div: e,
            lat: t,
            lng: l,
            mapTypeControlOptions: {
                mapTypeIds: ["hybrid", "roadmap", "satellite", "terrain", "osm", "cloudmade"]
            }
        });
        return r.addMapType("osm", {
            getTileUrl: function(e, t) {
                return "http://tile.openstreetmap.org/" + t + "/" + e.x + "/" + e.y + ".png"
            },
            tileSize: new google.maps.Size(256, 256),
            name: "OpenStreetMap",
            maxZoom: 18
        }), r.addMapType("cloudmade", {
            getTileUrl: function(e, t) {
                return "http://b.tile.cloudmade.com/8ee2a50541944fb9bcedded5165f09d9/1/256/" + t + "/" + e.x + "/" + e.y + ".png"
            },
            tileSize: new google.maps.Size(256, 256),
            name: "CloudMade",
            maxZoom: 18
        }), r.setMapTypeId("osm"), r
    }, e.prototype.createMapByType = function(e, t, l) {
        var r = new GMaps({
            div: e,
            lat: t,
            lng: l,
            mapTypeControlOptions: {
                mapTypeIds: ["hybrid", "roadmap", "satellite", "terrain", "osm", "cloudmade"]
            }
        });
        return r.addMapType("osm", {
            getTileUrl: function(e, t) {
                return "http://tile.openstreetmap.org/" + t + "/" + e.x + "/" + e.y + ".png"
            },
            tileSize: new google.maps.Size(256, 256),
            name: "OpenStreetMap",
            maxZoom: 18
        }), r.addMapType("cloudmade", {
            getTileUrl: function(e, t) {
                return "http://b.tile.cloudmade.com/8ee2a50541944fb9bcedded5165f09d9/1/256/" + t + "/" + e.x + "/" + e.y + ".png"
            },
            tileSize: new google.maps.Size(256, 256),
            name: "CloudMade",
            maxZoom: 18
        }), r.setMapTypeId("osm"), r
    }, e.prototype.createWithStyle = function(e, t) {
        new GMaps({
            div: e,
            lat: 25.685924, 
            lng: -100.317094,
            styles: t
        })
    }, e.prototype.init = function() {
        var e = this;
        t(document).ready(function() {
            e.createMapByType("#gmaps-overlay", -12.043333, -77.028333), e.createMarkers("#gmaps-overlay")
        }), e.createWithStyle("#dark", [{
            featureType: "all",
            elementType: "labels",
            stylers: [{
                visibility: "on"
            }]
        }, {
            featureType: "all",
            elementType: "labels.text.fill",
            stylers: [{
                saturation: 36
            }, {
                color: "#000000"
            }, {
                lightness: 40
            }]
        }, {
            featureType: "all",
            elementType: "labels.text.stroke",
            stylers: [{
                visibility: "on"
            }, {
                color: "#000000"
            }, {
                lightness: 16
            }]
        }, {
            featureType: "all",
            elementType: "labels.icon",
            stylers: [{
                visibility: "off"
            }]
        }, {
            featureType: "administrative",
            elementType: "geometry.fill",
            stylers: [{
                color: "#000000"
            }, {
                lightness: 20
            }]
        }, {
            featureType: "administrative",
            elementType: "geometry.stroke",
            stylers: [{
                color: "#000000"
            }, {
                lightness: 17
            }, {
                weight: 1.2
            }]
        }, {
            featureType: "administrative.country",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#e5c163"
            }]
        }, {
            featureType: "administrative.locality",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#c4c4c4"
            }]
        }, {
            featureType: "administrative.neighborhood",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#e5c163"
            }]
        }, {
            featureType: "landscape",
            elementType: "geometry",
            stylers: [{
                color: "#000000"
            }, {
                lightness: 20
            }]
        }, {
            featureType: "poi",
            elementType: "geometry",
            stylers: [{
                color: "#000000"
            }, {
                lightness: 21
            }, {
                visibility: "on"
            }]
        }, {
            featureType: "poi.business",
            elementType: "geometry",
            stylers: [{
                visibility: "on"
            }]
        }, {
            featureType: "road.highway",
            elementType: "geometry.fill",
            stylers: [{
                color: "#e5c163"
            }, {
                lightness: "0"
            }]
        }, {
            featureType: "road.highway",
            elementType: "geometry.stroke",
            stylers: [{
                visibility: "off"
            }]
        }, {
            featureType: "road.highway",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#ffffff"
            }]
        }, {
            featureType: "road.highway",
            elementType: "labels.text.stroke",
            stylers: [{
                color: "#e5c163"
            }]
        }, {
            featureType: "road.arterial",
            elementType: "geometry",
            stylers: [{
                color: "#000000"
            }, {
                lightness: 18
            }]
        }, {
            featureType: "road.arterial",
            elementType: "geometry.fill",
            stylers: [{
                color: "#575757"
            }]
        }, {
            featureType: "road.arterial",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#ffffff"
            }]
        }, {
            featureType: "road.arterial",
            elementType: "labels.text.stroke",
            stylers: [{
                color: "#2c2c2c"
            }]
        }, {
            featureType: "road.local",
            elementType: "geometry",
            stylers: [{
                color: "#000000"
            }, {
                lightness: 16
            }]
        }, {
            featureType: "road.local",
            elementType: "labels.text.fill",
            stylers: [{
                color: "#999999"
            }]
        }, {
            featureType: "transit",
            elementType: "geometry",
            stylers: [{
                color: "#000000"
            }, {
                lightness: 19
            }]
        }, {
            featureType: "water",
            elementType: "geometry",
            stylers: [{
                color: "#000000"
            }, {
                lightness: 17
            }]
        }])
    }, t.GoogleMap = new e, t.GoogleMap.Constructor = e
    }(window.jQuery),
    function() {
        "use strict";
        window.jQuery.GoogleMap.init()
    }();
   </script>
@endsection
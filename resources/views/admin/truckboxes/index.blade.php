
@extends('layouts.app')
@section('title') Listado de Cajas @endsection
@section('page_active') Cajas @endsection 
@section('subpage_active') Listado @endsection 

@section('content') 
<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <p class="text-muted font-14 mb-3" style="position: relative;height: 50px;">
                        <a href="{{ Asset($link . 'create') }}" type="button" class="btn btn-success waves-effect waves-light" style="float: right;">
                            <span class="btn-label"><i class="mdi mdi-check-all"></i></span>Agregar elemento
                        </a>
                    </p>

                    <table id="responsive-datatable" class="table table-striped table-responsive">
                        <thead>
                            <tr>
                                <th>Nombre de la Caja</th> 
                                <th>Identificador único</th>
                                <th>Descripcion</th>
                                <th>Dispositivo Asignado</th> 
                                <th>Status</th>
                                <th style="text-align: right">Opciones</th>
                            </tr> 
                        </thead>
                        <tbody>

                            @foreach ($data as $row)
                                <tr>
                                    <td>
                                        {{ $row->name_truck_box }}    
                                    </td>
                                    <td>
                                        {{ $row->id_truck_box }}
                                    </td>
                                    <td>
                                       {{ $row->descript_truck_box }}
                                    </td>
                                    <td>
                                        {{ ($row->gps != null) ? $Models->GetNameGPS($row->gps) : "Sin Asignar" }}
                                     </td>
                                    <td>
                                        @if ($row->status == 0)
                                            <button type="button"
                                                class="btn btn-success width-xs waves-effect waves-light"
                                                onclick="confirmAlert('{{ Asset($link . 'status/' . $row->id) }}')">Activo</button>
                                        @else
                                            <button type="button"
                                                class="btn btn-danger width-xs waves-effect waves-light"
                                                onclick="confirmAlert('{{ Asset($link . 'status/' . $row->id) }}')">Inactivo</button>
                                        @endif

                                    </td>
                                    <td width="17%" style="text-align: right">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    Opciones <i class="mdi mdi-chevron-down"></i></button>
                                            <div class="dropdown-menu">
                                                <a href="{{ Asset($link . $row->id . '/edit') }}" class="dropdown-item">
                                                    Editar
                                                </a> 
                                                <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#assign-devices-{{ $row->id }}">
                                                    Asginar GPS
                                                </a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="javascript:void()" onclick="deleteConfirm('{{ Asset($link . 'delete/' . $row->id) }}')">
                                                    <i class="mdi mdi-delete-forever"></i> Eliminar
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                {{-- Asignaciones --}}
                                @include('admin.truckboxes.assign-devices')
                                {{-- Asignaciones --}}
                            @endforeach

                        </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
</div> 
@endsection

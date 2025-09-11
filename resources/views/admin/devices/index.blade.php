
@extends('layouts.app')
@section('title') Listado de Dispositivos @endsection
@section('page_active') Dispositivos @endsection 
@section('subpage_active') Listado @endsection 
@section('css')
    <!-- DataTables -->
    <link href="{{ Asset('assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Asset('assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{ Asset('assets/libs/datatables.net-buttons-bs5/css/buttons.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ Asset('assets/libs/datatables.net-select-bs5/css//select.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content') 
<div class="container-fluid">

    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body table-responsive">
                    <p class="text-muted font-14 mb-3" style="position: relative;height: 50px;">
                        <a href="{{ Asset($link . 'create') }}" type="button" class="btn btn-success waves-effect waves-light" style="float: right;">
                            <span class="btn-label"><i class="mdi mdi-check-all"></i></span>Agregar elemento
                        </a>
                    </p>

                    <table id="responsive-datatable" class="table dt-responsive nowrap table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Nombre del Dispositivo</th> 
                                <th>UUID/IMEI</th>
                                <th>Número para comandos</th>
                                <th>Descripcion</th>
                                <th class="text-center">Status</th>
                                <th style="text-align: right">Opciones</th>
                            </tr> 
                        </thead>
                        <tbody>
                            @foreach ($data as $row)
                                <tr>
                                    <td>
                                        {{ $row->name_device }}    
                                    </td>
                                    <td>
                                       <span class="badge bg-success" style="font-size: 12px"> {{ $row->uuid_device }}</span>
                                    </td>
                                    <td>
                                        @if($row->phone)
                                        <span class="badge bg-success" style="font-size: 12px"> +52{{ $row->phone }}</span>
                                        @else 
                                        <span class="badge bg-danger" style="font-size: 12px">Sin Asignar</span>
                                        @endif
                                    </td>
                                    <td>
                                       {{ $row->descript_device }}
                                    </td>
                                    
                                    <td class="text-center">
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
    </div>
</div> 
@endsection
@section('js')
    <!-- Required datatable js -->
    <script src="{{ Asset('assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ Asset('assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <!-- Responsive examples -->
    <script src="{{ Asset('assets/libs/datatables.net-responsive/js/dataTables.responsive.min.js') }}"></script>
    <script src="{{ Asset('assets/libs/datatables.net-responsive-bs5/js/responsive.bootstrap5.min.js') }}"></script>
    <script src="{{ Asset('assets/libs/libs/datatables.net-keytable/js/dataTables.keyTable.min.js') }}"></script> 
    <script>
        $(document).ready(function() {
            $('#responsive-datatable').DataTable({
                keys: false,
                searching: true,
                placeholder: "Buscar...",
            });
            $('#responsive-datatable').DataTable();
            $('.dataTables_length select').addClass('form-select form-select-sm');
        });
    </script>
@endsection
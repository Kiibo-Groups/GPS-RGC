@extends('layouts.app')

@section('title')
    Dashboard | Fletes RGC - Empresa Mexicana especializada en el autotransporte de carga terrestre.
@endsection

@section('page_active')
    Dashboard
@endsection
@section('subpage_active')
    Home
@endsection
@section('content')
    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">

            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span class="float-start mt-3">Total de Rutas </span>
                                <h4 class="fw-normal mb-1"> 84 </h2>
                                    <p class="text-muted mb-3">Rutas de hoy</p>
                            </div>
                            <div class="progress progress-bar-alt-info progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="77" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 77%;">
                                    <span class="visually-hidden">77% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span class="float-start mt-3">Total de Cargas </span>
                                <h4 class="fw-normal mb-1"> 150 </h2>
                                    <p class="text-muted mb-3">Cargas de hoy</p>
                            </div>
                            <div class="progress progress-bar-alt-info progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="45" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 45%;">
                                    <span class="visually-hidden">45% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->


            <div class="col-xl-4 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="widget-box-2">
                            <div class="widget-detail-2 text-end">
                                <span class="float-start mt-3">Total de Tickets </span>
                                <h4 class="fw-normal mb-1"> 2 </h2>
                                    <p class="text-muted mb-3">Tickets de hoy</p>
                            </div>
                            <div class="progress progress-bar-alt-info progress-sm">
                                <div class="progress-bar bg-info" role="progressbar" aria-valuenow="5" aria-valuemin="0"
                                    aria-valuemax="100" style="width: 5%;">
                                    <span class="visually-hidden">5% Complete</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- end col -->


        </div>
        <!-- end row -->

        <div class="row">
            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title mt-0">Grafica de Viajes</h4>
                        <div id="morris-bar-example" dir="ltr" style="height: 280px;" class="morris-chart"></div>
                    </div>
                </div>
            </div><!-- end col -->

            <div class="col-xl-6">
                <div class="card">
                    <div class="card-body">

                        <h4 class="header-title mt-0 mb-3">Ultimos viajes realizados</h4>

                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Unidad</th>
                                        <th>Origen</th>
                                        <th>Destino</th>
                                        <th>Operador</th>
                                        <th>Destinatario</th>
                                        <th>Fecha</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>1</td>
                                        <td>U/55</td>
                                        <td>Monterrey NL</td>
                                        <td>Merida</td>
                                        <td>ICECOOL</td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td>01/01/2017</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>U/55</td>
                                        <td>Monterrey NL</td>
                                        <td>Merida</td>
                                        <td>ICECOOL</td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td>01/01/2017</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>U/55</td>
                                        <td>Monterrey NL</td>
                                        <td>Merida</td>
                                        <td>ICECOOL</td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td>01/01/2017</td>
                                    </tr>
                                    <tr>
                                        <td>1</td>
                                        <td>U/55</td>
                                        <td>Monterrey NL</td>
                                        <td>Merida</td>
                                        <td>ICECOOL</td>
                                        <td><span class="badge bg-danger">Admin</span></td>
                                        <td>01/01/2017</td>
                                    </tr>


                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div><!-- end col -->
        </div>
        <!-- end row -->
    </div>
@endsection

@section('js')
    <!--Morris Chart-->
    <script src="{{ asset('assets/libs/morris.js06/morris.min.js') }}"></script>
    <!-- Dashboar init js-->
    <script src="{{ asset('assets/js/pages/dashboard.init.js') }}"></script>
@endsection

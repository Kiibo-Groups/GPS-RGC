@extends('layouts.app')
@section('title') Página no encontrada @endsection 
@section('page_active') 404 @endsection 
@section('subpage_active') Error @endsection 

@section('content')

<div class="account-pages mt-5 mb-5">
  <div class="container">
      <div class="row justify-content-center">
          <div class="col-md-8 col-lg-6 col-xl-5">
              <div class="text-center">
                  <a href="index.html" class="logo">
                      <img src="{{ asset('resources/images/logo-dark.png') }}" alt="" height="22" class="logo-light mx-auto">
                  </a>
              </div>
              <div class="card">

                  <div class="card-body p-4">

                      <div class="text-center">
                          <h1 class="text-error">404</h1>
                          <h3 class="mt-3 mb-2">Página no encontrada</h3>
                          <p class="text-muted mb-3">Parece que has tomado un camino equivocado. No te preocupes... sucede
                            lo mejor de nosotros. Es posible que desees comprobar tu conexión a Internet. He aquí un pequeño consejo que podría
                            ayudarle a volver a la normalidad.</p>

                          <a href="{{ route('dash') }}" class="btn btn-danger waves-effect waves-light"><i class="fas fa-home mr-1"></i> Back to Home</a>
                      </div>


                  </div> <!-- end card-body -->
              </div>
              <!-- end card -->

          </div> <!-- end col -->
      </div>
      <!-- end row -->
  </div>
  <!-- end container -->
</div>
<!-- end page -->

@endsection

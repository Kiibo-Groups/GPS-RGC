@extends('layouts.app')
@section('title') Fletes RGC, S.A DE C.V. | Ingresa a tu panel de control  @endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-4">
        <div class="card">
            <div class="card-body p-4" style="box-shadow: 0 4px 24px 0 rgba(34,41,47,.1);">
                
                <div class="text-center mb-4">
                    <h4 class="text-uppercase mt-0">Cuenta Administrativa</h4>
                    <p class="text-muted mt-2 mb-4">Ingresa tus datos para iniciar sesión</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email">Correo electronico</label>

                        <div class="col-md-12">
                            <input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                            @error('email')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="password">Contraseña</label>

                        <div class="col-md-12">
                            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                            @error('password')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="checkbox-signin" checked>
                            <label class="form-check-label" for="checkbox-signin">Recordar acceso</label>
                        </div>
                    </div>

                    <div class="mb-3 d-grid text-center">
                        <button class="btn btn-primary" type="submit"> Iniciar sesión</button>
                    </div>
                </form>

            </div> <!-- end card-body -->
        </div>
    </div>
</div>
@endsection
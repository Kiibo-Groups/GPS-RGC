@extends('layouts.app')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8 col-lg-6 col-xl-4">
        <div class="text-center">   
            <a href="index.html">
                <img src="../assets/images/logo-dark.png" alt="" height="22" class="mx-auto">
            </a>
            <p class="text-muted mt-2 mb-4">Panel de control para GPS</p>

        </div>
        <div class="card">
            <div class="card-body p-4">
                
                <div class="text-center mb-4">
                    <h4 class="text-uppercase mt-0">Ingresa a tu cuenta</h4>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="email">{{ __('Email Address') }}</label>

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
                        <label for="password">{{ __('Password') }}</label>

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
                            <label class="form-check-label" for="checkbox-signin">Remember me</label>
                        </div>
                    </div>

                    <div class="mb-3 d-grid text-center">
                        <button class="btn btn-primary" type="submit"> Log In </button>
                    </div>
                </form>

            </div> <!-- end card-body -->
        </div>
        <!-- end card -->

        <div class="row mt-3">
            <div class="col-12 text-center">
                <p> <a href="pages-recoverpw.html" class="text-muted ms-1"><i class="fa fa-lock me-1"></i>Forgot your password?</a></p>
                <p class="text-muted">Don't have an account? <a href="pages-register.html" class="text-dark ms-1"><b>Sign Up</b></a></p>
            </div> <!-- end col -->
        </div>
        <!-- end row -->

    </div> <!-- end col -->
</div>
@endsection

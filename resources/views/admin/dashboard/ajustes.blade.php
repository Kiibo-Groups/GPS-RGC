@extends('layouts.app')
@section('title') Configuraciones @endsection 
@section('page_active') Dashboard @endsection 
@section('subpage_active') Ajustes @endsection 
 
@section('content')
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-11">
            
            <form action="{{ $form_url }}" method="post" enctype="multipart/form-data">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                 
                <h4>Google ApiKey <br /><small style="font-size: 12px">(Introduce el ApiKey de tu cuenta en <a href="https://cloud.google.com/" target="_blank">https://cloud.google.com/</a> )</small></h4>
                <div class="card py-3 m-b-30" style="margin-bottom: 50px">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="ApiKey_google">ApiKey</label>
                                <input type="text" class="form-control" id="ApiKey_google" name="ApiKey_google" value="{{ $data->ApiKey_google }}">
                            </div>

                        </div>
                    </div>
                </div>
  
                <h4>Cliente Stripe <br /><small style="font-size: 12px">(Deja vacío si quieres deshabilitar Stripe)</small></h4>
                <div class="card py-3" style="margin-bottom: 50px">
                    <div class="card-body">
                        <div class="form-row">
                            <div class="form-group col-md-12">
                                <label for="asd">Stripe Publish Key</label>
                                <input type="text" class="form-control" id="asd" name="stripe_client_id" value="{{ $data->stripe_client_id }}">
                            </div>

                            <div class="form-group col-md-12 mt-3">
                                <label for="asd">Stripe API Key</label>
                                <input type="text" class="form-control" id="asd" name="stripe_api_id" value="{{ $data->stripe_api_id }}">
                            </div>
                        </div>
                    </div>
                </div> 
                <div class="d-flex justify-content-end mt-5">
                    <button type="submit" class="btn btn-primary mb-2 btn-pill">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
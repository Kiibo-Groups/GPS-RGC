@extends('layouts.app')
@section('title') Gestion de SubCuentas @endsection
@section('page_active') SubCuentas @endsection 
@section('subpage_active') Agregar Elemento @endsection 

@section('content')
    {!! Form::model($data, ['url' => [ $form_url ],'files' => true]) !!} 
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
        @include('admin.subAccount.form')
    </form>
@endsection
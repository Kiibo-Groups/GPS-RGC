
@extends('layouts.app')
@section('title') Mensaje de entrada @endsection
@section('page_active') Mensajes Entrantes @endsection 
@section('subpage_active') Listado @endsection 

@section('css')
    <!-- quill css -->
    <link href="{{ asset('assets/libs/quill/quill.core.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/quill/quill.snow.css') }}" rel="stylesheet" type="text/css" />
    
    <link href="{{ asset('assets/libs/mohithg-switchery/switchery.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/multiselect/css/multi-select.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/select2/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/libs/selectize/css/selectize.bootstrap3.css') }}" rel="stylesheet" type="text/css" />

@endsection

@section('content') 
<!-- Start Content-->
<div class="container-fluid">
    <div class="row">
        <div class="col-sm-12">
            <div class="inbox-app-main">
                <div class="row">
                    <div class="col-md-2">
                        <aside id="sidebar">
                            <div class="h-100" data-simplebar>

                                <div class="text-center">
                                    <a id="email-left-content" data-bs-toggle='modal' 
                                    data-bs-target="#modal-new-msg" class="btn btn-danger rounded-pill width-lg waves-effect waves-light mb-2 mt-3" 
                                    data-animation="fadein" data-plugin="custommodal" data-overlayColor="#36404a">Nuevo Mensaje</a>
                                </div>
                                
                                <menu class="menu-segment">
                                    <ul class="list-unstyled">
                                        <li class="active"><a href="javascript:void(0);">Bandeja <span> ({{$bandeja}})</span></a></li> 
                                        {{-- <li><a href="javascript:void(0);">Enviados <span> ({{$sends}})</span></a></li> 
                                        @if (Auth::user()->role == 1)
                                        <li><a href="javascript:void(0);">Eliminados <span> ({{$dels}})</span></a></li>
                                        @endif --}}
                                    </ul>
                                </menu>

                                <div class="separator"></div>
                                <div class="bottom-padding"></div>
                            </div>
                        </aside>
                    </div> <!-- end col -->

                    <div class="col-md-10">
                        <main id="main">
                            <div class="overlay"></div>
                            <header class="header">

                                <h1 class="page-title">
                                    <a class="sidebar-toggle-btn trigger-toggle-sidebar">
                                        <span class="line"></span>
                                        <span class="line"></span>
                                        <span class="line"></span>
                                        <span class="line line-angle1"></span>
                                        <span class="line line-angle2"></span>
                                    </a>
                                </h1>
                                <div class="action-bar float-start">
                                    <ul class="list-inline mb-0">
                                        <li class="list-inline-item">
                                            <a href="{{ route('chats_inbox') }}" class="icon circle-icon"><i class="mdi mdi-refresh text-muted"></i></a>
                                        </li>
                                         
                                        <li class="list-inline-item">
                                            <a class="icon circle-icon red"><i class="mdi mdi-close text-danger"></i></a>
                                        </li>
                                         
                                    </ul>
                                </div>
                                <div class="search-box float-end">
                                    <input placeholder="Search..."><span
                                        class="icon fa fa-search"></span>
                                </div>

                                <div class="clearfix"></div>

                            </header>

                            <div id="main-nano-wrapper" class="nano">
                                <div class="nano-content h-100" data-simplebar>
                                    <ul class="message-list">
                                        @if (count($data) > 0)
                                            @foreach ($data as $box)
                                            <li @if($box->ready == 0) class="unread" @endif>
                                                <div class="mail-col mail-col-1"><span class="dot"></span>
                                                    <div class="checkbox-wrapper-mail">
                                                        <input type="checkbox" id="chk-{{ $box->id }}">
                                                        <label for="chk-{{ $box->id }}" class="toggle chk-del-inbox"></label>
                                                    </div>
                                                    <a href="{{ url('chats_inbox/view/'.$box->id) }}"  style="@if($box->ready == 1) color: #8e98a2 !important @else color: #000 !important @endif;text-decoration: none;"  >
                                                        <p class="title">{{$box->subject}}</p>
                                                        <span class="star-toggle far fa-star"></span>
                                                    </a>
                                                </div>
                                                <div class="mail-col mail-col-2">
                                                    <div class="subject">
                                                        <a href="{{ url('chats_inbox/view/'.$box->id) }}"   style="@if($box->ready == 1) color: #8e98a2 !important @else color: #000 !important @endif;text-decoration: none;" >
                                                            <span style="display: flex;">
                                                                {!! substr($box->message,0,120) !!}
                                                                @if (str($box->message)->length() > 120)
                                                                    ...
                                                                @endif
                                                            </span>
                                                        </a>
                                                    </div>
                                                    <div class="date">{{ $box->created_at->isoFormat('H:mm A') }}</div>
                                                </div>
                                            </li>
                                            @endforeach
                                        @endif 
                                    </ul> 
                                </div>
                            </div>
                        </main>
                    </div> <!-- end col -->
                </div><!-- end row -->
            </div>

        </div>
    </div>
</div>
<!-- End content -->
 
<!-- Modal -->
<div class="modal fade" id="modal-new-msg" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content modal-lg">
            <div class="modal-header bg-light">
                <h4 class="modal-title" id="myCenterModalLabel">Agregar Email</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
            </div>
           
            <div class="modal-body">
                {!! Form::model($data, ['url' => [ $form_url ],'files' => true,'id' => 'form-new-msg']) !!} 
                    <div class="mb-3">
                        <select name="user_id" id="user_id" class="form-control">
                            @foreach ($list_users as $us)
                            <option value="{{ $us->id }}">{{ $us->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <input type="text" class="form-control" name="subject" placeholder="Subject" required>
                    </div>
                    <div class="mb-3 card border-0">
                        <div id="snow-editor" style="height: 180px;">
                            
                        </div> <!-- end Snow-editor-->
                    </div>

                    <div class="btn-toolbar">
                        <div class="float-end">
                            <button class="btn btn-purple waves-effect waves-light">
                                <span>Enviar</span> <i class="fas fa-paper-plane ms-1"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
    <!-- Quill js -->
    <script src="{{ asset('assets/libs/quill/quill.min.js') }}"></script>
    <script src="{{ asset('assets/libs/selectize/js/standalone/selectize.min.js') }}"></script>

    {{-- Inbox --}}
    <script src="{{ asset('assets/js/pages/inbox.js') }}"></script>
    <script>
    jQuery(document).ready(function(i) {
        
        $("#form-new-msg").on("submit",function(e) {
            e.preventDefault();

            let action = $(this).attr('action');
            let user_id = $("select[name='user_id']").val();
            let subject = $("input[name='subject']").val();

            postData(action, { 
                'user_id' : user_id,
                'subject' : subject,
                'message' : $("#snow-editor .ql-editor").html(),
             }).then((data) => {
                $('#modal-new-msg').modal('toggle');
                if (data.status || data.code == 200) {
                    Swal.fire({
                        position: "top-end",
                        icon: "success",
                        title: "Mensaje enviado con éxito.",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                }else {
                    Swal.fire({
                        position: "top-end",
                        icon: "danger",
                        title: 'Algo ha pasado',
                        text: "El mensaje no pudo enviarse, Consulta con Administración.",
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        window.location.reload();
                    });
                }
            });
        });

        async function postData(url = "", data = {}) {
            const token = document.head.querySelector("[name~=csrf-token][content]").content;
 
            // Default options are marked with *
            const response = await fetch(url, {
                method: "POST", // *GET, POST, PUT, DELETE, etc.
                mode: "cors", // no-cors, *cors, same-origin
                cache: "no-cache", // *default, no-cache, reload, force-cache, only-if-cached
                credentials: "same-origin", // include, *same-origin, omit
                headers: {
                "Content-Type": "application/json",
                "X-CSRF-Token": token
                },
                redirect: "follow", // manual, *follow, error
                referrerPolicy: "no-referrer", // no-referrer, *no-referrer-when-downgrade, origin, origin-when-cross-origin, same-origin, strict-origin, strict-origin-when-cross-origin, unsafe-url
                body: JSON.stringify(data), // body data type must match "Content-Type" header
            });
            return response.json(); // parses JSON response into native JavaScript objects
        }       
    });
    </script>
@endsection
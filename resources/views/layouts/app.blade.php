<!DOCTYPE html>
<html lang="es">
<head>

    <meta charset="utf-8" />
    <title>Dashboard | Adminto - Responsive Admin Dashboard Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="A fully featured admin theme which can be used to build CRM, CMS, etc." name="description" />
    <meta content="Coderthemes" name="author" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- App favicon -->
    <link rel="shortcut icon" href="../public/assets/images/favicon.ico">

    <!-- App css -->
    <link href="{{ asset('public/assets/css/config/default/bootstrap.min.css') }}" rel="stylesheet" type="text/css" id="bs-default-stylesheet" />
    <link href="{{ asset('public/assets/css/config/default/app.min.css') }}" rel="stylesheet" type="text/css" id="app-default-stylesheet" />

    <link href="{{ asset('public/assets/css/config/default/bootstrap-dark.min.css') }}" rel="stylesheet" type="text/css" id="bs-dark-stylesheet" disabled="disabled" />
    <link href="{{ asset('public/assets/css/config/default/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="app-dark-stylesheet" disabled="disabled" />

    <!-- icons -->
    <link href="{{ asset('public/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />

    <link href="{{ asset('public/assets/css/main.css') }}" rel="stylesheet" type="text/css" />

</head>
<body class="loading" data-layout='{"mode": "light", "width": "fluid", "menuPosition": "fixed", "sidebar": { "color": "dark", "size": "default", "showuser": true}, "topbar": {"color": "light"}, "showRightSidebarOnPageLoad": true}'>

    <!-- Begin page -->
    <div id="wrapper">

    @if (!Route::is('login'))
    <!-- ========== Left Topbar Start ========== -->
    @include('layouts.nav')
    <!-- end Topbar -->

    <!-- ========== Left Sidebar Start ========== -->
    @include('layouts.sidebar')
    <!-- Left Sidebar End -->
    @endif

    <!-- ============================================================== -->
    <!-- Start Page Content here -->
    <!-- ============================================================== -->

    <div class="@if (!Route::is('login')) content-page @else account-pages my-5 @endif">
        <div class="@if (!Route::is('login')) content @else container @endif">
            <main class="py-4">
                @yield('content')
            </main>
        </div>

        <!-- Footer Start -->
        <footer class="footer">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <script>document.write(new Date().getFullYear())</script> &copy; Adminto theme by <a href="">Coderthemes</a> 
                    </div>
                    <div class="col-md-6">
                        <div class="text-md-end footer-links d-none d-sm-block">
                            <a href="javascript:void(0);">About Us</a>
                            <a href="javascript:void(0);">Help</a>
                            <a href="javascript:void(0);">Contact Us</a>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>


     <!-- Right bar overlay-->
     <div class="rightbar-overlay"></div>

     <!-- Vendor js -->
     <script src="{{ asset('public/assets/js/vendor.min.j') }}s"></script>
 
     <!-- knob plugin -->
     <script src="{{ asset('public/assets/libs/jquery-knob/jquery.knob.min.js') }}"></script>
 
     <!--Morris Chart-->
     <script src="{{ asset('public/assets/libs/morris.js06/morris.min.js') }}"></script>
     <script src="{{ asset('public/assets/libs/raphael/raphael.min.js') }}"></script>
 
     <!-- Dashboar init js-->
     <script src="{{ asset('public/assets/js/pages/dashboard.init.js') }}"></script>
 
     <!-- App js-->
     <script src="{{ asset('public/assets/js/app.min.js') }}"></script>
</body>
</html>

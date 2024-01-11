<div class="navbar-custom">
    <ul class="list-unstyled topnav-menu float-end mb-0">
        <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <i class="fe-bell noti-icon"></i>
                {{-- <span class="badge bg-danger rounded-circle noti-icon-badge">9</span> --}}
            </a>

            <div class="dropdown-menu dropdown-menu-end dropdown-lg">
                <!-- item -->
                <div class="dropdown-item noti-title">
                    <h5 class="m-0">
                        <span class="float-end">
                            <a href="" class="text-dark">
                                <small>Limpiar todo</small>
                            </a>
                        </span>Notificaciones
                    </h5>
                </div>

                <div class="noti-scroll" data-simplebar>
                    <!-- item-->
                    <a href="javascript:void(0);" class="dropdown-item notify-item">
                        <div class="notify-icon bg-primary">
                            <i class="mdi mdi-comment-account-outline"></i>
                        </div>
                        <p class="notify-details">Nueva ruta creada
                            <small class="text-muted">1 min ago</small>
                        </p>
                    </a> 
                </div>

                <!-- All
                <a href="javascript:void(0);" class="dropdown-item text-center text-primary notify-item notify-all">
                    View all
                    <i class="fe-arrow-right"></i>
                </a>-->
            </div>
        </li>

        <li class="dropdown notification-list topbar-dropdown">
            <a class="nav-link dropdown-toggle nav-user me-0 waves-effect waves-light" data-bs-toggle="dropdown" href="#" role="button" aria-haspopup="false" aria-expanded="false">
                <img src="{{ asset('assets/images/users/'.Auth::guard()->user()->logo) }}" alt="user-image" class="rounded-circle">
                <span class="pro-user-name ms-1">
                    {{ Auth::guard()->user()->name  }} <i class="mdi mdi-chevron-down"></i> 
                </span>
            </a>
            <div class="dropdown-menu dropdown-menu-end profile-dropdown ">
                <!-- item-->
                <div class="dropdown-header noti-title">
                    <h6 class="text-overflow m-0">Bienvenido(a) !</h6>
                </div>

                <!-- item-->
                <a href="{{ url('dash') }}" class="dropdown-item notify-item">
                    <i class="fe-user"></i>
                    <span>Mi Cuenta</span>
                </a>
                <a href="{{ url('subaccounts') }}" class="dropdown-item notify-item">
                    <i class="mdi mdi-account"></i>
                    <span>SubCuentas</span>
                </a>
                <a href="{{ url('rutas') }}" class="dropdown-item notify-item">
                    <i class="mdi mdi-ticket"></i>
                    <span>Gestor de Rutas</span>
                </a>
                <a href="{{ url('cargas') }}" class="dropdown-item notify-item">
                    <i class="mdi mdi-ticket"></i>
                    <span>Gestor de Cargas</span>
                </a>
                <a href="{{ url('ticket') }}" class="dropdown-item notify-item">
                    <i class="mdi mdi-headset"></i> 
                    <span>Tickets de Soporte</span>
                </a>
                <a href="{{ url('dispositivos') }}" class="dropdown-item notify-item">
                    <i class="mdi mdi-car"></i>
                    <span>Dispositivos</span>
                </a>

                <div class="dropdown-divider"></div>

                <!-- item-->
                <a href="{{ url('/logout') }}" class="dropdown-item notify-item">
                    <i class="fe-log-out"></i>
                    <span>Cerrar sessión</span>
                </a> 
            </div>
        </li>
    </ul>

    <!-- LOGO -->
    <div class="logo-box">
        <a href="{{ url('./') }}" class="logo logo-light text-center">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/users/'.Auth::guard()->user()->logo_top_sm) }}" alt="" height="60">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/users/'.Auth::guard()->user()->logo_top) }}" alt="">
            </span>
        </a>
        <a href="{{ url('./') }}" class="logo logo-dark text-center">
            <span class="logo-sm">
                <img src="{{ asset('assets/images/users/'.Auth::guard()->user()->logo_top_sm) }}" alt="" height="22">
            </span>
            <span class="logo-lg">
                <img src="{{ asset('assets/images/users/'.Auth::guard()->user()->logo_top) }}" alt="" height="16">
            </span>
        </a>
    </div>

    <ul class="list-unstyled topnav-menu topnav-menu-left mb-0">
        <li>
            <button class="button-menu-mobile disable-btn waves-effect">
                <i class="fe-menu"></i>
            </button>
        </li>

        <li>
            <h4 class="page-title-main"> @yield('page_active') </h4>
        </li>
        
    </ul>

    <div class="clearfix"></div> 
</div>
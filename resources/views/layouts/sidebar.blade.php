
<div class="left-side-menu">

    <div class="h-100" data-simplebar>
 

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title">Principal</li>
                
                <li>
                    <a href="{{ route('dash') }}">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span>&nbsp;Dashboard </span>
                    </a>
                </li> 
                <li>
                    <a href="{{ url('account') }}">
                        <i class="dripicons-user-id"></i>
                        <span>&nbsp;Mi Cuenta </span>
                    </a>
                </li> 

                
                <li>
                    <hr style="margin:0 !important" />                    
                </li>

                <li>
                    <a href="{{ url('/trackings') }}">
                        <i class="mdi mdi-crosshairs-gps"></i>
                        <span>&nbsp;Seguimientos</span>
                    </a>
                </li> 

                <li class="menu-title mt-2">Navegación</li>
                
                <li>
                    <a href="{{ url('/subaccounts') }}">
                        <i class="mdi mdi-card-account-details"></i>
                        <span>&nbsp;SubCuentas</span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/mirror_beads') }}">
                        <i class="mdi mdi-mirror"></i>
                        <span>&nbsp;Cuentas de Espejo </span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/rutas') }}">
                        <i class="mdi mdi-ticket"></i>
                        <span>&nbsp;Gestor de Rutas </span>
                        <span class="menu-arrow"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/vehicle_units') }}">
                        <i class="mdi mdi-truck-fast-outline"></i>
                        <span>&nbsp;Gestor de unidades</span>
                        <span class="menu-arrow"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/truck_boxes') }}">
                        <i class="mdi mdi-truck-trailer"></i>
                        <span>&nbsp;Gestor de cajas</span>
                        <span class="menu-arrow"></span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/dispositivos') }}">
                        <i class="mdi mdi-crosshairs-gps"></i>
                        <span>&nbsp;Gesto de dispositivos </span>
                        <span class="menu-arrow"></span>
                    </a>
                </li>

                <li class="menu-title mt-2">Extras</li>

                <li>
                    <a href="{{ url('/chats_inbox') }}" >
                        <i class="mdi mdi-headset"></i> 
                        <span>&nbsp;Tickets de soporte </span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/ajustes') }}">
                        <i class="mdi mdi-cog"></i>
                        <span>&nbsp;Ajustes </span>
                    </a>
                </li>
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

        <a href="{{ url('./') }}" class="tag_top shadow-drop-2-center" @if(Route::is('trackings')) style="left: -125px;" @endif>
            <span class="logo-sm">
                <img src="{{ asset('assets/images/users/'.Auth::user()->logo_top_sm) }}" alt="" height="60">
            </span> 

            <img src="{{ asset('assets/images/users/user-1.jpg') }}" alt="user-img" title="Mat Helme" class="rounded-circle img-thumbnail avatar-md" style="height: 3rem;width: 3rem;margin-top: 5px;">
            <span style="width: 15px;height: 15px;background: green;border-radius: 2003px;position: absolute;right: 16px;top: 5px;"></span>
        </a>
    </div>
    <!-- Sidebar -left -->

</div>
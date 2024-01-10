
<div class="left-side-menu">

    <div class="h-100" data-simplebar>

        <!--- Sidemenu -->
        <div id="sidebar-menu">

            <ul id="side-menu">

                <li class="menu-title">Menu Principal</li>
                
                <li>
                    <a href="{{ route('dash') }}">
                        <i class="mdi mdi-view-dashboard"></i>
                        <span> Inicio </span>
                    </a>
                </li>

                <li>
                    <a href="{{ url('/subaccounts') }}">
                        <i class="mdi mdi-account"></i>
                        <span>Cuentas de Usuario </span>
                    </a>
                </li>

                <li>
                    <a href="#rutas" data-bs-toggle="collapse">
                        <i class="mdi mdi-ticket"></i>
                        <span> Gestor de Rutas </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="rutas">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ url('/rutas') }}">Control de rutas</a>
                            </li> 
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="#cargas" data-bs-toggle="collapse">
                        <i class="mdi mdi-ticket"></i>
                        <span> Gestor de Cargas </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="cargas">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ url('/cargas') }}">Vista de Cargas</a>
                            </li>
                            <li>
                                <a href="{{ url('/history_cargas') }}">Historial</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="{{ url('/ticket') }}" >
                        <i class="mdi mdi-headset"></i> 
                        <span> Tickets de soporte </span>
                    </a>
                </li>

                <li>
                    <a href="#dispositivos" data-bs-toggle="collapse">
                        <i class="mdi mdi-car"></i>
                        <span> Dispositivos </span>
                        <span class="menu-arrow"></span>
                    </a>
                    <div class="collapse" id="dispositivos">
                        <ul class="nav-second-level">
                            <li>
                                <a href="{{ url('/dispositivos') }}">
                                    Dispositivos
                                </a>
                            </li>
                            <li>
                                <a href="{{ url('conexiones') }}">
                                    Conexiones
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="{{ url('/ajustes') }}">
                        <i class="mdi mdi-cog"></i>
                        <span> Ajustes </span>
                    </a>
                </li>
            </ul>

        </div>
        <!-- End Sidebar -->

        <div class="clearfix"></div>

    </div>
    <!-- Sidebar -left -->

</div>
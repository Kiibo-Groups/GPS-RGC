
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
                    <a href="{{ route('conexiones') }}">
                        <i class="mdi mdi-antenna"></i>
                        <span> Conexiones </span>
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
                                <a href="{{ asset('/dispositivos') }}">Dispositivos</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <li>
                    <a href="{{ asset('/usuarios') }}">
                        <i class="mdi mdi-account"></i>
                        <span> Usuarios </span>
                    </a>
                </li>

                <li>
                    <a href="{{ asset('/servicios') }}">
                        <i class="mdi mdi-ticket"></i>
                        <span> Servicios </span>
                    </a>
                </li>

                <li>
                    <a href="{{ asset('/ticket') }}" >
                        <i class="mdi mdi-headset"></i>
                        <span class="badge bg-success float-end">Nuevo</span>
                        <span> Ticket de soporte </span>
                    </a>
                </li>

                <li>
                    <a href="{{ asset('/ajustes') }}">
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
<nav id="mainnav-container" class="mainnav">
    <div class="mainnav__inner">

        <!-- Navigation menu -->
        <div class="mainnav__top-content scrollable-content pb-5">

            <!-- Profile Widget -->
            <div class="mainnav__profile mt-3">

                <!-- Profile picture  -->
                <div class="mininav-toggle text-center py-2">
                    @if (isset(Auth::user()->img_usuario ) && Auth::user()->img_usuario!='')
                        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.Auth::user()->img_usuario) }}" id="main_user_image" class="img-md rounded-circle">
                    @else
                        {!! icono_nombre(Auth::user()->name,40,16) !!}
                    @endif
                </div>
                
                <div class="mininav-content collapse d-mn-max">
                    <div class="d-grid">
        
                        <!-- User name and position -->
                        <button class="d-block btn shadow-none p-2" data-bs-toggle="collapse" data-bs-target="#usernav" aria-expanded="false" aria-controls="usernav">
                            <span class="dropdown-toggle d-flex justify-content-center align-items-center">
                                <h6 class="mb-0 me-2">{{ Auth::user()->name }}</h6>
                            </span>
                            <small class="text-muted">{{Auth::user()->email}}</small>
                        </button>
                       
                        <!-- Collapsed user menu -->
                        <div id="usernav" class="nav flex-column collapse">
                            <a href="#" class="nav-link d-flex justify-content-between align-items-center">
                                <span><i class="fa-light fa-bell fs-5 me-3"></i> Notificaciones</span>
                                <span class="badge bg-danger rounded-pill cuenta_notificaciones">0</span>
                            </a>
                            <a href="{{ url('/miperfil/'.Auth::user()->id) }}" class="nav-link">
                                <i class="demo-pli-male fs-5 me-3"></i> Perfil
                            </a>
                            <a href="{{ url('/user_settings/'.Auth::user()->id) }}" class="nav-link " data-bs-toggle="offcanvas" data-bs-target="#_dm-settingsContainer" aria-controls="_dm-settingsContainer">
                                <i class="demo-pli-gear fs-5 me-3"></i> Ajustes
                            </a>
                            <a href="{{ url('/lockscreen') }}" class="nav-link">
                                <i class="demo-pli-computer-secure fs-5 me-3"></i> Bloquear pantalla
                            </a>
                            <a href="{{url('/logout')}}" class="nav-link">
                                <i class="demo-pli-unlock fs-5 me-3"></i> Logout
                            </a>
                            <div class='onesignal-customlink-container'></div>
                            <div>
                                
                                @admin
                                    @include('resources.combo_clientes')
                                @endadmin
                            </div>
                        </div>
        
                    </div>
                </div>
            </div>
            <!-- End - Profile widget -->

            <h6 class="mainnav__caption mt-5 px-3 fw-bold">Mi espacio</h6>
            <ul class="mainnav__menu nav flex-column">

                <li class="nav-item">
                    <a href="{{ url('/') }}" class="nav-link mininav-toggle collapsed"><i class="fa-light fa-house fs-5 me-2"></i>
                        <span class="nav-label mininav-content">Home</span>
                    </a>
                </li>
                @if(checkPermissions(['Scan acceso'],['R']))
                <li class="nav-item main_scan">
                    <a href="/scan_usuario" class="nav-link mininav-toggle collapsed"><i class="fa-light fa-qrcode fs-5 me-2"></i>
                        <span class="nav-label mininav-content"> Scan</span>
                    </a>
                </li>
                @endif
                @if(checkPermissions(['Reservas'],['R']))
                <li class="nav-item has-sub ">
                    <a href="#" class="mininav-toggle nav-link reservas"><i class="fa-light fa-calendar-day fs-5 me-2"></i>{{-- active --}}
                        <span class="nav-label">Reservas</span>
                    </a>
                    <!-- Dashboard submenu list -->
                    <ul class="mininav-content nav collapse">
                        @if(checkPermissions(['Reservas puestos'],['R']))<li class="reservas_puestos nav-item"><a href="/reservas" class="text-nowrap nav-link"><i class="fad fa-door-open"></i> Habitaciones</a></li> @endif
                        @if(checkPermissions(['Reservas salas'],['R']) && session('CL')['mca_salas']=='S')<li class="reservas_salas nav-item"><a href="/salas/reservas" class="text-nowrap nav-link"><i class="fad fa-users-class"></i> Salas</a></li> @endif
                    </ul>
                    <!-- END : Dashboard submenu list -->
                </li>
                @endif

               
            </ul>
            <ul class="mainnav__menu nav flex-column">
                @if(checkPermissions(['Mi oficina'],['R']))
                    <li class="nav-item has-sub ">
                        <a href="#" class="mininav-toggle nav-link mioficina">
                            <i class="fa-light fa-browser fs-5 me-2"></i>
                            @if(checkPermissions(['Mi oficina'],['R']))<span class="nav-label">Estado actual</span> @endif
                        </a>
                        
                        <!--Submenu-->
                        <ul class="mininav-content nav collapse">
                            @if(checkPermissions(['Mapa puestos'],['R']))<li class="  nav-item"><a href="/puestos/mapa"  class="text-nowrap nav-link mapa"><i class="fad fa-th"></i> Espacios</a></li> @endif
                            @if(checkPermissions(['Salas'],['R']) && session('CL')['mca_salas']=='S')<li class=" text-nowrap nav-item"><a href="/salas" class="text-nowrap nav-link salas"><i class="fa-light fa-users-class"></i> Salas reunion</a></li> @endif

                        </ul>
                    </li>
                @endif
            </ul>
            @if(checkPermissions(['Mantenimiento','Limpieza','Trabajos menu servicios'],['R']))
            <h6 class="mainnav__caption mt-5 px-3 fw-bold">Servicios</h6>
            <ul class="mainnav__menu nav flex-column">

                @if(checkPermissions(['Solicitudes'],['R']) && session('CL')['mca_solicitudes']=='S')
                    <li class="nav-item">
                        <a href="{{ url('/solicitudes') }}" class="nav-link mininav-toggle collapsed solicitudes"><i class="fa-light fa-clipboard-question fs-5 me-2"></i>
                            <span class="nav-label mininav-content"> Solicitudes</span>
                        </a>
                    </li>    
                @endif

                @if(checkPermissions(['Incidencias'],['R']))
                    <li class="nav-item">
                        <a href="{{ url('/incidencias') }}" class="nav-link mininav-toggle collapsed incidencias"><i class="fa-light fa-exclamation-triangle fs-5 me-2"></i>
                            <span class="nav-label mininav-content"> Incidencias</span>
                        </a>
                    </li>    
                @endif

                
                @if(checkPermissions(['Trabajos menu servicios'],['R']))
                <li class="nav-item has-sub ">
                    @if(checkPermissions(['Trabajos mis trabajos','Trabajos planes'],['R']))<a href="#" class="mininav-toggle nav-link servicios_trabajos"><i class="fa-light fa-light fa-user-helmet-safety fs-5 me-2"></i> <span class="nav-label">Trabajos planificados</span></a>@endif
                    <ul class="mininav-content nav collapse">
                        @if(checkPermissions(['Trabajos mis trabajos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/trabajos/mistrabajos" class="nav-link servicios_mistrabajos"> <i class="fa-light fa-list-check"></i>Mis tareas</a></li> @endif
                        @if(checkPermissions(['Trabajos planes'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/trabajos/planificacion" class="nav-link servicios_planes"> <i class="fa-light fa-list-tree"></i> Planes</a></li> @endif
                    </ul>
                </li>
                @endif
            </ul>
            @endif
            @if(checkPermissions(['Informes'],['R']))
                <h6 class="mainnav__caption mt-5 px-3 fw-bold">Informes</h6>
                <ul class="mainnav__menu nav flex-column">

                    @if(checkPermissions(['Informes'],['R']))
                    <li class="nav-item has-sub">
                        <a href="#" class="mininav-toggle nav-link informes"><i class="fa-light fa-file-chart-pie fs-5 me-2"></i>{{-- active --}}
                            <span class="nav-label">Informes</span>
                        </a>
                        <!-- Dashboard submenu list -->
                        <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Informes > Reservas canceladas'],['R']))<li class=" nav-item informes"><a href="/reports/canceladas" class="text-nowrap nav-link inf_reservas"><i class="fa-light fa-file-alt"></i> Reservas canceladas</a></li> @endif
                                @if(checkPermissions(['Informes > Trabajos planificados'],['R']))<li class=" nav-item informes"><a href="/reports/trabajos" class="text-nowrap nav-link inf_trabajos"><i class="fa-light fa-file-alt"></i> Trabajos planificados</a></li> @endif
                                @if(checkPermissions(['Informes > Incidencias'],['R']))<li class=" nav-item informes"><a href="/reports/incidencias" class="text-nowrap nav-link inf_incidencias"><i class="fa-light fa-file-alt"></i> Incidencias y solicitudes</a></li> @endif
                               
                                @if(checkPermissions(['Informes programados'],["R"]))<li class=" nav-item informes"><a href="{{url('prog_report')}}" class="text-nowrap nav-link inf_programados"><i class="fa-light fa-envelope"></i> Informes Programados</a></li>@endif
                        </ul>
                        <!-- END : Dashboard submenu list -->
                    </li>
                    @endif

                   
                </ul>
            @endif


            @if(checkPermissions(['Configuracion'],['R']))
            <h6 class="mainnav__caption mt-5 px-3 fw-bold">Configuracion</h6>
            <ul class="mainnav__menu nav flex-column">
                <li class="nav-item has-sub">

                    <a href="#" class="mininav-toggle nav-link collapsed menu_parametrizacion"><i class="fa-light fa-cog fs-5 me-2"></i>
                        <span class="nav-label">Parametrizacion</span>
                    </a>

                    <ul class="mininav-content nav collapse">
                        <li class="nav-item">
                            @if(checkPermissions(['Bitacora'],['R']))<li class=" nav-item"><a href="/bitacoras" class="nav-link bitacora"><i class="fa-light fa-clipboard-list fs-5 me-2"></i> Bitacora</a></li> @endif
                        </li>
                        <li class="nav-item has-sub ">
                            <a href="#" class="mininav-toggle nav-link collapsed espacios"><i class="fa-light fa-city fs-5 me-2"></i> Espacios</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Clientes'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/clientes" class="nav-link clientes"><i class="fa-light fa-user-tie"></i> Empresas</a></li> @endif
                                @if(checkPermissions(['Edificios'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/edificios" class="nav-link edificios"><i class="fa-light fa-building"></i> Edificios</a></li> @endif
                                @if(checkPermissions(['Plantas'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/plantas" class="nav-link plantas"> <i class="fa-light fa-layer-group"></i> Plantas</a></li> @endif
                                @if(checkPermissions(['Puestos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/puestos" class="nav-link puestos"> <i class="fa-light fa-desktop-alt"></i> Espacios</a></li> @endif
                                
                                @if(checkPermissions(['Tipos de puesto'],['R']))<li class="  text-nowrap ml-2 nav-item" ><a href="/puestos/tipos" class="nav-link puestostipos"><i class="fa-light fa-desktop-alt"></i> Tipos de espacio</a></li> @endif
                                @if(checkPermissions(['Tags'],['R']))<li class="  text-nowrap ml-2 nav-item"><a href="/tags" class="nav-link"> <i class="fa-light fa-tags tags"></i> Tags</a></li> @endif
                                @if(checkPermissions(['Encuestas'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/encuestas" class="nav-link"><i class="fa-light fa-poll-h encuestas"></i> Encuestas</a></li> @endif
                                @if(checkPermissions(['Bloqueo de puestos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/bloqueo" class="nav-link"><i class="fa-light fa-lock"></i> Bloqueo de puestos</a></li> @endif
                            </ul>
                        </li>
                        <li class="nav-item has-sub ">
                            <a href="#" class="mininav-toggle nav-link collapsed menu_usuarios"><i class="fa-light fa-user fs-5 me-2"></i>Personas</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Usuarios'],['R'])) <li class=" text-nowrap ml-2 nav-item"><a href="/users" class="nav-link usuarios"><i class="fa-light fa-user"></i> Usuarios</a></li>  @endif
                                @if(checkPermissions(['Plantas usuarios'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/users/plantas_usuarios" class="nav-link plantas_usuarios"> <i class="fa-light fa-layer-plus"></i> Asignar plantas</a></li> @endif
                                {{-- @if(checkPermissions(['Puestos supervisores'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/users/puestos_supervisores" class="nav-link puestos_supervisores"> <i class="fa-light fa-magnifying-glass-location"></i> Supervision puestos</a></li> @endif --}}
                                @if(checkPermissions(['Departamentos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/departments" class="nav-link departamentos"><i class="fa-light fa-sitemap"></i> Departamentos</a></li> @endif
                                @if(checkPermissions(['Colectivos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/collective" class="nav-link colectivos"><i class="fa-light fa-user-tag"></i> Colectivos</a></li> @endif
                                {{-- @if(checkPermissions(['Turnos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/turnos" class="nav-link turnos"><i class="fa-light fa-repeat-1"></i> Turnos</a></li> @endif --}}
                                {{-- @if(checkPermissions(['Festivos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/festives" class="nav-link festivos"><i class="fa-light fa-calendar-range"></i> Festivos</a></li> @endif --}}
                            </ul>
                        </li>
                        <li class="nav-item has-sub ">
                            <a href="#" class="mininav-toggle nav-link collapsed tipos_incidencia"><i class="fa-light fa-exclamation-triangle fs-5 me-2"></i> Incidencias</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Tipos de incidencia'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/incidencias/tipos" class="nav-link incidencias_tipos"> <i class="fa-light fa-exclamation-triangle"></i> Tipos de incidencia</a></li> @endif
                                @if(checkPermissions(['Causas de cierre'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/incidencias/causas" class="nav-link incidencias_causas"> <i class="fa-light fa-times-hexagon"></i> Causas de cierre</a></li> @endif
                                @if(checkPermissions(['Estados de incidencia'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/incidencias/estados" class="nav-link incidencias_estados"> <i class="fa-light fa-sign"></i> Estados</a></li> @endif
                            </ul>
                        </li>
                        <li class="nav-item has-sub ">
                            @if(checkPermissions(['Trabajos tipos','Trabajos','Trabajos planificador'],['R']))<a href="#" class="mininav-toggle nav-link collapsed trabajos"><i class="fa-light fa-light fa-user-helmet-safety fs-5 me-2"></i> Trabajos</a>@endif
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Trabajos tipos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/trabajos/tipos" class="nav-link trabajos_tipos"> <i class="fa-light fa-list-check"></i>Tareas</a></li> @endif
                                @if(checkPermissions(['Trabajos'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/trabajos/grupos" class="nav-link trabajos_grupos"> <i class="fa-light fa-list-tree"></i> Grupos</a></li> @endif
                                @if(checkPermissions(['Trabajos contratas'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/trabajos/contratas" class="nav-link trabajos_contratas"> <i class="fa-regular fa-industry"></i> Contratas</a></li> @endif
                                @if(checkPermissions(['Trabajos planificador'],['R']))<li class=" text-nowrap ml-2 nav-item"><a href="/trabajos/planes" class="nav-link trabajos_planes"> <i class="fa-light fa-square-kanban"></i> Planes de trabajo</a></li> @endif
                            </ul>
                        </li>
                        <li class="nav-item has-sub ">
                            <a href="#" class="mininav-toggle nav-link collapsed menu_permisos"><i class="fa-light fa-user-lock fs-5 me-2"></i> Permisos</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Perfiles'],['R']))<li class=" nav-item"><a href="/profiles"  class="nav-link perfiles"><i class="fa-light fa-users"></i> Perfiles</a></li> @endif
                                @if(checkPermissions(['Secciones'],['R']))<li class=" nav-item"><a href="/sections" class="nav-link secciones"> <i class="fa-light fa-browser"></i> Secciones</a></li> @endif
                                @if(checkPermissions(['Permisos'],['R']))<li class=" nav-item"><a href="/profile-permissions" class="nav-link permisos" ><i class="fa-light fa-lock-alt"></i> Permisos</a></li> @endif
                            </ul>
                        </li>
                        <li class="nav-item has-sub ">
                            <a href="#" class="mininav-toggle nav-link collapsed menu_utilidades"><i class="fa-light fa-screwdriver-wrench fs-5 me-2"></i> Utilidades</a>
                            <ul class="mininav-content nav collapse">
                                @if(checkPermissions(['Tareas programadas'],['R']))<li class=" nav-item"><a href="/tasks" class="text-nowrap nav-link tareas_programadas"> <i class="fa-light fa-timer"></i> Tareas programadas</a></li> @endif
                                @if (checkPermissions(['Eventos'],["R"]))<li class=" nav-item"><a href="{{url('/events')}}" class="nav-link eventos"><i class="fa-light fa-engine"></i> Eventos</a></li>@endif
                                @if(checkPermissions(['Señaletica'],['R']))<li class=" nav-item"><a href="/MKD" class="nav-link mkd"><i class="fa-light fa-sign"></i> Señaletica</a></li> @endif
                    
                                @if(checkPermissions(['Importar datos'],['R']))<li class=" nav-item"> <a href="/import" class="nav-link importar"><i class="fa-light fa-upload"></i> Importar datos</a></li> @endif
                                @if(checkPermissions(['Log viewer'],['R']))<li class=" nav-item"> <a href="/ext/docs/log-viewer" class="nav-link logviewer"><i class="fa-light fa-file-lines"></i> Logs</a></li> @endif
                            </ul>
                        </li>
                    </ul>

                </li>
            </ul>
            @endif
            @if(session('DIS') && isset(session('DIS')['img_logo']))
                <div class="text-center mt-5">
                    <img src="{{ url('/img/distribuidores/'.session('DIS')['img_logo']) }}" title="{{ session('DIS')['nom_distribuidor'] }}" style="width:50%">
                </div> 
            @endif
            {{-- <!-- Widget -->
            <div class="mainnav__profile">

                <!-- Widget buttton form small navigation -->
                <div class="mininav-toggle text-center py-2 d-mn-min">
                    <i class="pli-monitor-2"></i>
                </div>

                <div class="d-mn-max mt-5"></div>

                <!-- Widget content -->
                <div class="mininav-content collapse d-mn-max">
                    <h6 class="mainnav__caption px-3 fw-bold">Server Status</h6>
                    <ul class="list-group list-group-borderless">
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-2 me-auto">CPU Usage</p>
                                <span class="badge bg-info rounded">35%</span>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-info" role="progressbar" style="width: 35%" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="d-flex justify-content-between align-items-start">
                                <p class="mb-2 me-auto">Bandwidth</p>
                                <span class="badge bg-warning rounded">73%</span>
                            </div>
                            <div class="progress progress-md">
                                <div class="progress-bar bg-warning" role="progressbar" style="width: 73%" aria-valuenow="73" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </li>
                    </ul>
                    <div class="d-grid px-3 mt-3">
                        <a href="#" class="btn btn-sm btn-success">View Details</a>
                    </div>
                </div>
            </div> --}}
            <!-- End - Profile widget -->

        </div>
        <!-- End - Navigation menu -->

        <!-- Bottom navigation menu -->
        <div class="mainnav__bottom-content border-top pb-2">
            <ul id="mainnav" class="mainnav__menu nav flex-column">
                <li class="nav-item has-sub">
                    <a href="#" class="nav-link mininav-toggle collapsed" aria-expanded="false">
                        <i class="pli-unlock fs-5 me-2"></i>
                        <span class="nav-label">Logout</span>
                    </a>
                    <ul class="mininav-content nav flex-column collapse">
                        <li class="nav-item">
                            <a href="{{url('/logout')}}" class="nav-link">Este dispositivo</a>
                        </li>
                        <li class="nav-item">
                            <a href="{{url('/logout')}}" class="nav-link">Todos mis dispositivos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ url('/lockscreen') }}" tabindex="-1" aria-disabled="true">Bloquear pantalla</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- End - Bottom navigation menu -->

    </div>
</nav>
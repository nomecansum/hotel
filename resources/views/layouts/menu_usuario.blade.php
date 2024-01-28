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
                @if(checkPermissions(['Incidencias > Mis incidencias'],['R']))<li class="incidencias  nav-item"><a href="/incidencias/mis_incidencias" class="nav-link mininav-toggle collapsed"><i class="fa-light fa-exclamation-triangle fs-5 me-2"></i> <span class="nav-label mininav-content"> Mis incidencias</span></a></li> @endif
                @if(checkPermissions(['Solicitudes> Mis solicitudes'],['R']))<li class="  nav-item"><a href="/solicitudes/mis_solicitudes" class="text-nowrap nav-link mininav-toggle collapsed mis_solicitudes"><i class="fa-light fs-5 me-2 fa-clipboard-question"></i> Mis solicitudes</a></li> @endif

                @if(checkPermissions(['Reservas puestos'],['R']))<li class="reservas_puestos nav-item"><a href="/reservas" class="nav-link mininav-toggle collapsed"><i class="fa-light fa-calendar-day fs-5 me-2"></i><span class="nav-label mininav-content">  Reservas</span></a></li> @endif
                @if(checkPermissions(['Reservas salas'],['R']) && session('CL')['mca_salas']=='S')<li class="reservas_salas nav-item"><a href="/salas/reservas" class="nav-link mininav-toggle collapsed"><i class="fad fa-users-class fs-5 me-2"></i><span class="nav-label mininav-content">  Salas</span></a></li> @endif

            
                <h6 class="mainnav__caption mt-5 px-3 fw-bold">Mi oficina</h6>

                @if(checkPermissions(['Mapa puestos'],['R']))<li class="mapa  nav-item"><a href="/puestos/mapa"  class="nav-link mininav-toggle collapsed"><i class="fad fa-th fs-5 me-2"></i><span class="nav-label mininav-content">  Mapa</span></a></li> @endif
                @if(checkPermissions(['Compañeros'],['R']))<li class="compas  nav-item"><a href="/puestos/compas"  class="nav-link mininav-toggle collapsed"><i class="fa-duotone fa-users fs-5 me-2"></i><span class="nav-label mininav-content">  Mis compañeros</span></a></li> @endif
                @if(checkPermissions(['Salas'],['R']) && session('CL')['mca_salas']=='S')<li class="salas text-nowrap nav-item"><a href="/salas" class="nav-link mininav-toggle collapsed"><i class="fa-light fa-users-class fs-5 me-2"></i><span class="nav-label mininav-content">  Salas reunion</span></a></li> @endif

            
            </ul>
            


            
            @if(session('DIS') && isset(session('DIS')['img_logo']))
                <div class="text-center mt-5">
                    <img src="{{ url('/img/distribuidores/'.session('DIS')['img_logo']) }}" title="{{ session('DIS')['nom_distribuidor'] }}" style="width:50%">
                </div> 
            @endif


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
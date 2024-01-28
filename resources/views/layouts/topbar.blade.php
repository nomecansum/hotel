<div class="header__inner">

    <!-- Brand -->
    <div class="header__brand">
        <div class="brand-wrap">

            <!-- Brand logo -->
            <a href="{{url('/')}}" class="brand-img stretched-link">
                @if(session('logo_cliente_menu'))
                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente_menu')) }}" style="width: 45px; height: 45px" alt="" class="Nifty logo rounded">
                @else   
                <img src="/img/logo.png" alt="Spotlinker" class="Nifty logo">
                @endif
                
            </a>

           

            <!-- Brand title -->
            <div class="brand-title">
                <span class="brand-text"> SpotHospitality</span>
            </div>

            <!-- You can also use IMG or SVG instead of a text element. -->

        </div>
    </div>
    <!-- End - Brand -->

    <div class="header__content">

        <!-- Content Header - Left Side: -->
        <div class="header__content-start">

            <!-- Navigation Toggler -->
            <button type="button" class="nav-toggler header__btn btn btn-icon btn-sm">
                <i class="demo-psi-view-list"></i>
            </button>

            <!-- Searchbox -->
            <div class="header-searchbox">

                <!-- Searchbox toggler for small devices -->
                <label for="header-search-input" class="header__btn d-md-none btn btn-icon rounded-pill shadow-none border-0 btn-sm" type="button">
                    <i class="demo-psi-magnifi-glass"></i>
                </label>

                <!-- Searchbox input -->
                <form class="searchbox searchbox--auto-expand searchbox--hide-btn input-group" method="post" action="{{ url('/search') }}">
                    @csrf
                    <input id="header-search-input" class="searchbox__input form-control bg-transparent" name="txt_buscar" type="search" placeholder="Buscar . . ." aria-label="Search" value="{{ isset($r->txt_busar)?$r->txt_buscar:'' }}">
                    <div class="searchbox__backdrop">
                        <button class="searchbox__btn header__btn btn btn-icon rounded shadow-none border-0 btn-sm" type="button" id="button-addon2">
                            <i class="demo-pli-magnifi-glass"></i>
                        </button>
                    </div>
                </form>
            </div>

            @if(config('app.env') == 'local' || config('app.env') == 'qa')
                <div>{{ env('DB_DATABASE') }}</div>
            @endif
            
        </div>
        <!-- End - Content Header - Left Side -->

        <!-- Content Header - Right Side: -->
        <div class="header__content-end">

            <!-- Notification Dropdown -->
            <div class="dropdown">

                @php
                    $cn=cuenta_notificaciones();
                @endphp
                <!-- Toggler -->
                <button class="header__btn btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false" id="btn_notif">
                    <span class="d-block position-relative">
                        <i class="demo-psi-bell"></i>
                        <span class="badge badge-super rounded bg-danger p-1" id="badge_notificaciones" style="{{ count($cn)>0?'':'display:none' }}" >
                            <span class="cuenta_notificaciones">{{ count($cn)>0?count($cn):'' }}</span><span class="visually-hidden">unread messages</span>
                        </span>
                    </span>
                </button>

                <!-- Notification dropdown menu -->
                <div class="dropdown-menu dropdown-menu-end w-md-300px">
                    <div class="border-bottom px-3 py-3 mb-3">
                        <h5>Notificaciones</h5>
                    </div>

                    <div class="list-group list-group-borderless" id="lista_notif">


                    </div>
                    <a href="{{ url('/notif') }}" class="btn btn-link shadow-none">Ver todas las notificaciones</a>
                </div>
            </div>
            <!-- End - Notification dropdown -->

           <!-- User dropdown -->
            <div class="dropdown">

                <!-- Toggler -->
                <button class="header__btn btn btn-icon btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-user"></i>
                </button>

                <!-- User dropdown menu -->
                <div class="dropdown-menu dropdown-menu-end w-md-450px">

                    <!-- User dropdown header -->
                    <div class="d-flex align-items-center border-bottom p-3">
                        <div class="flex-shrink-0">
                           
                            @if (isset(Auth::user()->img_usuario ) && Auth::user()->img_usuario!='')
                                <img src="{{ Storage::disk(config('app.img_disk'))->url('/img/users/'.Auth::user()->img_usuario) }}" class="img-md rounded-circle">
                            @else
                            {!! icono_nombre(Auth::user()->name,50,18) !!}
                            @endif
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h5 class="mb-0">{{ Auth::user()->name }}</h5>
                            <span class="text-muted fst-italic">{{Auth::user()->email}}</span>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-7">

                            <!-- Simple widget and reports -->
                            <div class="list-group list-group-borderless ">
                                <div class="list-group-item text-center border-bottom mb-2">
                                    <p class="display-1 text-warning cuenta_reservas">{{ session('reservas')!==null?count(session('reservas')):0 }}</p>
                                    <p class="h6 mb-0"><i class="fa-light fa-calendar-circle-user"></i> Reservas</p>
                                    <small class="text-muted">Reservas para hoy {!! beauty_fecha(Carbon\Carbon::now(),0) !!}</small>
                                </div>
                            </div>
    
                                @if(session('reservas')!==null)
                                    @foreach(session('reservas') as $reserva)
                                        <div class="py-0">
                                            <i class="{{ $reserva->icono_tipo }}" style="color: {{ $reserva->color_tipo }}"></i> {{ $reserva->des_tipo_puesto }} 
                                            <small class="fw-bolder"> {{ $reserva->cod_puesto }}</small>
                                        </div>
                                    @endforeach
                                @endif
     
                        </div>
                        <div class="col-md-5">

                            <!-- User menu link -->
                            <div class="list-group list-group-borderless h-100 py-3">
                                <a href="{{ url('/notif') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                    <span><i class="fa-light fa-bell fs-5 me-3"></i> Avisos</span>
                                    <span class="badge bg-danger rounded-pill cuenta_notificaciones">0</span>
                                </a>
                                <a href="{{ url('/miperfil/'.Auth::user()->id) }}" class="nav-link">
                                    <i class="demo-pli-male fs-5 me-3"></i> Perfil
                                </a>
                                <a class="list-group-item list-group-item-action" id="_dm-settingsToggler" class="_dm-btn-settings list-group-item list-group-item-action" data-bs-toggle="offcanvas" data-bs-target="#_dm-settingsContainer" aria-controls="_dm-settingsContainer">
                                    <i class="demo-pli-gear fs-5 me-3"></i> Ajustes
                                </a>

                                <a href="{{ url('/lockscreen') }}" class="nav-link">
                                    <i class="demo-pli-computer-secure fs-5 me-3"></i> Bloquear
                                </a>
                                <a href="{{url('/logout')}}" class="nav-link">
                                    <i class="demo-pli-unlock fs-5 me-3"></i> Logout
                                </a>
                                @if(session('back_id') && session('back_id')!=Auth::user()->id)
                                    <a href="#" onclick="document.location='{{url('reback')}}'" class="nav-link text-warning">
                                        <i class="fa-regular fa-delete-left"></i> Volver a mi sesion
                                    </a>
                                @endif
                            </div>

                        </div>
                    </div>
                    {{-- <div class="row">
                        <div class="col-md-12">
                            @if(session('reservas')!==null)
                                @foreach(session('reservas') as $reserva)
                                    <div class="py-0">
                                        <i class="{{ $reserva->icono_tipo }}" style="color: {{ $reserva->color_tipo }}"></i> {{ $reserva->des_tipo_puesto }} 
                                        <small class="fw-bolder"> {{ $reserva->cod_puesto }}</small>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div> --}}

                </div>
            </div>
            <!-- End - User dropdown -->

            <!-- Sidebar Toggler -->
            {{-- <button class="sidebar-toggler header__btn btn btn-icon btn-sm" type="button">
                <i class="demo-psi-dot-vertical"></i>
            </button> --}}

        </div>
    </div>
</div>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta name="generator" content="Hugo 0.87.0" />
    <meta http-equiv="content-type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0, user-scalable=no, shrink-to-fit=yes">
    <title>{{ config('app.name') }}</title>
    <link rel="shortcut icon" type="image/jpg" href="/img/logo.png"/>

    <!-- STYLESHEETS -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~--- -->

    <!-- Fonts [ OPTIONAL ] -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&family=Ubuntu:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap CSS [ REQUIRED ] -->
    @if(session('template')!==null && isset(session('template')->esquema)) <link rel="stylesheet" href="{{ asset('/assets/css'.session('template')->esquema.'/bootstrap.min.css') }}"> @else <link rel="stylesheet" href="{{ asset('/assets/css/bootstrap.min.css') }}"> @endif
    
    {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> --}}


    <!-- Nifty CSS [ REQUIRED ] -->
    @if(session('template')!==null && isset(session('template')->tema)) <link rel="stylesheet" href="{{ asset('/assets/css'.session('template')->tema.'/nifty.min.css') }}"> @else <link rel="stylesheet" href="{{ asset('/assets/css/nifty.min.css') }}"> @endif
    

    <!-- Nifty Demo Icons [ OPTIONAL ] -->
    <link rel="stylesheet" href="{{ asset('/assets/css/demo-purpose/demo-icons.min.css') }}">

    <!-- Demo purpose CSS [ DEMO ] -->
    <link rel="stylesheet" href="{{ asset('/assets/css/demo-purpose/demo-settings.min.css') }}">

    {{--  FontAwesome  --}}
    <link href="{{ asset('/plugins/fontawesome6/css/all.min.css') }}" rel="stylesheet">

    <!-- Select2 -->
    <link rel="stylesheet" href="{{ asset('/plugins/select2/css/select2.min.css') }}">

    <!--Mosaic custom CSS [ REQUIRED ]-->
    <link href="{{ asset('/css/mosaic.css') }}" rel="stylesheet">
    {{--  Animate CSS  --}}
    <link rel="stylesheet" href="{{ asset('/plugins/animate-css/animate.min.css') }}">

    {{--  Datatables  --}}
    <link rel="stylesheet" href="{{ asset('/plugins/bootstrap-table/bootstrap-table.min.css') }}">

    {{--  Toast  --}}
    {{-- <link href="{{asset('/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet" media="all"> --}}
    <link href="{{ asset('/plugins/BootstrapToaster-master/css/bootstrap-toaster.min.css') }}" rel="stylesheet">

    {{--  Iconpicker  --}}
    <link href="{{asset('/plugins/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css')}}" rel="stylesheet" media="all">
    {{--  CSS Loaders  --}}
    <link href="{{ asset('/plugins/css-loaders/css/css-loaders.css') }}" rel="stylesheet">
    {{--  Datepicker  --}}
    <link href="{{ asset('/plugins/litepicker-master/dist/litepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/MCDatepicker/mc-calendar.min.css') }}" rel="stylesheet">

    @yield('styles')
    @yield('styles2')
    @include('layouts.styles')
    {{-- Onesignal SDK --}}
    <script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" defer></script>
    @if(!config('app.debug'))
        @laravelPWA
    @endif
   
</head>

<body class="in-out-back {{ clase_body() }}" {!! image_body() !!}>

    <!-- PAGE CONTAINER -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <div id="root" class="root {{ clase_root() }} {{ clase_menu() }} {{ clase_sticky() }}">

        <!-- CONTENTS -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        {{--  --}}
        <section id="content" class="content">
            <div class="content__header content__boxed overlapping ">
                <div class="content__wrap pt-3 pb-4">
                    <nav aria-label="breadcrumb">
                        @yield('breadcrumb')
                    </nav>
                    <div class="d-md-flex align-items-center">
                        <div class="me-auto">
                            <!-- Page title and information -->
                            <div class="text-start">
                                <h1 class="page-title mb-2"> @yield('title')</h1>
                                {{-- <h3 class="h5">Welcome back to the Dashboard.</h3>
                                <p class="">Scroll down to see quick links and overviews of your Server, To do list<br> Order status or get some Help using Nifty.</p> --}}
                            </div>
                            <!-- END : Page title and information -->
                        </div>
                    </div>
                </div>

            </div>

            <div class="content__boxed">
                <div class="content__wrap">
                    @yield('content')
                </div>
            </div>
            
            <div class="content__boxed">
                <div class="content__wrap">
                    @yield('content2')
                </div>
            </div>
            <!-- FOOTER -->
            <footer class="content__boxed mt-auto">
                @include('layouts.footer')
            </footer>
            <!-- END - FOOTER -->

        </section>

        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <!-- END - CONTENTS -->

        <!-- HEADER -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        @if(!isset($error_cuenta_no_activada))
            <header class="header">
                @if(Auth::check())
                    @include('layouts.topbar')
                @endif
                @include('flash::message')
                
            </header>
        @endif
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <!-- END - HEADER ->

        <!-- MAIN NAVIGATION -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

        @if(Auth::check() && !isset($error_cuenta_no_activada))
            @if(Auth::user()->nivel_acceso>=10)
                @include('layouts.menu')
            @endif
            @if(Auth::user()->nivel_acceso==1)
                @include('layouts.menu_usuario')
            @endif
        @endif

        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <!-- END - MAIN NAVIGATION -->

        <!-- SIDEBAR -->
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        @if(!isset($error_cuenta_no_activada))
            <aside class="sidebar">
                @include('layouts.aside')
            </aside>
        @endif
        <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
        <!-- END - SIDEBAR -->

    </div>
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <!-- END - PAGE CONTAINER -->

    <!-- SCROLL TO TOP BUTTON -->
    <div class="scroll-container">
        <a href="#root" class="scroll-page rounded-circle ratio ratio-1x1"></a>
    </div>
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <!-- END - SCROLL TO TOP BUTTON -->

    
    @include('layouts.settings')
    

    <!-- OFFCANVAS [ DEMO ] -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <div id="offcanvasBottom" class="offcanvas offcanvas-bottom" tabindex="-1">

        <!-- Offcanvas header -->
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Politica de privacidad</h5>
            <button type="button" class="btn-close btn-lg text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <!-- Offcanvas content -->
        <div class="offcanvas-body body_politica">
            <h5>Content Here</h5>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente eos nihil earum aliquam quod in dolor, aspernatur obcaecati et at. Dicta, ipsum aut, fugit nam dolore porro non est totam sapiente animi recusandae obcaecati dolorum, rem ullam cumque. Illum quidem reiciendis autem neque excepturi odit est accusantium, facilis provident molestias, dicta obcaecati itaque ducimus fuga iure in distinctio voluptate nesciunt dignissimos rem error a. Expedita officiis nam dolore dolores ea. Soluta repellendus delectus culpa quo. Ea tenetur impedit error quod exercitationem ut ad provident quisquam omnis! Nostrum quasi ex delectus vero, facilis aut recusandae deleniti beatae. Qui velit commodi inventore.</p>
        </div>

    </div>

    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
    <!-- END - OFFCANVAS [ DEMO ] -->

    <!-- JAVASCRIPTS -->
    <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->

    <!--jQuery [ REQUIRED ]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.0/jquery.min.js"></script>
    <!--jQueryUI [ REQUIRED ]-->
    <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.13.2/jquery-ui.min.js"></script>
    <!-- Popper JS [ OPTIONAL ] -->
    <script src="{{ asset('/assets/vendors/popperjs/popper.min.js')}}" defer></script>

    <!-- Bootstrap JS [ OPTIONAL ] -->
    {{-- <script src="{{ asset('/assets/vendors/bootstrap/bootstrap.min.js')}}" defer></script> --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Nifty JS [ OPTIONAL ] -->
    <script src="{{ asset('/assets/js/nifty.js')}}" defer></script>

    <!-- Nifty Settings [ DEMO ] -->
    <script src="{{ asset('/assets/js/nifty_settings.js')}}" defer></script>


    <!-- Select2 -->
    <script src="{{ url('/plugins/select2/js/select2.full.min.js') }}"></script>
    {{--  SweetAlert  --}}
    <script src="{{url('/plugins/sweetalert/dist/sweetalert2.all.min.js')}}"></script>
    {{-- Iconpicker --}}
    <script src="{{ asset('/plugins/bootstrap-iconpicker/js/bootstrap-iconpicker-iconset-all.min.js') }}"></script>
    <script src="{{ asset('/plugins/bootstrap-iconpicker/js/bootstrap-iconpicker.js') }}"></script>

    {{--  Toast  --}}
    {{-- <script src="{{url('/plugins/toast-master/js/jquery.toast.js')}}"></script> --}}
    <script src="{{ asset('/plugins/BootstrapToaster-master/js/bootstrap-toaster.min.js') }}"></script>
    
    {{-- Plugin print --}}
    <script src="{{asset('/plugins/printThis-master/printThis.js')}}"></script>

    {{-- Datatables --}}
    <script src="{{asset('/plugins/bootstrap-table/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('/plugins/bootstrap-table/extensions/mobile/bootstrap-table-mobile.min.js')}}"></script>
    <script src="{{asset('/plugins/bootstrap-table/bootstrap-table-locale-all.min.js')}}"></script>

    {{-- Datepickers --}}
    <script src="{{ asset('/plugins/momentjs/moment.js') }}"></script>
    <script src="{{ asset('/plugins/MCDatepicker/mc-calendar.min.js') }}"></script>
    <script src="{{ asset('/plugins/litepicker-master/dist/bundle.min.js') }}"></script>

    {{-- switchery switchs deslizable --}}
    <script src="{{ asset('/plugins/switchery/switchery.min.js') }}"></script>
 


     @include('layouts.main_scripts')
     @yield('scripts')
     @yield('scripts2')
     @yield('scripts3')
     @yield('scripts4')
     @yield('scripts5')
     @yield('scripts6')
     @yield('onesignal')

</body>

</html>
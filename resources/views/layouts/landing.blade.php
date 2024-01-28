<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>Crambo - Evento</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Font Awesome icons (free version)-->
        <script src="https://use.fontawesome.com/releases/v5.15.4/js/all.js" crossorigin="anonymous"></script>
        <!-- Google fonts-->
        <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700" rel="stylesheet" type="text/css" />
        <link href="https://fonts.googleapis.com/css?family=Roboto+Slab:400,100,300,700" rel="stylesheet" type="text/css" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="/landing/css/styles.css" rel="stylesheet" />
        <link href="{{ asset('/plugins/fontawesome5/css/all.min.css') }}" rel="stylesheet">

         <!-- Select2 -->
        <link rel="stylesheet" href="{{ URL('/plugins/select2/css/select2.min.css') }}">
        @yield('estilos')
    </head>
    <body id="page-top">
        <!-- Navigation-->
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top" id="mainNav">
            <div class="container">
                <a class="navbar-brand" href="#page-top"><img src="/img/crambo_500_bl.png" id="logo_crambo" alt="..." /></a>
                {{-- <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                    Menu
                    <i class="fas fa-bars ms-1"></i> --}}
                </button>
                <div class="collapse navbar-collapse" id="navbarResponsive">
                    {{-- <ul class="navbar-nav text-uppercase ms-auto py-4 py-lg-0">
                        <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
                        <li class="nav-item"><a class="nav-link" href="#portfolio">Portfolio</a></li>
                        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="#team">Team</a></li>
                        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    </ul>
                </div> --}}
            </div>
        </nav>
        <!-- Masthead-->
        @yield('cabecera2')
        
        @yield('cuerpo')
       

        
        <!-- Footer-->
        <footer class="footer py-4">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-4 text-lg-start">Copyright &copy; Crambo MFL 2022</div>
                    <div class="col-lg-4 my-3 my-lg-0">
                        <a class="btn btn-dark btn-social mx-2" href="https://twitter.com/cramboEU?lang=es"><i class="fab fa-twitter"></i></a>
                        <a class="btn btn-dark btn-social mx-2" href="https://www.facebook.com/cramboeu/?modal=admin_todo_tour"><i class="fab fa-facebook-f"></i></a>
                        <a class="btn btn-dark btn-social mx-2" href="https://www.linkedin.com/company/cramboeu/"><i class="fab fa-linkedin-in"></i></a>
                        <a class="btn btn-dark btn-social mx-2" href="https://www.instagram.com/crambo_eu/"><i class="fab fa-instagram"></i></a>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a class="link-dark text-decoration-none me-3" href="#!">Privacy Policy</a>
                        <a class="link-dark text-decoration-none" href="#!">Terms of Use</a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- Portfolio Modals-->
        
       <!--jQuery [ REQUIRED ]-->
        <script src="{{ url('/js/jquery.min.js') }}"></script>
        <!--jQueryUI [ REQUIRED ]-->
        <script src="{{ url('/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
        <!--BootstrapJS [ RECOMMENDED ]-->
        <script src="{{ url('/js/bootstrap.min.js') }}"></script>
        <!-- Core theme JS-->
        <script src="/landing/js/scripts.js"></script>
         <!--JQuery -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
          <!--Moment -->
        <script src="{{ asset('/plugins/momentjs/moment.js') }}"></script>
        <!-- Select2 -->
        <script src="{{ url('/plugins/select2/js/select2.full.min.js') }}"></script>
         {{--  SweetAlert  --}}
        <script src="{{url('/plugins/sweetalert/dist/sweetalert2.all.min.js')}}"></script>

        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        <!-- * *                               SB Forms JS                               * *-->
        <!-- * * Activate your form at https://startbootstrap.com/solution/contact-forms * *-->
        <!-- * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *-->
        {{-- <script src="https://cdn.startbootstrap.com/sb-forms-latest.js"></script> --}}
        {{-- @include('layouts.main_scripts')         --}}
        @yield('scripts1')
        @yield('scripts2')

    </body>
</html>

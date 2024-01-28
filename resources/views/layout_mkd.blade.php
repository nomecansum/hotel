<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>QRClean</title>
    <!--STYLESHEET-->
    <!--=================================================-->
    <!--Open Sans Font [ OPTIONAL ]-->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
     <!--JQueryUI [OPTIONAL]-->
     <link href="{{ url('/plugins/jquery-ui/jquery-ui.css') }}" rel="stylesheet">
    <!--Bootstrap Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
    <!--Nifty Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/nifty.min.css') }}" rel="stylesheet">
    <!--Nifty Premium Icon [ DEMONSTRATION ]-->
    <link href="{{ url('/css/demo/nifty-demo-icons.min.css/') }}" rel="stylesheet">
    <!--=================================================-->
    <!--Pace - Page Load Progress Par [OPTIONAL]-->
    <link href="{{ url('/plugins/pace/pace.min.css') }}" rel="stylesheet">
    <script src="{{ url('/plugins/pace/pace.min.js') }}"></script>
    <!--Demo [ DEMONSTRATION ]-->
    <link href="{{ url('/css/demo/nifty-demo.css') }}" rel="stylesheet">
    {{-- MAterial design fonts --}}
    <link rel="stylesheet" href="{{ URL('/css/materialdesignicons.min.css') }}">
    {{--  FontAwesome  --}}
    <link href="{{ asset('/plugins/fontawesome5/css/all.min.css') }}" rel="stylesheet">


    <!--Mosaic custom CSS [ REQUIRED ]-->
    <link href="{{ url('/css/mosaic.css') }}" rel="stylesheet">
    {{--  Animate CSS  --}}
    <link rel="stylesheet" href="{{ URL('/plugins/animate-css/animate.min.css') }}">

    {{--  Datatables  --}}
    {{-- <link rel="stylesheet" href="{{ URL('/plugins/datatables/datatables.min.css') }}"> --}}
    <link rel="stylesheet" href="{{ URL('/plugins/bootstrap-table/bootstrap-table.min.css') }}">

    {{-- Custom file --}}
    <link href="{{ URL('/plugins/custom_file.css') }}" rel="stylesheet">
    <!-- Select2 -->
    <link rel="stylesheet" href="{{ URL('/plugins/select2/css/select2.min.css') }}">
    {{--  Toast  --}}
    <link href="{{url('/plugins/toast-master/css/jquery.toast.css')}}" rel="stylesheet" media="all">
    {{--  sweetAlert  --}}
    <link href="{{url('/plugins/sweetalert/dist/sweetalert2.min.css')}}" rel="stylesheet" media="all">
    {{-- Iconpicker --}}
    <link href="{{ asset('/plugins/bootstrap-iconpicker/css/bootstrap-iconpicker.min.css') }}" rel="stylesheet">
    {{--  CSS Loaders  --}}
    <link href="{{ asset('/plugins/css-loaders/css/css-loaders.css') }}" rel="stylesheet">
    {{-- Daterangepicker --}}
    <link href="{{ asset('/plugins/daterangepicker/daterangepicker.css') }}" rel="stylesheet">
    {{-- Boostrap Select --}}
    <link href="{{ asset('/plugins/bootstrap-select-master/css/bootstrap-select.min.css') }}" rel="stylesheet">



    @include('layouts.styles')
    @yield('styles')
</head>
<!--TIPS-->
<!--You may remove all ID or Class names which contain "demo-", they are only used for demonstration. -->
<body>
    <div id="container">

        <div class="boxed">

            <!--CONTENT CONTAINER-->
            <!--===================================================-->
            @yield('content')



        <!-- FOOTER -->
        <!--===================================================-->
        <footer id="footer">
            <!-- Visible when footer positions are fixed -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="show-fixed pad-rgt pull-right">
                You have <a href="#" class="text-main"><span class="badge badge-danger">3</span> pending action.</a>
            </div>
            <!-- Visible when footer positions are static -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <div class="hide-fixed pull-right pad-rgt">
                14GB of <strong>512GB</strong> Free.
            </div>
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <!-- Remove the class "show-fixed" and "hide-fixed" to make the content always appears. -->
            <!-- ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~ -->
            <p class="pad-lft">&#0169; 2020 <img src="{{url('/img/Mosaic_brand_20.png')}}"></p>
        </footer>
        <!--===================================================-->
        <!-- END FOOTER -->

    </div>
    <!--===================================================-->
    <!-- END OF CONTAINER -->





    <!--JAVASCRIPT-->
    <!--=================================================-->
    <!--jQuery [ REQUIRED ]-->
    <script src="{{ url('js/jquery.min.js') }}"></script>
    <!--jQueryUI [ REQUIRED ]-->
    <script src="{{ url('/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!--BootstrapJS [ RECOMMENDED ]-->
    <script src="{{ url('js/bootstrap.min.js') }}"></script>
    <!--NiftyJS [ RECOMMENDED ]-->
    <script src="{{ url('js/nifty.min.js') }}"></script>
    <!--=================================================-->
    <!--Demo script [ DEMONSTRATION ]-->
    <script src="{{ url('js/demo/nifty-demo.js') }}"></script>
     
    <!--Specify page [ SAMPLE ]-->
    {{--  <script src="{{ url('js/demo/dashboard.js') }}"></script>  --}}
    <!-- Select2 -->
    <script src="{{ url('/plugins/select2/js/select2.full.min.js') }}"></script>
    {{--  Toast  --}}
    <script src="{{url('/plugins/toast-master/js/jquery.toast.js')}}"></script>
   
    {{--  SweetAlert  --}}
    <script src="{{url('/plugins/sweetalert/dist/sweetalert2.all.min.js')}}"></script>
    {{-- Iconpicker --}}
    <script src="{{ asset('/plugins/bootstrap-iconpicker/js/bootstrap-iconpicker.bundle.min.js') }}"></script>
    {{-- Plugin print --}}
    <script src="{{asset('/plugins/printThis-master/printThis.js')}}"></script>

    {{-- Datatables --}}
    {{-- <script src="{{asset('/plugins/datatables/datatables.min.js')}}"></script> --}}
    <script src="{{asset('/plugins/bootstrap-table/bootstrap-table.min.js')}}"></script>
    <script src="{{asset('/plugins/bootstrap-table/bootstrap-table-locale-all.min.js')}}"></script>

    {{-- Datepickers --}}
    <script src="{{ asset('/plugins/momentjs/moment.js') }}"></script>
    <script src="{{ asset('/plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js') }}"></script>
    <script src="{{ asset('/plugins/bootstrap-datepicker/locales/bootstrap-datepicker.es.min.js') }}"></script>
    <script src="{{ asset('/plugins/daterangepicker/daterangepicker.js') }}"></script>

    {{-- Bootstrap select --}}
    <script src="{{ asset('/plugins/bootstrap-select-master/js/bootstrap-select.min.js') }}"></script>

    @include('layouts.main_scripts')
    @yield('scripts')
    @yield('scripts2')
    @yield('scripts3')

</body>
</html>

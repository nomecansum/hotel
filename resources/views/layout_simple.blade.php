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
    @yield('styles')
    @laravelPWA
</head>

<body>
    <div id="container" class="effect aside-float aside-bright mainnav-lg">

     @yield('content')
    </div>
    <!-- END OF CONTAINER -->





    <!--JAVASCRIPT-->
    <!--=================================================-->
    <!--jQuery [ REQUIRED ]-->
    <script src="{{ url('js/jquery.min.js') }}"></script>
    <!--jQueryUI [ REQUIRED ]-->
    <script src="{{ url('/plugins/jquery-ui/jquery-ui.min.js') }}"></script>
    <!--BootstrapJS [ RECOMMENDED ]-->
    <script src="{{ url('js/bootstrap.min.js') }}"></script>

    @include('layouts.main_scripts')
    @yield('scripts')
    @yield('scripts2')
    @yield('scripts3')

</body>
</html>

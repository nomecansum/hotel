@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Puestos pendientes de limpieza</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item"><a href="{{url('/limpieza')}}">Limpieza</a></li>
        <li class="breadcrumb-item active">Puestos pendientes de limpieza</li>
    </ol>
@endsection

@php
    $edificio_ahora=0;
    $planta_ahora=0;
    $estado_destino=1;
    $modo='cambio_estado';
    $titulo='Marcar puesto como disponible';
    $tipo_scan="limpieza";
@endphp

@section('content')
        @include('scan.inc_scan')
        @include('puestos.content_mapa')
        
        
@endsection


@section('scripts')
    <script>
        var tooltip = $('.add-tooltip');
        if (tooltip.length)tooltip.tooltip();

        $('.limpieza').addClass('active active-sub');
        $('.pendientes').addClass('active');

        function post_procesado(puesto){
            $('puesto'+puesto).hide();
        }

        $('.sp_edificio').hide();
    </script>
@endsection

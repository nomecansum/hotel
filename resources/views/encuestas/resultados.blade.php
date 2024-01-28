@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Resultados de la encuesta</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Encuestas</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">resultados</a></li>
        <li class="breadcrumb-item active">{{ !empty($encuesta->titulo) ? $encuesta->titulo : '' }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">$encuesta->titulo</h3>
    </div>
    <div class="card-body">

    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.encuestas').addClass('active');
    </script>
@endsection

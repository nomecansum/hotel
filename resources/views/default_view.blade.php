@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de cámaras</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Titulo</h3>
    </div>
    <div class="card-body">

    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.ITEM_MENU').addClass('active');
    </script>
@endsection

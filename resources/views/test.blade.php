@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Pagina de prueba</h1>
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
       {{-- @php
             ini_set('max_execution_time', 300); //300 seconds = 5 minutes
           $icons=[];
           $json=file_get_contents(public_path('/plugins/fontawesome6/metadata/categories.json'));
           $json=json_decode($json);
           foreach($json as $cat){
                foreach($cat->icons as $icon){
                    $icons[]='fa-solid fa-'.$icon;
                    $icons[]='fa-regular fa-'.$icon;
                    $icons[]='fa-dutone fa-'.$icon;
                }
           }
           $icons=array_unique($icons);
           dd(json_encode(array_values($icons)));
       @endphp --}}


       @php
        dump(metodos_notificacion_usuario(2));
    @endphp
    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.ITEM_MENU').addClass('active');
    </script>
@endsection

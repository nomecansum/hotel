@extends('layout_simple')
@php
    $e= new \stdClass();
    $e->grande="error";
    $e->icono="fas fa-truck";
    $e->titulo="Método no permitido";
    $e->mensaje="Método no permitido";
    $e->enlace="/";
    $e->tit_enlace="Volver a inicio";
@endphp


@section('content')
    <div class="text-center">
        <h1 class="text-info" id="error_grande"><i class="{{$e->icono}}"></i>{{$e->grande}}</h1>
        <h3 class="text-uppercase"><{{$e->titulo}}/h3>
        <p class="text-muted mt-4 mb-4">{{$e->mensaje}}</p>
        <h6>{{ $mensaje_largo }}</h6>
        @if(isset($e->enlace))<a href="{{url($e->enlace)}}" class="btn btn-info btn-rounded waves-effect waves-light mb-5">{{$e->tit_enlace}}</a>@endif
    </div>
@endsection

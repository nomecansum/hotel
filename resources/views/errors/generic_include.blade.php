@extends('layout')

@section('content')
<div class="text-center">
    <h1 class="text-danger" id="error_grande"><i class="{{$e->icono}}"></i>{{$e->grande}}</h1>
    <h3 class="text-uppercase"><{{$e->titulo}}/h3>
    <p class="text-muted mt-4 mb-4">{{$e->mensaje}}</p>
    <h6>{{ $e->mensaje_largo }}</h6>
    @if(isset($e->enlace))<a href="{{url($e->enlace)}}" class="btn btn-info btn-rounded waves-effect waves-light mb-5">{{$e->tit_enlace}}</a>@endif
</div>
@endsection
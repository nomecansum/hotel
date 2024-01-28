@extends('layout')
@php
    use Carbon\Carbon;
@endphp
@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de salas de reuniones</h1>
@endsection

@section('styles')
<link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
<link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Parametrizacion</li>
        <li class="breadcrumb-item "><a href="{{url('/salas')}}">Estado de salas de reunion</a></li>
        <li class="breadcrumb-item active"><a href="{{url('/sala/'.$sala->token)}}">{{ nombrepuesto($sala) }}</a></li>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">@if(in_array(session('CL')['val_campo_puesto_mostrar'],['A','C'])) {{$sala->cod_puesto}}@endif - @if(in_array(session('CL')['val_campo_puesto_mostrar'],['A','C'])){{$sala->des_puesto}}@endif {!!beauty_fecha(Carbon::now(),0) !!}</h3>
    </div>
    <div class="card-body" id="detalles_reserva">
        @php
            $reserva_sala=$reservas->where('id_puesto',$sala->id_puesto);
        @endphp
    @include('salas.fill_sala')
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">¿Que quiere hacer?</h3>
    </div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-1">
     
            </div>
            <div class="col-md-4 mb-3">
                <div class="btn-group">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle btn-lg" data-toggle="dropdown" type="button" aria-expanded="false">
                            Utilizar la sala ahora <i class="dropdown-caret"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-right" style="">
                            <li ><a href="#" class="opt_reserva" data-ini="{{ Carbon::now()->format('H:i') }}" data-fin="{{ Carbon::now()->addMinutes(30)->format('H:i') }}" >30 minutos</a></li>
                            <li ><a href="#" class="opt_reserva"  data-ini="{{ Carbon::now()->format('H:i') }}" data-fin="{{ Carbon::now()->addMinutes(45)->format('H:i') }}" >45 minutos</a></li>
                            <li ><a href="#" class="opt_reserva"  data-ini="{{ Carbon::now()->format('H:i') }}" data-fin="{{ Carbon::now()->addMinutes(60)->format('H:i') }}" >1 hora</a></li>
                            <li ><a href="#" class="opt_reserva"  data-ini="{{ Carbon::now()->format('H:i') }}" data-fin="{{ Carbon::now()->addMinutes(90)->format('H:i') }}" >1 hora y 30 minutos</a></li>
                            <li ><a href="#" class="opt_reserva"  data-ini="{{ Carbon::now()->format('H:i') }}" data-fin="{{ Carbon::now()->addMinutes(120)->format('H:i') }}" >2 horas</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                
            </div>
            <div class="col-md-4 mb-3">
                <button class="btn btn-success btn-lg" id="btn_nueva_puesto">&nbsp;&nbsp;&nbsp; Crear una reserva &nbsp;&nbsp;&nbsp;</button> 
                
            </div>
        </div>
    </div>
</div>
@endsection


@section('scripts')
<script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
<script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.salas').addClass('active');

        $('#btn_nueva_puesto').click(function(){
            spshow('spin');
            $('#div_fechas').hide();
            $('#editorCAM').load("{{ url('/salas/crear_reserva/sala/'.$sala->id_puesto) }}", function(){
                animateCSS('#editorCAM','bounceInRight');
                sphide('spin');
                $('#titulo').html('Nueva reserva de sala');
            });
        });

        $('.opt_reserva').click(function(){
            $.post('{{url('reservas/save')}}', {_token: '{{csrf_token()}}',fechas:'{{ Carbon::now()->format("d/m/Y") }} - {{ Carbon::now()->format("d/m/Y") }}', id_cliente: '{{ Auth::user()->id_cliente }}',salas:'1',id_puesto: '{{ $sala->id_puesto }}', hora_inicio: $(this).data('ini'),hora_fin: $(this).data('fin'), des_puesto: '',tipo_vista:'comprobar',sala:'{{ $sala->id_puesto }}' }, function(data, textStatus, xhr) {
                $('#detalles_reserva').html(data);
            })
            .done(function(data) {
            console.log(data);
            if(data.error){
                mensaje_error_controlado(data);
            } else if(data.alert){
                mensaje_warning_controlado(data);
            } else{
                toast_ok(data.title,data.mensaje);
                setTimeout(function() {
                    location.reload();
                }, 2000);
            }
            
        })
        .fail(function(err) {
            mensaje_error_respuesta(err);
        })
        })
    </script>
    
@endsection

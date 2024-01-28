@extends('layout')
@php
    use Carbon\Carbon;
@endphp
@section('title')
    <h1 class="page-header text-overflow pad-no">Estado de salas de reunion</h1>
@endsection

@section('styles')
<style>
    .solo_icono{
        margin-top: 10px;
    }
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">espacios</li>
        <li class="breadcrumb-item active"><a href="{{url('/salas')}}">salas de reunion</a></li>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <form action="{{ url('/salas') }}" name="form_mapa" id="form_mapa" method="POST">
        {{ csrf_field() }}
        <div class="col-md-3">
            <div class="input-group float-right" id="div_fechas">
                <input type="text" class="form-control pull-left ml-1" id="fecha_ver" name="fecha_ver" value="{{ Carbon::now()->format('d/m/Y') }}">
                <span class="btn input-group-text btn-secondary btn_fecha"  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
            </div>
        </div>
    </form>
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Salas de reunion {!!beauty_fecha(Carbon::now(),0) !!}</h3>
    </div>
    <div class="card-body">
       @foreach($salas as $sala)
            @php
                $reserva_sala=$reservas->where('id_puesto',$sala->id_puesto);
            @endphp
            @include('salas.fill_sala')
       @endforeach
    </div>
</div>
@endsection


@section('scripts')
    <script>
        $('.mioficina').addClass('active active-sub');
        $('.salas').addClass('active');

        $('.btn_fecha').click(function(){
            picker.open('#fecha_ver');
        })

        const picker = MCDatepicker.create({
            el: "#fecha_ver",
            dateFormat: cal_formato_fecha,
            autoClose: true,
            closeOnBlur: true,
            firstWeekday: 1,
            customMonths: cal_meses,
            customWeekDays: cal_diassemana
        });

        picker.onSelect((date, formatedDate) => {
            $('#form_mapa').submit();
        });
    </script>
@endsection

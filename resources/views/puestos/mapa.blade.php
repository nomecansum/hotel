@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Mapa de espacios (mosaico)</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    {{-- <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet"> --}}
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item"><a href="{{url('/puestos')}}">Puestos</a></li>
        <li class="breadcrumb-item active">Mapa de puestos</li>
    </ol>
@endsection

@php
    $edificio_ahora=0;
    $planta_ahora=0;
@endphp

@section('content')
        <div class="row botones_accion mb-2">
            <div class="col-md-2">
                <form action="{{ url('puestos/mapa') }}" name="form_mapa" id="form_mapa" method="POST">
                    {{ csrf_field() }}
                    <div class="input-group float-right" id="div_fechas">
                        <input type="text" class="form-control pull-left" id="fecha_ver" name="fecha" value="{{isset($r->fecha)?$r->fecha:Carbon\Carbon::now()->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-secondary btn_fecha" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </form>
            </div>
            <div class="col-md-5">
                
            </div>
            <div class="col-md-2 text-end pt-4">
                <a href="#modal-leyenda " class="link-secondary btn_leyenda" data-bs-toggle="modal" data-bs-target="#modal-leyenda"><img src="{{ url("img/img_leyenda.png") }}"> LEYENDA</a>
            </div>
            <div class="col-md-3 text-end pt-4">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio3" data-href="lista" autocomplete="off">
                    <label class="btn btn-outline-light btn-xs boton_modo"  for="btnradio3"><i class="fad fa-list"></i> Lista</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" data-href="mapa" autocomplete="off"  checked="">
                    <label class="btn btn-outline-light btn-xs boton_modo"  for="btnradio1"><i class="fad fa-th"></i> Mosaico</label>
                    
                    <input type="radio" class="btn-check" name="btnradio" data-href="plano"  id="btnradio2" autocomplete="off" >
                    <label class="btn btn-outline-light btn-xs boton_modo" for="btnradio2"><i class="fad fa-map-marked-alt"></i> Plano</label>
                </div>
            </div>
            
        </div>
        @include('resources.leyenda_reservas')
        @include('puestos.content_mapa')
       

@endsection


@section('scripts')
    <script>


        $('.mioficina').addClass('active active-sub');
        $('.mapa').addClass('active');

        
        $('.btn_fecha').click(function(){
            console.log("click");
            picker.open('#fecha_ver');
        })


        const picker = MCDatepicker.create({
            el: "#fecha_ver",
            dateFormat: cal_formato_fecha,
            autoClose: true,
            closeOnBlur: true,
            firstWeekday: 1,
            disableWeekDays: cal_dias_deshabilitados,
            customMonths: cal_meses,
            customWeekDays: cal_diassemana
        });

        picker.onSelect((date, formatedDate) => {
            lafecha=moment(formatedDate,"DD/MM/YYYY").format('YYYY-MM-DD');
            $('#form_mapa').submit();
        });

        document.querySelectorAll(".btn-check").forEach(item => 
            item.addEventListener("click", () => {
                window.location.href = item.getAttribute("data-href");
        }));


    </script>
@endsection

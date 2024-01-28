@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Mapa de puestos (plano)</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .container {
            border: 1px solid #DDDDDD;
            width: 100%;
            position: relative;
            padding: 0px 0px 0px 0px !important;
            margin: 0px 0px 0px 0px !important;
            --bs-gutter-x: 0 !important;
            --bs-gutter-y: 0 !important;
        }
        .flpuesto {
            float: left;
            position: absolute;
            z-index: 1000;
            font-size: 9px;
            width: 40px;
            height: 40px;
            overflow: hidden;
            cursor: default;
        }
        .glow {
            background-color: #1c87c9;
            border: none;
            color: #eeeeee;
            cursor: pointer;
            display: inline-block;
            font-family: sans-serif;
            font-size: 20px;
            padding: 13px 10px;
            text-align: center;
            text-decoration: none;
            opacity: 1;
        }
        @keyframes glowing {
            0% {
            background-color: #2ba805;
            box-shadow: 0 0 5px #2ba805;
            }
            50% {
            background-color: #49e819;
            box-shadow: 0 0 20px #49e819;
            }
            100% {
            background-color: #2ba805;
            box-shadow: 0 0 5px #2ba805;
            }
        }
        .glow {
            animation: glowing 1300ms infinite;
        }

        .card_plano{
            --bs-gutter-x: 0;
            --bs-gutter-y: 0;
            padding: 0px 0px 0px 0px;
            margin: 0px 0px 0px 0px;
        }
        
    </style>
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
    use App\Models\plantas;

@endphp

@section('content')
        <div class="row botones_accion mb-2">
            <div class="col-md-3">
                <form action="{{ url('puestos/plano') }}" name="form_mapa" id="form_mapa" method="POST">
                    {{ csrf_field() }}
                    <div class="input-group float-right" id="div_fechas">
                        <input type="text" class="form-control pull-left" id="fecha_ver" name="fecha" style="width: 100px" value="{{isset($r->fecha)?$r->fecha:Carbon\Carbon::now()->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-secondary" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </form>
            </div>
            <div class="col-md-4">
                
            </div>
            <div class="col-md-2 text-end pt-4">
                <a href="#modal-leyenda " class="link-light" data-bs-toggle="modal" data-bs-target="#modal-leyenda"><img src="{{ url("img/img_leyenda.png") }}"> LEYENDA</a>
            </div>
            <div class="col-md-3 text-end pt-4">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio3" data-href="lista" autocomplete="off">
                    <label class="btn btn-outline-light btn-xs boton_modo"  for="btnradio3"><i class="fad fa-list"></i> Lista</label>

                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" data-href="mapa" autocomplete="off"  >
                    <label class="btn btn-outline-light btn-xs boton_modo"  for="btnradio1"><i class="fad fa-th"></i> Mosaico</label>
                    
                    <input type="radio" class="btn-check" name="btnradio" data-href="plano"  id="btnradio2" autocomplete="off" checked="">
                    <label class="btn btn-outline-light btn-xs boton_modo" for="btnradio2"><i class="fad fa-map-marked-alt"></i> Plano</label>
                </div>
            </div>
        </div>
   
        @foreach ($edificios as $e)
        <div class="card">
            <div class="card-header bg-gray-dark">
                <div class="row">
                    <div class="col-md-5">
                        <span class="fs-2 ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}</span>
                    </div>
                    <div class="col-md-5"></div>
                    <div class="col-md-2 text-end">
                        <h4 class="text-white">
                            <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $e->plantas }}</span>
                            <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $e->puestos }}</span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    $plantas=plantas::where('id_edificio',$e->id_edificio)->get();
                    if(isset($cualpuesto)){ //Esto es para el caso de que queramos filtrar para mostrar la ubicacion de un puesto
                        $plantas=$plantas->where('id_planta',$cualpuesto->id_planta);
                    }
                @endphp
                @foreach($plantas as $pl)
                    <div class="card border-dark mb-3">
                        <div class="card-header bg-gray">
                            <h3 >{{ $pl->des_planta }}</h3>
                        </div>
                        <div class="card-body overflow-auto card_plano">
                            @include('puestos.fill-plano')
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach
        @include('resources.leyenda_reservas')

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

        window.addEventListener('resize', () => { recolocar_puestos(); });

        window.addEventListener('load', (event) => {
            setTimeout(() => {
                recolocar_puestos();
            }, 300);
        });

        document.querySelectorAll(".btn-check").forEach(item => 
            item.addEventListener("click", () => {
                window.location.href = item.getAttribute("data-href");
        }));

        document.querySelectorAll(".nav-toggler").forEach(item => 
            item.addEventListener("click", () => {
                setTimeout(() => {
                    recolocar_puestos();
                }, 300);
        }));


    </script>
@endsection

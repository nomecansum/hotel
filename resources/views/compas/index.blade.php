@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Mis compañeros</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <style type="text/css">
        .container {
            border: 1px solid #DDDDDD;
            width: 100%;
            position: relative;
            padding: 0px;
        }
        .flpuesto {
            float: left;
            position: absolute;
            z-index: 1000;
            color: #FFFFFF;
            font-size: 9px;
            width: 40px;
            height: 40px;
            overflow: hidden;
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

        .letras_imagen {
            position: absolute;
            top: 20%;
            left: 40%;
            color: #fff; 
            -webkit-text-stroke: 1px #2e0edf;
            font-weight: bold;
            z-index: 1000;
        }
        
    </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item"><a href="{{url('/puestos/mapa')}}">Mi oficina</a></li>
        <li class="breadcrumb-item active">Ubicacion de mis compañeros</li>
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
                <form action="{{ url('puestos/compas') }}" name="form_mapa" id="form_mapa" method="POST">
                    {{ csrf_field() }}
                    <div class="input-group float-right" id="div_fechas">
                        <input type="text" class="form-control pull-left" id="fecha_ver" name="fecha" style="width: 100px" value="{{isset($r->fecha)?$r->fecha:Carbon\Carbon::now()->format('d/m/Y') }}">
                        <span class="btn input-group-text btn-secondary" disabled  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
                    </div>
                </form>
            </div>
            <div class="col-md-8">
                
            </div>
            
        </div>
        @if(isset($puestos_mostrar) && count($puestos_mostrar)==0)
            <div class="col-md-12 alert alert-warning">
                No hay informacion para mostrar
            </div>
        @endif
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
                        <div class="card-body">
                            @include('puestos.fill_usuarios_puestos')
                        </div>
                    </div>

                    
                @endforeach
            </div>
        </div>
        @endforeach
@endsection


@section('scripts')
    <script>
        $('.parametrizacion').addClass('active active-sub');
        $('.compas').addClass('active');


        

        
        $(function(){
            $(window).resize(function(){
                recolocar_puestos();
            })

        })
        
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
            $('#form_mapa').submit();
        });

        window.addEventListener('load', (event) => {
            setTimeout(() => {
                recolocar_puestos();
            }, 300);
        });

        document.querySelectorAll(".nav-toggler").forEach(item => 
            item.addEventListener("click", () => {
                setTimeout(() => {
                    recolocar_puestos();
                }, 300);
        }));


    </script>
@endsection

@extends('layout')

@section('title')

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
        .blink_me {
            animation: blinker 1s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
        
    </style>
@endsection

@section('breadcrumb')

@endsection

@section('content')
@php
    $cookie=Cookie::get('encuesta');
@endphp
    
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-4 text-center text-white">
            @if(isset($puesto) && ($puesto->img_logo!=null))
            <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$puesto->img_logo) }}" style="width: 13vw" alt="" onerror="this.src='{{ url('/img/logo.png') }}';">
            @else
            <h2>{{ $puesto->nom_cliente??'' }}</h2>
            @endif
        </div>
        <div class="col-md-4"></div>
    </div>
    <div class="row" style="margin-top: 70px">
        <div class="col-md-12 text-center">
            @if(isset($puesto))
            <div class="pad-all text-center font-bold" style="color: {{ $puesto->val_color }}; font-size: 22px">
               @if(config('app.env')=='local') [{{ $puesto->id_puesto }}] @endif<i class="fa-duotone fa-building"></i> {{ $puesto->des_edificio }} <i class="fa-duotone fa-layer-group"></i> {{ $puesto->des_planta }}  <i class="{{ $puesto->val_icono }}"></i>  {{ nombrepuesto($puesto) }}
            </div>
            @endif
        </div>
    </div>
    {{-- Formulario para que introduzca el pin y validar el acceso al puesto --}}
   <div class="row">
    <form method="POST" action="{{ url('id', $puesto->token) }}" id="form_acceso" name="form_acceso" accept-charset="UTF-8">
        {{csrf_field()}}
        <input type="hidden" name="id_puesto" value="{{ $puesto->token }}">
        <div class="row">
            <div class="form-group mx-auto {{ $errors->has('des_planta') ? 'has-error' : '' }}" style=" width: 300px">
                <label class="control-label"> Indique el PIN de acceso de su reserva</label>
                <div class="input-group float-right" id="div_fechas">
                    <input type="text" class="form-control pull-left ml-1 font-bold text-uppercase" name="pin" id="pin" maxlength="6" required style="font-size: 26px"  >
                    <button type="submit" class="btn input-group-text btn-primary btn_fecha"  style="height: 57px; font-size: 18px"><i class="fa-solid fa-square-check"></i> check</button>
                </div>
            </div>
        </div>
        <div class="text-danger font-18 text-center">
            @if($errors->any())
                {{ implode('', $errors->all(':message')) }}
            @endif
        </div>
    </form>
   </div>
    
    

    
           
    {{-- <div class="row">
        <div class="col-md-12 text-center mt-3">
            <a class="btn btn-lg btn-primary fs-2 rounded btn_otravez" href="{{ url('/scan_usuario/') }} "><i class="fad fa-qrcode fa-3x"></i> Escanear otra vez</a>
        </div>
    </div> --}}


    <div class="row mt-3" id="boton_home" style="display:none">
        <div class="col-md-12 text-center">
            <a class="btn btn-lg btn-secondary fs-2 rounded btn_home" href="{{ url('/') }} "><i class="fa fa-home"></i> Inicio</a>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        
    </script>
@endsection

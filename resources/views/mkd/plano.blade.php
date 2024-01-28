
@extends('layout_mkd')

@section('styles')

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
@section('content')
    @php
        $puestos_activos=$puestos->wherein('id_estado',[1,2,3])->count();
        $puestos_libres=$puestos->where('id_estado',1)->count();
        $puestos_asignados=$asignados_usuarios->count();
        $puestos_reservados=$reservas->where('id_estado',1)->count()+$puestos_asignados;
        $disponibles=$puestos_libres-$puestos_reservados;
        $ocupados=$puestos_activos-$disponibles;
        
        $pct_aforo=round(100*$ocupados/$puestos_activos);
        $pl=$plantas;
    @endphp
    <div class="row">
        <div class="col-md-2 text-center">
            @if(isset($cliente))
            <img src="{{ !empty($cliente->img_logo)?Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')): url('/img/logo.png') }}" style="width: 190px; margin-top: 5px">
            
            @endif
        </div>
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6"><span style="font-size: 40px">{{ $edificio->des_edificio }}</span></div>
                <div class="col-md-6"><span style="font-size: 40px">{{ $plantas->des_planta }}</span></div>
            </div>
            <div class="row">
                <div class="col-md-4 text-center fs-4" style="font-size: 20px">
                    Aforo<br>
                    <span id="activos" style="font-size: 40px; font-weight: bolder;">{{ $puestos_activos }}</span>
                </div>
                <div class="col-md-4 text-center fs-4 text-success" style="font-size: 20px">
                    Disponibles<br>
                    <span id="disponibles" style="font-size: 40px; font-weight: bolder;">{{ $disponibles }}</span>
                </div>
                <div class="col-md-4 text-center fs-2" style="color: LightCoral; text-shadow: 0 0 2px rgb(34, 41, 83); font-weight: bolder; font-size: 20px">
                    Reservados<br>
                    <span id="reservados" style="font-size: 40px; font-weight: bolder;">{{ $puestos_reservados }}</span>
                </div>
            </div>
        </div>
        
        <div class="col-md-2">
            <div class="text-center fs-2 b-all rounded bg-{{ color_porcentaje_inv($pct_aforo) }}" style="padding-top: 30px">
                Ocupacion<br>
                <span id="ocupacion" class="mt-3 text-white" style="font-size: 60px;font-weight: bolder;">{{ $pct_aforo }}%</span>
                
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2"></div>
        
        
    </div>
    <div class="card">
        <div class="card-body">
            @if(isset($pl->img_plano))
            {{--  {!! json_encode($pl->posiciones) !!}  --}}

                <div class="row container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
                    <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$pl->img_plano) }}" style="width: 100%" id="img_fondo{{ $pl->id_planta }}">
                    @php
                        $left=0;
                        $top=0;
                        $agent = new \Jenssegers\Agent\Agent;

                    @endphp
                    <div id="puestos">
                        @foreach($puestos as $puesto)
                            @php
                                $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
                                $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
                                if($puesto->puesto_width!=null || $puesto->puesto_height!=null || $puesto->border!=null || $puesto->font!=null  || $puesto->roundness!=null){
                                    $custom=true;
                                } else {
                                    $custom=false;
                                }
                                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"Mioficina",$r->fecha??Carbon\Carbon::now()->format('d/m/Y'));
                
                            @endphp
                            
                            @if(isset(session('CL')['modo_visualizacion_puestos']) && session('CL')['modo_visualizacion_puestos']=='C')
                            <div class="text-center add-tooltip  align-middle flpuesto puesto_parent draggable  {{ isset($cualpuesto)&&$cualpuesto->id_puesto==$puesto->id_puesto?'glow':'' }} " title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!! strip_tags($puesto->des_puesto." \r\n ".$cuadradito['title']) !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" data-width="{{ $puesto->puesto_width??0 }}" data-height="{{ $puesto->puesto_height??0 }}" style="height: {{ $puesto->factor_puestoh }}% ; width: {{ $puesto->factor_puestow }}%;top: {{ $top }}px; left: {{ $left }}px; background-color: {{ $cuadradito['color'] }}; {{ $cuadradito['borde'] }}; opacity: {{ $cuadradito['transp']}}; border-radius: {{ $cuadradito['border-radius'] }}">
                                <span class="h-100 align-middle text-center puesto_child" style="font-size: {{ $puesto->font!=null?$puesto->font:$puesto->factor_letra }}vw; ">
                                        {{ nombrepuesto($puesto) }}
                                        @include('resources.adornos_iconos_puesto')
                                </span>
                            </div>
                            @else
                            <div class="text-center add-tooltip align-middle flpuesto draggable puesto_parent {{ isset($cualpuesto)&&$cualpuesto->id_puesto==$puesto->id_puesto?'glow':'' }} " id="puesto{{ $puesto->id_puesto }}" title="{!! strip_tags( $puesto->cod_puesto." \r\n ".$cuadradito['title']) !!}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" data-width="{{ $puesto->puesto_width??0 }}" data-height="{{ $puesto->puesto_height??0 }}" style="height: {{ $puesto->factor_puestoh }}vh ; width: {{ $puesto->factor_puestow }}vw;top: {{ $top }}px; left: {{ $left }}px;color: {{ $cuadradito['font_color'] }}; {{ $cuadradito['borde'] }}; opacity: {{ $cuadradito['transp']  }}">
                                <span class="h-100 align-middle text-center puesto_child" style="font-size: {{ $puesto->font!=null?$puesto->font:$puesto->factor_letra }}vw; color:#FFF">
                                    <i class="{{ $puesto->icono_tipo }} fa-2x" style="color: {{ $puesto->color_tipo }}"></i><br>
                                    {{ nombrepuesto($puesto) }}</span>
                                {{--  @include('resources.adornos_iconos_puesto')  --}}
                            </div>
                            @endif
                            @php
                                $left+=50;
                                if($left==500){
                                    $left=0;
                                    $top+=50;
                                }
                            @endphp
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
@section('scripts')    
    <script>
        $('.form-ajax').submit(form_ajax_submit);
        try{
            posiciones={!! json_encode($pl->posiciones)??'[]' !!};
            //posiciones=JSON.parse(posiciones); 
        } catch($err){
            posiciones=[];
        }
        document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
       
        
        $(window).resize(function(){
            recolocar_puestos(posiciones);
        })

        $('.nav-toggler').click(function(){
            recolocar_puestos(posiciones);
        })

        $(function(){
            recolocar_puestos(posiciones);
        })
        function refrescar(){
            // console.log('Refrescar');
            // $('.flpuesto').remove();
            // $.get("{{ url('/MKD/plano/'.$planta.'/'.$token.'/refresh') }}", function(data){
            //     $('#puestos').html(data);
            // });  
            //recolocar_puestos(posiciones);
            //$('.flpuesto').remove();

        }

        setInterval(refrescar, 5000);
       
        
        
    </script>
@endsection
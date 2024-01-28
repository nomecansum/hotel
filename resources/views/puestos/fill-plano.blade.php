<div class="form-group col-md-4 mb-3">
    <label>Zoom: </label> <span id="puesto-zoom-def-val-{{ $pl->id_planta }}"></span>
    <div id="puesto-zoom-def-{{ $pl->id_planta }}"></div>	
</div>
@if(isset($pl->img_plano))
{{--  {!! json_encode($pl->posiciones) !!}  --}}

    <div class="container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$pl->img_plano) }}" style="width: 100%; opacity: {{ ($pl->factor_transp??100)/100 }}" id="img_fondo{{ $pl->id_planta }}">
        @php
            $left=0;
            $top=0;
            $puestos= DB::Table('puestos')
                ->select('puestos.*','puestos.width as puesto_width', 'puestos.height as puesto_height','plantas.*','estados_puestos.val_color as color_estado','estados_puestos.hex_color','estados_puestos.des_estado','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo','users.name as usuario_usando')
                ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
                ->leftjoin('users','puestos.id_usuario_usando','users.id')
                ->join('plantas','puestos.id_planta','plantas.id_planta')
                ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
                ->where('puestos.id_planta',$pl->id_planta)
                ->where('puestos.id_puesto','>',0)
                ->get();

            $agent = new \Jenssegers\Agent\Agent;

        @endphp
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
                $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"MKD",$r->fecha??Carbon\Carbon::now()->format('d/m/Y'));

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
    <script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
    <script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
    <script>

        try{
            posiciones={!! json_encode($pl->posiciones)??'[]' !!};
            //posiciones=JSON.parse(posiciones); 
        } catch($err){
            posiciones=[];
        }
        document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
       
        zoom_actual{{ $pl->id_planta }}=100;
        var hacer_zoom{{ $pl->id_planta }};

        function zoom{{ $pl->id_planta }}(){

            $('#plano{{ $pl->id_planta }}').animate({ 'zoom': zoom_actual{{ $pl->id_planta }}/100 }, 0);
            $('#plano{{ $pl->id_planta }}').animate({ 'width': zoom_actual{{ $pl->id_planta }}+'%' }, 0);
            recolocar_puestos();
        }
        

        var z_def{{ $pl->id_planta }} = document.getElementById('puesto-zoom-def-{{ $pl->id_planta }}');
        var z_def_value{{ $pl->id_planta }} = document.getElementById('puesto-zoom-def-val-{{ $pl->id_planta }}');

        noUiSlider.create(z_def{{ $pl->id_planta }},{
            start   : [ @mobile {{ Auth::user()->zoom_mobile??100 }} @else {{ Auth::user()->zoom_desktop??100 }} @endmobile ],
            connect : 'lower',
            step: 10,
            range   : {
                'min': [  20 ],
                'max': [ 500 ]
            },
            format: wNumb({
                decimals: 0
            }),
        });

        z_def{{ $pl->id_planta }}.noUiSlider.on('update', function( values, handle ) {
            z_def_value{{ $pl->id_planta }}.innerHTML = values[handle]+' %';
            zoom_actual{{ $pl->id_planta }}=values[handle];
            clearTimeout(hacer_zoom{{ $pl->id_planta }});
            fetch('{{ url('/users/setzoom') }}/'+{{ Auth::user()->id }}+'/'+values[handle]+'/'+{{ isMobile()?1:0 }})
                .then(data=>console.log(data));
            hacer_zoom{{ $pl->id_planta }}=setTimeout(() => {
                zoom{{ $pl->id_planta }}();
            }, 200);
        });


    </script>
@else
    <div id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        No hay plano de la planta
    </div>
@endif
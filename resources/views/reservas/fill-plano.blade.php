
@php
// dd($pl);   
@endphp
<div class="form-group col-md-4 mb-3">
    <label>Zoom: </label> <span id="puesto-zoom-def-val-{{ $pl->id_planta }}"></span>
    <div id="puesto-zoom-def-{{ $pl->id_planta }}"></div>	
</div>
{{-- <div class="viewport"></div> --}}
@if(isset($pl->img_plano))
{{--  {!! json_encode($pl->posiciones) !!}  --}}

    
    @php
        $left=0;
        $top=0;
        if(isset($puestos)){ //La lista de puestos a mostrar viene ya filtrada del controller
            $puestos_mostrar= $puestos->where('id_planta',$pl->id_planta);
        }


    @endphp
    
    @if($puestos_mostrar->count()>0)
   
    <div class="container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$pl->img_plano) }}" style="width: 100%; opacity: {{ ($pl->factor_transp??100)/100 }}" id="img_fondo{{ $pl->id_planta }}">
        @else
        {{-- <div class="row">
            <div class="col-md-1"></div><div class="col-md-4 bg-warning pad-all rounded font-20 font-bold v-middle"><i class="fad fa-info-square fa-2x"></i> No hay puestos disponibles</div> --}}
            <script>
                $('#planta{{ $pl->id_planta }}').hide();
            </script>
    @endif
    
    @foreach($puestos_mostrar as $puesto)
        @php
            $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();
            $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();
            $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();
            $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();
            $es_reserva="Reservas";
            $cuadradito=\App\Classes\colorPuestoRes::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,$es_reserva,$r->fechas);
            if($puesto->puesto_width!=null || $puesto->puesto_height!=null || $puesto->border!=null || $puesto->font!=null  || $puesto->roundness!=null){
                $custom=true;
            } else {
                $custom=false;
            }
        @endphp
        {{--  --}}
        <div class="text-center  add-tooltip align-middle flpuesto draggable puesto_parent {{ $cuadradito['clase_disp'] }} {{ $puesto->id_puesto==$id_puesto_edit?'disponible':'' }}  {{ $custom?'custom':'' }}" id="puesto{{ $puesto->id_puesto }}" title="{{ strip_tags( $puesto->cod_puesto." \r\n ".$cuadradito['title']) }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" data-factorh="{{ $puesto->factor_puestoh }}" data-factorw="{{$puesto->factor_puestow}}" data-factorr="{{ $puesto->roundness!=null?$puesto->roundness:$puesto->factor_puestor }}" data-width="{{ $puesto->puesto_width??0 }}" data-height="{{ $puesto->puesto_height??0 }}" style="height: {{ $puesto->factor_puestoh }}% ; width: {{ $puesto->factor_puestow }}%; top: {{ $top }}px; left: {{ $left }}px; background-color: {{ $cuadradito['color'] }}; @if(session('CL')['modo_visualizacion_puestos']=='C') color: {{ $cuadradito['font_color'] }} @endif; {{ $cuadradito['borde'] }}; opacity: {{ $cuadradito['transp']}}; border-radius: {{ $cuadradito['border-radius'] }}  ">
            @if(session('CL')['modo_visualizacion_puestos']=='C')
                <span class="h-100 align-middle text-center puesto_child {{ $puesto->font!=null?'custom':'' }}" style="font-size: {{ $puesto->font!=null?$puesto->font:$puesto->factor_letra }}vw; color:#666">{{ $puesto->des_puesto }}</span>
                @include('resources.adornos_iconos_puesto')
            @else
                <i class="{{ $puesto->icono_tipo }} fa-2x" style="color: {{ $puesto->color_tipo }}"></i><br>
                {{ $puesto->des_puesto }}</span>
            @endif
        </div>
    @php
        $left+=50;
        if($left==500){
            $left=0;
            $top+=50;
        }
     @endphp
    @endforeach

</div>
{{-- <div>fh: {{ $pl->factor_puestoh }} fw: {{ $pl->factor_puestow }}</div> --}}
<script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
<script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
<script>
    $.fn.scale = function(x) {
        if(!$(this).filter(':visible').length && x!=1)return $(this);
        if(!$(this).parent().hasClass('scaleContainer')){
            $(this).wrap($('<div class="scaleContainer">').css('position','relative'));
            $(this).data({
                'originalWidth':$(this).width(),
                'originalHeight':$(this).height()});
        }
        $(this).css({
            'transform': 'scale('+x+')',
            '-ms-transform': 'scale('+x+')',
            '-moz-transform': 'scale('+x+')',
            '-webkit-transform': 'scale('+x+')',
            'transform-origin': 'right bottom',
            '-ms-transform-origin': 'right bottom',
            '-moz-transform-origin': 'right bottom',
            '-webkit-transform-origin': 'right bottom',
            'position': 'absolute',
            'bottom': '0',
            'right': '0',
        });
        if(x==1)
            $(this).unwrap().css('position','static');else
                $(this).parent()
                    .width($(this).data('originalWidth')*x)
                    .height($(this).data('originalHeight')*x);
        return $(this);
    };

    var tooltip = $('.add-tooltip');
    if (tooltip.length)tooltip.tooltip();
    
    try{
        posiciones={!! json_encode($pl->posiciones)??'[]' !!};
    } catch($err){
        posiciones=[];
    }
    try{
        document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
    } catch($err){
       
    }

    zoom_actual{{ $pl->id_planta }}=100;
    var hacer_zoom{{ $pl->id_planta }};

    function zoom{{ $pl->id_planta }}(){
        $('#plano{{ $pl->id_planta }}').animate({ 'zoom': zoom_actual{{ $pl->id_planta }}/100 }, 0);
        $('#plano{{ $pl->id_planta }}').animate({ 'width': zoom_actual{{ $pl->id_planta }}+'%' }, 0);
        //$('#plano{{ $pl->id_planta }}').scale(zoom_actual{{ $pl->id_planta }}/100);
        setTimeout(() => {
            recolocar_puestos();
            console.log('zoom');
        }, 100);
       
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
        console.log("on update");
        z_def_value{{ $pl->id_planta }}.innerHTML = values[handle]+' %';
        zoom_actual{{ $pl->id_planta }}=values[handle];
        clearTimeout(hacer_zoom{{ $pl->id_planta }});
        fetch('{{ url('/users/setzoom') }}/'+{{ Auth::user()->id }}+'/'+values[handle]+'/'+{{ isMobile()?1:0 }})
            .then(data=>console.log(data));
        hacer_zoom{{ $pl->id_planta }}=setTimeout(() => {
            zoom{{ $pl->id_planta }}();
        }, 100);
    });

    
    $(function(){
        setTimeout(function(){
            zoom{{ $pl->id_planta }}();
        }, 400);
    });
   

    //document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
    //$('#plano{{ $pl->id_plano }}').data('posiciones',posiciones);
</script>
@else
    <div id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        No hay plano de la planta
    </div>
@endif
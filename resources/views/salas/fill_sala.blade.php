@php
    use Carbon\Carbon;
    if(!isset($reserva_sala)){
        $reserva_sala=[];
    }
    $tamano_iconos="1.4em";
    $tamano_letra="2.0em";
    $margin="1vw";
@endphp

<div class="row mb-4 {{ $link??'' }}" data-id="{{ $sala->id_puesto }}" data-desc="{{ $sala->des_puesto }}">
    <div class="col-md-2 text-center p-t-10">
        <span class="fs-4">{{ $sala->cod_puesto }}</span><br>
        {{ nombrepuesto($sala) }}
        @if(config('app.debug'))
        <br>[{{ $sala->id_puesto }}]
        @endif
    </div>
    <div class="col-md-2">
        <img src="{{ isset($sala) ? Storage::disk(config('app.img_disk'))->url('img/puestos/'.$sala->img_puesto) : ''}}" style="width: 90%;" alt="" class="img-fluid ml-0">
    </div>
    <div class="col-md-8">
        <div class="mt-2 mb-2 d-flex flex-row " style="margin-left: {{ $margin}};">
            <div class="add-tooltip" title="Capacidad {{$sala->val_capacidad}} personas" style="margin-right: {{ $margin}}"><i class="fad fa-user" style="font-size: {{ $tamano_iconos}};"></i><span class="font-bold" style="font-size: {{ $tamano_letra}}; margin-top: -10px">{{$sala->val_capacidad}}</span></div>
            <div class="add-tooltip" title="Maximo tiempo de reserva: {{round($sala->max_horas_reservar,1)}}h" style="margin-right: {{ $margin}}"><i class="fad fa-clock" style="font-size: {{ $tamano_iconos}}"></i><span class="font-bold" style="font-size: {{ $tamano_letra}}; margin-top: -10px">{{round($sala->max_horas_reservar,1)}}h</span></div>
            <div class="add-tooltip solo_icono" title="Proyector" style="margin-right: {{ $margin}}"><i class="fad fa-projector text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_proyector=='N'?'color:#eee':'' }}"></i></div>
            <div class="add-tooltip  solo_icono" title="Pantalla" style="margin-right: {{ $margin}}"><i class="fad fa-tv-alt  text-info"  style="font-size: {{ $tamano_iconos}};{{ $sala->mca_pantalla=='N'?'color:#eee':'' }}"></i></div>
            <div class="add-tooltip  solo_icono" title="Videoconferencia" style="margin-right: {{ $margin}}"><i class="fad fa-webcam  text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_videoconferencia=='N'?'color:#eee':'' }}"></i></div>
            <div class="add-tooltip  solo_icono" title="Manos libres" style="margin-right: {{ $margin}}"><i class="fad fa-volume-up  text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_manos_libres=='N'?'color:#eee':'' }}"></i></div>
            <div class="add-tooltip  solo_icono" title="Pizarra" style="margin-right: {{ $margin}}"><i class="fad fa-chalkboard  text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_pizarra=='N'?'color:#eee':'' }}"></i></div>
            <div class="add-tooltip  solo_icono" title="Pizarra digital" style="margin-right: {{ $margin}}"><i class="fad fa-chalkboard-teacher  text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_pizarra_digital=='N'?'color:#eee':'' }}"></i></div>
        </div>
        <div class="row">
            <div class="col-md-12">
                {{ $sala->obs_sala }}
            </div>
        </div>
        <div>
            <div style="border: 1px solid #999; width: 100%; height: 20px; border-radius: 3px; background-color: #ddf7c5; position: relative">
                @foreach($reserva_sala as $res)
                    <div class="add-tooltip" title="Reservada de {{ Carbon::parse($res->fec_reserva)->format('H:i') }} a {{ Carbon::parse($res->fec_fin_reserva)->format('H:i') }} @if(config_cliente('mca_mostrar_nombre_usando')=='S') por {{ $res->name }} @endif" 
                        style="background-color: {{ $res->id_usuario==Auth::user()->id?'#ffd700':'#cd5c5c' }};border-radius: 2px; position: absolute; height: 18px; width: {{ 100*(Carbon::parse($res->fec_fin_reserva)->diffinMinutes(Carbon::parse($res->fec_reserva)))/1440 }}%; left: {{ 100*(Carbon::parse($res->fec_reserva)->secondsSinceMidnight()/60)/1440 }}%">
                    </div>
                @endforeach
            </div>
            
           
        </div>
        <div class="nowrap d-flex flex-row">
            @for($n=0;$n<=23;$n++)
                <div class="text-nowrap" style="width: {{ 100/24 }}%; height: 10px; font-size: 10px; color: #ccc; border-left: 1px solid #efefef; text-align: center; padding-left: 5px">{{ $n }}</div>
            @endfor
        </div>
    </div>
</div>
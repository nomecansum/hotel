
@extends('layout_mkd')

@section('styles')
<link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
<style type="text/css">
   
    
</style>
@endsection
@section('content')
    @php
        use Carbon\Carbon;
        $tamano_iconos="1.8em";
        $tamano_letra="2.0em";
        $margin="2vw";
    @endphp
    <div class="row">
        <div class="col-md-12 text-center">
            <img src="{{ Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.session('logo_cliente')) }}" style="width: 13vw" alt="" onerror="this.src='{{ config('app.url_asset_mail').'/img/Mosaic_brand_300.png' }}';">
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center font-bold text-3x">
            {{ nombrepuesto($sala) }}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <img src="{{ isset($sala) ? Storage::disk(config('app.img_disk'))->url('img/puestos/'.$sala->img_puesto) : ''}}" style="width: 90%;" alt="" class="img-fluid ml-0">
        </div>
    </div>
    <div class="row text-center mb-3 mt-3" style="padding-left: 30px">
        <div class="d-flex flex-wrap  add-tooltip" title="Capacidad" style="margin-right: {{ $margin}}"><i class="fad fa-users" style="font-size: {{ $tamano_iconos}};"></i><span class="font-bold" style="font-size: {{ $tamano_letra}}; margin-top: -10px">{{$sala->val_capacidad}}</span></div>
            <div class="d-flex flex-wrap  add-tooltip" title="Maximo tiempo de reserva" style="margin-right: {{ $margin}}"><i class="fad fa-clock" style="font-size: {{ $tamano_iconos}}"></i><span class="font-bold" style="font-size: {{ $tamano_letra}}; margin-top: -10px">{{$sala->max_horas_reservar}}h</span></div>
            <div class="d-flex flex-wrap  add-tooltip" title="Proyector" style="margin-right: {{ $margin}}"><i class="fad fa-projector text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_proyector=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap  add-tooltip" title="Pantalla" style="margin-right: {{ $margin}}"><i class="fad fa-tv-alt  text-info"  style="font-size: {{ $tamano_iconos}};{{ $sala->mca_pantalla=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap  add-tooltip" title="Videoconferencia" style="margin-right: {{ $margin}}"><i class="fad fa-webcam  text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_videoconferencia=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap  add-tooltip" title="Manos libres" style="margin-right: {{ $margin}}"><i class="fad fa-volume-up  text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_manos_libres=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap  add-tooltip" title="Pizarra" style="margin-right: {{ $margin}}"><i class="fad fa-chalkboard  text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_pizarra=='N'?'color:#eee':'' }}"></i></div>
            <div class="d-flex flex-wrap  add-tooltip" title="Pizarra digital" style="margin-right: {{ $margin}}"><i class="fad fa-chalkboard-teacher  text-info" style="font-size: {{ $tamano_iconos}};{{ $sala->mca_pizarra_digital=='N'?'color:#eee':'' }}"></i></div>
    </div>
    <div class="row">
        <div class="col-md-1">
        </div>
        <div class="col-md-10">
            <div class="row" >
                <div style="border: 1px solid #999; width: 100%; height: 40px; border-radius: 3px; background-color: #ddf7c5">
                    @foreach($reservas as $res)
                        <div class="add-tooltip" title="Reservada de {{ Carbon::parse($res->fec_reserva)->format('H:i') }} a {{ Carbon::parse($res->fec_fin_reserva)->format('H:i') }} @if(config_cliente('mca_mostrar_nombre_usando')=='S') por {{ $res->name }} @endif" 
                            style="background-color: {{ Auth::check() && $res->id_usuario==Auth::user()->id?'#ffd700':'#cd5c5c' }};border-radius: 2px; position: absolute; height: 38px; width: {{ 100*(Carbon::parse($res->fec_fin_reserva)->diffinMinutes(Carbon::parse($res->fec_reserva)))/1440 }}%; left: {{ 100*(Carbon::parse($res->fec_reserva)->secondsSinceMidnight()/60)/1440 }}%">
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="row nowrap"  >
                @for($n=0;$n<=23;$n++)
                    <div style="width: {{ 100/24 }}%; height: 10px; font-size: 10px; color: #ccc; border-left: 1px solid #ccc; text-align: center; padding-left: 5px">{{ $n }}</div>
                @endfor
               
            </div>
        </div>
    </div>
    <div class="row">
        <div class=" col-md-12 mt-3 text-center mb-5">
            @if(config('app.debug'))
                <a href="{{ str_replace('puesto','sala',config('app.url_base_scan')).$sala->token }}">
            @endif
            <img class="qr" src="data:image/png;base64, {!! base64_encode(QrCode::format('png')->size(300)->merge('/public/img/logo.png', .2)->generate(str_replace('puesto','sala',config('app.url_base_scan')).$sala->token)) !!} ">
            {{--  {{config('app.url_base_scan').$puesto->token}}  --}}
            @if(config('app.debug'))
                </a>
            @endif
        </div>
    </div>
    
@endsection
@section('scripts')    
    <script>
        
        
    </script>
@endsection
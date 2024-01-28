@php

 if(!isset($tipo_ronda))
    {
        $tipo_ronda='M';
    }
    
 $rondas=DB::table('rondas_limpieza')
    ->join('limpiadores_ronda','limpiadores_ronda.id_ronda','rondas_limpieza.id_ronda')
    ->join('users as u1','rondas_limpieza.user_creado','u1.id')
    ->join('users as u2','limpiadores_ronda.id_limpiador','u2.id')
    ->select('fec_ronda','des_ronda','rondas_limpieza.id_ronda','u1.name as user_creado')
    ->selectraw("group_concat(u2.name SEPARATOR '#') as user_asignado") 
    ->where(function($q){
        if (!isAdmin()) {
            $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
        } else {
            $q->where('rondas_limpieza.id_cliente',session('CL')['id_cliente']);
        }
    })
    ->where(function($q){
        if (Auth::user()->nivel_acceso==10) {  //Personal de limpieza
            $q->where('limpiadores_ronda.id_limpiador',Auth::user()->id);
        }
    })
    ->where('tip_ronda',$tipo_ronda)
    ->groupby('rondas_limpieza.id_ronda','fec_ronda','des_ronda','u1.name')
    ->orderby('id_ronda','desc')
    ->get();

    $detalles=DB::table('rondas_limpieza')
        ->select('puestos_ronda.*','puestos.cod_puesto','puestos.id_edificio','puestos.id_planta','puestos.id_estado')
        ->join('puestos_ronda','puestos_ronda.id_ronda','rondas_limpieza.id_ronda')
        ->join('puestos','puestos_ronda.id_puesto','puestos.id_puesto')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('rondas_limpieza.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->where('tip_ronda',$tipo_ronda)
        ->get();

@endphp

<style type="text/css">
.pr.around {
    width: 70px;
    height: 70px;
    position: relative;
    display: inline-block;
}
.pr.around span {
    color: navy;
}
.pr.around span.outer {
    position: absolute;
    left: 0;
    top: 0;
    width: 70px;
    text-align: center;
    font-size: 10px;
    padding: 15px 0;
}
.pr.around span.value {
    font-size: 25px;
}
</style>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><span class="font-bold fs-2" id="cuenta">{{ $rondas->count() }}</span> Rondas de {{ $tipo_ronda=='M'?'mantenimiento':'limpieza' }} con algun puesto pendiente {!! beauty_fecha(Carbon\Carbon::now()->Settimezone(Auth::user()->val_timezone)) !!}</h3>
    </div>
    <div class="card-body">
        @foreach($rondas as $r)
            @php
                $cnt_edificios=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_edificio')->unique()->count();
                $cnt_plantas=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_planta')->unique()->count();
                $cnt_puestos=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_puesto')->unique()->count();
                $puestos_si=$detalles->where('id_ronda',$r->id_ronda)->wherenotnull('fec_fin')->count();
                $puestos_no=$detalles->where('id_ronda',$r->id_ronda)->wherenull('fec_fin')->count();
                try{
                    $pct_completado=(100*$puestos_si/$cnt_puestos);
                } catch(\Exception $e){
                    $pct_completado=0;
                }
                $cuenta=0;
                
            @endphp
            @if($pct_completado<100)
                <div class="row">
                    <div class="col-md-2 fs-2 text-center">
                        {!! beauty_fecha($r->fec_ronda,0) !!}
                    </div>
                    <div class="col-md-2 text-3x font-bold text-center text-{{ color_porcentaje($pct_completado) }}">
                        <span class="pr around" data-color="{{ color_porcentaje($pct_completado,'hex') }}"><span class="outer"><span class="value">{{ round($pct_completado) }}</span><br>%</span></span>
                    </div>
                    
                    <div class="col-md-2  text-center">
                        <i class="fad fa-building fs-2"></i> <span class="text-3x ml-2"> {{ $cnt_edificios }}</span>
                    </div>
                    <div class="col-md-2  text-center">
                        <i class="fad fa-layer-group  fs-2"></i> <span class="text-3x ml-2"> {{ $cnt_plantas }}</span>
                    </div>
                    <div class="col-md-2  text-center">
                        <i class="fad fa-desktop-alt  fs-2"></i> <span class="text-3x ml-2"> {{ $cnt_puestos }}</span>
                    </div>
                    <div class="col-md-2 font-16  text-center">
                        {{ $r->des_ronda }}
                    </div>
                    
                </div>
                @php
                    $cuenta++;   
                @endphp
            @endif
        @endforeach

        

    </div>
</div>
@section('scripts5')
<script type="text/javascript" src="{{ url('plugins/Dynamic-Pie-Chart/js/min/jquery-progresspiesvg-min.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function(){ 
        $('#cuenta').html("{{ $cuenta??0 }}");
    }, false);


    $(".pr.around").progressPie({
        size: 70,
        ringWidth: 7,
        strokeWidth: 0,
        ringEndsRounded: true,
        valueSelector: "span.value",
        color: $(this).data('color'),
    });
</script>
@endsection

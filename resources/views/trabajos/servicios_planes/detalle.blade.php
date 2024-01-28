@php
    use Carbon\Carbon;
    use App\Http\Controllers\TrabajosController;
    $fecha_ant=Carbon::parse($fecha->format('Y-m-d'))->startOfMonth()->subHour(2)->format('Y-m-d');
    $fecha_sig=Carbon::parse($fecha->format('Y-m-d'))->endOfMonth()->addDay()->startOfMonth()->format('Y-m-d');
    $ultimo_dia=$fecha->endOfMonth()->format('d');
    $meses = ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
    $hoy=Carbon::now();
    $mes_anio=Carbon::parse($fecha)->format('Y-m').'-';

    
@endphp


<div class="card editor mb-5">

    <div class="card-header toolbar">
        <div class="toolbar-start">
            <h5 class="m-0">Ver plan</h5>
        </div>
        <div class="toolbar-end">
            <button type="button" class="btn-close btn-close-card">
                <span class="visually-hidden">Close the card</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <div class="">
            <table class="table table-bordered" style="font-size: 10px">
                <thead>
                   
                    <tr>
                        <th><a data-month="{{ $fecha_ant }}" data-action="sub" class="changeMonth" style="float:left; font-size: 18px; cursor: pointer;"> <i class="fas fa-arrow-alt-left"></i> </a></th>
                        <th colspan="{{ $ultimo_dia+1 }}" class="text-center">
                            <h4>{{trans('strings.'.$meses[Carbon::parse($fecha)->format('n')-1]).' '. ucwords(Carbon::parse($fecha)->format('Y')) }}</h4>
                        </th>
                        <th><a data-month="{{ $fecha_sig }}" data-action="add" class="changeMonth" style="float:right; font-size: 18px; cursor: pointer;"> <i class="fas fa-arrow-alt-right"></i> </a></th>
                    </tr>
                    <tr>
                        <th scope="col"  colspan="2" class="text-center">Trabajos</th>
                        <th scope="col"  class="text-center">Espacios</th>
                        @for ($n=1;$n<=$ultimo_dia;$n++)
                            <th style="font-weight: normal; width: 15px; background-color: #eee;" class="text-center">{{ lz($n,2) }}</th>
                        @endfor
                    </tr>
                </thead>
                <tbody>
                    @foreach($grupos as $grupo)
                        @php
                            $trabajos_grupo=$trabajos->where('id_grupo',$grupo->id_grupo);
                            if($grupo->fec_inicio==null && $grupo->fec_fin==null){
                                $in_time=true;
                            }
                            $fec_ini_grupo=$grupo->fec_inicio!=null?Carbon::parse(Carbon::parse($fecha)->format('Y').'-'.Carbon::parse($grupo->fec_inicio)->format('m-d')):null;
                            $fec_fin_grupo=$grupo->fec_inicio!=null?Carbon::parse(Carbon::parse($fecha)->format('Y').'-'.Carbon::parse($grupo->fec_fin)->format('m-d')):null;
                        @endphp
                        @foreach($trabajos_grupo as $trabajo)
                            <tr>
                                @if($loop->index==0)
                                    <td rowspan="{{ $trabajos_grupo->count()*($plantas->count()+$zonas->count()) }}" class="text-center align-middle" style="vertical-align: middle; padding: 10px 0px 10px 0px; background-color: {{ $grupo->val_color }}"><span class="vertical text-center {{ txt_blanco($grupo->val_color) }}"> {{ $grupo->des_grupo }}</span></td>
                                @endif
                                <td scope="col" rowspan={{ $plantas->count()+$zonas->count() }} class="{{ txt_blanco($trabajo->val_color) }}" style="background-color:{{ $trabajo->val_color }}; padding-top: 6em"><span class="vertical text-center{{ txt_blanco($trabajo->val_color) }}" style="vertical-align: middle"><i class="{{ $trabajo->val_icono }}"></i> {{ $trabajo->des_trabajo }}</span></td>
                                @foreach($plantas as $planta)
                                    @if($loop->index==0)
                                        <td nowrap>{{ $planta->des_planta }}</td>
                                        @for ($n=1;$n<=$ultimo_dia;$n++)
                                            @php
                                                $fecha=Carbon::parse($mes_anio.lz($n,2));
                                                $tarea=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_planta',$planta->id_planta)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
                                                if($tarea){
                                                    $tarea->fec_ini_grupo=$fec_ini_grupo;
                                                    $tarea->fec_fin_grupo=$fec_fin_grupo;
                                                    $tarea->fec_ini_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_inicio):null;
                                                    $tarea->fec_fin_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_fin):null;
                                                }
                                                $programa=$programaciones->where('id_trabajo_plan',$tarea->key_id??0)->where('fecha_corta',$mes_anio.lz($n,2))->first();
                                                $datos_celda=TrabajosController::celda_plan_trabajos($tarea,$programa,$hoy,$fecha);
                                            @endphp
                                            <td class="{{ $datos_celda['color']??'' }} text-center td_planta" title="{{ $datos_celda['title'] }}"  data-programacion="{{ $programa->id_programacion??0 }}" data-trabajo={{ $programa->id_trabajo_plan??'0' }} data-fecha="{{ $fecha->format('Y-m-d') }}" data-desc="#{{ $programa->id_programacion??'' }} {{ $trabajo->des_trabajo }} en {{ $planta->des_planta}} el {{beauty_fecha($fecha)}}">
                                                <i class="{{ $datos_celda['icono'] }}"></i>
                                            </td>
                                        @endfor
                                    @endif
                                @endforeach
                            </tr>
                            @foreach($plantas as $planta)
                                @if($loop->index!=0)
                                    <tr>
                                        <td nowrap>{{ $planta->des_planta }}</td>
                                        @for ($n=1;$n<=$ultimo_dia;$n++)
                                            @php
                                                $fecha=Carbon::parse($mes_anio.lz($n,2));
                                                $tarea=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_planta',$planta->id_planta)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
                                                if($tarea){
                                                    $tarea->fec_ini_grupo=$fec_ini_grupo;
                                                    $tarea->fec_fin_grupo=$fec_fin_grupo;
                                                    $tarea->fec_ini_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_inicio):null;
                                                    $tarea->fec_fin_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_fin):null;
                                                }
                                                $programa=$programaciones->where('id_trabajo_plan',$tarea->key_id??0)->where('fecha_corta',$mes_anio.lz($n,2))->first();
                                                $datos_celda=TrabajosController::celda_plan_trabajos($tarea,$programa,$hoy,$fecha);
                                            @endphp
                                            <td class="{{ $datos_celda['color']??'' }} text-center td_planta" title="{{ $datos_celda['title'] }}"   data-programacion="{{ $programa->id_programacion??0 }}" data-trabajo={{ $programa->id_trabajo_plan??'0' }} data-fecha="{{ $fecha->format('Y-m-d') }}" data-desc="#{{ $programa->id_programacion??'' }} {{ $trabajo->des_trabajo }} en {{$planta->des_planta}} el {{beauty_fecha($fecha)}}">
                                                <i class="{{ $datos_celda['icono'] }}"></i>
                                            </td>
                                        @endfor
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                @foreach($zonas as $zona)
                                    @if($loop->index==0)
                                        <td nowrap>[{{ $zona->des_planta }}] {{ $zona->des_zona }}</td>
                                        @for ($n=1;$n<=$ultimo_dia;$n++)
                                            @php
                                                $fecha=Carbon::parse($mes_anio.lz($n,2));
                                                $tarea=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_zona',$zona->key_id)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
                                                if($tarea){
                                                    $tarea->fec_ini_grupo=$fec_ini_grupo;
                                                    $tarea->fec_fin_grupo=$fec_fin_grupo;
                                                    $tarea->fec_ini_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_inicio):null;
                                                    $tarea->fec_fin_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_fin):null;
                                                }
                                                $programa=$programaciones->where('id_trabajo_plan',$tarea->key_id??0)->where('fecha_corta',$mes_anio.lz($n,2))->first();
                                                $datos_celda=TrabajosController::celda_plan_trabajos($tarea,$programa,$hoy,$fecha);
                                            @endphp
                                            <td class="{{ $datos_celda['color']??'' }} text-center td_planta" title="{{ $datos_celda['title'] }}"   data-programacion="{{ $programa->id_programacion??0 }}" data-trabajo={{ $programa->id_trabajo_plan??'0' }} data-fecha="{{ $fecha->format('Y-m-d') }}" data-desc="#{{ $programa->id_programacion??'' }} {{ $trabajo->des_trabajo }} en [{{ $zona->des_planta }}] {{ $zona->des_zona }} el {{beauty_fecha($fecha)}}">
                                                <i class="{{ $datos_celda['icono'] }}"></i>
                                            </td>
                                        @endfor
                                    @endif
                                @endforeach
                            </tr>
                            @foreach($zonas as $zona)
                                @if($loop->index!=0)
                                    <tr>
                                        <td nowrap>[{{ $zona->des_planta }}] {{ $zona->des_zona }}</td>
                                        @for ($n=1;$n<=$ultimo_dia;$n++)
                                            @php
                                                $fecha=Carbon::parse($mes_anio.lz($n,2));
                                                $tarea=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_zona',$zona->key_id)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
                                                if($tarea){
                                                    $tarea->fec_ini_grupo=$fec_ini_grupo;
                                                    $tarea->fec_fin_grupo=$fec_fin_grupo;
                                                    $tarea->fec_ini_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_inicio):null;
                                                    $tarea->fec_fin_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_fin):null;
                                                }
                                                $programa=$programaciones->where('id_trabajo_plan',$tarea->key_id??0)->where('fecha_corta',$mes_anio.lz($n,2))->first();
                                                $datos_celda=TrabajosController::celda_plan_trabajos($tarea,$programa,$hoy,$fecha);
                                            @endphp
                                            <td class="{{ $datos_celda['color']??'' }} text-center td_planta" title="{{ $datos_celda['title'] }}"   data-programacion="{{ $programa->id_programacion??0 }}" data-trabajo={{ $programa->id_trabajo_plan??'0' }} data-fecha="{{ $fecha->format('Y-m-d') }}" data-desc="#{{ $programa->id_programacion??'' }} {{ $trabajo->des_trabajo }} en [{{ $zona->des_planta }}] {{ $zona->des_zona }} el {{beauty_fecha($fecha)}}">
                                                <i class="{{ $datos_celda['icono'] }}"></i>
                                            </td>
                                        @endfor
                                    </tr>
                                @endif
                            @endforeach
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
 
<div class="modal fade" id="detalle-trabajo" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header">
                
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h3 class="modal-title text-nowrap">Detalle: <span id="desc_detalle">#</span></h3>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body" id="detalle_modal">
                
            </div>
            <div class="modal-footer">
                {{-- <a class="btn btn-info" id="btn_si_detalle" href="javascript:void(0)">Si</a> --}}
                <button type="button" id="btn_no_detalle" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cerrar</button>
            </div>
        </div>
    </div>
</div>


<script>
var grupo_periodo;
var trabajo_periodo;

function loadMes(fecha){
    $.ajax({
        url: "{{url('trabajos/planificacion/ver')}}/{{ $id }}/"+fecha,
        type: "GET",
        success: function(data){
            $('#editorCAM').html(data);
        }
    });
}

$('.changeMonth').click(function(){
    loadMes($(this).data('month'),'');
})

$('.td_planta').click(function(){
    var programa=$(this).data('programacion');
    var trabajo=$(this).data('trabajo');
    var fecha=$(this).data('fecha');
    var desc=$(this).data('desc');
    $.post('{{url('/trabajos/servicios/detalle_trabajo')}}', {_token:'{{csrf_token()}}',programa:programa,trabajo:trabajo,fecha:fecha}, function(data, textStatus, xhr) {
        $('.modal-body').empty();
        $('#detalle_modal').html(data);
        $('#desc_detalle').html(desc);
        $('#detalle-trabajo').modal('show');
    });
});

document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

</script>
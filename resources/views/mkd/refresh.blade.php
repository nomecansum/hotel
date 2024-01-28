@php
    $puestos_activos=$puestos->wherein('id_estado',[1,2,3])->count();
    $puestos_libres=$puestos->where('id_estado',1)->count();
    $puestos_asignados=$asignados_usuarios->count();
    $puestos_reservados=$reservas->where('id_estado',1)->count()+$puestos_asignados;
    $disponibles=$puestos_libres-$puestos_reservados;
    $ocupados=$puestos_activos-$disponibles;

    $pct_aforo=round(100*$ocupados/$puestos_activos);
    $pl=$plantas;
    $posiciones=json_decode($plantas->posiciones);
$agent = new \Jenssegers\Agent\Agent;

@endphp
@foreach($puestos as $puesto)
    @php
        $left=0;
        $top=0;
        $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();   
        $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
        $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
        $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
        $cuadradito=\App\Classes\colorPuesto::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"Refresh",Carbon\Carbon::now()->format('d/m/Y'));
        foreach($posiciones as $pos){
            if($pos->id==$puesto->id_puesto){
                $left=$pos->offsetleft;
                $top=$pos->offsettop;
            }
        }
    @endphp
   
    <div class="text-center rounded add-tooltip bg-{{ $puesto->color_estado }} align-middle flpuesto puesto_parent draggable" title="@if(isadmin()) #{{ $puesto->id_puesto }} @endif {!! nombrepuesto($puesto)." \r\n ".$cuadradito['title'] !!}" id="puesto{{ $puesto->id_puesto }}" data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" style="height: {{ $puesto->factor_puesto }}vw ; width: {{ $puesto->factor_puesto }}vw;{{ $cuadradito['borde'] }}">
        <span class="h-100 align-middle text-center puesto_child" style="font-size: {{ $puesto->factor_letra }}vw; ">
                {{ $puesto->cod_puesto }}
                @include('resources.adornos_iconos_puesto')
        </span>
    </div>
    <script>
        puesto=$('#puesto{{ $puesto->id_puesto }}');
        puesto.css('top',plano.height()*item.offsettop/100);
        puesto.css('left',plano.width()*item.offsetleft/100);
    </script>

@endforeach
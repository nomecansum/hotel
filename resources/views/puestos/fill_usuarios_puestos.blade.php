@if(isset($pl->img_plano))
    @php
        $left=0;
        $top=0;
        if(isset($puestos)){ //La lista de puestos a mostrar viene ya filtrada del controller
            $puestos_mostrar=$puestos->pluck('id_puesto')->toArray();
        }
        
        $puestos= DB::Table('puestos')
            ->select('puestos.*','plantas.*','estados_puestos.val_color as color_estado','estados_puestos.hex_color','estados_puestos.des_estado', 'puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->where('puestos.id_planta',$pl->id_planta)
            ->when(isset($puestos_mostrar), function($q) use($puestos_mostrar){
                $q->wherein('puestos.id_puesto',$puestos_mostrar);
            })
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->where(function($q){
                if(!checkPermissions(['Mostrar puestos no reservables'],['R'])){
                    $q->where('puestos.mca_reservar','S');
                }
            })
            ->get();
    @endphp
    
    @if($puestos->count()>0)
    <div class="row container" id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$pl->img_plano) }}" style="width: 100%" id="img_fondo{{ $pl->id_planta }}">
        @else
        <div class="row">
            <div class="col-md-1"></div><div class="col-md-4 bg-warning pad-all rounded font-20 font-bold v-middle"><i class="fad fa-info-square fa-2x"></i> No hay puestos disponibles</div>
    @endif
    
    @foreach($puestos as $puesto)
        @php
            $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
            $usuario=$reservas->where('id_puesto',$puesto->id_puesto)->union($asignados_usuarios->where('id_puesto',$puesto->id_puesto))->first();
            // $puesto->factor_puestow=$puesto->factor_puestow+1;
            // $puesto->factor_puestoh=$puesto->factor_puestoh+1;
        @endphp
        <div class="text-center  add-tooltip align-middle flpuesto puesto_parent"  id="puesto{{ $puesto->id_puesto }}"  data-id="{{ $puesto->id_puesto }}" data-puesto="{{ $puesto->cod_puesto }}" data-planta="{{ $pl->id_planta }}" style="height: {{ $puesto->factor_puestow }}vw ; width: {{ $puesto->factor_puestow }}vw;top: {{ $top }}px; left: {{ $left }}px; ">
            <span class="h-100 align-middle text-center puesto_child" style="font-size: {{ $puesto->factor_letra }}vw; ; color:#666">
                @if (isset($usuario->img_usuario ) && $usuario->img_usuario!='')
                    <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$usuario->img_usuario) }}" title="{{ $usuario->name }}" class="rounded-circle add-tooltip" style="height:{{ $puesto->factor_puestow*1.3 }}em; width:{{ $puesto->factor_puestow*1.3 }}em;">
                    <div class=" letras_imagen" style="font-size: {{ $puesto->factor_letra*2 }}vw">{{ iniciales($usuario->name,2) }}</div>
                @else
                    {!! icono_nombre($usuario->name) !!}
                @endif
            </span>
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
<script>
        try{
            posiciones={!! json_encode($pl->posiciones)??'[]' !!};
        } catch($err){
            posiciones=[];
        }

        try{
            document.getElementById('plano{{ $pl->id_planta }}').setAttribute("data-posiciones", posiciones);
        } catch($err){
        
        }
</script>
@else
    <div id="plano{{ $pl->id_planta }}" data-posiciones="" data-id="{{ $pl->id_planta }}">
        No hay plano de la planta
    </div>
@endif


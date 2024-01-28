<select class="form-control" id="id_puesto" name="id_puesto">
    <option value="" ></option>
    @php
        $puestos=DB::table('puestos')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();
        $planta=0;
        $edificio=0;	
    @endphp
    @foreach ($puestos as $puesto)
        @if($edificio!= $puesto->id_edificio)
            <optgroup label="{{ $puesto->des_edificio }}"></optgroup>
            @php $edificio=$puesto->id_edificio @endphp
        @endif
        @if($planta!= $puesto->id_planta)
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<optgroup label="{{ $puesto->des_planta }}"></optgroup>
            @php $planta=$puesto->id_planta @endphp
        @endif
        <option value="{{ $puesto->id_puesto }}" >
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ nombrepuesto($puesto) }}
        </option>
    @endforeach
</select>


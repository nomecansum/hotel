@php
 $incidencias=DB::table('incidencias')
    ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
    ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
    ->join('edificios','puestos.id_edificio','edificios.id_edificio')
    ->join('plantas','puestos.id_planta','plantas.id_planta')
    ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
    ->join('clientes','puestos.id_cliente','clientes.id_cliente')
    ->where(function($q){
        if (!isAdmin()) {
            $q->where('puestos.id_cliente',Auth::user()->id_cliente);
        } else {
            $q->where('puestos.id_cliente',session('CL')['id_cliente']);
        }
    })
    ->wherenull('fec_cierre')
    ->get();

    $datos_quesito=DB::table('incidencias')
        ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
        ->selectraw('des_tipo_incidencia as des_estado, count(id_incidencia) as cuenta')
        ->groupby('des_tipo_incidencia')
        ->get();
        
@endphp

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><span class="font-bold fs-2">{{ $incidencias->count() }}</span> Incidencias abiertas a {!! beauty_fecha(Carbon\Carbon::now()->Settimezone(Auth::user()->val_timezone)) !!}</h3>
    </div>
    <div class="card-body">
        <table id="tabla"  
            data-toggle="table" data-mobile-responsive="true"
            data-locale="es-ES"
            data-search="false"
            data-show-columns="false"
            data-show-columns-toggle-all="true"
            data-page-list="[5, 50, 500]"
            data-page-size="5"
            data-pagination="true" 
            data-show-pagination-switch="true"
            data-show-button-icons="true"
            data-show-footer="false"
            >
            <thead>
                <tr>
                    <th data-sortable="true">Id</th>
                    <th data-sortable="true">Puesto</th>
                    <th data-sortable="true">Edificio</th>
                    <th data-sortable="true">Planta</th>
                    <th data-sortable="true">Fecha</th>
                    <th data-sortable="true">Tipo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($incidencias as $inc)
                    <tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
                        <td>{{$inc->id_incidencia}}</td>
                        
                        <td>{{ nombrepuesto($inc) }}</td>
                        <td>{{ $inc->des_edificio}}</td>
                        <td>{{ $inc->des_planta}}</td>
                        <td>{!! beauty_fecha($inc->fec_apertura)!!}</td>
                        <td>{{$inc->des_tipo_incidencia}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
</div>
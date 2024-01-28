

@include('home.accesos_directos')

@include('home.4_kpi')

{{-- @include('home.puesto_asignado') --}}

<div class="row">
    <div class="col-md-6">
        @include('home.kpi_grafico_puestos')
        @if(checkPermissions(['Incidencias'],['R']))
            @include('home.incidencias_abiertas')
        @endif
    </div>
    <div class="col-md-6">
        @if(checkPermissions(['Reservas'],['R']))
            @include('home.calendario')
        @endif
    </div>
</div>
@if(checkPermissions(['Incidencias'],['R']))
    @include('home.tabla_incidencias')
@endif

@include('home.rondas_pendientes')

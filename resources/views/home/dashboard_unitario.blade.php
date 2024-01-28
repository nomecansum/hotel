@include('home.accesos_directos')
@include('home.puesto_asignado')
@if(checkPermissions(['Reservas puestos'],['R']) || checkPermissions(['Reservas salas'],['R']))
    @include('home.calendario')
@endif



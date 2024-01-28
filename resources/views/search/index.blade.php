@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Resultados de busqueda</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item active">buscar "{{ !empty($r->txt_buscar) ? $r->txt_buscar : '' }}"</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="row">
@if($clientes->count()>0 && checkPermissions(['Clientes'],["R"]))
<div class="col-md-4">
    <div class="card mt-3">
        <div class="card-header">
            <h4 onclick="$('#bloque_clientes').toggle();"><i class="fa-solid fa-user-tie"></i> Empresas [{{ $clientes->count() }}]</h4>
        </div>
        <div class="card-body p-0 overflow-scroll scrollable-content" style="height: 300px" id="bloque_clientes">
            <ul>
            @foreach($clientes as $cliente)
                <li><a href="{{ url('/target/cutomers/'.$cliente->id_cliente).'/'.$cliente->nom_cliente }}">
                    @if(isset($cliente->img_logo) && $cliente->img_logo<>'')
                        <img src="{{Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$cliente->img_logo)}}" width="40px" alt="">
                    @endif
                    {{ $cliente->nom_cliente }}</a></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@if($usuarios->count()>0 && checkPermissions(['Usuarios'],["R"]))
<div class="col-md-4">
    <div class="card mt-3">
        <div class="card-header">
            <h4 onclick="$('#bloque_empleados').toggle();"><i class="fa-solid fa-user"></i> Usuarios [{{ $usuarios->count() }}]</h4>
        </div>
        <div class="card-body p-0 overflow-scroll scrollable-content" style="height: 300px" id="bloque_empleados">
            <ul>
            @foreach($usuarios as $usuario)
                <li style="margin: 5 0 5 0"><a href="{{ url('/target/users/'.$usuario->id).'/'.$usuario->name }}">
                    @if (isset($users->img_usuario ) && $users->img_usuario!='')
                        <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$usuario->img_usuario) }}" class="img-md rounded-circle" style="height:40px; width:40px; object-fit: cover;">
                    @else
                        {!! icono_nombre($usuario->name,40,14) !!}
                    @endif
                    {{ $usuario->name }} <span class="font-14 text-muted">{{ count(clientes())>1?"[".$usuario->nom_cliente."]":"" }}</a></span></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@if($edificios->count()>0 && checkPermissions(['Edificios'],["R"]))
<div class="col-md-4">
    <div class="card mt-3">
        <div class="card-header">
            <h4 onclick="$('#bloque_edificios').toggle();"><i class="fa-solid fa-building"></i> Edificios [{{ $edificios->count() }}]</h4>
        </div>
        <div class="card-body p-0 overflow-scroll scrollable-content" style="height: 300px" id="bloque_edificios">
            <ul >
            @foreach($edificios as $edificio)
                <li style="margin: 5 0 5 0"><a href="{{ url('/target/edificios/'.$edificio->id_edificio).'/'.$edificio->des_edificio }}">
                    {{ $edificio->des_edificio }} <span class="font-14 text-muted">{{ count(clientes())>1?"[".$edificio->nom_cliente."]":"" }}</a></span></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@if($plantas->count()>0 && checkPermissions(['Plantas'],["R"]))
<div class="col-md-4">
    <div class="card mt-3">
        <div class="card-header">
            <h4 onclick="$('#bloque_plantas').toggle();"><i class="fa-solid fa-layer-group"></i> Plantas [{{ $plantas->count() }}]</h4>
        </div>
        <div class="card-body p-0 overflow-scroll scrollable-content" style="height: 300px" id="bloque_plantas" >
            <ul >
            @foreach($plantas as $planta)
                <li style="margin: 5 0 5 0"><a href="{{ url('/target/plantas/'.$planta->id_planta).'/'.$planta->des_planta }}">
                    {{ $planta->des_planta }} <span class="font-14 text-muted">{{ count(clientes())>1?"[".$planta->nom_cliente."]":"" }}</a></span></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@if($puestos->count()>0 && checkPermissions(['Puestos'],["R"]))
<div class="col-md-4">
    <div class="card mt-3">
        <div class="card-header">
            <h4 onclick="$('#bloque_plantas').toggle();"><i class="fa-solid fa-desktop-alt"></i> Puestos [{{ $puestos->count() }}]</h4>
        </div>
        <div class="card-body p-0 overflow-scroll scrollable-content" style="height: 300px" id="bloque_plantas" >
            <ul >
            @foreach($puestos as $puesto)
                <li style="margin: 5 0 5 0"><a href="{{ url('/target/puestos/'.$puesto->id_puesto).'/'.$puesto->cod_puesto }}">
                    {{ $puesto->cod_puesto }} <i class="fa-solid fa-arrow-right"></i> {{ $puesto->des_puesto }} <span class="font-14 text-muted">{{ count(clientes())>1?"[".$puesto->nom_cliente."]":"" }}</a></span></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
@if($incidencias->count()>0 && checkPermissions(['Incidencias'],["R"]))
<div class="col-md-4">
    <div class="card mt-3">
        <div class="card-header">
            <h4 onclick="$('#bloque_plantas').toggle();"><i class="fa-solid fa-exclamation-triangle"></i> Incidencias [{{ $incidencias->count() }}]</h4>
        </div>
        <div class="card-body p-0 overflow-scroll scrollable-content" style="height: 300px" id="bloque_plantas" >
            <ul >
            @foreach($incidencias as $incidencia)
                <li style="margin: 5 0 5 0"><a href="{{ url('/target/incidencias/'.$incidencia->id_incidencia).'/'.$incidencia->des_incidencia }}" title="{{ $incidencia->des_incidencia }}" class="add-tooltip">
                    {!! beauty_fecha($incidencia->fec_apertura) !!} <i class="fa-solid fa-arrow-right"></i> {{ substr($incidencia->des_incidencia,0,50) }}... <span class="font-14 text-muted">{{ count(clientes())>1?"[".$incidencia->nom_cliente."]":"" }}</a></span></li>
            @endforeach
            </ul>
        </div>
    </div>
</div>
@endif
</div>
@endsection


@section('scripts')
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.ITEM_MENU').addClass('active');
    </script>
@endsection

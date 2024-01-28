
@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Incidencia de puesto</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Ccnfiguracion</li>
        <li class="breadcrumb-item"><a href="{{url('/users')}}">mantenimiento</a></li>
        <li class="breadcrumb-item active">Incidencia de puesto {{ $puesto->cod_puesto }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Incidencia de puesto {{ $puesto->cod_puesto }}</h3>
    </div>
    <div class="card-body">
        @if($incidencia)
            @include('incidencias.fill-detalle-incidencia',compact('incidencia'))
            <div class="col-md-12 text-center mt-3">
                <a class="btn btn-lg btn-success fs-2 rounded btn_cerrar" href="#cerrar-incidencia" title="Cerrar incidencia" data-toggle="modal" class="btn btn-xs btn-success add-tooltip btn-cierre" data-id="{{ $incidencia->id_incidencia}}" id="boton-cierre{{ $incidencia->id_incidencia }}" onclick="cierre_incidencia({{ $incidencia->id_incidencia}})"><i class="fad fa-thumbs-up fa-2x"></i></i> Cerrar incidencia</a>
            </div>
        @else
            <h3>El puesto no tiene incidencias</h3>
            <div class="rounded btn-lg font-20 bg-{{ $puesto->val_color }}">{{ $puesto->des_estado }}</div>
        @endif
    </div>
</div>
<div class="row">
    <div class="col-md-12 text-center mt-3">
        <a class="btn btn-lg btn-primary fs-2 rounded btn_otravez" href="{{ url('/scan_mantenimiento/') }} "><i class="fad fa-qrcode fa-3x"></i> Escanear otra vez</a>
    </div>
</div>

@if($incidencia)
<div class="modal fade" id="cerrar-incidencia">
	<form method="POST" action="{{ url('/incidencias/cerrar') }}" accept-charset="UTF-8" class="form-horizontal form-ajax">	
		@csrf
		<div class="modal-dialog modal-md">
			<div class="modal-content"><div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
				<div class="modal-header"><i class="mdi mdi-thumb-up text-success mdi-48px"></i><b>
					Cerrar incidencia {{ $incidencia->des_incidencia}}
				</div>
				<div class="modal-body" id="body_cierre">
					
				</div>
				<div class="modal-footer">
					<button class="btn btn-info btn_cerrar_incidencia">Si</button>
					<button type="button" data-dismiss="modal" class="btn btn-warning">Cancelar</button>
				</div>
			</div>
		</div>
	</form>
</div>
@endif

@endsection


@section('scripts')
    <script>
        $('.mantenimiento').addClass('active active-sub');
        $('.scan').addClass('active');

        function cierre_incidencia(id){
            $('#body_cierre').load("{{ url('/incidencias/form_cierre/') }}/"+id);
        }

        function post_form_ajax(data){
            console.log(data);
            window.location.replace("{{ url('/scan_mantenimiento') }}");
        }

    </script>
@endsection



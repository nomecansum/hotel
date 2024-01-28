@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Informe estado de usuarios</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
		<li class="breadcrumb-item">Informes</li>
        <li class="breadcrumb-item">Estado de usuarios</li>
    </ol>
@endsection
@section('content')
@php
	$total = 0;
	//Solo para depuracion
	use Carbon\Carbon;
	if(config('app.manolo') == 1){
		$f1=Carbon::parse("2020-09-01");
		$f2=Carbon::parse("2020-09-31");
	}
	$controller="reports";//Este es el nombre de metodo a ejecutar para la programacion de informes
@endphp
<div class="card">
	<div class="card-header">
		<h3 class="card-title">Informe de usuarios</h3>
		<span class="float-right" id="spin" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
	</div>
	<div class="card-body">
		@if (checkPermissions(['Informes'],["R"]))
			<form action="{{url('/reports/estado_usu/filter')}}" method="POST" class="ajax-filter">
				{{csrf_field()}}
				<input type="hidden" value="{{Auth::user()->id_cliente}}" name="id_cliente">
				@include('resources.combos_filtro',[$hide=['est'=>1,'head'=>1,'btn'=>1,'est_inc'=>1,'tip_mark'=>1,'tip_inc'=>1,'tip_feria'=>1,'tip'=>0,'pue'=>1,'tag'=>1],$show=['u_id'=>1,'tur'=>1]])
				<div class="col-md-2 mb-3">
					@include('resources.combo_fechas',['singleMode'=>true])
				</div>

				
				<div class="col-12">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label>Mostrar</label>
								<select name="mostrar" class="form-control">
									<option value="T">Todo</option>
									<option value="E">Solo errores</option>
									<option value="C">Solo correctos</option>

								</select>
							</div>
						</div>
					</div>
				</div>
				@include('resources.combos_opciones_informes',[$show=['output'=>1,'orientation'=>1]])
				<div class="row">
					<div class="col-md-12 text-end">
						<button id="btn_submit" class="btn btn-primary btn-lg mb-2 mr-2" style="margin-top:10px"><i class="fa-solid fa-file-lines"></i> Obtener informe</button>
					</div>
				</div>
			</form>
		@endif
	</div>
</div>
@include('resources.programacion_informe')
<br>
<div class="table m-t-40 overflow-hidden table-vcenter">

	@include('resources.informes_imprimir_resumen')
	<table class="table table-hover table-sm" style="font-size: 12px; background-color: #fff">
		<tbody id="myFilter" >
		</tbody>
	</table>
</div>
@php
    $nombre_empresa = "Informe de de usuarios" . " ";
    $___cl = \DB::table('clientes')->where('id_cliente',Auth::user()->id_cliente)->first();
    if(isset($___cl) && ($___cl->nom_cliente))
        $nombre_empresa .= $___cl->nom_cliente;
    else $nombre_empresa .= "SPOTDESKING";
@endphp
@endsection

@section('scripts4')

<script>
	document.title = '{{$nombre_empresa}}';
	$('.informes').addClass('active active-sub');
    $('.inf_estado_usuarios').addClass('active');

</script>
@endsection

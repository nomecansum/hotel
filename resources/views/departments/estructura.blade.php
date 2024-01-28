

@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Departamentos</h1>
@endsection

@section('styles')
<style type="text/css">
	.round{
		line-height: 40px !important;
	}
	.noborders td {
		border:0;
	}
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuracion</li>
		<li class="breadcrumb-item">parametrizacion</li>
		<li class="breadcrumb-item">personas</li>
        <li class="breadcrumb-item active">estructura organizativa</li>
    </ol>
@endsection

@section('content')

<div class="card">
    <div class="card-header">
		<div class="row botones_accion">
			<div class="col-md-6">
				<span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
			</div>
			<div class="col-md-4 text-end">
				<select name="id_edificio" id="id_edificio" class="form-control">
					@foreach($edificios as $edificio)
						<option value="{{$edificio->id_edificio}}" {{ $edificio->id_edificio==$seleccionado ? 'selected' : '' }}>{{$edificio->des_edificio}}</option>
					@endforeach
				</select>
				
			</div>
			<div class="col-md-2 text-end">
				<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
					<input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked="">
					<label class="btn btn-outline-primary btn-xs boton_modo" data-href="departments" for="btnradio1"><i class="fa-light fa-list-timeline"></i> Listado</label>
					
					<input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
					<label class="btn btn-outline-primary btn-xs boton_modo" data-href="departments/estructura" for="btnradio2"><i class="fa-regular fa-list-tree"></i> Estructura</label>
				</div>
			</div>
		</div>
    </div>
    <div class="card-body">
		<ul id="listas-usuarios">
			@foreach ($edificios->where('id_edificio',$seleccionado) as $cen)
				<li class="clickable" style="font-size:20px">
					<label style="font-size: 22px;">
						<i class="mdi mdi-minus-box"></i>
						<i class="mdi mdi-store icon-box" style="color:cornflowerblue"></i>
						 {{$cen->des_edificio}}
					</label>
					<ul>
						@php
							$departamentos=DB::table('departamentos')
								->select('departamentos.cod_departamento','departamentos.nom_departamento')
								->selectraw($cen->id_edificio.' as id_edificio')
								->wherenull('departamentos.cod_departamento_padre')
								->orderBy('nom_departamento','asc')
								->get();
						@endphp
						@each('departments.fill_fila_departamento_estructura', $departamentos, 'dep','departments.fill_fila_departamento_final')
					</ul>
				</li>
			@endforeach
		</ul>
    </div>
</div>


@endsection


@section('scripts')
    <script>
        $('.configuracion').addClass('active active-sub');
		$('.menu_parametrizacion').addClass('active active-sub');
		$('.menu_usuarios').addClass('active active-sub');
        $('.departamentos').addClass('active');


		$('#id_edificio').change(function(){
			$('#loadfilter').show();
			var id_edificio=$(this).val();
			var url="{{url('departments/estructura')}}/"+id_edificio;
			window.location.href=url;
		});
		

		$('.boton_modo').click(function(){
			window.location.href="{{ url('/') }}/"+$(this).attr('data-href');
		});
    </script>
@endsection

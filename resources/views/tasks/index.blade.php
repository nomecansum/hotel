@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Tareas programadas</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
	<ol class="breadcrumb">
		<li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
		<li class="breadcrumb-item">Configuracion</li>
		<li class="breadcrumb-item">Utilidades</li>
		<li class="breadcrumb-item active"><a href="{{url('/tasks')}}">tareas programadas</a></li>
		{{-- <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li> --}}
	</ol>
@endsection

@section('content')
<div class="row botones_accion">
	<div class="col-md-4">

	</div>
	<div class="col-md-6">
		<br>
	</div>
	<div class="col-md-2 text-end">
		@if(checkPermissions(['Tareas programadas'],['C']))
		<div class="btn-group btn-group-sm pull-right" role="group">
				<a  href="{{url('tasks/create')}}" id="btn_nueva_puesto" class="btn btn-success" title="Nueva tarea">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nueva</span>
			</a>
		</div>
		@endif
	</div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Tareas programadas</h3>
    </div>
    <div class="card-body">
		<div class="table-responsive m-t-40">
			<table id="myTable"
				data-toggle="table" 
                data-mobile-responsive="true"
                data-locale="es-ES"
                data-search="true"
                data-show-columns="true"
                data-show-toggle="true"
                data-show-columns-toggle-all="true"
                data-page-list="[5, 10, 20, 30, 40, 50, 75, 100]"
                data-page-size="50"
                data-pagination="true" 
                data-toolbar="#all_toolbar"
                data-buttons-class="secondary"
                data-show-button-text="true"
				>
				<thead>
					<tr>
						<th></th>
						<th>ID</th>
						<th>Descripcion</th>
						<th>Comando</th>
						<th style="width:5%">{{__('general.timeout')}}</th>
						<th>{{__('general.intervalo')}}</th>
						<th style="width: 8%" class="txt_nowrap">{{__('general.dia_semana')}}</th>
						<th style="width: 7%">{{__('general.horas')}}</th>
						<th style="width: 15%">{{__('general.clientes')}}</th>
						<th style="width: 15%">{{__('tareas.last_run')}}</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($tareas as $t)
						<tr class="hover-this" data-href="{{url('tasks/edit',$t->cod_tarea)}}">
							<td class="align-middle text-center"><i style="color:{{ $t->val_color }}" class="fa {{ $t->val_icono }} fa-2x"></i></td>
							<td class="align-middle text-center">{{ $t->cod_tarea}}</td>
							<td style="background-color: {{ $t->val_color }}" class="{{ txt_blanco($t->val_color) }} align-middle">{{$t->des_tarea}}</td>
							<td class="align-middle">{{ str_replace(".php","",str_replace("_"," ",basename($t->nom_comando))) }}</td>
							<td class="align-middle">{{$t->val_timeout}}</td>
							<td class="align-middle">
								@switch($t->val_intervalo)
									@case("hourlyAt")
										hourlyAt min :{{ $t->det_minuto }}
									@break

									@case("dailyAt")
										dailyAt {{ Carbon\Carbon::parse($t->det_horaminuto)->format('H:i') }}
									@break

									@case("weeklyOn")
										weeklyOn {{ dayOfWeek($t->det_diasemana) }} @ {{ Carbon\Carbon::parse($t->det_horaminuto)->format('H:i') }}
									@break

									@case("monthlyOn")
										monthlyOn {{ $t->det_diames }}th @ {{ Carbon\Carbon::parse($t->det_horaminuto)->format('H:i') }}
									@break
									@default
										{{ $t->val_intervalo }}
									@break
								@endswitch

							</td>
							<td>
								@php
									$dias=array_filter(explode(',',$t->dias_semana));
								@endphp
								@foreach ($dias as $d)
										<li>{{$d}}</li>
								@endforeach
							</td>
							<td class="align-middle">
								{{ Carbon\Carbon::createFromTimeString("$t->hora_inicio")->format('H:i') }} <i class="mdi mdi-arrow-right-bold"></i> {{ Carbon\Carbon::createFromTimeString("$t->hora_fin")->format('H:i') }}
							</td>
							<td style="font-size: 14px">
								@php
									$clientes=array_filter(explode(',',$t->clientes));
								@endphp
								@foreach ($clientes as $c)
										<li>{{ DB::table('clientes')->where('id_cliente',$c)->value('nom_cliente')}}</li>
								@endforeach
							</td>
							<td style="position: relative;" class="align-middle">
								{!! beauty_fecha($t->fec_ult_ejecucion)!!}
								<div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
									<div class="btn-group btn-group pull-right ml-1" role="group">
										<a href="javascript:void(0)" class="btn btn-xs btn-warning btn_run" onclick="run({{$t->cod_tarea}},'{{$t->des_tarea}}')" data-id="{{ $t->cod_tarea }}"><i class="fas fa-play"></i> {{__('tareas.ejecutar')}}</a>
										<a href="{{url('tasks/edit',$t->cod_tarea)}}" class="btn btn-xs btn-info"><i class="fas fa-pencil"></i> {{__('general.edit')}}</a>
										<a href="#eliminar-tarea-{{$t->cod_tarea}}" onclick="del({{$t->cod_tarea}})" data-toggle="modal" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i> {{__('general.delete')}}</a>
									</div>
								</div>
								<div class="modal fade" id="eliminar-tarea-{{$t->cod_tarea}}">
									<div class="modal-dialog modal-sm">
										<div class="modal-content p-2">
											<div class="modal-header">
												<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
												<h1 class="modal-title text-nowrap">Borrar tarea </h1>
												<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
													<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
												</button>
											</div>    
											<div class="modal-body">
												{{__('tareas.desea_eliminar_tarea')}} {{ $t->des_tarea }}?
											</div>
											<div class="modal-footer">
												<a class="btn btn-info" href="{{url('tasks/delete',$t->cod_tarea)}}">{{__('general.yes')}}</a>
												<button type="button" data-dismiss="modal" class="btn btn-warning" onclick="cerrar_modal()" >{{__('general.cancelar')}}</button>
											</div>
										</div>
									</div>
								</div>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Cronjobs</h3>
    </div>
    <div class="card-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header bg-primary text-white">
						<i class="mdi mdi-camera-timer mdi-24px"></i> {{__('tareas.estado_programacion_tareas')}}
					</div>
					<div class="card-body">
						<table class="table table-bordered table-condensed table-hover">
			                <thead>
			                    <tr>
			                        <th>ID</th>
			                        <th>{{__('tareas.comando')}}</th>
									<th>{{__('tareas.ultima_ejec_programada')}}</th>
									<th>{{__('tareas.ultima_ejec_real')}}</th>
									<th>{{__('tareas.prox_ejec')}}</th>
									<th>Cron</th>
			                    </tr>
			                </thead>
			                <tbody>
			                	@foreach ($crons as $cron)
									<tr class="hover-this fila_tarea" data-id="{{$cron->id_tarea}}">
										<td class="text-center">{{$cron->id_tarea}}</td>
										<td class="">{{$cron->comando}}</td>
										<td>{!! beauty_fecha(\Carbon\Carbon::parse($cron->last)->setTimezone(Auth::user()->val_timezone)) !!}</td>
										<td>{!! beauty_fecha(\Carbon\Carbon::parse($cron->real_last)->setTimezone(Auth::user()->val_timezone)) !!}</td>
										<td>{!! beauty_fecha(\Carbon\Carbon::parse($cron->prox)->setTimezone(Auth::user()->val_timezone)) !!}</td>
										<td>{{$cron->cron}}</td>
									</tr>
									<tr >
										<td colspan="6" id="tarea{{$cron->id_tarea}}"  style="display:none"></td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Colas de proceso</h3>
    </div>
    <div class="card-body">
		<div class="row">
			<div class="col-12">
				<div class="card">
					<div class="card-header bg-primary text-white">
						<i class="mdi mdi-buffer mdi-24px"></i> {{__('tareas.colas_proceso')}}
					</div>
					<div class="card-body">
						<table class="table table-bordered table-condensed table-hover">
			                <thead>
			                    <tr>
			                        <th>{{__('tareas.comando')}}</th>
									<th>{{__('tareas.ultima_ejec_programada')}}</th>
									<th>{{__('tareas.ultima_ejec_real')}}</th>
									<th>{{__('tareas.prox_ejec')}}</th>
									<th>Cron</th>
			                    </tr>
			                </thead>
			                <tbody>
			                	@foreach ($queues as $cron)
									<tr class="hover-this fila_cola" data-colas="{{isset($cron->queue)?$cron->queue : 0}}" data-id="{{isset($cron->queue_id)?$cron->queue_id : 0}}">
										<td class="">{{$cron->comando}}</td>
										<td>{!! beauty_fecha(\Carbon\Carbon::parse($cron->last)->setTimezone(Auth::user()->val_timezone)) !!}</td>
										<td>{!! beauty_fecha(\Carbon\Carbon::parse($cron->real_last)->setTimezone(Auth::user()->val_timezone)) !!}</td>
										<td>{!! beauty_fecha(\Carbon\Carbon::parse($cron->prox)->setTimezone(Auth::user()->val_timezone)) !!}</td>
										<td>{{$cron->cron}}</td>
									</tr>
									<tr >
										<td colspan="6" id="cola{{isset($cron->queue_id)?$cron->queue_id : 0}}"  style="display:none"></td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
    </div>
</div>

<div class="modal fade" id="run_tarea">
	<div class="modal-dialog modal-lg">
		<div class="modal-content">
			<div class="modal-header">
				<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
				<div class="spinner-border text-info float-left" role="status" style="margin-right: 10px; display: none" id="spin_tarea"><span class="sr-only">{{trans('strings.espere')}}...</span></div>
				<h3 class="modal-title text-nowrap">Ejecutar tarea <span id="des_tarea_run"></span> </h3>
				<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
					<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
				</button>
			</div> 
			<div class="modal-body text-start" id="log_fichero" style="height: 350px; overflow: Auto; font-size: 10px">

			</div>
			<div class="modal-footer">
				{{--  <a class="btn btn-info" href="{{url('tasks/delete',$t->cod_tarea)}}">{{trans('strings.yes')}}</a>  --}}
				<button type="button" data-dismiss="modal" onclick="cerrar_modal()" class="btn btn-warning">Cerrar</button>
			</div>
		</div>
	</div>
</div>

@endsection


@section('scripts')
<script src="{{url('/plugins/jQuery-slimScroll-1.3.8/jquery.slimscroll.min.js')}}"></script>
    <script>
        $('.configuracion').addClass('active active-sub');
		$('.menu_utilidades').addClass('active active-sub');
        $('.tareas_programadas').addClass('active');
	</script>
	
	<script>
		$('.fila_tarea').click(function(){
			$('#tarea'+$(this).data('id')).toggle();
			$('#tarea'+$(this).data('id')).load("{{url('tasks/detalle')}}/"+$(this).data('id'));
		});
		$('.fila_cola').click(function(){
			console.log($(this).data('id'));
			if($(this).data('id')!=0){
				$('#cola'+$(this).data('id')).toggle();
				$('#cola'+$(this).data('id')).load("{{url('tasks/cola')}}/"+$(this).data('colas'));
			}
	
		});

		function del(id){
            $('#eliminar-tarea-'+id).modal('show');
        }
	
		function run(id,desc){
			event.preventDefault();
			event.stopPropagation();
			$('#log_fichero').html('');
			$('#des_tarea_run').html(desc);
			$('#log_fichero').html();
			fecha_consulta=moment().utc().format('YYYY-MM-DD HH:mm:ss');
			$('#run_tarea').modal('show');
			$('#spin_tarea').show();
			id_tarea=id;
			var lt=setInterval(() => {
				$.get("{{url('/tasks/log_tarea') }}/"+id_tarea+"/"+fecha_consulta,function(data, textStatus, xhr){
					$('#log_fichero').html(data);
					$("#log_fichero").animate({ scrollTop: $('#log_fichero').prop("scrollHeight")}, 1000);
				});
			}, 2000);
			$.get("{{ url('tasks/runTask') }}/"+id_tarea, function(data){
				$('#spin_tarea').hide();
				console.log('hecho, data:')
				console.log(data);
				if (data.error){
					toast_error(data.title,data.error)
				} else {
					toast_ok(data.title,data.message);
				}
				clearInterval(lt);
				ocultar=setTimeout(() => {
					$('#run_tarea').modal('hide');
				}, 10000);
				$.get("{{url('/tasks/log_tarea') }}/"+id_tarea+"/"+fecha_consulta,function(data, textStatus, xhr){
					$('#log_fichero').html(data);
					$("#log_fichero").animate({ scrollTop: $('#log_fichero').prop("scrollHeight")}, 1000);
				});
				
			});
			$('#log_fichero').click(function(){
				clearTimeout(ocultar);
			});
		}
	</script>

@endsection

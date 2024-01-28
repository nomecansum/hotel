@php
	use Carbon\Carbon;
	use Carbon\CarbonPeriod;
    use App\Http\Controllers\TrabajosController;
	$total = 0;
	$clientes=$planes->pluck('id_cliente')->unique();
	$cnt_fechas=$programaciones->pluck('fec_programacion')->unique()->count();
	$cnt_clientes=$planes->pluck('id_cliente')->unique()->count();
	$filas=$planes->count();
	$nombre_informe="Informe de trabajos planificados";
	$date = explode(" - ",$r->fechas);
	$f1 = Carbon::parse(adaptar_fecha($date[0]));
	$f2 = Carbon::parse(adaptar_fecha($date[1]));

	
    $fecha_ant=$f1->format('Y-m-d');
    $fecha_sig=$f2->format('Y-m-d');
    $ultimo_dia=$f2->format('d');
    $meses = ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
    $hoy=Carbon::now();
	$periodo=CarbonPeriod::create($f1,$f2);

@endphp

@if($r->output=="pdf" || (isset($r->email_schedule)&&$r->email_schedule==1))
<style type="text/css">
	thead {
		display: table-row-group;
	}
	tr {
		page-break-before: always;
		page-break-after: always;
		page-break-inside: avoid;
	}
	table {
		word-wrap: break-word;
	}
	table td {
		word-break: break-all;
	}

</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<link href="{{ url('/css/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ url('/css/nifty.min.css') }}" rel="stylesheet">
<link href="{{ url('/css/themes/type-e/theme-navy.min.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ URL('/css/materialdesignicons.min.css') }}">
<link href="{{ asset('/plugins/fontawesome6/css/all.min.css') }}" rel="stylesheet">
<link href="{{ url('/css/mosaic.css') }}" rel="stylesheet">
<link rel="stylesheet" href="{{ URL('/plugins/bootstrap-table/bootstrap-table.min.css') }}">

<div class="table-responsive m-t-40 overflow-hidden">
<span id="resumen_informe"></span>
@endif


@if($clientes->isEmpty())
	@if($r->output=="pdf" || $r->output=="excel")
	<table class="table table-bordered table-condensed table-hover table-informes" style="font-size: 12px">
		<tbody >
	@endif
	<div class="text-center">
		<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
		<h3 class="text-warning">No hay datos para mostrar</h3>
	</div>
	@if($r->output=="pdf" || $r->output=="excel")
		</tbody>
	</table>
	@endif
@endif


@foreach($clientes as $cliente)
	@if($r->output=="pdf" || $r->output=="excel")
		<table class="table table-bordered table-condensed table-hover table-informes table-striped" style="font-size: 12px; " >
			<tbody>
	@endif
	@php
		$inf=$planes->where('id_cliente',$cliente);
	@endphp
	
	@if($planes->count()>0)
		<tr>
			<td colspan="{{ count($periodo)+3 }}" >
				@include('resources.cabecera_cliente_informes')
			</td>
		</tr>
		<tr>
			<td colspan="{{ count($periodo)+3 }}">
				<h4 class="text-muted text-center">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
			</td>
		</tr>
	@endif
	
	@if($r->output=="pdf" || $r->output=="excel")
		</tbody>
	@endif

	@foreach($inf as $plan)
		<tr>
			<td colspan="{{ count($periodo)+3 }}">
				<h3 class="bg-light" style="color:{{ $plan->val_color }}"><i  class="{{ $plan->val_icono }}"></i>{{ $plan->des_plan }}</h3>
			</td>
		</tr>
		<tr>
			<td colspan="3"></td>
			@foreach($periodo as $fecha)
				<td class="text-center" style="@if($r->output=="excel") width: 50px; @else width: 5% @endif">
					<span class="vertical">{{ $fecha->format('d/m') }}</span>
				</td>
			@endforeach
		</tr>
		@php
			$grupos_plan=$detalle->where('id_plan',$plan->id_plan)->pluck('id_grupo_trabajo')->unique()->toarray();
		@endphp
		@foreach($grupos->wherein('id_grupo',$grupos_plan) as $grupo)
			@php
				$trabajos_grupo=$trabajos->where('id_grupo',$grupo->id_grupo);
				if($grupo->fec_inicio==null && $grupo->fec_fin==null){
					$in_time=true;
				}
				$fec_ini_grupo=$grupo->fec_inicio!=null?Carbon::parse(Carbon::parse($fecha)->format('Y').'-'.Carbon::parse($grupo->fec_inicio)->format('m-d')):null;
				$fec_fin_grupo=$grupo->fec_inicio!=null?Carbon::parse(Carbon::parse($fecha)->format('Y').'-'.Carbon::parse($grupo->fec_fin)->format('m-d')):null;
			@endphp
			@foreach($trabajos_grupo as $trabajo)
				<tr>
					@if($loop->index==0)
						<td rowspan="{{ $trabajos_grupo->count()*($plantas->count()+$zonas->count()) }}" class="text-center align-middle" style="vertical-align: middle; padding: 10px 0px 10px 0px; background-color: {{ $grupo->val_color }}"><span class="vertical text-center {{ txt_blanco($grupo->val_color) }}"> {{ $grupo->des_grupo }}</span></td>
					@endif
					<td scope="col" rowspan={{ $plantas->count()+$zonas->count() }} class="{{ txt_blanco($trabajo->val_color) }}" style="background-color:{{ $trabajo->val_color }}; padding-top: 6em"><span class="vertical text-center{{ txt_blanco($trabajo->val_color) }}" style="vertical-align: middle"><i class="{{ $trabajo->val_icono }}"></i> {{ $trabajo->des_trabajo }}</span></td>
					@foreach($plantas as $planta)
						@if($loop->index==0)
							<td nowrap>{{ $planta->des_planta }}</td>
							@foreach($periodo as $fecha)
								@php
									$tarea=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_planta',$planta->id_planta)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
									if($tarea){
										$tarea->fec_ini_grupo=$fec_ini_grupo;
										$tarea->fec_fin_grupo=$fec_fin_grupo;
										$tarea->fec_ini_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_inicio):null;
										$tarea->fec_fin_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_fin):null;
									}
									$programa=$programaciones->where('id_trabajo_plan',$tarea->key_id??0)->where('fecha_corta',$fecha->format('Y-m-d'))->first();
									$datos_celda=TrabajosController::celda_plan_trabajos($tarea,$programa,$hoy,$fecha);
								@endphp
								<td class="{{ $datos_celda['color']??'' }} text-center td_planta" title="{{ $datos_celda['title'] }}"  data-programacion="{{ $programa->id_programacion??0 }}" data-trabajo={{ $programa->id_trabajo_plan??'0' }} data-fecha="{{ $fecha->format('Y-m-d') }}" data-desc="#{{ $programa->id_programacion??'' }} {{ $trabajo->des_trabajo }} en {{ $planta->des_planta}} el {{beauty_fecha($fecha)}}">
									@if($r->output=="excel") {{ $datos_celda['abr'] }} @else <i class="{{ $datos_celda['icono'] }}"></i> @endif
								</td>
							@endforeach
						@endif
					@endforeach
				</tr>
				@foreach($plantas as $planta)
					@if($loop->index!=0)
						<tr>
							<td nowrap>{{ $planta->des_planta }}</td>
							@foreach ($periodo as $fecha)
								@php
									$tarea=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_planta',$planta->id_planta)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
									if($tarea){
										$tarea->fec_ini_grupo=$fec_ini_grupo;
										$tarea->fec_fin_grupo=$fec_fin_grupo;
										$tarea->fec_ini_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_inicio):null;
										$tarea->fec_fin_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_fin):null;
									}
									$programa=$programaciones->where('id_trabajo_plan',$tarea->key_id??0)->where('fecha_corta',$fecha->format('Y-m-d'))->first();
									$datos_celda=TrabajosController::celda_plan_trabajos($tarea,$programa,$hoy,$fecha);
								@endphp
								<td class="{{ $datos_celda['color']??'' }} text-center td_planta" title="{{ $datos_celda['title'] }}"   data-programacion="{{ $programa->id_programacion??0 }}" data-trabajo={{ $programa->id_trabajo_plan??'0' }} data-fecha="{{ $fecha->format('Y-m-d') }}" data-desc="#{{ $programa->id_programacion??'' }} {{ $trabajo->des_trabajo }} en {{$planta->des_planta}} el {{beauty_fecha($fecha)}}">
									@if($r->output=="excel") {{ $datos_celda['abr'] }} @else <i class="{{ $datos_celda['icono'] }}"></i> @endif
								</td>
							@endforeach
						</tr>
					@endif
				@endforeach
				<tr>
					@foreach($zonas as $zona)
						@if($loop->index==0)
							<td nowrap>[{{ $zona->des_planta }}] {{ $zona->des_zona }}</td>
							@foreach ($periodo as $fecha)
								@php
									$tarea=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_zona',$zona->key_id)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
									if($tarea){
										$tarea->fec_ini_grupo=$fec_ini_grupo;
										$tarea->fec_fin_grupo=$fec_fin_grupo;
										$tarea->fec_ini_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_inicio):null;
										$tarea->fec_fin_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_fin):null;
									}
									$programa=$programaciones->where('id_trabajo_plan',$tarea->key_id??0)->where('fecha_corta',$fecha->format('Y-m-d'))->first();
									$datos_celda=TrabajosController::celda_plan_trabajos($tarea,$programa,$hoy,$fecha);
								@endphp
								<td class="{{ $datos_celda['color']??'' }} text-center td_planta" title="{{ $datos_celda['title'] }}"   data-programacion="{{ $programa->id_programacion??0 }}" data-trabajo={{ $programa->id_trabajo_plan??'0' }} data-fecha="{{ $fecha->format('Y-m-d') }}" data-desc="#{{ $programa->id_programacion??'' }} {{ $trabajo->des_trabajo }} en [{{ $zona->des_planta }}] {{ $zona->des_zona }} el {{beauty_fecha($fecha)}}">
									@if($r->output=="excel") {{ $datos_celda['abr'] }} @else <i class="{{ $datos_celda['icono'] }}"></i> @endif
								</td>
							@endforeach
						@endif
					@endforeach
				</tr>
				@foreach($zonas as $zona)
					@if($loop->index!=0)
						<tr>
							<td nowrap>[{{ $zona->des_planta }}] {{ $zona->des_zona }}</td>
							@foreach ($periodo as $fecha)
								@php
									$tarea=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_zona',$zona->key_id)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
									if($tarea){
										$tarea->fec_ini_grupo=$fec_ini_grupo;
										$tarea->fec_fin_grupo=$fec_fin_grupo;
										$tarea->fec_ini_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_inicio):null;
										$tarea->fec_fin_trabajo=$trabajo->fec_inicio!=null?Carbon::parse($trabajo->fec_fin):null;
									}
									$programa=$programaciones->where('id_trabajo_plan',$tarea->key_id??0)->where('fecha_corta',$fecha->format('Y-m-d'))->first();
									$datos_celda=TrabajosController::celda_plan_trabajos($tarea,$programa,$hoy,$fecha);
								@endphp
								<td class="{{ $datos_celda['color']??'' }} text-center td_planta" title="{{ $datos_celda['title'] }}"   data-programacion="{{ $programa->id_programacion??0 }}" data-trabajo={{ $programa->id_trabajo_plan??'0' }} data-fecha="{{ $fecha->format('Y-m-d') }}" data-desc="#{{ $programa->id_programacion??'' }} {{ $trabajo->des_trabajo }} en [{{ $zona->des_planta }}] {{ $zona->des_zona }} el {{beauty_fecha($fecha)}}">
									@if($r->output=="excel") {{ $datos_celda['abr'] }} @else <i class="{{ $datos_celda['icono'] }}"></i> @endif
								</td>
							@endforeach
						</tr>
					@endif
				@endforeach
			@endforeach
		@endforeach
	@endforeach
	@if($r->output=="excel")
		<tr>
			
		</tr>
		<tr>
			<td colspan="11">LEYENDA</td>
		</tr>
		<tr>
			<td></td>
			<td colspan="10">El trabajo aun no se ha iniciado</td>
		</tr>
		<tr>
			<td>T</td>
			<td colspan="10">El trabajo se ha realizado fuera de tiempo</td>
		</tr>
		<tr>
			<td>OK</td>
			<td colspan="10">El trabajo se ha realizado en tiempo</td>
		</tr>
		<tr>
			<td>X</td>
			<td colspan="10">El trabajo no se ha realizado</td>
		</tr>
		<tr>
			<td>F</td>
			<td colspan="10">El trabajo se ha iniciado pero fuera de la fecha prevista</td>
		</tr>
		<tr>
			<td>R</td>
			<td colspan="10">El trabajo esta fuera del rango de fechas establecido en la configuracion de la tarea o el grupo</td>
		</tr>
	@endif
	@if($r->output=="pdf" || $r->output=="excel")
		</table>
	@endif

	@if($r->output=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach
<script>
	$('#resumen_informe').html(" {{ $cnt_fechas }} Dias | {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('canceladas');
</script>
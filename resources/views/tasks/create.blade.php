@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Editar tarea programada</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
		<li class="breadcrumb-item">utilidades</li>
        <li class="breadcrumb-item"><a href="{{url('/tasks')}}">Tareas programadas</a></li>
        <li class="breadcrumb-item active">Editar tarea {{ !empty($t->des_tarea) ? $t->des_tarea : '' }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">@isset ($t)Editar tarea {{ !empty($t->des_tarea) ? $t->des_tarea : '' }}@else Crear tarea @endif</h3>
    </div>
    <div class="card-body">
		@isset ($t)
		<form action="{{url('tasks/update',$t->cod_tarea)}}" class="form-ajax" method="POST" id="form_tarea">
		<input type="hidden" name="cod_tarea" id="cod_tarea" value="{{ $t->cod_tarea }}">
		@else
		<form action="{{url('tasks/save')}}" class="form-ajax" method="POST">
		<input type="hidden" name="cod_tarea" id="cod_tarea" value="0">
		@endisset
			{{csrf_field()}}
			<div class="row">
				<div class="col-sm-9">
					<div class="form-group">
						<label>Descripcion</label>
						<input value="{{isset($t) ? $t->des_tarea : ''}}" type="text" class="form-control" name="des_tarea">
					</div>
				</div>
				<div class="col-sm-3">
					<div class="form-group">
						<label>Tipo de comando</label>
						<select required class="form-control" id="tip_comando" name="tip_comando">
							<option value="" selected disabled></option>
							{{--  <option {{isset($t) ? ($t->tip_comando == 'W' ? 'selected' : '') : ''}} value="W">Windows Task scheduler</option>  --}}
							<option {{isset($t) ? ($t->tip_comando == 'X' ? 'selected' : '') : ''}} value="X">Linux Crontab</option>
							<option {{ (isset($t) && $t->tip_comando == 'L')||!isset($t) ? 'selected' : ''}} value="L">Laravel Task Scheduler</option>

						</select>
					</div>
				</div>
			</div>

			<div class="row mt-2">
				<div class="col-sm-3">
					<div class="form-group">
						<label>Intervalo</label>
						<select class="form-control" name="val_intervalo" id="val_intervalo">
							<option value="" ></option>
							<option {{isset($t) ? ($t->val_intervalo == 'everyMinute' ? 'selected' : '') : ''}} value="everyMinute">{{__('tareas.cada_minuto')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'everyFiveMinutes' ? 'selected' : '') : ''}} value="everyFiveMinutes">{{__('tareas.cada_5_minutos')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'everyTenMinutes' ? 'selected' : '') : ''}} value="everyTenMinutes">{{__('tareas.cada_10_minutos')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'everyFifteenMinutes' ? 'selected' : '') : ''}} value="everyFifteenMinutes">{{__('tareas.cada_15_minutos')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'everyThirtyMinutes' ? 'selected' : '') : ''}} value="everyThirtyMinutes">{{__('tareas.cada_30_minutos')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'hourly' ? 'selected' : '') : ''}} value="hourly">{{__('tareas.cada_hora')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'hourlyAt' ? 'selected' : '') : ''}} value="hourlyAt">{{__('tareas.cada_hora_minuto')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'daily' ? 'selected' : '') : ''}} value="daily">{{__('tareas.diario')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'dailyAt' ? 'selected' : '') : ''}} value="dailyAt">{{__('tareas.diario_a_las')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'weekly' ? 'selected' : '') : ''}} value="weekly">{{__('tareas.semanal')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'weeklyOn' ? 'selected' : '') : ''}} value="weeklyOn">{{__('tareas.semanal_en')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'monthly' ? 'selected' : '') : ''}} value="monthly">{{__('tareas.mensual')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'lastDayOfMonth' ? 'selected' : '') : ''}} value="lastDayOfMonth">{{__('tareas.last_day_of_month')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'monthlyOn' ? 'selected' : '') : ''}} value="monthlyOn">{{__('tareas.mensual_en')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'quarterly' ? 'selected' : '') : ''}} value="quarterly">{{__('tareas.trimestral')}}</option>
							<option {{isset($t) ? ($t->val_intervalo == 'yearly' ? 'selected' : '') : ''}} value="yearly">{{__('tareas.anual')}}</option>
						</select>
					</div>
				</div>
				<div class="col-sm-2" id="detalle" style="display:none">
					<div class="form-group">
						<label id="lbl_Detalle"></label><br>
						<input value="{{isset($t) ? $t->det_minuto : '0'}} " type="number" class="form-control col-sm-3" min="0" max="59"  id="det_minuto" name="det_minuto" style="display:none">
						@php

						@endphp
						<select name="det_diasemana" class="form-control col-sm-3" id="cmb_diasemana" style="display:none">
							<option value="1" {{ isset($t->det_diasemana) && in_array(1,explode(",",$t->det_diasemana))===true?'selected':'' }}>{{__('general.lunes2')}}</option>
							<option value="2" {{ isset($t->det_diasemana) && in_array(2,explode(",",$t->det_diasemana))===true?'selected':'' }}>{{__('general.martes2')}}</option>
							<option value="3" {{ isset($t->det_diasemana) && in_array(3,explode(",",$t->det_diasemana))===true?'selected':'' }}>{{__('general.miercoles2')}}</option>
							<option value="4" {{ isset($t->det_diasemana) && in_array(4,explode(",",$t->det_diasemana))===true?'selected':'' }}>{{__('general.jueves2')}}</option>
							<option value="5" {{ isset($t->det_diasemana) && in_array(5,explode(",",$t->det_diasemana))===true?'selected':'' }}>{{__('general.viernes2')}}</option>
							<option value="6" {{ isset($t->det_diasemana) && in_array(6,explode(",",$t->det_diasemana))===true?'selected':'' }}>{{__('general.sabado2')}}</option>
							<option value="7" {{ isset($t->det_diasemana) && in_array(7,explode(",",$t->det_diasemana))===true?'selected':'' }}>{{__('general.domingo2')}}</option>
						</select>
						<select  name="det_diames" class="form-control col-sm-3" id="cmb_diames" style="display:none">
							@for($n=1; $n<32;$n++)
								<option value="{{ $n }}"  {{isset($t)&&$t->det_diames==$n ? 'selected' : ''}}>{{ $n }}</option>
							@endfor
						</select>
						<input type="time" placeholder="HH:mm" id="det_horaminuto" name="det_horaminuto" class="form-control hourMask col-sm-4" style="display:none" value="{{isset($t) ? $t->det_horaminuto : '00:00'}}">
					</div>
				</div>
				<div class="col-sm-6">
					<div class="form-group">
						@php
							$dias_semana=[];
							if(isset($t->dias_semana)&&$t->dias_semana!=''){
								$dias_semana=explode(",",$t->dias_semana);
							}
						@endphp
						<label>{{ __('tareas.dia_de_la_semana') }}</label><br>
						<select class="form-control select2 select2-multiple" multiple name="dias_semana[]" id="dias_semana">
							<option {{ isset($t)&&in_array('alldays',$dias_semana)||count($dias_semana)==0 ? 'selected' : ''}} value="alldays">{{__('tareas.todos_los_dias')}}</option>
							<option {{ isset($t)&&in_array('mondays',$dias_semana) ? 'selected' : ''}} value="mondays">{{__('general.lunes')}}</option>
							<option {{ isset($t)&&in_array('tuesdays',$dias_semana) ? 'selected' : ''}} value="tuesdays">{{__('general.martes')}}</option>
							<option {{ isset($t)&&in_array('wednesdays',$dias_semana) ? 'selected' : ''}} value="wednesdays">{{__('general.miercoles')}}</option>
							<option {{ isset($t)&&in_array('thursdays',$dias_semana) ? 'selected' : ''}} value="thursdays">{{__('general.jueves')}}</option>
							<option {{ isset($t)&&in_array('fridays',$dias_semana) ? 'selected' : ''}} value="fridays">{{__('general.viernes')}}</option>
							<option {{ isset($t)&&in_array('saturdays',$dias_semana) ? 'selected' : ''}} value="saturdays">{{__('general.sabado')}}</option>
							<option {{ isset($t)&&in_array('sundays',$dias_semana) ? 'selected' : ''}} value="sundays">{{__('general.domingo')}}</option>
							{{-- <option {{isset($t) ? ($t->VAL_INTERVALO == 'between' ? 'selected' : '') : ''}} value="between">Dos veces al dia a las</option> --}}
						</select>
					</div>
				</div>
				<div class="col-sm-3">
					<label>{{__('general.franja_horaria')}}</label><br>
					<div class="form-group d-flex">
						
						<input type="time" placeholder="HH:mm" name="hora_inicio" id="hor_desde"  class="form-control col-sm-5" style="width: 120px" value="{{isset($t) ? Carbon\Carbon::parse($t->hora_inicio)->format('H:i') : '00:00'}}">
						
						<input type="time" placeholder="HH:mm" name="hora_fin" id="hor_hasta"  class="form-control col-sm-5" style="width: 120px" value="{{isset($t) ?  Carbon\Carbon::parse($t->hora_fin)->format('H:i') : '23:59'}}">
					</div>
				</div>
				
			</div>

			<div class="row mt-2">
				<div class="col-sm-7" id="cmd-txt" style="display:none">
					<div class="form-group">
						<label>{{__('general.comando')}}</label>
						<input value="{{isset($t) ? $t->nom_comando : ''}}" type="text" class="form-control" name="comando">
					</div>
				</div>
				<div class="col-sm-7" id="cmd-cmb">
					<div class="form-group">
						<label>{{__('tareas.comando')}}</label>
						<select name="comando" id="comando"class="form-control select2" style="width: 100%" placeholder="Seleccione un comando">
							@php
								$files = File::allFiles(app_path() . '/Console/Commands/');
								$comandos_excluir=["DispatchJob","ScheduleListCommand"];
							@endphp
							<option value=""></option>
							@foreach ($files as $file)
								@if(!in_array(str_replace(".php","",str_replace("_"," ",basename($file))),$comandos_excluir))
									<option style="text-transform: uppercase" {{ isset($t->nom_comando)&&$t->nom_comando==basename($file)?'selected':'' }}  value="{{ basename($file) }}">{{ str_replace(".php","",str_replace("_"," ",basename($file))) }}</option>
								@endif
							@endforeach
						</select>
					</div>
				</div>

				<div class="col-sm-2">
					<div class="form-group">
						<label>{{__('general.timeout')}} (seg)</label>
						<input value="{{isset($t) ? $t->val_timeout : '180'}}" type="number" min="0" class="form-control" name="val_timeout">
					</div>
				</div>
				<div class="col-sm-1">
					<div class="form-group">
						<label>{{__('general.color')}}</label><br>
						<input type="color" autocomplete="no" name="val_color_tarea" class="form-control" value="{{isset($t) ? $t->val_color : App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
					</div>
				</div>
				<div class="col-sm-1">
					<div class="form-group">
						<label>{{__('general.icono')}}</label><br>
						<button type="button" autocomplete="no"  role="iconpicker" required name="val_icono_tarea"  id="val_icono_tarea" data-iconset="fontawesome5" data-icon="{{isset($t) ? ($t->val_icono) : ''}}"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
					</div>
				</div>
			</div>
	</div>
</div>
<div class="row mt-3" id="div_regla" style="display:none">
	<div class="col-md-12">
		<div class="card totales_resultados b-all" >
			<h4 class="mt-2 ml-2" >{{__('tareas.parametrizacion_del_comando')}}</h4>
			<div class="card-body" id="param_regla">

			</div>
		</div>
	</div>
</div>

<div class="row mt-2">
	<div class="col-md-12 text-end">
		<button type="submit" class="btn btn-primary btn_form float-right">{{__('general.submit')}}</button>
	</div>
</div>
</form>
					
			
   

@endsection


@section('scripts')
    <script>
        $('.configuracion').addClass('active active-sub');
		$('.menu_utilidades').addClass('active active-sub');
        $('.tareas_programadas').addClass('active');
	</script>
	
	<script>
		$(function(){
			$('.iconpicker').iconpicker({
				icon:'{{isset($t) ? ($t->val_icono) : ''}}'
			});
			$('#val_intervalo').change();
		})
		
	
		$('#val_intervalo').change(function(){
			$("#det_minuto").hide();
			$("#det_horaminuto").hide();
			$("#cmb_diasemana").hide();
			$("#cmb_diames").hide();
			$("#detalle").hide();
			switch ($(this).val()){
				case "hourlyAt":
					$("#detalle").show();
					$("#det_minuto").show();
					$("#lbl_Detalle").html("minuto");
				break;
	
				case "dailyAt":
					$("#detalle").show();
					$("#det_horaminuto").show();
					$("#lbl_Detalle").html("hora");
				break;
	
				case "weeklyOn":
					$("#detalle").show();
					$("#det_horaminuto").show();
					$("#cmb_diasemana").show();
					$("#lbl_Detalle").html("{{__('general.los')}}... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__('general.A_LAS')}}... ");
				break;
	
				case "monthlyOn":
					$("#detalle").show();
					$("#det_horaminuto").show();
					$("#cmb_diames").show();
					$("#lbl_Detalle").html("{{__('general.los')}}... &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{__('general.a_las')}}... ");
				break;
	
				case "lastDayOfMonth":
					$("#detalle").show();
					$("#det_horaminuto").show();
					$("#lbl_Detalle").html("&nbsp;&nbsp;{{__('general.a_las')}}... ");
				break;
			}
		});
	
		$('#tip_comando').change(function(){
			$("#cmd-txt").hide();
			$("#cmd-cmb").hide();
	
			switch ($(this).val()){
				case "W":
					$("#cmd-txt").show();
				break;
	
				case "X":
					$("#cmd-txt").show();
				break;
	
				case "L":
					$("#cmd-cmb").show();
				break;
			}
		});
	
		$("#comando").select2({
			placeholder: "{{__('tareas.seleccione_un_comando')}}",
			allowClear: true
		});
		$('#comando').on('select2:select', function (e) {
			var data = e.params.data;
			console.log(data);
			$('#param_regla').html('');
			$.post('{{url("/tasks/param_comando/")}}/'+$('#cod_tarea').val(), {_token: '{{csrf_token()}}', 'comando': data.id}, function(data, textStatus, xhr) {
	
			})
			.done(function(data) {
	
				$('#param_regla').html(data);
				$('#div_regla').show();
				$('#row_intervalo').show();
				animateCSS('#div_regla','bounceInRight');
	
			})
			.fail(function(err) {
				let error = JSON.parse(err.responseText);
				console.log(error);
				toast_error("Error",error.error);
			})
		});
	
		@if(isset($t)&&$t->cod_tarea!=0)
		$('#param_regla').html('');
			$.post('{{url("/tasks/param_comando/")}}/{{ $t->cod_tarea }}', {_token: '{{csrf_token()}}', 'comando': '{{ $t->nom_comando }}'}, function(data, textStatus, xhr) {
	
			})
			.done(function(data) {
	
				
				$('#div_regla').show();
				$('#row_intervalo').show();
				$('#param_regla').html(data);
				animateCSS('#div_regla','bounceInRight');
				
	
			})
		@endif;
		$('.btn_form').click(function(){
			$('#param_regla').detach().appendTo('#form_tarea');
			$('#param_regla').hide();
		})
	</script>
@endsection


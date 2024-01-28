@php
	use Carbon\Carbon;
	($total = 0); 
	$clientes=$informe->pluck('id_cliente')->unique();
	$usuarios=$informe->pluck('id_user')->unique();
	$cnt_fechas=$informe->pluck('fecha')->unique()->count();  
	$cnt_clientes=$informe->pluck('id_cliente')->unique()->count();
	$filas=$informe->count();
	$nombre_informe="Informe de uso de puestos";
	$date = explode(" - ",$r->fechas);
	$f1 = adaptar_fecha($date[0]);
	$f2 = adaptar_fecha($date[1]);
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
		<tbody id="myFilter" >
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
		<table class="table table-bordered table-condensed table-hover table-informes table-striped" style="font-size: 12px">
			<tbody id="myFilter" >
	@endif
	@php
		$inf=$informe->where('id_cliente',$cliente);
	@endphp	
	@if($informe->count()>0)
		<tr>
			<td colspan="11" >
				@include('resources.cabecera_cliente_informes')
			</td>
		</tr>
		<tr>
			<td colspan="11">
				<h4 class="text-muted text-center">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
			</td>
		</tr>
	@endif
	<tr class="font-bold">
		<td></td>
		<td colspan="4" class="text-center" @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Cambios de estado</td>
		<td colspan="3" class="text-center"  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Incidencias</td>
		<td colspan="3" class="text-center"  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Reservas</td>
	</tr>
	<tr class="text-center font-bold">
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Puesto</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Check-in</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Check-out</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Limpieza</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Total</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Abiertas</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Cerradas</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Total</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Utilizadas</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Anuladas</td>
		<td @if($r->output=="excel") style="text-align: center; background-color: #fde9d9; " @endif>Total</td>
	</tr>
	@foreach ($inf as $puesto)
		@php
			$usado=$usos->where('id_puesto',$puesto->id_puesto)->where('id_estado',2);
			$disponible=$usos->where('id_puesto',$puesto->id_puesto)->where('id_estado',1);
			$limpieza=$usos->where('id_puesto',$puesto->id_puesto)->where('id_estado',6);
			$cambios=$usos->where('id_puesto',$puesto->id_puesto);
			$inc_abiertas=$incidencias->where('id_puesto',$puesto->id_puesto)->wherenull('fec_cierre');
			$inc_cerradas=$incidencias->where('id_puesto',$puesto->id_puesto)->wherenotnull('fec_cierre');
			$inc_total=$incidencias->where('id_puesto',$puesto->id_puesto);
			$res_total=$reservas->where('id_puesto',$puesto->id_puesto);
			$res_anuladas=$reservas->where('id_puesto',$puesto->id_puesto)->where('mca_anulada','S');
			$res_usadas=$reservas->where('id_puesto',$puesto->id_puesto)->wherenotnull('fec_utilizada');
		@endphp	
		@if($cambios->count()>0 || $res_total->count()>0 || $inc_total->count()>0)
			<tr class="text-center">
				<td @if($r->output=="excel") style="background-color: #bbbbbb; font-weight: 400" @endif>
					@isset($puesto->icono_tipo)
						<i class="{{ $puesto->icono_tipo }} fa-2x" style="color: {{ $puesto->color_tipo }}"></i>
					@endisset
					{{ $puesto->cod_puesto }}</td>
				<td>{{ $usado->count() }}</td>
				<td>{{ $disponible->count() }}</td>
				<td>{{ $limpieza->count() }}</td>
				<td @if($r->output=="excel") style="background-color: #bbbbbb; font-weight: 400" @endif>{{ $cambios->count() }}</td>
				<td>{{ $inc_abiertas->count() }}</td>
				<td>{{ $inc_cerradas->count() }}</td>
				<td @if($r->output=="excel") style="background-color: #bbbbbb; font-weight: 400" @endif>{{ $inc_total->count() }}</td>
				<td>{{ $res_usadas->count()-$res_anuladas->count() }}</td>
				<td>{{ $res_anuladas->count() }}</td>
				<td @if($r->output=="excel") style="background-color: #bbbbbb; font-weight: 400" @endif>{{ $res_total->count() }}</td>
			</tr>
		@endif
	@endforeach
	@if($r->output=="pdf" || $r->output=="excel")
		</tbody>
	</table>
	@endif
	@if($r->output=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach
<script>
	$('#resumen_informe').html(" {{ $cnt_fechas }} Dias | {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('puestos');
</script>
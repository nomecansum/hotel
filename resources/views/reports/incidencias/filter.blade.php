@php
	use Carbon\Carbon;
	use Carbon\CarbonPeriod;
	use Carbon\CarbonInterface;

	$total = 0;
	$clientes=$informe->pluck('id_cliente')->unique();
	$cnt_fechas=$informe->pluck('fec_programacion')->unique()->count();
	$cnt_clientes=$informe->pluck('id_cliente')->unique()->count();
	$filas=$informe->count();
	$nombre_informe="Informe de incidencias";
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
		$inf=$informe->where('id_cliente',$cliente);
	@endphp
	
	@if($informe->count()>0)
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

	<tr>
		<th>Fecha</th>
		<th>Tipo</th>
		<th>Espacio</th>
		<th>Estado</th>
		<th>Tiempo</th>
		<th>Acciones</th>
	</tr>
	@foreach ($inf as $dato)
		<tr>
			<td>@if($r->output!="excel"){!! beauty_fecha($dato->fec_apertura) !!} @else {{ Carbon::parse($dato->fec_apertura)->format('d/m/Y H:i') }} @endif</td>
			<td>{{ $dato->des_tipo_incidencia }}</td>
			<td>{{ $dato->cod_puesto }}</td>
			<td>{{ $dato->fec_cierre==null?'Abierta':'Cerrada' }}</td>
			<td>{{ Carbon::parse($dato->fec_audit)->diffforHumans(Carbon::parse($dato->fec_apertura), CarbonInterface::DIFF_ABSOLUTE) }}</td>
			<td>{{ $dato->num_acciones }}</td>
		</tr>
		
	@endforeach
	
	@if($r->output=="pdf" || $r->output=="excel")
		</table>
	@endif

	@if($r->output=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach
<script>
	$('#resumen_informe').html(" {{ $cnt_fechas }} Dias | {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('incidencias');
</script>
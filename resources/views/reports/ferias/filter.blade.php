@php
	use Carbon\Carbon;
	($total = 0); 
	$clientes=$informe->pluck('id_cliente')->unique();
	$usuarios=$informe->pluck('id_user')->unique();
	$cnt_fechas=$informe->pluck('fecha')->unique()->count();  
	$cnt_clientes=$informe->pluck('id_cliente')->unique()->count();
	$filas=$informe->count();
	$nombre_informe="Informe de contactos en evento";
	$date = explode(" - ",$r->fechas);
	$f1 = adaptar_fecha($date[0]);
	$f2 = adaptar_fecha($date[1]);
@endphp
@if($r->output=="pdf" || (isset($r->email_schedule)&&$r->email_schedule==1))
<style type="text/css">
	tdead {
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
	<tr>
		<th>Fecha</th>
		<th>Cliente</th>
		<th>Empresa</th>
		<th>e-mail</th>
		<th>Marca</th>
		<th>Comercial</th>
	</tr>
	@foreach ($inf as $dato)
		<tr>
			<td>@if($r->output!="excel"){!! beauty_fecha($dato->fecha_contacto) !!} @else {{ Carbon::parse($dato->fecha_contacto)->format('d/m/Y H:i') }} @endif</td>
			<td>{{ $dato->nombre }}</td>
			<td>{{ $dato->empresa }}</td>
			<td>{{ $dato->email }}</td>
			<td>{{ $dato->des_marca }}</td>
			<td>{{ $dato->name??'' }}</td>
		</tr>
		
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
	$('#controller').val('ferias');
</script>
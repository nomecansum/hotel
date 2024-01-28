@php
	use Carbon\Carbon;
	use Carbon\CarbonPeriod;
	($total = 0); 
	$clientes=$informe->pluck('id_cliente')->unique();
	$usuarios=$informe->pluck('id_user')->unique();
	$cnt_fechas=$informe->pluck('fecha')->unique()->count();  
	$cnt_clientes=$informe->pluck('id_cliente')->unique()->count();
	$filas=$informe->count();
	$nombre_informe="Informe de actividad de usuarios";
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
<link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">


@if($clientes->isEmpty())
	@if($r->output=="pdf" || $r->output=="excel")
	<table class="table table-bordered table-condensed table-hover table-informes  table-vcenter" style="font-size: 12px">
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
	<table class="table table-bordered table-condensed table-hover table-informes  table-vcenter  table-striped" style="font-size: 12px"  border="1">
		@php
			$inf=$informe->where('id_cliente',$cliente);
		@endphp	
		<thead>
			@if($informe->count()>0)
				<tr>
					<th  colspan="9">
						@include('resources.cabecera_cliente_informes')
					</th>
				</tr>
				<tr>
					<th   colspan="9" class="text-center">
						<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
					</th>
				</tr>
			@endif
			<tr >
				<th @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif></th>
				<th @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif></th>
				<th  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif class="text-center font-bold">Acciones</th>
				<th  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif class="text-center font-bold">Capturar</th>
				<th  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif class="text-center font-bold">Liberar</th>
				<th  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif class="text-center font-bold">Reservas</th>
				<th  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif class="text-center font-bold">Usadas</th>
				<th  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif class="text-center font-bold">Anuladas</th>
				<th  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold" @endif class="text-center font-bold">Incidencias</th>
			</tr>
		</thead>
		<tbody>
		
		
		@foreach ($usuarios as $u)
			@php
				$inf=$informe->where('id_user',$u);
				$fechas=$inf->pluck('fecha')->unique();
				$inc_usuario=$incidencias->where('id_usuario',$u);
				$reservas_usuario=$reservas->where('id_usuario',$u);
				$reservas_anuladas=$reservas->where('id_usuario',$u)->where('mca_anulada','S');
				$reservas_usadas=$reservas->where('id_usuario',$u)->wherenotnull('fec_utilizada');
				$utilizados=$inf->where('id_estado',1);
				$dejados=$inf->where('id_estado',2);
			@endphp	
			<tr class="text-center">
				
					@if($r->output!=="excel")
						<td style="width: 60px" class="text-center" rowspan="2">
							@if (isset($inf->first()->img_usuario ) && $inf->first()->img_usuario!='' && $r->output!=="excel" )
								<img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$inf->first()->img_usuario) }}" class="img-md rounded-circle" style="width: 50px; height:50px">
							@else
								{!! icono_nombre($inf->first()->name) !!}
							@endif
						</td>
					@else
						<td style="width: 1px"></td>
					@endif
				<td @if($r->output=="excel") style="width: 30px" @else rowspan="2"  @endif>
					{{ $inf->first()->name }}
				</td>
				<td class="add_tooltip" title="{{ $inf->count() }} Escaneos de puesto">
					{{ $inf->count() }}
				</td>
				<td  class="add_tooltip" title="{{ $utilizados->count() }} Puestos capturados">
					{{ $utilizados->count() }}
				</td>
				<td  class="add_tooltip" title=" {{ $dejados->count() }} Puestos liberados">
					{{ $dejados->count() }}
				</td>
				<td  class="add_tooltip" title="{{ $reservas_usuario->count() }} Total reservas">
					{{ $reservas_usuario->count() }}
				</td>
				<td  class="add_tooltip" title="{{ $reservas_usadas->count() }} Reservas usadas">
					{{ $reservas_usadas->count() }}
				</td>
				<td  class="add_tooltip" title="{{ $reservas_anuladas->count() }} Reservas anuladas">
					{{ $reservas_anuladas->count() }}
				</td>
				<td  class="add_tooltip" title="{{ $inc_usuario->count() }} Incidencias abiertas">
					{{ $inc_usuario->count() }}
				</td>
			</tr>
			@if($r->output=="pdf" || $r->output=="pantalla" || (isset($r->email_schedule)&&$r->email_schedule==1))
				<tr>
					<td colspan="7">
						@if($r->output=="pantalla")
						<div class="d-flex flex-wrap">
							@foreach($fechas as $f)
							<div class="pad-all" style="width: 20%">
								@php
									$puestos=$inf->where('fecha',$f)
								@endphp	
								<span class="w-100 text-center" style="font-size: 12px; font-weight: bold">{{ Carbon::parse($f)->format('d/m/Y') }}</span> 
									
								@foreach($puestos as $p) 	
								<li>
										@if($p->id_estado==1) <i class="fas fa-sign-out-alt text-success"></i> @else <i class="fas fa-sign-in-alt text-danger"></i>@endif
										{{ $p->cod_puesto }}<span style="font-size: 10px"> [{{ Carbon::parse($p->fecha_log)->format('H:i') }}]</span>
										
								</li> 
								@endforeach
								
							</div>
							@endforeach
						</div>
						@endif
						@php $cuenta=1; @endphp
						@if($r->output=="pdf")
							@foreach($fechas as $f)
								<table class="table table-condensed m-l-15" style="width: 150px; display: inline-block; float: left;">
									<thead>
										<tr>
										@php
											$puestos=$inf->where('fecha',$f)
										@endphp	
										<th class="text-center" style="font-size: 12x; font-weight: bold">{{ Carbon::parse($f)->format('d/m/Y') }}</th> 
										</tr>
									</thead>	
									<tbody>
										@foreach($puestos as $p) 	
										<tr>
												<td>
												@if($p->id_estado==1) <i class="fas fa-sign-out-alt text-success"></i> @else <i class="fas fa-sign-in-alt text-danger"></i>@endif
												{{ $p->cod_puesto }}<span style="font-size: 10px"> [{{ Carbon::parse($p->fecha_log)->format('H:i') }}]</span>
												<td>
										</tr> 
										@endforeach
									</tbody>	
								</table>

								@php $cuenta++; @endphp
								@if($cuenta==5)
									@php $cuenta=1; @endphp
								@endif
							@endforeach
						@endif
					</td>
				</tr>
			@endif
		@endforeach
		</tbody>
	</table>
	@if($r->output=="excel")
	@php
		$period = CarbonPeriod::create($f1,$f2);
	@endphp
		<table>
			<thead>
				<tr style="">
					<th></th>
					<th  style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold">Nombre</th>
					@foreach($period as $p)
						<th style="text-align: center; background-color: #cccccc; font-size: 12px; font-weight: bold">{{ Carbon::parse($p)->format('d/m/Y') }}</th>
					@endforeach
				</tr>
			</thead>
			<tbody>
				@foreach ($usuarios as $u)
					@php
						$inf=$informe->where('id_user',$u);
					@endphp	
					<tr>
						<td></td>
						<td>{{ $inf->first()->name }}</td>
						@foreach($period as $f)
							@php
								$puestos=$inf->where('fecha',$f->format('Y-m-d'));
								
							@endphp	
							<td style="width: 20px" style="text-align: center; font-size: 12px">
								@foreach($puestos as $p) 	
									@if($p->id_estado==1) In @else Out @endif
									{{ $p->cod_puesto }}<span style="font-size: 10px"> [{{ Carbon::parse($p->fecha_log)->format('H:i') }}]<br>
								@endforeach
							</td>
						@endforeach
					</tr>
				@endforeach
			</tbody>
		</table>
	@endif

	@if($r->output=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach



<script>
	$('#resumen_informe').html(" {{ $cnt_fechas }} Dias | {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('users');
</script>
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


<script src="{{url('/plugins/heatmap.js/build/heatmap.js')}}"></script>


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

	.card_plano{
        --bs-gutter-x: 0;
        --bs-gutter-y: 0;
        padding: 0px 0px 0px 0px;
        margin: 0px 0px 0px 0px;
        overflow: auto !important;
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
	
	<table class="table table-bordered table-condensed table-hover table-informes table-striped" style="font-size: 12px">
		<tbody>
			
			@php
				$inf=$informe->where('id_cliente',$cliente);
				
				$plantas=$inf->pluck('id_planta')->unique();
			@endphp	
			@if($plantas->count()>0)
				<tr>
					<td colspan="3" >
						
						@include('resources.cabecera_cliente_informes')
					</td>
				</tr>
				<tr>
					<td colspan="3">
						<h4 class="text-muted">Periodo {!! beauty_fecha($f1,0) !!} <i class="mdi mdi-arrow-right-bold"></i> {!! beauty_fecha($f2,0) !!}</h4>
					</td>
				</tr>
			@endif
			@if($r->output=='excel')
				@foreach($plantas as $planta)
					@php
						$datos_planta=App\Models\plantas::find($planta);
						$obj=new \stdClass();
						$obj->data=[];
						$puntos=$inf->where('id_planta',$planta)->map(function($item,$key) use ($obj,$datos_planta){
							$props=new \stdClass();
							$props->x=round($datos_planta->width*$item->offset_left/100)+round($datos_planta->width/100*$datos_planta->factor_puestow);
							$props->y=round($datos_planta->height*$item->offset_top/100)+round($datos_planta->height/100*$datos_planta->factor_puestoh);
							$props->value=$item->cuenta;
							$obj->data[]=$props;
						});
					@endphp
					<tr>
						<td colspan="3">PLANTA {{ $datos_planta->des_planta }}</td>
					</tr>
					<tr>
						<th>x</th><th>y</th><th>value</th>
					</tr>
					@foreach($obj->data as $punto)
						<tr>
							<td>{{ $punto->x }}</td><td>{{ $punto->y }}</td><td>{{ $punto->value }}</td>
						</tr>
					@endforeach
				@endforeach
			@endif
		</tbody>
	</table>
	@if($r->output=="pdf" || $r->output=="pantalla")
        <div class="card">
            <div class="card-body">
                @foreach($plantas as $planta)
					@php
						$datos_planta=App\Models\plantas::find($planta);
						$obj=new \stdClass();
						$obj->data=[];
						$puntos=$inf->where('id_planta',$planta)->map(function($item,$key) use ($obj,$datos_planta){
							$props=new \stdClass();
							$props->x=round($datos_planta->width*$item->offset_left/100)+round($datos_planta->width/100*$datos_planta->factor_puestow);
							$props->y=round($datos_planta->height*$item->offset_top/100)+round($datos_planta->height/100*$datos_planta->factor_puestoh);
							$props->value=$item->cuenta;
							$obj->data[]=$props;
						});
					@endphp
					<div class="card border-dark mb-3">
						<div class="card-header bg-gray">
							<h3 >{{ $datos_planta->des_planta }}</h3>
						</div>
						<div class="card_plano">
							<style>
								<style type="text/css">
								.mapa{{ $planta }} {
									background-image: url("{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$datos_planta->img_plano) }}");
								}
								</style>
							</style>
							<div id="wrapper{{ $planta }}">
								<div id="mapa{{ $planta }}" class="mapa{{ $planta }}" style="width: {{ $datos_planta->width }}px; height: {{ $datos_planta->height }}px" >
									<img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$datos_planta->img_plano) }}" id="img_fondo{{ $datos_planta->id_planta }}">
								</div>
							</div>
							<script>
								var config = {
									container: document.querySelector('#mapa{{ $planta }}'),
									radius: 15,
									maxOpacity: 1,
									minOpacity: 0,
									blur: .75
								};
								var heatmapInstance{{ $planta }} = h337.create(config);
								heatmapInstance{{ $planta }}.setData({!! str_replace('"','',json_encode($obj)) !!});
								//console.log(heatmapInstance{{ $planta }});
							</script>
						</div>
					</div>

                   
                    
                @endforeach
            </div>
        </div>
	@endif
	

@endforeach
<script>

	$('#resumen_informe').html(" {{ $cnt_fechas }} Dias | {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('heatmap');
</script>
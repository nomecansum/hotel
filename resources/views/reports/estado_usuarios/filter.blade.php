@php
	use Carbon\Carbon;
	($total = 0); 
	$clientes=$informe->pluck('id_cliente')->unique();
	$usuarios=$informe->pluck('id')->unique();
	$cnt_clientes=$informe->pluck('id_cliente')->unique()->count();
	$nombre_informe="Informe de estado de usuarios";
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
		$puestos=DB::table('puestos')->where('id_cliente',$cliente)->get();
		$plantas=DB::table('plantas')->where('id_cliente',$cliente)->get();
		$turnos=DB::table('turnos')
			->join('turnos_usuarios','turnos_usuarios.id_turno','=','turnos.id_turno')
			->where('turnos.id_cliente',$cliente)
			->get();
		$plantas_usuario=DB::Table('plantas_usuario')->get();
		$filas=0;
	@endphp
	@if($informe->count()>0)
		<tr>
			<td colspan="5" >
				@include('resources.cabecera_cliente_informes')
			</td>
		</tr>
		
	@endif

		<tr>
			{{-- <th>e-mail</th> --}}
			<th @if($r->output=="excel") style="background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Usuario</th>
			<th class="text-center"  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Plantas</th>
			<th class="text-center"  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Puesto asignado</th>
			<th class="text-center"  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Reserva</th>
			<th class="text-center"  @if($r->output=="excel") style="text-align: center; background-color: #cccccc; font-size: 16px; font-weight: bold" @endif>Turno</th>
		</tr>

	@foreach ($inf as $dato)
		@php
			$pu=$plantas_usuario->where('id_usuario',$dato->id)->pluck('id_planta')->toArray();
			$pl=$plantas->wherein('id_planta',$pu)->pluck('abreviatura')->toArray();
			$tu=$turnos->where('id_usuario',$dato->id)->pluck('des_turno')->toArray();
			$res=$reservas->where('id_usuario',$dato->id)->pluck('cod_puesto')->toArray();
	
			$data=json_decode($dato->list_puestos_preferidos);
			$preferidos=Collect($data)->where('tipo','pu');
			$puestos_preferidos=$preferidos->pluck('id')->toArray();
			$preferidos=Collect($data)->where('tipo','zo');
			$zonas_preferidas=$preferidos->pluck('text')->toArray();
			$puesto_pref_usuario="";
			foreach($puestos_preferidos as $pp){
				if($puestos->where('id_puesto',$pp)->first()!=null){
					$puesto_pref_usuario=$puestos->where('id_puesto',$pp)->first()->cod_puesto;
				}
			}
			//Vamos a ver si se tiene que mostrar la linea en base al tipo de visualizacion
			//E: Errores, C: Correctos, T: Todos
			$mostrar=false;
			switch ($r->mostrar) {
				case 'T':
					$mostrar=true;
					break;
				
				case 'E':
					if($res===null || $res==[]){
						$mostrar=true;
					} else {
						if($puesto_pref_usuario!="" && $puesto_pref_usuario!=implode("",$res)){
							$mostrar=true;
						}
					}
					break;
				case 'C':
					if($res===null  || $res==[]){
						$mostrar=false;
					} else {
						if($puesto_pref_usuario==implode("",$res) || ($puesto_pref_usuario=="" && $res!==null)){
							$mostrar=true;
						}

					}
					break;

				default:
					$mostrar=false;
					break;
			}		
		@endphp

			@if($mostrar)
				<tr>
					<td>{{ $dato->name }}</td>
					<td>
						{{implode(", ",$pl)}}
					</td>
					<td>
						{{ $puesto_pref_usuario }}
					@if($zonas_preferidas)
						{!! implode("<br> ",$zonas_preferidas) !!}
					@endif
					</td>
					<td>
						{!! implode("<br> ",$res) !!}
					</td>
					<td>
						{!! implode("<li> ",$tu) !!}
					</td>
				</tr>
				@php
					$filas++;
				@endphp
			@endif
	@endforeach
	@if($r->output=="pdf" || $r->output=="excel")
		</tbody>
	</table>
	@endif
	@if($r->output=="pdf")<div style="page-break-after:always;"></div>@endif
@endforeach
<script>
	$('#resumen_informe').html(" {{ $filas }} Filas  | {{ round($executionTime,2) }} seg ");
	$('#request_orig').val('{!! json_encode($r->all()) !!}');
	$('#controller').val('puestos');
</script>
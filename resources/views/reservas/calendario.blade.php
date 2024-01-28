@php
$month = isset( $backMonth ) ? $backMonth : date( 'Y-n' );
$carbon = new Carbon\Carbon;
$week = 1;
for ( $i=1;$i<=date( 't', strtotime( $month ) );$i++ ) {

	$day_week = date( 'N', strtotime( $month.'-'.$i )  );

	$calendar[ $week ][ $day_week ] = $i;
	if ( $day_week == 7 )
		$week++;
}

$leyenda = [];
$color = "silver";
$meses = ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
$rand=\Str::random(10);
$total_puestos=DB::table('puestos')->where('id_cliente',Auth::user()->id_cliente)->where('mca_reservar','S')->count();
@endphp
<style>
	.calendar-badge {
		position: absolute; font-size: 8px; font-weight: 700; color: #fff;display: block; width: 14px; height: 14px; text-align: center; border-radius: 64px; top: 5px; right:5px; cursor: pointer;
	}
	.popover-content, .popover-title {
		font-size: 12px;
	}
	.popover-content ul {padding: 0;padding-left:15px; margin-bottom:0;}
	/* .popover-content ul li{font-size: 10px; margin-bottom: 3px; border-bottom: 1px solid #eee} */
	.bloque{
		left: 40px;
	}
	.background {
		position: absolute;
		top: 0;
		left: 0;
		bottom: 0;
		right: 0;
		z-index: -1;
		overflow: hidden;
		}
	.table_calendar {
		border-spacing: 10px !important;
		border-collapse: separate !important;
		}


	.celda{
		max-height: 114px !important;
		overflow-y: hidden !important;
	}

	.Cal-tooltip .tooltip-inner {
		white-space:pre-wrap;
	}

	/* .table>:not(caption)>*>*{
		padding: 0;
	} */
</style>
{{-- <div class="card-header">
	<table class="table table-calendar mb0">


	</table>
</div> --}}

<div class="card-body p-0">
	<table class="table table-calendar mb0 rounded w-100 table_calendar" style="border: 1px solid #f2f7f8" >
		<thead>
			<tr class="">
				<th>
					<a data-month="{{ $month }}" data-action="sub" class="changeMonth mt-1" style="float:left; font-size: 2em; cursor: pointer;"> <i class="fas fa-arrow-left"></i> </a>
				</th>
				<th class="text-center" colspan="5" width="80%" style="font-size: 2em; font-weight: 1000">
					{{trans('strings.'.$meses[$carbon->parse($month)->format('n')-1]).' '. ucwords($carbon->parse($month)->format('Y')) }}
				</th>
				<th>
					<a data-month="{{ $month }}" data-action="add" class="changeMonth mt-1" style="float:right; font-size: 2em; cursor: pointer;"> <i class="fas fa-arrow-right"></i> </a>
				</th>
			</tr>
			<tr style="background-color: #ced1cc; color: #737887; border: 1px solid #f2f7f8; border-radius: 3px 3px 0 0; text-align: center; font-size: 12px; border-spacing: 1px;">
				<th style="width: 14.28%" class="text-center table_head">{{ trans('strings.lunes') }}</th>
				<th style="width: 14.28%" class="text-center table_head">{{ trans('strings.martes') }}</th>
				<th style="width: 14.28%" class="text-center table_head">{{ trans('strings.miercoles') }}</th>
				<th style="width: 14.28%" class="text-center table_head">{{ trans('strings.jueves') }}</th>
				<th style="width: 14.28%" class="text-center table_head">{{ trans('strings.viernes') }}</th>
				<th style="width: 14.28%" class="text-center table_head">{{ trans('strings.sabado') }}</th>
				<th style="width: 14.28%" class="text-center table_head">{{ trans('strings.domingo') }}</th>
			</tr>
		</thead>
		<tbody>
			@foreach ($calendar as $days)
				<tr>
					
					@for ($i=1;$i<=7;$i++)
						@if (isset($days[$i]))
							@php
								$dias=$reservas->where('fec_reserva','<=',Carbon\Carbon::parse($month.'-'.$days[$i])->format('Y-m-d'))->where('fec_fin_reserva','>=',Carbon\Carbon::parse($month.'-'.$days[$i])->format('Y-m-d'))->all();
								$festivo=$festivos_mes->where('date',Carbon\Carbon::parse($month.'-'.$days[$i])->format('Y-m-d'))->first()->festivo;
							@endphp
					
							
							@php
								$dia_pasado=Carbon\Carbon::parse($month.'-'.$days[$i]) < Carbon\Carbon::now()->format('Y-m-d');	

								if($festivo==1){
									$color = '#ffa69e';
									$borde="border: 2px solid #999";
									$title="";
									$estado="festivo";
									$noclickable=1;
								} else if($dia_pasado){
									$color="#dedede";
									$borde="";
									$title="";
									$estado="";
									$noclickable=1;
								} else if(count($dias)>0){
									$color="#edf5e0";
									$borde="";
									$title="";
									$estado="ocupado";
									$noclickable=0;
								} else {
									$color = '#fff';
									$borde="border: 2px solid #999";
									$title="";
									$estado="vacio"; 
									$noclickable=0;
								}
								if(Carbon\Carbon::parse($month.'-'.$days[$i])->format('Y-m-d')==Carbon\Carbon::now()->format('Y-m-d')){
									$borde="border: 3px solid #1e90ff";
								}

								
							@endphp
							{{--  data-tooltip-content="#tooltip_content{{$carbon->parse($actual->fecha)->format('d-m-Y')}}"  --}}
							
							<td style="background-color: {{$color}};  color: #999; border-radius: 12px; {{ $borde }}; "  class="dia text-center  pt-3 @if(!$dia_pasado)td_calendar @endif {{ $estado }}" data-past="{{ $noclickable }}" data-fecha="{{ Carbon\Carbon::parse($month.'-'.$days[$i])->format('Y-m-d') }}" data-fechaID="{{ Carbon\Carbon::parse($month.'-'.$days[$i])->format('Ymd') }}" id="TD{{ Carbon\Carbon::parse($month.'-'.$days[$i])->format('Ymd') }}" data-toggle="tooltip" data-container="body" data-placement="top" data-original-title="{!!$title!!}" >
                                <div class="font-bold" style="font-size: @desktop  2em; @elsedesktop  1em; @enddesktop; font-weigth: 400;position absolute; color: #fff; -webkit-text-stroke: 1px #999; z-index: 1" >{{ isset($days[$i]) ? $days[$i] : '' }}</div>
								<div style="color: #fff; cursor: pointer;" class="text-start">
									{{-- @foreach($dias as $dia)
										@php
											$icono=$dia->val_icono;
											$ic_color=$dia->val_color;
											$descrip=nombrepuesto($dia);
											//$title=Carbon\Carbon::parse($dia->fec_reserva)->format('d/m/Y').chr(13)." Puesto: ".$descrip." - Edificio: ".$dia->des_edificio." - Planta: ".$dia->des_planta;	
										@endphp
									@if($dia)
									<div class="des_evento mb-1 text-nowrap  text-center cal-tooltip" style="font-size:@desktop  1.2vw; @elsedesktop  12px; @enddesktop color:#555;" title="{!! $dia->cod_puesto !!}<br> @if(isset($dia->name))<i class='fa-regular fa-user'></i> {{$dia->name}} @else <i class='fa-regular fa-clock'></i>  {{Carbon\Carbon::parse($dia->fec_reserva)->format('H:i')}} -> {{Carbon\Carbon::parse($dia->fec_fin_reserva)->format('H:i')}} @endif">
										@if($icono!="") <i class="{{ $icono }}" style="color: {{ $ic_color }};"></i> @endif 
										@desktop {{ str_replace(' ','',$dia->des_puesto) }}  @enddesktop
									</div>
									@endif
									@endforeach --}}
									<div class="des_evento mb-1 text-nowrap  text-center cal-tooltip" style="font-size:@desktop  1.2vw; @elsedesktop  12px; @enddesktop color:#555;" title="">
										
										@php
											$pct_ocupacion=100*count($dias)/$total_puestos;
										@endphp
										{{ count($dias)>0?count($dias):'' }} <span class="text-muted font-14">{{ round($pct_ocupacion) }}%</span>
										@if($pct_ocupacion>0)
											<div class="progress" style="height: 5px; margin-top: 5px;">
												<div class="progress-bar" role="progressbar" style="width: {{ $pct_ocupacion }}%; background-color: #{{ color_porcentaje_inv($pct_ocupacion,"hex") }};" aria-valuenow="{{ $pct_ocupacion }}" aria-valuemin="0" aria-valuemax="100"></div>
											</div>
										@endif
									</div>
									
								</div>
							</td>
						@else
							<td></td>
						@endif
					@endfor
				</tr>
			@endforeach
		</tbody>
	</table>
</div>

<script>

	$(function(){
		$('#detalle_horario').hide();
	})

	const calTriggerList{{ $rand }} = [...document.querySelectorAll( '.cal-tooltip' )];
    const caltipList{{ $rand }} = calTriggerList{{ $rand }}.map( tooltipTriggerEl => new bootstrap.Tooltip( tooltipTriggerEl,{html: true} ));
	
	fechacal="{{ $month }}";

	$('.td_calendar').click(function(){
		spshow('spin');
		if($(this).data('past')==0){
			$('#editorCAM').load("{{ url('/reservas/create/') }}/"+$(this).data('fecha'), function(){
				animateCSS('#editorCAM','bounceInRight');
				sphide('spin');
				$('body, html').animate({scrollTop : 0}, 500);
			});
		} else {
			toast_warning('Reservas','No se puede reservar en un festivo o fin de semana si no lo habilita en su perfil');
			sphide('spin');
		}
		
	})


	
</script>

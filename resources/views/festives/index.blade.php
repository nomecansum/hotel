@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Días festivos</h1>
@endsection
@section('styles')
	<link href="{{url('/plugins/js-year-calendar')}}/js-year-calendar.min.css" rel="stylesheet" media="all">
	<link href="{{url('/plugins/flag-icon-css/css/flag-icon.min.css')}}" rel="stylesheet" media="all">
	<style type="text/css">
		.calendar{
			overflow-x: visible;
		}

		.day{
			border-radius: 5px;
		}

		.rounded_panel{
			border-radius: 10px;
		}

	</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">parametrizacion</li>
		<li class="breadcrumb-item">personas</li>
        <li class="breadcrumb-item active"><a href="{{url('/festivos')}}">dias festivos</a></li>
	</ol>
@endsection

@section('content')
	@php
		Carbon\Carbon::setLocale(session('lang'));
		setlocale(LC_TIME, 'Spanish');
		$anio = Carbon\Carbon::now()->year;	

	@endphp

	<div class="row botones_accion">
        <div class="col-md-4">

        </div>
        <div class="col-md-4">
            <br>
        </div>
        <div class="col-md-4 text-end">

			@if(checkPermissions(['Festivos'],['C']))
				<a href="#new-calendar" data-toggle="modal" id="btn_nuevo_cal" class="btn btn-secondary mr-2" title="Nueva planta">
					<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
					<span>Nuevo calendario</span>
				</a>
			@endif
			@if(checkPermissions(['Festivos'],['C']))
				<a href="#" id="btn_nuevo_festivo" onclick="editar_festivo(0);"  class="btn btn-success" title="Nuevo festivo">
					<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
					<span>Nuevo</span>
				</a>
			@endif

        </div>
    </div>

	<div class="row">
		<div class="col-12" id="edit_festivo"></div>
	</div>
{{-- KPI y tabla --}}
	<div class="card">
		<div class="card-header">
			<h3 class="card-title">Festivos</h3>
		</div>
		<div class="card-body">
			{{-- Fila de KPI --}}
			<div id="fila_kpi">
				@include('festives.fill_kpi_festivos')
			</div>
			{{-- Tabla de los festivos --}}
			<div class="row">
				<div class="col-12 formfestivo">
					<div class="card">
						<div class="card-body">
							<div id="all_toolbar" class="ml-3 d-flex">
								<div class="col-8 text-nowrap" style="padding-top: 10px;">
									<div class="spinner-border text-success float-left" role="status" style="margin-right: 10px; display: none" id="spinner"><span class="sr-only">{{trans('strings.espere')}}...</span></div>
									{{trans('strings.festives')}} año
								</div>
								<div class="col-6">
									<div class="col">
										<select name="year" class="form-control form-control" id="cmb_anio">
											@if(empty($minimo) && empty($maximo))
												<option selected value="{{ Carbon\Carbon::now()->format('Y') }}">{{ Carbon\Carbon::now()->format('Y') }}</option>
											@else
												@for ($i = $minimo; $i <= $maximo; $i++)
													<option {{ $i==Carbon\Carbon::now()->format('Y') ? 'selected' : '' }} value="{{$i}}">{{$i}}</option>
												@endfor
											@endif
										</select>
									</div>
								</div>
								
							</div>
							<div class="table-responsive m-t-40">
		
								<table id="myTableFes"  data-toggle="table" data-mobile-responsive="true"
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
											<th style="width: 50%">{{trans('strings._employees.festives.name')}}</th>
											<th style="width: 8%">{{trans('strings._employees.festives.date')}}</th>
											@admin @desktop<th>Cliente</th>@enddesktop @endadmin
											<th class="noExport">{{trans('strings.type')}}</th>
										</tr>
									</thead>
									<tbody id="filter-results">
										@include('festives.fill_tabla_festivos')
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>
			</div>
			{{-- Fin tabla festivos --}}
		</div>
	</div>
	<div class="card">
		<div class="card-header">
			<h3 class="card-title"></h3>
		</div>
		<div class="card-body">
			{{-- Calendario --}}
			<div class="row">
				<div class="col-12">
					<div class="card">
						<div class="card-body">
							<div id="calendar-festives" style="overflow: show"></div>
						</div>
					</div>
				</div>
			</div>
			{{-- Fin calendario --}}
		</div>
	</div>

	{{-- Modales --}}

	<div class="modal fade" id="full-calendar">
		<div class="modal-dialog modal-lg">
			<div class="modal-content">
				<div id="calendar-festives">

				</div>
			</div>
		</div>
	</div>

	<div class="modal fade" id="new-calendar">
		<div class="modal-dialog">
			<div class="modal-content">
				<form action="{{ url('festives/calendar')}}" class="form-ajax" method="POST" id="new-calendar-form">
					{{csrf_field()}}
					<div class="modal-header">
						<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
						<h1 class="modal-title text-nowrap">Crear calendario </h1>
						<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
							<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
						</button>
					</div>    

					<div class="modal-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="">Para el año</label>
									<input type="number" class="form-control" value="{{date('Y')+1}}" name="to" min="{{date('Y')}}">
								</div>
							</div>

							<div class="col-sm-6">
								<div class="form-group">
									<label for="">A partir del año</label>
									<select name="from" class="form-control">
										<option value="{{date('Y')}}">{{date('Y')}}</option>
										<option value="{{date('Y')-1}}">{{date('Y')-1}}</option>
									</select>
								</div>
							</div>
							<input type="hidden" name="cod_cliente" value="{{ Auth::user()->id_cliente }}">
							<div class="col-sm-12">
								<div class="alert alert-warning not-dismissable">
									<h3 class="text-warning"><i class="fa fa-exclamation-triangle"></i> Atención</h3> Si ya existiera un nuevo festivo para ese año se borrará. ¿Estas seguro?
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" onclick="checkNewCalendar()" class="btn btn-info mr-auto">GENERAR</button>
						<button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">{{trans('strings.cancel')}}</button>
					</div>
				</form>
			</div>
		</div>
	</div>

@endsection

@section('scripts')
<script src="{{url('/plugins/js-year-calendar')}}/js-year-calendar.min.js"></script>
<script src="{{url('/plugins/js-year-calendar')}}/js-year-calendar.es.js"></script>
<script>
	$('.configuracion').addClass('active active-sub');
    $('.menu_parametrizacion').addClass('active active-sub');
	$('.menu_usuarios').addClass('active active-sub');
	$('.festivos').addClass('active');

var anio = {{ Carbon\Carbon::now()->year }};

	calendar = new Calendar('#calendar-festives',{
		enableContextMenu: true,
        enableRangeSelection: true,
        language: 'es',
        startYear: {{ $anio }},
        mouseOnDay: function(e) {
            if(e.events.length > 0) {
                var content = '';
                
                for(var i in e.events) {
                    content += '<div class="event-tooltip-content">'
                                    + '<div class="event-name">' + e.events[i].name + '</div>'
                                    // + '<div class="event-location">' + e.events[i].location + '</div>'
                                + '</div>';
                }
            
                $(e.element).popover({ 
                    trigger: 'manual',
                    container: 'body',
                    html:true,
                    content: content
                });

                $(e.element).popover('show');
                $('.event-name').css('color', e.events[i].color);
            }
        },
        mouseOutDay: function(e) {
            if(e.events.length > 0) {
                $(e.element).popover('hide');
            }
        },
        renderEnd: function(e){
                if(e.currentYear!=$('#cmb_anio').val()){
                    $('#spinner').show();
					$('#cmb_anio').val(e.currentYear);
					$('#frm_anio').submit();
                }
            },
        dataSource: [
        	@foreach ($festives_cal as $_f)
            {
                name: '{{$_f->des_festivo}} {{$_f->mca_nacional == 'S' ? '[Nacional]' : ''}}',
                startDate: new Date('{{Carbon\Carbon::parse($_f->val_fecha)->format("Y-m-d")}} 00:00:00'),
				@if (strlen($_f->cod_centro>0))
					location : "{{ implode(",",App\Models\edificios::wherein('id_edificio',explode(",",$_f->cod_centro))->pluck('des_edificio')->toArray()) }}",
					color: '#f06292',
				@elseif(strlen($_f->cod_provincia>0))
					location : "{{ implode(",",App\Models\provincias::wherein('id_prov',explode(",",$_f->cod_provincia))->pluck('nombre')->toArray()) }}",
					color: '#03a9f4',
				@elseif(strlen($_f->cod_region>0))
					location : "{{ implode(",",App\Models\regiones::wherein('cod_region',explode(",",$_f->cod_region))->pluck('nom_region')->toArray()) }}",
					color: '#26a69a',
				@elseif	($_f->mca_nacional == 'S')
					location : "{{ implode(",",App\Models\paises::wherein('id_pais',explode(",",$_f->cod_pais))->pluck('nom_pais')->toArray()) }}",
					color: '#f44336',
				@endif
				endDate: new Date('{{Carbon\Carbon::parse($_f->val_fecha)->format("Y-m-d")}} 00:00:00')
            },
        	@endforeach
        	{
        		name: 'HOY',
        		startDate: new Date('{{Carbon\Carbon::today()}}'),
                endDate: new Date('{{Carbon\Carbon::today()}}'),
                color: 'blue'
        	}
        ]
	});
    calendar.setStyle('background');

	$('#cmb_anio').change(function(event){
		calendar.setYear($(this).val());
	});


	var checkNewCalendar = ()=>
	{
		let _to = Number($('[name="to"]').val());
		let _from = Number($('[name="from"]').val());

		///console.log(_from >= _to,_to,_from)

		if (_from >= _to) {
			sw=Swal.fire({
					title: "{{trans('strings._employees.festives.error')}}",
					footer: '<img src="/img/Mosaic_brand_20.png" class="float-right">',
					allowEscapeKey: true,
					allowOutsideClick: true,
					timer: 90000
					});
		}else{
			$('#new-calendar-form').trigger('submit');
		}
	}

	function del(id){
		$('#eliminar-festivo-'+id).modal('show');
	}

	function editar_festivo(id){

		if(id!=0){
			try{
				stopPropagation();
			} catch(e){}

		}

		$.ajax({
            url: "{{ url('festives/edit') }}/" + id + "/" + $("#cod_cliente").val()
        })
        .done(function(data) {
			//console.log(data);
			$('#edit_festivo').html('');
			$('#edit_festivo').html(data);
			$('#edit_festivo').show();
			animateCSS('#edit_festivo','bounceInRight');
        })
        .fail(function(err) {
			console.log(err);

        })
	}

	$('#frm_anio').submit(function(event) {
		event.preventDefault();

		$.post($(this).attr('action'), $(this).serializeArray(), function(data, textStatus, xhr) {
			$('#filter-results').html(data);
			$('#spinner').hide();
		});
	});



	
	calendar.setStyle('background');

	document.querySelector('#calendar-festives').addEventListener('yearChanged', function(e) {
		 console.log("New year selected: " + e.currentYear);
		 $.post("{{url('/festives/kpi-filter')}}", {_token:'{{csrf_token()}}', year: e.currentYear, id_cliente:'{{Auth::user()->id_cliente}}'}, function(data, textStatus, xhr) {
		// 		console.log(data);
		 		$('#fila_kpi').html(data);
		 		$('#spinner').hide();
		});
		 $.post("{{url('/festives/tabla-filter')}}", {_token:'{{csrf_token()}}', year: e.currentYear, id_cliente:'{{Auth::user()->id_cliente}}'}, function(data, textStatus, xhr) {
				//console.log(data);
		 		$('#filter-results').html(data);
		 		$('#spinner').hide();
		});
	})


	$('#check-calendar').click(function(event) {
		if (($('#baseurl').val() != "" && $('#baseyear').val() != "")) {
			window.open($('#baseurl').val()+$('#baseyear').val(),'_blank');
		}
	});
</script>
@stop

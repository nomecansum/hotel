@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Eventos</h1>
@endsection

@section('styles')

 <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
 <style type="text-css">
	.select2-container .select2-search__field {
		width: 100% !important;
	}
 </style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item">Utilidades</li>
        <li class="breadcrumb-item"><a href="{{url('/events')}}">eventos</a></li>
        <li class="breadcrumb-item active">regla de evento {{ isset($reglas->nom_regla)?$reglas->nom_regla:'' }}</li>
    </ol>
@endsection

@php
	Carbon\Carbon::setLocale(session('lang'));
    setlocale(LC_TIME, 'Spanish');
    if(!isset($cod_regla)){
        $cod_regla=0;
    }

    $clientes=DB::table('clientes')
        ->wherein('clientes.id_cliente',clientes())
        ->get();

@endphp

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">{{ isset($reglas->nom_regla)?$reglas->nom_regla:'' }}</h3>
    </div>
    <div class="card-body">
        <form action="{{url(config('app.carpeta_asset').'/save')}}" method="POST" class="form-ajax" id="formcomando">
            {{csrf_field()}}
            <input type="hidden" name="cod_regla" value="{{ $cod_regla }}">
            <div class="row">
                <div class="col-md-12">
                    <div class="card ">
                        <div class="card-body">
                            {{-- <h4 class="card-title">Nueva regla</h4> --}}
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="form-group">
                                        <label  for="nom_regla">{{ __('general.nombre') }}</label><br>
                                        <input required type="text" name="nom_regla" id="nom_regla" class="form-control" style="height: 47px" value="{{ isset($reglas->nom_regla)?$reglas->nom_regla:'' }}">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <div class="form-group"  >
                                            <label>{{ __('general.propietario') }}
                                                @include('resources.spin_puntitos',['id_spin'=>'spin_cli'])
                                            </label>
                                            <select name="cod_propietario"  required  class="form-control" id="cod_propietario" lang="{{ config('app.lang', 'es') }}">
                                                @foreach ($clientes as $c)
                                                    <option value="{{$c->id_cliente}}" @if(isset($reglas) && $c->id_cliente=$reglas->cod_cliente) selected @endif>{{$c->nom_cliente}}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">{{ __('general.comando') }}</label>
                                        <select name="comando" id="comando"class="form-control select2" style="width: 100%" placeholder="{{ __('eventos.seleccione_un_comando') }}">
                                            @php
                                                $files = File::allFiles(resource_path('views/events/comandos'));
    
                                            @endphp
                                            <option value=""></option>
                                            @foreach ($files as $file)
                                            {{-- {{ $elementos->url_elemento==basename($file) ? 'selected' : '' }} --}}
                                            <option style="text-transform: uppercase" {{ isset($reglas->nom_comando)&&$reglas->nom_comando==basename($file)?'selected':'' }}  value="{{ basename($file) }}">{{ str_replace(".php","",str_replace("_"," ",basename($file))) }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-8 mt-4 text-muted" id="txt-desc" style="font-weight: 400;">
                                    {!! isset($descripcion)?$descripcion:'' !!}
                                </div>
                            </div>
                            <div class="row mt-2" id="row_intervalo" style="display: none">
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label for="">{{ __('general.intervalo') }} (min)</label><br>
                                        <input required type="number" name="intervalo" id="intervalo" class="form-control" value="{{ isset($reglas->intervalo)?$reglas->intervalo:'' }}" min="2" max="99999">
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label  for="cod_grupo">{{ __('general.grupo') }}</label><br>
                                        <select class="form-control"  name="cod_grupo" id="cod_grupo">
                                            <option value=""> </option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="I"?'selected':''}} value="I">{{ __('general.minuto') }}</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="H"?'selected':''}} value="H">{{ __('general.hora') }}</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="D"?'selected':''}} value="D">{{ __('general.dia') }}</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="S"?'selected':''}} value="S">{{ __('general.semana') }}</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="Q"?'selected':''}} value="Q">{{ __('general.quincena') }}</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="A"?'selected':''}} value="A">{{ __('general.grupo') }} A</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="B"?'selected':''}} value="B">{{ __('general.grupo') }} B</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="C"?'selected':''}} value="C">{{ __('general.grupo') }} C</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="D"?'selected':''}} value="D">{{ __('general.grupo') }} D</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="E"?'selected':''}} value="E">{{ __('general.grupo') }} E</option>
                                            <option {{ isset($reglas->cod_grupo) && $reglas->cod_grupo=="F"?'selected':''}} value="F">{{ __('general.grupo') }} F</option>
                                        </select>
    
                                    </div>
                                </div>
                                <div class="col-md-1" style="padding-top: 30px">
                                    <div class="form-check">
                                        <input name="mca_activa"  id="mca_activa" value="S" {{ isset($reglas->mca_activa)&&$reglas->mca_activa=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                                        <label class="form-check-label" for="mca_activa">Activa</label>
                                    </div>
                                </div>
                                <div class="col-md-5 d-flex flex-row p-t-20">
                                    <label class="mt-2 mr-1" for="" title="{{ __('eventos.hint_tiempo_espera') }}">{{ __('eventos.intervalo_de_espera') }}</label>
                                    <input required type="number" name="int_espera" id="int_espera" class="form-control col-2" value="{{ $reglas->nomolestar??'' }}" min="0" max="365">
                                    <select class="form-control col-3"  name="tip_espera" id="tip_espera">
                                        <option value="M" {{ isset($reglas->tip_nomolestar) && $reglas->tip_nomolestar=='M'? 'selected':'' }}>{{ __('general.minutos') }}</option>
                                        <option value="H" {{ isset($reglas->tip_nomolestar) && $reglas->tip_nomolestar=='H'? 'selected':'' }}>{{ __('general.horas') }}</option>
                                        <option value="D" {{ isset($reglas->tip_nomolestar) && $reglas->tip_nomolestar=='D'? 'selected':'' }}>{{ __('general.dias') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-1 mt-4 text-end">
                                    <button type="submit" class="btn btn-primary btn_form float-right">{{trans('general.submit')}}</button>
                                </div>
                                <div class="col-md-1 mt-4 text-end">
                                    <a class="btn btn-secondary text-white btn_probar"> Probar</a>
                                </div>
                            </div>
    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row" id="div_regla" style="display:none">
        <div class="col-md-12">
            <div class="card totales_resultados  mb-3" >
                <h4 class="mt-2 ml-2 expandir"  data-div="param_regla">{{ __('eventos.parametrizacion_del_comando') }} <span id="count_parametros"  class="text-light"></span> <i class="fas fa-caret-right text-info"></i></h4>
                <div class="card-body" id="param_regla"  style="display:none">

                </div>
            </div>
        </div>
    </div>

    <div class="row" id="div_prog" style="display:none">
        <div class="col-md-12">
            <div class="card  mb-3" >
                <h4 class="mt-2 ml-2 expandir"  data-div="prog_regla">{{ __('eventos.programacion_de_la_regla') }} <span id="count_programacion" class="text-light"></span> <i class="fas fa-caret-right text-info"></i></h4>
                <div class="card-body" id="prog_regla"  style="display:none">

                </div>
            </div>
        </div>
    </div>
</form>
<form action="{{url(config('app.carpeta_asset').'/acciones/param_acciones/save')}}" method="POST" class="form-ajax" id="formaccion">
    
    <div class="row" id="div_acc" style="display:none">
        <div class="col-md-12">
            <div class="card  mb-3" >
                <div class="row pt-1 pb-1">
                    <div class="col-md-4">
                        <h4 class="mt-2 ml-2 expandir"  data-div="acciones_regla" >Acciones <span id="count_acciones"  class="text-light"></span> <i class="fas fa-caret-down text-info"></i></h4>
                    </div>
                    <div class="col-md-8 text-end">
                        <div class="dropdown">
                            <a href="javascript:void(0)" class="btn float-right hidden-sm-down btn-success dropdown-toggle mr-3 pr-3" data-bs-toggle="dropdown"><i class="fas fa-plus-circle"></i> {{ ucfirst(__('general.addadir')) }}</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <a class="dropdown-item nueva_accion p-1"  id="nueva_iteracion" data-tipo="iteracion" href="javascript:void(0)"><i class="fad fa-recycle" style="width:20px"></i> {{ __('eventos.iteracion') }}</a>
                                <a class="dropdown-item nueva_accion p-1" id="nueva_accion" data-tipo="accion" href="javascript:void(0)"><i class="fad fa-shoe-prints" style="width:20px"></i> {{ __('general.accion') }}</a>
                            </div>
                        </div>
                    </div>
                </div>
                    
                <div class="card-body" id="acciones_regla"></div>
            </div>
        </div>
    </div>
</form>
    

<div class="modal fade" id="run_tarea">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h3>@include('resources.spin_puntitos',['id_spin'=>'spin_tarea','clase'=>'spin_tarea'])
                    <b>{{ __('eventos.prueba_de_comando') }} <span id="des_tarea_run"></span></b></h3>
                <button type="button" class="btn btn-info btn-xs" data-bs-dismiss="modal" aria-label="Close" >
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x"></i></span>
                </button>
            </div>
            <div class="modal-body text-left" id="log_fichero" style="height: 550px; overflow: Auto">

            </div>
            <div class="modal-footer">
                <button type="button" data-bs-dismiss="modal" class="btn btn-warning">{{ __('general.cerrar') }}</button>
            </div>
        </div>
    </div>
</div>

@endsection


@section('scripts')
    <script>
        $('.configuracion').addClass('active active-sub');
        $('.menu_utilidades').addClass('active active-sub');
        $('.eventos').addClass('active');
    </script>

<script>
    var iteracion_seleccionada=1;
    //Esto cargara los form si se esta editando la regla
    @if($cod_regla!=0)
        $.post('{{url(config('app.carpeta_asset')."/param_comando/".$cod_regla)}}', {_token: '{{csrf_token()}}', 'comando': '{{ $reglas->nom_comando }}', 'cod_regla': '{{ $reglas->cod_regla }}'}, function(data, textStatus, xhr) {
        })
        .done(function(data){
            $('#param_regla').html(data);
            $('#div_regla').show();
            $('#row_intervalo').show();
            animateCSS('#div_regla','bounceInRight');
            // //Cargar el calendario
            $('#prog_regla').load("{{ url(config('app.carpeta_asset').'/calendario/'.$cod_regla) }}");
            $('#div_prog').show();
            animateCSS('#div_prog','bounceInLeft');
            //Cargar las acciones
            $('#acciones_regla').load("{{ url(config('app.carpeta_asset').'/acciones/'.$cod_regla) }}");
            $('#div_acc').show();
            animateCSS('#div_acc','bounceInUp');
        });

    @endif;

    $('.expandir').click(function(){
        $('#'+$(this).data('div')).toggle();
        $(this).find('i').toggleClass('fas fa-caret-right fas fa-caret-down');
    })

    $('.nueva_accion').click(function(){
        tipo_accion=$(this).data('tipo');
        $.ajax({
                url: "{{ url(config('app.carpeta_asset').'/acciones/nueva/'.$cod_regla) }}/"+$(this).data('tipo')
            })
            .fail(function(err) {
                let error = JSON.parse(err.responseText);
                let html = "";
                console.log(error);
                toast_error(error.titulo,error.error);
            })
            .done(function(data) {
                console.log(data);
                toast_ok("{{ __('general.addadir') }} "+tipo_accion,tipo_accion+" {{ __('general.addadida') }}");
                $('#acciones_regla').html(data);
                $('.form_param').empty();

            });
                    //$('#acciones_regla').load("{{ url('/events/acciones/nueva/'.$cod_regla.'/accion') }}");
    });

    // $('#nueva_iteracion').click(function(){
    //     $.get('#acciones_regla').load("{{ url('/events/acciones/nueva/'.$cod_regla.'/iteracion') }}")
    // });

    $("#comando").select2({
        placeholder: "{{ __('eventos.seleccione_un_comando') }}",
        allowClear: false
    });
    $('#comando').on('select2:select', function (e) {
        var data = e.params.data;
        console.log(data);
        $('#param_regla').html('');
        $.post('{{url(config('app.carpeta_asset')."/param_comando/".$cod_regla)}}', {_token: '{{csrf_token()}}', 'comando': data.id}, function(data, textStatus, xhr) {

        })
        .done(function(data) {

            $('#param_regla').html(data);
            $('#div_regla').show();
            $('#row_intervalo').show();
            animateCSS('#div_regla','bounceInRight');
            //Cargar el calendario
            $('#prog_regla').load("{{ url(config('app.carpeta_asset').'/calendario/'.$cod_regla) }}");
            $('#div_prog').show();
            animateCSS('#div_prog','bounceInLeft');

        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            console.log(error);
            toast_error("Error",error.error);
        })
    });

    $("#cod_propietario").select2({
        placeholder: "{{ __('general.seleccione_un_cliente') }}",
        width: '100%',
        language: "es",
    });



    $('.btn_probar').click(function(){
        let form = $('#formcomando');

        let data = new FormData(form[0]);

        $.ajax({
            url: "{{ url(config('app.carpeta_asset').'/probar_comando') }}",
            type: form.attr('method'),
            contentType: false,
            processData: false,
            data: data,
        })

        .done(function(data) {
            $('#log_fichero').empty();
            if($('#comando').val()=='' || $('#comando').val()==null){
                toast_error("{{__('general.error')}}","{{__('eventos.seleccione_un_comando')}}");
                return false;
            }
            $('#run_tarea').modal('show');
            $('#des_tarea_run').html($('#comando option:selected').text());
            $('#log_fichero').html(data);
           //console.log(data);
        })
        .fail(function(err) {
            let error = JSON.parse(err.responseText);
            let html = "";
            if(error.message){
                html+=error.message;
            }
            @if(fullAccess())
                html+=error.exception+' '+'['+error.line+']';
            @endif
            console.log(error);
            $.each(error.errors, function(index, val) {
                 html += "- "+$(this)[0]+"<br>";
            });
            toast_error("{{__('general.error_general')}}",html);
        })
        .always(function() {
            console.log("complete");
        });
    })
</script>
@endsection




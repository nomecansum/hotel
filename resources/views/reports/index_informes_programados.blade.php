@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Informes programados</h1>
@endsection

@section('styles')
{{-- Aqui van los CSS adicionales que se quieran meter --}}
<style type="text/css">
    .modal-lg {
        max-width: calc(100% - 60px) !important;
    }
</style>
<link href="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Informes</li>
        <li class="breadcrumb-item active">Informes programados</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        {{-- <h2 class="card-title float-left">{{ trans('strings.incidents') }} </h2> --}}
                        {{-- @include('resources.combo_clientes') --}}
                        <div class="table-responsive m-t-40">
                            <table  class="table table-bordered table-condensed table-hover text-lg-center">
                                <thead>
                                    <tr>
                                        <th style="width:4%">{{trans('strings.id')}}</th>
                                        <th>Nombre</th>
                                        <th>{{trans('strings._devices.business')}}</th>
                                        <th>URL</th>
                                        <th>Periodo</th>
                                        <th style="width:10%">Creado</th>
                                        <th style="width:10%">Ultima ejec.</th>
                                        <th>Intervalo</th>
                                        <th style="width:10%">Prox ejec</th>
                                        {{-- <th>Destinatarios</th> --}}
                                    </tr>
                                </thead>
                                <tbody>

                                    @foreach ($informes as $i)
                                        <tr class="hover-this fila" data-id="{{$i->cod_informe_programado}}">
                                            <td class="text-center">{{$i->cod_informe_programado}}</td>
                                            <td>{{$i->des_informe_programado}}</td>
                                            <td>{{$i->nom_cliente}}</td>
                                            <td><a href="{{$i->url_informe}}" target="_blank"  rel="noopener noreferrer">{{$i->url_informe}}</a></td>
                                            <td >
                                                @switch($i->val_periodo)
                                                    @case(1)
                                                    Ayer
                                                        @break
                                                    @case(2)
                                                    La semana pasada
                                                        @break
                                                    @case(3)
                                                    L-V de la semana pasada
                                                        @break
                                                    @case(4)
                                                    Los ultimos 10 dias
                                                        @break
                                                    @case(5)
                                                    La ultima quincena
                                                        @break
                                                    @case(6)
                                                    El ultimo mes
                                                        @break
                                                    @case(7)
                                                    El ultimo trimestre
                                                        @break
                                                    @case(8)
                                                    El ultimo semestre
                                                        @break
                                                    @case(9)
                                                    El ultimo año
                                                        @break
                                                    @case(10)
                                                    hoy-{{ $i->dia_desde }} <i class="mdi mdi-arrow-right-bold"></i> hoy-{{ $i->dia_desde }}
                                                        @break
                                                    @default

                                                @endswitch
                                            </td>
                                            <td>{!! beauty_fecha($i->fec_creacion) !!}</td>
                                            <td>{!! beauty_fecha($i->fec_ult_ejecucion) !!}</td>
                                            <td>
                                                @switch($i->val_intervalo)
                                                    @case("1")
                                                    Diario
                                                        @break
                                                    @case("7")
                                                    Semanal
                                                        @break
                                                    @case("14")
                                                    Quincenal
                                                        @break
                                                    @case("1M")
                                                    Mensual
                                                        @break
                                                    @case("2M")
                                                    Bimensual
                                                        @break
                                                    @case("3M")
                                                    Trimestral
                                                        @break
                                                    @case("6M")
                                                   Semestral
                                                        @break
                                                    @case("1Y")
                                                    Anual
                                                        @break
                                                    @default
                                                @endswitch
                                            </td>
                                            <td class="text-center" style="position: relative;">
                                                {!! beauty_fecha($i->fec_prox_ejecucion) !!}
                                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                                        @if((checkPermissions(['Informes programados'],["W"])) || fullAccess())<a href="#edit_informe_programado" data-toggle="modal" class="btn btn-xs btn-info btn-edit" data-url="{{$i->url_informe}}" data-id="{{ $i->cod_informe_programado }}"><span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                                        @if((checkPermissions(['Informes programados'],["D"])) || fullAccess())<a href="#eliminar-usuario-{{$i->cod_informe_programado}}" data-toggle="modal" class="btn btn-xs btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
                                                    </div>
                                                </div>
                                                <div class="modal fade" id="eliminar-usuario-{{$i->cod_informe_programado}}">
                                                    <div class="modal-dialog modal-sm">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
												                <h4 class="modal-title">¿Borrar informe programado {{$i->des_informe_programado}}?</h4>
												                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
												            </div>
                                                            <div class="modal-footer">
                                                                <a class="btn btn-info" href="{{url('prog_report/delete',$i->cod_informe_programado)}}">{{trans('strings.yes')}}</a>
                                                                <button type="button" data-dismiss="modal" class="btn btn-warning">{{trans('strings.cancel')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        @php
                                            $datos=$i->val_parametros;
                                            $datos=str_replace("[]","",$datos);
                                            $datos=json_decode($datos);
                                        @endphp
                                        <tr class="bg-light-inverse" id="detalle_{{$i->cod_informe_programado}}" style="display: none">
                                            <td colspan="2" class="border-right-0 pl-5" data-toggle="mytooltip" data-placement="right" @isset($datos->document)title="{{ $datos->document }}@endisset @isset($datos->orientation){{$datos->orientation=='V'?'vertical':'horizontal'}}@endisset" >
                                                @isset($datos->document)
                                                    @switch($datos->document)
                                                        @case("pantalla")
                                                                <i class="mdi mdi-monitor mdi-36px"></i> Pantalla
                                                            @break
                                                        @case("pdf")
                                                            <i class="mdi mdi-file-pdf mdi-36px"></i> PDF
                                                            @break
                                                        @case("excel")
                                                            <i class="mdi mdi-file-excel mdi-36px"></i> Excel
                                                            @break
                                                        @default
                                                    @endswitch
                                                @endisset
                                                <br>
                                                @isset($datos->orientation)
                                                    @switch($datos->orientation)
                                                        @case("v")
                                                                <i class="mdi mdi-crop-landscape mdi-36px"></i> Horizontal
                                                            @break
                                                        @case("h")
                                                            <i class="mdi mdi-crop-portrait mdi-36px"></i> Vertical
                                                            @break
                                                        @default
                                                    @endswitch
                                                @endisset
                                            </td>
                                            <td colspan="2" class="border-right-0 border-left-0">
                                                <h4>Destinatarios</h4>
                                                @foreach(DB::table('users')->wherein('id',explode(",",$i->list_usuarios))->get() as $u)
                                                    <li>{{$u->name}} ({{$u->email}})</li>
                                                @endforeach
                                            </td>
                                            <td colspan="5" class="border-right-0 border-left-0">
                                                <h4>Detalles</h4>
                                                @isset($datos->clientes)
                                                    <i class="mdi mdi-briefcase"></i><b>Empresas:</b>
                                                        <ul>
                                                            @foreach(DB::table('clientes')->wherein('id_cliente',(array)$datos->clientes)->get() as $c)
                                                                <li style="font-size:14px">{{$c->nom_cliente}}</li>
                                                            @endforeach
                                                        </ul>

                                                @endisset

                                                @isset($datos->edificios)
                                                    <i class="mdi mdi-store"></i><b>Centros: </b>
                                                        <ul>
                                                            @foreach(DB::table('edificios')->wherein('id_edificio',(array)$datos->centros)->get() as $c)
                                                            <li style="font-size:14px">{{$c->des_centro}}</li>
                                                            @endforeach
                                                        </ul>
                                                @endisset
                                                {{-- @isset($datos->departamentos)
                                                    <i class="mdi mdi-account-multiple"></i><b>Departamentos: </b>
                                                        <ul>
                                                            @foreach(DB::table('cug_departamentos')->wherein('cod_departamento',(array)$datos->departamentos)->get() as $c)
                                                            <li style="font-size:14px">{{$c->nom_departamento}}</li>
                                                            @endforeach
                                                        </ul>
                                                @endisset
                                                @isset($datos->colectivos)
                                                    <i class="mdi mdi-folder-account"></i><b>Colectivos: </b>
                                                        <ul>
                                                            @foreach(DB::table('cug_colectivos')->wherein('cod_colectivo',(array)$datos->colectivos)->get() as $c)
                                                            <li style="font-size:14px">{{$c->des_colectivo}}</li>
                                                            @endforeach
                                                        </ul>
                                                @endisset
                                                @isset($datos->dispositivos)
                                                    <i class="mdi mdi-camera-front-variant"></i><b>Dispositivos: </b>
                                                        <ul>
                                                            @foreach(DB::table('cug_dispositivos')->wherein('cod_dispositivo',(array)$datos->dispositivos)->whereNull('cug_dispositivos.fec_borrado')->get() as $c)
                                                            <li style="font-size:14px">{{$c->nom_dispositivo}}</li>
                                                            @endforeach
                                                        </ul>
                                                @endisset 
                                                @isset($datos->empleados)
                                                    <i class="mdi mdi-account"></i><b>Empleados: </b>
                                                        <ul>
                                                            @foreach(DB::table('cug_empleados')->wherein('cod_empleado',(array)$datos->empleados)->get() as $c)
                                                            <li style="font-size:14px">{{$c->nom_empleado}} {{$c->ape_empleado}}</li>
                                                            @endforeach
                                                        </ul>
                                                @endisset --}}
                                                <i class="mdi mdi-sort-descending"></i>@isset($datos->order)Orden: {{ $datos->order }}@endisset<br>
                                                <i class="mdi mdi-format-strikethrough"></i>@isset($datos->type)Tipo: {{ $datos->type }}@endisset
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="modal fade" id="edit_informe_programado">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h1 class="modal-title text-nowrap">Editar configuración de informe programado</h1>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>

                <div class="modal-body" id="body_inf">
    
                </div>
                <div class="modal-footer">
                     <button type="submit" class="btn btn-info btn_submit" >{{trans('strings.edit')}}</button>
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection




@section('scripts')
    {{-- Normalmente los scripts aqui--}}
    <script src="{{ asset('/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js') }}"></script>
    <script src="{{ asset('plugins/typeahead-js/main.js') }}"></script>
    <script>
        $('.informes').addClass('active active-sub');
	    $('.inf_programados').addClass('active');
        
        $('.fila').click(function(){
            $('#detalle_'+$(this).data('id')).toggle();
            animateCSS('#detalle_'+$(this).data('id'),'bounceInRight');
        });

        const validateEmail = (email) => {
        return String(email)
            .toLowerCase()
            .match(
            /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
            );
        };

        $('.btn-edit').click(function(){
            event.preventDefault();
            id_informe=$(this).data('id');
            console.log(id_informe);
            $('#body_inf').load("{{ url('prog_report/edit/') }}/"+id_informe, function(){
                $('#div_programar_informe').show();
                $('#body_programar').show();
                $('#multi-usuarios').select2();
                $('#btn_programar').hide();
                $('#frm_programar_informe').attr('action',"{{ url('prog_report/save') }}");
                $('#cod_informe_programado').val(id_informe);
                console.log("Ponemos el cod_informe_programado a " + id_informe);
                $('#frm_programar_informe').submit(form_ajax_submit);
                $('.btn_fecha').click(function(){
                    simplepicker.open('#val_fecha');
                })

                const simplepicker = MCDatepicker.create({
                    el: "#val_fecha",
                    dateFormat: cal_formato_fecha,
                    autoClose: true,
                    closeOnBlur: true,
                    firstWeekday: 1,
                    customMonths: cal_meses,
                    customWeekDays: cal_diassemana
                });
                $('.edit_tag').on("keypress", function(e) {
                if (e.keyCode == 13) {
                    e.preventDefault();
                }
                });

                var data="";
                var lista_tags = new Bloodhound({
                    datumTokenizer: Bloodhound.tokenizers.obj.whitespace('name'),
                    queryTokenizer: Bloodhound.tokenizers.whitespace,
                    local: $.map(data, function (elem) {
                        return {
                            name: elem
                        };
                    })
                });
                lista_tags.initialize();


                $('.edit_tag').tagsinput({
                    typeaheadjs: [{
                        minLength: 1,
                        highlight: true,
                    },{
                        minlength: 1,
                        name: 'lista_tags',
                        displayKey: 'name',
                        valueKey: 'name',
                        source: lista_tags.ttAdapter()
                    }],
                    freeInput: true,
                    allowDuplicates: false,
                    tagClass: 'label label-primary p-3 rounded'
                });

                $('.edit_tag').on('itemAdded', function(event) {
                    $('#tags').val($(".edit_tag").tagsinput('items'));
                });

                $('.edit_tag').on('beforeItemAdd', function(event) {
                   if(validateEmail(event.item)==null){
                    event.item=null;
                    event.cancel=true;
                    event.preventDefault=true;
                   } 
                });
            });


        })

        $('.btn-danger').click(function(){
            event.preventDefault();
        })

        function prog_personalizada(){
            if($('#fechas_prog').val()=='P'){
                $('.personalizado').show();
            } else {
                $('.personalizado').hide();
            }
        }

        $('.btn_submit').click(function(){
            $('#frm_programar_informe').submit();
        })


    </script>
@endsection

@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Tipos de incidencia</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuración</li>
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">incidencias</li>
        <li class="breadcrumb-item active">tipos de incidencia</li>
        {{--  <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection

@section('content')
<div class="row botones_accion">
    <div class="col-md-4">

    </div>
    <div class="col-md-6">
        <br>
    </div>
    <div class="col-md-2 text-end">
        <div class="btn-group btn-group-sm pull-right" role="group">
            @if(checkPermissions(['Tipos de incidencia'],['C'])) 
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo edificio">
                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                    <span>Nuevo</span>
                </a>
            @endif
        </div>
    </div>
</div>
<div id="editorCAM" class="mt-2">

</div>
    @if(Session::has('success_message'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            {!! session('success_message') !!}

            <button type="button" class="close" data-dismiss="alert" aria-label="close">
                <span aria-hidden="true">&times;</span>
            </button>

        </div>
    @endif

    <div class="card">

        <div class="card-header">
            <h3 class="card-title">Tipos de incidencia</h3>
        </div>
        
        @if(count($tipos) == 0)
            <div class="card-body text-center">
                <h4>No hay datos.</h4>
            </div>
        @else
        <div class="card-body panel-body-with-table">
            <div class="table-responsive w-100" >

                <table id="tabla"  data-toggle="table" data-mobile-responsive="true"
                    data-locale="es-ES"
                    data-search="true"
                    data-show-columns="true"
                    data-show-columns-toggle-all="true"
                    data-page-list="[5, 10, 20, 30, 40, 50]"
                    data-page-size="50"
                    data-pagination="true" 
                    data-buttons-class="secondary"
                    data-show-button-text="true"
                    data-toolbar="#all_toolbar"
                    >
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th></th>
                            <th>Nombre</th>
                            <th>Tipos</th>
                            @admin<th> Procesado </th>@endadmin
                            <th>Aplica a</th>
                            @admin @desktop<th>Cliente</th>@enddesktop @endadmin
                            <th>Responsable</th>
                            
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($tipos as $tipo)
                        <tr class="hover-this">
                            <td>{{ $tipo->id_tipo_incidencia }}</td>
                            <td class="text-center"><i class="{{ $tipo->val_icono }} fa-2x" style="color:{{ $tipo->val_color }}"></i></td>
                            <td>{{ $tipo->des_tipo_incidencia }}</td>
                            <td>
                                @php
                                    $tipos_puesto = DB::table('puestos_tipos')->wherein('id_tipo_puesto',explode(',',$tipo->list_tipo_puesto))->where('id_tipo_puesto','>',0)->get();
                                @endphp
                                @foreach ($tipos_puesto as $tp)
                                <ul  style="font-size: 10px;list-style:none;">
                                    <li><i class="{{ $tp->val_icono }}" style="color: {{ $tp->val_color }}"></i> {{ $tp->des_tipo_puesto }}</li>
                                </ul>
                                @endforeach
                                
                            </td>
                            
                            @admin
                            <td style="font-size: 10px">
                                @php
                                    $procesados=DB::table('incidencias_postprocesado')->where('id_tipo_incidencia',$tipo->id_tipo_incidencia)->get();
                                    $momentos=$procesados->pluck('val_momento')->unique();
                                @endphp
                                @foreach ($momentos as $momento)
                                    @switch($momento)
                                        @case('C')
                                            Creacion:
                                            @break
                                        @case('A')
                                            Accion:
                                            @break
                                        @case('F')
                                            Cierre:
                                            @break
                                        @case('E')
                                            Estado:
                                            @break
                                        @case('R')
                                            Reapertura:
                                            @break
                                        @default
                                    @endswitch
                                    {{-- S: SMS
                                    M: EMAIL
                                    P: HTTP Post
                                    U: HTTP Put
                                    G: HTTP Get
                                    L: Spotlinker
                                    W: Web Push
                                    N: Nada (solo web) --}}
                                    <ul style="list-style:none;">
                                        @foreach ($procesados->where('val_momento',$momento) as $procesado)
                                        <li>
                                            @switch($procesado->tip_metodo)
                                                @case('S')
                                                    <i class="fa-solid fa-message-sms" style="color: {{ genColorCodeFromText("SMS") }}"></i>SMS
                                                    @break
                                                @case('M')
                                                    <i class="fa-solid fa-envelope" style="color: {{ genColorCodeFromText("SMS") }}"></i>Mail
                                                    @break
                                                @case('P')
                                                @case('U')
                                                @case('G')
                                                    <i class="fa-solid fa-browser" style="color: {{ genColorCodeFromText("WEB ") }}"></i> HTTP ({{ $procesado->tip_metodo }})
                                                    @break
                                                @case('L')
                                                    <img src="{{ url('/img/logo.png') }}" style="height:14px"/>Salas
                                                    @break
                                                @case('W')
                                                    <i class="fa-solid fa-laptop-mobile" style="color: {{ genColorCodeFromText("WPUSH") }}"></i> Push
                                                    @break
                                                @case('N')
                                                    <i class="fa-solid fa-square" style="color: {{ genColorCodeFromText("NADA") }}"></i>
                                                    @break
                                                @default
                                            @endswitch
                                        </li> 
                                        @endforeach
                                    </ul>
                                @endforeach
                            </td>
                            <td>
                                @switch($tipo->mca_aplica)
                                    @case('A')
                                        Ambos
                                        @break
                                    @case('S')
                                        Solicitudes
                                        @break
                                    @case('I')
                                        Incidencias
                                        @break
                                    @default
                                @endswitch
                            </td>
                            @endadmin
                            @admin @desktop<td>{{ $tipo->nom_cliente }}</td>@enddesktop @endadmin
                            <td style="position: relative">
                                {{ $tipo->val_responsable }}
                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                        {{-- <a href="#"  class="btn btn-primary btn_editar add-tooltip thumb"  title="Ver planta" data-id="{{ $tipo->id_edificio }}"> <span class="fa fa-eye" aria-hidden="true"></span></a> --}}
                                        <a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $tipo->id_tipo_incidencia }})" title="Editar tipo" data-id="{{ $tipo->id_tipo_incidencia }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>
                                        @if(checkPermissions(['Estados de incidencia'],['D']) && ($tipo->mca_fijo=='N' || ($tipo->mca_fijo=='S' && fullAccess())))<a href="#eliminar-planta-{{$tipo->id_tipo_incidencia}}" onclick="del({{ $tipo->id_tipo_incidencia }})" data-target="#eliminar-planta-{{$tipo->id_tipo_incidencia}}" title="Borrar tipo" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del </a>@endif
                                    </div>
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$tipo->id_tipo_incidencia}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h1 class="modal-title text-nowrap">Borrar tipo de incidencia </h1>
                                                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                                                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                                                </button>
                                            </div>    
                                            <div class="modal-body">
                                                ¿Borrar tipo de incidencia {{$tipo->des_tipo_incidencia}}?
                                            </div>
                                
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/incidencias/tipos/delete',$tipo->id_tipo_incidencia)}}">Si</a>
                                                <button type="button" data-dismiss="modal" class="btn btn-warning">No</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>
        
        @endif
    
    </div>
@endsection

@section('scripts')
    <script>
        $('.configuracion').addClass('active active-sub');
        $('.menu_parametrizacion').addClass('active active-sub');
        $('.tipos_incidencia').addClass('active active-sub');
        $('.incidencias_tipos').addClass('active');
        
        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/incidencias/tipos/edit/0') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/incidencias/tipos/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            $(window).scrollTop(0);
        }

        function del(id){
            $('#eliminar-planta-'+id).modal('show');
        }


        $('.td').click(function(event){
            editar( $(this).data('id'));
        })
    </script>
@endsection
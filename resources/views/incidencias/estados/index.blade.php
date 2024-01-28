@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Estados de incidencia</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuración</li>
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">incidencias</li>
        <li class="breadcrumb-item active">estados de incidencia</li>
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
            @if(checkPermissions(['Estados de incidencia'],['C']))    
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo estado">
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
            <h3 class="card-title">Estados de incidencia</h3>
        </div>
        
        @if(count($estados) == 0)
            <div class="card-body text-center">
                <h4>No hay datos.</h4>
            </div>
        @else
        <div class="card-body panel-body-with-table">
            <div class="table-responsive w-100" >

                <table id="tabla"  
                data-toggle="table" 
                data-mobile-responsive="true"
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
                            <th>ID</th>
                            <th></th>
                            <th>Nombre</th>
                            <th>Aplica</th>
                            @admin @desktop<th>Cliente</th>@enddesktop @endadmin
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($estados as $tipo)
                        <tr class="hover-this">
                            <td>{{ $tipo->des_estado }}</td>
                            <td class="text-center"><i class="{{ $tipo->val_icono }} fa-2x" style="color:{{ $tipo->val_color }}"></i></td>
                            <td>{{ $tipo->des_estado }}</td>
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
                            @admin @desktop<td>{{ $tipo->nom_cliente }}</td>@enddesktop @endadmin
                            <td style="position: relative">
                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                        {{-- <a href="#"  class="btn btn-primary btn_editar add-tooltip thumb"  title="Ver planta" data-id="{{ $tipo->id_edificio }}"> <span class="fa fa-eye" aria-hidden="true"></span></a> --}}
                                        <a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $tipo->id_estado }})" title="Editar tipo" data-id="{{ $tipo->id_estado }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>
                                        @if(checkPermissions(['Estados de incidencia'],['D']) && ($tipo->mca_fijo=='N' || ($tipo->mca_fijo=='S' && fullAccess())))<a href="#eliminar-planta-{{$tipo->id_estado}}" onclick="del({{ $tipo->id_estado }})"  data-target="#eliminar-planta-{{$tipo->id_estado}}" title="Borrar tipo" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del </a>@endif
                                    </div>
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$tipo->id_estado}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h1 class="modal-title text-nowrap">Borrar causa de cierre </h1>
                                                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                                                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                                                </button>
                                            </div>    
                                            <div class="modal-body">
                                                ¿Borrar causa de cierre {{$tipo->des_estado}}?
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/incidencias/estados/delete',$tipo->id_estado)}}">Si</a>
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
        $('.incidencias_estados').addClass('active');
        
        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/incidencias/estados/edit/0') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/incidencias/estados/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }

        function del(id){
            $('#eliminar-planta-'+id).modal('show');
        }

        $('.td').click(function(event){
            editar( $(this).data('id'));
        })
    </script>
@endsection
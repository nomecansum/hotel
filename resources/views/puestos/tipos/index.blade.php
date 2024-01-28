@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Tipos de puesto</h1>
@endsection

@section('styles')
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuración</li>
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">espacios</li>
        <li class="breadcrumb-item">tipos de puesto</li>
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
            @if(checkPermissions(['Tipos de puesto'],['C']))    
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo tipo de puesto">
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
            <h3 class="card-title">Tipos de puesto</h3>
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
                    data-show-toggle="true"
                    data-toolbar="#all_toolbar"
                    data-buttons-class="secondary"
                    data-show-button-text="true"
                    >
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th></th>
                            <th>Nombre</th>
                            @admin @desktop<th>Cliente</th>@enddesktop @endadmin
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($tipos as $tipo)
                        <tr class="hover-this">
                            <td>{{ $tipo->id_tipo_puesto }}</td>
                            <td class="text-center"><i class="{{ $tipo->val_icono }} fa-2x" style="color:{{ $tipo->val_color }}"></i></td>
                            <td>{{ $tipo->des_tipo_puesto }}</td>
                            
                            @admin @desktop<td>{{ $tipo->nom_cliente }}</td>@enddesktop @endadmin

                            <td style="position: relative;">
                                {{ $tipo->abreviatura }}
                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                        @if(checkPermissions(['Tipos de puesto'],['W'])) <a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $tipo->id_tipo_puesto }})" title="Editar tipo" data-id="{{ $tipo->id_tipo_puesto }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                        @if(checkPermissions(['Tipos de puesto'],['D']) && ($tipo->mca_fijo=='N' || ($tipo->mca_fijo=='S' && fullAccess())))  <a href="#eliminar-planta-{{$tipo->id_tipo_puesto}}" onclick="del({{ $tipo->id_tipo_puesto }})"  data-target="#eliminar-planta-{{$tipo->id_tipo_puesto}}" title="Borrar tipo" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del </a>@endif
                                    </div>
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$tipo->id_tipo_puesto}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h1 class="modal-title text-nowrap">Borrar tipo de puesto </h1>
                                                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                                                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                                                </button>
                                            </div>    
                                            <div class="modal-body">
                                                ¿Borrar tipo de puesto {{$tipo->des_tipo_puesto}}?
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/puestos/tipos/delete',$tipo->id_tipo_puesto)}}">Si</a>
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
	    $('.espacios').addClass('active active-sub');
        $('.puestostipos').addClass('active');
        
        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/puestos/tipos/edit/0') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/puestos/tipos/edit/') }}"+"/"+id, function(){
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
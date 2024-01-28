@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Bloqueo programado de puestos</h1>
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
        <li class="breadcrumb-item">bloqueo programado de puestos</li>
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

        
        @if(count($bloqueos) == 0)
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
                            <th>Inicio</th>
                            <th>Fin</th>
                            <th>Turnos</th>
                            <th>Puestos</th>
                            <th>Creado</th>
                            @admin @desktop<th>Cliente</th>@enddesktop @endadmin
                            <th>Motivo</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($bloqueos as $bl)
                        <tr class="hover-this">
                            <td>{{ $bl->id_bloqueo }}</td>
                            <td>{{ $bl->fec_inicio }}</td>
                            <td>{{ $bl->fec_fin }}</td>
                            <td>{{ $bl->list_turnos }}</td>
                            <td></td>
                            <td>{{ $bl->name }}</td>
                            @admin @desktop<td>{{ $bl->nom_cliente }}</td>@enddesktop @endadmin

                            <td style="position: relative;">
                                {{ $bl->des_motivo }}
                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                        @if(checkPermissions(['Tipos de puesto'],['W'])) <a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $bl->id_bloqueo }})" title="Editar tipo" data-id="{{ $bl->id_bloqueo }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                        @if(checkPermissions(['Tipos de puesto'],['D']))  <a href="#eliminar-planta-{{$bl->id_bloqueo}}" onclick="del({{ $bl->id_bloqueo }})"  data-target="#eliminar-planta-{{$bl->id_bloqueo}}" title="Borrar tipo" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del </a>@endif
                                    </div>
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$bl->id_bloqueo}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h1 class="modal-title text-nowrap">Borrar bloqueo programado de puestos </h1>
                                                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                                                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                                                </button>
                                            </div>    
                                            <div class="modal-body">
                                                ¿Borrar bloqueo programado de puestos {{$bl->id_bloqueo}}?
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/puestos/tipos/delete',$bl->id_bloqueo)}}">Si</a>
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
            $('#editorCAM').load("{{ url('/bloqueo/edit/0') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/bloqueo/edit/') }}"+"/"+id, function(){
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
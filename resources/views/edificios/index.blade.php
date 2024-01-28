@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de edificios</h1>
@endsection

@section('styles')
    <link href="{{url('/plugins/flag-icon-css/css/flag-icon.min.css')}}" rel="stylesheet" media="all">  
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">espacios</li>
        <li class="breadcrumb-item active">edificios</li>
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
        @if(checkPermissions(['Edificios'],['C']))
        <div class="btn-group btn-group-sm pull-right" role="group">
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo edificio">
                <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                <span>Nuevo</span>
            </a>
        </div>
        @endif
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
            <h3 class="card-title">Edificios</h3>
        </div>
        
        @if(count($edificiosObjects) == 0)
            <div class="card-body text-center">
                <h4>No Edificios Available.</h4>
            </div>
        @else
        <div class="card-body panel-body-with-table">
            <div class="table-responsive w-100" >

                <table id="tablaedificios"  
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
                            <th  data-sortable="true" >ID</th>
                            <th  data-sortable="true" >Nombre</th>
                            <th  data-sortable="true" >Ubicación</th>
                            @admin @desktop<th  data-sortable="true" >Cliente</th>@enddesktop @endadmin
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($edificiosObjects as $edificios)
                        <tr class="hover-this">
                            <td>{{ $edificios->id_edificio }}</td>
                            <td>{{ $edificios->des_edificio }}</td>
                            <td>{{ $edificios->nombre }}</td>
                            @admin @desktop<td>{{ $edificios->nom_cliente }}</td>@enddesktop @endadmin

                            <td style="position: relative">
                                {{ $edificios->abreviatura }}
                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                        {{-- <a href="#"  class="btn btn-primary btn_editar add-tooltip thumb"  title="Ver planta" data-id="{{ $edificios->id_edificio }}"> <span class="fa fa-eye" aria-hidden="true"></span></a> --}}
                                        @if(checkPermissions(['Edificios'],['W']))<a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $edificios->id_edificio }})" title="Editar edificio" data-id="{{ $edificios->id_edificio }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                        @if(checkPermissions(['Edificios'],['D']))<a href="#eliminar-planta-{{$edificios->id_edificio}}" data-target="#eliminar-planta-{{$edificios->id_edificio}}" onclick="del({{ $edificios->id_edificio }})" title="Borrar edificio" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
                                    </div>
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$edificios->id_edificio}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h1 class="modal-title text-nowrap">Borrar edificio </h1>
                                                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                                                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                                                </button>
                                            </div>    
                                            <div class="modal-body">
                                                ¿Borrar edificio {{$edificios->des_edificio}}?
                                            </div>
                                            
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/edificios/delete',$edificios->id_edificio)}}">Si</a>
                                                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
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
        $('.edificios').addClass('active');
        
        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/edificios/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/edificios/edit/') }}"+"/"+id, function(){
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
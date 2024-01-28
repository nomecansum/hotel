@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de plantas</h1>
@endsection

@section('styles')
    {{-- Boostrap Select --}}
    <link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">parametrizacion</li>
	    <li class="breadcrumb-item">espacios</li>
        <li class="breadcrumb-item">plantas</li>
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
        <div class="col-md-2 text-right">
            @if(checkPermissions(['Plantas'],['C']))
            <div class="btn-group btn-group-sm pull-right" role="group">
                    <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva planta">
                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                    <span>Nuevo</span>
                </a>
            </div>
            @endif
        </div>
    </div>
    <div id="editorCAM" class="mt-2">

    </div>

    <div id="editorPUESTOS" class="mt-2">

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
        
        @if(count($plantasObjects) == 0)
            <div class="panel-body text-center">
                <h4>No Plantas Available.</h4>
            </div>
        @else
        <div class="card-header">
            <h3 class="panel-title">Plantas</h3>
        </div>
        <div class="card-body card-body-with-table">
            <div class="table-responsive w-100">

                <table id="tablaplantas"  
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
                            <th data-sortable="true" >ID</th>
                            <th data-sortable="true" >Planta</th>
                            <th data-sortable="true" >Edificio</th>
                            @admin @desktop<th data-sortable="true">Cliente</th>@enddesktop @endadmin
                            <th class="text-center">Puestos</th>
                            <th class="text-center">Zonas</th>
                            <th class="text-center">Plano</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($plantasObjects as $plantas)
                        <tr class="hover-this">
                            @php
                                $cnt_puestos=$puestos->where('id_planta',$plantas->id_planta)->first();
                                $cnt_zonas=$zonas->where('id_planta',$plantas->id_planta)->first();
                            @endphp
                            <td>{{ $plantas->id_planta }}</td>
                            <td>{{ $plantas->des_planta }}</td>
                            <td>{{ $plantas->des_edificio }}</td>
                            @admin @desktop<td>{{ $plantas->nom_cliente }}</td>@enddesktop @endadmin
                            <td class="text-center">{{ $cnt_puestos->cnt_puestos??0 }}</td>
                            <td class="text-center">{{ $cnt_zonas->cnt_zonas??0 }}</td>
                            <td class="text-center" style="position: relative">@if(isset($plantas->img_plano))<img src="{{ Storage::disk(config('app.img_disk'))->url('img/plantas/'.$plantas->img_plano) }}" style="height: 50px; position: absoluite">@endif
                            
                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                        {{-- <a href="#"  class="btn btn-primary btn_editar add-tooltip thumb"  title="Ver planta" data-id="{{ $plantas->id_planta }}"> <span class="fa fa-eye" aria-hidden="true"></span></a> --}}
                                        @if(checkPermissions(['Plantas'],['W']))<a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $plantas->id_planta }})" title="Editar planta" data-id="{{ $plantas->id_planta }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                        @if(checkPermissions(['Plantas'],['W']))<a href="#"  class="btn btn-xs btn-secondary btn_puestos add-tooltip" onclick="puestos({{ $plantas->id_planta }})" title="Distribucion de puestos en la  planta" data-id="{{ $plantas->id_planta }}"> <span class="fa fa-desktop-alt pt-1" aria-hidden="true"></span> Pos</a>@endif
                                        @if(checkPermissions(['Plantas'],['W']))<a href="#"  class="btn btn-xs btn-primary btn_zonas add-tooltip" onclick="edit_zonas({{ $plantas->id_planta }})" title="Zonas de la  planta" data-id="{{ $plantas->id_planta }}"> <span class="fa-solid fa-draw-square pt-1" aria-hidden="true"></span> Zonas</a>@endif
                                        @if(checkPermissions(['Plantas'],['D']))<a href="#eliminar-planta-{{$plantas->id_planta}}" data-target="#eliminar-planta-{{$plantas->id_planta}}" onclick="del({{ $plantas->id_planta }})" title="Borrar planta" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
                                    </div>
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$plantas->id_planta}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h1 class="modal-title text-nowrap">Borrar planta </h1>
                                                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                                                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                                                </button>
                                            </div>    
                                            <div class="modal-body">
                                                ¿Borrar planta {{$plantas->des_planta}}?
                                            </div>
                                            
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/plantas/delete',$plantas->id_planta)}}">Si</a>
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
    <script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
    <script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
    <script>
        $('.configuracion').addClass('active active-sub');
        $('.menu_parametrizacion').addClass('active active-sub');
	    $('.espacios').addClass('active active-sub');
        $('.plantas').addClass('active');

        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/plantas/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function del(id){
            $('#eliminar-planta-'+id).modal('show');
        }

        function editar(id){
            $('#editorCAM').load("{{ url('/plantas/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }

        function puestos(id){
            $('#editorPUESTOS').load("{{ url('/plantas/puestos/') }}"+"/"+id, function(){
                animateCSS('#editorPUESTOS','bounceInRight');
            });
        }

        function edit_zonas(id){
            $('#editorPUESTOS').load("{{ url('/plantas/zonas/') }}"+"/"+id, function(){
                animateCSS('#editorPUESTOS','bounceInRight');
                
            });
        }
        
        

        $('.td').click(function(event){
            editar( $(this).data('id'));
        })
    </script>
@endsection
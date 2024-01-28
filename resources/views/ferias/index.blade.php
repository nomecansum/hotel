@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de ferias</h1>
@endsection

@section('styles')
    {{-- Boostrap Select --}}
    <link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">ferias</li>
        <li class="breadcrumb-item">listado de ferias</li>
        {{--  <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
        <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection

@section('content')
    <div class="row botones_accion">
        <div class="col-md-4">

        </div>
        <div class="col-md-7">
            <br>
        </div>
        <div class="col-md-2 text-end">
            @if(checkPermissions(['Ferias'],['C']))
            <div class="btn-group btn-group-sm pull-right" role="group">
                    <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva feria">
                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                    <span>Nueva</span>
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
        
        @if(count($ferias) == 0)
            <div class="card-body text-center">
                <h4>No Ferias Available.</h4>
            </div>
        @else
        <div class="card-header">
            <h3 class="card-title">Ferias</h3>
        </div>
        <div class="card-body panel-body-with-table">
            <div class="table-responsive w-100">

                <table id="tablaplantas"  data-toggle="table" data-mobile-responsive="true"
                data-locale="es-ES"
                data-search="true"
                data-show-columns="true"
                data-show-toggle="true"
                data-show-columns-toggle-all="true"
                data-page-list="[5, 10, 20, 30, 40, 50, 75, 100]"
                data-page-size="50"
                data-pagination="true" 
                data-show-pagination-switch="true"
                data-toolbar="#all_toolbar"
                data-buttons-class="secondary"
                data-show-button-text="true"
                >
                    <thead>
                        <tr>
                            <th data-sortable="true" >ID</th>
                            <th data-sortable="true" >Fecha</th>
                            <th data-sortable="true">Nombre</th>
                           
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($ferias as $feria)
                        <tr class="hover-this">
                            <td>{{ $feria->id_feria }}</td>
                            <td>{!! beauty_fecha($feria->fec_feria) !!}</td>
                            <td class="text-center" style="position: relative;">{{ $feria->des_feria }}
                            
                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                        @if(checkPermissions(['Plantas'],['W']))<a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $feria->id_feria }})" title="Editar planta" data-id="{{ $feria->id_feria }}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
                                        @if(checkPermissions(['Plantas'],['D']))<a href="#eliminar-planta-{{$feria->id_feria}}" data-target="#eliminar-planta-{{$feria->id_feria}}" title="Borrar planta" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip btn_del"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
                                    </div>
                                </div>
                                <div class="modal fade" id="eliminar-planta-{{$feria->id_feria}}" style="display: none;">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                <h1 class="modal-title text-nowrap">¿Borrar planta {{$feria->des_feria}}?</h1>
                                                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                                                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                                                </button>
                                            </div>
                                            <div class="modal-footer">
                                                <a class="btn btn-info" href="{{url('/ferias/delete',$feria->id_feria)}}">Si</a>
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
        $('.ferias').addClass('active active-sub');
        $('.ferias_ferias').addClass('active');

        $('#btn_nueva_puesto').click(function(){
            $('#editorCAM').load("{{ url('/ferias/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/ferias/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }

       

        $('.td').click(function(event){
            editar( $(this).data('id'));
        })
    </script>
@endsection
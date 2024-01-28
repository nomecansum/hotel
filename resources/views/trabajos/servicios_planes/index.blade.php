@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Planes de trabajo</h1>
@endsection

@section('styles')
<style type="text/css">
    .vertical{
        writing-mode:tb-rl;
        -webkit-transform:rotate(180deg);
        -moz-transform:rotate(180deg);
        -o-transform: rotate(180deg);
        -ms-transform:rotate(180deg);
        transform: rotate(180deg);
        white-space:nowrap;
        display:block;
        bottom:0;
    }
    .rotado{
        -webkit-transform:rotate(180deg);
        -moz-transform:rotate(180deg);
        -o-transform: rotate(180deg);
        -ms-transform:rotate(180deg);
        transform: rotate(180deg);
    }
    </style>
@endsection
@php
    use Carbon\Carbon;
@endphp

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">servicios</li>
        <li class="breadcrumb-item">trabajos planificados</li>
        <li class="breadcrumb-item active">planes de trabajo</li>
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
            @if(checkPermissions(['Trabajos planificacion'],['C']))
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nuevo tipo">
                    <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                    <span>Nuevo</span>
                </a>
            @endif
        </div>
    </div>
</div>
<span class="float-right" id="spinner" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
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
            <h3 class="card-title">Planes de trabajo</h3>
        </div>
        
        @if(count($datos) == 0)
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
                            @admin @desktop<th>Cliente</th>@enddesktop @endadmin
                            <th>Edificio</th>
                            <th>Tareas</th>
                            <th>Tiempo</th>
                            <th>Contratas</th>
                            <th>Operarios</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($datos as $tipo)
                        @php
                            $tareas = $detalles->where('id_plan',$tipo->id_plan)->count();
                            $tiempo = $detalles->where('id_plan',$tipo->id_plan)->sum('val_tiempo');
                            $contratas =  $detalles->where('id_plan',$tipo->id_plan)->map(function($item){
                                $c= new \stdClass();
                                $c->id_contrata = $item->id_contrata;
                                $c->des_contrata = $item->des_contrata;
                                $c->img_logo = $item->img_logo;
                                return $c;
                            })->unique();
                            $operarios_gen = $detalles->where('id_plan',$tipo->id_plan)->sum('num_operarios');
                            $operarios_list=[];
                            foreach($detalles->where('id_plan',$tipo->id_plan)->wherenotnull('list_operarios') as $item){
                                $operarios_list = array_merge($operarios_list,explode(",",$item->list_operarios));
                            }
                            $operarios_list = array_unique($operarios_list);
                        @endphp
                        <tr class="hover-this trfila {{ $tipo->mca_activo=='N'?'bg-light':'' }}"" >
                            <td>{{ $tipo->id_plan }}</td>
                            <td class="text-center"><i class="{{ $tipo->val_icono }} fa-2x" style="color:{{ $tipo->val_color }}"></i></td>
                            <td>{{ $tipo->des_plan }}</td>
                            @admin @desktop<td>{{ $tipo->nom_cliente }}</td>@enddesktop @endadmin
                            <td>{{ $tipo->des_edificio }}</td>
                            <td>{{ $tareas }}</td>
                            <td class="text-center">{{ decimal_to_time($tiempo/60) }}</td>
                            <td class="text-center">
                                @foreach($contratas as $contrata)
                                <div><img src="{{ isset($contrata->img_logo) ? Storage::disk(config('app.img_disk'))->url('img/contratas/'.$contrata->img_logo) : ''}}"  title="{{ $contrata->des_contrata??'' }}"  style="margin: auto; display: block; width: 30px; heigth:30px" alt=""> {{ $contrata->des_contrata??'' }}</div>
                                @endforeach
                            </td>
                            <td class="text-center"><i class="fa-solid fa-person-simple"></i> {{ ($operarios_gen??0)+count($operarios_list)??0 }}</td>
                            <td style="position: relative">
                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                        <a href="#"  class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{ $tipo->id_plan }})" title="Editar tipo" data-id="{{ $tipo->id_plan }}"> <span class="fa fa-eye pt-1" aria-hidden="true"></span> Ver</a>
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
        $('.servicios').addClass('active');
        $('.servicios_trabajos').addClass('active');
        $('.servicios_planes').addClass('active');

        function editar(id){
            $('#spinner').show();
            $('#editorCAM').empty();
            $('#editorCAM').load("{{ url('/trabajos/planificacion/ver/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
                $('#spinner').hide();
            });
        }

    </script>
@endsection
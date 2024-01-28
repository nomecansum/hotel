@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Eventos</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item">Utilidades</li>
        <li class="breadcrumb-item active">eventos</li>
    </ol>
@endsection

@section('content')
@php
    Carbon\Carbon::setLocale(session('lang'));
    setlocale(LC_TIME, 'Spanish');
    //dd($eventos);
@endphp

<div class="row botones_accion">
    <div class="col-md-4">

    </div>
    <div class="col-md-4">
        <br>
    </div>
    <div class="col-md-4 text-end">
        <div class="btn-group btn-group-sm pull-right mb-1 " role="group">
            @if (checkPermissions(['Eventos'],['C']))<a href="{{url(config('app.carpeta_asset').'/add')}}" class="btn float-right hidden-sm-down btn-success"><i class="fas fa-plus-circle"></i> Nuevo</a>@endif
        </div>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Eventos</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
							<h2 class="card-title float-left col">{{ __('general.eventos') }}</h2>
						</div>
                        <div class="table-responsive">
                            <table id="tabledeps"
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
                                        <th style="width: 1%">Id</th>
                                        <th style="width: 30%">{{ __('general.nombre') }}</th>
                                        <th  style="width: 15%">{{ __('general.comando') }}</th>
                                        <th>{{ __('general.act') }}</th>
                                        <th>{{ __('general.acciones') }}</th>
                                        <th>{{ __('general.ejecucion') }}</th>
                                        <th  style="width: 15%">{{ __('general.clientes') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($eventos as $ev)

                                        <tr  @if((checkPermissions(['Eventos'],['W'])) || fullAccess()) data-href="{{url(config('app.carpeta_asset').'/edit/'.$ev->cod_regla)}}"@endif>
                                            <td>{{$ev->cod_regla}}</td>
                                            <td>{{$ev->nom_regla}}</td>
                                            <td>{{ str_replace(".php","",str_replace("_"," ",basename($ev->nom_comando))) }}</td>
                                            <td class="text-center pl-4">
                                                {{-- <div class="custom-control custom-checkbox" style="display: inline-block; margin-right: 6px">
                                                    <input {{$ev->mca_activa == "S" ? 'checked' : ''}} type="checkbox" class="check-f2f custom-control-input">
                                                    <label class="custom-control-label"></label>
                                                </div> --}}
                                                @if($ev->mca_activa == "S")
                                                <i class="fa-solid fa-circle-check text-success fa-2x"></i>
                                                {{-- <div class="form-check pt-2 ">
                                                    <input readonly class="form-check-input fs-4" type="checkbox" checked>
                                                    <label class="form-check-label" for="chktodos"></label>
                                                </div> --}}
											@endif
                                            </td>
                                            <td nowrap="nowrap">
                                                {{$ev->iteraciones}} {{ __('general.iteraciones') }}<br>
                                                {{$ev->acciones}} {{ __('general.acciones') }}
                                            </td>
                                            <td nowrap="nowrap">Prox: {!! beauty_fecha($ev->fec_prox_ejecucion)!!}<br>Ult: {!! beauty_fecha($ev->fec_ult_ejecucion)!!}</td>
                                            <td style="position: relative; padding-left: 40px;">
                                                @php
                                                $clientes=array_filter(explode(',',$ev->clientes));
                                                $clientes=DB::table('clientes')->select('nom_cliente')->whereIn('id_cliente',$clientes)->pluck('nom_cliente')->toArray();
                                                @endphp
                                                @foreach ($clientes as $c)
                                                        <li class="text-nowrap" style="font-size: 12px">{{$c}}</li>
                                                @endforeach
                                                <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                                                    {{-- <div class="btn-group btn-group pull-right ml-1" role="group">
                                                        <a href="javascript:void(0);" data-cod_regla="{{$ev->cod_regla}}" onclick="detalle($ev->cod_regla)"  class="btn btn-xs btn-secondary log_regla"><i class="fa-solid fa-magnifying-glass"></i> {{__('general.detalles')}}</a>
                                                        @if(checkPermissions(['Eventos'],['W']))<a href="{{url(config('app.carpeta_asset').'/edit',$ev->cod_regla)}}" class="btn btn-xs btn-info"><i class="fas fa-pencil"></i> {{__('general.edit')}}</a>@endif
                                                        @if(checkPermissions(['Eventos'],['D']))<a href="javascript:void(0);" onclick="del($ev->cod_regla)" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i> {{__('general.delete')}}</a>@endif
                                                    </div> --}}
                                                    <div class="btn-group btn-group pull-right ml-1" role="group">
                                                        <a href="javascript:void(0)" class="btn btn-xs btn-secondary btn_run" onclick="detalle({{$ev->cod_regla}},'{{$ev->nom_regla}}')" data-id="{{ $ev->cod_regla }}"><i class="fa-solid fa-magnifying-glass"></i> {{__('general.detalles')}}</a>
                                                        @if(checkPermissions(['Eventos'],['W']))<a href="{{url('events/edit',$ev->cod_regla)}}" class="btn btn-xs btn-info"><i class="fas fa-pencil"></i> {{__('general.edit')}}</a>@endif
                                                        @if(checkPermissions(['Eventos'],['D']))<a href="#eliminar-regla-{{$ev->cod_regla}}" onclick="del({{$ev->cod_regla}})" data-toggle="modal" class="btn btn-xs btn-danger"><i class="fas fa-trash"></i> {{__('general.delete')}}</a>@endif
                                                    </div>
                                                </div>
                                                
                                                <div class="modal fade" id="eliminar-regla-{{$ev->cod_regla}}">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                                                                <h1 class="modal-title text-nowrap">Borrar regla </h1>
                                                                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                                                                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                                                                </button>
                                                            </div>    
                                                            <div class="modal-body">
                                                                {{ __('eventos.eliminar_la_regla') }} {{ $ev->nom_regla }}?
                                                            </div>
                                                            <div class="modal-footer">
                                                                <a class="btn btn-info" href="{{url(config('app.carpeta_asset').'/delete',$ev->cod_regla)}}">{{trans('general.yes')}}</a>
                                                                <button type="button" data-dismiss="modal" class="btn btn-warning" onclick="cerrar_modal()">{{trans('general.cancelar')}}</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="log_evento_{{$ev->cod_regla}}" style="display: none">
                                            <td colspan="8" id="detail_evento_{{$ev->cod_regla}}"></td>
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
</div>


@endsection


@section('scripts2')
    <script src="{{url('/plugins/jQuery-slimScroll-1.3.8/jquery.slimscroll.min.js')}}"></script>
    <script>
        $('.configuracion').addClass('active active-sub');
        $('.menu_utilidades').addClass('active active-sub');
        $('.eventos').addClass('active');

        function del(id){
            $('#eliminar-regla-'+id).modal('show');
        }

        function detalle(id){
            event.stopPropagation();
            $("#log_evento_"+id).show();
            $("#detail_evento_"+id).load("{{url(config('app.carpeta_asset').'/detalle_evento')}}/"+id, function(){
                //animateCSS("#log_evento_"+$(this).data('cod_regla'),"fadeIn");
            });
            //console.log("Log "+$(this).data('cod_regla'));
        }
    </script>
    
@endsection

@php
Use \Carbon\Carbon;
if (isset($r->fechas)){
    $f = explode(' - ',$r->fechas);
    $f1 = Carbon::parse(adaptar_fecha($f[0]));
    $f2 = Carbon::parse(adaptar_fecha($f[1]));
    //dd($fechas);
} else {
    $fechas[0]=Carbon::now()->startOfMonth();
    $fechas[1]=Carbon::now()->endOfMonth();
}

@endphp

@extends('layout')

@section('styles')
<style type="text/css">
    .select2-results__options[id*="tipo_log"] .select2-results__option:nth-child(2) {
        color: green;
    }
    .select2-results__options[id*="tipo_log"] .select2-results__option:nth-child(3) {
       
        color: red;
    }

    .select2-container{
        height: 40px;
    }
       
    .select2-selection{
        height: 40px;
    }

    .select2-selection__choice{
        height: 30px;
        padding-top: 2px;
        background-color: #25476a;
    }
  
</style>
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Bitácora</h1>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuración</li>
        <li class="breadcrumb-item">bitácora</li>
        {{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
    </ol>
@endsection


@section('content')

    @if(Session::has('success_message'))
        <div class="alert alert-success">
            <span class="glyphicon glyphicon-ok"></span>
            {!! session('success_message') !!}

            <button type="button" class="close" data-dismiss="alert" aria-label="close">
                <span aria-hidden="true">&times;</span>
            </button>

        </div>
    @endif

    <div class="card " >
        <div class="card-header cursor-pointer" style="padding-top: 10px" id="headfiltro" >
            <span class="mt-3 ml-2 font-18"><i class="fad fa-filter"></i> Filtro</span>
        </div>
        <div class="card-body" id="divfiltro" style="display:none" >
            <form name="frm_busca_bitacora" method="POST" action="{{ url('bitacoras/search') }}">
                {{ csrf_field() }}
                <div class="row">
                    
                
                    <div class="col-md-3" style="width: 110px">
                        <div class="form-group">
                            <label>Mostrar</label>
                            <select class="form-control" name="tipo_log" id="tipo_log">
                                <option value=""></option>
                                <option  {{ isset($r) && $r->tipo_log=="ok" ? 'selected' : '' }} value="ok" id="ok">ok</option>
                                <option {{ isset($r) && $r->tipo_log=="error" ? 'selected' : '' }} value="error" id="error">error</option>
                            </select>
                        </div>
                    </div>
                
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Usuario</label>
                            <select class="form-control select2" tabindex="-1" aria-hidden="true" name="usuario">
                                <option value=""></option>
                                @foreach($usuarios as $key=>$value)
                                <option {{ isset($r) && $r->usuario==$key ? 'selected' : '' }} value="{{ $key }}">{{ $value }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fechas:</label>
                            <div class="input-group mar-btm">
                                <input type="text" class="form-control pull-right" id="fechas" name="fechas" value="{{ isset($r)?$f1->format('d/m/Y').' - '.$f2->format('d/m/Y'):'' }}">
                                <div class="input-group-btn">
                                    <span class="btn input-group-text btn-secondary"  style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                                </div>
                            </div>
                            <!-- /.input group -->
                        </div> 
                    </div>
                    <div class="col-md-2 form-group text-end">
                        <button type="submit" class="btn btn-primary mt-3"><i class="fa fa-search"></i> Buscar</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-11">
                        <div class="form-group">  
                            <label>Modulo</label>
                            <select class="form-control select2 select2-multiple" multiple="" data-placeholder="Seleccione modulo" tabindex="-1" aria-hidden="true" name="modulos[]"> 
                                @forelse($modulos as $modulo)
                                <option {{ isset($r) &&is_array($r->modulos) && in_array($modulo,$r->modulos) ? 'selected' : '' }}  value="{{ $modulo }}">{{ $modulo }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                        
                    
                    
                </div>
            </form>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Bitácora</h3>
        </div>
        @if(count($bitacoras) == 0)
            <div class="card-body text-center">
                <h4>No Bitacoras Available.</h4>
            </div>
        @else
            <div class="col-md-8 ml-3">{{ $bitacoras->count() }} registros del {!! beauty_fecha($min) !!} al {!! beauty_fecha($max) !!}</div>
            <div class="card-body panel-body-with-table">
                <div class="table-responsive">

                    <table id="tablapuestos"  data-toggle="table" data-mobile-responsive="true"
                        data-locale="es-ES"
                        data-search="true"
                        data-show-columns="true"
                        data-show-columns-toggle-all="true"
                        data-page-list="[5, 10, 20, 30, 40, 50]"
                        data-page-size="50"
                        data-pagination="true" 
                        data-show-pagination-switch="true"
                        data-show-button-text="true"
                        data-toolbar="#all_toolbar"
                        >
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                @admin @desktop<th>Cliente</th>@enddesktop @endadmin
                                <th>Modulo</th>
                                <th>Seccion</th>
                                <th style="width:30%">Accion</th>
                                <th>Status</th>
                                <th style="width: 140px">Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($bitacoras as $bitacora)
                            <tr >
                                <td>{{ $bitacora->name }}</td>
                                @admin @desktop<td>{{ $bitacora->nom_cliente }}</td>@enddesktop @endadmin
                                <td>{{ $bitacora->id_modulo }}</td>
                                <td>{{ $bitacora->id_seccion }}</td>
                                <td style="word-break: break-all;">{!! $bitacora->accion !!}</td>
                                <td class="text-center" ><a href="#"  onclick="ver({{ $bitacora->id_bitacora }},'{!! beauty_fecha($bitacora->fecha) !!}')"><span @if(strtoupper($bitacora->status)=="OK") class="badge p-2 bg-success" @else class="badge p-2 bg-danger" @endif style="padding: 0 5px 0 5px">{{ $bitacora->status }}</span></a></td>
                                <td>{!! beauty_fecha($bitacora->fecha) !!}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <div class="modal fade" id="ver_detalle" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                
                <div class="modal-header">
                    
                    <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                    <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h1 class="modal-title text-nowrap">Entrada <span class="fecha"></span></h1>
                    <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                        <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                    </button>
                </div>
                <div class="modal-body" id="detalle_modal">
                    
                </div>
                <div class="modal-footer">
                    <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cerrar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@php
    //dd($r->modulos);
    if(isset($r)){
        $fechas=explode(" - ",$r->fechas);
    } else {
        $fechas[0]=date('Y-m-d');
        $fechas[1]=date('Y-m-d', strtotime(date('Y-m-d') . " + 30 day"));
    }
    
@endphp
@section('scripts')
    <script>

    $('.configuracion').addClass('active active-sub');
	$('.bitacora').addClass('active');

    $('#headfiltro').click(function(){
        $('#divfiltro').toggle();
    })

    function ver(id,fecha){
        console.log("ver");
        $('#detalle_modal').html('<div class="text-center"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;"> LOADING</div>');
        $('#ver_detalle').modal('show');
        $('#detalle_modal').load('{{ url('/bitacoras/detalle') }}/'+id);
        $('#fecha').html(fecha);
    }

    var rangepicker = new Litepicker({
        element: document.getElementById( "fechas" ),
        singleMode: false,
        @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
        @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
        autoApply: true,
        format: 'DD/MM/YYYY',
        lang: "es-ES",
        tooltipText: {
            one: "day",
            other: "days"
        },
        tooltipNumber: (totalDays) => {
            return totalDays - 1;
        },
        setup: (rangepicker) => {
            rangepicker.on('selected', (date1, date2) => {
                //comprobar_puestos();
            });
        }
    });

    @if(isset($fechas) && $fechas[0]!='' && $fechas[1]!='')
        $('#fechas').val('{{ $fechas[0] }} - {{ $fechas[1] }}');
    @endif;
    </script>
@endsection
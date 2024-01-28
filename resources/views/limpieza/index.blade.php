@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Rondas de {{ $entidades['tipo'] }}</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">Configuracion</li>
        <li class="breadcrumb-item">{{ $entidades['tipo'] }}</li>
        <li class="breadcrumb-item active">rondas de {{ $entidades['tipo'] }}</li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion mb-2">
    <br><br>
</div>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Rondas de {{ $entidades['tipo'] }}</h3>
    </div>
    <div class="card-body">
        <div id="all_toolbar">
            <div class="input-group">
                <input type="text" class="form-control pull-left" id="fechas"  autocomplete="off" name="fechas" style="width: 200px"  value="{{ $f1->format('d/m/Y').' - '.$f2->format('d/m/Y') }}">
                <span class="btn input-group-text btn-secondary btn_calendario"   style="height: 40px"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
                {{-- <button id="btn-toggle" class="btn btn-secondary float-right ml-3 "><i class="fal fa-table"></i> | <i class="fal fa-credit-card-blank mt-1"></i></button> --}}
            </div>
        </div>
        <table id="tablarondas"  
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
                    <th class="no-sort text-center w-2">Fecha</th>
                    <th style="width: 30%" class="no-sort">Descripcion</th>
                    <th data-sortable="true" class="text-center"  style="width: 50px"><i class="fad fa-building"></i> Edificios</th>
                    <th data-sortable="true" class="text-center" style="width: 50px"><i class="fad fa-layer-group"></i> Plantas</th>
                    <th data-sortable="true" class="text-center" style="width: 50px"><i class="fad fa-desktop-alt"></i> Puestos</th>
                    <th data-sortable="true"  data-card-visible="false">{{ $entidades['plural'] }}</th>
                    @if($entidades['tipo']=='limpieza')<th data-sortable="true" class="text-center" style="width: 50px"><i class="fa-solid fa-clock"></i> Tiempo</th>@endif
                    <th data-sortable="true"class="text-center" style="width: 100px">Completado</th>
                    {{--  <th></th>  --}}
                </tr>
            </thead>
            <tbody>
                @foreach($rondas as $r)
                @php
                    $cnt_edificios=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_edificio')->unique()->count();
                    $cnt_plantas=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_planta')->unique()->count();
                    $cnt_puestos=$detalles->where('id_ronda',$r->id_ronda)->pluck('id_puesto')->unique()->count();
                    $puestos_si=$detalles->where('id_ronda',$r->id_ronda)->wherenotnull('fec_fin')->count();
                    $puestos_no=$detalles->where('id_ronda',$r->id_ronda)->wherenull('fec_fin')->count();
                    $tiempo=$detalles->where('id_ronda',$r->id_ronda)->sum('val_tiempo_limpieza')/60;
                    try{
                        $pct_completado=(100*$puestos_si/$cnt_puestos);
                    } catch(\Throwable $e){
                        $pct_completado=0;
                    }
                    
                @endphp
                <tr class="hover-this" data-id="{{ $r->id_ronda }}">
                    <td class="text-center">
                        {!! beauty_fecha($r->fec_ronda) !!}
                    </td>
                    <td class="text-center" data-id="" >
                        {{ $r->des_ronda }}
                    </td>
                    
                    <td class="td text-center fs-2" data-id="">{{ $cnt_edificios }}</td>
                    <td class="td text-center fs-2" data-id="">{{ $cnt_plantas }}</td>
                    <td class="td text-center fs-2" data-id="">{{ $cnt_puestos }}</td>
                    <td class="td" data-id="" >
                        @foreach(explode('#',$r->user_asignado) as $u)
                            <li>{{ $u }}</li>
                        @endforeach
                    </td>
                    @if($entidades['tipo']=='limpieza')<td class="td text-center fs-2" data-id="">{{ decimal_to_time($tiempo) }}</td>@endif
                    <td class="text-center " >
                        <span class="text-{{ color_porcentaje($pct_completado) }} font-bold " style="font-size: 2em ">{{ round($pct_completado) }} %</span>
                    </td>
                </tr>

                @endforeach
            </tbody>
        </table> 
    </div>
</div>
<div class="modal fade" id="ronda-limpieza" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header">
                
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h1 class="modal-title text-nowrap">Ronda de {{ $entidades['tipo'] }} <span class="idronda">#</span></h1>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body" id="detalle_modal">
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" data-id_ronda=0 id="btn_completar"><i class="fad fa-check-double"></i> Completar ronda</button>
                <a  class="btn btn-info" id="btn_print" href=""><i class="fad fa-print"></i> Imprimir</a>
                <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection



@section('scripts')
    <script>


        $('{{ $entidades['menu1'] }}').addClass('active active-sub');
        $('{{ $entidades['menu2'] }}').addClass('active');
       
       $('#btn-toggle').click(function(){
         $('#tablarondas').bootstrapTable('toggleView')
       })

       $('.btn_calendario').click(function(){
            $('#fechas').trigger('click');
        })

       $('#tablarondas').on('click-cell.bs.table', function(e, value, row, $element){
           //console.log($element._data.id);
           @if(Auth::user()->nivel_acceso==10)
                window.location.href = '{{ url('/rondas/detallelimp/') }}/'+$element._data.id;
           @else
            $('#loading').show();
            $('#ronda-limpieza').modal('show');
            $('#detalle_modal').load("{{ url('/rondas/view/') }}/"+$element._data.id+"/0",function(){
                $('#loading').hide();
            })
            $('#btn_print').attr('href',"{{ url('/rondas/view/')}}/"+$element._data.id+"/1");
            $('.idronda').html('#'+$element._data.id);
            $('#btn_completar').data('id_ronda',$element._data.id)
           @endif
           //animateCSS('#detalle_ronda'+$element._data.id,'bounceInRight');
        });

        var picker = new Litepicker({
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
            setup: (picker) => {
                picker.on('selected', (date1, date2) => {
                    window.location.href = '{{ url('/rondas/index/') }}/{{ $tipo }}/'+date1.format('YYYY-MM-DD')+'/'+date2.format('YYYY-MM-DD');
                });
            }
        });

        

        $('#btn_completar').click(function(){
            get_ajax("{{ url('/rondas/completar_ronda/') }}/"+$(this).data('id_ronda')+'/'+id_user,'loading',function(){
                console.log('ok0');
            });
        })

    </script>
@endsection

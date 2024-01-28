@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Ronda de limpieza #{{ $ronda->id_ronda }} {{ $ronda->des_ronda }}</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item"><a href="{{url('/rondas')}}">Rondas de limpieza</a></li>
        <li class="breadcrumb-item active">Ronda de limpieza #{{ $ronda->id_ronda }} {{ $ronda->des_ronda }}</li>
    </ol>
@endsection

@php
    $edificio_ahora=0;
    $planta_ahora=0;
@endphp

@section('content')

   
        @foreach ($edificios as $e)
        @php
            $cnt_plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('id_planta')->unique()->count();
            $cnt_puestos=$puestos->where('id_edificio',$e->id_edificio)->pluck('cod_puesto')->unique()->count();
        @endphp
        <div class="card">
            <div class="card-header bg-gray-dark">
                <div class="row">
                    <div class="col-md-3">
                        <span class="fs-2 ml-2 mt-2 font-bold"><i class="fad fa-building"></i> {{ $e->des_edificio }}</span>
                    </div>
                    <div class="col-md-7"></div>
                    <div class="col-md-2 text-end">
                        <h4>
                            <span class="mr-2"><i class="fad fa-layer-group"></i> {{ $cnt_plantas }}</span>
                            <span class="mr-2"><i class="fad fa-desktop-alt"></i> {{ $cnt_puestos }}</span>
                        </h4>
                    </div>
                </div>
            </div>
            <div class="card-body">
                @php
                    $plantas=$puestos->where('id_edificio',$e->id_edificio)->pluck('des_planta','id_planta')->sortby('des_planta');
                @endphp
                @foreach($plantas as $key=>$value)
                    @php
                        $puestos_planta=$puestos->where('id_planta',$key);
                    @endphp
                    <h3 class="pad-all w-100 bg-gray rounded"><span>PLANTA {{ $value }}</span><button class="btn btn-primary btn_todo" data-planta="{{ $key }}"  data-key="{{ implode(',',$puestos_planta->pluck('key_id')->toArray()) }}" style="float: right"><i class="fad fa-check-double"></i> Todo</button></h3>
                    <div class="d-flex flex-wrap">
                        @foreach($puestos_planta as $p)
                            <div class="text-center font-bold rounded bg-{{ $p->val_color }} mr-2 mb-2 align-middle divpuesto" id="div_puesto{{ $p->key_id }}" data-key="{{ $p->key_id }}" data-puesto="{{ $p->cod_puesto }}" data-estado="{{ $p->id_estado }}" data-ronda="{{ $ronda->id_ronda }}" style="width:100px; height: 100px; overflow: hidden; cursor: pointer">
                                <span class="h-100 align-middle">{{ $p->cod_puesto }}</span>
                            </div>
                            
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
        @endforeach


@endsection


@section('scripts')
    <script>
        $('.limpieza').addClass('active active-sub');
        $('.rondas').addClass('active');

        $('.divpuesto').click(function(){
           
            nuevo_estado=0;
            if($(this).data('estado')==1)
                {
                    nuevo_estado=3;
                } else {
                    nuevo_estado=1;
                }
            
            //console.log(nuevo_estado);
            if(nuevo_estado!=0){
                $.post("{{ url('/rondas/estado_puesto/') }}/", {_token:'{{csrf_token()}}',id:$(this).data('key'), user: {{ Auth::user()->id }},estado: nuevo_estado}, function(data, textStatus, xhr) {
                    //console.log(data);
                    if (data.error){
                        toast_error('ERROR',data.error);
                        return;
                    }
                    $.each(data.id,function(index,value){
                        //console.log(data.estado);
                        $('#div_puesto'+value).removeClass('bg-info bg-success bg-danger bg-pink');      
                        $('#div_puesto'+value).addClass('bg-'+color_estado(data.estado));  
                        $('#div_puesto'+value).data('estado',data.estado);                   
                        toast_ok('Estado',$('#div_puesto'+value).data('puesto')+' Actualizado');
                    });
                    
                })   
            }
        })

        $('.btn_todo').click(function(){
            $.post("{{ url('/rondas/estado_puesto/') }}/", {_token:'{{csrf_token()}}',id:$(this).data('key'), user: {{ Auth::user()->id }},estado: 1}, function(data, textStatus, xhr) {
                    //console.log(data);
                    if (data.error){
                        toast_error('ERROR',data.error);
                        return;
                    }
                    $.each(data.id,function(index,value){
                        //console.log(data.estado);
                        $('#div_puesto'+value).removeClass('bg-info bg-success bg-danger bg-pink');      
                        $('#div_puesto'+value).addClass('bg-'+color_estado(data.estado));  
                        $('#div_puesto'+value).data('estado',data.estado);                   
                        toast_ok('Estado',$('#div_puesto'+value).data('puesto')+' Actualizado');
                    });
                    
                })   
        })
    </script>
@endsection

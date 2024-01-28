@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Reserva de salas de reunion</h1>
@endsection

@section('styles')

<link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
<style>
    .solo_icono{
        margin-top: 10px;
    }
</style>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">reservas</li>
        <li class="breadcrumb-item active"><a href="{{url('/users')}}">Reserva de salas de reunion</a></li>
    </ol>
@endsection

@section('content')
<div class="row botones_accion">
    <div class="col-md-3">
        <div class="input-group float-right" id="div_fechas">
            <input type="text" class="form-control pull-left ml-1" id="fecha_ver" name="fecha_ver" value="{{ Carbon\Carbon::now()->format('d/m/Y') }}">
            <span class="btn input-group-text btn-secondary btn_fecha"  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
        </div>
    </div>
    <div class="col-md-7">
        <br>
    </div>
    <div class="col-md-2 text-end">
        <div class="btn-group btn-group pull-right" role="group">
                <a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva reserva">
                <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                <span>Nueva</span>
            </a>
        </div>
    </div>
</div>
<div id="editorCAM" class="mt-2">

</div>


 



<div class="card">
     <div class="card-header">
        <h3 class="card-title" id="titulo">Estado de salas para {!! beauty_fecha(Carbon\Carbon::now(),0) !!}</h3>
    </div> 
    <div class="card-body" id="detalles_reserva">
       @foreach($salas as $sala)
            @php
                $reserva_sala=$reservas->where('id_puesto',$sala->id_puesto);
            @endphp
            @include('salas.fill_sala')
       @endforeach
    </div>
</div>


@if(count($misreservas)>0)
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Mis reservas</h3>
        </div>
        <div class="card-body" id="misreservas">
            @include('salas.mis_reservas')      
        </div>
    </div>
@endif
@endsection


@section('scripts')
    <script>
        $('.reservas').addClass('active active-sub');
        $('.reservas_salas').addClass('active');
    </script>

    <script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
    <script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
    <script>
        
        
        function filter_hour(value, type) {
        return (value % 60 == 0) ? 1 : 0;
        }

        $('.btn_fecha').click(function(){
            picker.open('#fecha_ver');
        })

        const picker = MCDatepicker.create({
            el: "#fecha_ver",
            dateFormat: cal_formato_fecha,
            autoClose: true,
            closeOnBlur: true,
            firstWeekday: 1,
            customMonths: cal_meses,
            customWeekDays: cal_diassemana
        });

        picker.onSelect((date, formatedDate) => {
            lafecha=moment(formatedDate,"DD/MM/YYYY").format('YYYY-MM-DD');
            $('#detalles_reserva').load("/salas/dia/"+lafecha);
        });

        $('#btn_nueva_puesto').click(function(){
            spshow('spin');
            $('#div_fechas').hide();
            $('#editorCAM').load("{{ url('/salas/crear_reserva') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
                sphide('spin');
                $('#titulo').html('Nueva reserva de sala');
            });
            
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/reservas/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }

       

        function borrar(){
            $.post('{{url('/reservas/cancelar')}}', {_token: '{{csrf_token()}}',fecha: $(this).data('fecha'),id: $(this).data('id'), des_puesto: $(this).data('des_puesto')}, function(data, textStatus, xhr) {
                
            })
            .done(function(data) {
                console.log(data);
                if(data.error){
                    mensaje_error_controlado(data);

                } else if(data.alert){
                    mensaje_warning_controlado(data);
                } else{
                    toast_ok(data.title,data.mensaje);
                    $('#misreservas').load("/salas/mis_reservas");
                }
                
            })
            .fail(function(err) {
                mensaje_error_respuesta(err);
            });
        }

        $(function(){
            $('#fecha_ver').change(function(){
                
            })

        })          
         
        $('.btn_del').click(borrar);
        
        

    </script>
@endsection
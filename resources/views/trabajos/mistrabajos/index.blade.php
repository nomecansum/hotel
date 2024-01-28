@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Mis trabajos</h1>
@endsection

@section('styles')
<style type="text/css">
    .table td, .table th {
        padding: .05rem;
        vertical-align: top;
        border-top: 1px solid #dee2e6;
    }

    .td_calendar{
        font-size: 12px;
        font-weight: 400;
    }

    .round{
        line-height: 20px;
    }
    .noborders td {
        border:0;
    }
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



@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">servicios</li>
        <li class="breadcrumb-item">trabajos planificados</li>
        <li class="breadcrumb-item active">mis trabajos</li>
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
        
    </div>
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
    
    <div class="card-body panel-body-with-table" id="calendario">
        @mobile
        <div class="col-md-3">
            <div class="input-group float-right" id="div_fechas">
                <input type="text" class="form-control pull-left ml-1" style="font-size: 15px" readonly id="fecha_ver" name="fecha_ver" value="{{ Carbon\Carbon::now()->format('d/m/Y') }}">
                <span class="btn input-group-text btn-secondary btn_fecha"  style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
            </div>
        </div>
        @endmobile
    </div>
</div>

<div class="row mt-3" id="trabajos_dia">

</div>

@endsection

@section('scripts')
    <script>
        $('.servicios').addClass('active');
        $('.servicios_trabajos').addClass('active');
        $('.servicios_mistrabajos').addClass('active');

        function loadMes(fecha){
            $.ajax({
                url: "{{url('trabajos/mistrabajos/load_calendario')}}/"+fecha,
                type: "GET",
                success: function(data){
                    $('#calendario').html(data);
                }
            });
        }

        function loadDia(fecha,vista){
            $.ajax({
                url: "{{url('trabajos/mistrabajos/load_dia')}}/"+fecha+"/"+vista,
                type: "GET",
                success: function(data){
                    $('#trabajos_dia').html(data);
                }
            });
        }

        

        $(function(){
           @desktop loadMes("{{date('Y-m-d')}}"); @enddesktop
            loadDia("{{date('Y-m-d')}}");
        })

        @mobile
        $('.btn_fecha').click(function(){
            picker.open('#fecha_ver');
        })

        const picker = MCDatepicker.create({
            el: "#fecha_ver",
            dateFormat: cal_formato_fecha,
            autoClose: true,
            closeOnBlur: true,
            firstWeekday: 1,
            disableWeekDays: cal_dias_deshabilitados,
            customMonths: cal_meses,
            customWeekDays: cal_diassemana
        });

        picker.onSelect((date, formatedDate) => {
            loadDia(moment(date).format('YYYY-MM-DD'));
        });
        
        @endmobile

    </script>
@endsection
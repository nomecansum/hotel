@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Reservas</h1>
@endsection

@section('styles')
    <!--Bootstrap FLEX Stylesheet [ REQUIRED ]-->
    <link href="{{ url('/css/bootstrap-grid.min.css') }}" rel="stylesheet">
    <link href="{{ asset('/plugins/noUiSlider/nouislider.min.css') }}" rel="stylesheet">
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item active">Reservas</li>
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
                <a href="#" id="btn_nueva_reserva" class="btn btn-success" title="Nueva reserva">
                <i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
                <span>Nueva</span>
            </a>
        </div>
    </div>
</div>
<div id="editorCAM" class="mt-2 mb-5">

</div>
<div class="card">
    <div class="card-header bg-light">
        <h3 class="card-title ">Reservas</h3>
        <span class="float-right" id="spin" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="card-body">
        <div id="calendario"></div>
    </div>
</div>
@endsection


@section('scripts')
    <script src="{{url('/plugins/noUiSlider/nouislider.min.js')}}"></script>
    <script src="{{url('/plugins/noUiSlider/wNumb.js')}}"></script>
    <script>
        $('.SECCION_MENU').addClass('active active-sub');
        $('.reservas').addClass('active');
        $('.reservas_puestos').addClass('active');

        function filter_hour(value, type) {
        return (value % 60 == 0) ? 1 : 0;
        }

        
        function loadMonth(month = null,type = null)
        {
            $('#spinner').show();
            $.post('{{url('reservas/loadMonthSchedule')}}', {_token:'{{csrf_token()}}',month: month,type:type,emp:'{{Auth::user()->id}}'}, function(data, textStatus, xhr) {
                $('#calendario').html(data);
            
                
                $('.changeMonth').click(function(event) {
                    loadMonth($(this).data('month'),$(this).data('action'));
                });
                $('#spinner').hide();
            });
        }

        $(function(){
            loadMonth();
        })

        $('#btn_nueva_puesto').click(function(){
            spshow('spin');
            $('#editorCAM').load("{{ url('/reservas/create/') }}/"+fechacal, function(){
                animateCSS('#editorCAM','bounceInRight');
                sphide('spin');
                $('#titulo').html('Nueva reserva de puesto');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        $('#btn_nueva_reserva').click(function(){
            spshow('spin');
            lafecha=moment($('#fecha_ver').val(),"DD/MM/YYYY").format('YYYY-MM-DD');
            $('#editorCAM').load("{{ url('/reservas/create/') }}/"+lafecha, function(){
                animateCSS('#editorCAM','bounceInRight');
                sphide('spin');
                $('#titulo').html('Nueva reserva de puesto');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

        function editar(id){
            $('#editorCAM').load("{{ url('/reservas/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
        }


        function boton_modo_click(){
            $('#loadfilter').show();
                $.post('{{url('/reservas/comprobar')}}', {_token: '{{csrf_token()}}',fechas: $('#fechas').val(),edificio:$('#id_edificio').val(),tipo: $(this).data('href'), hora_inicio: $('#hora_inicio').val(),hora_fin: $('#hora_fin').val(),id_planta:$('#id_planta').val()}, function(data, textStatus, xhr) {
                    $('#detalles_reserva').html(data);
                    recolocar_puestos();
                    console.log('modo.click');
                });
        }


    
        $(window).resize(function(){
            recolocar_puestos();
            console.log('window.resize');
        })

        document.querySelectorAll(".nav-toggler").forEach(item => 
            item.addEventListener("click", () => {
                setTimeout(() => {
                    console.log('nav-toggler');
                    recolocar_puestos();
                }, 300);
        }));

        picker = MCDatepicker.create({
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
            $('#detalles_reserva').load("/reservas/dia/"+lafecha);
        });


    </script>
@endsection

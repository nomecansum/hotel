@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Crear nueva incidencia</h1>
@endsection

@section('styles')

<link href="{{url('/plugins/dropzone/dropzone.css')}}" rel="stylesheet">

@endsection

@section('breadcrumb')
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
    <li class="breadcrumb-item">Incidencias</li>
    <li class="breadcrumb-item active">crear nueva incidencia</li>
    {{--  <li class="breadcrumb-item"><a href="{{url('/users')}}">Usuarios</a></li>
    <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
</ol>
@endsection

@section('content')
<div class="card" style="margin-top: 150px">
    <div class="card-body">
        <div class="row">
            <div class="form-group col-md-12">
                <label for="id_cliente" class="control-label">Seleccione un puesto para crear la incidencia</label>
                <select class="form-control select2" id="id_puesto" name="id_puesto">
                    <option value="" ></option>
                    @php
                        $planta=0;
                        $edificio=0;	
                    @endphp
                    @foreach ($puestos as $puesto)
                        @if($edificio!= $puesto->id_edificio)
                            <optgroup label="{{ $puesto->des_edificio }}"></optgroup>
                            @php $edificio=$puesto->id_edificio @endphp
                        @endif
                        @if($planta!= $puesto->id_planta)
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<optgroup label="{{ $puesto->des_planta }}"></optgroup>
                            @php $planta=$puesto->id_planta @endphp
                        @endif
                        <option value="{{ $puesto->id_puesto }}" >
                            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $puesto->cod_puesto }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card_body" id="form_incidencia">

    </div>
</div>
@endsection


@section('scripts')
<script type="text/javascript"  src="{{url('/plugins/dropzone/dropzone.js')}}"></script>
<script>
    $('#id_puesto').change(function(){
        $.get("{{ url('incidencias/create') }}/"+$(this).val()+"/embed",function(data){
            $('#form_incidencia').html(data);
            animateCSS('#form_incidencia','bounceInRight');
        });
    })
</script>


    <script>

        
    </script>

@endsection

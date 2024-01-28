@extends('layout')




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
table {
    text-align: left;
    position: relative;
    border-collapse: collapse;
    }
    th, td {
    padding: 0.25rem;
    }
    th {
    background: white;
    position: sticky;
    top: 0; /* Don't forget this, required for the stickiness */
    box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
    }
</style>
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestor de puestos para supervisores</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
	<li class="breadcrumb-item">configuracion</li>
	<li class="breadcrumb-item">parametrizacion</li>
	<li class="breadcrumb-item">personas</li>
    <li class="breadcrumb-item active">puestos - supervisores</li>
	{{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
</ol>
@endsection

@section('content')
@php
    //dd($incidencias);
@endphp

<div class="row botones_accion">
	<div class="col-md-4">

	</div>
	<div class="col-md-7">
		<br>
	</div>
	<div class="col-md-1 text-end">
		
	</div>
</div>

<div class="row mt-2">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Gestor de puestos para supervisores</h3>
			</div>
			<div class="card-body">
				<table id="myTable" class="">
					<thead>
						<tr>
                            <th style="width: 180px"></th>
                            @foreach($usuarios as $u)
                                <th class="tduser text-center" data-usuario="{{ $u->id }}" style="font-size: 10px" data-checked="false"><div class="vertical" style="margin-left: 15px;">{{ $u->name }}</div></th>
                            @endforeach
						</tr>
					</thead>
					<tbody>

                        @foreach($edificios as $e)
                            @php
                                $lista_plantas=$plantas->where('id_edificio',$e->id_edificio);           
                            @endphp
                            <tr>
                                <td class="text-center nowrap" style="background-color:#696969; color: #fff; font-weight: bold; padding: 18px" ><div>{{ $e->des_edificio }}</div></td>
                                @foreach($usuarios as $u)
                                    <td class="text-center" style="background-color:#696969 "  title="{{ $e->des_edificio }} -> {{ $u->name }}" id="pastillaE_{{ $u->id }}_{{ $e->id_edificio }}" >
                                        <div class="form-check pt-2">
                                            <input  name="chkE_{{ $u->id }}_{{ $e->id_edificio }}"  id="chkE_{{ $u->id }}_{{ $e->id_edificio }}" data-usuario="{{ $u->id }}"  data-edificio="{{ $e->id_edificio }}"  class="form-check-input chkedificio" type="checkbox">
                                            <label class="form-check-label" for="chkE_{{ $u->id }}_{{ $e->id_edificio }}"></label>
                                        </div>
                                    </td>
                                @endforeach 
                            </tr>
                            @foreach($lista_plantas as $pl)
                                @php
                                    $lista_puestos=$puestos->where('id_planta',$pl->id_planta);           
                                @endphp
                                <tr>
                                    <td class="text-center nowrap" style="background-color:#d3d3d3; font-weight: bold" ><div >{{ $pl->des_planta }}</div></td>
                                    @foreach($usuarios as $u)
                                        <td class="text-center"  style="background-color:#d3d3d3 " title="{{ $pl->des_planta }} -> {{ $u->name }}" id="pastillaP_{{ $u->id }}_{{ $pl->id_planta }}" >
                                            <div class="form-check pt-2">
                                                <input  name="chkP_{{ $u->id }}_{{ $pl->id_planta }}"  id="chkP_{{ $u->id }}_{{ $pl->id_planta }}" data-usuario="{{ $u->id }}"  data-planta="{{ $pl->id_planta }}" data-edificio="{{ $pl->id_edificio }}" class="form-check-input chkplanta" type="checkbox">
                                                <label class="form-check-label"  for="chkP_{{ $u->id }}_{{ $pl->id_planta }}"></label>
                                            </div>
                                        </td>
                                    @endforeach 
                                </tr>
                                @foreach($lista_puestos  as $p)
                                    @php
                                        $marcados=$puestos_users->where('id_puesto',$p->id_puesto)->pluck('id_usuario')->toArray(); 
                                    @endphp
                                    <tr>
                                        <td style="width: 15px">{{ nombrepuesto($p) }}</td>
                                        @foreach($usuarios as $u)
                                            <td class="text-center" title="[{{ $p->cod_puesto }}]{{ $p->des_puesto }} -> {{ $u->name }}" id="pastilla_{{ $u->id }}_{{ $p->id_puesto }}" >
                                                <div class="form-check pt-2">
                                                    <input  {{ in_array($u->id,$marcados)?'checked':'' }} name="chk_{{ $u->id }}_{{ $p->id_puesto }}"  id="chk_{{ $u->id }}_{{ $p->id_puesto }}" data-usuario="{{ $u->id }}"  data-puesto="{{ $p->id_puesto }}"  data-planta="{{ $p->id_planta }}" data-edificio="{{ $p->id_edificio }}"  class="form-check-input chkpuesto" type="checkbox">
                                                    <label class="form-check-label"  for="chk_{{ $u->id }}_{{ $p->id_puesto }}"></label>
                                                </div>
                                            </td>
                                        @endforeach 
                                    </tr>
                                @endforeach
                            @endforeach
                        @endforeach
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('scripts')
	<script>

	$('.configuracion').addClass('active active-sub');
    $('.menu_parametrizacion').addClass('active active-sub');
    $('.menu_usuarios').addClass('active active-sub');
	$('.puestos_supervisores').addClass('active');
    
    $('.chkpuesto').click(function(){
        if($(this).is(':checked'))
            accion='A';
            else accion='D';
        $.get("{{ url('users/add_puesto_supervisor') }}/"+$(this).data('usuario')+"/"+$(this).data('puesto')+"/"+accion,function(data){

        })
    })

    $('.chkplanta').click(function(){
        
        if($(this).is(':checked'))
            accion='A';
            else accion='D';

        $.get("{{ url('users/supervisor_planta') }}/"+$(this).data('usuario')+"/"+$(this).data('planta')+"/"+accion,function(data){
            $('[data-planta='+data.planta+'][data-usuario='+data.usuario+']').prop('checked',data.estado);
        })
    })

    $('.chkedificio').click(function(){
        if($(this).is(':checked'))
            accion='A';
            else accion='D';

        $.get("{{ url('users/supervisor_edificio') }}/"+$(this).data('usuario')+"/"+$(this).data('edificio')+"/"+accion,function(data){
            $('[data-edificio='+data.edificio+'][data-usuario='+data.usuario+']').prop('checked',data.estado);
        })
    })

	</script>

@endsection
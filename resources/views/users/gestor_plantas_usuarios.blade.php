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
    <h1 class="page-header text-overflow pad-no">Gestor de plantas por usuario</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
	<li class="breadcrumb-item">configuracion</li>
    <li class="breadcrumb-item">parametrizacion</li>
	<li class="breadcrumb-item">personas</li>
    <li class="breadcrumb-item active">plantas - usuarios</li>
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
				<h3 class="card-title">Gestor de plantas por usuarios</h3>
			</div>
			<div class="card-body">

                
				<table id="tabla" class="">
					<thead>
						<tr>
							
							<th></th>
                            @foreach ($plantas as $planta)
                                <th style="font-size: 12px;" class="tdplanta"data-planta="{{ $planta->id_planta }}" title="{{ $planta->des_planta }}"><div class="vertical" style="margin-left: 10px;">{{ acronimo($planta->des_planta,30) }}</div></th>
                            @endforeach
						</tr>
					</thead>
					<tbody>
                        @foreach($usuarios as $u)
                            @php
                                $asignadas=$plantas_users->where('id_usuario',$u->id)->pluck('id_planta')->toArray();
                            @endphp
                            <tr>
                                <td class="tduser" data-usuario="{{ $u->id }}" data-checked="false">{{ $u->name }}</td>
                                @foreach($plantas as $pl)
                                    <td class="text-center" style="padding-left:20px" title="{{ $pl->des_planta }} -> {{ $u->name }}" id="pastilla_{{ $u->id }}_{{ $pl->id_planta }}" >
                                        <div class="form-check pt-2">
                                            <input  name="chk_{{ $u->id }}_{{ $pl->id_planta }}"  id="chk_{{ $u->id }}_{{ $pl->id_planta }}" data-usuario="{{ $u->id }}"  data-planta="{{ $pl->id_planta }}" {{ in_array($pl->id_planta,$asignadas)?'checked':'' }} class="form-check-input chkplanta" type="checkbox">
                                            <label class="form-check-label" for="chk_{{ $u->id }}_{{ $pl->id_planta }}"></label>
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
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
	$('.plantas_usuarios').addClass('active');
    
    $('.chkplanta').click(function(){
        if($(this).is(':checked')){
            $.get("{{ url('users/addplanta') }}/"+$(this).data('usuario')+"/"+$(this).data('planta'),function(data){
                $('#pastilla'+$(this).data('usuario')+"_"+$(this).data('planta')).css("background-color",'#02c59b');
            })
        } else {
            $.get("{{ url('users/delplanta') }}/"+$(this).data('usuario')+"/"+$(this).data('planta'),function(data){
                $('#pastilla'+$(this).data('usuario')+"_"+$(this).data('planta')).css("background-color",'#eae3b8');
            })
        }
    })

    $('.tdplanta').dblclick(function(){
        $(this).data('checked',!$(this).data('checked'));
        console.log($(this).data('checked'));
        estado=$(this).data('checked');
        $.get("{{ url('users/addtodaplanta') }}/"+estado+"/"+$(this).data('planta'),function(data){
            $('[data-planta='+data.planta+']').prop('checked',data.estado);
        })
    })

    $('.tduser').dblclick(function(){
        $(this).data('checked',!$(this).data('checked'));
        console.log($(this).data('checked'));
        estado=$(this).data('checked');
        $.get("{{ url('users/addtodouser') }}/"+estado+"/"+$(this).data('usuario'),function(data){
            $('[data-usuario='+data.usuario+']').prop('checked',data.estado);
        })
    })
	

	</script>

@endsection
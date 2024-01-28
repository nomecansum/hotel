@extends('layout')
@section('styles')
<style>

.chk_verde:checked{
	background-color:#28a745;
	}
</style>
@endsection
@section('title')
<h1 class="page-header text-overflow pad-no">Permisos</h1>
@endsection
@section('breadcrumb')
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}">home </a></li>
    <li class="breadcrumb-item">Configuracion</li>
    <li class="breadcrumb-item">Permisos</li>
    <li class="breadcrumb-item active">permisos</li>
</ol>

@endsection

@section('content')
@php
	function checkChecked($n,$s,$t,$permisos)
	{
		$type = "";
		if ($t == "R") {$type = "mca_read";}
		if ($t == "W") {$type = "mca_write";}
		if ($t == "C") {$type = "mca_create";}
		if ($t == "D") {$type = "mca_delete";}
		foreach ($permisos as $permiso)
		{
			//Debugbar::info($permiso);
			if ($n==$permiso->id_perfil && $s==$permiso->id_seccion){
				return $permiso->$type;
				break;
			}
		}

	}
	$primero=true;
    
@endphp
<div class="container-fluid">

	<div class="tab-base mt-3">		
		<!--Nav Tabs-->
		<ul class="nav nav-tabs" role="tablist">
            @php $primero=true;	@endphp
			@foreach($grupos as $grupo)	
				@php
					$cuenta_secciones=$secciones->where('des_grupo',$grupo->des_grupo)->count();	
				@endphp		
                <li class="nav-item {{ ($primero===true) ? "active" :"" }}"  role="presentation">
                    <button class="nav-link {{ ($primero===true) ? "active" :"" }}" data-bs-toggle="tab" data-bs-target="#{{  str_replace(" ","_",$grupo->des_grupo) }}" type="button" role="tab" aria-controls="{{ $grupo->des_grupo }}" aria-selected="{{ ($primero===true) ? "true" :"false" }}"><i class="{{ $grupo->icono }}"></i></span>&nbsp;&nbsp;{{ $grupo->des_grupo }} <span class="badge bg-primary ml-2">{{ $cuenta_secciones }}</span></button>
				</li>
				@php $primero=false;	@endphp
			@endforeach
        </ul>
		<!--Tabs Content-->
		<div class="tab-content" style="overflow: auto; overflow-y: hidden;">
			@php $primero=true;	@endphp
                @foreach($grupos as $grupo)
                    <div id="{{ str_replace(" ","_",$grupo->des_grupo) }}" class="tab-pane fade {{ ($primero===true) ? "active show" :"" }}" role="tabpanel" aria-labelledby="{{ $grupo->des_grupo }}-tab">
                        @php $primero=false;	@endphp
                        <p class="text-main text-semibold">{{ $grupo->des_grupo }}</p>
                        <table class="table table-responsive table-hover   table-striped table-bordered mr-4" style="font-size:12px">
                            <thead>
                                <tr>
                                    <td></td>
                                    @foreach ($niveles as $n)
                                        <td class="text-center celda_{{ $n->cod_nivel }}"  style="font-size: 14px; font-weight: bold; {{ $n->cod_nivel==Auth::user()->cod_nivel?'':'display:none' }}">
                                            {{$n->des_nivel_acceso}}
                                            @if (isAdmin()  && !session('cod_cliente'))  @endif
                                        </td>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($secciones as $sec)
                                    @if($sec->des_grupo==$grupo->des_grupo)
                                        <tr>
                                            <td style="font-size: 14px">{{$sec->des_seccion}}</td>
                                            @foreach ($niveles as $n)
                                                <td style="vertical-align: middle;{{ $n->cod_nivel==Auth::user()->cod_nivel?'':'display:none' }}" class="celda_{{ $n->cod_nivel }} text-center" nowrap>
                                                    <div title="{{trans('strings._permissions.R')}}" class="custom-control custom-checkbox" style="display: inline-block; @if($sec->val_tipo=='Permiso') margin-left: 62px; @endif margin-right: 6px">
                                                        <input @if(!checkPermissions(['Permisos'],["W"]) || (!checkPermissions([$sec->des_seccion],["R"]) && !fullAccess()) || ($n->mca_fijo=='S' && !fullAccess())) readonly disabled @endif {{checkChecked($n->cod_nivel,$sec->cod_seccion,'R',$permisos) ? 'checked' : ''}} type="checkbox" data-type="R" data-section="{{$sec->cod_seccion}}" data-level="{{$n->cod_nivel}}" class="check-permission form-check-input @if($sec->val_tipo=='Permiso') chk_verde @else  @endif" id="read{{$sec->cod_seccion}}-{{$n->cod_nivel}}">
                                                        <label class="form-check-label" for="read{{$sec->cod_seccion}}-{{$n->cod_nivel}}" >@if($sec->val_tipo=='Seccion')R @endif</label>
                                                    </div>
                                                    @if($sec->val_tipo=='Seccion')
                                                    <div title="{{trans('strings._permissions.W')}}" class="custom-control custom-checkbox" style="display: inline-block; margin-right: 6px">
                                                        <input @if(!checkPermissions(['Permisos'],["W"]) || (!checkPermissions([$sec->des_seccion],["W"]) && !fullAccess()) || ($n->mca_fijo=='S' && !fullAccess()))  readonly disabled  @endif {{checkChecked($n->cod_nivel,$sec->cod_seccion,'W',$permisos) ? 'checked' : ''}} type="checkbox" data-type="W" data-section="{{$sec->cod_seccion}}" data-level="{{$n->cod_nivel}}" class="check-permission form-check-input @if($sec->val_tipo=='Permiso') chk_verde @else  @endif" id="write{{$sec->cod_seccion}}-{{$n->cod_nivel}}">
                                                        <label class="form-check-label" for="write{{$sec->cod_seccion}}-{{$n->cod_nivel}}" >W</label>
                                                    </div>

                                                    <div title="{{trans('strings._permissions.C')}}" class="custom-control custom-checkbox " style="display: inline-block; margin-right: 6px">
                                                        <input @if(!checkPermissions(['Permisos'],["W"]) || (!checkPermissions([$sec->des_seccion],["C"]) && !fullAccess()) || ($n->mca_fijo=='S' && !fullAccess()))  readonly disabled  @endif {{checkChecked($n->cod_nivel,$sec->cod_seccion,'C',$permisos) ? 'checked' : ''}} type="checkbox" data-type="C" data-section="{{$sec->cod_seccion}}" data-level="{{$n->cod_nivel}}" class="check-permission form-check-input @if($sec->val_tipo=='Permiso') chk_verde @else  @endif" id="create{{$sec->cod_seccion}}-{{$n->cod_nivel}}">
                                                        <label class="form-check-label" for="create{{$sec->cod_seccion}}-{{$n->cod_nivel}}" >C</label>
                                                    </div>

                                                    <div title="{{trans('strings._permissions.D')}}" class="custom-control custom-checkbox" style="display: inline-block; margin-right: 6px">
                                                        <input @if(!checkPermissions(['Permisos'],["W"]) || (!checkPermissions([$sec->des_seccion],["D"]) && !fullAccess()) || ($n->mca_fijo=='S' && !fullAccess()))  readonly disabled  @endif {{checkChecked($n->cod_nivel,$sec->cod_seccion,'D',$permisos) ? 'checked' : ''}} type="checkbox" data-type="D" data-section="{{$sec->cod_seccion}}" data-level="{{$n->cod_nivel}}" class="check-permission form-check-input @if($sec->val_tipo=='Permiso') chk_verde @else  @endif" id="delete{{$sec->cod_seccion}}-{{$n->cod_nivel}}">
                                                        <label class="form-check-label" for="delete{{$sec->cod_seccion}}-{{$n->cod_nivel}}" >D</label>
                                                    </div>
                                                    @endif
                                                </td>
                                            @endforeach
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endforeach
		</div>
	</div>
	<div class="row mt-4">
        <div class="col-md-12">
            <label class="text-info">Perfiles</label>
            <select class="select2 col-12" style="width: 100%; " multiple="" tabindex="-1" aria-hidden="true" name="niveles" id="niveles">
                <option value=""></option>
                @foreach($niveles as $nivel)
                <option {{ $nivel->cod_nivel==Auth::user()->cod_nivel?'selected':'' }} value="{{ $nivel->cod_nivel }}">
                    {{ $nivel->des_nivel_acceso }}
                    @if (isAdmin() && !session('cod_cliente')) @endif
                </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

@endsection
@section('scripts')

<script>
	$('.configuracion').addClass('active active-sub');
    $('.menu_permisos').addClass('active active-sub');
	$('.permisos').addClass('active');
		
	$('.check-permission').on('change', function(event){
		$(this).data('_token','{{csrf_token()}}');
		if ($(this).is(':checked')) {
			console.log($(this).data());
			$.post('{{url('addPermissions')}}', $(this).data(), function(data, textStatus, xhr) {
			});
		}else{
			$.post('{{url('removePermissions')}}', $(this).data(), function(data, textStatus, xhr) {
			});
		}
	});

	$('#niveles').on('select2:select', function (e) {
		var data = e.params.data;
		$(".celda_"+data.id).fadeToggle();
		console.log(data.id);
	});
	$('#niveles').on('select2:unselect', function (e) {
		var data = e.params.data;
		$(".celda_"+data.id).fadeToggle();
		console.log(data.id);
	});

	// $('.flat-green').iCheck({
    //   checkboxClass: 'icheckbox_flat-green',
    //   radioClass   : 'iradio_flat-green'
	// })

	// $('.flat-blue').iCheck({
    //   checkboxClass: 'icheckbox_flat-blue',
    //   radioClass   : 'iradio_flat-blue'
    // })
</script>

@endsection

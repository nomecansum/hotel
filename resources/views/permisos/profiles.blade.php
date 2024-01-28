@extends('layout')
@section('title')
<h1 class="page-header text-overflow pad-no">Pefiles</h1>
@endsection
@section('breadcrumb')
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}">home </a></li>
    <li class="breadcrumb-item">Configuracion</li>
    <li class="breadcrumb-item">Permisos</li>
    <li class="breadcrumb-item active">perfiles</li>
</ol>

@endsection
@section('content')
<div class="row botones_accion mb-2">
	<div class="col-md-4">

	</div>
	<div class="col-md-6">
		<br>
	</div>
	<div class="col-md-2 text-end">
		<div class="btn-group btn-group-sm pull-right mt-2" role="group" style="margin-right: 20px;">
				<a href="#" id="btn_nueva_seccion" onclick="nueva()" class="btn btn-success" title="Nuevo perfil">
				<span class="fa fa-plus-square pt-1" style="font-size: 20px" aria-hidden="true"></span> Nuevo
			</a>
		</div>
	</div>
</div>

<div class="container-fluid">

	<div class="row">
		<div class="col-md-12">
			<div class="card box-solid mb-5" id="editor" style="display:none">
				
			</div>
		</div>
	</div>
    
	<div class="row">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Perfiles de acceso</h3>
				</div>
			    <div class="card-body collapse show">
			        {{-- <h2 class="card-title float-left">{{trans('strings.profiles')}}</h2> --}}
			        {{-- @include('resources.combo_clientes') --}}
			        <div class="table-responsive mt-40">
			            <table id="myTable"
						data-toggle="table" data-mobile-responsive="true"
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
						data-show-button-text="true">
			                <thead>
			                    <tr>
									<th>ID  </th>
									<th style="width: 2%">Nivel</th>
			                        <th style="width: 30%" class="text-center">Pefil</th>
									@if(isAdmin())<th style="width: 80px" class="text-center">Fijo</th>@endif
									<th style="width: 80px" class="text-center">Multiple</th>
									<th style="width: 80px" class="text-center">Sabados</th>
									<th style="width: 80px" class="text-center">Domingos</th>
									<th style="width: 80px" class="text-center">Festivos</th>
									<th style="width: 80px" class="text-center"><i class="fa-solid fa-user-group"></i> Usuarios</th>
									<th></th>
			                    </tr>
			                </thead>
			                <tbody>
			                	@foreach ($niveles as $nivel)
			                		<tr class="hover-this pad-all" data-perfil="{{$nivel->cod_nivel}}" data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}">
										<td style="width:4%">{{$nivel->cod_nivel}}</td>
										
										<td>{{$nivel->val_nivel_acceso}}</td>
										<td>{{$nivel->des_nivel_acceso}}</td>
										@if(isAdmin())
										<td class="text-center">
											<div class="form-check pt-2 fs-4 ml-3">
												<input readonly  value="S" {{ $nivel->mca_fijo=='S'?'checked':'' }} class="form-check-input" type="checkbox">
												<label class="form-check-label" for="mca_fijo{{ $nivel->cod_nivel }}"></label>
											</div>
										</td>
										@endif
										<td class="text-center">
											<div class="form-check pt-2 fs-4 ml-3">
												<input readonly  value="S" {{ $nivel->mca_reserva_multiple=='S'?'checked':'' }} class="form-check-input" type="checkbox">
												<label class="form-check-label" for="mca_reserva_multiple{{ $nivel->cod_nivel }}"></label>
											</div>
										</td>
										<td class="text-center">
											<div class="form-check pt-2 fs-4 ml-3">
												<input  readonly  value="S" {{ $nivel->mca_reservar_sabados=='S'?'checked':'' }} class="form-check-input" type="checkbox">
												<label class="form-check-label" for="mca_reservar_sabados{{ $nivel->cod_nivel }}"></label>
											</div>
										</td>
										<td class="text-center">
											<div class="form-check pt-2 fs-4 ml-3">
												<input readonly  value="S" {{ $nivel->mca_reservar_domingos=='S'?'checked':'' }} class="form-check-input" type="checkbox">
												<label class="form-check-label"  for="mca_reservar_domingos{{ $nivel->cod_nivel }}"</label>
											</div>
										</td>
										<td class="text-center">
											<div class="form-check pt-2 fs-4 ml-3">
												<input readonly  value="S" {{ $nivel->mca_reservar_festivos=='S'?'checked':'' }} class="form-check-input" type="checkbox">
												<label class="form-check-label" for="mca_reservar_festivos{{ $nivel->cod_nivel }}"></label>
											</div>
										</td>
										<td  class="text-center">
											{{ $cuenta->where('cod_nivel',$nivel->cod_nivel)->first()->cuenta??0 }}
										</td>
			                			<td style="position: relative;">
                                            @if(($nivel->mca_fijo=='S' && isAdmin()) || $nivel->id_cliente==Auth::user()->id_cliente)
												<div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
													<div class="btn-group btn-group pull-right ml-1" role="group">
														<a href="#" class="btn btn-info btn-xs btn_editar add-tooltip" title="Editar perfil"  data-perfil="{{$nivel->cod_nivel}}" onclick="editar({{$nivel->cod_nivel}})" data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}"><span class="fa fa-pencil" aria-hidden="true"></span> Edit</a>
														<a href="#eliminar-usuario-{{$nivel->cod_nivel}}" data-toggle="modal" data-perfil="{{$nivel->cod_nivel}}" onclick="del({{$nivel->cod_nivel}})"  data-nombre="{{$nivel->des_nivel_acceso}}"  data-num="{{$nivel->val_nivel_acceso}}" class="btn btn-danger  btn-xs add-tooltip" title="Borrar perfil" ><span class="fa fa-trash" aria-hidden="true"></span> Del</a>
													</div>
												</div>
											@endif
                                            <div class="modal fade" id="eliminar-usuario-{{$nivel->cod_nivel}}">
                                                <div class="modal-dialog modal-sm">
                                                    <div class="modal-content">
														<div class="modal-header">
															<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
															<h1 class="modal-title text-nowrap">Borrar perfil</h1>
															<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
																<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
															</button>
														</div>    
														<div class="modal-body">
															¿Borrar perfil {{$nivel->des_nivel_acceso}}?
														</div>    
                                                        <div class="modal-footer">
                                                            <a class="btn btn-info" href="{{url('profiles/delete',$nivel->cod_nivel)}}">{{trans('strings.yes')}}</a>
                                                            <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">{{trans('strings.cancel')}}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
			                		     </td>
			                		</tr>
			                	@endforeach
			                </tbody>
			            </table>
			        </div>
			    </div>
			</div>

    </div>
</div>
@endsection

@section('scripts')
	<script>
	$('.configuracion').addClass('active active-sub');
	$('.menu_permisos').addClass('active active-sub');
	$('.perfiles').addClass('active');
	
	$(function(){
		$('#nn').change(function(event){
			if($('#nn').val()!=''){
				$('#warning_level').show();
			} else {
				$('#warning_level').hide();
			}
		});
	})

	function nueva(){
		$('#editor').show();
		$('#editor').load("{{ url('profiles/edit') }}/0", function(data){
			$('.box-title').html("Crear perfil");
			$('#formperfil').attr("action","{{url('profiles/save')}}")
		});
		animateCSS('#editor','bounceInRight');
		$('.box-title').html("Crear perfil");
		$('#id').val(0);
		$('#des_nivel_acceso').val("");
		$('#num_nivel_acceso').val("");
		$('#formperfil').attr("action","{{url('profiles/save')}}")
	}

	function editar(id){
		$('#editor').show();
		animateCSS('#editor','bounceInRight');
		$('#editor').load("{{ url('profiles/edit') }}/"+id, function(data){
			$('.box-title').html("Editar perfil");
			$('#formperfil').attr("action","{{url('profiles/update')}}")
		});
	}

	function del(id){
		$('#eliminar-usuario-'+id).modal('show');
	}

	
	</script>
@endsection

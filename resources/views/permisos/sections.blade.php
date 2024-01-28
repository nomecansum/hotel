@extends('layout')
@section('title')
<h1 class="page-header text-overflow pad-no">Secciones</h1>
@endsection
@section('breadcrumb')
<!-- Content Header (Page header) -->
<ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="{{url('/')}}">home </a></li>
    <li class="breadcrumb-item">Configuracion</li>
    <li class="breadcrumb-item">Permisos</li>
    <li class="breadcrumb-item active">secciones</li>
</ol>

@endsection
@section('content')
<div class="container-fluid">
	<div class="row botones_accion mb-2">
		<div class="col-md-4">

		</div>
		<div class="col-md-6">
			<br>
		</div>
		<div class="col-md-2 text-end">
			<div class="btn-group btn-group-sm pull-right" role="group">
					<a href="#" id="btn_nueva_seccion" onclick="nueva()" class="btn btn-success" title="Nueva seccion">
					<i class="fa fa-plus-square pt-2"  style="font-size: 20px" aria-hidden="true"></i>
					<span>Nueva</span>
				</a>
			</div>
		</div>
	</div>

    <div class="row">
		<div class="card  mb-5 col-md-12" id="editor" style="display:none">
			<div class="card-header toolbar">
				<div class="toolbar-start">
					<h5 class="m-0">@isset ($s)Editar seccion @else Crear seccion @endisset</h5>
				</div>
				<div class="toolbar-end">
					<button type="button" class="btn-close btn-close-card">
						<span class="visually-hidden">Close the card</span>
					</button>
				</div>
			</div>
			<div class="card-body">
					<form action="{{url('sections/save')}}" method="POST" class="form-ajax"  id="formseccion">
						<input type="hidden" name="id" value="0" id="id">
					{{csrf_field()}}
					<div class="row">
						<div class="form-group col-md-4">
							<label for="">Nombre</label>
							<input type="text" name="des_seccion" id="des_seccion" class="form-control" required value="{{isset($s) ? $s->des_seccion : ''}}">
						</div>
						<div class="form-grou col-md-2">
							<label for="">Tipo</label>
							<select class="form-control" required id="val_tipo" data-placeholder="Seleccione tipo" style="width: 100%; color #000;" tabindex="-1" aria-hidden="true" name="val_tipo">
								@foreach($tipos as $nivel)
								<option {{ isset($s->val_tipo) && $nivel==$s->val_tipo ? 'selected' : '' }}  value="{{ $nivel }}">{{ $nivel }}</option>
								@endforeach
							</select>
						</div>

						<div class="form-group col-md-4">
							<label for="">Grupo</label>
							<select  name="des_grupo" required class="form-control" id="des_grupo" data-placeholder="Seleccione grupo" style="width: 100%; color #000;" tabindex="-1" aria-hidden="true">
								@foreach($grupos as $nivel)
								<option {{ isset($s->des_grupo) && $nivel->des_grupo==$s->des_grupo ? 'selected' : '' }} data-icon="{{ $nivel->icono }}"  value="{{ $nivel->des_grupo }}">{{ $nivel->des_grupo }}</option>
								@endforeach
							</select>
						</div>
						<div class="col-md-2">
							<button type="submit" class="btn btn-primary" style="margin-top: 23px">Guardar</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div class="row">
		<div class="formfestivo">
			<div class="card">
				<div class="card-header">
					<h3 class="card-title">Secciones</h3>
				</div>
			    <div class="card-body">
			        <div class="table-responsive m-t-40">
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
			                        <th>ID</th>
									<th class="sorting">Seccion</th>
									<th class="sorting">Tipo</th>
									<th class="sorting">Grupo</th>
			                        {{-- <th></th> --}}
			                    </tr>
			                </thead>
			                <tbody>
			                	@foreach ($secciones as $secc)
			                		<tr class="hover-this">
										<td>{{$secc->cod_seccion}}</td>
										<td>{{$secc->des_seccion}}</td>
										<td class="text-{{ $secc->val_tipo=='Seccion' ? 'info' : 'success' }}">{{ $secc->val_tipo}}</td>
			                			<td style="position: relative;"><i class="{{ $secc->icono }}" style="font-size: 24px"></i> {{$secc->des_grupo}}
			                				<div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
												<div class="btn-group btn-group pull-right ml-1" role="group">
													<a href="#"  class="btn btn-info btn-xs btn_editar add-tooltip" title="Editar seccion" data-seccion="{{ $secc->cod_seccion }}" onclick="editar({{ $secc->cod_seccion}},'{{$secc->des_seccion }}','{{ $secc->des_grupo }}' ,'{{ $secc->val_tipo }}')" data-nombre="{{$secc->des_seccion}}" data-tipo="{{ $secc->val_tipo}}" data-grupo="{{$secc->des_grupo}}"> <span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>
													<a href="#eliminar-usuario-{{$secc->cod_seccion}}" data-target="#eliminar-usuario-{{$secc->cod_seccion}}" title="Borrar seccion" onclick="del({{ $secc->cod_seccion }})" data-toggle="modal" class="btn btn-danger btn-xs add-tooltip"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>
												</div>
			                				</div>
			                				<div class="modal fade" id="eliminar-usuario-{{$secc->cod_seccion}}" style="display: none;">
			                					<div class="modal-dialog">
			                						<div class="modal-content">
														<div class="modal-header">
															<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
															<h1 class="modal-title text-nowrap">Borrar seccion</h1>
															<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
																<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
															</button>
														</div>
														<div class="modal-body">
															¿Borrar seccion {{$secc->des_seccion}}?
														</div>
													
			                							<div class="modal-footer">
			                								<a class="btn btn-info" href="{{url('sections/delete',$secc->cod_seccion)}}">{{trans('strings.yes')}}</a>
			                								<button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()" >{{trans('strings.cancel')}}</button>
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
</div>
<div class="modal fade" id="modal-default" style="display: none;">
		<div class="modal-dialog">
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
				<span aria-hidden="true"><i class="fa-solid fa-circle-xmark"></i></span></button>
			  <h4 class="modal-title">Default Modal</h4>
			</div>
			<div class="modal-body">
			  <p>One fine body…</p>
			</div>
			<div class="modal-footer">
			  <button type="button" class="btn btn-default pull-left" data-dismiss="modal">Close</button>
			  <button type="button" class="btn btn-primary">Save changes</button>
			</div>
		  </div>
		  <!-- /.modal-content -->
		</div>
		<!-- /.modal-dialog -->
	  </div>
@stop
@section('scripts')
<script>
	function iformat(icon) {
		var originalOption = icon.element;
		return $('<span><i class="mdi ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
	}
	
	$('.configuracion').addClass('active active-sub');
	$('.menu_permisos').addClass('active active-sub');
	$('.secciones').addClass('active');


	function nueva(){
		$('#editor').show();
		animateCSS('#editor','bounceInRight');
		$('#id').val(0);
		$('#des_seccion').val("");
		$('#val_tipo').val("");
		$('#des_grupo').val("");
		$('#formseccion').attr("action","{{url('sections/save')}}")
	}

	function editar(id,des,grupo,tipo){
		$('#editor').show();
		animateCSS('#editor','bounceInRight');
		//console.log($(this).data('seccion'));
		$('#id').val(id);
		$('#des_seccion').val(des);
		$('#val_tipo').val(tipo);
		$('#des_grupo').val(grupo);
		$('#formseccion').attr("action","{{url('sections/update')}}")
	}

	function del(id){
		$('#eliminar-usuario-'+id).modal('show');
	}
</script>
@endsection

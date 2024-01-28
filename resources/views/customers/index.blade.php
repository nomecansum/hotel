@extends('layout')




@section('styles')
	
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Empresas</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
	<li class="breadcrumb-item">configuración</li>
	<li class="breadcrumb-item">parametrizacion</li>
	<li class="breadcrumb-item">espacios</li>
	<li class="breadcrumb-item active">empresas</li>
	{{--  <li class="breadcrumb-item active">Editar usuario {{ !empty($users->name) ? $users->name : '' }}</li>  --}}
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
		@if(checkPermissions(['Clientes'],['C']))
		<div class="btn-group btn-group-sm pull-right" role="group">
				<a href="#" id="btn_nueva_puesto" class="btn btn-success" title="Nueva cliente">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nuevo</span>
			</a>
		</div>
		@endif
	</div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="row mt-2">
	<div class="col-12">
		<div class="card">
			<div class="card-body">
				<table id="tabla"  data-toggle="table" data-mobile-responsive="true"
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
					data-show-button-text="true"
                    >
					<thead>
						<tr>
							<th>Id</th>
							<th></th>
							<th>Nombre</th>
							<th>Puestos</th>
							<th>Edificios</th>
							<th>Plantas</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						@foreach ($clientes as $cus)
							<tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
								<td>{{$cus->id_cliente}}</td>
								<td class="text-center no-sort">
									@isset($cus->img_logo)
										<img src="{{Storage::disk(config('app.img_disk'))->url('img/clientes/images/'.$cus->img_logo)}}" width="40px" alt="">
									@endif
								</td>
								<td>{{$cus->nom_cliente}}</td>
								<td>{{ $cus->puestos}}</td>
								<td>{{ $cus->edificios}}</td>
								<td>{{ $cus->plantas}}</td>
								<td style="position: relative;" class="pt-2">
									<div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
										<div class="btn-group btn-group pull-right ml-1" role="group">
											@if (checkPermissions(['Clientes'],["W"]))<a href="#" title="Editar cliente" data-id="{{ $cus->id_cliente }}" class="btn btn-xs btn-info add-tooltip btn_edit" onclick="edit({{ $cus->id_cliente }})"><span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a>@endif
											@if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-usuario-{{$cus->id_cliente}}" onclick="del({{ $cus->id_cliente }})" title="Borrar cliente" data-toggle="modal" class="btn btn-xs btn-danger add-tooltip "><span class="fa fa-trash pt-1" aria-hidden="true"></span> Del</a>@endif
											{{--  @if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-Cliente-{{$cus->id_cliente}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif  --}}
										</div>
									</div>
									@if (checkPermissions(['Clientes'],["D"]))
										
										<div class="modal fade" id="eliminar-Cliente-{{$cus->id_cliente}}">
											<div class="modal-dialog modal-md">
												<div class="modal-content">
													<div class="modal-header">
														<div><img src="/img/Mosaic_brand_20.png" alt="qrclean" class="float-right"></div>
														<h1 class="modal-title text-nowrap">Borrar cliente </h1>
														<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
															<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
														</button>
													</div>

													<div class="modal-body text-start">
														<div class="alert alert-danger"><b>Esta opción no se podrá deshacer! Seguro que quiere seguir?</b></div><br>
														El cliente tiene:<br>
														<ul>
															<li>{{ $cus->puestos }} Puestos</li>
															<li>{{ $cus->edificios }} Edificios</li>
															<li>{{ $cus->plantas }} Plantas</li>
														</ul>
													</div>
													<div class="modal-footer">
														<a class="btn btn-info" href="{{url('/clientes/deleteCompleto',$cus->id_cliente)}}">¡Si!</a>
														<button type="button" data-dismiss="modal" class="btn btn-warning close">Cancelar</button>
													</div>
												</div>
											</div>
										</div>
									@endif
								</td>
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
	$('.espacios').addClass('active active-sub');
	$('.clientes').addClass('active');

	$('#btn_nueva_puesto').click(function(){
       $('#editorCAM').load("{{ url('/clientes/edit/0') }}", function(){
		animateCSS('#editorCAM','bounceInRight');
	   });
	  // window.scrollTo(0, 0);
      //stopPropagation()
	});

	function edit(id){
		$('#editorCAM').load("{{ url('/clientes/edit/') }}"+"/"+id, function(){
			animateCSS('#editorCAM','bounceInRight');
		});
	}


    $('.td').click(function(event){
        editar( $(this).data('id'));
	})

	function del(id){
		$('#eliminar-Cliente-'+id).modal('show');
	}


	</script>

@endsection
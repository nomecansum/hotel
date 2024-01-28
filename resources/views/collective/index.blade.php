@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Colectivos</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuracion</li>
		<li class="breadcrumb-item">parametrizacion</li>
		<li class="breadcrumb-item">personas</li>
        <li class="breadcrumb-item active">colectivos</li>
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
        @if(checkPermissions(['Colectivos'],['C']))
        <div class="btn-group btn-group-sm pull-right" role="group">
			<a href="#" class="btn float-right hidden-sm-down btn-success btn_nuevo">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nuevo</span>
			</a>
        </div>
        @endif
    </div>
</div>
<div id="editorCAM" class="mt-2">

</div>
<div class="card">
    <div class="card-header">

    </div>
    <div class="card-body">
		<div class="table-responsive">
			<table id="tabladeps"  data-toggle="table" data-mobile-responsive="true"
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
						<th data-width="5%"  data-sortable="true" >ID</th>
						<th data-sortable="true"  style="width: 80%" >Nombre</th>
						<th data-width="250" data-sortable="true"  >Cliente</th>
						<th data-width="1%" data-sortable="true"  >Informes</th>
						<th data-width="80" data-sortable="true" style="width: 1%" class="text-center" ><i class="fa-solid fa-user add-tooltip" title="Usuarios en el colectivo"></i></th>
					</tr>
				</thead>
				<tbody>
					@php $idcl=0; @endphp
					@foreach ($colectivos as $col)
						
						<tr class="hover-this">
							<td>{{$col->cod_colectivo}}</td>
							<td>{{$col->des_colectivo}}</td>
							<td>{{ $col->nom_cliente }}</td>
							<td class="text-center">
								<div class="form-check fs-4 ml-4">
									<input  class="form-check-input ml-4" type="checkbox" {{$col->mca_noinformes == "S" ? 'checked' : ''}} >
								</div>
							</td>
							<td style="position: relative;" class="text-center">
								{{ $col->cuenta }}
								
								<div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
									<div class="btn-group btn-group pull-right ml-1" role="group">
										@if (checkPermissions(['Colectivos'],["C"]))<a href="#" onclick="editar({{ $col->cod_colectivo }},'{{ $col->des_colectivo }}','{{ $col->id_cliente }}','{{ $col->mca_noinformes }}','{{ $col->nom_cliente }}')" class="btn btn-xs btn-info"><span class="fa fa-pencil pt-1" aria-hidden="true"></span> Edit</a></a>@endif
										@if (checkPermissions(['Colectivos'],["D"]))<a href="#eliminar-colectivo-{{$col->cod_colectivo}}" onclick="del({{ $col->cod_colectivo }})" data-toggle="modal" class="btn btn-xs btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> Del</a></a>@endif
									</div>
								</div>
								<div class="modal fade" id="eliminar-colectivo-{{$col->cod_colectivo}}">
									<div class="modal-dialog">
										<div class="modal-content">
											<div class="modal-header">
												<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
												<h1 class="modal-title text-nowrap">Borrar colectivo </h1>
												<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
													<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
												</button>
											</div>    
											<div class="modal-body">
												¿Borrar colectivo {{ $col->des_colectivo }}?
											</div>
					
											<div class="modal-footer">
												<a class="btn btn-info" href="{{url('collective/delete',$col->cod_colectivo)}}">{{trans('strings.yes')}}</a>
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


@endsection


@section('scripts')
    <script>
        $('.configuracion').addClass('active active-sub');
		$('.menu_parametrizacion').addClass('active active-sub');
		$('.menu_usuarios').addClass('active active-sub');
        $('.colectivos').addClass('active');

		function editar(id){
			$('#editorCAM').load("{{ url('/collective/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
		}

		function del(id){
			$('#eliminar-colectivo-'+id).modal('show');
		}

		$('.btn_nuevo').click(function(){
            $('#editorCAM').load("{{ url('/collective/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });

        });
    </script>
@endsection



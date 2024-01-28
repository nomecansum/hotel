@extends('layout')

@section('title')
    <h1 class="page-header text-overflow pad-no">Departamentos</h1>
@endsection

@section('styles')

@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
        <li class="breadcrumb-item">configuracion</li>
		<li class="breadcrumb-item">parametrizacion</li>
		<li class="breadcrumb-item">personas</li>
        <li class="breadcrumb-item active">departamentos</li>
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
        @if(checkPermissions(['Departamentos'],['C']))
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
		<div class="row botones_accion">
			<div class="col-md-8">
				<span class="float-right" id="loadfilter" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
			</div>
			<div class="col-md-2 text-end">
				
			</div>
			<div class="col-md-2 text-end">
				<div class="btn-group" role="group" aria-label="Basic radio toggle button group">
					<input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked="">
					<label class="btn btn-outline-primary btn-xs boton_modo" data-href="departments" for="btnradio1"><i class="fa-light fa-list-timeline"></i> Listado</label>
					
					<input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
					<label class="btn btn-outline-primary btn-xs boton_modo" data-href="departments/estructura" for="btnradio2"><i class="fa-regular fa-list-tree"></i> Estructura</label>
				</div>
			</div>
		</div>
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
						<th data-sortable="true" style="width: 20px" >ID</th>
						<th data-sortable="true"  style="width: 60%" >Nombre</th>
						<th data-sortable="true" class="text-center"  ><i class="fa-solid fa-user add-tooltip" title="Usuarios en el departamento"></i></th>
					</tr>
				</thead>
				<tbody> 
					@php  
							$deps = lista_departamentos('global', 0, $r); 
							$idcl = 0;
					@endphp
					@if(isset($deps))
						@foreach ($deps as $d)
							<tr class="hover-this" @if (checkPermissions(['Departamentos'],["W"]))data-href="{{url('departments/edit',$d->cod_departamento)}}"@endif>
								@if(isAdmin())<td>{{$d->cod_departamento}}</td>@endif
								<td style="padding-left: {{ $d->num_nivel>1 ? (($d->num_nivel-1)*40) : "" }}px">{{$d->nom_departamento}}</td>
								<td style="position: relative; padding-left: 40px;">{{ $d->empleados}}
								
									<div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
										<div class="btn-group btn-group pull-right ml-1" role="group">
											{{-- <label style="cursor: pointer" data-toggle="modal" data-target="#employs-{{$d->cod_departamento}}" class="label label-info" title="Empleados"><i class="mdi mdi-account"></i> ({{$d->empleados}})</label> --}}
											@if (checkPermissions(['Departamentos'],["W"]))<a href="#" data-id="{{$d->cod_departamento}}" class="btn btn-xs btn-info btn_editar add-tooltip" onclick="editar({{$d->cod_departamento}})"><span class="fa fa-pencil pt-1" aria-hidden="true" ></span> Edit</a></a>@endif
											@if (checkPermissions(['Departamentos'],["D"]))<a href="#eliminar-usuario-{{$d->cod_departamento}}" data-toggle="modal" onclick="del({{$d->cod_departamento}})" class="btn btn-xs btn-danger"><span class="fa fa-trash" aria-hidden="true"></span> Del</a>@endif
										</div>
									</div>
									
									<div class="modal fade" id="eliminar-usuario-{{$d->cod_departamento}}">
										<div class="modal-dialog">
											<div class="modal-content">
							
												<div class="modal-header">
													<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
													<h1 class="modal-title text-nowrap">Borrar departamento </h1>
													<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
														<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
													</button>
												</div>    
												<div class="modal-body">
													¿Borrar departamento {{ $d->nom_departamento }}?
												</div>
							
												<div class="modal-footer">
													<a class="btn btn-info" href="{{url('departments/delete',$d->cod_departamento)}}">{{trans('strings.yes')}}</a>
													<button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">{{trans('strings.cancel')}}</button>
												</div>
											</div>
										</div>
									</div>
								</td>
							</tr>
						@endforeach
					@endif
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
        $('.departamentos').addClass('active');

		function editar(id){
			$('#editorCAM').load("{{ url('/departments/edit/') }}"+"/"+id, function(){
                animateCSS('#editorCAM','bounceInRight');
            });
		}

		function del(id){
			$('#eliminar-usuario-'+id).modal('show');
		}

		$('.btn_nuevo').click(function(){
            $('#editorCAM').load("{{ url('/departments/create') }}", function(){
                animateCSS('#editorCAM','bounceInRight');
            });
            // window.scrollTo(0, 0);
            //stopPropagation()
        });

		$('.boton_modo').click(function(){
			window.location.href="{{ url('/') }}/"+$(this).attr('data-href');
		});
    </script>
@endsection

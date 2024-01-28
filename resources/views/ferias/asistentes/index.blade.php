@extends('layout')




@section('styles')
<link href="{{url('/plugins/dropzone/dropzone.css')}}" rel="stylesheet">
@endsection

@section('title')
    <h1 class="page-header text-overflow pad-no">Gestión de asistentes a feria</h1>
@endsection

@section('breadcrumb')
<ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="{{url('/')}}" class="link-light">Home </a> </li>
	<li class="breadcrumb-item">ferias</li>
	<li class="breadcrumb-item">asistentes</li>
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
	<div class="col-md-6">
		<br>
	</div>
	<div class="col-md-2 text-end">
		@if(checkPermissions(['Incidencias'],['C']))
		<div class="btn-group btn-group-sm pull-right" role="group">
				<a href="javascript:void(0)" id="btn_nueva" class="btn btn-success" data-toggle="modal" title="Nueva marca">
				<i class="fa fa-plus-square pt-2" style="font-size: 20px" aria-hidden="true"></i>
				<span>Nueva</span>
			</a>
		</div>
		@endif
	</div>
</div>
<div id="editorCAM" class="mt-2">

</div>

<div class="row mt-2">
	<div id="div_filtro">
		<form method="post" name="formbuscador" id="formbuscador" action="{{ url('/ferias/asistentes/search') }}" class>
			@csrf
			<input type="hidden" name="document" value="pantalla">
			@include('resources.combos_filtro',[$hide=['tag'=>1,'est_inc'=>1,'pue'=>1,'tip_mark'=>1,'est'=>1,'tip_inc'=>1, 'pla'=>1, 'edi'=>1, 'tip'=>1, 'usu'=>1],$show=[]])
		</form>
	</div>
	
</div>
<form method="post" name="form_puestos" id="frm" action="{{ url('/ferias/asistentes/print_qr') }}" class>
	@csrf
<div class="row mt-2">
	<div class="col-12">
		<div class="card">
			<div class="card-header">
				<h3 class="card-title">Asistentes</h3>
			</div>
			
			<div class="card-body">
				@if(isset($datos)){{ $datos->count() }} asistentes @endif
				<div id="all_toolbar">
					<div class="row">
						<div class="col-md-4">
							<div class="form-check pt-2">
								<input id="chktodos" name="chktodos" class="form-check-input" type="checkbox">
								<label for="_dm-gridCheck" class="form-check-label">Todos</label>
							</div>
						</div>
						<div class="col-md-4">
							<div class="btn-group">
								<button type="button" class="btn btn-secondary dropdown-toggle p-2" data-bs-toggle="dropdown" aria-expanded="false"><i class="fad fa-poll-people pt-2" aria-hidden="true"></i> Acciones</button>
								<ul class="dropdown-menu" style="">
									<li class="dropdown-item"><a href="#" class="btn_qr dropdown-item"><i class="fad fa-qrcode"></i> Imprimir QR</a></li>
									<li class="dropdown-item"><a href="#" class="btn_export_qr dropdown-item"><i class="fad fa-file-export"></i></i> Exportar QR</a></li>
								</ul>
							</div>
						</div>
					</div>
				</div>
					
				<table id="tabla"  data-toggle="table" data-mobile-responsive="true"
					data-locale="es-ES"
					data-search="true"
					data-show-columns="true"
					data-show-columns-toggle-all="true"
					data-page-list="[5, 10, 20, 30, 40, 50,100,200]"
					data-page-size="50"
					data-pagination="true"
					data-show-pagination-switch="true"
					data-show-button-text="true"
					data-toolbar="#all_toolbar"
					>
					<thead>
						<tr>
							<th></th>
							<th data-sortable="true">Id</th>
							<th data-sortable="true">Nombre</th>
							<th data-sortable="true">e-mail</th>
							<th data-sortable="true">Empresa</th>
							<th data-sortable="true">Fecha</th>
							<th data-sortable="true">Envío</th>
							<th data-sortable="true">Registrado</th>
							<th data-sortable="true">Feria</th>
							<th data-sortable="true">Observaciones</th>
						</tr>
					</thead>

                    <tbody>
                        @foreach ($datos as $dato)
                            <tr class="hover-this" @if (checkPermissions(['Clientes'],["W"])) @endif>
                                <td class="text-center">
                                    <div class="form-check">
										<input name="lista_id[]" data-id="{{ $dato->id_contacto }}" id="chkp{{ $dato->id_contacto }}" value="{{ $dato->id_contacto }}" class="form-check-input chkpuesto" type="checkbox">
										<label class="form-check-label" for="chkp{{ $dato->id_contacto  }}"></label>
									</div>
                                </td>
								<td>{{$dato->id_contacto}}</td>
                                <td>{{$dato->nombre}}</td>
								<td style="word-wrap: break-wor; word-break:break-all; font-size:12px">{{$dato->email}}</td>
								<td>{{$dato->empresa}}</td>
								<td>{!!beauty_fecha($dato->fec_audit)!!}</td>
								<td>{{$dato->mca_enviar}}</td>
								<td>{{$dato->name}}</td>
								<td>{{$dato->des_feria}}</td>
                                <td style="position: relative; vertical-align: middle" class="pt-2">
                                    {{ $dato->mensaje}}
                                    <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
										<div class="btn-group btn-group pull-right ml-1" role="group">
											@if (checkPermissions(['Ferias marcas'],["W"]))<a href="#" title="Ver incidencia " data-id="{{ $dato->id_contacto }}" class="btn btn-xs btn-info add-tooltip btn_edit" onclick="edit({{ $dato->id_contacto }})"><span class="fa fa-eye pt-1" aria-hidden="true"></span> Ver</a>@endif
											@if (checkPermissions(['Ferias marcas'],["D"]))<a href="#eliminar-incidencia-{{$dato->id_contacto}}" title="Borrar incidencia" data-toggle="modal" onclick="del({{ $dato->id_contacto }})" class="btn btn-xs btn-danger add-tooltip btn_borrar "><span class="fa fa-trash pt-1" aria-hidden="true"></span> Del</a>@endif
											{{--  @if (checkPermissions(['Clientes'],["D"]))<a href="#eliminar-Cliente-{{$inc->id_incidencia}}" data-toggle="modal" class="btn btn-xs btn-danger">¡Borrado completo!</a>@endif  --}}
										</div>
                                    </div>
                                    @if (checkPermissions(['Incidencias'],["D"]))
                                        <div class="modal fade" id="eliminar-incidencia-{{$dato->id_contacto}}">
                                            <div class="modal-dialog modal-md">
                                                <div class="modal-content">
													<div class="modal-header">
														<div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
														<h1 class="modal-title text-nowrap">Borrar contacto </h1>
														<button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
															<span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
														</button>
													</div>
													<div class="modal-body">
                                                        ¿Borrar contacto de {{ $dato->nombre}}?
                                                    </div>
                                                    
                                                    <div class="modal-footer">
                                                        <a class="btn btn-info" href="{{url('/ferias/asistentes/delete',$dato->id_contacto)}}">Si</a>
                                                        <button type="button" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">Cancelar</button>
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
</form>
@endsection

@section('scripts')
	
	
	<script>

	@if(isset($r->cliente))
		$('#multi-cliente').val({!!js_array($r->cliente)!!});
	@endif

	@if(isset($r->tipoferia))
		$('#multi-tipoferia').val({!! js_array($r->tipoferia)!!});
	@endif

	var lista_ficheros=new Array(0);

	$('.ferias').addClass('active active-sub');
	$('.ferias_asistentes').addClass('active');

	$('#btn-toggle').click(function(){
         $('#tabla').bootstrapTable('toggleView')
    })

	function edit(id){
		$('#editorCAM').load("{{ url('/ferias/asistentes/edit/') }}"+"/"+id, function(){
			animateCSS('#editorCAM','bounceInRight');
		});
	}

	function del(id){
		$('#eliminar-incidencia-'+id).modal('show');
	}

    $('#btn_nueva').click(function(){
        $('#editorCAM').load("{{ url('/ferias/asistentes/edit/0') }}", function(){
			animateCSS('#editorCAM','bounceInRight');
		});
    })

    $('.td').click(function(event){
        edit($(this).data('id'));
	})


	$('.btn_qr').click(function(){
        var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); 
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún elemento');
            exit();
        }
		
		//block_espere();
        $('#frm').attr('action',"{{url('/ferias/asistentes/print_qr')}}");
        $('#frm').submit();
    });

    $('.btn_export_qr').click(function(){
		var searchIDs = $('.chkpuesto:checkbox:checked').map(function(){
        return $(this).val();
        }).get(); 
        if(searchIDs.length==0){
            toast_error('Error','Debe seleccionar algún elemento');
            exit();
        }
        $('#frm').attr('action',"{{url('/ferias/asistentes/export_qr')}}");
        $('#frm').submit();
    });
	
	$("#chktodos").click(function(){
		$('.chkpuesto').not(this).prop('checked', this.checked);
	});
	


	</script>

@endsection
<div class="card editor mb-5">
	<div class="card-header toolbar">
		<div class="toolbar-start">
			<h5 class="m-0">Modificar departamento</h5>
		</div>
		<div class="toolbar-end">
			<button type="button" class="btn-close btn-close-card">
				<span class="visually-hidden">Close the card</span>
			</button>
		</div>
	</div>
	<div class="card-body">
			@isset ($d)
				<form action="{{url('departments/update')}}" method="POST" class="form-ajax">
				<input type="hidden" name="id" value="{{$d->cod_departamento}}">
				@php
					$cliente=$d->id_cliente;
					$dep=$d->cod_departamento;
				@endphp
			@else
				<form action="{{url('departments/save')}}" method="POST" class="form-ajax">
					@php
						$dep=0;
						$cliente=Auth::user()->id_cliente;
					@endphp
			@endisset
				{{csrf_field()}}
				<div class="row">
					<div class="col-sm-12">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label for="">{{trans('strings._departments.name')}}</label>
									<input type="text" name="nom_departamento" class="form-control" maxlength="200" required value="{{isset($d) ? $d->nom_departamento : ''}}">
								</div>
							</div>
							<div class="col-sm-6">
								<label for="">{{trans('strings._centers.business')}}</label><br>
								<select name="cod_cliente" class="form-control select2" id="cod_cliente">
									{{-- <option value="" selected></option> --}}
									@foreach (\DB::table('clientes')->where(function($q){
										if (!fullAccess()){
											$q->WhereIn('id_cliente',clientes());
										}
										})->get() as $cl)
										<option {{ (isset($d) && $d->id_cliente == $cl->id_cliente) || (!isset($d) && $cl->id_cliente==session('CL')['id_cliente']) ? 'selected' : ''}} value="{{$cl->id_cliente}}">{{$cl->nom_cliente}}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="row mt-2">
							<div class="col-sm-6">
								<div class="form-group">
									<label>{{trans('strings._departments.parent')}}</label>
										<select   name="cod_departamento_padre" class="select2 tab_general" style="width: 100%" id="cod_departamento_padre">
											<option value="0"> </option>
											@php $departamentos=lista_departamentos("cliente",$cliente); @endphp
											@isset($departamentos)
												@foreach($departamentos as $departamento)
													@if(isset($d) && $departamento->cod_departamento!=$d->cod_departamento) {{-- para que un departamento no pueda ser padre de si mismo --}}
														<option style="padding-left: 20px" {{  (isset($d->cod_departamento_padre) && $d->cod_departamento_padre == $departamento->cod_departamento) ? 'selected' : '' }} value="{{ $departamento->cod_departamento}}">
															@for($i = 1; $i <= $departamento->num_nivel; $i++) &nbsp;&nbsp;&nbsp; @endfor{{ $departamento->nom_departamento}}
														</option>
													@endif
												@endforeach
											@endisset
										</select>

									<br>
								</div>
							</div>
						</div>


						<div class="row">
							<div class="col-sm-12">
								<label for=""></label>
							</div>
						</div>

					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-end">
						@if(checkPermissions(['Departamentos'],["W"])) <button type="submit" class="btn btn-primary">{{trans('strings.submit')}}</button>@endif
					</div>
				</div>

				
			</form>
		</div>
	</div>
</div>


<script>

	$('.form-ajax').submit(form_ajax_submit);

	$('.btn_nueva').click(function(){
		event.preventDefault();
		$('.form_nueva_regla').show()
		animateCSS('.form_nueva_regla','slideInRight');
	});

	$(".select2").select2({
        placeholder: "Seleccione",
        allowClear: true,
        width: "99.2%",
    });

	document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

	function normalizeDate(date)
	{
		let d = date.split('-');
		let aux = d[0];
		d[0] = d[2];
		d[2] = aux;
		return d.join('-');
	}

	$(function(){
		$('#cod_cliente').change(function(){
			$('#spinner').show();
			$.get('{{url('/combos/ReloadDepartamentoPadre')}}/'+$('#cod_cliente').val()+'/{{ $d->cod_departamento_padre??0 }}/{{ $d->cod_departamento??0 }}', function(data, textStatus, xhr) {
				//console.log(data);
				$('#cod_departamento_padre').html('');
				$('#cod_departamento_padre').html(data);
				$('[name="cod_departamento_padre"]').click(function(event) {
				// $('#cambio-departamento').text($(this).data('dep'));
				});
				$('#spinner').hide();
			});
		});

		$('#cod_cliente').trigger('change');
	});



</script>


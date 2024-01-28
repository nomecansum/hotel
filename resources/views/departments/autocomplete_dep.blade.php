
@php
	$departamentos=lista_departamentos("cliente",$cliente);
	//dump($departamentos);
@endphp

<option value="0"></option>
@isset($departamentos)
	@foreach($departamentos as $departamento)
		@if($departamento->cod_departamento!=$id) {{-- para que un departamento no pueda ser padre de si mismo --}}
			<option style="padding-left: 20px" {{  (isset($padre) && $padre == $departamento->cod_departamento) ? 'selected' : '' }} value="{{ $departamento->cod_departamento}}">
				@for($i = 1; $i <= $departamento->num_nivel; $i++) &nbsp;&nbsp;&nbsp; @endfor{{ $departamento->nom_departamento}}
			</option>
		@endif
	@endforeach
@endisset
  
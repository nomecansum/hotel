<div class="card">
    <div class="card-header">
        <h3 class="card-title" id="titulo">
            Nueva solicitud
            @isset($puesto->val_icono)
                <i class="{{ $puesto->val_icono }} fa-2x" style="color:{{ $puesto->val_color }}"></i>
            @endisset
           <span class="font-bold" style="color:{{ $puesto->val_color }}; font-size: 20px">{{ $puesto->cod_puesto }}</span>

        </h3>
        <span class="float-right" id="spinner" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ url('/incidencia/save_clientes') }}" id="incidencia_form" name="incidencia_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
        {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_puesto" value="{{ $puesto->id_puesto }}">
                <input type="hidden" name="referer" id="referer" value="{{ url('/form',$puesto->token,$mireserva->pin) }}">
                <input type="hidden" name="adjuntos[]" id="adjuntos" value="">
                <input type="hidden" name="procedencia" value="web"></input>
                <input type="hidden" name="tipo" value="solicitud"></input>
                <input type="hidden" name="origen" value="S"></input>
                <input type="hidden" name="pin" value="{{ $mireserva->pin }}"></input>
                @if(isset($config->val_layout_incidencias) && ($config->val_layout_incidencias=='T' || $config->val_layout_incidencias=='A'))
                    <div class="form-group col-md-8 {{ $errors->has('des_incidencia') ? 'has-error' : '' }}">
                        <label for="des_incidencia" class="control-label">Titulo</label>
                        <input class="form-control"  name="des_incidencia" type="text" id="des_incidencia"  maxlength="200" >
                        {!! $errors->first('des_incidencia', '<p class="help-block">:message</p>') !!}
                    </div>
                @endif
                
                <div class="form-group col-md-4 {{ $errors->has('id_tipo_incidencia') ? 'has-error' : '' }}">
                    <label for="id_tipo_incidencia" class="control-label">Tipo</label>
                    <select class="form-control" required id="id_tipo_incidencia" name="id_tipo_incidencia">
                        @foreach ($tipos as $tipo)
                            <option value="{{ $tipo->id_tipo_incidencia }}">{{ $tipo->des_tipo_incidencia }}</option>
                        @endforeach
                    </select>
                    
                </div>   
                {{-- Si es una solicitud, pondremos el campo de proyecto y el de presupuesto --}}
                @if($puesto->id_puesto==0)
                <div class="form-group col-md-2 {{ $errors->has('id_tipo_incidencia') ? 'has-error' : '' }}">
                    <label for="val_presupuesto" class="control-label">Presupuesto</label>
                    <input class="form-control"  name="val_presupuesto" type="text" id="val_presupuesto"  maxlength="200" >
                </div>
                <div class="form-group col-md-2 {{ $errors->has('id_tipo_incidencia') ? 'has-error' : '' }}">
                    <label for="val_proyecto" class="control-label">Proyecto</label>
                    <input class="form-control"  name="val_proyecto" type="text" id="val_proyecto"  maxlength="200" >
                </div>
                @endif
                
            </div>
            @if((isset($config->val_layout_incidencias) && ($config->val_layout_incidencias=='D' || $config->val_layout_incidencias=='A')) || (!isset($config->val_layout_incidencias)))
            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('txt_incidencia') ? 'has-error' : '' }}">
                    <label for="txt_incidencia" class="control-label">Descripcion</label>
                    <textarea class="form-control" name="txt_incidencia" type="text" id="txt_incidencia" value="" rows="4"></textarea>
                    {!! $errors->first('txt_incidencia', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            @endif

            <div class="form-group mt-3">
                <div class="col-md-12 text-center">
                    <input class="btn btn-lg btn-primary" id="btn_guardar" type="button" value="Guardar">
                </div>
            </div>
        </form>

    </div>
</div>
<script>
    function iformat(icon) {
        var originalOption = icon.element;
        return $('<span><i class="mdi ' + $(originalOption).data('icon') + '"></i> ' + icon.text + '</span>');
    }

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );

    window.Laravel = {!! json_encode([
        'csrfToken' => csrf_token(),
    ]) !!};
			
    $('#btn_guardar').click(function(){
        $('#spinner').show();
        @if(config('app.env')!="local") $('#btn_guardar').hide(); @endif
        $('#incidencia_form').submit();
    });
    $('.form-ajax').submit(form_ajax_submit);
    
</script>

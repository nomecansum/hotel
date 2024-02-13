

    <form  name="form_proceso{{ $tipo->id_proceso }}" method="POST" class="form-ajax mb-5" id="form_proceso{{ $tipo->id_proceso }}" data-id="{{ $tipo->id_proceso }}"> 
        @csrf
        <input type="hidden" name="id" value="{{ $tipo->id_proceso }}"> 
        <input type="hidden" name="val_momento" value="{{ $momento }}"> 
        <div class="row">
            <div class="form-group col-md-2  ">
                <label for="tip_metodo" class="control-label"><span class="text-muted " style="font-size:26px; font-weight: bold">{{ $index??'-' }}</span>@if(config('app.env')=='local')[{{ $tipo->id_proceso }}]@endif</label>
                <select required class="form-control tip_metodo tocado" name="tip_metodo" data-id="{{ $tipo->id_proceso }}">
                        {{-- <option value="S" {{ $tipo->tip_metodo== 'S' ? 'selected' : '' }} >SMS</option> --}}
                        <option value="M" {{ $tipo->tip_metodo== 'M' ? 'selected' : '' }} >e-mail</option>
                        <option value="W" {{ $tipo->tip_metodo== 'W' ? 'selected' : '' }} >Web Push</option>
                        <option value="P" {{ $tipo->tip_metodo== 'P' ? 'selected' : '' }} >HTTP POST</option>
                        <option value="U" {{ $tipo->tip_metodo== 'U' ? 'selected' : '' }} >HTTP PUT</option>
                        <option value="G" {{ $tipo->tip_metodo== 'G' ? 'selected' : '' }} >Http GET</option>
                        <option value="L" {{ $tipo->tip_metodo== 'L' ? 'selected' : '' }} >Gestionar en spotlinker</option>
                        <option value="N" {{ $tipo->tip_metodo== 'N' ? 'selected' : '' }} >Solo registrar</option>
                </select>
                {!! $errors->first('tip_metodo', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="col-md-8">
                @if($momento=='E')
                {{-- Cuando haya un cambio de estado, se debe mostrar el combo de los estados que se pueden seleccionar. --}}
                    <div class="row">
                        <div class="form-group col-md-12">
                            <label for="id_estado" class="control-label" style="text-align: left">Cuando cambie al estado:</label>
                            <select class="form-control" required id="id_estado" name="id_estado">
                                <option value="-1" {{ $tipo->id_estado==-1?'selected':'' }}> Cualquiera</option>  
                            @foreach ($estados as $estado)
                                <option value="{{ $estado->id_estado }}" {{ $estado->id_estado==$tipo->id_estado?'selected':'' }}>
                                    {{ $estado->des_estado }}
                                </option>
                            @endforeach
                            </select>
                        </div>
                    </div>
                @endif
                @switch($tipo->tip_metodo)
                    @case("M")
                            <div class="form-group col-md-12 {{ $errors->has('txt_destinos') ? 'has-error' : '' }}">
                                <label for="txt_destinos" class="control-label">Destinos <span style="font-size: 9px">(separados por ; )</span></label>
                                <textarea class="form-control tocado" name="txt_destinos" type="text" id="txt_destinos" value="" maxlength="65535" placeholder="Enter Destinos here..." rows="4">{{ old('txt_destinos', optional($tipo)->txt_destinos) }}</textarea>
                                {!! $errors->first('val_txt_destinosurl', '<p class="help-block">:message</p>') !!}
                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input name="mca_abriente"  id="mca_abriente{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_abriente)&&$tipo->mca_abriente=='S'?'checked':'' }} class="form-check-input tocado" type="checkbox">
                                            <label class="form-check-label"  for="mca_abriente{{$tipo->id_proceso}}">Notificar al usario creador</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input name="mca_implicados"  id="mca_implicados{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_implicados)&&$tipo->mca_implicados=='S'?'checked':'' }} class="form-check-input tocado" type="checkbox">
                                            <label class="form-check-label"  for="mca_implicados{{$tipo->id_proceso}}">Notificar a todos los implicados</label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input name="mca_responsable"  id="mca_responsable{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_responsable)&&$tipo->mca_responsable=='S'?'checked':'' }} class="form-check-input tocado" type="checkbox">
                                            <label class="form-check-label"  for="mca_responsable{{$tipo->id_proceso}}">Notificar al responsable del creador</label>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        @break
                    @case("P")
                            @include('incidencias.tipos.formulario_post_put')
                        @break
                    @case("U")
                        @include('incidencias.tipos.formulario_post_put')
                    @break
                    @case("G")
                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('val_url') ? 'has-error' : '' }}">
                                <label for="des_edificio" class="control-label">URL</label>
                                <input class="form-control tocado" name="val_url" type="text" id="val_url" value="{{ old('val_url', optional($tipo)->val_url) }}" maxlength="200" placeholder="Enter URL here...">
                                {!! $errors->first('val_url', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="form-group col-md-12 {{ $errors->has('param_url') ? 'has-error' : '' }}">
                                <div class="row">
                                    <div class="col-md-3"><label for="des_edificio" class="control-label w-100 mt-2">Parametros URL</label></div>
                                    <div class="col-md-9 text-end mt-2"><a href="#modal-param_url" data-toggle="modal" data-target="#modal-param_url" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda parametros URL"></i></a></div>
                                </div>
                                <input class="form-control tocado" name="param_url" type="text" id="param_url" value="{{ old('param_url', optional($tipo)->param_url) }}" maxlength="1000" placeholder="Enter Param URL here...">
                                {!! $errors->first('param_url', '<p class="help-block">:message</p>') !!}
                            </div>
                            <div class="form-group col-md-12 {{ $errors->has('val_body') ? 'has-error' : '' }}">
                                <div class="row">
                                    <div class="col-md-3"><label for="des_edificio" class="control-label w-100 mt-2">Header (JSON)</label></div>
                                    <div class="col-md-9 text-end mt-2"><a href="#modal-param_header" data-toggle="modal" data-target="#modal-param_header" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda Header"></i></a></div>
                                </div>
                                <textarea class="form-control tocado" name="val_header" type="text" id="val_header" value="" maxlength="65535" placeholder="Enter URL here..." rows="8">{{ old('val_header', optional($tipo)->val_header) }}</textarea>
                                {!! $errors->first('val_header', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        @break
                        @case("W")
                        <div class="row">
                            <div class="form-group col-md-12 {{ $errors->has('usuarios') ? 'has-error' : '' }} mt-3">
                                <label for="des_edificio" class="control-label">Usuarios</label><br>
                                <select class="select2 mb-2 col-md-11 select2-multiple form-control tocado" style="width: 100%" multiple="multiple" name="txt_destinos[]" id="multi-usuarios" required}>
                                    @foreach(DB::table('users')->where('id_cliente',Auth::user()->id_cliente)->get() as $item)
                                        <option value="{{$item->id}}" {{ isset($tipo->txt_destinos) && is_array (explode(",",$tipo->txt_destinos)) && in_array($item->id,explode(",",$tipo->txt_destinos))===true?'selected':'' }}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-12 {{ $errors->has('val_body') ? 'has-error' : '' }}">
                                <div class="row">
                                    <div class="col-md-3"><label for="des_edificio" class="control-label w-100 mt-2">Mensaje</label></div>
                                    <div class="col-md-9 text-end mt-2"><a href="#modal-param_body"  data-toggle="modal" data-target="#modal-param_body" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda Body"></i></a></div>
                                </div>
                                <input class="form-control tocado" name="val_body" type="text" id="val_body" required value="{{ old('val_body', optional($tipo)->val_body) }}" maxlength="1000" placeholder="Enter val_body here...">
                                {!! $errors->first('val_body', '<p class="help-block">:message</p>') !!}
                            </div>


                        </div>
                        @break
                    @default
                @endswitch
    
            </div>
            <div class="col-md-1 nowrap mt-4">
                <a href="javascript:void(0)"  class="btn btn-xs btn-info btn_save add-tooltip w-100"  id="btn_editar{{ $tipo->id_proceso }}" title="Guardar" data-id="{{ $tipo->id_proceso }}" style="display: none"> <span class="fa fa-save" aria-hidden="true" ></span> Guardar</a>
                <a href="javascript:void(0)"  class="btn btn-xs btn-danger add-tooltip btn_borrar_accion w-100" data-id="{{$tipo->id_proceso}}" title="Borrar tipo"  ><span class="fa fa-trash" aria-hidden="true"></span> Borrar</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="row bg-light">
                    <div class="col-md-2 mt-2">
                        <b>Procedencia<br> (aplica en)</b>
                    </div>
                    <div class="col-md-2 mt-1">
                        <div class="form-check pt-2">
                            <input name="mca_web"  id="mca_web{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_web)&&$tipo->mca_web=='S'?'checked':'' }} class="form-check-input tocado" type="checkbox">
                            <label class="form-check-label"  for="mca_web{{$tipo->id_proceso}}">WEB</label>
                        </div>
                    </div>
                    <div class="col-md-2 mt-1">
                        <div class="form-check pt-2">
                            <input  name="mca_api"  id="mca_api{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_api)&&$tipo->mca_api=='S'?'checked':'' }} class="form-check-input tocado" type="checkbox">
                            <label class="form-check-label"  for="mca_api{{$tipo->id_proceso}}">API</label>
                        </div>
                    </div>
                    <div class="col-md-2 mt-1">
                        <div class="form-check pt-2">
                            <input  name="mca_scan"  id="mca_scan{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_scan)&&$tipo->mca_scan=='S'?'checked':'' }} class="form-check-input tocado" type="checkbox">
                            <label class="form-check-label"  for="mca_scan{{$tipo->id_proceso}}">SCAN</label>
                        </div>
                    </div>
                    <div class="col-md-2 mt-1">
                        <div class="form-check pt-2">
                            <input  name="mca_salas"  id="mca_salas{{$tipo->id_proceso}}" value="S" {{ isset($tipo->mca_salas)&&$tipo->mca_salas=='S'?'checked':'' }} class="form-check-input tocado" type="checkbox">
                            <label class="form-check-label" for="mca_salas{{$tipo->id_proceso}}">SALAS</label>
                        </div>                    
                    </div>
                </div>
            </div>
        </div>
    </form>

<script>

    $('.tocado').on('change keyup paste', function() {
        $('#btn_editar'+$(this).closest("form").data('id')).show();
    });

    $('.tip_metodo').change(function(){
        id_accion=$(this).data('id');
        $('#fila'+$(this).data('id')).load("{{ url('/incidencias/tipos/fila_postprocesado') }}/"+$(this).data('id')+"/"+$(this).val()+"/{{ $momento }}",function(){
           $('#btn_editar'+id_accion).show();
            
        }); 
    });
    $(".select2").select2({
        placeholder: "Todos",
        allowClear: true,
        @desktop width: "90%", @elsedesktop width: "75%", @enddesktop 
    });
    $('#btn_editar{{ $tipo->id_proceso }}').click(function(){
        $.post("{{ url('/incidencias/tipos/postprocesado/save') }}", $('#form_proceso'+$(this).data('id')).serializeArray(), function(data, textStatus, xhr) {
			
		}).fail((e)=>{
			toast_error(JSON.parse(e.responseText))
		})
        .done((data)=>{
            console.log(data.mensaje);
			toast_ok("Postprocesado de incidencia",$( '#val_momento option:selected' ).text()+': '+data.mensaje);
            $('#btn_editar'+data.id).hide();
		});
    });

    $('textarea').on('change keyup paste', function() {
        prettyPrint($(this));
    });

    $('.btn_modal').click(function(){
        console.log($(this).data('target'));
        $($(this).data('target')).modal('show');
    });

</script>
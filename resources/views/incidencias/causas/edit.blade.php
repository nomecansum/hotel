
    <div class="card editor mb-5">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nueva causa de cierre
                    @else
                        Editar causa de cierre
                    @endif
                </h5>
            </div>
            <div class="toolbar-end">
                <button type="button" class="btn-close btn-close-card">
                    <span class="visually-hidden">Close the card</span>
                </button>
            </div>
        </div>

        <div class="card-body">

            @if ($errors->any())
                <ul class="alert alert-danger">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <form method="POST" action="{{ url('/incidencias/causas/save') }}" id="edit_causas_cierre_form" name="edit_causas_cierre_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="des_causa_cierre" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_causa" type="text" id="des_causa_cierre" value="{{ old('des_causa', optional($causa)->des_causa) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_causa_cierre', '<p class="help-block">:message</p>') !!}
                        </div>

                        @if(checkPermissions(['Causas de cierre'],['W']) && ($causa->mca_fija!='S' || ($causa->mca_fija=='S' && fullAccess())))
                        <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                            <label for="id_cliente" class="control-label">Cliente</label>
                            <select class="form-control" required id="id_cliente" name="id_cliente">
                                @foreach ($Clientes as $key => $Cliente)
                                    <option value="{{ $key }}" {{ old('id_cliente', optional($causa)->id_cliente) == $key||$id==0 && $key==session('CL')['id_cliente'] ? 'selected' : '' }}>
                                        {{ $Cliente }}
                                    </option>
                                @endforeach
                            </select>
                                
                            {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                        </div>
                        @endif

                        
                </div>
                <div class="row mt-2">
                    <div class="form-group col-md-1" >
                        <label for="val_color">Color</label><br>
                        <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($causa->val_color)?$causa->val_color:App\Classes\RandomColor::one(['luminosity' => 'bright'])}}" />
                    </div>
                    <div class="form-group col-md-1" style="margin-left: 10px">
                        <div class="form-group">
                            <label>Icono</label><br>
                            <button type="button"  role="iconpicker" name="val_icono"  id="val_icono" data-iconset="fontawesome5"  data-iconset-version="5.3.1_pro"  class="btn btn-light iconpicker" data-search="true" data-rows="10" @desktop data-cols="20" @elsedesktop data-cols="8" @enddesktop data-search-text="Buscar..."></button>
                        </div>
                    </div>
                    
                    <div class="form-group col-md-2 {{ $errors->has('id_causa_externo') ? 'has-error' : '' }}">
                        <label for="id_causa_externo" class="control-label">ID Externo</label>
                        <input class="form-control" name="id_causa_externo" type="text" id="id_causa_externo" value="{{ old('id_causa_externo', optional($causa)->id_causa_externo) }}" maxlength="200" placeholder="Enter id_causa_externo here...">
                        {!! $errors->first('id_causa_externo', '<p class="help-block">:message</p>') !!}
                    </div>
                    <div class="form-group col-md-2 {{ $errors->has('mca_aplica') ? 'has-error' : '' }}">
                        <label for="mca_aplica" class="control-label">Aplica a</label>
                        <select class="form-control" required id="mca_aplica" name="mca_aplica">
                            <option value="I" {{ old('mca_aplica', optional($causa)->mca_aplica) == 'I' ? 'selected' : '' }}>Incidencias</option>
                            <option value="S" {{ old('mca_aplica', optional($causa)->mca_aplica) == 'S' ? 'selected' : '' }}>Solicitudes</option>
                            <option value="A" {{ old('mca_aplica', optional($causa)->mca_aplica) == 'A' ? 'selected' : '' }}>Ambos</option>
                        </select>
                            
                        {!! $errors->first('mca_aplica', '<p class="help-block">:message</p>') !!}
                    </div>
                    @if(isAdmin())
                    <div class="col-md-2 p-t-30">
                        <div class="form-check">
                            <input  name="mca_fija"  id="mca_fija" value="S" {{ $causa->mca_fija=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                            <label class="form-check-label"  for="mca_fija">Fija</label>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="form-group">
                    <div class="col-md-12 text-end">
                        @if(checkPermissions(['Causas de cierre'],['W']) && ($causa->mca_fija!='S' || ($causa->mca_fija=='S' && fullAccess())))<input class="btn btn-primary" type="submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        $('.form-ajax').submit(form_ajax_submit);
        

        

        $('#val_icono').iconpicker({
            icon:'{{isset($causa) ? ($causa->val_icono) : ''}}'
        });
        document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
    </script>
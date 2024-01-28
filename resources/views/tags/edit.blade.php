
    <div class="card editor mb-5">
        <div class="card-header toolbar">
            <div class="toolbar-start">
                <h5 class="m-0">
                    @if($id==0)
                        Nueva tag
                    @else
                        Editar tag
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

            <form method="POST" action="{{ url('/tags/save') }}" id="edit_causas_cierre_form" name="edit_causas_cierre_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
            {{ csrf_field() }}
                <div class="row">
                        <input type="hidden" name="id" value="{{ $id }}">
                        <div class="form-group col-md-8 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="nom_tag_cierre" class="control-label">Nombre</label>
                            <input class="form-control" required name="nom_tag" type="text" id="nom_tag_cierre" value="{{ old('nom_tag', optional($tag)->nom_tag) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('nom_tag_cierre', '<p class="help-block">:message</p>') !!}
                        </div>


                        <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                            <label for="id_cliente" class="control-label">Cliente</label>
                            <select class="form-control" required id="id_cliente" name="id_cliente">
                                @foreach ($Clientes as $key => $Cliente)
                                    <option value="{{ $key }}" {{ old('id_cliente', optional($tag)->id_cliente) == $key||$id==0 && $key==session('CL')['id_cliente'] ? 'selected' : '' }}>
                                        {{ $Cliente }}
                                    </option>
                                @endforeach
                            </select>
                                
                            {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                        </div>
                </div>
                <div class="form-group">
                    <div class="col-md-12 text-end mt-2">
                        <input class="btn btn-primary" type="submit" value="Guardar">
                    </div>
                </div>
            </form>

        </div>
    </div>

    <script>
        $('.form-ajax').submit(form_ajax_submit);
        document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
    </script>
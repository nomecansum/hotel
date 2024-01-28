<div class="card editor mb-5">
    <div class="card-header toolbar">
        <div class="toolbar-start">
            <h5 class="m-0">Editar contacto</h5>
        </div>
        <div class="toolbar-end">
            <button type="button" class="btn-close btn-close-card">
                <span class="visually-hidden">Close the card</span>
            </button>
        </div>
    </div>

    <div class="card-body">
        <form method="POST" action="{{ url('/ferias/asistentes/save') }}" id="formulario" name="formulario" accept-charset="UTF-8" class="form-horizontal form-ajax"  enctype="multipart/form-data">
        {{ csrf_field() }}
            <div class="row">
                <input type="hidden" name="id_contacto" value="{{ $datos->id_contacto }}">
                <div class="form-group col-md-8 {{ $errors->has('nombre') ? 'has-error' : '' }}">
                    <label for="nombre" class="control-label">Nombre</label>
                    <input class="form-control"  name="nombre" type="text" id="nombre"  maxlength="500" value="{{ $datos->nombre }}">
                    {!! $errors->first('nombre', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-4 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                    <label for="id_cliente" class="control-label">Cliente</label>
                    <select class="form-control" required id="id_cliente" name="id_cliente">
                        @foreach ($clientes as $key => $cliente)
                            <option value="{{ $key }}" {{ old('id_cliente', optional($datos)->id_cliente) == $key ? 'selected' : '' }}>
                                {{ $cliente }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-4 {{ $errors->has('empresa') ? 'has-error' : '' }}">
                    <label for="empresa" class="control-label">Empresa</label>
                    <input class="form-control"  name="empresa" type="text" id="empresa"  maxlength="500" value="{{ $datos->empresa }}">
                    {!! $errors->first('empresa', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-4 {{ $errors->has('email') ? 'has-error' : '' }}">
                    <label for="email" class="control-label">e-mail</label>
                    <input class="form-control"  name="email" type="text" id="email"  maxlength="500" value="{{ $datos->email }}">
                    {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-4 {{ $errors->has('id_feria') ? 'has-error' : '' }}">
                    <label for="id_feria" class="control-label">Feria</label>
                    <select class="form-control" required id="id_feria" name="id_feria">
                        @foreach ($ferias as $feria)
                            <option value="{{ $feria->id_feria }}" {{ old('id_cliente', optional($datos)->id_feria) == $feria->id_feria ? 'selected' : '' }}>
                                {{ $feria->des_feria }}
                            </option>
                        @endforeach
                    </select>
                    {!! $errors->first('id_feria', '<p class="help-block">:message</p>') !!}
                </div>
            </div>

            <div class="row">
                <div class="form-group col-md-1 {{ $errors->has('empresa') ? 'has-error' : '' }}">
                    <label for="mca_acepto" class="control-label">Acepto</label>
                    <input class="form-control"  name="mca_acepto" type="text" id="mca_acepto" disabled  maxlength="1" value="{{ $datos->mca_acepto }}">
                    {!! $errors->first('mca_acepto', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-1 {{ $errors->has('empresa') ? 'has-error' : '' }}">
                    <label for="mca_enviar" class="control-label">Envío</label>
                    <input class="form-control"  name="mca_enviar" type="text" id="mca_enviar" disabled  maxlength="1" value="{{ $datos->mca_enviar }}">
                    {!! $errors->first('mca_enviar', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-2 {{ $errors->has('fec_audit') ? 'has-error' : '' }}">
                    <label for="fec_audit" class="control-label">Fecha alta</label>
                    <input class="form-control"  name="fec_audit" type="text" id="fec_audit" disabled  maxlength="500" value="{{ $datos->fec_audit }}">
                    {!! $errors->first('fec_audit', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group col-md-8 {{ $errors->has('token') ? 'has-error' : '' }}">
                    <label for="token" class="control-label">Token</label>
                    <input class="form-control"  name="token" type="text" id="token" disabled  maxlength="500" value="{{ $datos->token }}">
                    {!! $errors->first('token', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-12 {{ $errors->has('mensaje') ? 'has-error' : '' }}">
                    <label for="mensaje" class="control-label">Mensaje</label>
                    <textarea class="form-control" name="mensaje" type="text" id="mensaje" value="" rows="4">{{ $datos->mensaje }}</textarea>
                    {!! $errors->first('mensaje', '<p class="help-block">:message</p>') !!}
                </div>
            </div>
            

            <div class="form-group mt-3">
                <div class="col-md-12 text-end">
                    <input class="btn btn-lg btn-primary" type="submit" value="Guardar">
                </div>
            </div>
        </form>

    </div>
</div>
<script>
    $('.form-ajax').submit(form_ajax_submit);

    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $(this).next('label').html(fileName);
        //$('.custom-file-label').html(fileName);
    });

    $(".editor_imagen").change(function(){
        readURL(this);
    });

    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#img_preview').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
</script>

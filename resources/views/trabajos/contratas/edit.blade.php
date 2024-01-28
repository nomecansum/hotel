@php
    $operarios_internos=$operarios->wherenotnull('id_usuario');
    $operarios_externos=$operarios->whereNull('id_usuario');
@endphp
<div class="card editor mb-5">
    <div class="card-header toolbar">
        <div class="toolbar-start">
            <h5 class="m-0">
                @if($id==0)
                    Nueva contrata de trabajos
                @else
                    Editar contrata de trabajos
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

        <form method="POST" action="{{ url('/trabajos/contratas/save') }}" id="edit_trabajos_cierre_form" name="edit_trabajos_cierre_form" accept-charset="UTF-8" class="form-horizontal form-ajax">
        {{ csrf_field() }}
            <div class="row">
                    <input type="hidden" name="id" value="{{ $id }}">
                    

                    
            </div>
            <div class="row mt-2 mb-4">
                <div class="col-md-10">
                    <div class="row">
                        <div class="form-group col-md-12 {{ $errors->has('des_tipo_incidencia') ? 'has-error' : '' }}">
                            <label for="des_trabajo_cierre" class="control-label">Nombre</label>
                            <input class="form-control" required name="des_trabajo" type="text" id="des_trabajo_cierre" value="{{ old('des_trabajo', optional($dato)->des_contrata) }}" maxlength="200" placeholder="Enter nombre here...">
                            {!! $errors->first('des_trabajo_cierre', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>
                    <div class="row">
                        <div class="form-group col-md-6 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                            <label for="id_cliente" class="control-label">Cliente</label>
                            <select class="form-control" required id="id_cliente" name="id_cliente">
                                @foreach (lista_clientes() as $cliente)
                                    <option value="{{ $cliente->id_cliente }}" {{ old('id_cliente', optional($dato)->id_cliente) == $cliente->id_cliente||$id==0 && $cliente->id_cliente==session('CL')['id_cliente'] ? 'selected' : '' }}>
                                        {{  $cliente->nom_cliente }}
                                    </option>
                                @endforeach
                            </select>
                                
                            {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group col-md-4 {{ $errors->has('id_externo') ? 'has-error' : '' }}">
                            <label for="id_contrata_externo" class="control-label">ID Externo</label>
                            <input class="form-control" name="id_externo" type="text" id="id_externo" value="{{ old('id_externo', optional($dato)->id_externo) }}" maxlength="100" placeholder="Enter id_externo here...">
                            {!! $errors->first('id_contrata_externo', '<p class="help-block">:message</p>') !!}
                        </div>
                        <div class="form-group col-md-2" >
                            <label for="val_color">Color</label><br>
                            <input type="color" autocomplete="off" name="val_color" id="val_color"  class="form-control" value="{{isset($dato->val_color)?$dato->val_color:''}}" />
                        </div>
                    </div>
                    
                </div>
                <div class="col-md-2 text-center b-all rounded">
                    <div class="row font-bold" style="padding-left: 15px">
                        Logo<br>
                    </div>
                    <div class="col-12">
                        <div class="form-group  {{ $errors->has('img_logo') ? 'has-error' : '' }}">
                            <label for="img_logo" class="preview preview1">
                                <img src="{{ isset($dato) ? Storage::disk(config('app.img_disk'))->url('img/contratas/'.$dato->img_logo) : ''}}" style="margin: auto; display: block; width: 140px; heigth:180px" alt="" id="img_preview" class="img-fluid">
                            </label>
                            <div class="custom-file">
                                <input type="file" accept=".jpg,.png,.gif,.webp.jiff" class="form-control  custom-file-input" name="imagen" id="img_logo" lang="es" value="{{ isset($dato) ? $dato->img_logo : ''}}">
                                <label class="custom-file-label" for="img_logo"></label>
                            </div>
                        </div>
                            {!! $errors->first('img_logo', '<p class="help-block">:message</p>') !!}
                    </div>
                </div>
            </div>
            <h4>Operarios</h4>
            <div class="col-md-4 ">
                <div class="btn-group" role="group" aria-label="Basic radio toggle button group">
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio1" autocomplete="off" checked="">
                    <label class="btn btn-outline-primary btn-xs boton_modo" data-tipo="users" for="btnradio1"><i class="fa-solid fa-user"></i> Usuarios spotlinker ({{ $operarios_internos->count() }})</label>
                    
                    <input type="radio" class="btn-check" name="btnradio" id="btnradio2" autocomplete="off">
                    <label class="btn btn-outline-primary btn-xs boton_modo" data-tipo="generico" for="btnradio2"><i class="fa-duotone fa-people"></i> Genericos ({{ $operarios_externos->count() }})</label>
                </div>
            </div>
            <div class="row b-all rounded" id="lista_usuarios">

            </div>
            <div class="row b-all rounded" id="lista_genericos">

            </div>

            

            <div class="form-group">
                <div class="col-md-12 text-end">
                    @if(checkPermissions(['Trabajos'],['W']))<input class="btn btn-primary" type="submit" value="Guardar">@else <span class="bg-warning">Usted no puede modificar este dato</span>@endif
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    $('.form-ajax').submit(form_ajax_submit);

    $(".select2").select2();

    $('#val_icono').iconpicker({
        icon:'{{isset($dato) ? ($dato->val_icono) : ''}}'
    });

    $('.boton_modo').click(function(){
        var tipo=$(this).data('tipo');
        if(tipo=='users'){
            $('#lista_usuarios').load('{{url('/trabajos/contratas/usuarios_internos')}}/{{$id}}');
            $('#lista_usuarios').show();
            $('#lista_genericos').hide();
        }else{
            $('#lista_genericos').load('{{url('/trabajos/contratas/usuarios_genericos')}}/{{$id}}');
            $('#lista_genericos').show();
            $('#lista_usuarios').hide();
        }
    });
    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
</script>

<div class="row mt-2">
    <div class="form-group col-md-11 {{ $errors->has('des_edificio') ? 'has-error' : '' }}">
        <label for="des_edificio" class="control-label">Nombre</label>
        <input class="form-control" required name="des_edificio" type="text" id="des_edificio" value="{{ old('des_edificio', optional($edificios)->des_edificio) }}" maxlength="200" placeholder="Enter des edificio here...">
        {!! $errors->first('des_edificio', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="form-group col-md-1 {{ $errors->has('abreviatura') ? 'has-error' : '' }}">
        <label for="abreviatura" class="control-label">Alias</label>
        <input class="form-control" name="abreviatura" type="text" id="abreviatura" value="{{ old('abreviatura', optional($edificios)->abreviatura) }}" maxlength="200" placeholder="Enter abreviatura here...">
        {!! $errors->first('abreviatura', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<div class="row mt-2">
    @php
        $provincia_poner=old('id_provincia', optional($edificios)->id_provincia);
    @endphp
    <div class="form-group col-md-3 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
        <label for="id_provincia" class="control-label">Ubicación</label>
        <select class="form-control" required id="id_provincia" name="id_provincia">
            @include('resources.combo_provincias_jerarquico')
        </select>
        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="form-group col-md-6{{ $errors->has('id_cliente') ? 'has-error' : '' }}">
        <label for="id_cliente" class="control-label">Cliente</label>
        <select class="form-control" required id="id_cliente" name="id_cliente">
                <option value="" style="display: none;" {{ old('id_cliente', optional($edificios)->id_cliente ?: '') == '' ? 'selected' : '' }} disabled selected>Enter id cliente here...</option>
            @foreach ($Clientes as $key => $Cliente)
                <option value="{{ $key }}" {{ old('id_cliente', optional($edificios)->id_cliente) == $key ? 'selected' : '' }}>
                    {{ $Cliente }}
                </option>
            @endforeach
        </select>
            
        {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
    </div>
</div>
<script>
    
</script>
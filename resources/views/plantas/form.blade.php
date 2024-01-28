
<div class="row">
    <div class="form-group col-md-10 {{ $errors->has('des_planta') ? 'has-error' : '' }}">
        <label for="des_planta" class="control-label">Nombre</label>
            <input class="form-control" required name="des_planta" type="text" id="des_planta" value="{{ old('des_planta', optional($plantas)->des_planta) }}" maxlength="50" placeholder="Enter des planta here...">
            {!! $errors->first('des_planta', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="form-group col-md-1 {{ $errors->has('abreviatura') ? 'has-error' : '' }}">
        <label for="abreviatura" class="control-label">Alias</label>
            <input class="form-control" name="abreviatura" type="text" id="abreviatura" value="{{ old('abreviatura', optional($plantas)->abreviatura) }}" maxlength="50" placeholder="Enter abreviatura here...">
            {!! $errors->first('abreviatura', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="form-group col-md-1">
        <label for="des_planta" class="control-label">Orden</label>
        <select class="form-control" required id="num_orden" name="num_orden">
            @for ($n=1; $n<26;$n++ )
                <option value="{{ $n }}" {{ old('num_orden', optional($plantas)->num_orden) == $n ? 'selected' : '' }}>
                    {{ $n }}
                </option>
            @endfor
        </select>
    </div>
</div>

<div class="row">
    <div class="form-group col-md-6 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-md-12">
                <label for="id_cliente" class="control-label">Cliente</label>
                <select class="form-control" required id="id_cliente" name="id_cliente">
                    @foreach ($Clientes as $key => $Cliente)
                        <option value="{{ $key }}" {{ old('id_cliente', optional($plantas)->id_cliente) == $key ? 'selected' : '' }}>
                            {{ $Cliente }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
            </div>
            
        </div>
        <div class="row">
            <div class="col-md-12">
                <label for="id_edificio" class="control-label">Edificio</label>
                <select class="form-control" required id="id_edificio" name="id_edificio">
                        <option value="" style="display: none;" {{ old('id_edificio', optional($plantas)->id_edificio ?: '') == '' ? 'selected' : '' }} disabled selected>Enter id edificio here...</option>
                    @foreach ($Edificios as $key => $Edificio)
                        <option value="{{ $key }}" {{ old('id_edificio', optional($plantas)->id_edificio) == $key ? 'selected' : '' }}>
                            {{ $Edificio }}
                        </option>
                    @endforeach
                </select>
                {!! $errors->first('id_edificio', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-12 p-t-20">
                <div class="form-check">
                    <input  name="mca_publica"  id="mca_publica" value="S" {{ old('id_edificio', optional($plantas)->id_edificio ?: '') == '' ? 'checked' : '' }} class="form-check-input" type="checkbox">
                    <label class="form-check-label" for="mca_publica">Planta de acceso libre</label>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6 text-center b-all rounded">
        <div class="col-md-12" >
            <div class="form-group  {{ $errors->has('img_plano') ? 'has-error' : '' }}" style="padding-top: 50px">
                <label for="img_plano" class="preview preview1" style="background-image: url();">
                    <img src="{{ isset($plantas) ? Storage::disk(config('app.img_disk'))->url('img/plantas/'.$plantas->img_plano) : ''}}" style="margin: auto; display: block; width: 156px; heigth:180px" alt="" id="img_preview" class="img-fluid">
                </label>
                <div class="custom-file">
                    <input type="file" accept=".jpg,.png,.gif,.jpeg" class="form-control  custom-file-input" name="img_plano" id="img_plano" lang="es" value="{{ isset($plantas) ? $plantas->img_plano : ''}}">
                    <label class="custom-file-label" for="img_plano"></label>
                </div>
            </div>
                {!! $errors->first('img_plano', '<p class="help-block">:message</p>') !!}
        </div>
        <div class="row font-bold">
            Plano de la planta<br>
        </div>
    </div>
    
</div>
<script type="application/javascript">
    $('input[type="file"]').change(function(e){
        var fileName = e.target.files[0].name;
        $('.custom-file-label').html(fileName);
    });

    $('#id_cliente').change(function(){
        $('#id_edificio').load("{{ url('/combos/edificios') }}/"+$(this).val());
    })
</script>

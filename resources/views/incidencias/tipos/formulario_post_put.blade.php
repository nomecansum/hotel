<div>
    <div class="form-group col-md-12 {{ $errors->has('val_url') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-md-3"><label for="des_edificio" class="control-label">URL</label></div>
            <div class="col-md-9 text-end mt-2"><a href="#modal-url" data-toggle="modal" data-target="#modal-url" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda  URL"></i></a></div>
        </div>
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
    <div class="form-group col-md-12 {{ $errors->has('val_body') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-md-3"><label for="des_edificio" class="control-label w-100 mt-2">Body</label></div>
            <div class="col-md-9 text-end mt-2"><a href="#modal-param_body" data-toggle="modal" data-target="#modal-param_body" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda parametros Body"></i></a></div>
        </div>
        <textarea class="form-control tocado" name="val_body" type="text" id="val_body" value="" maxlength="65535" placeholder="Enter URL here..." rows="8">{{ old('val_body', optional($tipo)->val_body) }}</textarea>
        {!! $errors->first('val_body', '<p class="help-block">:message</p>') !!}
    </div>
    <div class="form-group col-md-12 {{ $errors->has('val_respuesta') ? 'has-error' : '' }}">
        <div class="row">
            <div class="col-md-3"><label for="des_edificio" class="control-label w-100 mt-2">Respuesta</label></div>
            <div class="col-md-9 text-end mt-2"><a href="#modal-param_respuesta" data-toggle="modal" data-target="#modal-param_respuesta" class="btn_modal"><i class="fa-solid fa-square-question fa-2x text-info" title="Ayuda parametros Respuesta"></i></a></div>
        </div>
        <textarea class="form-control tocado" name="val_respuesta" type="text" id="val_respuesta" value="" maxlength="65535" placeholder="Enter URL here..." rows="8">{{ old('val_respuesta', optional($tipo)->val_respuesta) }}</textarea>
        {!! $errors->first('val_respuesta', '<p class="help-block">:message</p>') !!}
    </div>
</div>
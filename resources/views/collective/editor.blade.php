<div class="card mb-5"  id="editor">
    <div class="card-header toolbar">
        <div class="toolbar-start">
            <h5 class="m-0">
                Modificar colectivo  
            </h5>
        </div>
        <div class="toolbar-end">
            <button type="button" class="btn-close btn-close-card">
                <span class="visually-hidden">Close the card</span>
            </button>
        </div>
    </div>
    <div class="card-body collapse show">
        <form  action="{{url('collective/update',$id)}}" method="POST" name="frm_colectivo" id="frm_colectivo" class="form-ajax">
            <div class="row">
                <input type="hidden" name="id" value="{{ $id }}">
                {{csrf_field()}}
                <div class="form-group col-md-5">
                    <label for="">{{trans('strings._employees.festives.name')}}</label>
                    <input required type="text" name="des_colectivo" id="des_colectivo" class="form-control" required value="{{ $c->des_colectivo }}">
                </div>
                <div class="form-group col-md-3 {{ $errors->has('id_cliente') ? 'has-error' : '' }}">
                    <label for="id_cliente" class="control-label">Cliente</label>
                    <select class="form-control" required id="id_cliente" name="id_cliente">
                        @foreach ($clientes as $cliente)
                            <option value="{{ $cliente->id_cliente }}" {{ old('id_cliente', optional($c)->id_cliente) == $cliente->id_cliente ? 'selected' : '' }}>
                                {{ $cliente->nom_cliente }}
                            </option>
                        @endforeach
                    </select>
                        
                    {!! $errors->first('id_cliente', '<p class="help-block">:message</p>') !!}
                </div>
                <div class="form-group">
                    <div class="col-md-3 mt-4">
                        <div class="form-check pt-2">
                            <input  name="mca_noinformes"  id="mca_noinformes" value="S"  {{  $c->mca_noinformes=='S'?'checked':'' }} class="form-check-input" type="checkbox">
                            <label class="form-check-label" for="mca_noinformes">No aparecer en informes</label>
                        </div>
                    </div>
                </div>
                <div class="md-12 text-end" style="margin-top:32px">   
                    @if(checkPermissions(['Colectivos'],["W"]))<button type="submit" class="btn btn-primary">{{trans('strings.submit')}}</button>@endif
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $('.form-ajax').submit(form_ajax_submit);
    document.querySelectorAll( ".btn-close-card" ).forEach( el => el.addEventListener( "click", (e) => el.closest( ".card" ).remove()) );
</script>
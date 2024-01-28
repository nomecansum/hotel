
@foreach($clientes as $key=>$value)
    @php $u=$usuarios->where('id_cliente',$key) @endphp
    <span class="font-bold">{{ $value }}</span>
    @foreach($usuarios as $u)
        <div class="ml-3">
            <div class="form-check pt-2">
                <input name="lista_user[]" data-id="{{ $u->id }}" id="chk{{ $u->id }}" value="{{ $u->id }}" class="form-check-input chkuser" type="checkbox">
                <label f class="form-check-label text-start" for="chk{{ $u->id }}">{{ $u->name }}</label>
            </div>
        </div>

    @endforeach
@endforeach
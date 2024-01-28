<option value=""></option>
@foreach($datos as $d)
    <option value="{{ $d->id}}">{{ $d->nombre }}</option>
@endforeach
@foreach($edificios as $ed)
    <option value="{{ $ed->id_edificio}}">{{ $ed->des_edificio }}</option>
@endforeach
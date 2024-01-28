@foreach($plantas as $pl)
    <option value="{{ $pl->id_planta}}">{{ $pl->des_planta }}</option>
@endforeach
@php
    $paises=$provincias->pluck('id_pais','nom_pais')->unique()->sortby('nom_pais desc')->ToArray();

@endphp
<option value="" ></option>
@foreach ($paises as $keyp => $valuep)
    <optgroup label="{{ $keyp }}" value="{{ $valuep }}" data-icon="flag-icon flag-icon-{{ strtolower(App\Models\paises::find($valuep)->cod_iso_pais) }}" data-content="<i class='flag-icon flag-icon-{{ strtolower(App\Models\paises::find($valuep)->cod_iso_pais) }}'></i> {{ $valuep }}">
    @php
        $regiones=$provincias->where('id_pais',$valuep)->pluck('cod_region','nom_region')->sortby('nom_region')->unique()->ToArray();
        dump($regiones);
    @endphp
    @foreach ($regiones as $keyr => $valuer)
        <optgroup label="&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $keyr }}">
        @php
            $pr=$provincias->where('id_pais',$valuep)->where('cod_region',$valuer)->pluck('id_prov','nombre')->sortby('nombre')->unique()->ToArray();
        @endphp
            @foreach ($pr as $key => $value)
                <option value="{{ $value }}" {{ isset($provincia_poner)&&$provincia_poner==$value?'selected':'' }}>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $key }}
                </option>
            @endforeach
        </optgroup>
    @endforeach
    </optgroup>
@endforeach
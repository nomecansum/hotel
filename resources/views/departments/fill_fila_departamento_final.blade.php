{{-- <li class="clickable" style="font-size:16px; font-weight:bold">
    <label>
        <i class="mdi mdi-minus-box"></i>
        <i class="mdi mdi-sitemap icon-box" style="color:darkgreen"></i>
        <input type="checkbox" class="chkdep @foreach(departamentos_padres($dep->cod_departamento,'simple') as $dp) dpto{{ $dp }} @endforeach cen{{ $dep->cod_centro }}" data-name="cod_departamento[]" data-departamento="{{$dep->cod_departamento}}" data-centro="{{$dep->cod_centro}}" value="{{$dep->cod_departamento}}"> {{$dep->nom_departamento}}
    </label>
    @include('employees.fill_empleados_departamento_estructura')
</li> --}}

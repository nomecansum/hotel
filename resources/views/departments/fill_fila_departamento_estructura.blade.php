<li class="clickable" style="font-size:16px; ">
    <label style="font-size: 20px;">
        <i class="mdi mdi-minus-box"></i>
        <i class="mdi mdi-sitemap icon-box" style="color:darkgreen"></i>
         {{$dep->nom_departamento}}
    </label>
    {{-- {{ $dep->cod_departamento }} - {{ $dep->id_edificio }} --}}
    @include('departments.fill_empleados_departamento_estructura')
</li>
@php
    $hay_hijos=Collect(departamentos_centro_hijos($dep->cod_departamento,$dep->id_edificio??0,2,'collect'));
@endphp
@if(!$hay_hijos->isempty())
    <ul>@each('departments.fill_fila_departamento_estructura', $hay_hijos, 'dep','departments.fill_fila_departamento_final')</ul>
@endif

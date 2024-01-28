<ul class="sortable">
    @php
        $usuarios=DB::table('users')->where('id_departamento',$dep->cod_departamento)
        ->select('users.id','users.name','users.id_departamento','users.img_usuario as img_empleado','users.id_edificio','users.id_usuario_supervisor','users.cod_nivel','users.img_usuario')
        ->where('users.id_edificio',$dep->id_edificio??0)
        ->wherenull('deleted_at')
        ->orderby('cod_nivel','desc')
        ->orderBy('name','asc')
        ->get();
    @endphp
    {{-- {{ $dep->cod_departamento }} - {{ $dep->id_edificio }} --}}
    @foreach ($usuarios as $_emp)
        <li data-emp="{{$_emp->id}}" style="font-size:14px" @if($_emp->cod_nivel=1) class="ml-3" @endif>
            <label style="text-transform: capitalize; font-weight: normal">
                {{-- <i class="mdi mdi-account icon-box"  style="color:darkorange"></i> --}}
                @if (isset($_emp->img_usuario ) && $_emp->img_usuario!='')<img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$_emp->img_usuario) }}" class="img-md rounded-circle" style="height:30px; width:30px; object-fit: cover;">@else {!! icono_nombre($_emp->name,30,13) !!} @endif
                 {{strtolower($_emp->name)}} 
            </label>
        </li>
    @endforeach
</ul>
{{-- {{ checkSupervisor2($_emp->id)?'checked':'' }} --}}


@foreach($usersObjects as $users)
    <tr class="hover-this p-0 {{ $users->deleted_at!=null?'bg-gray':'' }}" data-id="{{ $users->id }}" onclick="editar({{ $users->id }})" >
        <td class="text-center">
            <div class="form-check pt-2">
                <input  name="lista_id[]" data-id="{{ $users->id }}" id="chku{{ $users->id }}" value="{{ $users->id }}" class="form-check-input chkuser" type="checkbox">
                <label for="chku{{ $users->id }}" class="form-check-label">{{ $users->id }}</label>
            </div>
        </td>
        <td class="center">
            @if (isset($users->img_usuario ) && $users->img_usuario!='')
                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$users->img_usuario) }}" class="img-md rounded-circle" style="height:40px; width:40px; object-fit: cover;">
            @else
                {!! icono_nombre($users->name,40,14) !!}
            @endif
        </td>
        <td class="pt-3">{{ $users->name }}</td>
        <td>
            @if(isset($users->cod_nivel))
                {{$users->des_nivel_acceso}}
            @else
                <div>
                    <i class="fa fa-warning" style="color:orange">Pendiente</i>
                </div>
            @endif
        </td>

        <td>{!! beauty_fecha($users->last_login,0) !!} {!! $users->deleted_at!=null?'<br> <i class="fa-solid fa-user-slash"></i> Disabled':'' !!}</td>
        <td style="vertical-align: middle; font-size:12px; position: relative">
            {{ $users->email }}
            {{-- <form method="POST" action="{!! route('users.users.destroy', $users->id) !!}" accept-charset="UTF-8">
            <input name="_method" value="DELETE" type="hidden">
            {{ csrf_field() }} --}}
            <div class="pull-right floating-like-gmail mt-3" style="width: 400px;">
                <div class="btn-group btn-group pull-right ml-1" role="group">
                    @if (checkPermissions(['ReLogin'],["R"]) && $users->nivel_acceso<=Auth::user()->nivel_acceso)<a href="{{url('relogin',$users->id)}}" class="btn btn-xs btn-warning"><i class="fa fa-user" ></i> Suplantar</a>@endif
                    <a href="#" onclick="editar({{ $users->id }})" class="btn btn-xs btn-info  add-tooltip" title="Editar Usuario"  style="float: left"><span class="fa fa-pencil pt-1" ></span> Edit</a>
                    <a class="btn btn-xs btn-danger add-tooltip ml-1 btn_eliminar"   data-target="#eliminar-usuario" data-toggle="modal" style="float: left" title="Borrar usuario" onclick="del({{ $users->id }});$('#txt_borrar').html('{{ $users->name }}'); $('#id_usuario_borrar').val({{ $users->id }})" data-name="{{ $users->name }}"   style="float: right">
                        <span class="fa fa-trash"></span> Del
                    </a>
                </div>
            </div>
            {{-- </form> --}}
        </td>
    </tr>
@endforeach

<script>
    $('#cuenta_usuarios').html('{{ $usersObjects->count()}} Usuarios encontrados');
</script>

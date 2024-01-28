<div class="row mt-3 mb-3">
    <div class="col-md-3">
        <div class="form-group {{ $errors->has('id_perfil') ? 'has-error' : '' }}">
            <label for="id_perfil">Perfil</label>
            <select class="select2 notsearch form-control"  id="cod_nivel" name="cod_nivel">
                @foreach ($perfiles as $perfile)
                    @php
                        $cuenta=$usuarios->where('cod_nivel',$perfile->cod_nivel)->count();
                    @endphp 
                    <option value="{{ $perfile->cod_nivel }}" {{ $id_perfil == $perfile->cod_nivel ? 'selected' : '' }}>
                        {{ $perfile->des_nivel_acceso }} @if($cuenta>0)({{ $cuenta }})@endif
                    </option>
                @endforeach
            </select>
            {!! $errors->first('cod_nivel', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div>

@foreach($lista_usuarios as $u)
    @php
        $esta=$usuarios->where('id_usuario',$u->id)->first();
    @endphp
    <div class="col-md-4 mb-2" id="operarioI_{{ $u->id }}">
        <input type="checkbox" class="chk_interno" name="usuint_{{ $u->id }}" data-id="{{ $u->id }}" {{ isset($esta)?'checked':'' }}>
        @if (isset($u->img_usuario ) && $u->img_usuario!='')
            <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$u->img_usuario) }}" alt="{{ $u->name }}" class="img-md rounded-circle" style="height:30px; width:30px; object-fit: cover;">
        @else
            {!! icono_nombre($u->name,30,14,-3) !!}
        @endif
        {!! isset($esta)?'<b>'.$u->name.'</b>':$u->name !!}
    </div>
@endforeach

<script>
    $('#cod_nivel').change(function(){
        $('#lista_usuarios').load('{{url('/trabajos/contratas/usuarios_internos',$id)}}/'+$(this).val());
    })

    $('.chk_interno').click(function(){
        if($(this).is(':checked')){
            accion='AI';
        } else {
            accion='DI';
        }
        console.log(accion);
        $.get('{{url("/trabajos/contratas/set_usuarios_contrata")}}/'+accion+'/{{ $id }}/'+$(this).data('id'), function(data, textStatus, xhr) {
            if(data.error){
                toast_error(data.title,data.error);
            } else if(data.alert){
                toast_warning(data.title,data.alert);
            } else{
                toast_ok(data.title,data.message);
                
            }
        })
    })

</script>
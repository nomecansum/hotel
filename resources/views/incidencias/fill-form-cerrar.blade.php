<input type="hidden" name="id_incidencia" value="{{ $id }}"></input>
<input type="hidden" name="procedencia" value="web"></input>
<h5>Causa de cierre</h5>
<ul>
    @foreach($causas_cierre as $causa)
    <div class="radio">
        <i class="{{ $causa->val_icono }}" style="color:{{ $causa->val_color }}; font-weight: bold;"></i>
        <input required id="id_causa_cierre{{ $causa->id_causa_cierre }}" name="id_causa_cierre" class="magic-radio" type="radio" value="{{ $causa->id_causa_cierre }}">
        <label for="id_causa_cierre{{ $causa->id_causa_cierre }}" style="color:{{ $causa->val_color }}; font-weight: 400; font-size: 16px">{{ $causa->des_causa }} </label>
    </div>
    @endforeach
</ul>
<h5>Comentario de cierre</h5>
<textarea class="form-control" name="comentario_cierre" type="text" id="comentario_cierre" value="" rows="4"></textarea>
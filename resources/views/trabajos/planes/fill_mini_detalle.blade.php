<div class="row">
    <div class="form-group col-md-3" id="div_operarios_gen">
        <label for="num_operarios" class="control-label">Numero de operarios</label>
        <input class="form-control" name="num_operarios" type="number" id="num_operarios" value="{{ $num_operarios}}" min=1 max=1000  placeholder="">
    </div>
    <div class="form-group col-md-3" id="div_operarios_esp" {!! (isset($mostrar_operarios) && $mostrar_operarios==1)?'':'style="display: none"' !!}>
        <label for="num_operarios" class="control-label">Numero de operarios</label>
        <input class="form-control" disabled  type="number" id="num_operarios_esp" value="{{ $num_operarios}}" min=1 max=1000  placeholder="">
    </div>
    <div class="form-group col-md-3">
        <label for="val_tiempo" class="control-label">Tiempo total (min)</label>
        <input class="form-control" name="val_tiempo" type="number" id="val_tiempo" value="{{ $val_tiempo }}" min=1 max=10000  placeholder="">
    </div>
</div>
<div class="row  mt-3" id="list_operarios" {!! (isset($mostrar_operarios) && $mostrar_operarios==1)?'':'style="display: none"' !!}>
    @foreach($operarios->where('id_contrata',$detalle->id_contrata??$contratas->first()->id_contrata) as $operario)
        @php
            $esta=in_array($operario->id_operario,explode(',',$detalle->list_operarios??''));
        @endphp
        <div class="col-md-4 mb-2" id="operarioI_{{ $operario->id_operario }}">
            <input type="checkbox" class="chk_operario" name="operarios[]" data-id="{{ $operario->id_operario }}"  value="{{ $operario->id_operario }}" {{ $esta?'checked':'' }}>
            @if (isset($u->img_usuario ) && $u->img_usuario!='')
                <img src="{{ Storage::disk(config('app.img_disk'))->url('img/users/'.$operario->img_usuario) }}" alt="{{ $operario->name }}" class="img-md rounded-circle" style="height:30px; width:30px; object-fit: cover;">
            @else
                {!! icono_nombre($operario->name,30,14,-3) !!}
            @endif
            {!! isset($esta)?'<b>'.$operario->name.'</b>':$operario->name !!}
        </div>
    @endforeach
    <div class="row mt-3"></div>
    @foreach($operarios_genericos->where('id_contrata',$detalle->id_contrata??$contratas->first()->id_contrata) as $operario)
        @php
            $esta=in_array($operario->id_operario,explode(',',$detalle->list_operario??''));
        @endphp
         <div class="col-md-4 mb-2" style="color: {{ $operario->val_color }}; font-weight: 400" id="operario_{{ $operario->id_operario }}">
            <input type="checkbox" class="chk_operario" name="operarios[]" data-id="{{ $operario->id_operario }}" {{ $esta?'checked':'' }} value="{{ $operario->id_operario }}"> <i class="fa-solid fa-person-simple"></i>  {!! isset($esta)?'<b>'.$operario->nom_operario.'</b>':$operario->nom_operario !!}
        </div>
    @endforeach
</div>

<script>
    $('.chk_operario').on('click',function(){
        var id=$(this).data('id');
        cuenta=0;
        $('.chk_operario').each(function(item){
            if($(this).is(':checked')){
                cuenta++;
            }
        })
        $('#num_operarios_esp').val(cuenta);
    });
</script>

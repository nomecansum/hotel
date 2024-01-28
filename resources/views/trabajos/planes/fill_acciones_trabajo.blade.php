

<div class="row mb-3">
    <h4><i class="fa-solid fa-copy"></i> Copiar configuracion a otros trabajos</h4>
    <h5><input type="checkbox" class="chk_plantas"> PLANTAS</h5>
    @foreach($lista_plantas as $planta)
        <div class="col-md-4 mb-2">
            <input type="checkbox" class="chk_planta" name="plantas_copiar[]" data-id="{{ $planta->id_planta }}"  value="{{ $planta->id_planta }}">  {!! $planta->des_planta !!}
        </div>
    @endforeach
    <h5 class="mt-4"><input type="checkbox" class="chk_zonas"> ZONAS</h5>
    @foreach($lista_zonas as $zona)
        <div class="col-md-4 mb-2">
            <input type="checkbox" class="chk_zona" name="zonas_copiar[]" data-id="{{ $zona->id_zona }}"  value="{{ $zona->id_zona }}"> <span class="text-info">[{{ $zona->des_planta }}]</span>  {!! $zona->des_zona !!}
        </div>
    @endforeach
    <h5 class="mt-4"><input type="checkbox" class="chk_trabajos"> OTROS TRABAJOS DEL PLAN</h5>
    @foreach($plan as $p)

        <div class="col-md-6 mb-2">
            <input type="checkbox" class="chk_trab" name="trabajos_copiar[]" data-id="{{ $p->id_trabajo }}" value="{{ $p->id_grupo.'_'.$p->id_trabajo }}"> <span class="text-info">[{{ $p->des_grupo }}]</span>  {!! $p->des_trabajo !!}
        </div>
    @endforeach
</div>

<div class="row mb-3">
    <label class="text-danger font-bold"><i class="fa-solid fa-trash"></i> Borrar configuracion</label>
    <div class="col-md-4" id="boton_borrar_1">
        <a class="btn btn-lg btn-danger btn_borrar_detalle" data-id="{{ $detalle->key_id??0 }}"><i class="fa-solid fa-trash"></i> Borrar</a>
    </div>
    <div class="col-md-8" id="boton_borrar_2" style="display: none">
        <label>¿Seguro que quiere borrar este detalle? Esta accion no puede deshacerse</label>
        <a class="btn btn-lg btn-danger btn_borrar_detalleOK mr-3" data-id="{{ $detalle->key_id??0  }}"><i class="fa-solid fa-trash"></i>Si, Borrar</a>
        <a class="btn btn-lg btn-warning btn_borrar_detalleCANCEL" data-id="{{ $detalle->key_id??0  }}">Cancelar</a>
    </div>
    
</div>

<script>
    $('.btn_borrar_detalle').click(function(){
        $('#boton_borrar_1').hide();
        $('#boton_borrar_2').show();
        animateCSS('#boton_borrar_2', 'bounceInRight');
    })

    $('.btn_borrar_detalleCANCEL').click(function(){
        $('#boton_borrar_1').show();
        $('#boton_borrar_2').hide();
        animateCSS('#boton_borrar_2', 'bounceInRight');
    })

    $('.btn_borrar_detalleOK').click(function(){
        $.get("{{ url('trabajos/planes/delete_detalle') }}/"+$(this).data('id'), function(data){
            if (data.error) {
                toast_error(data.title,data.error);
            } else {
                toast_ok(data.title,data.message);
                $('.modal').modal('hide');
                $('.select2-multiple').trigger('change');
            }
        })
    })

    $('.chk_plantas').click(function(){
        if ($(this).is(':checked')) {
            $('.chk_planta').prop('checked',true);
        } else {
            $('.chk_planta').prop('checked',false);
        }
    })
    $('.chk_zonas').click(function(){
        if ($(this).is(':checked')) {
            $('.chk_zona').prop('checked',true);
        } else {
            $('.chk_zona').prop('checked',false);
        }
    })
    $('.chk_trabajos').click(function(){
        if ($(this).is(':checked')) {
            $('.chk_trab').prop('checked',true);
        } else {
            $('.chk_trab').prop('checked',false);
        }
    })
</script>
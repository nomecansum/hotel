@php
    isset($detalle) && old('id_contrata', optional($detalle)->list_operarios) != null? $mostrar_operarios=1:$mostrar_operarios=0;
@endphp

<form method="POST" action="{{ url('/trabajos/planes/detalle_save') }}" id="edit_plan_detalle" name="edit_plan_detalle" accept-charset="UTF-8" class="form-horizontal form-ajax">
{{ csrf_field() }}

<div class="tab-base">

    <!-- Nav tabs -->
    <ul class="nav nav-callout justify-content-end" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#_dm-recursos" type="button" role="tab" aria-controls="recursos" aria-selected="false">Recursos</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#_dm-programacion" type="button" role="tab" aria-controls="programacion" aria-selected="false">Programacion</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#_dm-acciones" type="button" role="tab" aria-controls="acciones" aria-selected="false">Acciones</button>
        </li>
    </ul>

    <!-- Tabs content -->
    <div class="tab-content">
        <div id="_dm-recursos" class="tab-pane fade active show" role="tabpanel" aria-labelledby="recursos-tab">
            <div class="row">
                <input type="hidden" name="id_detalle" value="{{ $detalle->key_id??0 }}">
                <input type="hidden" name="id_plan" value="{{ $r->id_plan }}">
                <input type="hidden" name="id_grupo" value="{{ $r->grupo }}">
                <input type="hidden" name="tipo" value="{{ $r->tipo }}">
                <input type="hidden" name="id_trabajo" value="{{ $r->trabajo }}">
                <input type="hidden" id="val_periodo" name="val_periodo" value="{{ $r->periodo }}" class="cron-expression">
                @if($r->tipo=='P')
                <input type="hidden" name="id_planta" value="{{ $r->id }}">
                @else
                <input type="hidden" name="id_zona" value="{{ $r->id }}">
                @endif
        
                <div class="col-md-4">
                    <label for="id_contrata" class="control-label">Contrata</label>
                    <select class="form-control" required id="id_contrata" name="id_contrata">
                        @foreach ($contratas as $dato)
                            <option value="{{ $dato->id_contrata }}" {{ old('id_contrata', optional($detalle)->id_contrata) == $dato->id_contrata ? 'selected' : '' }}>
                                {{ $dato->des_contrata }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="sel_operarios" class="control-label">Operarios</label>
                    <select class="form-control" required id="sel_operarios" name="sel_operarios">
                        <option value="0">Genericos</option>
                        <option value="1" {{  $mostrar_operarios==1? 'selected' : '' }}>Detallar</option>
                    </select>
                </div>
                <div class="col-md-2 p-t-20 mt-1">
                    <div class="form-check pt-1">
                        <input name="mca_activa"  id="mca_activa" value="S" {{ (isset($detalle) && $detalle->mca_activa=='S')||(!isset($setalle))?'checked':'' }} class="form-check-input" type="checkbox">
                        <label for="mca_activa" class="form-check-label">Activa</label>
                    </div>
                </div>
                {{-- <div class="col-md-2">
                    <label for="sel_tiempo" class="control-label">Tiempo</label>
                    <select class="form-control" required id="sel_tiempo" name="sel_tiempo">
                        <option value="0">Generico</option>
                        <option value="1" {{ isset($detalle) && old('id_contrata', optional($detalle)->val_tiempo) == null ? 'selected' : '' }}>Calculado</option>
                    </select>
                </div> --}}
            </div>
            <div class="row mt-3">
                <div class="form-group">
                    <label for="observaciones">Observaciones</label>
                    <textarea class="form-control" name="txt_observaciones" id="txt_observaciones" rows="3">{{ old('txt_observaciones', optional($detalle)->txt_observaciones) }}</textarea>
                </div>
            </div>
            <div class="row  mt-3" id="detalle_mini">
                @include('trabajos.planes.fill_mini_detalle')
            </div>
        </div>
        <div id="_dm-programacion" class="tab-pane fade" role="tabpanel" aria-labelledby="programacion-tab">
            @include('trabajos.planes.fill_detalle_periodo')
        </div>
        <div id="_dm-acciones" class="tab-pane fade" role="tabpanel" aria-labelledby="acciones-tab">
            @include('trabajos.planes.fill_acciones_trabajo')
        </div>
        
    </div>
</div>


    
</form>

<script>
    $('#sel_operarios').on('change', function() {
        if (this.value == 1) {
            $('#div_operarios_gen').hide();
            $('#div_operarios_esp').show();
            $('#list_operarios').show();
        } else {
            $('#list_operarios').hide();
            $('#div_operarios_gen').show();
            $('#div_operarios_esp').hide();
        }
    });


    $('#id_contrata').change(function(){
        var id_contrata = $(this).val();
        var url = "{{ url('/trabajos/planes/mini_detalle',[$r->id_plan,$r->grupo,$r->trabajo]) }}/"+id_contrata+"/"+$('#sel_operarios').val()+"/"+$('#sel_tiempo').val();
        $('#detalle_mini').load(url);
    });

    
</script>

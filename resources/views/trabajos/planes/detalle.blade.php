@php
    use Carbon\Carbon;
@endphp

<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th rowspan="2" colspan="2" scope="col" class="text-center">Trabajos</th>
                <th  rowspan="2"  scope="col" >Fechas</th>
                <th  rowspan="2"  scope="col" >Periodicidad</th>
                @if($plantas->count()>0)<th colspan="{{ $plantas->count() }}" class="text-center bg-light"  scope="col" >PLANTAS</th>@endif
                @if($zonas->count()>0)<th colspan="{{ $zonas->count() }}" class="text-center bg-light"  scope="col" >ZONAS</th>@endif
            </tr>
            <tr>
                @foreach($plantas as $planta)
                    <th class="text-center text-nowrap"  scope="col" ><span class="vertical text-nowrap align-middle text-center" nowrap>@local [{{ $planta->id_planta }}]  @endlocal {{$planta->des_planta}}</span></th>
                @endforeach
                @foreach($zonas as $zona)
                    <th class="text-center text-nowrap"  scope="col" ><span class="vertical text-nowrap text-center align-middle" nowrap>@local [{{ $zona->key_id }}]  @endlocal [{{ $zona->des_planta }}] {{$zona->des_zona}}</span></th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($grupos as $grupo)
                @php
                    $trabajos_grupo=$trabajos->where('id_grupo',$grupo->id_grupo);
                @endphp
                @foreach($trabajos_grupo as $trabajo)
                    <tr>
                        @if($loop->index==0)
                            <td rowspan="{{ $trabajos_grupo->count() }}" class="text-center align-middle" style="vertical-align: middle; padding: 10px 0px 10px 0px; background-color: {{ $grupo->val_color }}"><span class="vertical text-center ml-2 {{ txt_blanco($grupo->val_color) }}"> {{ $grupo->des_grupo }}</span></td>
                        @endif
                        <td scope="col" class="text-nowrap {{ txt_blanco($trabajo->val_color) }}" style="background-color:{{ $trabajo->val_color }}"><div style="margin-left: {{ 30*$trabajo->num_nivel }}px;"><i class="{{ $trabajo->val_icono }}"></i>@local [{{ $trabajo->id_trabajo }}]  @endlocal {{ $trabajo->des_trabajo }}</div></td>
                        <td class="text-center text-nowrap"  scope="col" >@if(isset($grupo->fec_inicio))(G) {{ Carbon::parse($grupo->fec_inicio)->format('d/M') }} <i class="fa-solid fa-arrow-right"></i> {{ Carbon::parse($grupo->fec_fin)->format('d/M') }}@endif @if(isset($trabajo->fec_inicio))(T) {{ Carbon::parse($trabajo->fec_inicio)->format('d/M') }} <i class="fa-solid fa-arrow-right"></i> {{ Carbon::parse($trabajo->fec_fin)->format('d/M') }}@endif</td>
                        <td scope="col" class="text-center td_periodo" data-grupo="{{ $grupo->id_grupo }}" data-trabajo="{{ $trabajo->id_trabajo }}" data-desc="{{ $grupo->des_grupo.' - '.$trabajo->des_trabajo }}">
                            @php
                                $periodos=$detalle->where('id_trabajo',$trabajo->id_trabajo)->where('id_grupo_trabajo',$grupo->id_grupo)->pluck('val_periodo')->unique()->toarray();
                            @endphp
                                @if(count($periodos)>1)<i class="fa-solid fa-asterisk add-tooltip" title="Varias periodicidades definidas en distintas zonas"></i>@elseif(count($periodos)==1) <span class="add-tooltip span_cron" data-expresion="{{ $periodos[0] }}" style="color:{{ genColorCodeFromText($periodos[0]) }}">{{ $periodos[0] }}</span> @else <i class="fa-solid fa-question add-tooltip" title="No se ha definido periodicidad para este trabajo en ninguna zona"></i> @endif
                        </td>
                        @foreach($plantas as $planta)
                            @php
                                $item=$detalle->where('id_planta',$planta->id_planta)->where('id_trabajo',$trabajo->id_trabajo)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
                            @endphp
                            <td scope="col" data-key_id= "{{ $item->key_id??0 }}" class="td_planta text-center text-nowrap {{ ($item->mca_activa??'')=='N'?'bg-gray text-muted':'' }}" data-detalle="{{ $item->key_id??0 }}" data-id="{{ $planta->id_planta }}" data-tipo="P" data-grupo="{{ $grupo->id_grupo }}" data-trabajo="{{ $trabajo->id_trabajo }}" data-desc="{{ $planta->des_planta.' - '.$trabajo->des_trabajo }}"  data-periodo="{{ $item->val_periodo??null }}">
                                <img src="{{ isset($item->img_logo) ? Storage::disk(config('app.img_disk'))->url('img/contratas/'.$item->img_logo) : ''}}"  title="{{ $item->des_contrata??'' }}"  style="margin: auto; display: block; width: 20px; heigth:20px" alt=""  class="img-fluid">@local [{{ $item->key_id??'-' }}]  @endlocal
                                @if(isset($item->num_operarios))<i class="fa-solid fa-person-simple add-tooltip " title="{{ $item->num_operarios }} Operarios asignados"></i> {{ $item->num_operarios }}  <br>@endif
                                @if(isset($item->list_operarios)) <i class="fa-solid fa-person-simple add-tooltip "></i> {{ count(explode(",",$item->list_operarios)) }} <br>@endif
                                @if(isset($item->val_tiempo)) <i class="fa-regular fa-stopwatch add-tooltip " title="Duracion total {{ $item->val_tiempo }} minuos"></i> {{ $item->val_tiempo }}' <br>@endif
                                @if(isset($item->val_periodo))<div class="add-tooltip span_cron" data-expresion="{{$item->val_periodo??null }}" style="color: {{ genColorCodeFromText($item->val_periodo??null) }}">{{ $item->val_periodo??null }}</div> @endif
                                
                            </td>
                        @endforeach
                        @foreach($zonas as $zona)
                            @php
                                $item=$detalle->where('id_zona',$zona->key_id)->where('id_trabajo',$trabajo->id_trabajo)->where('id_grupo_trabajo',$grupo->id_grupo)->first();
                            @endphp
                            <td scope="col" data-key_id= "{{ $item->key_id??0 }}"  class="td_planta text-center text-nowrap {{ ($item->mca_activa??'')=='N'?'bg-gray text-muted':'' }}" data-detalle="{{ $item->key_id??0 }}"  data-id="{{ $zona->key_id }}" data-tipo="Z" data-grupo="{{ $grupo->id_grupo }}" data-trabajo="{{ $trabajo->id_trabajo }}"  data-desc="{{ $zona->des_zona .' - '.$trabajo->des_trabajo}}"  data-periodo="{{ $item->val_periodo??null }}">
                                <img src="{{ isset($item->img_logo) ? Storage::disk(config('app.img_disk'))->url('img/contratas/'.$item->img_logo) : ''}}" title="{{ $item->des_contrata??'' }}" style="margin: auto; display: block; width: 20px; heigth:20px" alt=""  class="img-fluid">@local [{{ $item->key_id??'-' }}]  @endlocal
                                @if(isset($item->num_operarios))<i class="fa-solid fa-person-simple add-tooltip "></i> {{ $item->num_operarios }}  <br>@endif
                                @if(isset($item->list_operarios)) <i class="fa-solid fa-person-simple add-tooltip " title="{{ count(explode(",",$item->list_operarios)) }} Operarios asignados"></i> {{ count(explode(",",$item->list_operarios)) }} <br>@endif
                                @if(isset($item->val_tiempo)) <i class="fa-regular fa-stopwatch add-tooltip " title="Duracion total {{ $item->val_tiempo }} minuos"></i> {{ $item->val_tiempo }}' <br>@endif
                                @if(isset($item->val_periodo))<div class="add-tooltip span_cron" data-expresion="{{$item->val_periodo??null }}" style="color: {{ genColorCodeFromText($item->val_periodo??null) }}">{{$item->val_periodo??null }}</div> @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
</div>
 
<div class="modal fade" id="detalle-trabajo" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            
            <div class="modal-header">
                
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h3 class="modal-title text-nowrap">Detalle: <span id="desc_detalle">#</span></h3>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body" id="detalle_modal">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-info" id="btn_si_detalle" href="javascript:void(0)">Si</a>
                <button type="button" id="btn_no_detalle" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="detalle-periodo" style="display: none;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div><img src="/img/Mosaic_brand_20.png" class="float-right"></div>
                <span class="float-right" id="loading" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span><h3 class="modal-title text-nowrap">Periodicidad: <span id="desc_periodo">#</span></h3>
                <button type="button" class="close btn" data-dismiss="modal" onclick="cerrar_modal()" aria-label="Close">
                    <span aria-hidden="true"><i class="fa-solid fa-circle-x fa-2x"></i></span>
                </button>
            </div>
            <div class="modal-body" id="detalle_periodo">
                
            </div>
            <div class="modal-footer">
                <a class="btn btn-info" id="btn_si_periodo" href="javascript:void(0)">Si</a>
                <button type="button" id="btn_no_periodo" data-dismiss="modal" class="btn btn-warning close" onclick="cerrar_modal()">No</button>
            </div>
        </div>
    </div>
</div>



<script>
var grupo_periodo;
var trabajo_periodo;

$('.td_planta').click(function(){
    var id=$(this).data('id');
    var tipo=$(this).data('tipo');
    var grupo=$(this).data('grupo');
    var trabajo=$(this).data('trabajo');
    var desc='#'+$(this).data('detalle')+' '+$(this).data('desc');
    var periodo=$(this).data('periodo');
    $.post('{{url('/trabajos/planes/detalle_trabajo')}}', {_token:'{{csrf_token()}}',id_plan:{{ $dato->id_plan }},id:id,tipo:tipo,grupo:grupo,trabajo:trabajo,periodo:periodo,contratas:$('#multi-contratas').val(),plantas:$('#multi-planta').val(),zonas:$('#multi-zonas').val(),grupos:$('#multi-grupos').val()}, function(data, textStatus, xhr) {
        $('.modal-body').empty();
        $('#detalle_modal').html(data);
        $('#desc_detalle').html(desc);
        $('#detalle-trabajo').modal('show');
    });
    
});

$('.td_periodo').click(function(){
    grupo_periodo=$(this).data('grupo');
    trabajo_periodo=$(this).data('trabajo');
    var desc=$(this).data('desc');
    var url="{{ url('/trabajos/planes/detalle_periodo') }}/{{ $dato->id_plan }}/"+grupo_periodo+"/"+trabajo_periodo;
    $('.modal-body').empty();
    $('#detalle_periodo').load(url);
    $('#desc_periodo').html(desc);
    $('#detalle-periodo').modal('show');
});

$('#btn_si_detalle').click(function(){
        var url = "{{ url('/trabajos/planes/detalle_save') }}";
        var form = $('#edit_plan_detalle');
        var data = form.serialize();
        $.post(url, data, function (result) {
            if (result.error) {
                toast_error(result.title,result.error);
            } else {
                toast_ok(result.title,result.message);
                $('.modal').modal('hide');
                //$('#detalle_plan').load("{{ url('/trabajos/planes/detalle',[$dato->id_plan,$dato->grupo,$dato->trabajo]) }}");
                $('.select2-multiple').trigger('change');
            }
        });
    });

$('#btn_si_periodo').click(function(){
    var periodo=$('.cronResult').html();
    $.post('{{url('/trabajos/planes/periodo_save')}}', {_token:'{{csrf_token()}}',id_plan:{{ $dato->id_plan }},grupo:grupo_periodo,trabajo:trabajo_periodo,periodo:periodo}, function(data, textStatus, xhr) {
        if (data.error) {
                toast_error(data.title,data.error);
            } else {
                toast_ok(data.title,data.message);
                $('.modal').modal('hide');
                //$('#detalle_plan').load("{{ url('/trabajos/planes/detalle',$dato->id_plan)}}/"+grupo_periodo+"/"+trabajo_periodo);
                $('.select2-multiple').trigger('change');
            }
    });
});

$('.span_cron').each(function(item){
    if($(this).data('expresion')!=''){
        $(this).attr('title',cronstrue.toString($(this).data('expresion'),{ use24HourTimeFormat: true,locale: "es",dayOfWeekStartIndexZero: false }));
    }
   
})


</script>
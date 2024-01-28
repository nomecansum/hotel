@php
    //dd($sched);   

    function check_hora($d,$h,$sched){
        foreach($sched as $sc){
            if($sc->num_dia==$d){
                foreach($sc->horas as $sh){
                    if($sh==$h){
                        return "checked";
                    }
                }
            }
        }
    }
    $list_dias=[];
@endphp


<div class="row mb-3">
    <div class="col-md-3">
        <label for="fec_inicio">Desde</label><br>
        <div class="input-group float-right" id="div_fechas">
            <input type="text" class="form-control pull-left singledate" name="fec_inicio"  id="fec_inicio" style="width:120px"  value="{{ isset($regla->fec_inicio) ? Carbon\Carbon::parse($regla->fec_inicio)->format('d/m/Y') : Carbon\Carbon::now()->format('d/m/Y') }}">
            <span class="btn input-group-text btn-secondary btn_fecha"   style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
        </div>
    </div>
    <div class="col-md-3">
        <label for="fec_inicio">Hasta</label><br>
        <div class="input-group float-right" id="div_fechas">
            <input type="text" class="form-control pull-left singledate" name="fec_fin"  id="fec_fin" style="width:120px"  value="{{ isset($regla->fec_fin) ? Carbon\Carbon::parse($regla->fec_fin)->format('d/m/Y') : Carbon\Carbon::now()->addYear()->format('d/m/Y') }}">
            <span class="btn input-group-text btn-secondary btn_fecha2"   style="height: 40px"><i class="fas fa-calendar mt-1"></i></span>
        </div>
    </div>
</div>
<table class="table " border="0" cellpadding="0" cellspacing="0">
    <thead>
        <tr class="p-0">
            <th></th>
            @for($h=0; $h<24; $h++)
                <th data-hora={{ $h }} class="td_hora p-0" style="width: 3%;">&nbsp;&nbsp;{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        @for($d=1; $d<8; $d++)
            <tr class="p-0">
                <td class="td_dia" data-dia="{{ $d }}">{{$dowMap[$d-1]}}</td>
                @for($h=0; $h<24; $h++)
                    <td class="p-0 text-center">
                        <div class="p-0 m-0">
                            {{-- <input type="checkbox" class="chk_dia" name="dia{{ $d }}[]" data-dia="{{ $d }}" data-hora="{{ $h }}" id="check_{{ $d }}_{{ $h }}" value="{{ $h }}" @isset($sched){{ check_hora($d,$h,$sched) }}@endisset /> --}}
                            <div class="form-check pt-2 fs-4">
                                <input name="dia{{ $d }}[]" data-dia="{{ $d }}" data-hora="{{ $h }}" id="check_{{ $d }}_{{ $h }}" value="{{ $h }}" @isset($sched){{ check_hora($d,$h,$sched) }}@endisset class="form-check-input chk_dia" type="checkbox">
                                <label class="form-check-label" for="check_{{ $d }}_{{ $h }}"></label>
                                @php
                                    if(isset($sched) && check_hora($d,$h,$sched)){
                                        $list_dias[]=$dowMap[$d-1];
                                    }
                                @endphp
                            </div>
                        </div>
                    </td>
                @endfor
            </tr>
        @endfor
    </tbody>
</table>
<div class="row">
    <div class="col-md-12 text-end">
        <button type="submit" class="btn btn-primary btn_form float-right">{{trans('general.submit')}}</button>
    </div>
</div>

<script>
    $('#count_programacion').html('('+$('#fec_inicio').val() + ' - ' + $('#fec_fin').val() +' | {{ implode(",",array_unique($list_dias)) }})');

    $('.td_dia').click(function(){
        $('[data-dia='+$(this).data('dia')+']').each(function () { this.checked = !this.checked; });
    })

    $('.td_hora').click(function(){
        $('[data-hora='+$(this).data('hora')+']').each(function () { this.checked = !this.checked; });
    })

    $('.chk_dia').click(function(){
        $(this).checked=!$(this).is('checked');
    })

    $('.btn_fecha').click(function(){
        picker.open('#fec_inicio');
    })

    $('.btn_fecha2').click(function(){
        picker.open('#fec_fin');
    })

    function check_fechas(){
        if(moment($('#fec_inicio').val(),"DD/MM/YYYY") > moment($('#fec_fin').val(),"DD/MM/YYYY"))
            $('#fec_inicio').val($('#fec_fin').val());
    }

    const picker1 = MCDatepicker.create({
        el: "#fec_inicio",
        dateFormat: cal_formato_fecha,
        autoClose: true,
        closeOnBlur: true,
        firstWeekday: 1,
        disableWeekDays: cal_dias_deshabilitados,
        customMonths: cal_meses,
        customWeekDays: cal_diassemana
    });

    const picker2 = MCDatepicker.create({
        el: "#fec_fin",
        dateFormat: cal_formato_fecha,
        autoClose: true,
        closeOnBlur: true,
        firstWeekday: 1,
        disableWeekDays: cal_dias_deshabilitados,
        customMonths: cal_meses,
        customWeekDays: cal_diassemana
    });

    picker1.onSelect((date, formatedDate) => {
        check_fechas();
        
    });

    picker2.onSelect((date, formatedDate) => {
        check_fechas();
    });
</script>
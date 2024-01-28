<div class="form-group">
    <label>Fechas</label>
    <div class="input-group">
        <input type="text" class="form-control pull-left" id="fechas" name="fechas"  @if(isset($singleMode) && $singleMode==true) value="{{ Carbon\Carbon::now()->format('d/m/Y')}}" @else value="{{ (isset($f1)?$f1->format('d/m/Y'):Carbon\Carbon::now()->startofmonth()->format('d/m/Y')).' - '.(isset($f2)?$f2->format('d/m/Y'):Carbon\Carbon::now()->endofmonth()->format('d/m/Y')) }}"@endif>
        <span class="btn input-group-text btn-secondary btn_calendario"  style="height: 44px;"><i class="fas fa-calendar mt-1"></i> <i class="fas fa-arrow-right"></i> <i class="fas fa-calendar mt-1"></i></span>
    </div>
</div>
@section('scripts5')
<script>
//Date range picker
var picker = new Litepicker({
    element: document.getElementById( "fechas" ),
    singleMode: {{ $singleMode ?? 'false' }},

    @desktop numberOfMonths: 2, @elsedesktop numberOfMonths: 1, @enddesktop
    @desktop numberOfColumns: 2, @elsedesktop numberOfColumns: 1, @enddesktop
    autoApply: true,
    format: 'DD/MM/YYYY',
    lang: "es-ES",
    tooltipText: {
        one: "day",
        other: "days"
    },
    tooltipNumber: (totalDays) => {
        return totalDays - 1;
    },
    setup: (picker) => {
        picker.on('selected', (date1, date2) => {
            
            try{
                change_fechas(date1, date2);
            } catch(err) { console.log(err)}
        }),
        picker.on('show', () => {
            $('#tabla').css('margin-top', '200px');
        }),
        picker.on('hide', () => {
            $('#tabla').css('margin-top', '0px');
        });
    }
});

$('.btn_calendario').click(function(){
    $('#fechas').trigger('click');
})
</script>
@endsection
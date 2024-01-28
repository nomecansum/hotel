<label>Slots de reserva disponibles</label><br>
@foreach($slots as $slot)
    <a class="btn btn-default slot @if($loop->first) btn1 @endif @if((isset($reserva) && Carbon\Carbon::parse($reserva->fec_reserva)->format('H:i')==$slot->hora_inicio && Carbon\Carbon::parse($reserva->fec_fin_reserva)->format('H:i')==$slot->hora_fin ) ) btn-info   @endif btn_slot" href="javascript:void(0)" data-inicio="{{ $slot->hora_inicio }}" data-fin="{{ $slot->hora_fin }}">
        {{ $slot->hora_inicio  }} <i class="fa-solid fa-right"></i> {{ $slot->hora_fin }}
    </a>
@endforeach

<script>
    $('.btn_slot').click(function(){
        $('#hora_inicio').val($(this).data('inicio'));
        $('#hora_fin').val($(this).data('fin'));
        $('.btn_slot').removeClass('btn-info');
        $(this).addClass('btn-info');
        comprobar_puestos();  
    })
    @if(!isset($reserva))
        $('.btn1').addClass('btn-info');
        $('.slot.btn1').click();
    @endif
</script>
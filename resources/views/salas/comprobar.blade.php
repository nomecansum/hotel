@foreach($salas as $sala)
    @php
        $reserva_sala=$reservas->where('id_puesto',$sala->id_puesto);
        $link="res_sala";
    @endphp
    @include('salas.fill_sala')
@endforeach

<script>

    @if($salas->isEmpty())
        $('.btn_reservar').hide();
    @else
        $('.btn_reservar').show();
    @endif

$('.res_sala').hover(function(){
    $('.res_sala').removeClass('bg-gray');
    $(this).addClass('bg-gray');
    $(this).css('cursor','pointer');
})

var tooltip = $('.add-tooltip');
if (tooltip.length)tooltip.tooltip();


$('.res_sala').click(function(){
    $('#des_puesto_form').val($(this).data('desc'));
    $('#id_puesto').val($(this).data('id'));
    $('#frm_contador').submit();
})

</script>
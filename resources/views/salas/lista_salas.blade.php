
@foreach($salas as $sala)
    @php
        $reserva_sala=$reservas->where('id_puesto',$sala->id_puesto);
    @endphp
        @include('salas.fill_sala')
@endforeach

<script>
var tooltip = $('.add-tooltip');
if (tooltip.length)tooltip.tooltip();

$('#titulo').html("Estado de salas para {!! beauty_fecha(Carbon\Carbon::parse($fecha),0) !!}");
</script>
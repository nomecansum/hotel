<style>
    .des_evento{
        font-size: 12px !important;
    }
</style>

<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title">Reservas</h3>
        {{-- <span class="float-right" id="spin" style="display: none"><img src="{{ url('/img/loading.gif') }}" style="height: 25px;">LOADING</span> --}}
    </div>
    <div class="card-body">
        <div id="calendario"></div>
    </div>
</div>

@php
    $rand=\Str::random(10);
@endphp
@section('scripts5')
    <script>
        function loadMonth(month = null,type = null)
        {
            $('#spin').show();
            $.post('{{url('reservas/loadMonthSchedule')}}', {_token:'{{csrf_token()}}',month: month,type:type,emp:'{{Auth::user()->id}}'}, function(data, textStatus, xhr) {
                $('#calendario').html(data);
                $('.des_evento').css('font-size','0.4vw');
                $('.des_evento').css('font-weight','normal');
                $('.des_evento').css("overflow","hidden");
                $('.dia').css('height','50px')
                
                $('.changeMonth').click(function(event) {
                    loadMonth($(this).data('month'),$(this).data('action'));
                });
                $('#spin').hide();
                const calTriggerList{{$rand}} = [...document.querySelectorAll( '.cal-tooltip' )];
                const caltipList = calTriggerList{{$rand}}.map( tooltipTriggerEl => new bootstrap.Tooltip( tooltipTriggerEl,{html: true} ));
            });
        }

        $(function(){
            loadMonth(function(){
                $('.td_calendar').off('click');
            });
        })
        

        

    </script>
@endsection
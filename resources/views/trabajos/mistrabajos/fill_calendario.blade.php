@php
    use Carbon\Carbon;
    $fecha_ant=Carbon::parse($fecha->format('Y-m-d'))->startOfMonth()->subHour(2)->format('Y-m-d');
    $fecha_sig=Carbon::parse($fecha->format('Y-m-d'))->endOfMonth()->addDay()->startOfMonth()->format('Y-m-d');
    $ultimo_dia=$fecha->endOfMonth()->format('d');
    $meses = ["enero","febrero","marzo","abril","mayo","junio","julio","agosto","septiembre","octubre","noviembre","diciembre"];
    $hoy=Carbon::now()->format('Y-m-d');
@endphp

<table class="table table-condensed w-100" style="font-size: 12px">
    <thead>
        <tr>
            <th><a data-month="{{ $fecha_ant }}" data-action="sub" class="changeMonth" style="float:left; font-size: 18px; cursor: pointer;"> <i class="fas fa-arrow-alt-left"></i> </a></th>
            <th colspan="{{ $ultimo_dia-2 }}" class="text-center">
                {{trans('strings.'.$meses[Carbon::parse($fecha)->format('n')-1]).' '. ucwords(Carbon::parse($fecha)->format('Y')) }}
            </th>
            <th><a data-month="{{ $fecha_sig }}" data-action="add" class="changeMonth" style="float:right; font-size: 18px; cursor: pointer;"> <i class="fas fa-arrow-alt-right"></i> </a></th>
        </tr>
        <tr>
            @for ($n=1;$n<=$ultimo_dia;$n++)
                <th style="font-weight: normal; width: 20px; background-color: #eee" class="text-center">{{ $n }}</th>
            @endfor
        </tr>
        <tr>
            @for ($n=1;$n<=$ultimo_dia;$n++)
                <th style="font-weight: normal; width: 20px; background-color: #ccc; font-size: 8px; color: #fff" class="text-center">{{ Carbon::parse(Carbon::parse($fecha->format('Y-m-').$n))->ISOformat('dd') }}</th>
            @endfor
        </tr>
    </thead>
    <tbody>
        <tr>
            @for ($n=1;$n<=$ultimo_dia;$n++)
                @php
                    $dia=Carbon::parse($fecha->format('Y-m-d'))->startOfMonth()->addDay($n-1)->format('Y-m-d');
                    $datos_dia=$calendario->where('fecha_corta',$dia)->first();
                @endphp
                <td style="border: 1px solid #eee; font-size: 12px; cursor: pointer" class="text-center hover-this td_dia {{ $dia==$hoy?'bg-info':'' }}" data-fecha="{{ $dia }}">{{ $datos_dia->trabajos??'' }}</td>
            @endfor
        </tr>
    </tbody>
</table>
<script>

    $('.changeMonth').click(function(){
        loadMes($(this).data('month'),'');
    })

    $('.td_dia').click(function(){
        loadDia($(this).data('fecha'));
        $('.td_dia').removeClass('bg-warning');
        $(this).addClass('bg-warning');
    })
    $('.td_dia').hover(function(){
        $(this).css('background-color','#eee');
    },function(){
        if($(this).data('fecha')!= '{{ $hoy }}'){
            $(this).css('background-color','transparent');
        }
    })
</script>
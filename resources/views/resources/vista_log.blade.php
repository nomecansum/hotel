
<style>
    td {padding: 1px 5px 1px 5px}
</style>
<div class="table-responsive rounded">
    <table class="table table-condensed">
        @foreach($log as $l)
            @php
                $bgcolor = null;
                switch($l->tip_mensaje){
                    case ("error"):
                        $color="#f25c5a";
                    break;
                    case "warning":
                    $color="#ffe066";
                    break;
                    case "debug":
                        $color="#8e9aaf";
                    break;
                    case "info":
                        $color="#70c2b4";
                    break;
                    case "critical":
                        $color="#ff0000";
                    break;
                    case "notice":
                        $color="#447a9c";
                    break;
                    case "alert":
                        $color="#ff9f1a";
                    break;
                    case "user":
                        $color="#060452";
                        $bgcolor="#7cc0d0";
                    break;

                }
                $des_log= $l->txt_log;
                $des_log=str_replace('Iniciando proceso','<span style="color: #4169E1; font-weight: bold">Iniciando proceso</span>',$des_log);
                $des_log=str_replace('Finalizado el procesado','<span style="color: #3CB371; font-weight: bold">Finalizado el procesado</span>',$des_log);
                $des_log=str_replace('ID a procesar','<span style="color: #BA55D3; font-weight: bold">ID a procesar</span>',$des_log);
                $des_log=str_replace('Accion','<span style="color: #000080; font-weight: bold">Accion</span>',$des_log);
                $des_log=str_replace('Iteracion','<span style="color: #000080; font-weight: bold">Superado</span>',$des_log);
                $des_log=str_replace('Superado el maximo de iteraciones','<span style="color: #f08080; font-weight: bold">Superado el maximo de iteraciones</span>',$des_log);
                $des_log=str_replace('Añadido no molestar','<span style="color: #20b2aa; font-weight: bold">Añadido no molestar</span>',$des_log); 
                $des_log=str_replace('SOLO UNA','<span style="color: #93ef2a; font-weight: bold">SOLO UNA</span>',$des_log);    
            @endphp
            <tr style="font-size: 10px; @isset($bgcolor) background-color: {{ $bgcolor }} @endisset"><td style="width: 5%"><b>{{ Carbon\Carbon::parse($l->fec_log)->setTimezone(session('timezone'))->format('H:i')}}</b></td><td class="font-bold" style="color: {{ $color }}; font-weight: bold">{{ $l->tip_mensaje }}</td><td> {!! nl2br($des_log) !!}</td></tr>
        @endforeach
    </table>
</div>
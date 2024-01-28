<style type="text/css">
    .btn_filtro{
        cursor: pointer;
    }
</style>

<div class="table-responsive rounded">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-1 btn_total btn_filtro" data-tipo="total" style="color:#000000"></div>
        <div class="col-md-1 btn_error btn_filtro"  data-tipo="error" style="color:#f25c5a"></div>
        <div class="col-md-1 btn_warning btn_filtro"  data-tipo="warning" style="color:#ffe066"></div>
        <div class="col-md-1 btn_debug btn_filtro"  data-tipo="debug" style="color:#8e9aaf"></div>
        <div class="col-md-1 btn_info btn_filtro"  data-tipo="info" style="color:#00a4eb"></div>
        <div class="col-md-1 btn_success btn_filtro"  data-tipo="success" style="color:#a4bd00"></div>
        <div class="col-md-1 btn_critical btn_filtro"  data-tipo="critical" style="color:#ff0000"></div>
        <div class="col-md-1 btn_alert btn_filtro"  data-tipo="alert" style="color:#ff9f1a"></div>
        <div class="col-md-1 btn_user btn_filtro"  data-tipo="user" style="color:#7cc0d0"></div>
        
    </div>
    <table class="table">
        @foreach($log as $l)
            @php
                $bgcolor = null;
                //Contador de tipos de mensaje
                $tipo=$l->tip_mensaje;
                if(isset($$tipo)){
                    $$tipo++;
                } else {
                    $$tipo=0;
                }
                switch($l->tip_mensaje){
                    case "error":
                        $color="#f25c5a";
                    break;
                    case "warning":
                    $color="#ffe066";
                    break;
                    case "debug":
                        $color="#8e9aaf";
                    break;
                    case "info":
                        $color="#00a4eb";
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
                    case "success":
                        $color="#a4bd00";
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
                $des_log=str_replace('Iteracion','<span style="color: #000080; font-weight: bold">Iteracion</span>',$des_log);
                $des_log=str_replace('Superado el maximo de iteraciones','<span style="color: #f08080; font-weight: bold">Superado el maximo de iteraciones</span>',$des_log);
                $des_log=str_replace('Añadido no molestar','<span style="color: #20b2aa; font-weight: bold">Añadido no molestar</span>',$des_log);
                $des_log=str_replace('SOLO UNA','<span style="color: hsl(27, 87%, 67%); font-weight: bold">SOLO UNA</span>',$des_log);
            @endphp
            <tr class="row_log {{ $l->tip_mensaje }}"  style="font-size: 10px; @isset($bgcolor) background-color: {{ $bgcolor }}; color: {{ txt_blanco($bgcolor) }} @endisset"><td style="width: 5%"><b>{{ Carbon\Carbon::parse($l->fec_log)->setTimezone(session('timezone'))->format('H:i')}}</b></td><td class="font-bold" style="color: {{ $color }}">{{ $l->tip_mensaje }}</td><td> {!! nl2br($des_log) !!}</td></tr>
        @endforeach
    </table>
</div>
<script>
    $(document).ready(function(){
        $('.btn_total').html('Total: {{ count($log) }}');
        $('.btn_error').html('Error: {{ $error??0 }}');
        $('.btn_warning').html('Warning: {{ $warning??0 }}');
        $('.btn_debug').html('Debug: {{ $debug??0 }}');
        $('.btn_info').html('Info: {{ $info??0 }}');
        $('.btn_success').html('Success: {{ $success??0 }}');
        $('.btn_critical').html('Critical: {{ $critical??0 }}');
        $('.btn_alert').html('Alert: {{ $alert??0 }}');
        $('.btn_user').html('User: {{ $user??0 }}');
       
    });

    $('.btn_filtro').click(function(){
        if($(this).data('tipo')=='total'){
            $('.row_log').show();
            return;
        } else {
            $('.row_log').hide();
            $('.'+$(this).data('tipo')).show();
        }
        
    });
</script>

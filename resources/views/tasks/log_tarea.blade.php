<style type="text/css">
    .btn_filtro{
        cursor: pointer;
    }
</style>
<div class="table-responsive rounded" style="width:100%">
    <div class="row">
        <div class="col-md-4"></div>
        <div class="col-md-1 btn_total btn_filtro" data-tipo="total" style="color:#000000"></div>
        <div class="col-md-1 btn_error btn_filtro"  data-tipo="error" style="color:#f25c5a"></div>
        <div class="col-md-1 btn_warning btn_filtro"  data-tipo="warning" style="color:#ffe066"></div>
        <div class="col-md-1 btn_debug btn_filtro"  data-tipo="debug" style="color:#8e9aaf"></div>
        <div class="col-md-1 btn_info btn_filtro"  data-tipo="info" style="color:#00a4eb"></div>
        <div class="col-md-1 btn_critical btn_filtro"  data-tipo="critical" style="color:#ff0000"></div>
        <div class="col-md-1 btn_alert btn_filtro"  data-tipo="alert" style="color:#ff9f1a"></div>
        <div class="col-md-1 btn_user btn_filtro"  data-tipo="user" style="color:#7cc0d0"></div>
        <div class="col-md-1 btn_notice btn_filtro"  data-tipo="notice" style="color:#447a9c"></div>
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
                case "user":
                    $color="#060452";
                    $bgcolor="#7cc0d0";
                break;

            }
            $des_log= $l->txt_log;
            $des_log=str_replace('Inicio de la tarea','<span style="color: #4169E1; font-weight: bold">Inicio de la tarea</span>',$des_log);
            $des_log=str_replace('Fin de la tarea','<span style="color: #3CB371; font-weight: bold">Fin de la tarea</span>',$des_log);
            $des_log=str_replace('clientes','<span style="color: #BA55D3; font-weight: bold">clientes</span>',$des_log);
            $des_log=str_replace('informe','<span style="color: #000080; font-weight: bold">informe</span>',$des_log);
            $des_log=str_replace('RunTask','<i class="fa-solid fa-play text-success"></i> <span class="font-bold text-success"> RunTask</span>',$des_log);
            $des_log=str_replace('ENDTask','<i class="fa-solid fa-stop text-danger"></i><span class="font-bold text-danger">  ENDTask</span>',$des_log);
            $des_log=str_replace('Schedule start','<i class="fa-sharp fa-solid fa-stopwatch" style="color:#f4a462"></i><span class="font-bold" style="color:#f4a462">  Schedule start</span>',$des_log);
            $des_log=str_replace('Fin schedule','<i class="fa-thin fa-stopwatch"  style="color:#3d5a7f"></i><span class="font-bold" style="color: #3d5a7f">  Fin schedule</span>',$des_log);
            
            
        @endphp
            <tr class="row_log {{ $l->tip_mensaje }}"   style="font-size: 10px" @isset($bgcolor) background-color: {{ $bgcolor }}; color: {{ txt_blanco($bgcolor) }} @endisset><td style="width: 5%"><b>{{ Carbon\Carbon::parse($l->fec_log)->setTimezone(session('timezone'))->format('H:i')}}</b></td><td style="font-weight: bold; color: {{ $color }}">{{ $l->tip_mensaje }}</td><td> {!! nl2br($des_log) !!}</td></tr>
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
        $('.btn_critical').html('Critical: {{ $critical??0 }}');
        $('.btn_alert').html('Alert: {{ $alert??0 }}');
        $('.btn_user').html('User: {{ $user??0 }}');
        $('.btn_notice').html('Notice: {{ $notice??0 }}');
    });

    $('.btn_filtro').click(function(){
        $('.btn_filtro').removeClass('b-all  bg-light');
        $(this).addClass('b-all bg-light');
        if($(this).data('tipo')=='total'){
            $('.row_log').show();
            return;
        } else {
            $('.row_log').hide();
            $('.'+$(this).data('tipo')).show();
        }
        
    });
</script>
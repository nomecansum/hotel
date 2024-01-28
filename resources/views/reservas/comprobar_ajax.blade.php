<script>
@foreach($puestos as $puesto)
    @php
        $reserva=$reservas->where('id_puesto',$puesto->id_puesto)->first();  
        $asignado_usuario=$asignados_usuarios->where('id_puesto',$puesto->id_puesto)->first();  
        $asignado_otroperfil=$asignados_nomiperfil->where('id_puesto',$puesto->id_puesto)->first();  
        $asignado_miperfil=$asignados_miperfil->where('id_puesto',$puesto->id_puesto)->first();  
        $cuadradito=\App\Classes\colorPuestoRes::colores($reserva, $asignado_usuario, $asignado_miperfil,$asignado_otroperfil,$puesto,"Reservas",Carbon\Carbon::now()->format('d/m/Y'));
        $es_reserva="Reservas";
        if(isMobile()){
            $puesto->factor_puestow=15;
            $puesto->factor_puestoh=15;
            $puesto->factor_letra=2.8;
        } else {
            $puesto->factor_puestow=3.7;
            $puesto->factor_puestoh=3.7;
            $puesto->factor_letra=0.8;
        }
    @endphp
    $("#puesto{{ $puesto->id_puesto }}").removeClass("disponible");
    $("#puesto{{ $puesto->id_puesto }}").addClass("{{  $cuadradito['clase_disp'] }}");
    $("#puesto{{ $puesto->id_puesto }}").prop('title', "{!! nombrepuesto($puesto) ." \r\n ".$cuadradito['title'] !!}" );

@endforeach
</script>
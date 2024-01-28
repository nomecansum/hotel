@if($puesto->mca_incidencia=='S')
    <div class="puesto_icono"  ><i class="fas fa-exclamation-triangle" style="color: #fff"></i></div>
@elseif($puesto->id_estado==5 || $puesto->id_estado==4)
    <div  class="puesto_icono"><i class="fas fa-ban" style="color: #fff"></i></div>
@elseif($puesto->id_estado==7)
    <div  class="puesto_icono"><i class="fa-solid fa-poll-people"  style="color: #fff"></i></div>
@elseif(isset($reserva))
    <div  class="puesto_icono" style="color: #ff0; font-size: 4em">R</div>
@elseif(isset($asignado_usuario))
    <div  class="puesto_icono mt-4"  style="color: {{ isset($es_reserva) && $es_reserva?"#fff":"#9bc63f" }}; font-size: 1.5em; font-weight: bolder; opacity: 0.8">{{ iniciales($asignado_usuario->name,3) }}</div>
@elseif(isset($asignado_miperfil))
    <div  class="puesto_icono" style=" color: #05688f; "><i class="fad fa-user" style="color: {{ isset($es_reserva) && $es_reserva?"#339470":"#fff" }};"></i></div>
@elseif(isset($asignado_otroperfil))
    <div  class="puesto_icono" style=""><i class="fad fa-users" style="color: #fff"></i></div>
@elseif(isset(session('CL')['modo_visualizacion_puestos']) && session('CL')['modo_visualizacion_puestos']=='I')
    <div  class="puesto_icono" style="line-height: 0px;"><i class="{{ $puesto->icono_tipo??'' }} fa-2x" style="color: {{ $puesto->color_tipo??'' }}"></i></div>
@endif
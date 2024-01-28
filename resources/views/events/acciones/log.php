<?php

$descripcion = "Escribe un mensaje en el log de la tarea de eventos";

$campos_notificaciones=true;

$icono='<i class="fa-solid fa-file-lines"></i>';

$tipo_destino='*';

$params='{
    "parametros":[
        {
            "label": "Tipo",
            "name": "tipo",
            "tipo": "list",
            "multiple": false,
            "list": "error|warning|debug|info|critical|notice|alert",
            "values": "error|warning|debug|info|critical|notice|alert",
            "required": true
        },
        {
            "label": "Cuerpo del mensaje",
            "name": "cuerpo",
            "tipo": "txt",
            "def": "",
            "required": true
        },
        {
            "label": "Enviar solo uno por iteracion",
            "name": "solouno",
            "tipo": "bool",
            "def": "true",
            "required": true
        }
    ]
}';

$func_accion = function($accion, $resultado, $campos,$id) {

    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));

    $resultado = collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso
    $datos = $resultado->where('id', $id)->first();
    //DAtos para las notificaciones comunes de la iteracion
    $campo= new stdClass();
    $campo->label='[cuenta_id]';
    $campo->desc='Cuenta de ID afectados';
    $campos[]=$campo;
    $campo= new stdClass();
    $campo->label='[lista_id]';
    $campo->desc='Lista de ID afectados';
    $campos[]=$campo;
    $campo= new stdClass();
    $campo->label='[lista_nombres]';
    $campo->desc='Lista de entidades afectadas';
    $campos[]=$campo;

    $datos->cuenta_id=$resultado->count();
    $datos->lista_id=$resultado->pluck('id')->implode(', ');
    $datos->lista_nombres=$resultado->pluck('nombre')->implode(', ');

    log_evento(comodines_texto(valor($param_accion, "cuerpo"), $campos, $datos),$accion->cod_regla,valor($param_accion, "tipo"));

    //A partir de aqui empieza la parte "personalizada" de la accion

    //Especial atencion a la funcion comodines_texto
    
    //enviar_email($emp->dir_email, config('mail.from.address'), $emp->dir_email, $emp->nom_empleado." ".$emp->ape_empleado, valor($param_accion, "titulo"), "email.mail_eventos", $emp, $cli, null, comodines_texto(valor($param_accion, "cuerpo"), $campos, $datos));

    //echo("Enviado email a ".$datos->nombre." ".$emp->dir_email.chr(10).chr(13));
    //echo("Ejecutada accion enviar email empleado Regla: ".$accion->cod_regla." Accion: [".$accion->val_iteracion."-".$accion->num_orden."]".chr(10).chr(13));
    $solouno=valor($param_accion, "solouno");
    if(isset($solouno) && $solouno==1){
        return ['no_ejecutar_mas'=>true];
    }
}
?>

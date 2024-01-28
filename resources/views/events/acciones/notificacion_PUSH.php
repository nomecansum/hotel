<?php
if(!function_exists('mis_clientes')){
    function mis_clientes(){
        if(is_array(clientes())){
            $clientes=implode(",",clientes());
        }   else {
            $clientes=implode(",",clientes()->ToArray());
        } 
        return $clientes;
    }
}
$descripcion = "Envia una notificacion PUSH el usuario";

$campos_notificaciones=true;

$icono='<i class="fa-solid fa-message-dots"></i>';

$tipo_destino='*';

$params='{
    "parametros":[
        {
            "label": "Texto de la notificacion",
            "name": "cuerpo",
            "tipo": "txt",
            "def": "",
            "required": true
        },
        {
            "label": "Destinatarios",
            "name": "usuarios",
            "tipo": "list_db",
            "multiple": true,
            "sql": "SELECT DISTINCT \n
                        `users`.`id` as id, \n
                        concat(\'[\',nom_cliente,\'] - \',`users`.`name`) as nombre  \n
                    FROM \n
                        `users` \n
                        INNER JOIN `clientes` ON (`users`.`id_cliente` = `clientes`.`id_cliente`) \n
                    WHERE \n
                    `users`.`id_cliente` in('.mis_clientes().')  \n
                    ORDER BY 2", 
            "required": true,
            "buscar": true
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

    foreach(valor($param_accion, "usuarios") as $u){
        notificar_usuario( $u,null,null,comodines_texto(valor($param_accion, "cuerpo"), $campos, $datos),[3],1,[],0);
    }

    $solouno=valor($param_accion, "solouno");
    if(isset($solouno) && $solouno==1){
        return ['no_ejecutar_mas'=>true];
    }
}
?>

<?php

$descripcion = "Envia un email a un usuario concreto";
$campos_notificaciones=false;
$params='{
    "parametros":[
        {
            "label": "Parametro de mentira para que este relleno",
            "name": "void",
            "tipo": "void",
            "required": false
        }
    ]
}';

$tipo_destino='*';
$icono="<i class='fa-solid fa-folder-magnifying-glass'></i>";

$func_accion = function($accion, $resultado, $campos, $id,$output) {

    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));

    $evento=DB::table('eventos_reglas')
        ->where('cod_regla',$accion->cod_regla)
        ->first();
    Log::debug('Reevaluando Comando :'.resource_path('views/events/comandos').'/'.$evento->nom_comando);
    include(resource_path('views/events/comandos').'/'.$evento->nom_comando);
    $resultado_json=$func_comando($evento,$output);
    $resultado=json_decode($resultado_json);
    unset($func_comando);
    try{
        $campos=json_decode($campos);
        $campos=$campos->campos;
    } catch(\Exception $e){
        $campos=[];
    }

    $solouno=valor($param_accion, "solouno");
    return ['lista_ids'=>$resultado->lista_id,
            'no_ejecutar_mas'=>true,
            'resultado'=>$resultado,
            'campos'=>$campos,
            'no_ejecutar_mas'=>$solouno
        ];
}
?>

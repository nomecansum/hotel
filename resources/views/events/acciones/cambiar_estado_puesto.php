<?php
    use App\Models\users;
    use App\Models\reglas;
    use App\Models\puestos;
    use App\Models\logpuestos;
    use Carbon\Carbon;

$descripcion="Cambia el estado del puesto por el indicado";

$campos_notificaciones=false;

$icono='<i class="fa-solid fa-arrow-down-square-triangle"></i>';

$detalle_regla=reglas::find($regla??0);
if(isset($detalle_regla->clientes)){
    $clientes=$detalle_regla->clientes;
} else {
    $clientes=implode(",",clientes());
}

$tipo_destino='id_puesto';

$params='{
    "parametros":[
        {
            "label": "Estado",
            "name": "id_estado",
            "tipo": "list_db",
            "multiple": false,
            "sql": "select id_estado as id, des_estado as nombre from estados_puestos",
            "required": true
        }
        
    ]
}';

$func_accion = function($accion, $resultado, $campos, $id) {
    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));
    $id_estado = valor($param_accion, "id_estado");


    $resultado = Collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso
    $datos = $resultado->where('id',$id)->first();
    Log::debug('Cambiando estado a '. $id_estado.' del puesto '.$datos->nombre);
    //Y enviamos el comando
    $puesto=puestos::find($id);
    $puesto->id_estado=$id_estado;
    $puesto->save();

    DB::table('log_cambios_estado')->insert([
        'id_puesto' => $id,
        'id_estado' => $id_estado,
        'fecha' => Carbon::now(),
        'id_user' => config('app.id_usuario_tareas')
    ]);
    
}
?>

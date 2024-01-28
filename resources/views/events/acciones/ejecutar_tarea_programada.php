<?php
    use App\Models\users;
    use App\Models\reglas;
    use App\Models\tareas;

$descripcion="Ejecuta inmediatamente la tarea programada seleccionada";

$campos_notificaciones=false;

$tipo_destino='*';

$icono='<i class="fa-solid fa-calendar-clock"></i>';

$detalle_regla=reglas::find($regla??0);
$clientes_regla=explode(",",$detalle_regla->clientes??"");
$tareas=tareas::all();
$lista_tareas=[0];
foreach($tareas as $t){
    $t->clientes=explode(",",$t->clientes);
    if(count($t->clientes=array_intersect($clientes_regla,$t->clientes))>0){
        $lista_tareas[]=$t->cod_tarea;
    }
}
$lista_tareas=implode(",",$lista_tareas);
$params='{
    "parametros":[
        {
            "label": "Tarea programada",
            "name": "id_tarea", 
            "tipo": "list_db", 
            "multiple": false, 
            "sql": "SELECT DISTINCT \n
                        `tareas_programadas`.`cod_tarea` as id, \n
                        concat(des_tarea,\' [\',signature,\']\') as nombre  \n
                    FROM \n
                        `tareas_programadas` \n
                    WHERE \n
                    cod_tarea in('.$lista_tareas.')  \n
                    ORDER BY 2", 
            "required": true,
            "buscar": true
        }
    ]
}';


$func_accion = function($accion, $resultado, $campos, $id) {
    //Parte comun de todas las acciones
    $param_accion = $accion->param_accion;
    $param_accion = json_decode(json_decode($param_accion));
    $id_tarea = valor($param_accion, "id_tarea");

    $resultado = Collect($resultado->data); //Cogemos el bloque da datos del resultado que es donde estan los datos de los elementos afectadso
    Log::debug('Ejecutar tarea programada'. valor($param_accion, "id_acion"));
    ini_set('max_execution_time', 300); //300 seconds = 5 minutes
    $tarea=DB::table('tareas_programadas')->where('cod_tarea',$id_tarea)->first();
    log_tarea("Inicio de la tarea [".$tarea->cod_tarea."] ".$tarea->des_tarea,$tarea->cod_tarea);
    log_tarea("Ejecucion forzada de la tarea [".$tarea->cod_tarea."]".$tarea->des_tarea." por Evento ".$accion->cod_regla,$tarea->cod_tarea);
    Artisan::call($tarea->signature,['id'=>$tarea->cod_tarea,'origen'=>'W']);
    log_tarea("Fin de la tarea [".$tarea->cod_tarea."] ".$tarea->des_tarea,$tarea->cod_tarea);
    
}
?>

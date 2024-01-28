<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;

$descripcion="Este comando devuelve los puestos que cumplan con los criterios de busqueda.";

$grupo="A";
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

$optgroup="Puestos";

$params='{
    "parametros":[
        {
            "label": "Estado",
            "name": "id_estado",
            "tipo": "list_db",
            "multiple": true,
            "sql": "select id_estado as id, des_estado as nombre from estados_puestos",
            "required": true
        },
        {
            "label": "Horas desde el ultimo estado",
            "name": "val_horas",
            "tipo": "num",
            "def": "2",
            "required": false
        },
        {
            "label": "Poniendo este parametro a true, indicaremos que necesariamente se debe seleccionar un cliente",
            "name": "control_cliente",
            "tipo": "cli",
            "required": true
        },
        {
            "label": "Tipo de puesto",
            "name": "id_tipo_puesto",
            "tipo": "list_db",
            "multiple": true, 
            "sql": "SELECT DISTINCT \n
                        `puestos_tipos`.`id_tipo_puesto` as id, \n
                        concat(\'[\',nom_cliente,\'] - \',`puestos_tipos`.`des_tipo_puesto`) as nombre  \n
                    FROM \n
                        `puestos_tipos` \n
                        INNER JOIN `clientes` ON (`puestos_tipos`.`id_cliente` = `clientes`.`id_cliente`) \n
                    WHERE \n
                    `puestos_tipos`.`id_cliente` in('.mis_clientes().')  \n
                    ORDER BY 2", 
            "required": false,
            "buscar": true
        },
        {
            "label": "Edificio",
            "name": "id_edificio",
            "tipo": "list_db",
            "multiple": true, 
            "sql": "SELECT DISTINCT \n
                        `edificios`.`id_edificio` as id, \n
                        concat(\'[\',nom_cliente,\'] - \',`edificios`.`des_edificio`) as nombre  \n
                    FROM \n
                        `edificios` \n
                        INNER JOIN `clientes` ON (`edificios`.`id_cliente` = `clientes`.`id_cliente`) \n
                    WHERE \n
                    `edificios`.`id_cliente` in('.mis_clientes().')  \n
                    ORDER BY 2", 
            "required": false,
            "buscar": true
        },
        {
            "label": "Planta",
            "name": "id_planta",
            "tipo": "list_db",
            "multiple": true, 
            "sql": "SELECT DISTINCT \n
                        `plantas`.`id_planta` as id, \n
                        concat(\'[\',nom_cliente,\'] - \',`plantas`.`des_planta`) as nombre  \n
                    FROM \n
                        `plantas` \n
                        INNER JOIN `clientes` ON (`plantas`.`id_cliente` = `clientes`.`id_cliente`) \n
                    WHERE \n
                    `plantas`.`id_cliente` in('.mis_clientes().')  \n
                    ORDER BY 2", 
            "required": false,
            "buscar": true
        }
    ]
}';

$tipo_destino='id_puesto';

//Acciones recomendadas para este comando
$acciones_def='[
       {
        "accion": "cambiar_estado_puesto.php",
        "iteracion": 1,
        "orden" : 1
       }
    ]';

//Los campos son una descriocion de los campos que se podrán leer en las notificaciones directaamente desde los datos leidos del comando
$campos='{
    "campos":
    [
        {
            "label": "[fecha]",
            "desc": "fecha actual"
        },
        {
            "label": "[hora]",
            "desc": "Hora actual"
        },
        {
            "label": "[id]",
            "desc": "ID de puesto"
        },
        {
            "label": "[cod]",
            "desc": "Codigo de puesto"
        },
        {
            "label": "[des_tipo_puesto]",
            "desc": "Tipo de puesto"
        },
        {
            "label": "[des_estado]",
            "desc": "Estado del puesto antes del cambio"
        }
    ]
}';

$func_comando = function($evento,$output){
    //aqui va el codigo que queramos ejecutar, puede ser una simple consulta a BDD o cualquier logica compleja que se necesite
    $parametros=json_decode($evento->param_comando);
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $id_estado=valor($parametros,"id_estado");
    $val_horas=valor($parametros,"val_horas");
    $id_tipo_puesto=valor($parametros,"id_tipo_puesto");
    $id_edificio=valor($parametros,"id_edificio");
    $id_planta=valor($parametros,"id_planta");

    //Construir la query
    $data=DB::table('puestos')
            ->select('puestos.id_puesto as id','puestos.cod_puesto as nombre','puestos_tipos.des_tipo_puesto','estados_puestos.des_estado')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->WhereIn('puestos.id_cliente',explode(",",$evento->clientes))

            ->when($id_edificio, function($q) use ($id_edificio){
                $q->WhereIn('puestos.id_edificio',$id_edificio);
            })
            ->when($id_planta, function($q) use ($id_planta){
                $q->WhereIn('puestos.id_planta',$id_planta);
            })
            ->when($id_tipo_puesto, function($q) use ($id_tipo_puesto){
                $q->WhereIn('puestos.id_tipo_puesto',$id_tipo_puesto);
            })
            ->when($id_estado, function($q) use ($id_estado){
                $q->WhereIn('puestos.id_estado',$id_estado);
            })
            ->where('puestos.fec_ult_estado','<=',Carbon::now()->subHours($val_horas));
        $query=getFullSql($data);
        $data=$data->get();
    
    $lista_id=$data->pluck('id')->toArray();
    //Al final se debe retornar un JSON con la lista de ID que se han obtenido de la consulta y sobre los que se actuará en las acciones
    return json_encode([
        "respuesta" => "ok",
        "comando" => basename(__FILE__, '.php'),
        "tipo_id" => "puestos",
        "table" => "puestos",
        "campo" => "id_puesto",
        "lista_id" =>$lista_id,
        "data" => $data,
        "query" => $query,
        "TS" => Carbon::now()->format('Y-m-d h:i:s')
    ]);
}
?>

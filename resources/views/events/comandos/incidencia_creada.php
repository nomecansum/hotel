<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;

$descripcion="Este comando devuelve las incidencias que hayan sido creadas o modificadas desde la ultima ejecucion del comando.";

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

$optgroup="Incidencias";

$params='{
    "parametros":[
        {
            "label": "Estado",
            "name": "id_estado",
            "tipo": "list_db",
            "multiple": true,
            "sql": "SELECT DISTINCT \n
                        `estados_incidencias`.`id_estado` as id, \n
                        concat(\'[\',nom_cliente,\'] - \',`estados_incidencias`.`des_estado`) as nombre  \n
                    FROM \n
                        `estados_incidencias` \n
                        INNER JOIN `clientes` ON (`estados_incidencias`.`id_cliente` = `clientes`.`id_cliente`) \n
                    WHERE \n
                    `estados_incidencias`.`id_cliente` in('.mis_clientes().')  \n
                    ORDER BY 2", 
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
        },
        {
            "label": "Procedencia",
            "name": "val_procedencia",
            "tipo": "list",
            "multiple": false,
            "list":  "Scan|Web|API|Salas",
            "values": "scan|web|api|salas",
            "required": false
        }
    ]
}';

$tipo_destino='id_puesto';

//Acciones recomendadas para este comando
$acciones_def='[
       {
        "accion": "notificacion_web.php",
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
            "label": "[cod_puesto]",
            "desc": "Codigo de puesto"
        },
        {
            "label": "[des_tipo_puesto]",
            "desc": "Tipo de puesto"
        },
        {
            "label": "[des_estado]",
            "desc": "Estado de la incidencia"
        },
        {
            "label": "[fec_apertura]",
            "desc": "Fecha de apertura de la incidencia"
        },
        {
            "label": "[name]",
            "desc": "Usuario que ha abierto la incidencia"
        },
        {
            "label": "[txt_incidencia]",
            "desc": "Cuerpo de la incidencia"
        },
        {
            "label": "[des_tipo_incidencia]",
            "desc": "Tipo de la incidencia"
        }
    ]
}';

$func_comando = function($evento,$output){
    //aqui va el codigo que queramos ejecutar, puede ser una simple consulta a BDD o cualquier logica compleja que se necesite

    $parametros = json_decode($evento->param_comando);
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $id_estado=valor($parametros,"id_estado");
    $id_tipo_puesto=valor($parametros,"id_tipo_puesto");
    $id_edificio=valor($parametros,"id_edificio");
    $id_planta=valor($parametros,"id_planta");
    $val_procedencia=valor($parametros,"val_procedencia");

    //Construir la query
    $data=DB::table('incidencias')
        ->select('puestos.id_puesto as id','puestos.cod_puesto','puestos.des_puesto','edificios.des_edificio','edificios.id_edificio','plantas.id_planta','plantas.des_planta','estados_incidencias.des_estado as estado_incidencia','causas_cierre.des_causa','incidencias.fec_apertura','incidencias.id_incidencia','incidencias.des_incidencia','incidencias.txt_incidencia','incidencias_tipos.des_tipo_incidencia','users.name')
        ->leftjoin('estados_incidencias','incidencias.id_estado','estados_incidencias.id_estado')
        ->leftjoin('causas_cierre','incidencias.id_causa_cierre','causas_cierre.id_causa_cierre')
        ->join('incidencias_tipos','incidencias.id_tipo_incidencia','incidencias_tipos.id_tipo_incidencia')
        ->join('puestos','incidencias.id_puesto','puestos.id_puesto')
        ->join('edificios','puestos.id_edificio','edificios.id_edificio')
        ->join('plantas','puestos.id_planta','plantas.id_planta')
        ->join('users','incidencias.id_usuario_apertura','users.id')
        ->join('clientes','puestos.id_cliente','clientes.id_cliente')
        ->WhereIn('puestos.id_cliente',explode(",",$evento->clientes))
        ->when($id_estado, function($q) use($id_estado){
            $q->where('incidencias.id_estado',$id_estado);
        })
        ->when($id_tipo_puesto, function($q) use($id_tipo_puesto){
            $q->whereIn('incidencias.id_tipo_puesto',$id_tipo_puesto);
        })
        ->when($id_planta, function($q) use($id_planta){
            $q->whereIn('puestos.id_planta',$id_planta);
        })
        ->when($id_edificio, function($q) use($id_edificio){
            $q->whereIn('puestos.id_edificio',$id_edificio);
        })
        ->when($id_tipo_puesto, function($q) use($id_tipo_puesto){
            $q->whereIn('puestos.id_tipo_puesto',$id_tipo_puesto);
        })
        ->when($val_procedencia, function($q) use($val_procedencia){
            $q->where('incidencias.val_procedencia',$val_procedencia);
        })
        ->where('incidencias.fec_audit','>=',Carbon::parse($evento->fec_ult_ejecucion))
        ->orderby('fec_apertura','desc');
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

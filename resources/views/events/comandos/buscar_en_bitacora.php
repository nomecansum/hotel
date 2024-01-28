<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;

$descripcion="Este comando busca entradas en la bitacora, que cumplan unos determinados criterios que se hayan producido despues de su ultima ejecucion";

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

$optgroup="Varios";

$params='{
    "parametros":[
        {
            "label": "Estado",
            "name": "status",
            "tipo": "list",
            "multiple": false,
            "list":  "|OK|ERROR",
            "values": "0|OK|ERROR",
            "required": false
        },
        {
            "label": "Usuario",
            "name": "id_usuario",
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
            "required": false,
            "buscar": false
        },
        {
            "label": "Modulo",
            "name": "id_modulo",
            "tipo": "list_db",
            "multiple": true,
            "sql": "select distinct(id_modulo) as id, id_modulo as nombre from bitacora order by 2",
            "required": false
        },
        {
            "label": "Seccion",
            "name": "id_seccion",
            "tipo": "list_db",
            "multiple": true,
            "sql": "select distinct(id_seccion) as id, id_seccion as nombre from bitacora order by 2",
            "required": false
        },
        {
            "label": "Texto en la accion (que contenga)",
            "name": "val_texto",
            "tipo": "txt",
            "def": "",
            "required": false
        }
    ]
}';

$tipo_destino='*';

//Acciones recomendadas para este comando
$acciones_def='[
       {
        "accion": "enviar_email_usuario.php",
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
            "label": "[status]",
            "desc": "Estado"
        },
        {
            "label": "[id_usuario]",
            "desc": "ID del usuario"
        },
        {
            "label": "[name]",
            "desc": "Nombre del usuario"
        },
        {
            "label": "[modulo]",
            "desc": "Nombre del modulo"
        },
        {
            "label": "[seccion]",
            "desc": "Nombre de la seccion"
        },
        {
            "label": "[cliente]",
            "desc": "Nombre del cliente"
        },
        {
            "label": "[texto]",
            "desc": "Texto de la accion"
        }
    ]
}';

$func_comando = function($evento,$output){
    //aqui va el codigo que queramos ejecutar, puede ser una simple consulta a BDD o cualquier logica compleja que se necesite

    $parametros = json_decode($evento->param_comando);
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $status=valor($parametros,"status");
    $id_usuario=valor($parametros,"id_usuario");
    $id_modulo=valor($parametros,"id_modulo");
    $id_seccion=valor($parametros,"id_seccion");
    $val_texto=valor($parametros,"val_texto");

    //Construir la query
    $data=DB::table('bitacora')
            ->select('bitacora.id_bitacora as id','bitacora.accion as texto','bitacora.id_modulo','bitacora.id_seccion','bitacora.status','clientes.nom_cliente','users.id as id_usuario','users.name')
            ->join('users','bitacora.id_usuario','users.id')
            ->join('clientes','clientes.id_cliente','users.id_cliente')
            ->where(function($q) use($evento){
                if($evento->clientes!=0){
                    $q->whereIn('users.id_cliente',explode(",",$evento->clientes));
                }
            })
            ->where(function($q) use($status){
                if($status!=0){
                    $q->where('bitacora.status',$status);
                }
            })
            ->when($id_usuario, function($q) use ($id_usuario){
                $q->WhereIn('bitacora.id_usuario',$id_usuario);
            })
            ->when($id_modulo, function($q) use ($id_modulo){
                $q->WhereIn('bitacora.id_modulo',$id_modulo);
            })
            ->when($id_seccion, function($q) use ($id_seccion){
                $q->WhereIn('bitacora.id_seccion',$id_seccion);
            })
            ->when($val_texto, function($q) use ($val_texto){
                $q->Where('bitacora.accion', 'LIKE', "%{$val_texto}%");
            })
            ->where('bitacora.fecha','>=',Carbon::parse($evento->fec_ult_ejecucion));
    $query=getFullSql($data);
    $data=$data->get();
    
    $lista_id=$data->pluck('id')->toArray();
    //Al final se debe retornar un JSON con la lista de ID que se han obtenido de la consulta y sobre los que se actuará en las acciones
    return json_encode([
        "respuesta" => "ok",
        "comando" => basename(__FILE__, '.php'),
        "tipo_id" => "bitacora",
        "table" => "bitacora",
        "campo" => "id_bitacora",
        "lista_id" =>$lista_id,
        "data" => $data,
        "query" => $query,
        "TS" => Carbon::now()->format('Y-m-d h:i:s')
    ]);
}
?>

<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;

$descripcion="Este comando devuelve una lista de usuarios que hayan tenido mas de N reservas anuladas automaticamente por falta de confirmacion en los ultimos X dias.";

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
            "label": "Numero de veces",
            "name": "val_veces",
            "tipo": "num",
            "def": "2",
            "required": true
        },
        {
            "label": "Numero de dias",
            "name": "val_dias",
            "tipo": "num",
            "def": "2",
            "required": true
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

$tipo_destino='id_usuario';

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
            "label": "[veces]",
            "desc": "Numero de veces"
        },
        {
            "label": "[id]",
            "desc": "ID externo de usuario"
        },
        {
            "label": "[name]",
            "desc": "Nombre del usuario"
        },
        {
            "label": "[email]",
            "desc": "e-mail del usuario"
        }
    ]
}';

$func_comando = function($evento,$output){
    //aqui va el codigo que queramos ejecutar, puede ser una simple consulta a BDD o cualquier logica compleja que se necesite

    $parametros = json_decode($evento->param_comando);
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $id_estado=valor($parametros,"id_estado");
    $val_veces=valor($parametros,"val_veces");
    $val_dias=valor($parametros,"val_dias");
    $id_edificio=valor($parametros,"id_edificio");
    $id_planta=valor($parametros,"id_planta");

    //El identificador principal siempre tiene que ser el campo id
    //Construir la query
    $data=DB::table('reservas')
            ->where('fec_reserva','>=',Carbon::now()->subDays($val_dias))
            ->select('users.id_usuario_externo as id','users.name','users.email')
            ->selectraw('count(*) as veces')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('users','reservas.id_usuario','users.id')
            ->WhereIn('puestos.id_cliente',explode(",",$evento->clientes))

            ->when($id_edificio, function($q) use ($id_edificio){
                $q->WhereIn('puestos.id_edificio',$id_edificio);
            })
            ->when($id_planta, function($q) use ($id_planta){
                $q->WhereIn('puestos.id_planta',$id_planta);
            })
            ->when($id_estado, function($q) use ($id_estado){
                $q->WhereIn('puestos.id_estado',$id_estado);
            })
            ->where('reservas.mca_anulada','S')
            ->groupby(['users.id_usuario_externo','users.name','users.email']);
        $query=getFullSql($data);
        $data=$data->get();

    $lista_id=$data->pluck('id')->toArray();
    //Al final se debe retornar un JSON con la lista de ID que se han obtenido de la consulta y sobre los que se actuará en las acciones
    return json_encode([
        "respuesta" => "ok",
        "comando" => basename(__FILE__, '.php'),
        "tipo_id" => "usuarios",
        "table" => "users",
        "campo" => "id",
        "lista_id" =>$lista_id,
        "data" => $data,
        "query" => $query,
        "TS" => Carbon::now()->format('Y-m-d h:i:s')
    ]);
}
?>

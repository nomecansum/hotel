<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\Console\Helper\ProgressBar;

$descripcion="Este es un comando de ejemplo que puede servir como plantilla para la creacion de nuevos comandos. En esta campo va la descripcion que el usuario verá cuando selecciona el comando. El objetivo de esta es explicar al usuario que hace el comando y ".
"como se puede parametrizar, intentando dar el mayor detalle posible a los valores que pueden tomar los distintos parametros";

$grupo="A";

$params='{
    "parametros":[
        {
            "label": "Parametro tipo boolean",
            "name": "mca_respetar_festivos",
            "tipo": "bool",
            "def": true,
            "required": false
        },
        {
            "label": "Parametro numerico",
            "name": "val_margen",
            "tipo": "num",
            "def": "15",
            "required": false
        },
        {
            "label": "Parametro de texto",
            "name": "val_texto",
            "tipo": "txt",
            "def": "dato",
            "required": false
        },
        {
            "label": "Parametro de fecha",
            "name": "fec_demo",
            "tipo": "fec",
            "def": "2020-01-01",
            "required": false
        },
        {
            "label": "Parametro con lista simple estatica",
            "name": "cod_motivo",
            "tipo": "list",
            "multiple": false,
            "list":  "Motivo1|Motivo2|Motivo3|Motivo4|Motivo5|Motivo6",
            "values": "1|2|3|4|5|6",
            "required": false
        },
        {
            "label": "TIPO PRODUCTO -> Parametro lista multiple proveniente de BDD",
            "name": "id_tipo_producto",
            "tipo": "list_db",
            "multiple": true,
            "sql": "select id_tipo_incidencia as id, des_tipo_incidencia as nombre from incidencias_tipos",
            "required": false
        },
        {
            "label": "ROL -> Parametro lista simple proveniente de BDD",
            "name": "id_rol",
            "tipo": "list_db",
            "multiple": false,
            "sql": "select id_pais as id, nom_pais as nombre from paises",
            "required": false
        },
        {
            "label": "ROL -> Parametro lista simple proveniente de BDD CON BUSQUEDA INTEGRADA",
            "name": "id_rol",
            "tipo": "list_db",
            "multiple": false,
            "sql": "select id_pais as id, nom_pais as nombre from paises",
            "required": false,
            "buscar": true
        },
        {
            "label": "Parametro con lista multiple estatica",
            "name": "cod_motivo",
            "tipo": "list",
            "multiple": true,
            "list": "Motivo1|Motivo2|Motivo3|Motivo4|Motivo5|Motivo6",
            "values": "1|2|3|4|5|6",
            "required": false
        },
        {
            "label": "Parametro con lista de tags de los clientes seleccionados",
            "name": "id_tag",
            "tipo": "tags",
            "required": false
        },
        {
            "label": "Parametro con lista de dispositivos de los clientes seleccionados",
            "name": "id_dispositivo",
            "tipo": "disp",
            "required": false
        },
        {
            "label": "Poniendo este parametro a true, indicaremos que necesariamente se debe seleccionar un cliente",
            "name": "control_cliente",
            "tipo": "cli",
            "required": true
        },
        {
            "label": "Parametro de mentira para que este relleno",
            "name": "void",
            "tipo": "void",
            "required": false
        }
    ]
}';

//Acciones recomendadas para este comando
$acciones_def='[
       {
        "accion": "enviar_accion.php",
        "iteracion": 1,
        "orden" : 1
       },
       {
        "accion": "notificar empleado.php",
        "iteracion": 1,
        "orden" : 2
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
            "desc": "Codigo de empleado"
        }
    ]
}';

$tipo_destino='id_puesto';  //Determina el tipo de entidad que busca para poder compararlo con el tipo de entidad que soporta la accion

function ejecutar($evento,$output){
    //aqui va el codigo que queramos ejecutar, puede ser una simple consulta a BDD o cualquier logica compleja que se necesite
    $parametros = json_decode(json_decode($evento->param_comando));
    //Log::debug('Parametros de busqueda '.$evento->param_comando);
    $param1=valor($parametros,"val_margen");
    $param2=valor($parametros,"val_texto");
    //El identificador principal siempre tiene que ser el campo id
    $query=[];

    //Al final se debe retornar un JSON con la lista de ID que se han obtenido de la consulta y sobre los que se actuará en las acciones
    return json_encode([
        "respuesta" => "ok",
        "comando" => basename(__FILE__, '.php'),
        "tipo_id" => "empleados",
        "table" => "cug_empleados",
        "campo" => "nom_empleado",
        "lista_id" => [1,2,3,4,5,6,7,8,9,0],
        "data" => $query,
        "TS" => Carbon::now()->format('Y-m-d h:i:s')
    ]);

}

?>

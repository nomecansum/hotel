<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use App\Models\users;
use App\Models\rondas;
use App\Models\puestos_ronda;
use App\Models\puestos;
use App\Models\limpiadores;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class limpiarPuestos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:limpiarPuestos {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea para marcar para limpiar los puestos indicados a unas horas concretas, con posibilidad de añadirlos a una ronda de limpieza';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */

    function escribelog_comando($tipo,$mensaje){
        Log::$tipo($mensaje);
        log_tarea($mensaje,$this->argument('id'),$tipo);   
        if($this->argument('origen')=='W')
        {
            
        }
    } 
    static function clientes(){
        if(is_array(clientes())){
            $clientes=implode(",",clientes());
        }   else {
            $clientes=implode(",",clientes()->ToArray());
        } 
        return $clientes;
    }
    
    static function params(){

        $params='{
            "parametros":[
                {
                    "label": "Horas (separadas por comas)",
                    "name": "horas",
                    "tipo": "txt",
                    "def": "",
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
                            `puestos_tipos`.`id_cliente` in('.limpiarPuestos::clientes().')  \n
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
                            `edificios`.`id_cliente` in('.limpiarPuestos::clientes().')  \n
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
                            `plantas`.`id_cliente` in('.limpiarPuestos::clientes().')  \n
                            ORDER BY 2", 
                    "required": false,
                    "buscar": true
                },
                {
                    "label": "Crear ronda de limpieza",
                    "name": "mca_crear_ronda_limpieza",
                    "tipo": "bool",
                    "def": false,
                    "required": false
                },
                {
                    "label": "Usuarios para la ronda de limpieza",
                    "name": "id_usuario_asignado",
                    "tipo": "list_db",
                    "multiple": true, 
                    "sql": "SELECT DISTINCT \n
                                `users`.`id` as id, \n
                                concat(\'[\',nom_cliente,\'] - \',`users`.`name`) as nombre  \n
                            FROM \n
                                `users` \n
                                INNER JOIN `clientes` ON (`users`.`id_cliente` = `clientes`.`id_cliente`) \n
                            WHERE \n
                            `users`.`id_cliente` in('.limpiarPuestos::clientes().')  \n
                            ORDER BY 2", 
                    "required": false,
                    "buscar": true
                },
                {
                    "label": "Poniendo este parametro a true, indicaremos que necesariamente se debe seleccionar un cliente",
                    "name": "control_cliente",
                    "tipo": "cli",
                    "required": true
                }
                
            ]
        }';
        return $params;
     }

    static function signature(){

        return "task:limpiarPuestos";
     }

    static function definicion(){

        return "Tarea para marcar para limpiar los puestos indicados a unas horas concretas, con posibilidad de añadirlos a una ronda de limpieza";
     }

     static function grupo(){

        return "A";
     }


    public function handle()
    {
        $this->escribelog_comando('info','Inicio de la tarea programada ['.$this->argument('id').']'.__CLASS__);
        //Sacamos los parametros de la tarea
        $tarea=tareas::find($this->argument('id'));
        $parametros=json_decode($tarea->val_parametros);
        $horas=valor($parametros,"horas");
        $id_edificio=valor($parametros,"id_edificio");
        $id_planta=valor($parametros,"id_planta");
        $id_tipo_puesto=valor($parametros,"id_tipo_puesto");
        $mca_crear_ronda_limpieza=valor($parametros,"mca_crear_ronda_limpieza");
        $id_usuario_asignado=valor($parametros,"id_usuario_asignado");
        try{
            $timezone=users::find($tarea->usu_audit)->val_timezone;
        } catch(\Throwable $e){
            $timezone='Europe/Madrid';
        }
        
        if($tarea->clientes!=null){
            $cliente=explode(",",$tarea->clientes)[0];
        } else {
            $cliente=users::find($tarea->usu_audit)->id_cliente;
        }

        //Primero vamos a ver las horas que hay que limpiar
        $horas=explode(",",$horas);
        //y ahora vamos a ver si la hora actual está dentro de las horas a limpiar
        $toca_limpieza=false;
        foreach($horas as $h){
            if($h==Carbon::now()->timezone($timezone)->format('H:i')){
                $toca_limpieza=true;
                break;
            }
        }
        if($toca_limpieza){
            //Ahora vamos a ver si hay que crear la ronda de limpieza
            if($mca_crear_ronda_limpieza){
                //Si hay que crear la ronda de limpieza, vamos a crearla
                $ronda=new rondas();
                $ronda->id_cliente=$cliente;
                $ronda->user_creado=config('app.id_usuario_tareas');
                $ronda->fec_ronda=Carbon::now()->timezone($timezone);
                $ronda->des_ronda="Ronda de limpieza automática de puestos ".Carbon::now()->timezone($timezone)->format('H:i');
                $ronda->tip_ronda='L';
                $ronda->save();
                $this->escribelog_comando('info','Se ha creado la ronda de limpieza ['.$ronda->id.']');
                foreach($id_usuario_asignado as $id_usuario){
                    $limpia=new limpiadores;
                    $limpia->id_ronda=$ronda->id_ronda;
                    $limpia->id_limpiador=$id_usuario;
                    $limpia->save();
                }
            }
            //Ahora vamos a ver si hay que limpiar los puestos
            $puestos=DB::table('puestos')
                ->where(function($q) use($id_edificio){
                    if ($id_edificio) {
                        $q->WhereIn('puestos.id_edificio',$id_edificio);
                    }
                })
                ->where(function($q) use($id_planta){
                    if ($id_planta) {
                        $q->whereIn('puestos.id_planta',$id_planta);
                    }
                })
                ->where(function($q) use($id_tipo_puesto){
                    if ($id_tipo_puesto) {
                        $q->whereIn('puestos.id_tipo_puesto',$id_tipo_puesto);
                    }
                })
                ->get();

            $this->escribelog_comando('info','Encontrados '.count($puestos).' puestos para limpiar');
            foreach($puestos as $p){
                $puesto_ronda=new puestos_ronda;
                $puesto_ronda->id_ronda=$ronda->id_ronda;
                $puesto_ronda->id_puesto=$p->id_puesto;
                $puesto_ronda->save();

                $puesto=puestos::find($p->id_puesto);
                $puesto->mca_estado=3;
            }
        }        

        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}

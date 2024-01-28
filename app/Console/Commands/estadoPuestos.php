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

class estadoPuestos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:estadoPuestos {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea para poner todos los puestos que cumplan un determinado criterio de uibicacios/estado a un estado concreto';

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
                    "buscar": true,
                    "width": 6
                },
                {
                    "label": "Estado del puesto",
                    "name": "id_estado",
                    "tipo": "list_db",
                    "multiple": true,
                    "sql": "SELECT DISTINCT \n
                                `estados_puestos`.`id_estado` as id, \n
                                `estados_puestos`.`des_estado` as nombre \n
                            FROM \n
                                `estados_puestos`",
                    "required": false,
                    "buscar": false,
                    "width": 4
                },
                {
                    "label": "Cambiar a estado",
                    "name": "id_estado_final",
                    "tipo": "list_db",
                    "multiple": false,
                    "sql": "SELECT DISTINCT \n
                                `estados_puestos`.`id_estado` as id, \n
                                `estados_puestos`.`des_estado` as nombre \n
                            FROM \n
                                `estados_puestos`",
                    "required": false,
                    "buscar": false,
                    "width": 2
                },
                {
                    "label": "Poniendo este parametro a true, indicaremos que necesariamente se debe seleccionar un cliente",
                    "name": "control_cliente",
                    "tipo": "cli",
                    "required": true,
                    "width": 2
                }
                
            ]
        }';
        return $params;
     }

    static function signature(){

        return "task:estadoPuestos";
     }

    static function definicion(){

        return "Tarea para poner todos los puestos que cumplan un determinado criterio de uibicacios/estado a un estado concreto";
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
        $id_edificio=valor($parametros,"id_edificio");
        $id_planta=valor($parametros,"id_planta");
        $id_tipo_puesto=valor($parametros,"id_tipo_puesto");
        $id_estado=valor($parametros,"id_estado");
        $id_estado_final=valor($parametros,"id_estado_final");
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
            ->when($id_estado, function($q) use($id_estado) {
                $q->where('puestos.id_estado',$id_estado);
            })
            ->get();
        $this->escribelog_comando('info','Encontrados '.count($puestos).' puestos');

        foreach($puestos as $puesto){
            $p=Puestos::find($puesto->id_puesto);
            $p->id_estado=$id_estado_final;
            $p->id_usuario_usando=null;
            $p->fec_ult_estado=Carbon::now();
            $p->save();
            $this->escribelog_comando('debug','Puesto '.$puesto->id_puesto.' cambiado a estado '.$id_estado_final);
            DB::table('log_cambios_estado')->insert([
                'id_puesto' => $puesto->id_puesto,
                'id_estado' => $id_estado_final,
                'fecha' => Carbon::now(),
                'id_user' => config('app.id_usuario_tareas')
            ]);
        }

        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}

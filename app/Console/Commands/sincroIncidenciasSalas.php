<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use App\Models\users;
use App\Models\puestos;
use App\Models\salas;
use App\Models\incidencias;
use App\Models\estados_incidencias;
use App\Models\incidencias_tipos;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\IncidenciasController;
use App\Http\Controllers\APIController;

class sincroIncidenciasSalas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:sincroIncidenciasSalas {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea para sincronizar los ID de incidencias entre salas y la plataforma';

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
            "parametros":[]
        }';
        return $params;
     }

    static function signature(){

        return "task:sincroIncidenciasSalas";
     }

    static function definicion(){

        return "Tarea para sincronizar los ID de incidencias entre salas y la plataforma";
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
        //Primero veamos si hay incidencias pendientes de crear
        $pendientes=DB::table('incidencias')
            ->join('salas','salas.id_puesto','incidencias.id_puesto')
            ->wherenull('incidencias.id_incidencia_salas')
            ->get();
        
        //Se envian a la API de Salas
        foreach($pendientes as $p){
            $sala=salas::where('id_puesto',$p->id_puesto)->first();
            $estado=estados_incidencias::find($p->id_estado);
            $tipo=incidencias_tipos::find($p->id_tipo_incidencia);
            $notas_admin=$p->txt_incidencia;
            $endpoint="add_or_set_incidencia_empresa";
            if($sala!=null && $sala->id_externo_salas!=null){
                $body=new \stdClass;
                $body->sala_id=$sala->id_externo_salas;
                $body->fecha=Carbon::parse($p->fec_apertura)->toISOString();
                $body->tipo_incidencia_id=$tipo->id_tipo_salas;
                $body->estado=$estado->id_estado_salas;
                $body->descripcion_adicional=$p->des_incidencia;
                $body->notas_admin=$notas_admin;
                $body->incidencia_id_puestos=$p->id_incidencia;
                $body=json_encode($body);
                log::debug($body);
                $response=APIController::enviar_request_salas('post',$endpoint,"",$body,$p->id_cliente);
                if(isset($response['status']) && $response['status']==200){
                    $resp=json_decode($response['body']);
                    $i=incidencias::find($p->id_incidencia);
                    $i->id_incidencia_salas=$resp->incidencia_sala_id;
                    $i->save();
                    Log::info("Respuesta OK de salas: ".$response['body']);
                } else {
                    Log::error("Error en el request a spotlinker salas");
                }
            } else { //El puesto no tiene sala asociada
                Log::error("No hay sala asociada para el puesto o la sala no esta asociada a una sala de spotlinker salas puesto ->".$p->id_puesto);
            }
        }

        $this->escribelog_comando('info','Iniciando proceso de sincronizacion de ID de incidencias');
        // Y ahora a ver si hay pares de ID pendientes de sincronizar
        $incidencias_sincro=DB::table('incidencias')
            ->join('salas','salas.id_puesto','incidencias.id_puesto')
            ->wherenotnull('incidencias.id_incidencia_salas')
            ->where('mca_sincronizada','N')
            ->get();

        //Por si vienen de distintos clientes
        $clientes=$incidencias_sincro->pluck('id_cliente')->unique()->toArray();
        foreach($clientes as $cl){
            $this->escribelog_comando('info','Sincronizando cliente '.$cl);
            $pendientes=[];
            foreach($incidencias_sincro->where('id_cliente',$cl) as $p){
                $obj=new \stdClass;
                $obj->incidencia_puestos_id=$p->id_incidencia;
                $obj->incidencia_sala_id=$p->id_incidencia_salas;
                $pendientes[]=$obj;
            }
            $endpoint="add_incidencias_id_puestos_pendientes";
            $body=json_encode($pendientes);
            log::debug($body);
            $response=APIController::enviar_request_salas('POST',$endpoint,"",$body,$cl);
            if(isset($response['status']) && $response['status']==200 && json_decode($response['body'])->cod_servidor==0){
                $resp=json_decode($response['body']);
                Log::info("Respuesta OK de salas: ".$response['body']);
                foreach($incidencias_sincro->where('id_cliente',$cl) as $p){
                    $i=incidencias::find($p->id_incidencia);
                    $i->mca_sincronizada='S';
                    $i->save();
                }
            } else {
                Log::error("Error en el request a spotlinker salas ".$response['body']);
            }
        }  
        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}

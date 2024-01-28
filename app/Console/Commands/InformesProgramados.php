<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\cucoWEB;
use App\Models\informes_programados;
use App\Jobs\GeneraInforme;
use Illuminate\Queue\Jobs\Job;
use Illuminate\Support\Facades\Queue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;




class InformesProgramados extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:InformesProgramados {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea para generar y enviar los informes programados añadiendolos a una cola';

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

    static function params(){
        $params='{
            "parametros":[
                {
                    "label": "Identificador de cola",
                    "name": "val_cola",
                    "tipo": "list",
                    "multiple": false,
                    "list": "Todos,Grandes,Medianos,Pequeños",
                    "values": "0,L,M,S",
                    "required": false
                }
            ]
        }';
        return $params;
     }

    static function signature(){

        return "task:InformesProgramados";
     }

    static function definicion(){

        return "Tarea para generar y enviar los informes programados, Recibe como parametro el identificador de la cola sobre el que va a añadir los trabajos de generacion de informes";
     }

     static function grupo(){

        return "A";
     }


    public function handle()
    {
        //Aumentamos el tiempo maximo de timeout, porque los informes pueden tardar
        ini_set('max_execution_time', 500);
        set_time_limit(500);

        //Y la memoria disponibie para ejecucion
        ini_set('memory_limit', '4095M');

        Log::info('Inicio de la tarea programada ['.$this->argument('id').']'.__CLASS__);
        //Sacamos los parametros de la tarea
        $tarea=tareas::find($this->argument('id'));
        ini_set('max_execution_time', $tarea->val_timeout);
        $parametros=json_decode($tarea->val_parametros);
        $val_cola=valor($parametros,"val_cola");
        //Ahora buscamos los informes que estan pendientes de ejecuccion
       Log::debug('Buscando informes programados');
        // $informes=DB::table('cug_informes_programados')->where('fec_prox_ejecucion','<=',Carbon::now())->dd();
        // dd($informes);
        $informes=informes_programados::where('fec_prox_ejecucion','<=',Carbon::now());
        Log::debug('Encontrados '.$informes->count().' Informes para ejecutar');
        if($tarea->clientes!=''){
            $informes->wherein('cod_cliente',explode(",",$tarea->clientes));
            Log::debug('Filtrados informes para clientes '.$tarea->clientes.' Quedan '.$informes->count().' Informes para ejecutar');
        }
        foreach($informes->get() as $inf){
            Log::info('Informe '.$inf->des_informe_programado);
            //Vamos a ver si el informe ya esta en la cola para no despacharlo otra vez
            $jobs=DB::table('jobs')->get();
            $esta=false;
            foreach($jobs as $job){
                $payload=json_decode($job->payload);
                $id_informe=unserialize($payload->data->command)->id_informe;
                if($id_informe==$inf->cod_informe_programado){
                    $esta=true;
                }
            }
            if (!$esta){
                GeneraInforme::dispatch($inf->cod_informe_programado)->onQueue('Informes'.$val_cola);
                Log::warning('Añadiendo informe '.$inf->des_informe_programado.' a la cola '.'Informes'.$val_cola);
            } else {
                Log::error('El informe '.$inf->cod_informe_programado.' ya estaba en la cola de proceso');
            }

           // Queue::pushOn('Informes'.$val_cola, new GeneraInforme($inf->cod_informe_programado));
        }


        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        Log::info('Fin de la tarea '.__CLASS__);

        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}

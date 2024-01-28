<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\cucoWEB;
use App\Models\evolucion;
use Illuminate\Support\Facades\Log;

class eventos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:eventos {id?} {origen=C} {--queue}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Comando que evalua la ejecucion de eventos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    static function params(){
        $params='{
            "parametros":[
                {
                    "label": "Grupo de ejecucion",
                    "name": "cod_grupo_ejecucion",
                    "tipo": "list",
                    "multiple": false,
                    "list": "Todos|grupo A|grupo B|grupo C|grupo D|grupo E|grupo F",
                    "values": "*|A|B|C|D|E|F",
                    "def": "*",
                    "required": false
                },
                {
                    "label": "Poniendo este parametro a true, indicaremos que se oculte el selector de cliente",
                    "name": "hide_cliente",
                    "tipo": "hidecli",
                    "required": true
                }
            ]
        }';
        return $params;
     }
     

     static function signature(){

        return "task:eventos";
     }

    static function definicion(){

        return "Esta tarea evalua y ejecuta los eventos programados";
     }

     static function grupo(){

        return "A";
     }

     static function log_evento($texto,$cod_regla,$tipo="info"){
        DB::table('eventos_log')->insert([
            'fec_log'=>Carbon::now(),
            'txt_log'=>substr($texto,0,5000),
            'cod_regla'=>$cod_regla,
            'tip_mensaje'=>$tipo
        ]);
        if($tipo=='success')
            $tipo='warning';
        Log::$tipo($texto);
     }

    function escribelog_comando($tipo,$mensaje){
        if($tipo=='success')
            $tipo='notice';
        Log::$tipo($mensaje);
        log_tarea($mensaje,$this->argument('id'),$tipo);
    } 

     public function scope(){
        return "events";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //Las posibilidaddes de log son:  error    warning  debug    info   critical   notice    alert

        
        //Sacamos los parametros de la tarea
        $this->escribelog_comando('info','Inicio de la tarea programada ['.$this->argument('id').']'.__CLASS__);
        try{
            $tarea=tareas::findorfail($this->argument('id'));
            ini_set('max_execution_time', $tarea->val_timeout);
        } catch(\Exception $e) {
            $this->escribelog_comando('error','No existe la tarea '.$this->argument('id'));
            exit();
        }
        $dowMap = array('Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom');

        try{
            $parametros=json_decode($tarea->val_parametros);
            $grupo=valor($parametros,"cod_grupo_ejecucion");
            $this->escribelog_comando('debug','Grupo '.$grupo);

            try{
                $clientes=$tarea->lista_clientes;
            } catch (\Throwable $e){
                $clientes="";
            }
             if($clientes!=''){
                 $clientes=explode(",",$clientes);
             } else {
                 $clientes=null;
             }

            //Primero vamos a obtener los eventos que tenemos que evaluar
            $eventos=DB::table('eventos_reglas')
                ->where(function($qr) use($grupo){
                    if($grupo!='*'){
                        $qr->where('cod_grupo',$grupo);
                    }
                })
                ->where('fec_inicio','<=',Carbon::now())
                ->where('fec_fin','>=',Carbon::now())
                ->where('mca_activa','S')
                // ->when($clientes, function($q) use($clientes){
                //     $q->wherein('cod_cliente',$clientes);
                // })
                ->where( function($q) {
                    $q->where('fec_prox_ejecucion','<',Carbon::now());
                    $q->orWhereNull('fec_prox_ejecucion');
                })
                ->get();
        } catch(\Throwable $e) {
            $this->escribelog_comando('error','Error al obtener los parametros de la tarea, edite la tarea revise los parametros y vuelva a guardarla '.$e->getMessage());
            $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
            exit();
        }
        

        foreach($eventos as $evento){
            $this->log_evento('['.$evento->cod_regla.'] -> Regla: '.$evento->nom_regla,$evento->cod_regla,'notice');
            $this->escribelog_comando('notice','['.$evento->cod_regla.'] -> Regla: '.$evento->nom_regla);
            
            //Siguiente paso, a ver si estamos dentro de las horas de programacion de la regla
            if(!isset($evento->timezone) || $evento->timezone==''){
                $evento->timezone="Europe/Madrid";
            }
            $hoy=Carbon::now()->setTimezone($evento->timezone)->format('Y-m-d');
            $hoy_hora=Carbon::now()->setTimezone($evento->timezone);
            $hora=Carbon::now()->setTimezone($evento->timezone)->format('H');
            $dia=Carbon::now()->setTimezone($evento->timezone)->dayOfWeek;
            if($dia==0) $dia=7; //DOmingo en ingles
            $this->log_evento('Dia: '.$dia." Hora: ".$hora,$evento->cod_regla,'notice');
            $programacion=decodeComplexJson(decodeComplexJson($evento->schedule));
            //Lo primero es comprobar si la preogramacion es del formato nuevo o del antiguo, se diferencia porque ta nueva es un objeto que tiene una propiedad llamada tipo_programacion. La vieja es un array con los dias de la semana y las horas por cada dia
            $toca_programacion=false;
            if(($programacion->tipo_programacion??'P')=='P'){
                //Tipo planificador o antigua. Si es nueva , tiene la misma info que la vieja pero en el nodo "dias"
                if(isset($programacion->tipo_programacion)){
                    $programacion=$programacion->dias;
                }
                foreach($programacion as $prog){
                    if($prog->num_dia==$dia){
                        foreach($prog->horas as $h){
                            if($h==$hora){
                                $toca_programacion=true;
                            }
                        }
                    }
                }
            } else { //Nueva Custom
                if(in_array($dia,$programacion->dias)){ //Si el dia esta en la programacion
                    if($hoy_hora->between(Carbon::parse($hoy.' '.$programacion->hora_inicio)->shiftTimezone($evento->timezone),Carbon::parse($hoy.' '.$programacion->hora_fin)->shiftTimezone($evento->timezone))){
                        $toca_programacion=true;
                    }
                }
            }
            
            if (!$toca_programacion){
                $this->log_evento("La regla no esta programada para ser ejecutada el día ".$dowMap[$dia-1]." en la hora ".$hoy_hora->format('H:i'),$evento->cod_regla,'error');
                continue;
            }
            $this->log_evento('Iniciando proceso de la regla dentro del horario de programacion',$evento->cod_regla);
            //Ahora leemos el fichero de la regla
            $this->log_evento('Comando :'.resource_path('views/events/comandos').'/'.$evento->nom_comando,$evento->cod_regla);
            $output=$this->output;
            try{
                include(resource_path('views/events/comandos').'/'.$evento->nom_comando);
                $resultado_json=$func_comando($evento,$output);
                $resultado=json_decode($resultado_json);
                try{
                    $campos=json_decode($campos);
                    $campos=$campos->campos;
                } catch(\Throwable $e){
                    $campos=[];
                }
                if(sizeof($resultado->lista_id)>0){
                    $this->log_evento('Comando ejecutado, '.count($resultado->lista_id).' ID a procesar: '.implode(",",$resultado->lista_id),$evento->cod_regla,'info');
                } else{
                    $this->log_evento('Comando ejecutado, no hay ID para procesar',$evento->cod_regla,'notice');
                }
                unset($func_comando);
                $this->log_evento("Resultado del comando: ".$resultado->respuesta,$evento->cod_regla,'notice');
            } catch(\Throwable $e){
                if(config('app.env')=="local"){
                    dump($e);
                }
                $this->log_evento('Comando :'.resource_path('views/events/comandos').'/'.$evento->nom_comando.', ERROR :'.mensaje_excepcion($e),$evento->cod_regla,'error');
                $resultado=json_decode(json_encode([
                    "respuesta" => "ERROR",
                    "comando" => $evento->nom_comando,
                    "tipo_id" => "void",
                    "table" => "void",
                    "campo" => "void",
                    "lista_id" =>  [],
                    "data" =>[],
                    "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ]));
            }
            


            //Sacamos la cuenta total de iteraciones para saber cuando acaba
            $max_iteracion=DB::table('eventos_acciones')->where('cod_regla',$evento->cod_regla)->max('val_iteracion');
            $this->log_evento('Iteraciones maximas de la regla: '.$max_iteracion,$evento->cod_regla,'debug');
            //Sacamos las acciones para la iteracion
            $acciones=DB::table('eventos_acciones')->where('cod_regla',$evento->cod_regla)->wherenotnull('nom_accion')->get();
            //Iteraciones de la regla
            $iteraciones=DB::table('eventos_acciones')->where('cod_regla',$evento->cod_regla)->wherenotnull('nom_accion')->pluck('val_iteracion')->unique()->toArray();
            //Y sacamos la iteracion en que que esta cada ID
            $evolucion=DB::table('eventos_evolucion_id')->where('cod_regla',$evento->cod_regla)->get();
            $ya_estan=$evolucion->pluck('id')->toArray();
            //ID que entran de nuevas
            $nuevos_id=array_diff($resultado->lista_id,$ya_estan);
            //Vamos a hacer las acciones iteracion por iteracion
            foreach($iteraciones as $iteracion){
                //Buscamos los ID que estan en esta iteracion
                $acciones_iteracion=$acciones->where('val_iteracion',$iteracion)->sortby('num_orden');
                $this->log_evento('Iteracion: '.$iteracion.' -> acciones: '.count($acciones_iteracion),$evento->cod_regla,'notice');
                if($iteracion==1){
                    //Si es la primera iteracion, los ID que entran de nuevas
                    $ids_para_la_iteracion=$nuevos_id;
                } else{
                    //Si no, los ID que ya estan en la iteracion anterior
                    $ids_para_la_iteracion=$evolucion->where('val_iteracion',$iteracion-1)->pluck('id')->toArray();
                }
                //ID sobre los que no hay que actuar porque estan en espera(descompresion :-)
                $lista_ids_en_espera=DB::table('eventos_noactuar')->where('cod_regla',$evento->cod_regla)->where('fecha','>',Carbon::now())->pluck('id')->unique()->toArray();
                //Los quitamos de la lista a procesar
                $ids_para_la_iteracion=array_diff($ids_para_la_iteracion,$lista_ids_en_espera);
                $this->log_evento('Descontados: '.count($lista_ids_en_espera).' en estado no molestar, quedan '.count( $ids_para_la_iteracion).' id para procesar',$evento->cod_regla,'notice');
                foreach($acciones_iteracion as $accion){
                    $acciones_no_ejecutar=[];
                    $lista_ids_procesar=array_values($ids_para_la_iteracion);
                    if(count($lista_ids_procesar)>0){
                        do{
                            $id=$lista_ids_procesar[0];
                            try{
                                if(!in_array($accion->cod_accion,$acciones_no_ejecutar)){  //En el caso que queramos que esa accion solo se ejecute una vez por iteracion (por ejemplo, enviar un email)
                                    $this->log_evento("ID: ".$id." | Iteracion: " .$iteracion." | Accion[".$accion->num_orden."]: ".$accion->nom_accion,$evento->cod_regla,'info');
                                    include(resource_path('views/events/acciones').'/'.$accion->nom_accion);
                                    //Ejecutamos la funcion principal de cada accion
                                    $result_accion=$func_accion($accion,$resultado,$campos,$id,$output);
                                    
                                    //Como norma general, la funcion de la accion devolvera null, en caso de devolver otra cosa será para evitar que se vuelva a ejecutar en esa iteracion (notificaciones) o porque se va a rellenar la lista de id (evaluar regla )
                                    if(isset($result_accion['no_ejecutar_mas']) && $result_accion['no_ejecutar_mas']==true){
                                        $acciones_no_ejecutar[]=$accion->cod_accion;
                                        $this->log_evento('SOLO UNA (1) ejecucion por iteracion',$evento->cod_regla,'warning');
                                    }
                                    //En este caso si en la accion se ha rellenado la lista de id, se añaden a la lista de id para la siguiente accion
                                    if(isset($result_accion['lista_id']) && count($result_accion['lista_id'])>0){
                                        $lista_ids_procesar=$result_accion['lista_id'];
                                        $ids_para_la_iteracion=$lista_ids_procesar;
                                        $resultado=$result_accion['resultado'];
                                        $campos=$result_accion['campos'];
                                        
                                    }
                                    //Se elimina la funcion por si hay mas acciones en la misma regla
                                    unset($func_accion);
                                }
                            } catch(\Throwable $e){
                                if(config('app.env')=="local"){
                                    dump($e);
                                }
                                $this->log_evento('Error al ejecutar la accion :'.$accion->nom_accion.', '.mensaje_excepcion($e),$evento->cod_regla,'error');
                            }
                            array_shift($lista_ids_procesar);
                        } while(count($lista_ids_procesar)>0);
                    } else {
                        $this->log_evento('No hay ID para la accion '.$accion->num_orden.' de la iteracion '.$iteracion,$evento->cod_regla,'warning');
                    }
                }
                foreach(array_values($ids_para_la_iteracion) as $id){
                    if($iteracion==1 && $max_iteracion>1){ //Estamos en la primera ya hay que insertar en la tabla de evolucion para ir progresandola
                        DB::table('eventos_evolucion_id')->insert([
                            "cod_regla"=>$evento->cod_regla,
                            "val_iteracion"=>$iteracion,
                            "id"=>$id,
                            "fecha"=>Carbon::now()
                        ]);
                    } else if($iteracion>=$max_iteracion){ //Ha llegado al tope, la borramos de la tabla para en la siguiente volver a empezar
                        $this->log_evento("Superado el maximo de iteraciones, borrando evolucion",$evento->cod_regla,'debug');
                        DB::table('eventos_evolucion_id')->where('cod_regla',$evento->cod_regla)->where('id',(string) $id)->delete();
                        //Ahora si la regla tiene un no molestar en X horas lo ponemos
                        if(isset($evento->nomolestar)){
                            $this->log_evento('Añadido no molestar para '.$id.' hasta dentro de '.$evento->nomolestar.' '.$evento->tip_nomolestar,$evento->cod_regla,'debug');
                            DB::table('eventos_noactuar')->where('cod_regla',$evento->cod_regla)->where('id',(string) $id)->delete();
                            //Dependiendo de la unidad de tiempo de nomolestar
                            if($evento->tip_nomolestar=='H'){
                                $fecha_noactuar=Carbon::now()->addHours($evento->nomolestar);
                            } else if($evento->tip_nomolestar=='D'){
                                $fecha_noactuar=Carbon::now()->addDays($evento->nomolestar);
                            } else if($evento->tip_nomolestar=='M'){
                                $fecha_noactuar=Carbon::now()->addMonths($evento->nomolestar);
                            } else if($evento->tip_nomolestar=='Y'){
                                $fecha_noactuar=Carbon::now()->addYears($evento->nomolestar);
                            }
                            // 
                            if($evento->nomolestar>0 && config('app.debug_eventos')==false){
                                DB::table('eventos_noactuar')->insert([
                                    "cod_regla"=>$evento->cod_regla,
                                    "id"=>$id,
                                    "fecha"=>$fecha_noactuar,
                                ]);
                            }
                        }
                    } else { //Estamos a mitad del fregao, aumentamos el numero de iteracion y listo
                        DB::table('eventos_evolucion_id')->where('cod_regla',$evento->cod_regla)->where('id',$id)->update([
                            "val_iteracion"=>$iteracion,
                        ]);
                    }
                }
            }
            $this->log_evento('Finalizado el procesado de la regla '.$evento->nom_regla,$evento->cod_regla,'info');
            $this->escribelog_comando('info','Finalizado el procesado de la regla '.$evento->nom_regla);
            
            //Actualizamos la fecha de ultima y proxima ejecucion ejecucion de la regla
            DB::table('eventos_reglas')->where('cod_regla',$evento->cod_regla)->update([
                'fec_ult_ejecucion'=>Carbon::now(),
                'fec_prox_ejecucion'=>config('app.debug_eventos')==true?Carbon::now():Carbon::now()->addMinutes($evento->intervalo)
                ]);
            $this->log_evento('Proxima ejecucion establecida para  '.Carbon::now()->addMinutes($evento->intervalo)->toString(),$evento->cod_regla,'notice');
        }
        if($eventos->count()==0){
            $this->escribelog_comando('info','No hay eventos para evaluar');
        }
        //Actualiza la fechad de ultima ejecucion de la tarea
        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
    }
}


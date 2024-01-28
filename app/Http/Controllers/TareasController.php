<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use DB;
use Carbon\Carbon;
use App\Models\tareas;
use Illuminate\Support\Facades\Auth;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel;
use Cron\CronExpression;
use stdClass;
use Illuminate\Foundation\Providers\ArtisanServiceProvider;
use Artisan;

class TareasController extends Controller
{

	protected static function fixupCommand($command)
    {
		$parts = explode(' ', $command);

        if (count($parts) > 2 && $parts[1] === '"artisan"') {
            array_shift($parts);
		}
		//dd($parts);
        return $parts;
	}

    protected static function previousRunDate($expression)
    {
        return Carbon::instance(
            CronExpression::factory($expression)->getPreviousRunDate()
        );
    }

	//
    public function index()
    {
		app()->make(\Illuminate\Contracts\Console\Kernel::class);
		$schedule = app()->make(\Illuminate\Console\Scheduling\Schedule::class);

		$crons= [];
		//$events = collect($schedule->events())->where('output','<>','NUL');
		$events = collect($schedule->events())->filter(function ($item) {
			// replace stristr with your choice of matching function
			return false === stripos(strtoupper($item->output), 'NUL');
        });

		foreach($events as $ev)
		{
			try{
                $cron= new stdClass();
                //Cuando se saca la lista de comandos, en linux tienen una posicion mas
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
                     //Sistema basado en windows
                    $indice=1;
                } else { //Sistema basado en Linux
                    $indice=2;
                }
                    $cron->comando=static::fixupCommand($ev->command)[$indice];
                    $cron->id_tarea=static::fixupCommand($ev->command)[$indice+1];
                    $cron->prox=$ev->nextRunDate();
                    $cron->last=static::previousRunDate($ev->expression);
                    $cron->cron=$ev->expression;
                    $tarea=tareas::find($cron->id_tarea);
                    $cron->real_last=(isset($tarea->fec_ult_ejecucion)?$tarea->fec_ult_ejecucion:null);
                    $cron->log=(isset($tarea->txt_resultado)?$tarea->txt_resultado:null);
                    $crons[]=$cron;
				} catch(\Exception $e){

			}
		}

		$queues= [];
		//$events = collect($schedule->events())->where('output','NUL');
		$events = collect($schedule->events())->filter(function ($item) {
			// replace stristr with your choice of matching function
			return false !== stripos(strtoupper($item->output), 'NUL');
		});
		foreach($events as $ev)
		{
			try{

				$cron= new stdClass();
				$cron->comando=static::fixupCommand($ev->command)[1];
                if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'){
                        //Sistema basado en windows
                    $indice=6;
                } else { //Sistema basado en Linux
                    $indice=7;
                }
				if(count(static::fixupCommand($ev->command))>2){
					$cron->comando.=static::fixupCommand($ev->command)[$indice];
				}

				$cron->id_tarea=null;
				$cron->prox=$ev->nextRunDate();
				$cron->last=static::previousRunDate($ev->expression);
				$cron->cron=$ev->expression;
				$tarea=tareas::find($cron->id_tarea);
				$cron->real_last=(isset($tarea->fec_ult_ejecucion)?$tarea->fec_ult_ejecucion:null);
				$cron->log=(isset($tarea->txt_resultado)?$tarea->txt_resultado:null);
				if(count(static::fixupCommand($ev->command))>2){
					$cron->queue_id=str_replace(",","",str_replace("--queue=","",static::fixupCommand($ev->command)[$indice]));
					$cron->queue=str_replace("--queue=","",static::fixupCommand($ev->command)[$indice]);
				}
				$queues[]=$cron;
				} catch(\Exception $e){
                    // dd($e);
                    // dd(static::fixupCommand($ev->command));

			}
		}
		$tareas=tareas::get();
		return view('tasks.index',compact('tareas','crons','queues'));
	}

    public function create()
    {
    	return view('tasks.create');
	}
	//For occasions when you need to access the list of scheduled events programmatically \Hmazter\LaravelScheduleList\ScheduleList::all exists that will return all the scheduled events as an array of ScheduleEvent.
	//kernel::schedule->command($controller->signature());
	//$tarea=$schedule->command($controller->signature());

	function detalle_tarea($id){

		$log=DB::table('tareas_programadas_log')
			->where('cod_tarea',$id)
			->where('fec_log','>',Carbon::now()->subDays(7))
			->get();

		$patron=DB::select("select
											date(fec_log) as fecha,
											date_format(fec_log,'%H') as hora,
											count(cod_log) as cuenta
									from tareas_programadas_log
									where cod_tarea=".$id."
										and txt_log like 'Inicio%'
										and fec_log>date_sub(now(), interval 7 day)
									group by
										date(fec_log),
											date_format(fec_log,'%H')");
		$patron=Collect($patron);
		$fechas=$patron->pluck('fecha')->unique()->sortby('fecha')->toArray();
		return view('tasks.detalle_tarea',compact('log','id','patron','fechas'));

	}

	function ver_log_tarea($id,$fecha,$hora){

		$log=DB::table('tareas_programadas_log')
			->where('cod_tarea',$id)
			->where('fec_log','>',Carbon::parse($fecha))
			->whereraw("date_format(fec_log,'%H')=".$hora)
			->get();
		return view('tasks.log_tarea',compact('log'));

	}

	public function ver_cola($id){
		$colas=explode(",",$id);
		$jobs=DB::table('jobs')->wherein('queue',$colas)->get();
		$failed=DB::table('failed_jobs')->wherein('queue',$colas)->get();
		return view('tasks.detalle_cola',compact('jobs','failed'));
	}

	public function validar_request($r){
		$this->validate($r,[
			'des_tarea' => 'required',
			'comando' => 'required',
			'val_intervalo' => 'required',
		]);
	}

	public function procesar_parametros($r){
		//Primero vamos a recuperar el comando
		$className = 'App\\Console\\Commands\\' . str_replace(".php","",$r->comando);
		$controller =  new $className;

		$JSON_param=decodeComplexJson($controller->params());
		if(isset($JSON_param)){
			$parametros=$JSON_param->parametros;//Esto es porque el JSON lleva un nodo que se llama parametros
		} else return [];
		//Esto es porque el JSON lleva un nodo que se llama parametros
		//Ahora sustituimos en el JSON de parametros los valores que se han seleccionado
		//Buscamos en el request el parametro con el nombre en cuestion y si esta, guardamos su valor

		foreach($parametros as $p){
			$nombre=$p->name;
			if(isset($r->$nombre)){
				$p->value=$r->$nombre;
			} else {
				if($p->tipo=='bool')
					{
						$p->value=false;
					}
			}
		}
		//Ahora lo convertimos en JSON para guardarlo
		$parametros=json_encode($parametros);
		return $parametros;
	}

	public function getsignature($r){
		//Primero vamos a recuperar el comando
		$className = 'App\\Console\\Commands\\' . str_replace(".php","",$r->comando);
		$controller =  new $className;
		return $controller->signature();
	}

	public function validar_intervalo($r){
		//Validacion de datos
		switch ($r->val_intervalo){
			case "hourlyAt":
				if(!isset($r->det_minuto)){
					return [
						"title" => "ERROR",
						"error" => trans('tareas.debe_indicar_minuto'),
					];
				}
			break;

			case "dailyAt":
				if(!isset($r->det_horaminuto)){
					return [
						"title" => "ERROR",
						"error" => trans('tareas.debe_indicar_hora_minuto'),
						"TS" => Carbon::now()->format('Y-m-d h:i:s')
						];
				}
			break;

			case "weeklyOn":
				if(!isset($r->det_diasemana) || !isset($r->det_horaminuto)){
					return [
						"title" => "ERROR",
						"error" => trans('tareas.debe_indicar_dia_semana'),
						"TS" => Carbon::now()->format('Y-m-d h:i:s')
						];
				}
			break;

			case "monthlyOn":
				if(!isset($r->det_diasemana) || !isset($r->det_diames)){
					return[
						"title" => "ERROR",
						"error" => trans('tareas.debe_indicar_dia_mes'),
						"TS" => Carbon::now()->format('Y-m-d h:i:s')
						];
				}
			break;
		}
	}

	public function poner_datos_tarea($r,$tarea,$parametros){

		//dd(Carbon::parse($r->hora_inicio));
		$dias_semana="";
		if(is_array($r->dias_semana)){
			$dias_semana=implode(",",$r->dias_semana);
		} else {
			$dias_semana="alldays";
		}

		if (isset($r->clientes)){
			$r->clientes=implode(",",$r->clientes);
		} else {
			$r->clientes="";
		}

		$tarea->des_tarea=$r->des_tarea;
		$tarea->tip_comando=$r->tip_comando;
		$tarea->val_intervalo=$r->val_intervalo;
		$tarea->det_minuto=$r->det_minuto;
		$tarea->det_diasemana=$r->det_diasemana;
		$tarea->det_diames=$r->det_diames;
		$tarea->clientes=$r->clientes;
		$tarea->dias_semana=$dias_semana;
		if(isset($r->det_horaminuto))
			$tarea->det_horaminuto=Carbon::parse($r->det_horaminuto);
		$tarea->hora_inicio=Carbon::parse($r->hora_inicio);
		$tarea->hora_fin=Carbon::parse($r->hora_fin);
		$tarea->nom_comando=$r->comando;
		$tarea->val_timeout=$r->val_timeout;
		$tarea->val_icono=$r->val_icono_tarea;
		$tarea->val_color=$r->val_color_tarea;
		$tarea->val_parametros=$parametros;
		$tarea->signature=$this->getsignature($r);

	}

    public function save(Request $r)
    {
		$this->validar_request($r);
		try{
			$parametros=$this->procesar_parametros($r);

			$this->validar_intervalo($r);

			$tarea= new tareas;
			$this->poner_datos_tarea($r,$tarea,$parametros);
			$tarea->usu_audit=Auth::user()->id;
			$tarea->save();
			savebitacora(trans('tareas.tarea')." ".$r->des_tarea." ".trans('tareas.added'),"Tareas","save","OK");
			return [
				'message' => trans('tareas.tarea')." ".$r->des_tarea." ".trans('tareas.added'),
				'url' => url('tasks')
			];
			
			} catch(\Exception $e){
				return [
					'title' => trans('tareas.tareas_programadas'),
					'error' => trans('tareas.error_creando_tarea')." ".$r->des_tarea." ".mensaje_excepcion($e),
					'url' => url('tasks')
				];
		}
	}

	public function edit($id)
	{
		$t = DB::table('tareas_programadas')->where('COD_TAREA',$id)->first();
		return view('tasks.create',compact('t'));
	}

	public function update(Request $r, $id)
	{
		$this->validar_request($r);

			$parametros=$this->procesar_parametros($r);

			$this->validar_intervalo($r);

			$tarea= tareas::find($id);
			$this->poner_datos_tarea($r,$tarea,$parametros);
			$tarea->usu_audit=Auth::user()->id;
			$tarea->save();

			savebitacora(trans('tareas.tarea')." ".$r->des_tarea." modificada","Tareas","save","OK");
			return [
				'message' => trans('tareas.tarea')." ".$r->des_tarea." ".trans('tareas.modificada'),
				'url' => url('tasks')
			];
			try{} catch(\Exception $e){
				return [
					'title' => trans('tareas.tareas_programadas'),
					'error' => trans('tareas.error_creando_tarea')." ".$r->des_tarea." ".mensaje_excepcion($e),
					'url' => url('tasks')
				];
		}
	}

	public function delete($id)
	{
		$tarea=tareas::find($id);
		DB::table('tareas_programadas')->where('COD_TAREA',$id)->delete();
		savebitacora(trans('tareas.tarea')." ".$tarea->des_tarea." borrada","Tareas","delete","OK");
		return back();
	}

	public function param_comando(Request $r,$id){
        try{
            $path = app_path() . '/Console/Commands/';
            $fic_comando=$path.'/'.$r->comando;
            $className = 'App\\Console\\Commands\\' . str_replace(".php","",$r->comando);
            $controller =  new $className;
            $parametros=[];
			$parametros_tarea=[];
			$tareas=null;
			$descripcion=$controller->definicion();
			if (file_exists($fic_comando)){
				$parametros=decodeComplexJson($controller->params());
				if($parametros!=""){
					$parametros=$parametros->parametros;//Esto es porque el JSON lleva un nodo que se llama parametros
				}
			} else{
				return response()->json([
					"response" => "ERROR",
					"error" => "No existe el comando ".$r->comando,
					"TS" => Carbon::now()->format('Y-m-d h:i:s')
					],400)->throwResponse();
			}
            if ($id!=0){
				//Regla existente, recuperamos de la bdd la info y la pasamos a la vista
                $tareas=tareas::find($id);
                if($tareas->val_parametros!=[] && $tareas->nom_comando==$r->comando){
                    $parametros_tarea=json_decode($tareas->val_parametros);
                }
				foreach($parametros_tarea as $p_t){
					foreach($parametros as $p){
						if(isset($p_t->value) && $p->name==$p_t->name){
							$p->value=$p_t->value;
						}
					}
				}
                
            }
			return view('tasks.param_comando',compact('parametros','descripcion','tareas','id'));

            } catch(\Exception $e){
            return response()->json([
                "response" => "ERROR",
                "error" => trans('tareas.formato_comando_incorrecto')." ".$r->comando." ".mensaje_excepcion($e),
                "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ],400)->throwResponse();
        }
    }

    public function run_tarea($id=0,$comando=null,$params=[]){

        //Primero buscamos la tarea
        if($id!=0){
            $tarea=tareas::findorfail($id);
        } else if (isset($comando)) {
            $tarea=tareas::where('nom_comando',$comando.'.php');
        }
        if(!isset($tarea)){
            return [
                'title' => trans('tareas.tareas_programadas'),
                'error' => trans('tareas.la_tarea_no_existe')." [".$id." - ".$comando."]",
            ];
        }
		//Si nos han pasado paramentros en la llamada se los pasamos a la tarea, si no, cogera los que lleve por defecto
		savebitacora("Ejecucion manual de la tarea ".$tarea->des_tarea,"Tareas","run_tarea","OK");
        Artisan::call("task:scaffold", ['name' => $request['name'], '--fieldsFile' => 'public/Product.json']);

        //artisan task:generaxmlactualidad 10 --tag=74,75,76,77,78,79,80,81,82,83,84 --disp=1000007,1002064,1000001,1000008,1000009,1000150,1000011,1000014,1000016,1000024,1000040,1000049,1000059,1000026,1000025,1000027,1000028
	}
	
	public function ejecutar_tarea_web($id){
		try{
			if(!Auth::check()){
				Auth::loginUsingId(14);
			}
			$ahora=Carbon::now();
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$tarea=DB::table('tareas_programadas')->where('cod_tarea',$id)->first();
			log_tarea(trans('tareas.inicio_de_la_tarea')." [".$tarea->cod_tarea."] ".$tarea->des_tarea,$tarea->cod_tarea);
			log_tarea(trans('tareas.ejecucion_manual_tarea')." [".$tarea->cod_tarea."]".$tarea->des_tarea." ".trans('general.usuario')." ".Auth::user()->nom_usuario,$tarea->cod_tarea);
			Artisan::call($tarea->signature,['id'=>$id,'origen'=>'W']);
			Log_tarea(trans('tareas.fin_de_la_tarea')." [".$tarea->cod_tarea."] ".$tarea->des_tarea,$tarea->cod_tarea);
			savebitacora("Ejecucion manual de la tarea ".$tarea->des_tarea,"Tareas","ejecutar_tarea_web","OK");
			$logs=DB::table('tareas_programadas_log')->where('cod_tarea',$id)->where('fec_log','>=',$ahora)->get();
				$salida=$logs->map(function($item,$key){
					return '['.$item->fec_log.']->'.$item->txt_log.' | '.chr(13);
				});
				return [
					'title' =>  trans('tareas.tareas_programadas'),
					'message' => trans('tareas.tarea').' '.$tarea->des_tarea." ".trans('tareas.ejecutada'),
					'log' => $salida
				];
		} catch (\Exception $e){
			return [
				'title' =>  trans('tareas.tareas_programadas'),
				'error' => "ERROR: ".trans('tareas.error_ejecutando_la_tarea').' '.$tarea->des_tarea." ".mensaje_excepcion($e),
				//'url' => url('tasks')
			];
		}
		
	}

	public function log_tarea_web($id,$fecha){
        $tarea=DB::table('tareas_programadas')->where('cod_tarea',$id)->first();
        $log=DB::table('tareas_programadas_log')
            ->where('cod_tarea',$id)
            ->where('fec_log','>=',$fecha)
            ->get();     
        return view('resources.vista_log',compact('tarea','log')); 
    }
}

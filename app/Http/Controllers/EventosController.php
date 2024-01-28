<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Carbon\Carbon;
use App\Models\reglas;
use App\Models\acciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Redirect;

class EventosController extends Controller
{
    public function list(){
        $eventos=DB::table('eventos_reglas')
        // ->join('eventos_acciones','eventos_acciones.cod_regla','eventos_reglas.cod_regla')
        ->select('eventos_reglas.cod_regla','eventos_reglas.nom_regla','eventos_reglas.nom_comando','eventos_reglas.fec_inicio','eventos_reglas.fec_fin','eventos_reglas.fec_ult_ejecucion','eventos_reglas.fec_prox_ejecucion','eventos_reglas.mca_activa','eventos_reglas.clientes')
        ->selectraw("(select count(cod_accion) from eventos_acciones where cod_regla=eventos_reglas.cod_regla) as acciones")
        ->selectraw("(select count(distinct(val_iteracion)) from eventos_acciones where cod_regla=eventos_reglas.cod_regla) as iteraciones")
        //->selectraw("(select group_concat(nombre_cliente order by nombre_cliente separator '#') from clientes where id_cliente in (eventos_reglas.clientes)) as list_clientes")
        ->where(function($q){
            if (!fullAccess()) {
                $q->wherein('eventos_reglas.cod_cliente',clientes());
            }
            if (session('cod_cliente')) {
                $q->orWhere('eventos_reglas.cod_cliente',session('CL')['id_cliente']);
            }
        })
        ->groupby('eventos_reglas.cod_regla','eventos_reglas.nom_regla','eventos_reglas.nom_comando','eventos_reglas.fec_inicio','eventos_reglas.fec_fin','eventos_reglas.fec_ult_ejecucion','eventos_reglas.fec_prox_ejecucion','eventos_reglas.mca_activa','eventos_reglas.clientes')
        ->get();

        return view('events.index',compact('eventos'));
    }

    public function new(){

        return view('events.nueva_regla');
    }

    public function param_comando(Request $r){
        try{
            //Leemos los datos genericos del comadno
            $path = resource_path('views/events/comandos');
            $fic_comando=$path.'/'.$r->comando;
            include_once($fic_comando);
            $parametros=decodeComplexJson($params);
            $parametros=$parametros->parametros;//Esto es porque el JSON lleva un nodo que se llama parametros
            if (isset($r->cod_regla)){
                //Regla existente, recuperamos de la bdd la info y la pasamos a la vista
                $id=$r->cod_regla;
                $reglas=reglas::find($id);
                $parametros=json_decode($reglas->param_comando);
                return view('events.param_comando',compact('parametros','descripcion','reglas','id'));
            } else {
                //Regla nueva, leemos fichero de comando y pasamos los parametros
                if (file_exists($fic_comando)){
                    if (!$parametros)
                    {
                        return [
                            "response" => "ERROR",
                            "error" => "JSON de parametros de comando invalido: ".$r->comando,
                            "TS" => Carbon::now()->format('Y-m-d h:i:s')
                            ];
                    }
                    return view('events.param_comando',compact('parametros','descripcion'));
                } else{

                    return [
                        "response" => "ERROR",
                        "error" => "No existe el comando ".$r->comando,
                        "TS" => Carbon::now()->format('Y-m-d h:i:s')
                        ];
                }
            }

            } catch(\Exception $e){
            return [
                "response" => "ERROR",
                "error" => "El formato del comando no es correcto ".$r->comando." ".mensaje_excepcion($e),
                "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ];
        }
    }

    function cambiar_accion(Request $r){
        //DAtos de la regla
        $regla=$r->cod_regla;
        $reg=DB::table('eventos_reglas')->where('cod_regla',$regla)->first();
        $path = resource_path('views/events/comandos');
        $fic_comando=$path.'/'.$reg->nom_comando;
        include_once($fic_comando);
        $campos=json_decode($campos);
        $campos=$campos->campos;
        if(!isset($campos_notificaciones)){
            $campos_notificaciones=true;
        }
        
        //Datos de la accion
        $accion=acciones::find($r->id_accion);
        $accion->nom_accion=$r->accion;
        $accion->save();


        $path = resource_path('views/events/acciones');
        $fic_comando=$path.'/'.$r->accion;
        if (file_exists($fic_comando)){
            include_once($fic_comando);
            $parametros=decodeComplexJson($params);
            $parametros=$parametros->parametros;//Esto es porque el JSON lleva un nodo que se llama parametros
            if (!$parametros)
            {
                return response()->json([
                    "response" => "ERROR",
                    "error" => "JSON de parametros de comando invalido: ".$r->comando,
                    "TS" => Carbon::now()->format('Y-m-d h:i:s')
                    ],400)->throwResponse();
            }
            return view('events.param_accion',compact('regla','accion','parametros','descripcion','campos','campos_notificaciones'));
        } else{

            return [
                "response" => "ERROR",
                "error" => "No existe el comando ".$r->comando,
                "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ];
        }

    }

    public function param_accion(Request $r,$regla,$accion){

        //Leemos los datos del comando para sacar los campos de notificaciones
        $reg=DB::table('eventos_reglas')->where('cod_regla',$regla)->first();
        $path = resource_path('views/events/comandos');
        $fic_comando=$path.'/'.$reg->nom_comando;
        include_once($fic_comando);
        $campos=json_decode($campos);
        $campos=$campos->campos;
        if(!isset($campos_notificaciones)){
            $campos_notificaciones=true;
        }
        $tipo_destino_comando=$tipo_destino??"";
        //Primero vamos a ver si la accion es una nueva (esta vacia) o tiene datos, para ello, hay que ver si tiene fichero de accion
        $accion=acciones::find($accion);
        $descripcion="";
        $path = resource_path('views/events/acciones');
        if(isset($accion->nom_accion)&&file_exists($path.'/'.$accion->nom_accion)){
            //Esta en blanco
            $fic_comando=$path.'/'.$accion->nom_accion;
            include_once($fic_comando);
        }
        $tipo_destino_accion=$tipo_destino??"";
        if(($accion->param_accion)!=null){
            $parametros=json_decode($accion->param_accion);
        } else {
            if(isset($params)){
                //Esta en blanco
                $parametros=decodeComplexJson($params);
                $parametros=$parametros->parametros;//Esto es porque el JSON lleva un nodo que se llama parametros
            } else {
                $parametros=[];
            }
        }
        return view('events.param_accion',compact('regla','accion','parametros','descripcion','campos','campos_notificaciones','tipo_destino_comando','tipo_destino_accion'));
    }

    public function acciones_save(Request $r){
        try{
            //Primero vamos a recuperar la accion
            $regla=$r->cod_regla;
            $path = resource_path('views/events/acciones');
            $fic_comando=$path.'/'.$r->accion;
            include_once($fic_comando);
            $JSON_param=decodeComplexJson($params);
            $parametros=$JSON_param->parametros;//Esto es porque el JSON lleva un nodo que se llama parametros
            //Ahora sustituimos en el JSON de parametros los valores que se han seleccionado
            //Buscamos en el request el parametro con el nombre en cuestion y si esta, guardamos su valor
            foreach($parametros as $p){
                $nombre=$p->name;
                if(isset($r->$nombre)){
                    $p->value=$r->$nombre;
                }
            }
            //Ahora lo convertimos en JSON para guardarlo
            $parametros=json_encode($parametros);

        } catch(\Throwable $e){
            return [
                "response" => "ERROR",
                "error" => "Error al procesar los datos de la accion ".$r->accion." ".mensaje_excepcion($e),
                "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ];
        }

        //Paso 2, a guardar en la BDD
        try{
                $operacion="update";
                $accion=acciones::find($r->cod_accion);
                $accion->param_accion=$parametros;
                $accion->val_iteracion=$r->val_iteracion;
                $accion->num_orden=$r->num_orden;
                $accion->nom_accion=$r->accion;
                $accion->val_icono=$icono??null;
                $accion->save();

            return [
                "response" => "Accion creada",
                "id"=>$accion->cod_regla,
                "message" => "Accion ".str_replace(".php","",str_replace("_"," ",$r->accion))." guardada",
                "TS" => Carbon::now()->format('Y-m-d h:i:s'),
                "url"=> "reload_acciones()",
                ];
        } catch(\Throwable $e){
            return [
                "response" => "ERROR",
                "error" => "Error al guardar la regla en BDD [".$operacion."]".str_replace(".php","",str_replace("_"," ",$r->accion))." ".mensaje_excepcion($e),
                "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ];
        }
    }

    public function nueva(Request $r, $regla,$tipo){
        $id=$regla;
        if ($tipo=='accion')
        {
            $acciones=acciones::where('cod_regla',$id)->orderby('val_iteracion','desc')->orderby('num_orden','desc')->first();
            if(isset($acciones))  {
                $cual_toca=$acciones->num_orden+1;
                $it=$acciones->val_iteracion;
            } else {
                $cual_toca=1;
                $it=1;
            }

        }

        if ($tipo=='iteracion')
        {
            $acciones=acciones::where('cod_regla',$id)->orderby('val_iteracion','desc')->orderby('num_orden','desc')->first();
            if(isset($acciones))  {
                $cual_toca=1;
                $it=$acciones->val_iteracion+1;
            } else {
                $cual_toca=1;
                $it=1;
            }
            if ($it>5){
                return response()->json([
                    "response" => "ERROR",
                    "error" => "El maximo es de 5 iteraciones",
                    "TS" => Carbon::now()->format('Y-m-d h:i:s')
                    ],400)->throwResponse();
            }
        }

        //Añadimos la accion vacia
        $nueva_accion=new acciones();
        $nueva_accion->cod_regla=$id;
        $nueva_accion->num_orden=$cual_toca;
        $nueva_accion->val_iteracion=$it;
        $nueva_accion->save();
        //Ahora refrescamos el listado para pasarselo a la vista y que aparezca el cuadradico nuevo
        $acciones=acciones::where('cod_regla',$id)->get();
        return view('events.acciones',compact('id','acciones','r'));

    }

    public function calendario($id){
        $dowMap = array('Lun', 'Mar', 'Mie', 'Jue', 'Vie', 'Sab', 'Dom');
        if($id!=0){
            $regla=reglas::find($id);
            $sched=json_decode($regla->schedule);
            return view('events.program_regla',compact('id','sched','dowMap','regla'));
        } else {
            return view('events.program_regla',compact('id','dowMap'));
        }
    }

    public function acciones(Request $r, $id){
        $acciones=acciones::where('cod_regla',$id)->get();
        return view('events.acciones',compact('id','acciones','r'));
    }

    public function delete_accion(Request $r, $regla,$accion,$iteracion){
        if($accion==-1){
            //Borrar la iteracion entera
            $acciones=acciones::where('cod_regla',$regla)->where('val_iteracion',$iteracion)->delete();
            //Vamos a ver si esta en medio, porque a las demas hay que renumerarlas para que no queden huecos
            DB::table('eventos_acciones')->where('val_iteracion','>',$iteracion)->decrement('val_iteracion');
        } else{
            $acciones=acciones::where('cod_regla',$regla)->where('val_iteracion',$iteracion)->where('cod_accion',$accion)->delete();
        }
        $id=$regla;
        $acciones=acciones::where('cod_regla',$regla)->get();
        return view('events.acciones',compact('id','acciones','r'));

    }

    public function acciones_reindex(Request $r, $regla){
        
        $acciones=acciones::where('cod_regla',$regla)->get();
        $data=collect(json_decode($r->data));
        dump($data);
        foreach($data as $iteracion){
            $n=1;
            foreach($iteracion->data as $accion){
                if($accion!=""){                  
                    DB::table('eventos_acciones')->where('cod_accion',$accion)->update(['num_orden'=>$n,'val_iteracion'=>$iteracion->id]);
                    dump("table('eventos_acciones')->where('cod_accion',$accion)->update(['num_orden'=>$n,'val_iteracion'=>$iteracion->id])");
                    $n++;
                }
            }
        }
        return [
            'title' => "Reindexado de eventos regla ".$regla,
            'message' => "OK",
            //'url' => url('tasks')
        ];


    }

    public function info_accion($accion){
        $a=acciones::find($accion);
        $salida="";
        if(isJson($a->param_accion)){
            foreach(json_decode($a->param_accion) as $param){
                if(isset($param->value))
                {
                    $valor=$param->value;
                } else {
                    $valor="";
                }
                try{  $salida.=$param->label."(".$param->tipo.")=>".$valor."\r\n"; } catch(\Throwable $e){};
            }
        }
        return $salida;
    }

    public function duplicar_accion($regla,$accion){
        $accion=acciones::find($accion);
        $accion->replicate()->save();
        return [
            'title' => trans('eventos.clonado_de_accion'),
            'message' => "OK ".str_replace(".php","",str_replace("_"," ",$accion->des_accion)),
            //'url' => url('tasks')
        ];
    }

    public function save(Request $r){
        try{
            //Primero vamos a recuperar el comando
            $path = resource_path('views/events/comandos');
            $fic_comando=$path.'/'.$r->comando;
            include_once($fic_comando);
            $JSON_param=decodeComplexJson($params);
            $parametros=$JSON_param->parametros;//Esto es porque el JSON lleva un nodo que se llama parametros
            //Ahora sustituimos en el JSON de parametros los valores que se han seleccionado
            //Buscamos en el request el parametro con el nombre en cuestion y si esta, guardamos su valor
            foreach($parametros as $p){
                $nombre=$p->name;
                if(isset($r->$nombre)){
                    $p->value=$r->$nombre;
                }
            }
            //Ahora lo convertimos en JSON para guardarlo
            $parametros=json_encode($parametros);
            //Paso 2, programacion horaria de la regla
            $dias=[];
            for($n=1; $n<8; $n++)
            {
                $nom_dia="dia".$n;
                    if(isset($r->$nom_dia)){
                        $temp=[
                            "num_dia"=> $n,
                            "horas"=>$r->$nom_dia
                        ];
                        $dias[]=$temp;
                    }
            }
            $dias=json_encode($dias);
            if (isset($r->clientes)){
                $r->clientes=implode(",",$r->clientes);
            } else {
                $r->clientes="0";
            }

        } catch(\Throwable $e){
            return [
                "response" => "ERROR",
                "error" => "Error al procesar los datos de la regla ".$r->nom_regla." ".mensaje_excepcion($e),
                "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ];
        }

        //Paso 3, a guardar en la BDD
        try{
            if($r->cod_regla!=0){
                $operacion="update";
                $reglas=reglas::find($r->cod_regla);
            } else {
                    //Crear nueva
                    $operacion="insert";
                    $reglas= new reglas();
            }
            $reglas->cod_cliente=$r->cod_propietario;
            $reglas->cod_usuario=Auth::user()->id_usuario;
            $reglas->nom_comando=$r->comando;
            $reglas->mca_activa=isset($r->mca_activa)?'S':'N';
            $reglas->nom_regla=$r->nom_regla;
            $reglas->intervalo=$r->intervalo;
            $reglas->fec_inicio=adaptar_fecha($r->fec_inicio);
            $reglas->fec_fin=adaptar_fecha($r->fec_fin);
            $reglas->fec_prox_ejecucion=Carbon::now();
            $reglas->param_comando=$parametros;
            $reglas->schedule=$dias;
            $reglas->cod_grupo=$r->cod_grupo;
            $reglas->clientes=$r->clientes;
            $reglas->nomolestar=$r->int_espera;
            $reglas->tip_nomolestar=$r->tip_espera;
            $reglas->timezone=Auth::user()->val_timezone;
            $reglas->save();
            //Si la regla lleva acciones por defecto asociadas, y la estamos creando de nuevas, las añadimos
            if($operacion=="insert" && isset($acciones_def)){
               
                $acciones_def=decodeComplexJson($acciones_def);
                foreach($acciones_def as $a){
                    DB::table('eventos_acciones')->insert([
                        "cod_regla"=>$reglas->cod_regla,
                        "val_iteracion"=>$a->iteracion,
                        "num_orden"=>$a->orden,
                        "nom_accion"=>$a->accion
                    ]);
                }
            }

            return [
                "response" => "Regla creada",
                "id"=>$reglas->cod_regla,
                "message" => "Regla ".$r->nom_regla." creada. <br>Ahora asocie las acciones a ejecutar",
                "TS" => Carbon::now()->format('Y-m-d h:i:s'),
                "url"=> config('app.carpeta_asset')."/edit/".$reglas->cod_regla,
                ];
        } catch(\Throwable $e){
            return [
                "response" => "ERROR",
                "error" => "Error al guardar la regla en BDD [".$operacion."]".$r->nom_regla." ".mensaje_excepcion($e),
                "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ];
        }
    }

    public function edit($id){

        $reglas=reglas::find($id);

        $acciones=acciones::find($id);

        $cod_regla=$id;

        return view('events.nueva_regla',compact('acciones','reglas','cod_regla'));
    }

    public function delete($id){
        try{
            $reglas=reglas::find($id);

            $acciones=acciones::find($id);
            if (isset($acciones)){
                $acciones->delete();
            }
            $mensaje="Regla [".$reglas->cod_regla."] ".$reglas->nom_regla. " borrada";
            $cliente=$reglas->cod_cliente;

            $reglas->delete();

            savebitacora($mensaje,$cliente);
            flash($mensaje)->success();
            return Redirect::to('/events');

        } catch(\Exception $e){

            savebitacora( "Error al borrar la regla ".mensaje_excepcion($e),$cliente);
            flash( "Error al borrar la regla ".mensaje_excepcion($e))->error();
            return Redirect::to('/events');
        }

    }

    function detalle_evento($id){
		$log=DB::table('eventos_log')
			->where('cod_regla',$id)
			->where('fec_log','>',Carbon::now()->subDays(7))
			->get();

		$patron=DB::select("select
											date(fec_log) as fecha,
											date_format(fec_log,'%H') as hora,
											count(cod_log) as cuenta
									from eventos_log
									where cod_regla=".$id."
										and txt_log like 'Iniciando proceso%'
										and fec_log>date_sub(now(), interval 7 day)
									group by
										date(fec_log),
										date_format(fec_log,'%H')");
		$patron=Collect($patron);
		$fechas=$patron->pluck('fecha')->unique()->sortby('fecha')->toArray();

		return view('events.detalle_evento',compact('log','id','patron','fechas'));

	}

	function ver_log_evento($id,$fecha,$hora){

		$log=DB::table('eventos_log')
			->where('cod_regla',$id)
			// ->where('fec_log','>',Carbon::parse($fecha))
            ->whereraw("date(fec_log)='".$fecha."'")
			->whereraw("date_format(fec_log,'%H')=".$hora)
			->get();
		return view('events.log_evento',compact('log'));

	}

    public function ejecutar_tarea_web($id) {
		try {
			ini_set('max_execution_time', 300); //300 seconds = 5 minutes
			$tarea=DB::table('tareas_programadas')->where('cod_tarea',$id)->first();
			log_tarea("Inicio de la tarea [".$tarea->cod_tarea."] ".$tarea->des_tarea,$tarea->cod_tarea);
			log_tarea("Ejecucion manual de la tarea [".$tarea->cod_tarea."]".$tarea->des_tarea." por ".Auth::user()->nom_usuario,$tarea->cod_tarea);
			Artisan::call($tarea->signature,['id'=>$id,'origen'=>'W']);
			log_tarea("Fin de la tarea [".$tarea->cod_tarea."] ".$tarea->des_tarea,$tarea->cod_tarea);
				//dd(Artisan::output());
				return [
					'title' => "Tareas programadas",
					'message' => "Tarea ".$tarea->des_tarea." ejecutada",
					//'url' => url('tasks')
				];
		} catch (\Exception $e){
			return [
				'title' => "Tareas programadas",
				'error' => "ERROR: Ocurrio un error ejecutando la tarea programada la tarea programada ".$tarea->des_tarea." ".mensaje_excepcion($e)
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

    public function test(Request $r){
        try{
            //Primero vamos a recuperar el comando
            $path = resource_path('views/events/comandos');
            $fic_comando=$path.'/'.$r->comando;
            include_once($fic_comando);
            $JSON_param=decodeComplexJson($params);
            $parametros=$JSON_param->parametros;//Esto es porque el JSON lleva un nodo que se llama parametros
            //Ahora sustituimos en el JSON de parametros los valores que se han seleccionado
            //Buscamos en el request el parametro con el nombre en cuestion y si esta, guardamos su valor
            foreach($parametros as $p){
                $nombre=$p->name;
                if(isset($r->$nombre)){
                    $p->value=$r->$nombre;
                }
            }
            $parametros=json_encode($parametros);
            if (isset($r->clientes)){
                $r->clientes=implode(",",$r->clientes);
            } else {
                $r->clientes="0";
            }
            $evento=new reglas;
            $evento->cod_regla=0;
            $evento->param_comando=$parametros;
            
            $evento->clientes=$r->clientes;
            $output=null;
            $resultado=$func_comando($evento,$output);
            $resultado=decodeComplexJson($resultado);
            $cod_regla=$r->cod_regla;
            return view('events.test',compact('resultado','cod_regla'));
            

        } catch(Throwable $e){
            return response()->json([
                "response" => "ERROR",
                "error" => "Error al probar el comando ".$r->comando." ".mensaje_excepcion($e),
                "TS" => Carbon::now()->format('Y-m-d h:i:s')
                ])->throwResponse();
        }
    }

    public function reset_regla(Request $r){

        DB::table('eventos_evolucion_id')
            ->where('cod_regla',$r->cod_regla)
            ->delete();
        DB::table('eventos_noactuar')
            ->where('cod_regla',$r->cod_regla)
            ->delete();
        DB::table('eventos_reglas')->where('cod_regla',$r->cod_regla)->update(['fec_ult_ejecucion'=>null,'fec_prox_ejecucion'=>null]);
        return [
            'title' => trans('general.eventos'),
            'message' => trans('eventos.evolucion_y_estado_de_regla_reseteados'),
            //'url' => url('tasks')
        ];
    }
}

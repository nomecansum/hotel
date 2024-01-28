<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use DB;
use Auth;
use Carbon\Carbon;
use App\User;
use OpenApi\Annotations as OA;
use App\Http\Controllers\IncidenciasController;
use App\Models\users;
use App\Models\causas_cierre;
use App\Models\incidencias;
use App\Models\puestos;
use App\Models\salas;
use App\Models\clientes;
use App\Models\estados_incidencias;
use App\Models\incidencias_tipos;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Jobs\getEstructuraIncidenciasSalas;
use App\Jobs\sincroIncidenciasSalas;

class APIController extends Controller
{
    //////////////////FUNCIONES AUXILIARES/////////////////////////////
    public static function get_usuario($r){
        try{
            $result=users::where('id_cliente',Auth::user()->id_cliente)
            ->where(function($q) use($r){
                $q->where('id',$r->id_usuario_apertura);
                $q->orWhere('id_usuario_externo',$r->id_usuario_apertura);
                $q->orWhere('email',$r->id_usuario_apertura);
            })->first()->id;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El usuario '.$r->id_usuario_apertura.' no existe');
        }
       

    }

    public static function get_incidencia($r){
        try{
            $result=incidencias::where('id_cliente',Auth::user()->id_cliente)
                ->where(function($q) use($r){
                    $q->where('id_incidencia',$r->id_incidencia)
                    ->orWhere('id_incidencia_externo',$r->id_incidencia);
                })
                ->first()->id_incidencia;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('La incidencia '.$r->id_incidencia.' no existe');
        }
    }

    public static function get_puesto($r){
        try{
            $result=puestos::where('id_cliente',Auth::user()->id_cliente)
                ->where(function($q) use($r){
                    $q->where('id_puesto',$r->id_puesto)
                    ->orWhere('cod_puesto',$r->id_puesto);
                })
                ->first()->id_puesto;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El puesto '.$r->id_puesto.' no existe');
        }
    }

    public static function get_tipo($r){
        try{

            $result=incidencias_tipos::where('id_cliente',$r->id_cliente)
                ->where('id_tipo_externo',$r->id_tipo_incidencia)
                ->first()->id_tipo_incidencia;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El tipo '.$r->tipo_incidencia_id.' no existe',406);
        }
    }

    public static function get_causa_cierre($r){
        try{
            $result=causas_cierre::where('id_cliente',Auth::user()->id_cliente)
                ->where(function($q) use($r){
                    $q->where('id_causa_cierre',$r->id_causa_cierre)
                    ->orWhere('id_causa_externo',$r->id_causa_cierre);
                })
                ->first()->id_causa_cierre;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('La causa de cierre '.$r->id_causa_cierre.' no existe');
        }
    }

    public static function get_sala($r){
        try{
            $result=salas::where('id_cliente',$r->id_cliente)
                ->where('id_externo_salas',$r->sala_id)
                ->first()->id_puesto;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('La sala con ID '.$r->sala_id.' no existe',406);
        }
    }

    public static function get_cliente_ext($r){
        try{
            $result=clientes::where('id_cliente_externo',$r->id_cliente)
                ->first()->id_cliente;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El cliente '.$r->id_cliente.' no existe',406);
        }
    }

    public static function get_cliente_salas($r){
        try{
            $result=clientes::where('id_cliente_salas',$r->id_cliente)
                ->first()->id_cliente;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El cliente '.$r->id_cliente.' no existe',406);
        }
    }

    public static function get_estado_salas($r){
        try{

            $result=estados_incidencias::where('id_cliente',$r->id_cliente)
                ->where('id_estado_salas',$r->estado)
                ->first()->id_estado;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El estado '.$r->estado.' no existe',406);
        }
    }

    public static function get_tipo_salas($r){
        try{

            $result=incidencias_tipos::where('id_cliente',$r->id_cliente)
                ->where('id_tipo_salas',$r->tipo_incidencia_id)
                ->first()->id_tipo_incidencia;
            return $result;
        } catch (\Exception $e) {
            throw new \ErrorException('El tipo '.$r->tipo_incidencia_id.' no existe',406);
        }
    }

    public static function check_existe_incidencia($r,$campo,$id){
        try{

            $result=incidencias::where('id_cliente',$r->id_cliente)
                ->where($campo,$id)
                ->first()->id_incidencia;
            if($result!=null){;
                return true;
            }
            return false;
        } catch (\Exception $e) {
            
        }
    }

    public static function respuesta_error($texto,$codigo){
        return response()->json([
            'result'=>'error',
            'error' => $texto,
            'timestamp'=>Carbon::now(),
        ])->setStatusCode($codigo);
    }

    ////////////////////////////////////////////////////////////////////


    ////////////////////FUNCIONES GENERALES//////////////////////////

    public function test(){
         
        return response()->json([
        'result'=>'ok',
        'timestamp'=>Carbon::now(),
        'message' => 'Hello World!']);
    }
    
    public function entidades(){
        
        $edificios=DB::table('edificios')
            ->select('id_edificio','des_edificio','abreviatura')
            ->where('edificios.id_cliente',Auth::user()->id_cliente)
            ->get();

        $plantas=DB::table('plantas')
            ->select('id_planta','des_planta','id_edificio','num_orden','abreviatura')
            ->where('plantas.id_cliente',Auth::user()->id_cliente)
            ->get();
        
        
        $puestos=DB::table('puestos')
            ->select('puestos.id_planta','puestos.id_puesto','puestos.cod_puesto','puestos.des_puesto','puestos.id_estado','puestos.id_tipo_puesto','puestos.mca_incidencia','estados_puestos.des_estado','estados_puestos.hex_color','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->where('puestos.id_cliente',Auth::user()->id_cliente)
            ->get();

        foreach($edificios as $ed){
            $ed->plantas=$plantas->where('id_edificio',$ed->id_edificio);
            foreach($ed->plantas as $pl){
                $pl->puestos=$puestos->where('id_planta',$pl->id_planta);
            }
        }

        $tipos_puesto = DB::table('puestos_tipos')
            ->select('id_tipo_puesto','des_tipo_puesto','val_icono','val_color','abreviatura')
            ->where(function($q){
                $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                    $q->orwhere('puestos_tipos.mca_fijo','S');
                }
            })
        ->get();

        $tipos_incidencia=DB::table('incidencias_tipos')
            ->select('id_tipo_incidencia','des_tipo_incidencia','val_icono','val_color','list_tipo_puesto','id_tipo_externo','id_tipo_salas','val_responsable')
            ->where('incidencias_tipos.id_cliente',Auth::user()->id_cliente)
            ->get();

        foreach($tipos_incidencia as $ti){
            if($ti->list_tipo_puesto!=null){
                $ti->list_tipo_puesto=explode(',',$ti->list_tipo_puesto);
            }
        }

        $causas_cierre=DB::table('causas_cierre')
            ->select('id_causa_cierre','des_causa','val_icono','val_color','mca_default','id_causa_externo')
            ->where('causas_cierre.id_cliente',Auth::user()->id_cliente)
            ->get();

        $estados_incidencia=DB::table('estados_incidencias')
            ->select('id_estado','des_estado','val_icono','val_color','mca_cierre')
            ->where('estados_incidencias.id_cliente',Auth::user()->id_cliente)
            ->get();

        $perfiles = DB::table('niveles_acceso')
            ->select('cod_nivel','val_nivel_acceso','des_nivel_acceso')
            ->where(function($q){
                $q->where('id_cliente',Auth::user()->id_cliente);
                $q->orwhere('mca_fijo','S');
            })
            ->get();

        $turnos=DB::table('turnos')
            ->select('id_turno','des_turno','dias_semana','fec_inicio','fec_fin')
            ->where('id_cliente',Auth::user()->id_cliente)
            ->get();

        $turnos->transform(function($item,$key){
            $item->dias_semana=json_decode($item->dias_semana);
            return $item;
        });

        $departamentos=DB::table('departamentos')
            ->select('cod_departamento','nom_departamento','cod_departamento_padre')
            ->where('id_cliente',Auth::user()->id_cliente)
            ->get();

        $colectivos_cliente=DB::table('colectivos')
            ->select('cod_colectivo','des_colectivo')
            ->where('id_cliente',Auth::user()->id_cliente)
            ->get();
       
       $respuesta=array(
            'result'=>'ok',
            'timestamp'=>Carbon::now(),
            'edificios'=>$edificios,
            'tipos_puesto'=>$tipos_puesto,
            'tipos_incidencia'=>$tipos_incidencia,
            'causas_cierre'=>$causas_cierre,
            'estados_incidencia'=>$estados_incidencia,
            'perfiles'=>$perfiles,
            'turnos'=>$turnos,
            'colectivos'=>$colectivos_cliente,
            'departamentos'=>$departamentos,
        );

       
        savebitacora('Solicitud de listado de entidades ',"API","entidades","OK"); 
        return response()->json($respuesta);
    }

    public function echo_test(Request $r){
        $data=[
            "QUERY_STRING"=>$r->getQueryString(),
            "HEADERS"=>$r->headers->all(),
            "BODY"=>$r->getContent(),
        ];
        return response()->json($data);

    }

    public function process_test(Request $r){
        $data=[
            "id_incidencia"=>"FF2202",
            "url_detalle"=>"http://localhost:8080/incidencias/FF2202"
        ];
        return response()->json($data);
    }

    ///////////////////////FUNCIONES PARA INCIDENCIAS/////////////////

    public function get_incidents(Request $r){

        try{
            //Fechas
            $f1=(isset($r->fec_desde))?Carbon::parse($r->fec_desde):Carbon::now()->startOfMonth();
            $f2=(isset($r->fec_hasta))?Carbon::parse($r->fec_hasta):Carbon::now()->endOfMonth();
            $fechas=$f1->format('d/m/Y').' - '.$f2->format('d/m/Y');
            $r->request->add(['fechas' => $fechas]);
            $r->request->add(['ac' => 'B']);


            $respuesta=app('App\Http\Controllers\IncidenciasController')->search($r);
            $incidencias = $respuesta["incidencias"]->map(function ($item, $key) {
                $acciones=DB::table('incidencias_acciones')
                    ->select('id_accion','des_accion','fec_accion','id_usuario','mca_resuelve','users.id_usuario_externo as id_usuario_ext')
                    ->join('users','users.id','incidencias_acciones.id_usuario')
                    ->where('id_incidencia',$item->id_incidencia)
                    ->get();
                return [
                    'id_incidencia' => $item->id_incidencia,
                    'id_incidencia_externo' => $item->id_incidencia_externo,
                    'id_incidencia_salas' => $item->id_incidencia_salas,
                    'des_incidencia' => $item->des_incidencia,
                    'txt_incidencia' => $item->txt_incidencia,
                    'fec_apertura' => $item->fec_apertura,
                    'fec_cierre' => $item->fec_cierre,
                    'id_tipo_incidencia' => $item->id_tipo_incidencia,
                    'id_puesto' => $item->id_puesto,
                    'id_causa_cierre' => $item->id_causa_cierre,
                    'comentario_cierre' => $item->comentario_cierre,
                    'id_estado' => $item->id_estado,
                    'id_usuario_apertura' => $item->id_usuario_apertura,
                    'id_usuario_ext' => users::find($item->id_usuario_apertura)->id_usuario_externo,
                    'acciones' => $acciones,
                ];
            });
            $solicitudes = $respuesta["solicitudes"]->map(function ($item, $key) {
                $acciones=DB::table('incidencias_acciones')
                    ->select('id_accion','des_accion','fec_accion','id_usuario','mca_resuelve','users.id_usuario_externo as id_usuario_ext')
                    ->join('users','users.id','incidencias_acciones.id_usuario')
                    ->where('id_incidencia',$item->id_incidencia)
                    ->get();
                return [
                    'id_incidencia' => $item->id_incidencia,
                    'id_incidencia_externo' => $item->id_incidencia_externo,
                    'id_incidencia_salas' => $item->id_incidencia_salas,
                    'des_incidencia' => $item->des_incidencia,
                    'txt_incidencia' => $item->txt_incidencia,
                    'fec_apertura' => $item->fec_apertura,
                    'fec_cierre' => $item->fec_cierre,
                    'id_tipo_incidencia' => $item->id_tipo_incidencia,
                    'id_puesto' => $item->id_puesto,
                    'id_causa_cierre' => $item->id_causa_cierre,
                    'comentario_cierre' => $item->comentario_cierre,
                    'id_estado' => $item->id_estado,
                    'id_usuario_apertura' => $item->id_usuario_apertura,
                    'id_usuario_ext' => users::find($item->id_usuario_apertura)->id_usuario_externo,
                    'val_presupuesto' => $item->val_presupuesto,
                    'val_proyecto' => $item->val_proyecto,
                    'val_importe' => $item->val_importe,
                    'acciones' => $acciones,
                ];
            });
            savebitacora('Solicitud de listado de incidencias '.json_encode($r->all()),"API","get_incidents","OK"); 
            return response()->json([
                'result'=>'ok',
                'timestamp'=>Carbon::now(),
                'incidencias' => $incidencias,
                'solicitudes' => $solicitudes]);
        }catch (\Throwable $e) {
            savebitacora('ERROR Solicitud de listado de incidencias '.json_encode($r->all()),"API","get_incidents","ERROR"); 
            return $this->respuesta_error('ERROR Solicitud de listado de incidencias '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        }
    }

    public function crear_incidencia(Request $r){

        try{
            $r->request->add(['id_cliente' => Auth::user()->id_cliente]);
            if(strlen($r->id_incidencia_externo)>0){
                if($this->check_existe_incidencia($r,"id_causa_externo",$r->id_incidencia_externo)===true){
                    throw new \ErrorException('La incidencia ID '.$r->id_incidencia_externo.' ya existe',403);
                };
            } else {
                throw new \ErrorException('Debe indicar un ID de incidencia',400);
            } 
            $r->request->add(['fec_apertura' => Carbon::now()]);
            $r->request->add(['id_usuario_apertura' => $this->get_usuario($r)]);
            $r->request->add(['id_cliente' => Auth::user()->id_cliente]);
            $r->request->add(['id_puesto' => $this->get_puesto($r)]);
            $r->request->add(['id_tipo_incidencia' => $this->get_tipo($r)]);
            $r->request->add(['procedencia' => "api"]);
            $r->request->add(['id_incidencia_externo' => $r->id_incidencia_externo]);
            $r->request->add(['url_detalle_incidencia' => $r->url_detalle_incidencia]);
            $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
            savebitacora('Crear de incidencia '.json_encode($r->all()),"API","crear_incidencia","OK"); 
            //Le cambiamos la URL que devuelve el controller para usar en la web por la buena para la API
            $respuesta['url']=route('incidencias.show',$respuesta['id']);
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Creacion de incidencia '.json_encode($r->all()).' '.$e->getMessage(),"API","crear_incidencia","ERROR");
            return $this->respuesta_error('ERROR creando incidencia '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        } 
    }

    public function add_accion(Request $r){
        try{
            $r->request->add(['id_usuario' => $this->get_usuario($r)]);
            $r->request->add(['id_incidencia' => $this->get_incidencia($r)]);
            $r->request->add(['procedencia' => "api"]);

            $respuesta=app('App\Http\Controllers\IncidenciasController')->add_accion($r);
            savebitacora('Añadir accion en incidencia '.json_encode($r->all()),"API","add_accion","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR añadiendo accion a incidencia '.json_encode($r->all()).' '.$e->getMessage(),"API","add_accion","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        } 
        
    }

    public function cerrar_ticket(Request $r){
        try{
            $r->request->add(['id_usuario' => $this->get_usuario($r)]);
            $r->request->add(['id_incidencia' => $this->get_incidencia($r)]);
            $r->request->add(['id_causa_cierre' => $this->get_causa_cierre($r)]);
            $r->request->add(['procedencia' => "api"]);

            $respuesta=app('App\Http\Controllers\IncidenciasController')->cerrar($r);
            savebitacora('Cerrar incidencia incidencia '.json_encode($r->all()),"API","cerrar_ticket","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR cerrando incidencia '.json_encode($r->all()).' '.$e->getMessage(),"API","cerrar_ticket","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error cerrando incidencia '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        } 
    }

    public function reabrir_ticket(Request $r){
        try{
            $r->request->add(['id_usuario' => $this->get_usuario($r)]);
            $r->request->add(['id_incidencia' => $this->get_incidencia($r)]);
            $r->request->add(['procedencia' => "api"]);

            $respuesta=app('App\Http\Controllers\IncidenciasController')->reabrir($r);
            savebitacora('Reabrir incidencia incidencia '.json_encode($r->all()),"API","reabrir_ticket","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR reabriendo incidencia '.json_encode($r->all()).' '.$e->getMessage(),"API","reabrir_ticket","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error reabriendo incidencia '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        } 
    }
    
    ///////////////////////FUNCIONES PARA SALAS///////////////////////

    public function solicitud_sincro_datos(Request $r,$fecha,$cliente){
        try{
            $cliente_salas=clientes::where('id_cliente_salas',$cliente)->first()->id_cliente;
            $r->request->add(['id_cliente' => $cliente_salas]);
            $r->request->add(['procedencia' => "salas"]);
            //Lanzamos el job de sincronizacion con salas
            getEstructuraIncidenciasSalas::dispatch($fecha,$cliente_salas)->onQueue('salas');
            savebitacora('Solicitud de resincronizacion de estructuras salas'.file_get_contents('php://input'),"API","solicitud_sincro","OK");
            return response()->json([
                'result'=>'ok',
                'timestamp'=>Carbon::now(),
                'message' => 'Proceso de sincronizacion completado ']);
        }catch (\Throwable $e) {
            savebitacora('Error en sincronizacion  de estructuras salas '.file_get_contents('php://input'),"API","solicitud_sincro","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error en el proceso '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
            
        }
    }

    public function crear_incidencia_salas(Request $r){
        try{
            if(isset($r->all()[0])){
                $request=json_decode($r->all()[0],true);
                foreach($request as $key=>$value){
                    $r->request->add([$key => $value]);
                }
            }
            
            $r->request->add(['id_cliente' => $this->get_cliente_ext($r)]);
            //Comprobamos si existe la incidencia
            if($this->check_existe_incidencia($r,"id_incidencia_salas",$r->incidencia_sala_id)===true){
                throw new \ErrorException('La incidencia ID '.$r->incidencia_sala_id.' ya existe',403);
            };
            $r->request->add(['id_puesto' => $this->get_sala($r)]);
            
            $r->request->add(['fec_apertura' => Carbon::parse($r->fecha)]);
            $r->request->add(['id_usuario_apertura'=>config('app.id_usuario_spotlinker_salas')]);
            $r->request->add(['id_estado' => $this->get_estado_salas($r)]);
            $r->request->add(['id_tipo_incidencia' => $this->get_tipo_salas($r)]);
            $r->request->add(['procedencia' => "salas"]);
            $r->request->add(['id_incidencia_salas' => $r->incidencia_sala_id]);
            $r->request->add(['txt_incidencia' => $r->notas_admin]);
            $r->request->add(['des_incidencia' => $r->descripcion_adicional]);

            $respuesta=app('App\Http\Controllers\IncidenciasController')->save($r);
            savebitacora('Crear de incidencia desde salas '.file_get_contents('php://input'),"API","crear_incidencia_salas","OK");
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Creacion de incidencia desde salas '.file_get_contents('php://input'),"API","crear_incidencia_salas","ERROR");
            //var_dump($e->getMessage());
            return $this->respuesta_error('ERROR: Ocurrio un error creando la incidencia '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        } 
    }

    public function add_accion_salas(Request $r){
        try{
            if(isset($r->all()[0])){
                $request=json_decode($r->all()[0],true);
                foreach($request as $key=>$value){
                    $r->request->add([$key => $value]);
                }
            }

            $r->request->add(['id_cliente' => $this->get_cliente_ext($r)]);
            //Comprobamos si existe la incidencia
            if($this->check_existe_incidencia($r,"id_incidencia_salas",$r->incidencia_sala_id)===false){
                throw new \ErrorException('La incidencia ID '.$r->incidencia_sala_id.' no existe',404);
            };
            $r->request->add(['id_estado' => $this->get_estado_salas($r)]);
            $r->request->add(['procedencia' => "salas"]);
            $r->request->add(['id_incidencia_salas' => $r->incidencia_sala_id]);
            $r->request->add(['txt_incidencia' => $r->notas_admin]);
            //Vamos a sacar la diferencia en el campo de comentarios
            $incidencia=incidencias::find($r->incidencia_id_puestos);

            $incidencia->txt_incidencia=$r->notas_admin;
            $incidencia->save();
            if(isset($r->descipcion_adicional)){
                $txt_nuevo=$r->descipcion_adicional;
            } else {
                $txt_nuevo=str_replace($incidencia->txt_incidencia,"",$r->notas_admin);
            }
            

            $r->request->add(['des_accion' => $txt_nuevo]);
            $r->request->add(['id_incidencia' => $r->incidencia_id_puestos]);
            $respuesta=app('App\Http\Controllers\IncidenciasController')->add_accion($r);
            savebitacora('Modificar incidencia '.file_get_contents('php://input'),"API","add_accion_salas","OK");
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Modificacion de incidencia '.file_get_contents('php://input'),"API","add_accion_salas","ERROR");
            return response()->json([
                'result'=>'error',
                'error' => 'ERROR: Ocurrio un error añadiendo accion '.$e->getMessage(),
                'timestamp'=>Carbon::now(),
            ])->setStatusCode(400);
        }
    }

    public function request_sincro($fecha,$cliente){
        try{
            $r = new \Illuminate\Http\Request();
            $r->setMethod('POST');
            $cliente_salas=clientes::where('id_cliente_salas',$cliente)->first()->id_cliente;
            $r->request->add(['id_cliente' => $cliente_salas]);
            $r->request->add(['procedencia' => "salas"]);
            //Codigo para la resincronizacion de incidencias
            $fecha=Carbon::parse($fecha);
            $url="get_incidencias_desde_fecha/".$fecha;
            $respuesta=enviar_request_salas("GET",$url,"","",$cliente_salas);
            if ($respuesta['status'] != 200) {
                throw new \ErrorException($respuesta['error'].' | statusCode: '.$respuesta['status'], $respuesta['status']);
            }
            $respuesta=json_decode($respuesta['body']);
            //Lanzamos el job de sincronizacion con salas
            sincroIncidenciasSalas::dispatch($respuesta,$cliente_salas)->onQueue('salas');
            savebitacora('Solicitud de resincronizacion de incidencias salas'.file_get_contents('php://input'),"API","solicitud_sincro","OK");
            return response()->json([
                'result'=>'ok',
                'timestamp'=>Carbon::now(),
                'message' => 'Solicitud de resincronizacion de incidencias salas completada.',
                ]);
        }catch (\Throwable $e) {
            savebitacora('Error en sincronizacion  de incidencias salas '.file_get_contents('php://input'),"API","reabrir_ticket","ERROR");
            return $this->respuesta_error('ERROR: Ocurrio un error en el proceso '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
            
        }
    }

    public function get_incidencias_desde_fecha(Request $r,$fecha,$cliente){


        //OJO este solo debe daro las incidencias que esten pendientes de sincornizazion
        try{
            //Fechas
           
            $f1=(isset($fecha))?Carbon::parse($fecha):Carbon::now()->startOfMonth();
            $f2=Carbon::now()->endOfMonth();
            $fechas=$f1->format('d/m/Y').' - '.$f2->format('d/m/Y');
            $r->request->add(['fechas' => $fechas]);
            $r->request->add(['ac' => 'B']);
            $r->request->add(['tipo' => config('app.tipo_puesto_sala')]);
            $r->request->add(['id_cliente'=>$cliente]);
            $r->request->add(['cliente' => [$this->get_cliente_ext($r)]]);
            Auth::user()->id_cliente=$r->cliente[0];
            $respuesta=app('App\Http\Controllers\IncidenciasController')->search($r);
            //dd($respuesta);
            //{:a_incidencias_pendientes =>[{sala_id, fecha, descripcion_adicional, tipo_incidencia_id, estado, notas_admin, incidencia_id_puestos}]}
            $incidencias = $respuesta->map(function ($item, $key) {
                return
                    [
                        "sala_id"=>salas::where('id_puesto',$item->id_puesto)->first()->id_externo_salas??0,
                        "fecha"=>$item->fec_apertura,
                        "descripcion_adicional"=>$item->des_incidencia,
                        "tipo_incidencia_id"=>$item->id_tipo_salas,
                        "estado"=>$item->id_estado_salas,
                        "notas_admin"=>$item->txt_incidencia,
                        "id_puesto"=>$item->id_puesto,
                        "incidencia_id_puestos"=>$item->id_incidencia
                    ];
            });
            savebitacora('Solicitud de listado de incidencias '.file_get_contents('php://input'),"API","get_incidents","OK");
            return response()->json([
                'result'=>'ok',
                'timestamp'=>Carbon::now(),
                'a_incidencias_pendientes' => $incidencias]);
        }catch (\Throwable $e) {
            savebitacora('ERROR Solicitud de listado de incidencias '.file_get_contents('php://input'),"API","get_incidents","ERROR");
            return $this->respuesta_error('ERROR Solicitud de listado de incidencias '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        }
    }

    public function add_incidencia_id_puestos_pendientes(Request $r){
        try{

            if(isset($r->all()[0])){
                $request=json_decode($r->all()[0],true);
                foreach($request as $key=>$value){
                    $r->request->add([$key => $value]);
                }
            }
            
            $r->request->add(['id_cliente' => [$this->get_cliente_ext($r)]]);
            $r->request->add(['procedencia' => "salas"]);
            $lista_incidencias=$r->incidencias;
            $lista_incidencias=json_decode($lista_incidencias);
            $pendientes=[];
            
            //Recibo mis ID de incidenicci y los tengo que buscar para completar con el id_externo_salas que me mande
            foreach($lista_incidencias as $i){
                $incidencia=incidencias::where('id_incidencia',$i->incidencia_id_puestos)->first();
                if(isset($incidencia)){
                    $incidencia->id_incidencia_salas=$i->incidencia_id_salas;
                    $incidencia->save();
                }else{
                    $pendientes[]=$i;
                }
            }
            savebitacora('Solicitud de actualizacion de id de incidencias en salas '.file_get_contents('php://input'),"API","add_incidencia_id_puestos_pendientes","OK");
            return response()->json([
                'result'=>'ok',
                'timestamp'=>Carbon::now()]);
        }catch (\Throwable $e) {
            savebitacora('ERROR Solicitud de actualizacion de id de incidencias en salas '.file_get_contents('php://input'),"API","add_incidencia_id_puestos_pendientes","ERROR");
            return $this->respuesta_error('ERROR Solicitud de actualizacion de id de incidencias en salas '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        }
    }

    //////////////////////FUNCIONES PARA USUARIOS  //////////////////////////
    public function get_users(Request $r){
        try{
            //Fechas
            $respuesta=app('App\Http\Controllers\UsersController')->search($r);
            $usersObjects = $respuesta->map(function ($item, $key) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'email' => $item->email,
                    'created_at' => $item->created_at,
                    'img_usuario' => $item->img_usuario,
                    'cod_nivel' => $item->cod_nivel,
                    'last_login' => $item->last_login,
                    'id_cliente' => $item->id_cliente,
                    'val_timezone' => $item->val_timezone,
                    'id_usuario_supervisor' => $item->id_usuario_supervisor,
                    'id_usuario_externo' => $item->id_usuario_externo,
                    'id_edificio' => $item->id_edificio,
                    'id_departamento' => $item->id_departamento,
                    'mca_notif_push' => $item->mca_notif_push,
                    'mca_notif_email' => $item->mca_notif_email,
                    'deleted_at' => $item->deleted_at
                ];
            });
            savebitacora('Solicitud de listado de usuarios '.file_get_contents('php://input'),"API","get_users","OK"); 
            return response()->json([
                'result'=>'ok',
                'timestamp'=>Carbon::now(),
                'users' => $usersObjects]);
        }catch (\Throwable $e) {
            savebitacora('ERROR Solicitud de listado de usuarios '.file_get_contents('php://input'),"API","get_users","ERROR"); 
            return $this->respuesta_error('ERROR Solicitud de listado de usuarios '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        }
    }

    public function update_user(Request $r){
        try{
            
            $method = $r->method();
            if($method=='PUT'){//Crear uno nuevo
                $ya_esta=Users::where('email',$r->email)->first();
                if($ya_esta){
                    return $this->respuesta_error('ERROR el email '.$r->email.' esta en uso',400);
                }
                $user=Users::insertGetId(['name'=>$r->name,'email'=>$r->email,'password'=>bcrypt($r->password)]);
                $r->request->add(['id' =>  $user]);
                if(!isset($r->cod_nivel)){
                    $r->request->add(['cod_nivel' =>  1]);
                }
            } else {
                $user=Users::where('id',$r->id)->first();
                if(!isset($user)){
                    return $this->respuesta_error('ERROR Usuario no encontrado',400);
                }
                $user=$user->id;
            }
            if(!isset($r->cod_nivel)){
                return $this->respuesta_error('ERROR cod_nivel es requerido',400);
            }
            if(!isset($r->id_cliente)){
                return $this->respuesta_error('ERROR id_cliente es requerido',400);
            }

            $respuesta=app('App\Http\Controllers\UsersController')->update($user,$r);
            savebitacora('Solicitud de creacion de usuario '.file_get_contents('php://input'),"API","get_users","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Solicitud de listado de usuarios '.file_get_contents('php://input'),"API","get_users","ERROR"); 
            return $this->respuesta_error('ERROR Solicitud de creacion de usuario '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        }
    }

    public function delete_user(Request $r){
        try{
            $user=Users::where('id',$r->id)->first();
            if(!isset($user)){
                return $this->respuesta_error('ERROR Usuario no encontrado',400);
            }
            $user=$user->id;
            if (request()->is('*/soft')) {
                $tipo="soft";
                $respuesta=app('App\Http\Controllers\UsersController')->soft_delete($r,$user);
            } else {
                $tipo="hard";
                $respuesta=app('App\Http\Controllers\UsersController')->destroy($r,$user);
            }
            savebitacora('Solicitud de borrado '.$tipo.' de usuario '.file_get_contents('php://input'),"API","delete","OK"); 
            return response()->json($respuesta);
        }catch (\Throwable $e) {
            savebitacora('ERROR Solicitud de borrado de usuario '.file_get_contents('php://input'),"API","delete","ERROR"); 
            return $this->respuesta_error('ERROR Solicitud de borrado de usuario '.$e->getMessage(),$e->getCode()!=0?$e->getCode():400);
        }
    }

}

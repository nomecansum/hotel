<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\tareas;
use App\Models\users;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\colectivos;
use App\Models\departamentos;
use App\Models\edificios;
use App\Models\niveles_acceso;
use App\Models\turnos;
use App\Models\puestos;
use Str;

class sincronizarWorkdayGenerali extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'task:sincronizarWorkdayGenerali {id?} {origen=C} {--queue}'; //todas las tareas se tienen que llamar task:NOMBRE
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tarea para la carga y sincronizacion de datos desde Workday para Generali';

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
                    "tipo": "list",
                    "multiple": true,
                    "list": "BARCELONA-PEDROSA|BARCELONA TORRECERD\u00C1|MADRID - MANUEL GOMEZ MORENO|MADRID - CENTRAL",
                    "values": "BARCELONA-PEDROSA|BARCELONA TORRECERD\u00C1|MADRID - MANUEL GOMEZ MORENO|MADRID - CENTRAL",
                    "required": true
                },
                {
                    "label": "Perfil por defecto",
                    "name": "cod_nivel",
                    "tipo": "list_db",
                    "multiple": false,
                    "sql": "SELECT DISTINCT \n
                                `niveles_acceso`.`cod_nivel` as id, \n
                                concat(\'[\',nom_cliente,\'] - \',`niveles_acceso`.`des_nivel_acceso`) as nombre  \n
                            FROM \n
                                `niveles_acceso` \n
                                INNER JOIN `clientes` ON (`niveles_acceso`.`id_cliente` = `clientes`.`id_cliente`) \n
                            WHERE \n
                            `niveles_acceso`.`id_cliente` in('.sincronizarWorkdayGenerali::clientes().') \n
                            or mca_fijo=\'S\'  \n
                            ORDER BY 2",
                    "required": true,
                    "buscar": true,
                    "width": 6
                },
                {
                    "label": "Perfil supervisores",
                    "name": "cod_nivel_supervisor",
                    "tipo": "list_db",
                    "multiple": false,
                    "sql": "SELECT DISTINCT \n
                                `niveles_acceso`.`cod_nivel` as id, \n
                                concat(\'[\',nom_cliente,\'] - \',`niveles_acceso`.`des_nivel_acceso`) as nombre  \n
                            FROM \n
                                `niveles_acceso` \n
                                INNER JOIN `clientes` ON (`niveles_acceso`.`id_cliente` = `clientes`.`id_cliente`) \n
                            WHERE \n
                            `niveles_acceso`.`id_cliente` in('.sincronizarWorkdayGenerali::clientes().') \n
                            or mca_fijo=\'S\'  \n
                            ORDER BY 2",
                    "required": true,
                    "buscar": true,
                    "width": 6,
                    "br": 1
                },
                {
                    "label": "Procesar usuarios",
                    "name": "procesar_usu",
                    "tipo": "bool",
                    "def": true,
                    "required": false
                },
                {
                    "label": "Procesar departamentos",
                    "name": "procesar_dep",
                    "tipo": "bool",
                    "def": true,
                    "required": false
                },
                {
                    "label": "Procesar colectivos",
                    "name": "procesar_colectivos",
                    "tipo": "bool",
                    "def": true,
                    "required": false
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

        return "task:sincronizarWorkdayGenerali";
     }

    static function definicion(){

        return "Tarea para la carga y sincronizacion de datos desde Workday para Generali";
     }

    static function grupo(){

    return "A";
    }

    static function nombre_dep($dato){
        if(!strpos($dato, ' (')){
            return $dato;
        } else {
            return substr($dato, 0, strpos($dato, ' ('));
        }
    }
    static function nombre_sup($dato){
        if(!strpos($dato, ' (')){
            return null;
        } else {
            $nombre=substr($dato, strpos($dato, ' (')+2,strlen($dato));
            return substr($nombre, 0, strlen($nombre)-1);
        }
    }

    static function insertar_dep($nombre,$padre,$cliente,$nivel,$id_externo){
        $dato=new departamentos();
        $dato->nom_departamento=$nombre;
        $dato->id_cliente=$cliente;
        $dato->cod_departamento_padre=$padre;
        $dato->sync_at=Carbon::now();
        $dato->num_nivel=$nivel;
        $dato->id_externo=$id_externo;
        $dato->save();
        return $dato->cod_departamento;
    }

    static function insertar_cole($nombre,$cliente,$id_externo){
        $dato=new colectivos();
        $dato->des_colectivo=$nombre;
        $dato->id_cliente=$cliente;
        $dato->sync_at=Carbon::now();
        $dato->id_externo=$id_externo;
        $dato->save();
        return $dato->cod_colectivo;
    }

    static function insertar_usu($id,$nombre,$cliente,$id_externo,$colectivo,$departamento,$nivel,$email,$edificio,$turno,$puesto,$planta,$id_tarea){
        if($id!=null){
            $dato=users::find($id);
            if($nivel->val_nivel_acceso>$dato->val_nivel_acceso){
                $dato->cod_nivel=$nivel->cod_nivel;
                $dato->nivel_acceso=$nivel->val_nivel_acceso;
            }
        } else {
            $dato=new users();
            $dato->cod_nivel=$nivel->cod_nivel;
            $dato->nivel_acceso=$nivel->val_nivel_acceso;
            $dato->tipos_puesto_admitidos="311,396,411";
        }
        try{
            $dato->name=$nombre;
            $dato->id_cliente=$cliente;
            $dato->sync_at=Carbon::now();
            $dato->id_usuario_externo=$id_externo;
            $dato->id_departamento=$departamento;
            $dato->email=$email;
            $dato->id_edificio=$edificio;
            $dato->password=Str::random(40);
            $dato->save();
        } catch (\Exception $e){
            Log::error('Error al insertar usuario '.$nombre.' '.$email.' '.$e->getMessage());
            log_tarea('Error al insertar usuario '.$nombre.' '.$email.' '.$e->getMessage(),$id_tarea,'error');
        }
        
        $dato=users::where('email',$email)->first();
        //Colectivos
        $esta_colectivo=DB::table('colectivos_usuarios')->where('cod_colectivo',$colectivo)->where('id_usuario',$dato->id)->first();
        if($esta_colectivo){
            //A ver si el usuario ya estaba en el colectivo
            $esta_colectivo=DB::table('colectivos_usuarios')->where('cod_colectivo',$colectivo)->where('id_usuario',$dato->id)->first();
            if(!$esta_colectivo){
                DB::table('colectivos_usuarios')->insert(['cod_colectivo'=>$colectivo,'id_usuario'=>$dato->id]);
            }
        }
        //El turno
        if(isset($turno)){
            DB::table('turnos_usuarios')->where('id_usuario',$dato->id)->delete();
            DB::table('turnos_usuarios')->insert(['id_turno'=>$turno->id_turno,'id_usuario'=>$dato->id]);
        }

    
        //planta
        if(isset($planta)){
            //Cagada con la baja
            if($planta==0){
                $txt_planta='B';
            } else if($planta==-1) {
                $txt_planta='S1';
            } else if($planta==-2) {
                $txt_planta='S2';
            } else {
                $txt_planta=lz($planta,2);
            }
            $id_planta=DB::table('plantas')->where('id_cliente',$cliente)->where('id_edificio',$edificio)->where('abreviatura',$txt_planta)->first();
            if(isset($id_planta)){
                $planta=$id_planta->id_planta;
                $esta=DB::table('plantas_usuario')->where('id_usuario',$dato->id)->where('id_planta',$planta)->first();
                if(!isset($esta)){
                    DB::table('plantas_usuario')->insert(['id_planta'=>$planta,'id_usuario'=>$dato->id]);
                }
            }
            
        }

        //El puesto
        if(isset($puesto) && $puesto!='BENCH'){
            if($dato->list_puestos_preferidos!=null){
                $puestos_preferidos=json_decode($dato->list_puestos_preferidos);
                //Borramos las reservas de tipo puesto que tengael usuario metidas desde workday por si hay cambios para que actuelicen
                foreach($puestos_preferidos as $key=>$puesto_preferido){
                    if($puesto_preferido->tipo=='pu' && (isset($puesto_preferido->workday) && $puesto_preferido->workday==true)){
                        unset($puestos_preferidos[$key]);
                    }
                }
            } else {
                $puestos_preferidos=json_decode("[{
                    \"id\": 20,
                    \"tipo\": \"ul\",
                    \"text\": \"Ultimas 20 reservas\",
                    \"color\": \"rgba(0, 0, 0, 0)\"
                }]");
            }
            //Ahora como ya viene el codigo de puesto no hace falta hacer essta mierda
            $datos_puesto=puestos::where('cod_puesto',$puesto)->first();
            if(isset($datos_puesto))
            {
                $id_puesto=$datos_puesto->id_puesto;
                //Ahopra a ponerselo en las preferencias de reservas
                $ya_esta=collect($puestos_preferidos)->where('tipo','pu')->where('id',$id_puesto)->first();
                if(!isset($ya_esta)){
                    $encontrado=false;
                    foreach($puestos_preferidos as $key=>$p){
                        if($p->tipo=='pu' && $p->id==$id_puesto){
                           $encontrado=true;
                        }
                    }
                    if(!$encontrado){
                        $nuevo=new \stdClass();
                        $nuevo->id=$id_puesto;
                        $nuevo->tipo='pu';
                        $nuevo->text=$datos_puesto->des_puesto;
                        $nuevo->color="rgb(239, 195, 230)";
                        $nuevo->icono="<i class=\"fa-solid fa-chair-office\"></i> Puesto  ";
                        $nuevo->workday=true;
                        array_splice($puestos_preferidos,$key,0,[$nuevo]);
                    }

                    //Ahora por si acaso, a cada uno le añadimos por defecto la  planta del puesto que tiene asignado para el caso de que no tenga puesto
                }
                

                if(isset($planta)){
                    $ya_esta=collect($puestos_preferidos)->where('tipo','pl')->where('id',$planta)->first();
                    if(!isset($ya_esta)){
                        $encontrado=false;
                        foreach($puestos_preferidos as $key=>$p){
                            if($p->tipo=='pl' && $p->id==$planta){
                            $encontrado=true;
                            }
                        }
                        if(!$encontrado){
                            $nuevo=new \stdClass();
                            $nuevo->id=$planta;
                            $nuevo->tipo='pl';
                            $nuevo->text=$txt_planta;
                            $nuevo->color="rgb(252, 193, 74)";
                            $nuevo->icono="<i class=\"fa-solid fa-layer-group\"></i> Planta  ";
                            $nuevo->workday=true;
                            array_splice($puestos_preferidos,$key,0,[$nuevo]);
                        }
                    }
                }
                $dato->list_puestos_preferidos=json_encode($puestos_preferidos);
                $dato->save();
            } else {
                Log::error('No se ha encontrado el puesto '.$puesto.' para el usuario '.$email);
                log_tarea('No se ha encontrado el puesto '.$puesto.' para el usuario '.$email,$id_tarea,'error');
            }
        }
        return $dato->id;
    }

    public function handle()
    {
        $this->escribelog_comando('info','Inicio de la tarea programada ['.$this->argument('id').']'.__CLASS__);
        //Sacamos los parametros de la tarea
        $tarea=tareas::find($this->argument('id'));
        $parametros=json_decode($tarea->val_parametros);
        $procesar_colectivos=valor($parametros,"procesar_colectivos");
        $procesar_dep=valor($parametros,"procesar_dep");
        $procesar_usu=valor($parametros,"procesar_usu");
        $edificios_procesar=valor($parametros,"id_edificio");
        $cod_nivel=valor($parametros,"cod_nivel");
        $cod_nivel_supervisor=valor($parametros,"cod_nivel_supervisor");
        

        
        
        //Peticion de datos de workday
        $this->escribelog_comando('debug','GET '.config('app.workday_url'));
        $response=Http::withOptions(['verify' => false])
            ->withHeaders(['Accept' => 'text/xml', 'Content-Type' => 'text/xml'])
            ->withBasicAuth(config('app.user_workday'),config('app.pass_workday'))
            ->get(config('app.workday_url'));
        if($response->status()!=200){
            $this->escribelog_comando('error','Error en la respuesta de workday:');
        }  else {
            
            $response=$response->body();
            $response=str_replace('wd:','',$response);
            $xml = simplexml_load_string($response,'SimpleXMLElement',LIBXML_NOCDATA);
            $json = json_encode($xml);
            $array = json_decode($json,TRUE);
            $datos=Collect(array_values($array)[0]);
            $this->escribelog_comando('info','Datos de workday recibidos');
            //Procesamos los datos
            //DEPARTAMENTOS
            if($procesar_dep==1){
                $this->escribelog_comando('info','Procesando DEPARTAMENTOS');
                foreach($datos as $item){
                    //dd($item['CEO-1']['ID'][1]);  //Id Externo de departamento
                    //dd($item['CEO-1']['@attributes']['Descriptor']); //Nombre de departamento
                    $des_edificio=$item['CENTRO_DE_TRABAJO']['@attributes']['Descriptor']??null;
                    if(isset($des_edificio) && in_array($des_edificio,$edificios_procesar)){
                        for($i=1;$i<6;$i++){
                            if(isset($item['CEO-'.$i])){
                                $nom_departamento=$this->nombre_dep($item['CEO-'.$i]['@attributes']['Descriptor']);
                                $id_externo=$item['CEO-'.$i]['ID'][1];
                                $departamento=departamentos::where('id_externo',$id_externo)->where('id_cliente',$tarea->clientes)->first();
                                if(!$departamento){
                                    $this->insertar_dep($nom_departamento,null,$tarea->clientes,1,$id_externo);
                                    $this->escribelog_comando('info','Departamento creado: '.$nom_departamento);
                                } else {
                                    $departamento->sync_at=Carbon::now();
                                    $departamento->save();
                                }
                            }
                        }
                    }
                    
                }
                $this->escribelog_comando('info','Actualizando jerarquia de departamentos');
                foreach($datos as $item){
                    $des_edificio=$item['CENTRO_DE_TRABAJO']['@attributes']['Descriptor']??null;
                    if(isset($des_edificio) && in_array($des_edificio,$edificios_procesar)){
                        for($i=2;$i<6;$i++){
                            if(isset($item['CEO-'.$i])){
                                $id_externo=$item['CEO-'.$i]['ID'][1];
                                $departamento=departamentos::where('id_externo',$id_externo)->where('id_cliente',$tarea->clientes)->first();
                                $id_padre=$item['CEO-'.($i-1)]['ID'][1];
                                $departamento_padre=departamentos::where('id_externo',$id_padre)->where('id_cliente',$tarea->clientes)->first();
                                if($departamento && $departamento_padre){
                                    if($departamento->cod_departamento_padre!=$departamento_padre->cod_departamento){
                                        $departamento->cod_departamento_padre=$departamento_padre->cod_departamento;
                                        $departamento->num_nivel=$i;
                                        $departamento->save();
                                        $this->escribelog_comando('info','Departamento actualizado: '.$departamento->nom_departamento);
                                    }
                                }
                                
                            }
                        }
                    }
                }
                // //Borramos los que sobran
                departamentos::where('id_cliente',$tarea->clientes)->where('sync_at','<',Carbon::now()->subminutes(2))->delete();
            }

            //COLECTIVOS (CECOS)
            if($procesar_colectivos==1){
                $this->escribelog_comando('info','Procesando CECOS');
                foreach($datos as $item){
                    $des_edificio=$item['CENTRO_DE_TRABAJO']['@attributes']['Descriptor']??null;
                    if(isset($des_edificio) && in_array($des_edificio,$edificios_procesar)){
                        $des_coletivo=$item['CECOS']['@attributes']['Descriptor']??null;
                        $id_externo=$item['CECOS']['ID'][1];
                        $colectivo=colectivos::where('id_externo',$id_externo)->where('id_cliente',$tarea->clientes)->first();
                        if(!$colectivo){
                            $this->insertar_cole($des_coletivo,$tarea->clientes,$id_externo);
                            $this->escribelog_comando('info','Colectivo creado: '.$des_coletivo);
                        } else {
                            $colectivo->sync_at=Carbon::now();
                            $colectivo->save();
                        }
                    }
                }
                // //Borramos los que sobran
                colectivos::where('id_cliente',$tarea->clientes)->where('sync_at','<',Carbon::now()->subminutes(2))->delete();
            }

            //USUARIOS
            if($procesar_usu==1){
                $this->escribelog_comando('info','Procesando USUARIOS');
                $cod_nivel=niveles_acceso::find($cod_nivel);
                $cod_nivel_supervisor=niveles_acceso::find($cod_nivel_supervisor);
                foreach($datos as $item){
                    $turno=turnos::where('id_cliente',$tarea->clientes)->where('id_externo',$item['Turno_trabajo']??0)->first();
                    $des_edificio=$item['CENTRO_DE_TRABAJO']['@attributes']['Descriptor']??null;
                    if(isset($des_edificio) && in_array($des_edificio,$edificios_procesar)){
                        $nombre=$item['NOMBRE'].' '.($item['APELLIDO_1']??'').' '.($item['APELLIDO_2']??'');
                        $id_externo=$item['CODIGO_EMPLEADO'];
                        $email=$item['MAIL']??null;
                        $puesto=$item['PUESTO']??null;
                        $planta=$item['PLANTA']??null;
                        $email=isset($email)?strtolower($email):null;
                        $usuario=users::where('id_cliente',$tarea->clientes)
                            ->where(function($q) use($email,$id_externo){
                                $q->where('id_usuario_externo',$id_externo)
                                ->orWhere('email',$email);
                            })
                            ->first();
                        if($usuario){
                            $usuario=$usuario->id;
                        } else {
                            $usuario=null;
                        }
                        $colectivo=$item['CECOS']['ID'][1];
                        $colectivo=colectivos::where('id_externo',$colectivo)->where('id_cliente',$tarea->clientes)->first();
                        if($colectivo){
                            $colectivo=$colectivo->cod_colectivo;
                        } else {
                            $colectivo=null;
                        }
                        $edificio=edificios::where('des_edificio',$des_edificio)->where('id_cliente',$tarea->clientes)->first();
                        if($edificio){
                            $edificio=$edificio->id_edificio;
                        } else {
                            $edificio=null;
                        }

                        //Caso pedrosa<->torrecerda
                        if($tarea->clientes==5 && $edificio==101){
                            $edificio=41;
                        }
                        //Caso madrid central<->MGM
                        if($tarea->clientes==5 && $edificio==97){
                            $edificio=92;
                        }
                        $departamento=null;
                        for($i=5;$i>0;$i--){
                            if(isset($item['CEO-'.$i])){
                                $departamento=$item['CEO-'.$i]['ID'][1];
                                $departamento=departamentos::where('id_externo',$departamento)->where('id_cliente',$tarea->clientes)->first();
                                if($departamento){
                                    $departamento=$departamento->cod_departamento;
                                    break;
                                }
                            }
                        }
                        
                        //Si antes tenia un nivel superior (administrador) se lo mantenemos
                        if($usuario){
                            $user=users::find($usuario);
                            if($user->nivel_acceso>=$cod_nivel->val_nivel_acceso){
                                $nivel=niveles_acceso::find($user->cod_nivel);
                            } else {
                                if($item['Es_gerente-manager']==0){
                                    $nivel=$cod_nivel;
                                } else {
                                    $nivel=$cod_nivel_supervisor;
                                }
                            }
                        }
                        if($email){
                            $this->insertar_usu($usuario,$nombre,$tarea->clientes,$id_externo,$colectivo,$departamento,$nivel,$email,$edificio,$turno,$puesto,$planta,$tarea->id_tarea);
                            $this->escribelog_comando('info',$usuario==null?'Usuario creado: '.$nombre:'Usuario actualizado: '.$nombre.' ['.$nivel->cod_nivel.']');
                        } else {
                            $this->escribelog_comando('error','Usuario no creado, no tiene email: '.$nombre);
                        }
                    }
                }

                //Procesar supervisores
                $this->escribelog_comando('info','Actualizando supervisores');
                foreach($datos as $item){
                    $des_edificio=$item['CENTRO_DE_TRABAJO']['@attributes']['Descriptor']??null;
                    if(isset($des_edificio) && in_array($des_edificio,$edificios_procesar)){
                        for($i=5;$i>0;$i--){
                            if(isset($item['CEO-'.$i])){
                                $supervisor=$this->nombre_sup($item['CEO-'.$i]['@attributes']['Descriptor']);
                                $supervisor=users::where('name',$supervisor)->where('id_cliente',$tarea->clientes)->first();
                                $nom_supervisor=isset($supervisor->name)?$supervisor->name:null;
                                if($supervisor){
                                    $supervisor=$supervisor->id;
                                    $usuario=users::where('id_usuario_externo',$item['CODIGO_EMPLEADO'])->where('id_cliente',$tarea->clientes)->first();
                                    $nom_usuario=isset($usuario->name)?$usuario->name:null;
                                    if($usuario && $usuario->id!=$supervisor){
                                        $usuario->id_usuario_supervisor=$supervisor;
                                        $usuario->save();
                                        $this->escribelog_comando('debug',$nom_supervisor.' -> '.$nom_usuario);
                                        break;
                                    } else {
                                        $this->escribelog_comando('debug','No se ha encontrado usuario para asignar supervisor: '.$nom_usuario.' -> '.$nom_supervisor);
                                    }
                                } 
                            }
                        }
                    }
                }
                

                // //Borramos los que sobran
                //users::where('id_cliente',$tarea->clientes)->where('sync_at','<',Carbon::now()->subminutes(100))->update(['deleted_at'=>Carbon::now()]);

                //Actualizamos el edificio TORRECERDA
                DB::table('users')->where('id_edificio',101)->update(['id_edificio'=>41]);
            }
            $this->escribelog_comando('info','Fin de la tarea programada ['.$this->argument('id').']'.__CLASS__);
        }

        $tarea->fec_ult_ejecucion=Carbon::now();
        $tarea->save();
        $this->escribelog_comando('info','Fin de la tarea '.__CLASS__);
        //$this->info('Hola soy un ejemplo: '.$this->argument('id'));  //Puede ser info, error o list
    }
}

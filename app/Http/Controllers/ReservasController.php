<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Str;
use Auth;
use App\Models\reservas;
use App\Models\puestos;
use App\Models\users;
use App\Models\niveles_acceso;
use App\Models\puestos_tipos;
use stdClass;
use Spatie\IcalendarGenerator\Components\Calendar;
use Spatie\IcalendarGenerator\Components\Event;
use Illuminate\Support\Facades\Log;

class ReservasController extends Controller
{
    ///////////////FUNCIONES AUXILIARES/////////////////////////////
    public static function festivos_usuario($id){
        //Lista total de festivos del cliente para desactivarlos en el selector de fecha
        $ubicacion_usuario=DB::table('edificios')
            ->join('users','users.id_edificio','edificios.id_edificio')
            ->join('provincias','provincias.id_prov','edificios.id_provincia')
            ->where('users.id',$id)
            ->first();
        $festivos_usuario = DB::select("
            SELECT
            date_format(val_fecha,'%Y-%m-%d') as fecha
        FROM festivos
        WHERE (FIND_IN_SET(CONVERT(IFNULL(". ($ubicacion_usuario->id_prov??0).",-1),char), cod_provincia ) <> 0
            OR FIND_IN_SET(CONVERT(IFNULL(". ($ubicacion_usuario->cod_pais??0).",-1),char), cod_pais) <> 0
            OR FIND_IN_SET(CONVERT(IFNULL(". ($ubicacion_usuario->cod_region??0).",-1),char), cod_region) <> 0
            OR (FIND_IN_SET(CONVERT(IFNULL(". ($ubicacion_usuario->id_edificio??0).",-1),char),cod_centro) <> 0  OR (IFNULL(cod_centro,0) = 0))
            OR mca_nacional='S') 
            AND val_fecha>subdate(curdate(), (day(curdate())-1))
            AND id_cliente=".Auth::user()->id_cliente.";");
        $festivos_usuario=array_map(function($item){
            return $item->fecha;
        },$festivos_usuario);
        if (count($festivos_usuario)==0){
            $festivos_usuario=-1;
        } else {
            return js_array($festivos_usuario);
        }
       
    }

    ////////////////////////////////////////////////////////////////
    public function index(){
        return view('reservas.index');
    }

    public function loadMonthSchedule(Request $r)
    {
        if ($r->month)
            $month = Carbon::parse($r->month);
        else $month = Carbon::now()->startOfMonth();

        if ($r->type == 'add')
            $backMonth = $month->addMonth()->format('Y-n');
        elseif($r->type == 'sub')
            $backMonth = $month->subMonth()->format('Y-n');
        else $backMonth = $month->format('Y-n');
        $end=$month->copy()->endOfMonth();

        $festivos_mes=collect(estadefiesta(Auth::user()->id,$month,$end));

        
        $reservas=DB::table('reservas')
            ->selectraw('date(reservas.fec_reserva) as fec_reserva,reservas.fec_fin_reserva,puestos.cod_puesto,puestos.des_puesto,puestos.mca_incidencia,puestos.id_estado,edificios.des_edificio,plantas.des_planta,puestos_tipos.val_icono,puestos_tipos.val_color,plantas.factor_puestob,plantas.factor_puestoh,plantas.factor_puestow,plantas.factor_puestor,plantas.factor_letra')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->leftjoin('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where('reservas.id_usuario',Auth::user()->id)
            ->where('mca_anulada','N')
            ->where(function($q) use($month,$end){
                $q->where(function($q) use($month,$end){
                    $q->wherenull('fec_fin_reserva');
                    $q->where('fec_reserva',$month);
                });
                $q->orwhere(function($q) use($month,$end){
                    $q->whereraw("'".$month."' between fec_reserva AND fec_fin_reserva");
                    $q->orwhereraw("'".$end."' between fec_reserva AND fec_fin_reserva");
                    $q->orwherebetween('fec_reserva',[$month,$end]);
                    $q->orwherebetween('fec_fin_reserva',[$month,$end]);
                });
            })
            ->get();
        
        return view('reservas.calendario',compact('backMonth','reservas','festivos_mes'))->render();
    }

    public function create($fecha){
        
        $reserva=new reservas;
        $f1=Carbon::parse($fecha);
        $f2=Carbon::parse($fecha);
        $tipos = DB::table('puestos_tipos')
            ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
            ->where(function($q){
                $q->wherein('puestos_tipos.id_tipo_puesto',explode(",",Auth::user()->tipos_puesto_admitidos));
                $q->orwherein('puestos_tipos.id_tipo_puesto',config('app.tipo_puesto_publico'));
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                    if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                        $q->orwhere('puestos_tipos.mca_fijo','S');
                    }
                } else {
                    $q->where('puestos_tipos.id_cliente',session('CL')['id_cliente']);
                    if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                        $q->orwhere('puestos_tipos.mca_fijo','S');
                    }
                }
            })
            ->where(function($q){
                if(config_cliente('mca_salas',session('CL')['id_cliente'])=='S'){
                    $q->wherenotin('puestos_tipos.id_tipo_puesto',config('app.tipo_puesto_sala'));
                }
            })
            ->orderby('id_tipo_puesto')
            ->get();
        //Primero comprobamos si tiene una reserva para ese dia
        $misreservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q) use($f1,$f2){
                $q->where(function($q) use($f1,$f2){
                    $q->wherenull('fec_fin_reserva');
                    $q->where('fec_reserva',$f1);
                });
                $q->orwhere(function($q) use($f1,$f2){
                    $q->whereraw("'".$f1."' between fec_reserva AND fec_fin_reserva");
                    $q->orwhereraw("'".$f2."' between fec_reserva AND fec_fin_reserva");
                    $q->orwherebetween('fec_reserva',[$f1,$f2]);
                    $q->orwherebetween('fec_fin_reserva',[$f1,$f2]);
                });
            })
            ->where('mca_anulada','N')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();

        $plantas_usuario=DB::table('plantas_usuario')
            ->select('plantas.*')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->orwhere('plantas.mca_publica','S')
            ->get();

        $reserva->id_planta=0;
        $festivos_usuario=$this->festivos_usuario(Auth::user()->id);
        $perfil_usuario=niveles_acceso::find(Auth::user()->cod_nivel);

        return view('reservas.edit',compact('reserva','f1','f2','tipos','misreservas','plantas_usuario','festivos_usuario','perfil_usuario'));
    }

    public function edit($id){
        
        $reserva=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
            ->where('id_reserva',$id)
            ->first();

        $f1=Carbon::parse($reserva->fec_reserva);
        $f2=Carbon::parse($reserva->fec_fin_reserva);
        $tipos = DB::table('puestos_tipos')
            ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
            ->wherein('puestos_tipos.id_tipo_puesto',explode(",",Auth::user()->tipos_puesto_admitidos))
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos_tipos.id_cliente',Auth::user()->id_cliente);
                    if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                        $q->orwhere('puestos_tipos.mca_fijo','S');
                    }
                } else {
                    $q->where('puestos_tipos.id_cliente',session('CL')['id_cliente']);
                    if(config_cliente('mca_mostrar_datos_fijos')=='S'){
                        $q->orwhere('puestos_tipos.mca_fijo','S');
                    }
                }
            })
            ->where(function($q){
                if(config_cliente('mca_salas',session('CL')['id_cliente'])=='S'){
                    $q->wherenotin('puestos_tipos.id_tipo_puesto',config('app.tipo_puesto_sala'));
                }
            })
            ->orderby('id_tipo_puesto')
            ->get();
        //Primero comprobamos si tiene una reserva para ese dia
        $misreservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q) use($f1,$f2){
                $q->where(function($q) use($f1,$f2){
                    $q->wherenull('fec_fin_reserva');
                    $q->where('fec_reserva',$f1);
                });
                $q->orwhere(function($q) use($f1,$f2){
                    $q->whereraw("'".$f1."' between fec_reserva AND fec_fin_reserva");
                    $q->orwhereraw("'".$f2."' between fec_reserva AND fec_fin_reserva");
                    $q->orwherebetween('fec_reserva',[$f1,$f2]);
                    $q->orwherebetween('fec_fin_reserva',[$f1,$f2]);
                });
            })
            ->where('mca_anulada','N')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();

        $plantas_usuario=DB::table('plantas_usuario')
            ->select('plantas.*')
            ->leftjoin('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->orwhere('plantas.mca_publica','S')
            ->distinct()
            ->get();

        $festivos_usuario=$this->festivos_usuario(Auth::user()->id);
        $perfil_usuario=niveles_acceso::find(Auth::user()->cod_nivel);
        $edit=$reserva->id_reserva;

        return view('reservas.edit',compact('reserva','f1','f2','tipos','misreservas','plantas_usuario','festivos_usuario','perfil_usuario','edit'));
    }

    public function comprobar_puestos(Request $r){

        $plantas_usuario=DB::table('plantas')
            ->leftjoin('plantas_usuario','plantas.id_planta','plantas_usuario.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->orwhere('plantas.mca_publica','S')
            ->pluck('plantas.id_planta')
            ->unique()
            ->toArray();

        $edificios_usuario=DB::table('plantas')
            ->leftjoin('plantas_usuario','plantas_usuario.id_planta','plantas.id_planta')
            ->where(function($q){
                $q->where('plantas_usuario.id_usuario',Auth::user()->id);
                $q->orwhere('plantas.mca_publica','S');
            })
            ->pluck('id_edificio')
            ->unique()
            ->toArray();
       
        $usuario=DB::table('users')
            ->join('niveles_acceso','niveles_acceso.cod_nivel','users.cod_nivel')
            ->where('users.id',Auth::user()->id)
            ->first();

        //dd($r->all());
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);
        //Intervalo en minutos entre las dos hora en cualquier dia
        $h1=Carbon::parse($f1.' '.$r->hora_inicio);
        $h2=Carbon::parse($f1.' '.$r->hora_fin);
        $intervalo=$h2->diffInminutes($h1)/60;
        //Vamos a mirar que puestos estan disponibles todos los dias que ha solicitado el usuario
        $puestos_reservados=[];
        $period = CarbonPeriod::create($f1,$f2);
        
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where('puestos.id_edificio',$r->edificio)
            ->where(function($q) use($f1,$f2){
                $q->where(function($q) use($f1,$f2){
                    $q->wherenull('fec_fin_reserva');
                    $q->where('fec_reserva',$f1);
                });
                $q->orwhere(function($q) use($f1,$f2){
                    $q->whereraw("'".$f1."' between fec_reserva AND fec_fin_reserva");
                    $q->orwhereraw("'".$f2."' between fec_reserva AND fec_fin_reserva");
                    $q->orwherebetween('fec_reserva',[$f1,$f2]);
                    $q->orwherebetween('fec_fin_reserva',[$f1,$f2]);
                });
            })
            ->where('mca_anulada','N')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->get();
        $puestos_reservados=array_merge($puestos_reservados,$reservas->pluck('id_puesto')->toArray());
    
        $puestos_usuarios=[];
        //Vamos a mirar que puestos estan disponibles todos los dias que ha solicitado el usuario
        $period = CarbonPeriod::create($f1,$f2);
        foreach($period as $p){
            $asignados_usuarios=DB::table('puestos_asignados')
                ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
                ->join('users','users.id','puestos_asignados.id_usuario')
                // ->where('id_usuario','<>',Auth::user()->id)
                ->where(function($q){
                    if (!isAdmin()) {
                        $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->where(function($q) use($r,$p){
                    $q->wherenull('fec_desde');
                    $q->orwhereraw("'".$p."' between fec_desde AND fec_hasta");
                })
                ->get();
            if(isset($asignados_usuarios)){
                $puestos_usuarios=array_merge($puestos_usuarios,$asignados_usuarios->pluck('id_puesto')->toArray());
        }}
     
         
        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')
            ->where('id_perfil',Auth::user()->cod_nivel)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')
            ->where('id_perfil','<>',Auth::user()->cod_nivel)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();
        if(isset($asignados_nomiperfil)){
            $puestos_nomiperfil=$asignados_nomiperfil->pluck('id_puesto')->toArray();
        } else{
            $puestos_nomiperfil=[];
        }

        $puestos=DB::table('puestos')
            ->select('puestos.*','puestos.width as puesto_width', 'puestos.height as puesto_height','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color', 'puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo','puestos_tipos.val_dias_antelacion','puestos_tipos.mca_antelacion_obligatoria')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_puesto','>',0)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q) use($plantas_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('puestos.id_planta',$plantas_usuario??[]);
                }
            })
            ->where(function($q){
                if(!checkPermissions(['Mostrar puestos no reservables'],['R'])){
                    $q->where('puestos.mca_reservar','S');
                }
            })
            ->where(function($q) use($r){
                if($r->tipo_puesto>0){
                    $q->where('puestos.id_tipo_puesto',$r->tipo_puesto);
                }
            })
            ->where(function($q){
                $q->wherein('puestos.id_tipo_puesto',explode(",",Auth::user()->tipos_puesto_admitidos));
                $q->orwherein('puestos.id_tipo_puesto',config('app.tipo_puesto_publico'));
            })
            
            ->where(function($q) use($intervalo){
                if(session('CL')['mca_reserva_horas']=='S'){
                    $q->where('puestos.max_horas_reservar','>=',$intervalo);
                    $q->orwherenull('puestos.max_horas_reservar');
                    $q->orwhere('puestos.max_horas_reservar','0');
                    $q->orwhere('puestos.max_horas_reservar','');
                }
            })
            ->where(function($q) use($r){
                if($r->id_planta && $r->id_planta!=0){
                    $q->where('puestos.id_planta',$r->id_planta);
                }
            })
            ->where(function($q) use($r){
                if ($r->tags) {
                    if($r->andor=="true"){//Busqueda con AND
                        $puestos_tags=DB::table('tags_puestos')
                            ->select('id_puesto')
                            ->wherein('id_tag',$r->tags)
                            ->groupby('id_puesto')
                            ->havingRaw('count(id_tag)='.count($r->tags))
                            ->pluck('id_puesto')
                            ->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    } else { //Busqueda con OR
                        $puestos_tags=DB::table('tags_puestos')->wherein('id_tag',$r->tags)->pluck('id_puesto')->toarray();
                        $q->whereIn('puestos.id_puesto',$puestos_tags);
                    }
                }
            })

           
            //Indica si se quiere ver en el gestor de reservas todos los puestos, viendo cuales estan reservados y cuales no, o solo los disponibles segun los criterios dados D: Solo disponibles, T: Todos
            ->where(function($q) use($puestos_usuarios,$puestos_nomiperfil,$puestos_reservados){
                if(config_cliente('mca_mostrar_puestos_reservas')=='D'){
                    $q->wherenotin('puestos.id_estado',[4,5,6]);
                    $q->wherenotin('puestos.id_puesto',$puestos_usuarios);
                    $q->wherenotin('puestos.id_puesto',$puestos_nomiperfil);
                    $q->wherenotin('puestos.id_puesto',$puestos_reservados);
                }
            })
            //Ocultar de reserva los puestos que tengan incidencias
            ->where(function($q){
                if(session('CL')['mca_incidencia_reserva']=='N'){
                    $q->where('mca_incidencia','N');
                }
            })

            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

        //Ahora vamos a ver quepuestos no pueden ser reservados porque no cumplen la regla de maxima antelacion
        $festivos=collect(estadefiesta(Auth::user()->id,Carbon::now(),Carbon::parse($f2)->addDay(),0));
        $puestos_antelacion=$puestos->wherenotnull('val_dias_antelacion');
        $tipo_antelacion_cliente=config_cliente('mca_tipo_antelacion');
        $min_hora_reserva_cliente=config_cliente('min_hora_reservas');
        foreach($period as $p){
            foreach($puestos_antelacion as $pa){
                $dias_antelacion=$pa->val_dias_antelacion;
                
                //En caso de que la fecha en la que va el bucle sea festivo o no laborable, se añaden dias a los dias de antelacion hasta el siguiente dia laborable
                $periodo_antelacion=CarbonPeriod::create(Carbon::now()->addday(),$p->endOfDay());
                foreach($periodo_antelacion as $p2) {
                    $es_festivo=$p2->isWeekend() || $festivos->where('date',$p2->format('Y-m-d'))->where('festivo',1)->first();
                    if($es_festivo){
                        $p2->addDay();
                        $dias_antelacion++;
                    }
                }
                
                //Le añadimos a la fecha los dias de antelacion y 2 horas mas por cortesia
                // En funcion de la configuracion del cliente, se puede reservar desde las 00:00 del dia en cuestion o desde la hora minima de rango de horas de reserva del cliente, se establece con el campo mca_tipo_antelacion de la tabla config_clientes
                if($tipo_antelacion_cliente=='H'){
                    $min_inicial=$min_hora_reserva_cliente;
                } else {
                    $min_inicial=0;
                }
                
               
                $fecha_comparacion=Carbon::parse($p->format('Y-m-d'))->addminutes($min_inicial);
                $fecha_limite=Carbon::now()->setTimezone(Auth::user()->val_timezone)->addDays($dias_antelacion);
                //log::debug("fecha ".$fecha_comparacion." | Dias antelacion ".$dias_antelacion." dias | Fecha limite ".$fecha_limite." ");
                if(($fecha_limite<$fecha_comparacion)){
                    //Log::debug("quito puesto ".$pa->id_puesto." por antelacion");
                    $puesto_quitar=$puestos->where('id_puesto',$pa->id_puesto)->first();
                    if (($usuario->mca_saltarse_antelacion=='N') || ($puesto_quitar->mca_antelacion_obligatoria=='S')) //Si el usuario no tiene permiso para saltarse la antelacion o el puesto tiene la antelacion obligatoria, quitamos el puesto
                    {
                        //dump("entro ".$pa->cod_puesto." por antelacion " . $usuario->mca_saltarse_antelacion. " - " . $puesto_quitar->mca_antelacion_obligatoria);
                        $puesto_quitar->quitar=true;
                    }

                    
                }
            }
        }
        $puestos = $puestos->reject(function($item) {
            return isset($item->quitar);
        });

        //En el caso de que tenga restriccion de zonas en alguna planta, quitamos los puestos que no esten en esas zonas
        if(Auth::user()->list_zonas_admitidas!=null){
            //Primero a ver que plantas son
            $plantas=DB::table('plantas_zonas')->wherein('num_zona',explode(",",Auth::user()->list_zonas_admitidas))->pluck('id_planta')->toarray();
            foreach($plantas as $p){
                $puestos_zona=DB::table('puestos')
                    ->select('id_puesto','plantas.id_planta','plantas_zonas.val_ancho','plantas_zonas.val_alto','plantas_zonas.val_x','plantas_zonas.val_y','puestos.offset_top','puestos.offset_left','plantas.width','plantas.height')
                    ->join('plantas_zonas','plantas_zonas.id_planta','puestos.id_planta')
                    ->join('plantas','plantas.id_planta','plantas_zonas.id_planta')
                    ->where('puestos.id_planta',$p)
                    ->wherein('plantas_zonas.num_zona',explode(",",Auth::user()->list_zonas_admitidas))
                    ->where(function($q){  //Este rollo viene de que las posiciones de las zonas estan guardadas como valores absolutos sobre los pixeles reales de la imagen y las posiciones de los puestos estan guardadas como valores de porcentaje (offsettop y offsetleft) de las dimensiones de la imagen
                        $q->whereraw('puestos.offset_top between (100*plantas_zonas.val_y/plantas.`height`) AND ((100*plantas_zonas.val_y/plantas.`height`) + (100*plantas_zonas.val_alto/plantas.`height`))');
                        $q->whereraw('puestos.offset_left between (100*plantas_zonas.val_x/plantas.`width`) AND ((100*plantas_zonas.val_x/plantas.`width`) + (100*plantas_zonas.val_ancho/plantas.`width`))');
                    })
                    ->pluck('id_puesto')
                    ->toArray();
                    $puestos = $puestos->reject(function($item) use($puestos_zona,$p) {
                        return $item->id_planta==$p && !in_array($item->id_puesto,$puestos_zona);
                    });
            }
        }

        $edificios=DB::table('edificios')
            ->select('id_edificio','des_edificio')
            ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
            ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('edificios.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q) use($edificios_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('edificios.id_edificio',$edificios_usuario??[]);
                }
            })
            ->where('id_edificio',$r->edificio)
            ->get();

        $tipo_vista=$r->tipo;
        $id_planta=$r->id_planta;

        //Actualizadmos la config del usaurio para mostrarle siempre ese tipo de vista
        $u=users::find(Auth::user()->id);
        $u->val_vista_puestos=$r->tipo;
        $u->save();

        if(isset($r->ajax)){
            $r->tipo='comprobar_ajax';
        }
  
        return view('reservas.'.$r->tipo,compact('reservas','puestos','edificios','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','plantas_usuario','tipo_vista','id_planta','r'));
    }

    public function save(Request $r){
        $f = explode(' - ',$r->fechas);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);
        $mensajes_error=[];
        
        $puesto=puestos::find($r->id_puesto);
        $tipopuesto=puestos_tipos::find($puesto->id_tipo_puesto);
        if($puesto->mca_reservar=='N'){
            $mensajes_error[]='El puesto seleccionado no admite reservas';
        }
        $period = CarbonPeriod::create($f1,$f2);
        foreach($period as $p){
            $fec_desde=Carbon::parse($p->format('Y-m-d').' '.$r->hora_inicio);
            $fec_hasta=Carbon::parse($p->format('Y-m-d').' '.$r->hora_fin);
            //Si en el perfil le hemos puesto que pude reservar varios puestos del tipo no comprobamos si ya estaba pillado

            //Ahora hay que comprobar que no han pillado el puesto mientras el usuario se lo pensabe
            $ya_esta=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->where('puestos.id_puesto',$puesto->id_puesto)
                ->where(function($q) use($r,$fec_desde,$fec_hasta){
                    $q->where(function($q) use($fec_desde,$fec_hasta,$r){
                        $q->wherenull('fec_fin_reserva');
                        $q->where('fec_reserva',adaptar_fecha($r->fecha));
                    });
                    $q->orwhere(function($q) use($fec_desde,$fec_hasta,$r){
                        $q->whereraw("'".$fec_desde->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                        $q->orwhereraw("'".$fec_hasta->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                        $q->orwherebetween('fec_reserva',[$fec_desde,$fec_hasta]);
                        $q->orwherebetween('fec_fin_reserva',[$fec_desde,$fec_hasta]);
                    });
                })
                ->where('mca_anulada','N')
                ->where('id_reserva','<>',$r->id_reserva)
                ->where(function($q){
                    if (!isAdmin()) {
                        $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->first();

            if($ya_esta){//Ya esta pillado
                $mensajes_error[]='El espacio no esta disponible en el horario ['.beauty_fecha($fec_desde).' - '.beauty_fecha($fec_hasta).'] elija otro'.chr(13);
            }

        }
        if(count($mensajes_error)>0){
            return [
                'title' => "Reservas",
                'alert' => implode('<br>'.chr(13),$mensajes_error)
                //'url' => url('sections')
            ];
        }

        //Anulamos la reserva anterior si exsiste
        if($r->id_reserva!=null){
            $res=reservas::find($r->id_reserva);
            $pin=$res->pin;
           savebitacora('Eliminada reserva  '.$r->id_reserva.' por modificacion',"Reservas","save","OK");
        } else {
            //Pin nuevo
            $res=new reservas;
            $pin=strtoupper(str::random(6));
        }
        //Si todo ha ido bien y no hemos devuelto ningun mensaje de error entonces podemos insertar];
        
        
        $res->id_puesto=$r->id_puesto;
        $res->id_usuario=Auth::user()->id;
       
        if($tipopuesto->mca_reserva_horas=='S'){
            $fec_desde=Carbon::parse($f1.' '.$r->hora_inicio);
            $fec_hasta=Carbon::parse($f2.' '.$r->hora_fin);
            $res->fec_reserva=$fec_desde;
            $res->fec_fin_reserva=$fec_hasta;
        } else {
            $res->fec_reserva=$f1;
            $res->fec_fin_reserva=$f2;
        }

        $res->id_cliente=Auth::user()->id_cliente;
        $res->pin=$pin;
        $res->nombre=$r->nombre;
        $res->email=$r->email;
        $res->save();
        savebitacora('Espacio '.$puesto->cod_puesto.' reservado. Identificador de reserva: '.$res->id_reserva,"Reservas","save","OK");

        enviar_mail_reserva($res->id_reserva,$r->mca_ical);
        $reservas=\App\Http\Controllers\UsersController::mis_puestos(auth()->user()->id)['mispuestos'];
        session(['reservas'=>$reservas]);

        return [
            'title' => "Reservas",
            'mensaje' => 'Puesto '.$puesto->cod_puesto.' reservado. Identificadores de reserva: '.$res->id_reserva.' <br> PIN para acceso '.$pin.(isset($r->email)?'<br>Se ha enviado un email con los datos de la reserva a '.$r->email:'<br>No se han indicado datos de email del cliente'),
            'fecha' => '['.$fec_desde->format('d/m/Y H:i').' - '.$fec_hasta->format('d/m/Y H:i').']',
            'fec_ver' => $fec_desde->format('Y-m-d'),
            //'url' => url('puestos')
            'timeout'=>120
        ];
    
    }

    public function delete(Request $r){
        $reserva=reservas::where('id_usuario',Auth::user()->id)->where('id_reserva',$r->id)->first();
        
        if(isset($reserva)){
            $reserva->delete();
            savebitacora('Reserva del puesto '.$r->des_puesto.' reservado para el dia '.$r->fecha.' Cancelada',"Reservas","delete","OK");
            insertar_notificacion_web($reserva->id_usuario,2,'Reserva del puesto '.$r->des_puesto.' reservado para el dia '.$r->fecha.' Cancelada',$reserva->id_reserva);
            $reservas=\App\Http\Controllers\UsersController::mis_puestos(auth()->user()->id)['mispuestos'];
            session(['reservas'=>$reservas]);
            return [
                'title' => "Reservas",
                'mensaje' => 'Reserva del puesto '.$r->des_puesto.' reservado para el dia '.$r->fecha.' Cancelada',
                //'url' => url('puestos')
            ];
        } else {
            return [
                'title' => "Reservas",
                'error' => 'La reserva no existe o no es suya',
                //'url' => url('sections')
            ];
        }
    }

    public function puestos_usuario(Request $r, $id,$desde,$hasta){
        $usuario=users::find($id);

        $plantas_usuario=DB::table('plantas_usuario')
            ->where('id_usuario',$id)
            ->pluck('id_planta')
            ->toArray();

        $edificios=DB::table('edificios')
        ->select('id_edificio','des_edificio')
        ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
        ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
        ->where('edificios.id_cliente',$usuario->id_cliente)
        ->get();

        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q) use($desde,$hasta){
                //$q->whereraw("date(fec_reserva) between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->orwhere(function($q) use($desde,$hasta){
                    $q->whereraw("'".$desde."' between fec_reserva AND fec_fin_reserva");
                    $q->orwhereraw("'".$hasta."' between fec_reserva AND fec_fin_reserva");
                    $q->orwherebetween('fec_reserva',[$desde,$hasta]);
                    $q->orwherebetween('fec_fin_reserva',[$desde,$hasta]);
                });
            })
            ->where('reservas.id_cliente',$usuario->id_cliente)
            ->get();
            
        if(isset($reservas)){
            $puestos_reservados=$reservas->pluck('id_puesto')->toArray();
        } else{
            $puestos_reservados=[];
        }

        $puestos_reservados=$reservas->pluck('id_puesto')->unique()->toArray();

        //Estas tres querys hacen falta para la compatibilidad de la vista
        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->wherenotnull('id_usuario')
            ->where(function($q) use($desde,$hasta){
                $q->whereraw("date(fec_Desde) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->whereraw("date(fec_hasta) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->orwhere(function($q){
                    $q->wherenull('fec_desde');
                    $q->wherenull('fec_hasta');
                });    
            })
            ->get();
        if(isset($asignados_usuarios)){
            $puestos_usuarios=$asignados_usuarios->pluck('id_puesto')->toArray();
        } else{
            $puestos_usuarios=[];
        }

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',$usuario->id_perfil)
            ->where(function($q) use($desde,$hasta){
                $q->whereraw("date(fec_Desde) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->whereraw("date(fec_hasta) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->orwhere(function($q){
                    $q->wherenull('fec_desde');
                    $q->wherenull('fec_hasta');
                });    
            })
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',$usuario->id_perfil)
            ->where(function($q) use($desde,$hasta){
                $q->whereraw("date(fec_Desde) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->whereraw("date(fec_hasta) not between '".Carbon::parse($desde)->format('Y-m-d')."' AND '".Carbon::parse($hasta)->format('Y-m-d')."'");
                $q->orwhere(function($q){
                    $q->wherenull('fec_desde');
                    $q->wherenull('fec_hasta');
                });    
            })
            ->get();
        if(isset($asignados_nomiperfil)){
            $puestos_nomiperfil=$asignados_nomiperfil->pluck('id_puesto')->toArray();
        } else{
            $puestos_nomiperfil=[];
        }

        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color')
            ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('puestos.id_cliente',$usuario->id_cliente)
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $puestos_usuario=DB::table('puestos_usuario_supervisor')->where('id_usuario',Auth::user()->id)->pluck('id_puesto')->toArray();
                    $q->wherein('puestos.id_puesto',$puestos_usuario);
                }
            })
            ->when($puestos_reservados, function($q) use($puestos_reservados){
                $q->wherenotin('id_puesto',$puestos_reservados);
            })
            ->when($puestos_usuarios, function($q) use($puestos_usuarios){
                $q->wherenotin('id_puesto',$puestos_usuarios);
            })
            ->when($puestos_nomiperfil, function($q) use($puestos_nomiperfil){
                $q->wherenotin('id_puesto',$puestos_nomiperfil);
            })
            ->where(function($q) use($plantas_usuario){
                if(session('CL') && session('CL')['mca_restringir_usuarios_planta']=='S'){
                    $q->wherein('puestos.id_planta',$plantas_usuario??[]);
                }
            })
            ->where(function($q){
                if(session('CL')['mca_incidencia_reserva']=='N'){
                    $q->where('mca_incidencia','N');
                }
                
            })
            ->where('puestos_tipos.mca_reserva_masiva','S')
            ->wherenotin('puestos.id_estado',[4,5,6])
            ->where(function($q) use($r){
                if(isset($r->lista_id) && is_array($r->lista_id)){
                    $q->wherein('puestos.id_puesto',$r->lista_id);
                }
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();


        $puestos_check=[];
        $checks=0; //Para que la vista muestre los checkbox
        $id_check=$id;
        $url_check="users/add_puesto_usuario/";
        if(isset($r->lista_id) && is_array($r->lista_id)){ //Si viene por POST es que estamos en reserva multiple desde listado de puestos
            return [
                'title' => "Reserva multiple",
                'message' => $puestos->count().' puestos disponibles de '.count($r->lista_id).' puestos solicitados para para el periodo  '.beauty_fecha($r->desde).' - '.beauty_fecha($r->hasta),
                'recomendacion'=>$puestos->count()!=count($r->lista_id)?'Libere primero las reservas existentes en los puestos y compruebe que los puestos pueden ser reservados,  o reserve solo los puestos disponibles':'',
                'lista'=>$puestos->pluck('id_puesto')->toArray()
            ]; 
        } else {  //Si viene por GET es que estamos en usuario ->crear reserva
            return view('puestos.content_mapa',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','checks','puestos_check','id_check','url_check'));
        }
            
         
        
    }

    public function asignar_reserva_multiple(Request $r){

        $usuario=users::find($r->id_usuario[0]);
        $puesto=puestos::find($r->puesto);
        $period = CarbonPeriod::create(Carbon::parse($r->desde), Carbon::parse($r->hasta));
        $festivos_periodo=collect(estadefiesta($usuario->id,Carbon::parse($r->desde),Carbon::parse($r->hasta)));
        // $festivos_periodo = DB::select( DB::raw("
        //     WITH RECURSIVE t as (
        //         select date('".Carbon::parse($r->desde)->format('Y-m-d')."') as date,
        //         estadefiesta(".$usuario->id.",'".Carbon::parse($r->desde)->format('Y-m-d')."') as festivo
        //     UNION
        //         SELECT DATE_ADD(t.date, INTERVAL 1 DAY), estadefiesta(".$usuario->id.",DATE_ADD(t.date, INTERVAL 1 DAY)) as festivo FROM t WHERE DATE_ADD(t.date, INTERVAL 1 DAY) <= '".Carbon::parse($r->hasta)->format('Y-m-d')."'
        //     )select * FROM t;"));

        // $festivos_periodo=collect($festivos_periodo);
        $cuenta=0;
        foreach($period as $fecha){
            if($festivos_periodo->where('date',$fecha->format('Y-m-d'))->first()->festivo==0){
                $res=new reservas;
                $res->id_puesto=$r->puesto;
                $res->id_usuario=$usuario->id;
                $res->fec_reserva=$fecha;
                //Si no vienen horas las ponemos a 0
                $res->fec_reserva->hour=0;
                $res->fec_reserva->minute=0;
                $res->fec_reserva->second=0;
                if($puesto->max_horas_reservar && $puesto->max_horas_reservar>0 && is_int($puesto->max_horas_reservar) && session('CL')['mca_reserva_horas']=='S'){
                    $res->fec_fin_reserva=$res->fec_reserva->addHour($puesto->max_horas_reservar);   
                }
                $res->id_cliente=$usuario->id_cliente;
                $res->save();
                $cuenta++;
            }
        }
        savebitacora('Reserva  de puesto '.$r->des_puesto.' creada para el usuario '.$usuario->name.' para el periodo  '.$r->desde.' - '.$r->hasta.". ".$cuenta." dias reservados","Reservas","asignar_reserva_multiple","OK");
        $str_notificacion=Auth::user()->name.' ha creado una reserva  del puesto '.$r->des_puesto.' para usted en el periodo  '.$r->desde.' - '.$r->hasta.". ".$cuenta." dias reservados. Identificador de reserva: #".$res->id_reserva."";
        notificar_usuario($usuario,"Se le ha reservado un nuevo puesto",'emails.mail_reserva',$str_notificacion,metodos_notificacion_usuario($usuario->id),2,[],$res->id_reserva);
        return [
            'title' => "Reservas",
            'message' => 'Reserva  de puesto '.$r->des_puesto.' creada para el usuario '.$usuario->name.' para el periodo  '.$r->desde.' - '.$r->hasta.". ".$cuenta." dias reservados",
            //'fecha' => Carbon::parse(adaptar_fecha($r->fechas))->format('Ymd'),
            //'url' => url('puestos')
        ];
    }

    public function cancelar_reserva_puesto($id){
        $p=DB::table('puestos')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$id)
            ->first();
        
        if(!isset($p)){
            //Error puesto no encontrado
            return [
                'tipo'=>'ERROR',
                'mensaje'=>"Error, puesto no encontrado"
            ];
        } else {    
            //A ver si tiene reserva
            $reserva=DB::table('reservas')
                ->where('id_puesto',$p->id_puesto)
                ->where(function($q){
                    $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                    $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
                })
                ->get();

            if($reserva->count()==1){
                $res=reservas::find($reserva->first()->id_reserva);
                $res->mca_anulada='S';
                $res->fec_utilizada=Carbon::now();
                $res->save();
                savebitacora("Cancelada reserva ".$res->id_reserva. "por el administrador","Reservas","cancelar_reserva_puesto","OK");
                insertar_notificacion_web($res->id_usuario,2,"Cancelada reserva ".$res->id_reserva. " del ".$reserva->fec_reserva." por el administrador",$res->id_reserva);
                return [
                    'tipo'=>'OK',
                    'mensaje'=>"Reserva ".$res->id_reserva." cancelada",
                    'id'=>$p->id_puesto
                ];
            } else if($reserva->count()>1) {
            return [
                'tipo'=>'Warning',
                'mensaje'=>"El puesto tiene varias reservas para hoy",
                'reservas'=>$reserva
            ];
            } else {
                return [
                    'tipo'=>'OK',
                    'mensaje'=>"El puesto no tenia reservas para hoy"
                ];
            }
         }
    }

    public function reservas_multiples_admin(Request $r){
        ini_set('max_execution_time', 300); //300 seconds = 5 minutes
        $f = explode(' - ',$r->rango);
        $f1 = Carbon::parse(adaptar_fecha($f[0]));
        $f2 = Carbon::parse(adaptar_fecha($f[1]));

        //Primero la accion, a ver si hay que borrar o crear
        if ($r->accion=="D"){//Borrar
            //El borrado es mas simple, nos cepillamos todo 
            $reservas=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->wherein('puestos.id_puesto',$r->lista_id)
                ->wherebetween('fec_reserva',[$f1,$f2])
                ->where(function($q){
                    if (!isAdmin()) {
                        $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->get();
            foreach($reservas as $res){
                DB::table('reservas')->where('id_reserva',$res->id_reserva)->delete();
                $user_puesto=users::find($res->id_usuario);
                $str_notificacion=Auth::user()->name.' ha realizado una cancelacion masiva de reservas ';
                $str_respuesta=' Se ha cancelado su reserva de puesto que tenía para el día '.Carbon::parse($res->fec_reserva)->format('d/m/Y');
                notificar_usuario($user_puesto,"Se han producido cambios en su reserva de puesto",'emails.mail_reserva',$str_notificacion.$str_respuesta,metodos_notificacion_usuario($user_puesto->id),2,[],$res->id_reserva); 
            }
            savebitacora(" Se han eliminado las reservas para ".count($r->lista_id).'puestos ['.implode(",",$r->lista_id).']en el intervalo'.$r->rango.' por cancelacion masiva creada por '.Auth::user()->name,"Usuarios","reservas_multiples_admin","OK");
            return [
                'tipo'=>'OK',
                'title'=>'Cancelacion masiva de reservas',
                'mensaje'=>"Se han eliminado las reservas para ".count($r->lista_id).' puestos en el intervalo '.$r->rango
            ];

        } else {
            $period = CarbonPeriod::create($f1, $f2);
            $lista_puestos_pillados=[];
            $lista_nombres_puestos=[];
            $hora_inicio=$r->hora_inicio??'00:00:00';
            $hora_fin=$r->hora_fin??'23:59:59';
            foreach($period as $fecha){
                foreach($r->lista_id as $id_puesto){
                    //Ahora hay que comprobar que no han pillado el puesto mientras el usuario se lo pensabe
                    $ya_esta=DB::table('reservas')
                        ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                        ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
                        ->join('users','reservas.id_usuario','users.id')
                        ->where('puestos.id_puesto',$id_puesto)
                        ->where(function($q) use($hora_inicio,$hora_fin,$fecha){
                            $q->where(function($q) use($fecha){
                                $q->wherenull('fec_fin_reserva');
                                $q->wheredate('fec_reserva',$fecha);
                            });
                            $q->orwhere(function($q) use($fecha){
                                $q->whereraw("'".$fecha->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                            });
                            $q->orwhere(function($q) use($hora_inicio,$hora_fin,$fecha){
                                $q->whereraw("'".$fecha->format('Y-m-d')." ".$hora_inicio."' between fec_reserva AND fec_fin_reserva");
                                $q->orwhereraw("'".$fecha->format('Y-m-d')." ".$hora_fin."' between fec_reserva AND fec_fin_reserva");
                                $q->orwherebetween('fec_reserva',[$fecha->format('Y-m-d').' '.$hora_inicio,$fecha->format('Y-m-d').' '.$hora_fin]);
                                $q->orwherebetween('fec_fin_reserva',[$fecha->format('Y-m-d').' '.$hora_inicio,$fecha->format('Y-m-d').' '.$hora_fin]);
                            });
                        })
                        ->where('reservas.id_usuario','<>',$r->id_usuario)
                        ->where('mca_anulada','N')
                        ->where('puestos.id_cliente',Auth::user()->id_cliente)
                        ->first();

                    $asignados_usuarios=DB::table('puestos_asignados')
                        ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
                        ->join('users','users.id','puestos_asignados.id_usuario')    
                        ->where('puestos.id_puesto',$id_puesto)
                        ->where(function($q){
                            $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                        })
                        ->where(function($q) use($r,$fecha){
                            $q->wherenull('fec_desde');
                            $q->orwhereraw("'".$fecha."' between fec_desde AND fec_hasta");
                        })
                        ->first();

                    if(!isset($ya_esta) && !isset($asignados_usuarios)){
                        //Vale, podemos jugar
                        $puesto=puestos::find($id_puesto);
                        $lista_nombres_puestos[]=$puesto->cod_puesto;
                        $res=new reservas;
                        $res->id_puesto=$id_puesto;
                        $res->id_usuario=$r->id_usuario;
                        $res->fec_reserva=Carbon::parse($fecha->format('Y-m-d').' '.$hora_inicio);
                       
                        if($puesto->max_horas_reservar && $puesto->max_horas_reservar>0 && is_int($puesto->max_horas_reservar) && session('CL')['mca_reserva_horas']=='S'){
                            $res->fec_fin_reserva=$res->fec_reserva->addHour($puesto->max_horas_reservar);   
                        } else {
                            $res->fec_fin_reserva=carbon::parse($fecha->format('Y-m-d').' '.$hora_fin);   
                        }
                        $res->id_cliente=$puesto->id_cliente;
                        $res->save();
                    } else {
                        $lista_puestos_pillados[]=$id_puesto;
                    }
                    $usuario=users::find($r->id_usuario);
                    $str_notificacion=Auth::user()->name.' ha creado una nueva reserva de los puestos '.implode(",",$lista_nombres_puestos).' para usted en el intervalo '.$r->rango;
                    notificar_usuario($usuario,"Reserva de puesto creada por administrador | Se le ha reservado un nuevo puesto",'emails.mail_reserva',$str_notificacion,metodos_notificacion_usuario($usuario->id),2,[],$res->id_reserva);
                    $mensaje="Se han creado ".count($lista_nombres_puestos)." reservas de ".(count($r->lista_id)-count(array_unique($lista_puestos_pillados))).' puestos para el usuario '.$usuario->name.' y el intervalo '.$r->rango;
                    if(count($lista_puestos_pillados)>0){
                        $mensaje.="<br>Se detectaron ".count($lista_puestos_pillados)." conflictos con otras reservas realizadas por otros usuarios o puestos asignados a nominalmente a usuarios durante este proceso en alguna de las fechas, por lo tanto no pueden ser reservados";
                    }
                    savebitacora(' Se han creado reservas de puesto para los puestos'.implode(",",$lista_nombres_puestos).' para el usuario '.$usuario->name.' para el periodo  '.$r->rango.' creada por '.Auth::user()->name,"Usuarios","reservas_multiples_admin","OK");
                }
            }
            return [
                'tipo'=>'OK',
                'title'=>'Creacion masiva de reservas',
                'mensaje'=>$mensaje,
                'pillados'=>array_unique($lista_puestos_pillados)
            ];
        }
    }

    public function slots($id,$id_reserva){
        $tipo=puestos_tipos::find($id);
        $reserva=reservas::find($id_reserva);
        $slots=collect(json_decode($tipo->slots_reserva))->sortby('hora_inicio');

        return view ('reservas.fill_slots_reserva',compact('slots','tipo','reserva'));
    }
}

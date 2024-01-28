<?php

namespace App\Http\Controllers;

use App\Models\reservas;
use Illuminate\Http\Request;
use Image;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
Use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Str;
use Redirect;
use stdClass;
 

class SalasController extends Controller
{
    
    //
    public function index(Request $r, $sala=0){
        if(isset($r->fecha)){
            $fecha_mirar=Carbon::parse(adaptar_fecha($r->fecha));
        } else {
            $fecha_mirar=Carbon::now();
        }
        
        $salas=DB::table('salas')
            ->join('puestos','salas.id_puesto','puestos.id_puesto')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('salas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('salas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q) use($sala){
                if($sala!=0){
                    $q->where('salas.id_puesto',$sala);
                }
            })
            ->get();

        $lista_puestos=$salas->pluck('id_puesto')->toArray();

        $reservas=DB::table('reservas')
            ->join('users','reservas.id_usuario','users.id')
            ->wherein('reservas.id_puesto',$lista_puestos)
            ->wheredate('reservas.fec_reserva',$fecha_mirar)
            ->get();

        return view('salas.index',compact('salas','reservas','r'));
    }

    public function reservas(){
        $plantas_usuario=DB::table('plantas_usuario')
            ->select('plantas.*')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->pluck('id_planta')
            ->toArray();
        
        $salas=DB::table('salas')
            ->join('puestos','salas.id_puesto','puestos.id_puesto')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('salas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('salas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->wherein('puestos.id_planta',$plantas_usuario)
            ->get();

        $lista_puestos=$salas->pluck('id_puesto')->toArray();

        $reservas=DB::table('reservas')
            ->selectraw('reservas.*,puestos.cod_puesto,puestos.des_puesto,edificios.des_edificio,plantas.des_planta,puestos_tipos.val_icono,puestos_tipos.val_color,puestos_tipos.des_tipo_puesto,plantas.id_planta,users.name')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->leftjoin('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('users','reservas.id_usuario','users.id')
            ->wherein('reservas.id_puesto',$lista_puestos)
            ->wheredate('reservas.fec_reserva',Carbon::now())
            ->get();

        $misreservas=DB::table('reservas')
            ->selectraw('reservas.*,puestos.cod_puesto,puestos.des_puesto,edificios.des_edificio,plantas.des_planta,puestos_tipos.val_icono,puestos_tipos.val_color,puestos_tipos.des_tipo_puesto,plantas.id_planta,users.name')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->leftjoin('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('users','reservas.id_usuario','users.id')
            ->wherein('reservas.id_puesto',$lista_puestos)
            ->where('reservas.id_usuario',Auth::user()->id)
            ->wheredate('reservas.fec_reserva','>=',Carbon::now())
            ->orderby('reservas.fec_reserva')
            ->orderby('reservas.id_puesto')
            ->get();

        return view('salas.reserva',compact('salas','reservas','misreservas'));
    }

    public function dia($fecha=null){
        if(!isset($fecha)){
            $fecha=Carbon::now();
        } else {
            $fecha=Carbon::parse($fecha);
        }

        $plantas_usuario=DB::table('plantas_usuario')
            ->select('plantas.*')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->pluck('id_planta')
            ->toArray();
        
        $salas=DB::table('salas')
            ->join('puestos','salas.id_puesto','puestos.id_puesto')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('salas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('salas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->wherein('puestos.id_planta',$plantas_usuario)
            ->get();

        $lista_puestos=$salas->pluck('id_puesto')->toArray();

        $reservas=DB::table('reservas')
            ->selectraw('reservas.*,puestos.cod_puesto,puestos.des_puesto,edificios.des_edificio,plantas.des_planta,puestos_tipos.val_icono,puestos_tipos.val_color,puestos_tipos.des_tipo_puesto,plantas.id_planta,users.name')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->leftjoin('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('users','reservas.id_usuario','users.id')
            ->wherein('reservas.id_puesto',$lista_puestos)
            ->wheredate('reservas.fec_reserva',$fecha)
            ->get();

        $misreservas=DB::table('reservas')
            ->selectraw('reservas.*,puestos.cod_puesto,puestos.des_puesto,edificios.des_edificio,plantas.des_planta,puestos_tipos.val_icono,puestos_tipos.val_color,puestos_tipos.des_tipo_puesto,plantas.id_planta,users.name')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->leftjoin('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('users','reservas.id_usuario','users.id')
            ->wherein('reservas.id_puesto',$lista_puestos)
            ->where('reservas.id_usuario',Auth::user()->id)
            ->wheredate('reservas.fec_reserva','>=',$fecha)
            ->get();

        return view('salas.lista_salas',compact('salas','reservas','misreservas','fecha'));
    }

    public function crear_reserva($sala=0){
        $reserva=new reservas;
        $f1=Carbon::now();
        $plantas_usuario=DB::table('plantas_usuario')
            ->select('plantas.*')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->join('puestos','plantas.id_planta','puestos.id_planta')
            ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_sala'))
            ->where('id_usuario',Auth::user()->id)
            ->distinct()
            ->get();

        $lista_edificios=$plantas_usuario->pluck('id_edificio')->unique();

        $edificios=DB::table('edificios')
            ->wherein('id_edificio',$lista_edificios)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('edificios.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $tipos = DB::table('puestos_tipos')
            ->join('clientes','clientes.id_cliente','puestos_tipos.id_cliente')
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
            ->wherein('puestos_tipos.id_tipo_puesto',config('app.tipo_puesto_sala'))
            ->orderby('id_tipo_puesto')
            ->get();

        $festivos_usuario=\App\Http\Controllers\Reservascontroller::festivos_usuario(Auth::user()->id);

        return view('salas.edit_reserva', compact('reserva','f1','plantas_usuario','tipos','edificios','sala','festivos_usuario'));
    }

    public function comprobar(Request $r){
        $plantas_usuario=DB::table('plantas_usuario')
            ->where('id_usuario',Auth::user()->id)
            ->pluck('id_planta')
            ->toArray();

        
        $edificios_usuario=DB::table('plantas')
            ->join('plantas_usuario','plantas_usuario.id_planta','plantas.id_planta')
            ->where('plantas_usuario.id_usuario',Auth::user()->id)
            ->pluck('id_edificio')
            ->unique()
            ->toArray();

        //dd($r->all());
        $f = explode(' - ',$r->fecha);
        $f1 = adaptar_fecha($f[0]);
        $f2 = adaptar_fecha($f[1]);

        //Intervalo en minutos entre las dos hora en cualquier dia
        $h1=Carbon::parse($f1.' '.$r->hora_inicio);
        $h2=Carbon::parse($f1.' '.$r->hora_fin);
        $intervalo=$h2->diffInminutes($h1)/60;
        //Vamos a mirar que puestos estan disponibles todos los dias que ha solicitado el usuario
        $puestos_reservados=[];
        $period = CarbonPeriod::create($f1,$f2);
       
        foreach($period as $p){
            $fec_desde=Carbon::parse($p->format('Y-m-d').' '.$r->hora_inicio);
            $fec_hasta=Carbon::parse($p->format('Y-m-d').' '.$r->hora_fin);
            // dd($fec_desde.' - '.$fec_hasta);
            $intervalo=$fec_hasta->diffInminutes($fec_desde)/60;
            $reservas=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_sala'))
                ->where(function($q) use($r){
                    if($r->id_edificio!=null){
                        $q->where('puestos.id_edificio',$r->edificio);
                    }
                })
                ->where(function($q) use($r,$fec_desde,$fec_hasta){
                    $q->where(function($q) use($fec_desde,$fec_hasta,$r){
                        $q->wherenull('fec_fin_reserva');
                        $q->where('fec_reserva',$fec_desde->format('Y-m-d'));
                    });
                    $q->orwhere(function($q) use($fec_desde,$fec_hasta,$r){
                        $q->whereraw("'".$fec_desde->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                        $q->orwhereraw("'".$fec_hasta->format('Y-m-d H:i:s')."' between fec_reserva AND fec_fin_reserva");
                        $q->orwherebetween('fec_reserva',[$fec_desde,$fec_hasta]);
                        $q->orwherebetween('fec_fin_reserva',[$fec_desde,$fec_hasta]);
                    });
                })
                ->where(function($q){
                    if (!isAdmin()) {
                        $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->where(function($q) use($r){
                    if($r->sala!=0){
                        $q->where('puestos.id_puesto',$r->sala);
                    }
                })
                ->get();
            $puestos_reservados=array_merge($puestos_reservados,$reservas->pluck('id_puesto')->toArray());
        }

        $reservas_total=collect();
        foreach($period as $p){
            $fec_desde=Carbon::parse($p->format('Y-m-d').' '.$r->hora_inicio);
            $fec_hasta=Carbon::parse($p->format('Y-m-d').' '.$r->hora_fin);
            // dd($fec_desde.' - '.$fec_hasta);
            $intervalo=$fec_hasta->diffInminutes($fec_desde)/60;
            $reservas=DB::table('reservas')
                ->join('puestos','puestos.id_puesto','reservas.id_puesto')
                ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
                ->join('users','reservas.id_usuario','users.id')
                ->wherein('puestos.id_tipo_puesto',config('app.tipo_puesto_sala'))
                ->where(function($q) use($r){
                    if($r->id_edificio!=null){
                        $q->where('puestos.id_edificio',$r->edificio);
                    }
                })
                ->where(function($q) use($r,$fec_desde,$fec_hasta){
                    $q->where(function($q) use($fec_desde,$fec_hasta,$r){
                        $q->wherenull('fec_fin_reserva');
                        $q->wheredate('fec_reserva',$fec_desde->format('Y-m-d'));
                    });
                    $q->orwhere(function($q) use($fec_desde,$fec_hasta){
                        $q->orwherebetween('fec_reserva',[$fec_desde->format('Y-m-d'),$fec_hasta->format('Y-m-d 23:59:59')]);
                        $q->orwherebetween('fec_fin_reserva',[$fec_desde->format('Y-m-d'),$fec_hasta->format('Y-m-d 23:59:59')]);
                    });
                })
                ->where(function($q){
                    if (!isAdmin()) {
                        $q->where('puestos.id_cliente',Auth::user()->id_cliente);
                    } else {
                        $q->where('puestos.id_cliente',session('CL')['id_cliente']);
                    }
                })
                ->where(function($q) use($r){
                    if($r->sala!=0){
                        $q->where('puestos.id_puesto',$r->sala);
                    }
                })
                ->get();
            $reservas_total=$reservas_total->merge($reservas);
        }

        $salas=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','salas.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color', 'puestos.val_color as color_puesto','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo','puestos_tipos.des_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('salas','puestos.id_puesto','salas.id_puesto')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
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
            ->where(function($q) use($intervalo){
                $q->where('puestos.max_horas_reservar','>=',$intervalo);
                $q->orwherenull('puestos.max_horas_reservar');
                $q->orwhere('puestos.max_horas_reservar','0');
                $q->orwhere('puestos.max_horas_reservar','');
            })
            ->where(function($q) use($r){
                if($r->id_planta && $r->id_planta!=0){
                    $q->where('puestos.id_planta',$r->id_planta);
                }
            })
            ->where(function($q) use($r){
                if($r->sala!=0){
                    $q->where('puestos.id_puesto',$r->sala);
                }
            })
            ->wherenotin('puestos.id_estado',[4,5,6])
            ->where(function($q) use($r,$puestos_reservados){
                if($r->sala==0){
                    $q->wherenotin('puestos.id_puesto',$puestos_reservados);
                }
            })
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();
            
        $reservas=$reservas_total;
        $misreservas=$reservas->where('id_usuario',Auth::user()->id);
        
        return view('salas.comprobar',compact('salas','reservas','misreservas'));
    }

    public function mis_reservas(){
        $plantas_usuario=DB::table('plantas_usuario')
            ->select('plantas.*')
            ->join('plantas','plantas.id_planta','plantas_usuario.id_planta')
            ->where('id_usuario',Auth::user()->id)
            ->pluck('id_planta')
            ->toArray();
        
        $salas=DB::table('salas')
            ->join('puestos','salas.id_puesto','puestos.id_puesto')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('salas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('salas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->wherein('puestos.id_planta',$plantas_usuario)
            ->get();

        $lista_puestos=$salas->pluck('id_puesto')->toArray();
        
        $misreservas=DB::table('reservas')
            ->selectraw('reservas.*,puestos.cod_puesto,puestos.des_puesto,edificios.des_edificio,plantas.des_planta,puestos_tipos.val_icono,puestos_tipos.val_color,puestos_tipos.des_tipo_puesto,plantas.id_planta,users.name')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->leftjoin('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('users','reservas.id_usuario','users.id')
            ->wherein('reservas.id_puesto',$lista_puestos)
            ->where('reservas.id_usuario',Auth::user()->id)
            ->wheredate('reservas.fec_reserva','>=',Carbon::now())
            ->orderby('reservas.fec_reserva')
            ->orderby('reservas.id_puesto')
            ->get();

        return view('salas.mis_reservas',compact('misreservas'));
    }

    public function mkd($sala){

        $sala=DB::table('salas')
            ->join('puestos','salas.id_puesto','puestos.id_puesto')
            ->where('salas.id_puesto',$sala)
            ->first();

        $reservas=DB::table('reservas')
            ->join('users','reservas.id_usuario','users.id')
            ->where('reservas.id_puesto',$sala->id_puesto)
            ->wheredate('reservas.fec_reserva',Carbon::now())
            ->get();

        return view('mkd.sala',compact('sala','reservas'));
    }
    //https://192.168.1.103/sala/doqgPaOTjN3SJEmdlgNdL4KRBaHfCRCwHgNDW7RbfRHPTruznO
    public function getpuesto($token){
        $sala=DB::table('puestos')
            ->join('salas','salas.id_puesto','puestos.id_puesto')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where('token',$token)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('salas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('salas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->first();

        $reservas=DB::table('reservas')
            ->join('users','reservas.id_usuario','users.id')
            ->where('reservas.id_puesto',$sala->id_puesto)
            ->wheredate('reservas.fec_reserva',Carbon::now())
            ->get();

        return view('salas.scan',compact('sala','reservas'));

    }
}

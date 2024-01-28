<?php
namespace App\Http\Controllers;
use App\Models\clientes;
use App\Models\edificios;
use App\Models\plantas;
use App\Models\puestos;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use File;

class MKDController extends Controller
{
    
    public function index(){

        $usuarios=DB::table('users')
            ->leftjoin('niveles_acceso','users.cod_nivel', 'niveles_acceso.cod_nivel')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->wherein('users.id_cliente',clientes());
                } else {
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q){
                if (isSupervisor(Auth::user()->id)) {
                    $usuarios_supervisados=DB::table('users')->where('id_usuario_supervisor',Auth::user()->id)->pluck('id')->toArray();
                    $q->wherein('users.id',$usuarios_supervisados);
                }
            })
            ->where('nivel_acceso','<=',Auth::user()->nivel_acceso)
            ->wherenotnull('token_acceso')
            ->get();

        $plantas=DB::Table('plantas')
            ->join('edificios','edificios.id_edificio','plantas.id_edificio')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('plantas.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('plantas.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->orderby('plantas.id_cliente')
            ->orderby('plantas.id_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.id_planta')
            ->get();

        $edificios = edificios::where(function($q){
                if (!isAdmin()) {
                    $q->where('id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('edificios.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();
        
        return view('mkd.index',compact('usuarios','plantas','edificios'));
    }
    
    public function plano($planta,$token,$view="plano"){

        return null;
        //Buscamos el usuario por el token
        $user=DB::table('users')->where('token_acceso',$token)->first();
        if(!$user){
            $mensaje="No tiene permiso para ver esa informacion";
            return $mensaje;
        } else {
            $id_usuario=$user->id;
            Auth::loginUsingId($id_usuario);
        }
        
        // if(!authbyToken($token)){
        //     $mensaje="No tiene permiso para ver esa informacion";
        //     return view('genericas.error',compact('mensaje'));
        // }

        validar_acceso_tabla($planta,'plantas');
        
        $plantas=plantas::findOrFail($planta);
       
        $cliente=clientes::where('id_cliente',$plantas->id_cliente)->first();
        $edificio=edificios::where('id_edificio',$plantas->id_edificio)->first();
        $puestos= DB::Table('puestos')
            ->select('puestos.*','puestos.width as puesto_width', 'puestos.height as puesto_height','plantas.*','estados_puestos.val_color as color_estado','estados_puestos.hex_color','estados_puestos.des_estado','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo','users.name as usuario_usando')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->leftjoin('users','puestos.id_usuario_usando','users.id')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->where('puestos.id_planta',$planta)
            ->get();

        $lista_ids=$puestos->pluck('id_puesto')->toArray();
        $reservas=DB::table('reservas')
            ->join('puestos','puestos.id_puesto','reservas.id_puesto')
            ->join('users','reservas.id_usuario','users.id')
            ->where(function($q){
                $q->where('fec_reserva',Carbon::now()->format('Y-m-d'));
                $q->orwhereraw("'".Carbon::now()."' between fec_reserva AND fec_fin_reserva");
            })
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('reservas.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->where('puestos.id_planta',$planta)
            ->get();

        $asignados_usuarios=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('users','users.id','puestos_asignados.id_usuario')    
            ->where('id_usuario','<>',Auth::user()->id)
            ->where(function($q){
                $q->wherenull('fec_desde');
                $q->orwhereraw("'".Carbon::now()."' between fec_desde AND fec_hasta");
            })
            ->where('puestos.id_planta',$planta)
            ->get();

        $asignados_miperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')    
            ->where('id_perfil',Auth::user()->cod_nivel)
            ->where('puestos.id_planta',$planta)
            ->get();
        
        $asignados_nomiperfil=DB::table('puestos_asignados')
            ->join('puestos','puestos.id_puesto','puestos_asignados.id_puesto')   
            ->join('niveles_acceso','niveles_acceso.cod_nivel','puestos_asignados.id_perfil')     
            ->where('id_perfil','<>',Auth::user()->cod_nivel)
            ->where('puestos.id_planta',$planta)
            ->get();

        return view('mkd.'.$view,compact('token','planta','view','plantas','puestos','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil','cliente','edificio'));
        
    }

    public function datos_plano($planta,$token){
        validar_acceso_tabla($planta,'plantas');
        $puestos= DB::Table('puestos')
            ->select('puestos.id_puesto','cod_puesto','puestos.id_estado','estados_puestos.val_color')
            ->selectraw('(select(fec_reserva) from reservas where reservas.id_puesto=puestos.id_puesto and fec_reserva=date(now())) as fec_reserva')
            ->join('estados_puestos','estados_puestos.id_estado','puestos.id_estado')
            ->where('id_planta',$planta)
            ->get();
        $puestos=json_encode($puestos);
        return $puestos;       
    }

    public function gen_config(Request $r){
        // $destinationPath=public_path()."/uploads/json/".Auth::user()->id_cliente."/";
        // $file = 'playerweb.json';
        // if (!is_dir($destinationPath)) {  mkdir($destinationPath,0777,true);  }
        // File::put($destinationPath.$file,json_encode($r->lista));
        // return response()->download($destinationPath.$file);
        return response(json_encode($r->lista));
    } 
    
}

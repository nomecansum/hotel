<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Carbon\Carbon;
use App\Models\users;
use App\Models\puestos_ronda;
use App\Models\puestos;
use PDF;
use Auth;


class LimpiezaController extends Controller
{
    public function index($tipo="L",$f1=0,$f2=0){

        $f1=$f1==0?Carbon::now()->startOfMonth():Carbon::parse($f1);
        $f2=$f2==0?Carbon::now()->endOfMonth():Carbon::parse($f2);
        $fhasta=clone($f2);
        $fhasta=$fhasta->addDay();
        
        $usuarios=DB::table('users')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('users.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('users.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->get();

        $rondas=DB::table('rondas_limpieza')
            ->join('limpiadores_ronda','limpiadores_ronda.id_ronda','rondas_limpieza.id_ronda')
            ->join('users as u1','rondas_limpieza.user_creado','u1.id')
            ->join('users as u2','limpiadores_ronda.id_limpiador','u2.id')
            ->select('fec_ronda','des_ronda','rondas_limpieza.id_ronda','u1.name as user_creado')
            ->selectraw("group_concat(u2.name SEPARATOR '#') as user_asignado") 
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('rondas_limpieza.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->where(function($q){
                if (Auth::user()->nivel_acceso==10) {  //Personal de limpieza
                    $q->where('limpiadores_ronda.id_limpiador',Auth::user()->id);
                }
            })
            ->whereBetween('fec_ronda',[$f1,$fhasta])
            ->where('tip_ronda',$tipo)
            ->groupby('rondas_limpieza.id_ronda','fec_ronda','des_ronda','u1.name')
            ->orderby('id_ronda','desc')
            ->get();

        $detalles=DB::table('rondas_limpieza')
            ->select('puestos_ronda.*','puestos.cod_puesto','puestos.id_edificio','puestos.id_planta','puestos.id_estado','puestos_tipos.val_tiempo_limpieza','puestos_tipos.val_icono','puestos_tipos.val_color as color_puesto')
            ->join('puestos_ronda','puestos_ronda.id_ronda','rondas_limpieza.id_ronda')
            ->join('puestos','puestos_ronda.id_puesto','puestos.id_puesto')
            ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
            ->where(function($q){
                
                if (!isAdmin()) {
                    $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('rondas_limpieza.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->whereBetween('fec_ronda',[$f1,$fhasta])
            ->get();
        if($tipo=='L')
        {
            $entidades=["tipo"=>"limpieza","icono"=>"fad fa-broom","tipo_usuario"=>"limpiador","plural"=>"limpiadores","menu1"=>".limpieza","menu2"=>".rondas"]; 
        } else {
            $entidades=["tipo"=>"mantenimiento","icono"=>"fad fa-tools","tipo_usuario"=>"tecnico","plural"=>"tecnicos","menu1"=>".mantenimiento","menu2"=>".rondas_mant"]; 
        }
        return view('limpieza.index',compact('rondas','detalles','usuarios','f1','f2','entidades','tipo'));
    }

    public function view($id,$print=0){
        $ronda=DB::table('rondas_limpieza')
            ->join('users as u1','rondas_limpieza.user_creado','u1.id')
            ->join('clientes','rondas_limpieza.id_cliente','clientes.id_cliente')
            ->where('rondas_limpieza.id_ronda',$id)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->first();

        $limpiadores=DB::table('limpiadores_ronda')
            ->join('users','limpiadores_ronda.id_limpiador','users.id')
            ->where('id_ronda',$ronda->id_ronda)
            ->get();
            
        $datos_tiempo=DB::select("select max(fec_fin) as maximo, min(fec_fin) as minimo from puestos_ronda where id_ronda=".$id);
        $tiempo_empleado=Carbon::parse($datos_tiempo[0]->maximo)->diffAsCarbonInterval (Carbon::parse($datos_tiempo[0]->minimo));
        $tiempo_empleado=$tiempo_empleado->forHumans();

        $detalles=DB::table('puestos_ronda')
            ->select('puestos_ronda.*','puestos.cod_puesto','puestos.des_puesto','puestos.id_edificio','puestos.id_planta','puestos.id_estado','edificios.des_edificio','plantas.des_planta','estados_puestos.des_estado','estados_puestos.val_color','users.name','puestos_tipos.val_tiempo_limpieza','puestos_tipos.val_icono','puestos_tipos.val_color as color_puesto')
            ->join('puestos','puestos_ronda.id_puesto','puestos.id_puesto')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('puestos_tipos','puestos_tipos.id_tipo_puesto','puestos.id_tipo_puesto')
            ->leftjoin('users','puestos_ronda.user_audit','users.id')
            ->where('id_ronda',$ronda->id_ronda)
            ->get();

        if($print==0){
            return view('limpieza.detalle',compact('ronda','limpiadores','detalles','print','tiempo_empleado'));
        } else{
            $filename='Ronda de limpieza #'.$id.'.pdf';
            $pdf = PDF::loadView('limpieza.detalle',compact('ronda','limpiadores','detalles','print','tiempo_empleado'));
            return $pdf->download($filename);
        }
    }

    public function view_limpia($id){
        $ronda=DB::table('rondas_limpieza')
            ->join('users as u1','rondas_limpieza.user_creado','u1.id')
            ->join('clientes','rondas_limpieza.id_cliente','clientes.id_cliente')
            ->join('limpiadores_ronda','rondas_limpieza.id_ronda','limpiadores_ronda.id_ronda')
            ->where('rondas_limpieza.id_ronda',$id)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->where(function($q){
                if (Auth::user()->nivel_acceso==10) {  //Personal de limpieza
                    $q->where('limpiadores_ronda.id_limpiador',Auth::user()->id);
                }
            })
            ->first();

        $edificios=DB::table('edificios')
            ->select('edificios.id_edificio','edificios.des_edificio')
            ->selectraw("(select count(id_planta) from plantas where id_edificio=edificios.id_edificio) as plantas")
            ->selectraw("(select count(id_puesto) from puestos where id_edificio=edificios.id_edificio) as puestos")
            ->join('puestos','edificios.id_edificio','puestos.id_edificio')
            ->join('puestos_ronda','puestos_ronda.id_puesto','puestos.id_puesto')
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('edificios.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('rondas_limpieza.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->distinct()
            ->where('puestos_ronda.id_ronda',$ronda->id_ronda)
            ->get();

        $puestos=DB::table('puestos_ronda')
            ->select('puestos_ronda.*','puestos.cod_puesto','puestos.des_puesto','puestos.id_edificio','puestos.id_planta','puestos.id_estado','edificios.des_edificio','plantas.des_planta','estados_puestos.des_estado','estados_puestos.val_color','users.name')
            ->join('puestos','puestos_ronda.id_puesto','puestos.id_puesto')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->leftjoin('users','puestos_ronda.user_audit','users.id')
            ->where('id_ronda',$ronda->id_ronda)
            ->get();

            return view('limpieza.detalle_limpiador',compact('ronda','puestos','edificios'));
    }

    public function estado_puesto(Request $r){
        try{
            if(!$r->estado){ //Valor por defecto si no se pasa el estado al que queremos ir
                $r->estado=1;
            }
            $lista_id=explode(',',$r->id);
            foreach($lista_id as $i){
                $puesto=puestos_ronda::findorfail($i);
                $puesto->user_audit=$r->user;
                $puesto->fec_fin=Carbon::now();
                $puesto->save();

                $dato=puestos::findorfail($puesto->id_puesto);
                $dato->id_estado=$r->estado;
                $dato->save();
            }
            savebitacora('Estado de puesto '.implode(",",$lista_id). ' a estado '.$r->estado,"Ronda de limpieza","estado_puesto","OK");
            return [
                'title' => "OK",
                'message' => 'puesto '.$r->etiqueta. ' actualizado',
                'id' => $lista_id,
                'estado'=>$r->estado
            ];
        } catch (\Exception $exception) {
            return [
                'title' => "Estado de puesto",
                'error' => 'ERROR: Ocurrio un error actualizando el estado '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }
        
    }

    public function completar_ronda($id,$empleado){
        validar_acceso_tabla($id,"rondas_limpieza");
        $ronda=db::table('rondas_limpieza')
            ->where('id_ronda',$id)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('rondas_limpieza.id_cliente',Auth::user()->id_cliente);
                } 
            })
            ->first();
        if($ronda->tip_ronda=='L')
        {
            $tipo_ronda="limpieza";
        } else {
            $tipo_ronda="mantenimiento";
        }

        try{
            $puesto=puestos_ronda::where('id_ronda',$id);
            $puesto->update(['user_audit'=>$empleado,'fec_fin'=>Carbon::now()]);
            savebitacora('Completada ronda '.$ronda->des_ronda,"Ronda de ".$tipo_ronda,"completar_ronda","OK");
            return [
                'title' => "Rondas de limpieza",
                'message' => 'Ronda completada',
                'reload'=>1
            ];
        } catch (\Exception $exception) {
            return [
                'title' => "Edificios",
                'error' => 'ERROR: Ocurrio un error actualizando el estado '.mensaje_excepcion($exception),
                //'url' => url('sections')
            ];
        }
    }

    public function scan(){
        $estado_destino=1;
        $modo='cambio_estado';
        $titulo='Marcar puesto como disponible';
        $tipo_scan="limpieza";
        return view('scan',compact('estado_destino','modo','titulo','tipo_scan'));
    }

    public function pendientes(){
        $puestos=DB::table('puestos')
            ->select('puestos.*','edificios.*','plantas.*','clientes.*','estados_puestos.des_estado','estados_puestos.val_color as color_estado','estados_puestos.hex_color','puestos_tipos.val_icono as icono_tipo','puestos_tipos.val_color as color_tipo')
            ->join('edificios','puestos.id_edificio','edificios.id_edificio')
            ->join('plantas','puestos.id_planta','plantas.id_planta')
            ->join('estados_puestos','puestos.id_estado','estados_puestos.id_estado')
            ->join('puestos_tipos','puestos.id_tipo_puesto','puestos_tipos.id_tipo_puesto')
            ->join('clientes','puestos.id_cliente','clientes.id_cliente')
            ->where(function($q){
                $q->where('puestos.id_cliente',Auth::user()->id_cliente);
            })
            ->where('puestos.id_estado',3)
            ->orderby('edificios.des_edificio')
            ->orderby('plantas.num_orden')
            ->orderby('plantas.des_planta')
            ->orderby('puestos.des_puesto')
            ->get();

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
        ->get();

        $reservas=collect([]);

        $asignados_usuarios=collect([]);

        $asignados_miperfil=collect([]);
        
        $asignados_nomiperfil=collect([]);
        
        return view('limpieza.pendientes',compact('puestos','edificios','reservas','asignados_usuarios','asignados_miperfil','asignados_nomiperfil'));
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\turnos;
use Validator;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class TurnosController extends Controller
{
    //
    function index(){

        $turnos=DB::table('turnos')
        ->join('clientes','turnos.id_cliente','clientes.id_cliente')
        ->where(function($q){
            if (!isAdmin()) {
                $q->where('turnos.id_cliente',Auth::user()->id_cliente);
            } else {
                $q->where('turnos.id_cliente',session('CL')['id_cliente']);
            }
        })
        ->get();
        
        return view('turnos.index',compact('turnos'));
    }

    public function edit($id=0)
    {
        
        if ($id==0){
            $dato=new turnos();
            $dato->id_cliente=session('CL')['id_cliente'];
            $dato->fec_inicio=Carbon::now()->startOfMonth();
            $dato->fec_fin=Carbon::now()->endOfMonth();
            
        } else {
            $dato = DB::table('turnos')
            ->where('id_turno',$id)
            ->where(function($q){
                if (!isAdmin()) {
                    $q->where('turnos.id_cliente',Auth::user()->id_cliente);
                } else {
                    $q->where('turnos.id_cliente',session('CL')['id_cliente']);
                }
            })
            ->first();
        }
        $clientes = DB::table('clientes')->where('id_cliente', Auth::user()->id_cliente)->get();
    	return view('turnos.editor',compact('dato','clientes'));
    }

    public function save(Request $r){
        try {
            //Las fechas
            $f = explode(' - ',$r->fechas);
            $r->request->add(['fec_inicio'=>adaptar_fecha($f[0])]);
            $r->request->add(['fec_fin'=>adaptar_fecha($f[1])]);
            
            //Compnemos el JSON con los dias
            $datos_dias=[
                'dia'=>$r->dia,
                'hora_inicio'=>$r->hora_inicio,
                'hora_fin'=>$r->hora_fin,
                'mod_semana'=>$r->mod_semana
            ];
            $datos_dias=$datos_dias;

            $r->request->add(['datos_dias'=>$datos_dias]);
            $r->request->add(['id_cliente'=>session('CL')['id_cliente']]);

            if($r->id==0){
                $turno=turnos::create($r->all());
            } else {
                validar_acceso_tabla($r->id,"turnos");
                $turno=turnos::find($r->id);
                $turno->update($r->all());
            }
            //Guardamos el JSON aparte, que con el modelo no funciona
            DB::table('turnos')->where('id_turno',$turno->id_turno)->update(['dias_semana'=>$datos_dias]);
            savebitacora("Turno ".$turno->des_turno." creado","Turnos","save","OK");
            return [
                'message' =>"Turno ".$turno->des_turno." creado",
                'url' => url('turnos')
            ];
        } catch (\Throwable $exception) {
            return [
                'title' => "Error creando turno",
                'error' => 'Ocurrio un error creando el turno. '.mensaje_excepcion($exception),
                'url' => url('turnos')
            ];
        
        }
    }

    public function delete($id)
    {
        try {
            validar_acceso_tabla($id,"turnos");
            $dato = turnos::findOrFail($id);
            $dato->delete();
            savebitacora('Turno '.$dato->des_turno. ' borrado',"Turnos","delete","OK");
            flash("Turno ".$dato->des_turno." borrado")->success();
		    return redirect('turnos');
        } catch (\Throwable $exception) {
            flash('Ocurrio un error borrando el turno. '.mensaje_excepcion($exception))->error();
            return redirect('turnos');
            
        
        }
    }
}

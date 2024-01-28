<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\notif;
use Carbon\Carbon;

class NotifController extends Controller
{
    public function index(){
        $notif=DB::table('notificaciones')
            ->join('notificaciones_tipos','notificaciones.id_tipo_notificacion','notificaciones_tipos.id_tipo_notificacion')
            ->where('id_usuario',Auth::user()->id)
            ->orderby('mca_leida')
            ->orderby('fec_notificacion', 'desc')
            ->where(function($q){
                $q->where('mca_leida','N')
                  ->orWhere('fec_notificacion','>',Carbon::now()->subDays(10));
            })
            ->orderby('notificaciones.fec_notificacion','desc')
            ->get();
        return view('notificaciones.index', compact('notif'));
    }

    public function list(){
        $notificaciones_nuevas=DB::table('notificaciones')
            ->join('notificaciones_tipos','notificaciones.id_tipo_notificacion','notificaciones_tipos.id_tipo_notificacion')
            ->where('id_usuario',Auth::user()->id)
            ->where('mca_leida','N')
            ->orderby('notificaciones_tipos.val_prioridad')
            ->orderby('notificaciones.fec_notificacion','desc')
            ->get();

        $notificaciones_ultimas=DB::table('notificaciones')
            ->join('notificaciones_tipos','notificaciones.id_tipo_notificacion','notificaciones_tipos.id_tipo_notificacion')
            ->where('id_usuario',Auth::user()->id)
            ->where('mca_leida','S')
            ->orderby('notificaciones.fec_notificacion','desc')
            ->take(5)
            ->get();

        $notif=$notificaciones_nuevas->union($notificaciones_ultimas)->all();

        return view('notificaciones.fill_lista_notificaciones',compact('notif'));
    }

    public function leida(){
        notif::where('id_usuario',Auth::user()->id)
            ->where('mca_leida','N')
            ->update(['mca_leida'=>'S']);
        return [
            "result"=>"OK"
        ];
    }
}

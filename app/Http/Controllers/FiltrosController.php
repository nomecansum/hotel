<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class FiltrosController extends Controller
{
    ////////////MANEJO DE LOS COMBOS DE FILTRO/////////////
    private function filtro_clientes($r){
        if (!isset($r->clientes)){
            if (fullaccess()){
                //$r->clientes=DB::table('clientes')->where('id_cliente','>',1)->pluck('id_cliente')->toArray();
                $r->clientes=[];
            } else {
                if(count(session('clientes'))>100){
                    $r->clientes=[];
                } else {
                    $r->clientes=DB::table('clientes')
                        ->where('id_cliente','>',1)
                        ->wherein('id_cliente',session('clientes'))
                        ->pluck('id_cliente');
                }
            }
        } else{
            if(!fullaccess()){
                $r->clientes=DB::table('clientes')
                ->wherein('id_cliente',session('clientes'))
                ->wherein('id_cliente',$r->clientes)
                ->pluck('id_cliente');
            }
        }
        return $r;
    }

    public function loadtags(Request $r)
    {
        $r=$this->filtro_clientes($r);
        return
        [
            "tags" => DB::table('clientes')
                ->select('tags.id_tag','tags.nombre_tag','clientes.nom_cliente')
                ->wherein('clientes.id_cliente',$r->clientes)
                ->where(function($q) {
                    if (!fullAccess()) {
                        $q->WhereIn('clientes.id_cliente',clientes());
                    }
                    if (session('id_cliente')) {
                        $q->where('clientes.id_cliente',session('id_cliente'));
                    }
                })
                ->orderby('clientes.nom_cliente')
                ->get(),

            "dispositivos" => DB::table('dispositivos')
                    ->select('dispositivos.id_dispositivo','dispositivos.nombre','clientes.nombre_cliente')
                    ->join('contratos','dispositivos.id_contrato','contratos.id_contrato')
                    ->join('clientes','clientes.id_cliente','contratos.id_cliente')
                    ->where(function($q) {
                        if (!fullAccess()) {
                            $q->WhereIn('contratos.id_cliente',clientes());
                        }
                        if (session('id_cliente')) {
                            $q->where('contratos.id_cliente',session('id_cliente'));
                        }
                    })
                    ->whereIn('contratos.id_cliente',$r->clientes)
                    ->orderby('clientes.nombre_cliente')
                    ->orderby('dispositivos.nombre')
                    ->get(),

            "contenidos" => DB::table('textos_dinamicos')
            ->select('textos_dinamicos.id_texto_dinamico','textos_dinamicos.des_texto_dinamico','clientes.nombre_cliente')
            ->join('clientes','clientes.id_cliente','textos_dinamicos.id_cliente')
            ->wherein('textos_dinamicos.id_cliente',$r->clientes)
            ->wherein('textos_dinamicos.id_cliente',session('clientes'))
            ->orderby('clientes.nombre_cliente')
            ->orderby('textos_dinamicos.des_texto_dinamico')
            ->get(),

        ];
    }
}
